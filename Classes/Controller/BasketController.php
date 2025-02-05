<?php

namespace Slub\DigasFeManagement\Controller;

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

use In2code\Femanager\Controller\AbstractController;
use In2code\Femanager\Utility\StringUtility;
use Slub\DigasFeManagement\Domain\Model\Access;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class BasketController
 */
class BasketController extends AbstractController
{
    /**
     * @var \Slub\DigasFeManagement\Domain\Repository\KitodoDocumentRepository
     */
    protected $kitodoDocumentRepository;

    /**
     * @param \Slub\DigasFeManagement\Domain\Repository\KitodoDocumentRepository $kitodoDocumentRepository
     */
    public function injectKitodoDocumentRepository(\Slub\DigasFeManagement\Domain\Repository\KitodoDocumentRepository $kitodoDocumentRepository)
    {
        $this->kitodoDocumentRepository = $kitodoDocumentRepository;
    }

    /**
     * @var \Slub\DigasFeManagement\Domain\Repository\AccessRepository
     */
    protected $accessRepository;

    /**
     * @param \Slub\DigasFeManagement\Domain\Repository\AccessRepository $accessRepository
     */
    public function injectAccessRepository(\Slub\DigasFeManagement\Domain\Repository\AccessRepository $accessRepository)
    {
        $this->accessRepository = $accessRepository;
    }

    /**
     * @var array
     */
    protected $documents;


    /**
     * @var array
     */
    protected $requestParams = [];


    /**
     * @throws StopActionException
     */
    public function initializeAction()
    {
        parent::initializeAction();

        $this->checkUserLoggedIn();

        $kitodoRequestIds = $this->getRequestIdsFromCookie();

        if (!empty($kitodoRequestIds) && is_array($kitodoRequestIds)) {
            $documents = $this->kitodoDocumentRepository->findDocumentsByRecordId($kitodoRequestIds);

            // update kitodo cookie if count differs
            if (count($kitodoRequestIds) !== count($documents)) {
                $kitodoDocuments = [];
                foreach ($documents as $document) {
                    $kitodoDocuments[] = $document->getRecordId();
                }
                setcookie('dlf-requests', json_encode($kitodoDocuments), 0, '/');
            }
        }

        // new requests
        $newDocumentRequests = [];
        // already requested documents
        $oldDocumentRequests = [];
        // rejected documents
        $rejectedDocumentRequests = [];

        // check if documents were already requested
        if (!empty($documents)) {
            // get former requested kitodo access
            $requestedAccess = $this->accessRepository->findRequestsForUser($this->user->getUid());
            $requestedDocuments = [];
            if (!empty($requestedAccess)) {
                /** @var Access $access */
                foreach ($requestedAccess as $access) {
                    $document = $access->getDlfDocument();
                    if ($document != NULL) {
                        $requestedDocuments[$document->getUid()] = $access;
                    }
                }
            }

            foreach ($documents as $key => $document) {
                // get rejected documents
                if (array_key_exists($document->getUid(), $requestedDocuments) && $requestedDocuments[$document->getUid()]->getRejected()) {
                    $rejectedDocumentRequests[$document->getUid()] = $requestedDocuments[$document->getUid()];
                } // get documents that are already accessible or waiting for access
                elseif (array_key_exists($document->getUid(), $requestedDocuments)) {
                    // remove document from array for email handling
                    $oldDocumentRequests[$document->getUid()] = $requestedDocuments[$document->getUid()];
                } // new documents
                else {
                    $newDocumentRequests[$document->getUid()] = $document;
                }
            }
        }

        $this->documents = [
            'newDocumentRequests' => $newDocumentRequests,
            'oldDocumentRequests' => $oldDocumentRequests,
            'rejectedDocumentRequests' => $rejectedDocumentRequests
        ];

        // get request parameters
        $this->requestParams = !empty($this->request->getArguments()['request']) ? filter_var_array($this->request->getArguments()['request'], FILTER_SANITIZE_STRING) : [];
    }

    /**
     * action request
     *
     * @return void
     * @throws StopActionException
     */
    public function indexAction()
    {
        $this->view->assignMultiple([
            'documents' => [
                'oldDocumentRequests' => $this->documents['oldDocumentRequests'],
                // join new requests for access with rejected documents that can be requested again
                'newDocumentRequests' => array_merge($this->documents['newDocumentRequests'], $this->documents['rejectedDocumentRequests'])
            ],
            'request' => $this->requestParams
        ]);

        // add basket empty message
        if (!$this->documents['newDocumentRequests'] && !$this->documents['oldDocumentRequests'] && !$this->requestParams['sent']) {
            $this->addFlashMessage(
                LocalizationUtility::translate('basket.empty', 'DigasFeManagement'), '', -2
            );
        }
    }

    public function overviewAction()
    {
        $documents = array_merge($this->documents['newDocumentRequests'], $this->documents['rejectedDocumentRequests']);

        if (empty($documents)) {
            $this->redirect('index');
        }

        $this->view->assignMultiple([
            //join new requests for access with rejected documents that can be requested again
            'documents' => $documents,
            'request' => $this->requestParams
        ]);
    }

    /**
     * action request
     *
     * @return void
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function requestAction()
    {
        $documents = [];

        if (!empty($this->documents)) {
            // request new documents
            if (!empty($this->documents['newDocumentRequests'])) {
                foreach ($this->documents['newDocumentRequests'] as $key => $document) {
                    $requestAccess = new Access();
                    $requestAccess->setFeUser($this->user->getUid());
                    $requestAccess->setRecordId($document->getRecordId());
                    $requestAccess->setHidden(true);
                    $requestAccess->setDlfDocument($document);

                    $this->accessRepository->add($requestAccess);
                    $documents[] = $requestAccess;
                }
            }

            // request rejected documents again
            if (!empty($this->documents['rejectedDocumentRequests'])) {
                /** @var Access $accessRejectedNewRequest */
                foreach ($this->documents['rejectedDocumentRequests'] as $accessRejectedNewRequest) {
                    $accessRejectedNewRequest->setExpireNotification(0);
                    $accessRejectedNewRequest->setAccessGrantedNotification(0);
                    $accessRejectedNewRequest->setRejected(false);
                    $accessRejectedNewRequest->setHidden(true);
                    $accessRejectedNewRequest->setStartTime(0);
                    $accessRejectedNewRequest->setEndTime(0);
                    $accessRejectedNewRequest->setInformUser(false);
                    $this->accessRepository->update($accessRejectedNewRequest);
                    $documents[] = $accessRejectedNewRequest;
                }
            }

            setcookie('dlf-requests', '[]', 0, '/');

            $message = !empty($this->requestParams['message']) ? $this->requestParams['message'] : '';
            $this->sendRequestAccessMail($documents, $message);

            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'requestKitodoAccess.success',
                    'DigasFeManagement'
                )
            );
            $this->redirect('index', null, null, ['request' => ['sent' => true]]);
        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'requestKitodoAccess.error',
                    'DigasFeManagement'
                ),
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        }

        $this->redirect('index');
    }

    /**
     * test is user is logged in
     *
     * @throws StopActionException
     */
    protected function checkUserLoggedIn()
    {
        // @phpstan-ignore-next-line in fact user is not always set even if var definition says so
        if (empty($this->user) || empty($this->user->getUid())) {
            $uriBuilder = $this->uriBuilder;
            $uri = $uriBuilder->setTargetPageUid($this->settings['pids']['loginPage'])->build();
            $this->redirectToUri($uri);
        }
    }

    /**
     * Send request access mail with kitodo documents and reset basket cookie
     *
     * @param Access[] $documents
     * @param string $message
     * @return bool
     */
    protected function sendRequestAccessMail(array $documents, string $message = ''): bool
    {
        // send email for access to kitodo documents
        $variables = [
            'user' => $this->user,
            'settings' => $this->settings,
            'documents' => $documents,
            'message' => $message
        ];

        $mailSentAdmin = $this->sendMailService->send(
            'requestKitodoAccess',
            StringUtility::makeEmailArray($this->settings['adminEmail'], $this->settings['adminName']),
            StringUtility::makeEmailArray($this->settings['adminEmail'], $this->settings['adminName']),
            'Dokumentfreigabe beantragt',
            $variables,
            $this->config['requestKitodoAccess.']['email.']
        );

        //mail to user
        $this->sendMailService->send(
            'requestKitodoAccessUser',
            StringUtility::makeEmailArray($this->user->getEmail(), $this->user->getFirstName() . ' ' . $this->user->getLastName()),
            StringUtility::makeEmailArray($this->settings['adminEmail'], $this->settings['adminName']),
            'Dokumentfreigabe beantragt',
            $variables,
            $this->config['requestKitodoAccessUser.']['email.']
        );

        return $mailSentAdmin;
    }

    /**
     * Get kitodo request ids from basket cookie
     *
     * @return array
     */
    protected function getRequestIdsFromCookie(): array
    {
        $kitodoRequestIds = $_COOKIE['dlf-requests'];
        if (!empty($kitodoRequestIds)) {
            return json_decode($kitodoRequestIds);
        }
        return [];
    }
}
