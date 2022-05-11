<?php

namespace Slub\DigasFeManagement\Domain\Repository;

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
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * DiGA.Sax Frontend User repository
 */
class AccessRepository extends Repository
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
     * @var string
     */
    protected $tableName = 'tx_digasfemanagement_domain_model_access';

    /**
     * @param int $feUserId
     * @return array
     */
    public function findRequestsForUser(int $feUserId): array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        return $query->matching(
            $query->logicalAnd([
                $query->equals('fe_user', $feUserId),
            ]))
            ->execute()
            ->toArray();
    }

    /**
     * @param int $feUserId
     * @return Access[]
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAccessGrantedEntriesByUser(int $feUserId): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->tableName)->createQueryBuilder();
        // starttime + endtime get checked by typo3 core logic
        //remove hidden restriction
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $rows = $queryBuilder->select('*')
            ->where(
                $queryBuilder->expr()->eq($this->tableName . '.access_granted_notification', 0),
                $queryBuilder->expr()->eq($this->tableName . '.inform_user', 1),
                $queryBuilder->expr()->eq($this->tableName . '.fe_user', $feUserId),
            )
            ->from($this->tableName)
            ->orderBy($this->tableName . '.rejected','ASC')
            ->execute()
            ->fetchAll();

        $dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);

        return $dataMapper->map(Access::class, $rows);
    }

    /**
     * Get uids of fe_users with granted access
     *
     * @return Access[]
     */
    public function findAccessGrantedUsers()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->tableName)->createQueryBuilder();
        // starttime + endtime get checked by typo3 core logic
        //remove hidden restriction
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $rows = $queryBuilder->select('*')
            ->where(
                $queryBuilder->expr()->eq($this->tableName . '.access_granted_notification', 0),
                $queryBuilder->expr()->eq($this->tableName . '.inform_user', true),
            )
            ->from($this->tableName)
            ->groupBy($this->tableName . '.fe_user')
            ->execute()
            ->fetchAll();

        $dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);
        return $dataMapper->map(Access::class, $rows);
    }

    /**
     * Get uids of fe_users with expiring document access
     *
     * @param int $expirationTimestamp
     * @return Access[]
     */
    public function findExpirationUsers($expirationTimestamp)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->tableName)->createQueryBuilder();
        $rows = $queryBuilder->select('*')
            ->where(
                $queryBuilder->expr()->eq($this->tableName . '.expire_notification', 0),
                $queryBuilder->expr()->lte($this->tableName . '.endtime', $expirationTimestamp)
            )
            ->from($this->tableName)
            ->groupBy($this->tableName . '.fe_user')
            ->execute()
            ->fetchAll();

        $dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);
        return $dataMapper->map(Access::class, $rows);
    }

    /**
     * @param int $feUserId
     * @param int $expirationTimestamp
     * @return array
     */
    public function findExpiringEntriesByUser(int $feUserId, $expirationTimestamp)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->tableName)->createQueryBuilder();

        // starttime + endtime get checked by typo3 core logic
        $rows = $queryBuilder->select('*')
            ->where(
                $queryBuilder->expr()->eq($this->tableName . '.expire_notification', 0),
                $queryBuilder->expr()->lte($this->tableName . '.endtime', $expirationTimestamp),
                $queryBuilder->expr()->eq($this->tableName . '.fe_user', $feUserId)
            )
            ->from($this->tableName)
            ->execute()
            ->fetchAll();

        $dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);

        return $dataMapper->map(Access::class, $rows);
    }
}
