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

use Slub\SlubWebDigas\Domain\Model\KitodoDocument;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

class KitodoDocumentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var string
     */
    protected $tableName = 'tx_dlf_documents';

    /**
     * Find Kitodo documents by record_id
     *
     * @param array $documentIds
     * @return array
     */
    public function findDocumentsByRecordId(array $documentIds)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->tableName)->createQueryBuilder();
        $documents = $queryBuilder->select('*')
            ->where(
                $queryBuilder->expr()->in('record_id', $queryBuilder->createNamedParameter($documentIds, Connection::PARAM_STR_ARRAY)),
                $queryBuilder->expr()->eq('restrictions', $queryBuilder->createNamedParameter('ja'))
            )
            ->from($this->tableName)
            ->execute()
            ->fetchAll();

        return $this->dataMapQueryResult($documents);
    }

    /**
     * Map statistic query result to statistic objects
     *
     * @param array $rows
     * @return KitodoDocument[]
     */
    protected function dataMapQueryResult(array $rows): array
    {
        /** @var DataMapper $dataMapper */
        $dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);

        return $dataMapper->map(KitodoDocument::class, $rows);
    }
}
