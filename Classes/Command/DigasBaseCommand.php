<?php

namespace Slub\DigasFeManagement\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 SLUB Dresden <typo3@slub-dresden.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Slub\DigasFeManagement\Domain\Model\Access;
use Slub\DigasFeManagement\Domain\Model\User;
use Slub\DigasFeManagement\Domain\Repository\AccessRepository;
use Slub\DigasFeManagement\Domain\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class DigasBaseCommand
 *
 */
class DigasBaseCommand extends Command
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var UserRepository
     */
    protected $UserRepository;

    /**
     * @var AccessRepository
     */
    protected $AccessRepository;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $feUserGroups;

    /**
     * @var array
     */
    protected $kitodoTempUserGroup;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->UserRepository = $this->objectManager->get(UserRepository::class);
        $this->AccessRepository = $this->objectManager->get(AccessRepository::class);
        $this->persistenceManager = $this->objectManager->get(PersistenceManager::class);

        $configurationManager = $this->objectManager->get('TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface');
        $typoscriptConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        if (!empty($extSettings = $typoscriptConfiguration['plugin.']['tx_femanager.']['settings.'])) {
            $this->settings = $extSettings;
            $this->UserRepository->setStoragePid($this->settings['pids.']['feUsers']);
            $this->AccessRepository->setStoragePid($this->settings['pids.']['feUsers']);
            $this->feUserGroups = explode(',', $this->settings['feUserGroups']);
            $this->kitodoTempUserGroup = explode(',', $this->settings['kitodoTempUserGroup']);
        }
    }

    /**
     * Base Initialization
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());

        // throw error if necessary typoscript configuration is missing
        if (empty($this->settings['pids.']['feUsers'])) {
            $this->io->error('[DiGA.Sax FE Management] Typoscript variable {plugin.tx_femanager.settings.pids.feUsers} is not set. Abort.');
            return 1;
        }
        if (empty($this->settings['pids.']['loginPage'])) {
            $this->io->error('[DiGA.Sax FE Management] Typoscript variable {plugin.tx_femanager.settings.pids.loginPage} is not set. Abort.');
            return 1;
        }

        if (empty($this->settings['feUserGroups'])) {
            $this->io->error('[DiGA.Sax FE Management] Typoscript variable {plugin.tx_femanager.settings.feUserGroups} is not set. Abort.');
            return 1;
        }

        if (empty($this->settings['adminName'])) {
            $this->io->error('[DiGA.Sax FE Management] Typoscript variable {plugin.tx_femanager.settings.adminName} is not set. Abort.');
            return 1;
        }

        if (empty($this->settings['adminEmail'])) {
            $this->io->error('[DiGA.Sax FE Management] Typoscript variable {plugin.tx_femanager.settings.adminEmail} is not set. Abort.');
            return 1;
        }

        return 0;
    }

    /**
     * Loop and delete through feUsers array
     *
     * @param \Slub\DigasFeManagement\Domain\Model\User[] $feUsers
     * @return int|bool
     */
    protected function deleteFeUsers($feUsers)
    {
        $deleteCounter = 0;

        foreach ($feUsers as $feUser) {
            try {
                $this->UserRepository->remove($feUser);
                $deleteCounter++;
            } catch (Exception $e) {
                $this->io->error(sprintf('[DiGA.Sax FE Management] User (UID: %s) could not be deleted. Error Message: %s', $feUser->getUid(), $e->getMessage()));
                return false;
            }
        }
        return $deleteCounter;
    }

    /**
     * Init user locale to send emails in users selected language
     *
     * @param \Slub\DigasFeManagement\Domain\Model\User $feUser
     * @return void
     */
    protected function initUserLocal(\Slub\DigasFeManagement\Domain\Model\User $feUser)
    {
        // hack to send english texts to user only if user registered on english page (sys_language_uid==1)
        switch ($feUser->getLocale()) {
            case '1':
                setlocale(LC_ALL, 'en_US.utf8');
                $GLOBALS['LANG']->init('en');
                break;
            case '0':
            default:
                setlocale(LC_ALL, 'de_DE.utf8');
                $GLOBALS['LANG']->init('de');
                break;
        }
    }

    /**
     * Prepare notification email for kitodo access
     *
     * @param User $feUser
     * @param Access[] $userDocumentEntries
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    protected function notifyUser(User $feUser, array $userDocumentEntries)
    {
        // get fe_user data
        if (!empty($feUser) && !empty($userDocumentEntries)) {
            $documentsList = [];
            foreach ($userDocumentEntries as $accessEntry) {
                $documentsList[] = [
                    'recordId' => $accessEntry->getDlfDocument()->getRecordId(),
                    'documentTitle' => $accessEntry->getDlfDocument()->getTitle(),
                    'endtime' => $accessEntry->getEndtime(),
                    'rejected' => $accessEntry->getRejected(),
                    'rejectedReason' => $accessEntry->getRejectedReason()
                ];

                $notificationTimestamp = strtotime('now');
                $this->updateAccessEntry($accessEntry, $notificationTimestamp);
            }

            $this->sendNotificationEmail($feUser, $documentsList);
        }
    }

    /**
     * Generate notification mail content
     *
     * @param array $documentsList List of kitodo documents
     * @param string $emailTemplate Path to email template
     * @param string $emailType Email type (html or text)
     * @return string
     */
    protected function generateNotificationEmail(array $documentsList, string $emailTemplate, string $emailType = 'text')
    {
        // generate email template by given emailType
        $htmlView = GeneralUtility::makeInstance(StandaloneView::class);
        $htmlView->setFormat($emailType);
        $htmlView->setTemplatePathAndFilename($emailTemplate);
        // https://stackoverflow.com/questions/46807995/create-a-link-in-backend-to-a-frontend-page
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($this->settings['pids.']['loginPage']);
        $loginUrl = (string)$site->getRouter()->generateUri($this->settings['pids.']['loginPage']);
        $htmlView->assignMultiple([
            'loginUrl' => $loginUrl,
            'documentsList' => $documentsList
        ]);

        return $htmlView->render();
    }

    /**
     * Update access entry with access_granted_notification or expire_notification timestamp
     *
     * @param Access $accessEntry
     * @param int $notificationTimestamp
     */
    protected function updateAccessEntry(Access $accessEntry, int $notificationTimestamp)
    {
        // must be overridden in child class
    }

    /**
     * Send email to fe_users for kitodo documents access
     *
     * @param User $feUser
     * @param array $documentsList
     */
    protected function sendNotificationEmail(User $feUser, array $documentsList)
    {
        // must be overridden in child class
    }
}
