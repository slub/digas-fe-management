<?php

namespace Slub\DigasFeManagement\Domain\Repository;

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
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * DiGAS Frontend User repository
 */
class UserRepository extends Repository
{
    /**
     * @var int Storage PID
     */
    protected $storagePid;

    /**
     * @param int $storagePid
     */
    public function setStoragePid($storagePid)
    {
        $this->storagePid = $storagePid;
    }

    /**
     * @param int $deleteTimespan The crdate timestamp to check
     * @param string $feUserGroup Frontend user group (must be set in constants {$femanager.feUserGroup})
     * @return User[]
     */
    public function findInactiveAccounts($deleteTimespan, $feUserGroup)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setStoragePageIds([$this->storagePid])
            ->setIgnoreEnableFields(true);

        /** @var User[] $user */
        $user = $query->matching(
            $query->logicalAnd([
                $query->equals('disable', true),
                $query->equals('usergroup', $feUserGroup),
                $query->equals('tx_femanager_confirmedbyuser', 0),
                $query->equals('tx_femanager_confirmedbyadmin', 0),
                $query->lessThan('crdate', $deleteTimespan)
            ]))
            ->execute()
            ->toArray();

        return $user;
    }

    /**
     * @param int $unusedTimestamp
     * @param string $feUserGroup Frontend user group (must be set in constants {$femanager.feUserGroup})
     * @return User[]
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findUnusedAccounts(int $unusedTimestamp, $feUserGroup)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds([$this->storagePid]);

        /** @var User[] $user */
        $user = $query->matching(
            $query->logicalAnd([
                $query->lessThan('lastlogin', $unusedTimestamp),
                $query->equals('disable', false),
                $query->equals('tx_femanager_confirmedbyuser', 1),
                $query->equals('usergroup', $feUserGroup),
                $query->equals('inactivemessage_tstamp', null)
            ]))
            ->execute()
            ->toArray();

        return $user;
    }

    /**
     * @param int $deleteTimestamp
     * @param string $feUserGroup Frontend user group (must be set in constants {$femanager.feUserGroup})
     * @return User[]
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAccountsToDelete(int $deleteTimestamp, $feUserGroup)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds([$this->storagePid]);

        /** @var User[] $user */
        $user = $query->matching(
            $query->logicalAnd([
                $query->lessThan('lastlogin', $deleteTimestamp),
                $query->equals('usergroup', $feUserGroup),
                $query->lessThan('inactivemessage_tstamp', $deleteTimestamp)
            ]))
            ->execute()
            ->toArray();

        return $user;
    }

    /**
     * @param int $timestamp
     * @param string $feUserGroup Frontend user group (must be set in constants {$femanager.feUserGroup})
     * @return User[]
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findDeactivatedAccounts(int $timestamp, $feUserGroup)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setStoragePageIds([$this->storagePid])
            ->setIgnoreEnableFields(true);

        /** @var User[] $user */
        $user = $query->matching(
            $query->logicalAnd([
                $query->equals('disable', true),
                $query->equals('usergroup', $feUserGroup),
                $query->lessThan('inactivemessage_tstamp', $timestamp)
            ]))
            ->execute()
            ->toArray();

        return $user;
    }
}
