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

use Slub\DigasFeManagement\Domain\Model\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as ExtbaseLocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class RemindUnusedAccountsCommand
 */
class RemindUnusedAccountsCommand extends DigasBaseCommand
{
    /**
     * @var int Reminder timespan in days.
     */
    protected $unusedTimespan;

    /**
     * @var int Deletion timespan in days.
     */
    protected $deleteTimespan;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('[DiGA.Sax FE Management] Remind fe_users which have not logged in for a while. Unused fe_users will be deleted after given timespan.')
            ->addArgument(
                'unusedTimespan',
                InputArgument::REQUIRED,
                'Add a timespan in days (i.e. "365"). fe_users accounts which have not logged in will get an information email.'
            )->addArgument(
                'deleteTimespan',
                InputArgument::REQUIRED,
                'Add a timespan in days (i.e. "30"). fe_users accounts which were informed and not logged in will be deleted.'
            )->setHelp(
                'This command informs unused fe_users and delete them after given timespan if have not logged in in this period.'
            );
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Initialize IO
        parent::execute($input, $output);

        if (MathUtility::canBeInterpretedAsInteger($input->getArgument('unusedTimespan'))) {
            $this->unusedTimespan = MathUtility::forceIntegerInRange((int)$input->getArgument('unusedTimespan'), 0);
        }
        if (MathUtility::canBeInterpretedAsInteger($input->getArgument('deleteTimespan'))) {
            $this->deleteTimespan = MathUtility::forceIntegerInRange((int)$input->getArgument('deleteTimespan'), 0);
        }

        if ($this->unusedTimespan <= 0 || $this->deleteTimespan <= 0) {
            $this->io->error('"unusedTimespan" and "deleteTimespan" have to a positive integer value. Abort.');
            return 1;
        }
        $this->io->text('Begin Task "remindUnusedAccounts"');
        $remindCounter = $this->remindUnusedAccounts();
        $this->io->success('Task finished successfully. Reminded fe_users entries: ' . $remindCounter);

        $this->io->text('Begin Task "deleteUnusedAccounts"');
        $deleteCounter = $this->deleteUnusedAccounts();
        $this->io->success('Task finished successfully. Deleted fe_users entries: ' . $deleteCounter);

        return 0;
    }

    /**
     * Send email to fe_users which have not logged in for a while
     * @param User $feUser
     */
    protected function sendEmail($feUser)
    {
        $this->initUserLocal($feUser);
        $userEmail = $feUser->getEmail();
        $userFullName = $feUser->getFullName();
        if (!GeneralUtility::validEmail($userEmail)) {
            $this->io->warning(sprintf('[DiGA.Sax FE Management] Remind inactive warning to user (UID: %s) could not be sent. No valid email address.', $feUser->getUid()));
            return;
        }
        $email = GeneralUtility::makeInstance(MailMessage::class);
        $textEmail = $this->generateTextEmail();
        $htmlEmail = $this->generateHtmlEmail();
        $emailSubject = ExtbaseLocalizationUtility::translate('remindUnusedAccounts.email.subject', 'DigasFeManagement');

        // Prepare and send the message
        $email->setSubject($emailSubject)
            ->setFrom([
                $this->settings['adminEmail'] => $this->settings['adminName']
            ])
            ->setTo([
                $userEmail => $userFullName
            ])
            ->text($textEmail)
            ->html($htmlEmail)
            ->send();
    }

    /**
     * Command to remind fe_users
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function remindUnusedAccounts()
    {
        $remindCounter = 0;
        $time = new \DateTime();
        $unusedTimestamp = $time->getTimestamp() - ((60 * 60 * 24) * $this->unusedTimespan);

        $feUsers = $this->UserRepository->findUnusedAccounts($unusedTimestamp, $this->feUserGroups);

        if (!empty($feUsers)) {
            foreach ($feUsers as $feUser) {
                $feUser->setInactivemessageTstamp($time);

                try {
                    $this->UserRepository->update($feUser);
                    $this->sendEmail($feUser);
                    $remindCounter++;
                } catch (Exception $e) {
                    $this->io->warning(sprintf('[DiGA.Sax FE Management] User (UID: %s) could not be reminded. Error Message: %s', $feUser->getUid(), $e->getMessage()));
                }
            }
            $this->persistenceManager->persistAll();
        }

        return $remindCounter;
    }

    /**
     * Command to delete reminded fe_users
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function deleteUnusedAccounts()
    {
        $deleteCounter = 0;
        $time = new \DateTime();
        $deleteTimestamp = $time->getTimestamp() - ((60 * 60 * 24) * $this->deleteTimespan);

        $feUsers = $this->UserRepository->findAccountsToDelete($deleteTimestamp, $this->feUserGroups);

        if (!empty($feUsers)) {
            foreach ($feUsers as $feUser) {
                try {
                    $this->UserRepository->remove($feUser);
                    $deleteCounter++;
                } catch (Exception $e) {
                    $this->io->warning(sprintf('[DiGA.Sax FE Management] User (UID: %s) could not be deleted. Error Message: %s', $feUser->getUid(), $e->getMessage()));
                }
            }
            $this->persistenceManager->persistAll();
        }

        return $deleteCounter;
    }

    /**
     * Generate HTML Email content
     * Used Email Template: EXT:digas_fe_management/Resources/Private/Templates/Email/Html/RemindUnusedAccounts.html
     *
     * @return string
     */
    protected function generateHtmlEmail()
    {
        // generate html email template
        $htmlTemplate = GeneralUtility::getFileAbsFileName('EXT:digas_fe_management/Resources/Private/Templates/Email/Html/RemindUnusedAccounts.html');
        $htmlView = GeneralUtility::makeInstance(StandaloneView::class);
        $htmlView->setFormat('html');
        $htmlView->setTemplatePathAndFilename($htmlTemplate);
        // https://stackoverflow.com/questions/46807995/create-a-link-in-backend-to-a-frontend-page
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($this->settings['pids.']['loginPage']);
        $loginUrl = (string)$site->getRouter()->generateUri($this->settings['pids.']['loginPage']);
        $htmlView->assignMultiple([
            'loginUrl' => $loginUrl,
            'unusedTimespan' => $this->unusedTimespan,
            'deleteTimespan' => $this->deleteTimespan
        ]);

        return $htmlView->render();
    }

    /**
     * Generate Text Email content
     * Used Email Template: EXT:digas_fe_management/Resources/Private/Templates/Email/Text/RemindUnusedAccounts.html
     *
     * @return string
     */
    protected function generateTextEmail()
    {
        // generate text email template
        $textTemplate = GeneralUtility::getFileAbsFileName('EXT:digas_fe_management/Resources/Private/Templates/Email/Text/RemindUnusedAccounts.html');
        $textView = GeneralUtility::makeInstance(StandaloneView::class);
        $textView->setFormat('text');
        $textView->setTemplatePathAndFilename($textTemplate);
        // https://stackoverflow.com/questions/46807995/create-a-link-in-backend-to-a-frontend-page
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($this->settings['pids.']['loginPage']);
        $loginUrl = (string)$site->getRouter()->generateUri($this->settings['pids.']['loginPage']);
        $textView->assignMultiple([
            'loginUrl' => $loginUrl,
            'unusedTimespan' => $this->unusedTimespan,
            'deleteTimespan' => $this->deleteTimespan
        ]);

        return $textView->render();
    }
}
