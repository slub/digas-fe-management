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

use Slub\DigasFeManagement\Domain\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

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
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->UserRepository = $this->objectManager->get(UserRepository::class);
        $this->persistenceManager = $this->objectManager->get(PersistenceManager::class);

        $configurationManager = $this->objectManager->get('TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface');
        $typoscriptConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        if (!empty($extSettings = $typoscriptConfiguration['plugin.']['tx_femanager.']['settings.'])) {
            $this->settings = $extSettings;
            $this->UserRepository->setStoragePid($this->settings['pids.']['feUsers']);
        }
    }

    /**
     * Base Initialization
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());

        // throw error if necessary typoscript configuration is missing
        if (empty($this->settings['pids.']['feUsers'])) {
            $this->io->error('[DiGAS FE Management] Typoscript variable {plugin.tx_femanager.settings.pids.feUsers} is not set. Abort.');
            return;
        }
        if (empty($this->settings['pids.']['loginPage'])) {
            $this->io->error('[DiGAS FE Management] Typoscript variable {plugin.tx_femanager.settings.pids.loginPage} is not set. Abort.');
            return;
        }

        if (empty($this->settings['feUserGroup'])) {
            $this->io->error('[DiGAS FE Management] Typoscript variable {plugin.tx_femanager.settings.feUserGroup} is not set. Abort.');
            return;
        }

        if (empty($this->settings['adminName'])) {
            $this->io->error('[DiGAS FE Management] Typoscript variable {plugin.tx_femanager.settings.adminName} is not set. Abort.');
            return;
        }

        if (empty($this->settings['adminEmail'])) {
            $this->io->error('[DiGAS FE Management] Typoscript variable {plugin.tx_femanager.settings.adminEmail} is not set. Abort.');
            return;
        }
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
                $this->io->error(sprintf('[DiGAS FE Management] User (UID: %s) could not be deleted. Error Message: %s', $feUser->getUid(), $e->getMessage()));
                return false;
            }
        }
        return $deleteCounter;
    }
}
