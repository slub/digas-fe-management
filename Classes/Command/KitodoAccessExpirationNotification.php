<?php

namespace Slub\DigasFeManagement\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2022 SLUB Dresden <typo3@slub-dresden.de>
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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as ExtbaseLocalizationUtility;

/**
 * Class KitodoAccessExpirationNotification
 */
class KitodoAccessExpirationNotification extends DigasBaseCommand
{
    /**
     * @var int Expiration timespan in days.
     */
    protected $expirationTimestamp;

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
        $this->setDescription('[DiGAS FE Management] Notify fe_users about expiring kitodo documents.')
            ->addArgument(
                'expirationTimestamp',
                InputArgument::REQUIRED,
                'Add a timespan in days (i.e. "365"). fe_users with expiring document access will get an information email.'
            )->setHelp(
                'This command informs fe_users with expiring kitodo document access.'
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

        if (MathUtility::canBeInterpretedAsInteger($input->getArgument('expirationTimestamp'))) {
            $this->expirationTimestamp = MathUtility::forceIntegerInRange((int)$input->getArgument('expirationTimestamp'), 0);
        }

        if ($this->expirationTimestamp <= 0) {
            $this->io->error('"expirationTimestamp" has to a positive integer value. Abort.');
            return 1;
        }

        $time = new \DateTime();
        $expirationTimestamp = $time->getTimestamp() + ((60 * 60 * 24) * $this->expirationTimestamp);

        $this->io->text('Get fe_users with nearly expiration documents.');
        // get fe users with requests for access loop
        $expireAccessUsers = $this->AccessRepository->findExpirationUsers($expirationTimestamp);
        $this->io->text(count($expireAccessUsers) . ' fe_users with expiring documents were found.');

        if (!empty($expireAccessUsers)) {
            foreach ($expireAccessUsers as $expireUser) {
                /** @var User $feUser */
                $feUser = $this->UserRepository->findByUid($expireUser->getFeUser());
                $expiringAccessEntries = $this->AccessRepository->findExpiringEntriesByUser($expireUser->getFeUser(), $expirationTimestamp);

                if (!empty($expiringAccessEntries)) {
                    $this->io->text(sprintf('Notify fe_user (UID: %s) with %s expiring documents.', $expireUser->getFeUser(), count($expiringAccessEntries)));

                    $this->notifyUser($feUser, $expiringAccessEntries);
                    $this->persistenceManager->persistAll();
                }
            }
        }

        $this->io->success('Task finished successfully.');

        return 0;
    }

    /**
     * Update access model object and set accessGrantedNotification
     *
     * @param Access $accessEntry
     * @param string $notificationTimestamp
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    protected function updateAccessEntry(Access $accessEntry, int $notificationTimestamp)
    {
        // update access entry with notification time
        $accessEntry->setExpireNotification($notificationTimestamp);
        $this->AccessRepository->update($accessEntry);
    }

    /**
     * Send email to fe_users with expiring kitodo documents
     *
     * @param User $feUser
     * @param array $documentsList
     */
    protected function sendNotificationEmail(User $feUser, array $documentsList)
    {
        $this->initUserLocal($feUser);
        $userEmail = $feUser->getEmail();
        $userFullName = $feUser->getFullName();
        if (!GeneralUtility::validEmail($userEmail)) {
            $this->io->warning(sprintf('[DiGAS FE Management] Expiration notification warning to user (UID: %s) could not be sent. No valid email address.', $feUser->getUid()));
            return;
        }
        $email = GeneralUtility::makeInstance(MailMessage::class);

        $textEmail = $this->generateNotificationEmail(
            $documentsList,
            'EXT:digas_fe_management/Resources/Private/Templates/Email/Text/KitodoAccessExpirationNotification.html'
        );
        $htmlEmail = $this->generateNotificationEmail(
            $documentsList,
            'EXT:digas_fe_management/Resources/Private/Templates/Email/Html/KitodoAccessExpirationNotification.html',
            'html'
        );
        $emailSubject = ExtbaseLocalizationUtility::translate('kitodoAccessExpirationNotification.email.subject', 'DigasFeManagement');

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
}
