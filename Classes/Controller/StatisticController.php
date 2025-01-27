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
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Slub\SlubWebDigas\Domain\Repository\KitodoDocumentRepository;

use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
        $this->document = $this->kitodoDocumentRepository->findOneByRecordId($id);

        if (! $this->document) {
            $this->view->assign('counted', -1);
            return;
        }

        // check, if there is a record from the last 24h
        $statisticEntry = $this->statisticRepository->findOneByFeUserAndDocument($this->user, $this->document->getUid());

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
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function viewAction()
    {
        if (!$this->user) {
            // if no logged in user is present, the plugin will fail --> abort
            return false;
        }

        $requestMethod = $this->request->getMethod();
        $filterSubmit = $this->request->hasArgument('statistic');

        if ($requestMethod === 'POST' && $filterSubmit === true) {
            $filterParams = $this->request->getArgument('statistic');
            $statisticFilter = $this->validateStatisticFilter($filterParams);
            $statistic = $this->statisticRepository->findForFilter($statisticFilter['dateFrom'], $statisticFilter['dateTo'], $this->user->getUid());
        } else {
            $statistic = $this->statisticRepository->findForStatistic($this->user->getUid());
        }

        $this->view->assignMultiple([
            'statistic' => $statistic,
            'dateFrom' => $statisticFilter['dateFrom'] ?? null,
            'dateTo' => $statisticFilter['dateTo'] ?? date('Y-m-d')
        ]);
    }

    /**
     * View single user statistic action
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function viewSingleAction()
    {
        $requestMethod = $this->request->getMethod();
        $filterSubmit = $this->request->hasArgument('statistic');

        $adminRequest = GeneralUtility::_GP('tx_digasfemanagement_administration');

        if ($adminRequest['user'] > 0 && $adminRequest['action'] === 'show') {
            $userId = $adminRequest['user'];
        } else {
            return false;
        }

        if ($requestMethod === 'POST' && $filterSubmit === true) {
            $filterParams = $this->request->getArgument('statistic');
            $statisticFilter = $this->validateStatisticFilter($filterParams);
            $statistic = $this->statisticRepository->findForFilter($statisticFilter['dateFrom'], $statisticFilter['dateTo'], $userId);
        } else {
            $statistic = $this->statisticRepository->findForStatistic($userId);
        }

        $this->view->assignMultiple([
            'userId' => $userId,
            'statistic' => $statistic,
            'dateFrom' => $statisticFilter['dateFrom'] ?? null,
            'dateTo' => $statisticFilter['dateTo'] ?? date('Y-m-d')
        ]);
    }

    /**
     * Admin statistic action
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function administrationAction()
    {
        $requestMethod = $this->request->getMethod();
        $filterSubmit = $this->request->hasArgument('statistic');

        if ($requestMethod === 'POST' && $filterSubmit === true) {
            $filterParams = $this->request->getArgument('statistic');
            $statisticFilter = $this->validateStatisticFilter($filterParams);

            $statistic = $this->statisticRepository->findForFilter($statisticFilter['dateFrom'], $statisticFilter['dateTo']);
        } else {
            $statistic = $this->statisticRepository->findForStatistic();
        }

        $this->view->assignMultiple([
            'statistic' => $statistic,
            'dateFrom' => $statisticFilter['dateFrom'] ?? null,
            'dateTo' => $statisticFilter['dateTo'] ?? date('Y-m-d')
        ]);
    }

    /**
     * @param array $filterParams
     * @return array
     */
    protected function validateStatisticFilter($filterParams)
    {
        $dateFrom = null;
        $dateTo = null;
        $dateValidator = new StatisticTstampValidator();

        if (!empty($filterParams['dateFrom'])) {
            $dateFrom = $filterParams['dateFrom'];
            $dateFromError = $dateValidator->validate($dateFrom);
            if ($dateFromError->hasErrors() === true) {
                $this->addFlashMessage(LocalizationUtility::translate('statistic.dateFrom.validationError', 'DigasFeManagement'), '', FlashMessage::ERROR);
            }
        }

        if (!empty($filterParams['dateTo'])) {
            $dateTo = $filterParams['dateTo'];
            $dateToError = $dateValidator->validate($dateTo);
            if ($dateToError->hasErrors() === true) {
                $this->addFlashMessage(LocalizationUtility::translate('statistic.dateTo.validationError', 'DigasFeManagement'), '', FlashMessage::ERROR);
            }
        }

        return [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];
    }


}
