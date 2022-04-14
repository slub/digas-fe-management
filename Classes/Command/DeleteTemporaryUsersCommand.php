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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Class DeleteInactiveAccountsCommand
 *
 */
class DeleteTemporaryUsersCommand extends DigasBaseCommand
{
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
        $this->setDescription('[DiGAS FE Management] Delete temporary commercial created fe_users.')
            ->addArgument(
                'timespan',
                InputArgument::REQUIRED,
                'Add a timespan in days (i.e. "30"). Temporary commercial fe_users accounts will be deleted after this timespan.'
            )->setHelp(
                'This command allows to delete temporary commercial fe_users accounts after given timespan.'
            );
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Initialize IO
        parent::execute($input, $output);

        $deleteCounter = 0;
        $timespan = null;
        if (MathUtility::canBeInterpretedAsInteger($input->getArgument('timespan'))) {
            $timespan = MathUtility::forceIntegerInRange((int)$input->getArgument('timespan'), 0);
        }
        if ($timespan <= 0) {
            $this->io->error('"timespan" has to a positive integer value. Abort.');
            return 1;
        }

        $time = new \DateTime();
        $deleteTimestamp = $time->getTimestamp() - ((60 * 60 * 24) * $timespan);

        // set storage pid for temporary fe_users
        $this->UserRepository->setStoragePid($this->settings['pids.']['kitodoTempUserPid']);

        // delete temporary commercial fe_users
        $feUsers = $this->UserRepository->findTemporaryAccounts($deleteTimestamp, $this->kitodoTempUserGroup);

        if (!empty($feUsers)) {
            $deleteCounter = $this->deleteFeUsers($feUsers);
            if ($deleteCounter !== false) {
                $this->persistenceManager->persistAll();
            } else {
                $this->io->error('Task not finished successfully due to former errors.');
                return 1;
            }
        }

        $this->io->success('Task finished successfully. Deleted fe_users entries: ' . $deleteCounter);

        return 0;
    }
}
