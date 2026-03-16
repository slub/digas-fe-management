<?php

namespace Slub\DigasFeManagement\Controller;

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

use In2code\Femanager\Controller\AbstractController;
use In2code\Femanager\Utility\LocalizationUtility;
use Slub\DigasFeManagement\Domain\Model\Statistic;
use Slub\DigasFeManagement\Domain\Validator\StatisticTstampValidator;
use Slub\SlubWebDigas\Domain\Repository\KitodoDocumentRepository;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/**
 * Class StatisticController
 */
class StatisticController extends AbstractController
{
    /**
     * This holds the current document
     *
     * @var \Kitodo\Dlf\Domain\Model\Document
     * @access protected
     */
    protected $document;

    /**
     * @var \Slub\DigasFeManagement\Domain\Repository\StatisticRepository
     */
    protected $statisticRepository;

    /**
     * @param \Slub\DigasFeManagement\Domain\Repository\StatisticRepository $statisticRepository
     */
    public function injectStatisticRepository(\Slub\DigasFeManagement\Domain\Repository\StatisticRepository $statisticRepository)
    {
        $this->statisticRepository = $statisticRepository;
    }

    /**
     * kitodoDocumentRepository
     *
     * @var KitodoDocumentRepository
     */
    protected $kitodoDocumentRepository = null;

    /**
     * @param KitodoDocumentRepository $kitodoDocumentRepository
     */
    public function injectKitodoDocumentRepository(KitodoDocumentRepository $kitodoDocumentRepository)
    {
        $this->kitodoDocumentRepository = $kitodoDocumentRepository;
    }

    /**
     * Set the Kitodo storagePid to plugin.tx_dlf.persistence.storagePid
     *
     * Background: We need two different settings here:
     *    * one for Kitodo.Presentation (dlf) Repository
     *    * one for User-Data, which is set via plugin.tx_digasfemangement.persistence.storagePid
     *
     * It is not that simple (or I found no better way) to set the storagePid manually. This is one possible way.
     *
     */
    protected function setKitodoStoragePid()
    {
        $typoscriptConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        if (!empty($typoscriptConfiguration['plugin.']['tx_dlf.']['persistence.']['storagePid'])) {
            /** @var Typo3QuerySettings $defaultQuerySettings */
            $defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);

            // overwrite (probably empty) storagePid
            $defaultQuerySettings->setStoragePageIds([$typoscriptConfiguration['plugin.']['tx_dlf.']['persistence.']['storagePid']]);
            $this->kitodoDocumentRepository->setDefaultQuerySettings($defaultQuerySettings);
        }
    }

    /**
     * Download link action - adds statistic entry for dlf_document download
     * @param string $id
     * @param string $countType
     */
    public function downloadLinkAction(string $id, string $countType)
    {
        // make sure, the user is logged in
        if (empty($this->user->getUid())) {
            $this->view->assign('counted', -1);
            return;
        }

        // set the storagePid of kitodoDocumentRepository
        $this->setKitodoStoragePid();

        // first we find the document object
        // $id is the document UID passed from the frontend
        $this->document = $this->kitodoDocumentRepository->findByUid((int)$id);

        if (! $this->document) {
            $this->view->assign('counted', -1);
            return;
        }

        // check, if there is a record from the last 24h
        $statisticEntry = $this->statisticRepository->findOneByFeUserAndDocument($this->user->getUid(), $this->document->getUid());

        $isUpdate = true;

        if ($statisticEntry == null) {
            $isUpdate = false;
            // second we count the new download
            $statisticEntry = new Statistic();

            $statisticEntry->setFeUser($this->user);
            $statisticEntry->setDocument($this->document->getUid());
        }

        switch ($countType) {
            case 'work':
                $statisticEntry->setDownloadWork(1);
                break;
            case 'page':
                $statisticEntry->incDownloadPages();
                break;
            case 'workview':
                $statisticEntry->setWorkViews(1);
                break;
        }

        if ($isUpdate === true) {
            $this->statisticRepository->update($statisticEntry);
            $this->view->assign('counted', 1);
        } else {
            $this->statisticRepository->add($statisticEntry);
            $this->view->assign('counted', 2);
        }

        return;
    }

    /**
     * User statistic action
     *
     * @throws NoSuchArgumentException
     */
    public function viewAction()
    {
        if (!$this->user) {
            // if no logged-in user is present, the plugin will fail --> abort
            return false;
        }

        $statistic = $this->getStatistic($this->user->getUid());

        $this->view->assignMultiple([
            'statistic' => $statistic['result'],
            'totals' => $statistic['totals'],
            'dateFrom' => $statistic['dateFrom'],
            'dateTo' => $statistic['dateTo']
        ]);
    }

    /**
     * View single user statistic action
     *
     * @throws NoSuchArgumentException
     */
    public function viewSingleAction()
    {
        $adminRequest = GeneralUtility::_GP('tx_digasfemanagement_administration');

        if ($adminRequest['user'] > 0 && $adminRequest['action'] === 'show') {
            $userId = $adminRequest['user'];
        } else {
            return false;
        }

        $statistic = $this->getStatistic($userId);

        $this->view->assignMultiple([
            'userId' => $userId,
            'statistic' => $statistic['result'],
            'totals' => $statistic['totals'],
            'dateFrom' => $statistic['dateFrom'],
            'dateTo' => $statistic['dateTo']
        ]);
    }

    /**
     * Admin statistic action
     *
     * @throws NoSuchArgumentException
     */
    public function administrationAction()
    {
        $statistic = $this->getStatistic();

        $this->view->assignMultiple([
            'statistic' => $statistic['result'],
            'totals' => $statistic['totals'],
            'dateFrom' => $statistic['dateFrom'],
            'dateTo' => $statistic['dateTo']
        ]);
    }

    /**
     * Compute totals across all statistic entries.
     *
     * @param array $result
     *
     * @return array
     */
    private function computeTotals(array $result): array
    {
        $totals = ['downloadPages' => 0, 'downloadWork' => 0, 'workViews' => 0];
        foreach ($result as $entry) {
            $totals['downloadPages'] += $entry->getDownloadPages();
            $totals['downloadWork']  += $entry->getDownloadWork();
            $totals['workViews']     += $entry->getWorkViews();
        }
        return $totals;
    }

    /**
     * @param array $filterParams
     *
     * @return array
     */
    protected function validateStatisticFilter(array $filterParams): array
    {
        return [
            'dateFrom' => $this->validateDate($filterParams, 'dateFrom'),
            'dateTo' => $this->validateDate($filterParams, 'dateTo')
        ];
    }

    /**
     * Validate date
     *
     * @param array $filterParams
     * @param string $dateToValidate
     *
     * @return string|null
     */
    private function validateDate(array $filterParams, string $dateToValidate)
    {
        $date = null;
        $dateValidator = new StatisticTstampValidator();

        if (!empty($filterParams[$dateToValidate])) {
            $date = $filterParams[$dateToValidate];
            $dateError = $dateValidator->validate($date);
            if ($dateError->hasErrors() === true) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('statistic.' . $dateToValidate. '.validationError', 'DigasFeManagement'),
                    '',
                    AbstractMessage::ERROR
                );
            }
        }

        return $date;
    }

    /**
     * Get statistic data.
     *
     * @param int|null $userId
     *
     * @return array
     *
     * @throws NoSuchArgumentException
     */
    private function getStatistic($userId = null): array
    {
        $requestMethod = $this->request->getMethod();
        $filterSubmit = $this->request->hasArgument('statistic');

        if ($requestMethod === 'POST' && $filterSubmit === true) {
            $filterParams = $this->request->getArgument('statistic');
            $statisticFilter = $this->validateStatisticFilter($filterParams);
            $result = $this->statisticRepository->findForFilter($statisticFilter['dateFrom'], $statisticFilter['dateTo'], $userId);

            return [
                'result' => $result,
                'totals' => $this->computeTotals($result),
                'dateFrom' => $statisticFilter['dateFrom'],
                'dateTo' => $statisticFilter['dateTo']
            ];

        } else {
            $result = $this->statisticRepository->findForStatistic($userId);

            return [
                'result' => $result,
                'totals' => $this->computeTotals($result),
                'dateFrom' => null,
                'dateTo' => date('Y-m-d')
            ];
        }
    }
}
