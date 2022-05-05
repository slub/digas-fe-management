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

use Slub\DigasFeManagement\Domain\Model\Statistic;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class StatisticRepository
 * @package Slub\DigasFeManagement\Domain\Repository
 */
class StatisticRepository extends Repository
{
    /**
     * @var string
     */
    protected $tableName = 'tx_digasfemanagement_domain_model_statistic';

    /**
     * Add "dateFrom" filter
     * @param \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder
     * @param string $dateFrom
     * @return string
     */
    protected function addFilterDateFrom($queryBuilder, $dateFrom)
    {
        $timestamp = strtotime($dateFrom);
        return $queryBuilder->expr()->gte('tstamp', $timestamp);
    }

    /**
     * Add "dateTo" filter.
     * Set "dateTo" to today if no date is given
     *
     * @param \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder
     * @param $dateTo
     * @return string
     * @throws \Exception
     */
    protected function addFilterDateTo($queryBuilder, $dateTo)
    {
        $timestamp = strtotime($dateTo);

        // use the whole day timestamp if filter is today
        if ($dateTo === date('Y-m-d')) {
            $date = new \DateTime($dateTo);
            $date->modify('+1 day');
            $timestamp = $date->getTimestamp();
        }
        return $queryBuilder->expr()->lte('tstamp', $timestamp);
    }

    /**
     * Filter statistic data
     *
     * @param int|null $dateFrom
     * @param int|null $dateTo
     * @param int|null $feUserUid
     * @return array
     */
    public function findForFilter($dateFrom = null, $dateTo = null, $feUserUid = null)
    {
        $queryBuilder = $this->createStatisticQuery();
        $filterConditions = [];

        if (!empty($dateFrom)) {
            $filterConditions[] = $this->addFilterDateFrom($queryBuilder, $dateFrom);
        }

        if (!empty($dateTo)) {
            $filterConditions[] = $this->addFilterDateTo($queryBuilder, $dateTo);
        }

        if (!empty($feUserUid)) {
            $filterConditions[] = $queryBuilder->expr()->eq('fe_user', $feUserUid);
        }

        $rows = $queryBuilder->where(...$filterConditions)
            ->execute()
            ->fetchAll();

        return $this->dataMapQueryResult($rows);
    }

    /**
     * Create statistic filter query
     *
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected function createStatisticQuery()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->tableName)->createQueryBuilder();
        $queryBuilder->select('*')
            ->addSelectLiteral($queryBuilder->expr()->count('uid', 'download_work'))
            ->addSelectLiteral($queryBuilder->expr()->sum('download_pages', 'download_pages'))
            ->addSelectLiteral($queryBuilder->expr()->sum('work_views', 'work_views'))
            ->from($this->tableName)
            ->orderBy('download_work', 'DESC')
            ->groupBy('document');

        return $queryBuilder;
    }

    /**
     * Map statistic query result to statistic objects
     *
     * @param array $rows
     * @return array
     */
    protected function dataMapQueryResult($rows)
    {
        /** @var DataMapper $dataMapper */
        $dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);

        return $dataMapper->map(Statistic::class, $rows);
    }

    /**
     * Get whole statistic
     *
     * @param int|null $feUserUid
     * @return array
     */
    public function findForStatistic($feUserUid = null)
    {
        $queryBuilder = $this->createStatisticQuery();

        if (!empty($feUserUid)) {
            $queryBuilder->where($queryBuilder->expr()->eq('fe_user', $feUserUid));
        }

        $rows = $queryBuilder->execute()->fetchAll();

        return $this->dataMapQueryResult($rows);

    }


    /**
     * Find one statistic record for the given user and document of the last 24h.
     *
     * @param int $feUserUid
     * @param int $documentId
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findOneByFeUserAndDocument($feUserUid, $documentId)
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->equals('fe_user', $feUserUid);
        $constraints[] = $query->equals('document', $documentId);
        $constraints[] = $query->greaterThan('tstamp', time() - 86400);

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        return $query->execute()->getFirst();
    }
}
