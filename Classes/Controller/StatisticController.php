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
use Slub\SlubDigitalcollections\Helpers\GetDoc;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class StatisticController
 */
class StatisticController extends AbstractController
{
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
     * Download link action - adds statistic entry for dlf_document download
     * @param int $id
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function downloadLinkAction(int $id)
    {
        //test is user is logged in
        if (empty($this->user) || empty($this->user->getUid())) {
            $uriBuilder = $this->uriBuilder;
            $uri = $uriBuilder->setTargetPageUid($this->settings['pids']['loginPage'])->build();
            return $this->redirectToUri($uri);
        }

        $statisticEntry = new Statistic();

        $statisticEntry->setFeUser($this->user);
        $statisticEntry->setDocument($id);

        $this->statisticRepository->add($statisticEntry);
        $this->persistenceManager->persistAll();

        $documentLink = new GetDoc();
        $documentLink = $documentLink->getWorkLink();


        if (!empty($documentLink) && GeneralUtility::isValidUrl($documentLink)) {
            return $this->redirectToUri($documentLink);
        } else {
            // @todo redirect if no document could be loaded.
            return false;
        }
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
