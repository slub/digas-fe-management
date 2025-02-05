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
use Slub\DigasFeManagement\Domain\Model\Search;
use Slub\DigasFeManagement\Domain\Repository\SearchRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class SearchController
 */
class SearchController extends AbstractController
{

    /**
     * @access protected
     * @var SearchRepository
     */
    protected SearchRepository $searchRepository;

    /**
     * @access public
     *
     * @param SearchRepository $searchRepository
     *
     * @return void
     */
    public function injectSearchRepository(SearchRepository $searchRepository): void
    {
        $this->searchRepository = $searchRepository;
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $userUid = $this->user->getUid();

        $this->view->assignMultiple([
            // @phpstan-ignore-next-line
            'savedSearches' => $this->searchRepository->findByFeUser($userUid),
            'token' => GeneralUtility::hmac((string)$userUid, (string) $this->user->getCrdate()->getTimestamp()),
            'user' => $userUid
        ]);
    }

    /**
     * action save
     *
     * @return void
     */
    public function saveAction()
    {

    }

    /**
     * action create
     *
     * @return void
     */
    public function createAction()
    {
        if ($this->user !==NULL && $this->user->getUid()) {

            $searchParams = GeneralUtility::_GP('tx_dlf_search');

            if (is_array($searchParams) && array_key_exists('query',$searchParams['searchParameter']) && array_key_exists('fulltext',$searchParams['searchParameter'])) {
                $arguments = $this->request->getArguments();

                $searchRequest = new Search();
                $title = $arguments['title'] ? $arguments['title'] : LocalizationUtility::translate('search', 'DigasFeManagement').': '.strftime('%d.%m.%y - %H:%M');

                $searchRequest->setTitle($title);
                $searchRequest->setSearchParams($searchParams['searchParameter']);
                $searchRequest->setFeUser($this->user->getUid());

                $this->searchRepository->add($searchRequest);

                $statusMessage = LocalizationUtility::translate(
                    'search.created.success',
                    'DigasFeManagement'
                );

                if (GeneralUtility::_GET('type') > 0) {
                    return;
                }

                $this->addFlashMessage($statusMessage);
            }
        }

        $this->redirect('save');
    }


    /**
     * action delete
     *
     * @param Search $search
     * @return void
     */
    public function deleteAction(Search $search)
    {
        if ($this->user !==NULL && $this->user->getUid() && $search->getFeUser() === $this->user->getUid()) {
            $this->searchRepository->remove($search);
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'search.deleted.success',
                    'DigasFeManagement'
                )
            );
        }
        $this->redirect('list');
    }
}
