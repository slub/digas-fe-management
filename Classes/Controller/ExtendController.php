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
use In2code\Femanager\Utility\StringUtility;
use In2code\Femanager\Utility\HashUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;

/**
 * Class ExtendController
 */
class ExtendController extends AbstractController
{
    /**
     * action dialog
     *
     * @return void
     * @throws AspectNotFoundException
     */
    public function dialogAction()
    {
        //get kitodo parameters
        $kitodoParams = GeneralUtility::_GET('tx_dlf');

        //get femanager paramters
        $femanagerParams = GeneralUtility::_GET('tx_femanager_pi1');

        //check if user is logged in
        $user = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user', 'id', 0) > 0;

        //check if fe_user exists AND action is create --> redirect
        if ($user && $femanagerParams['action'] === 'create') {
            try {
                //remove all femanager flashMessages concerning profile create / login
                $this->controllerContext->getFlashMessageQueue('extbase.flashmessages.tx_femanager_pi1')->getAllMessagesAndFlush();
                //redirect
                $this->redirectToKitodoView(['tx_dlf' => $kitodoParams]);
            } catch (StopActionException $e) {
            }
        }
        elseif ($femanagerParams['action'] === 'create') {
            $this->view->assign('checkYes', true);
        }

        // check if fe_user exists OR cookie for private usage is set
        // return empty string
        if ($user || $GLOBALS["TSFE"]->fe_user->getKey("ses", $this->settings['dialog']['cookieName'])) {
            return;
        }

        //get request arguments
        $arguments = $this->request->getArguments();

        //check if cookie for private usage should be set --> redirect
        if (isset($arguments['setCookie'])) {
            //set cookie for private user
            $GLOBALS["TSFE"]->fe_user->setKey("ses", $this->settings['dialog']['cookieName'], true);
            try {
                $this->redirectToKitodoView(['tx_dlf' => $kitodoParams]);
            } catch (StopActionException $e) {
            }
        }

        //add assets
        $this->addAssets();
        $this->view->assign('kitodoParams', $kitodoParams);
    }

    /**
     * @param array $parameters
     * @throws StopActionException
     */
    protected function redirectToKitodoView(array $parameters)
    {
        //build redirect uri
        $uriBuilder = $this->uriBuilder;
        $uri = $uriBuilder
            ->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setArguments($parameters)
            ->build();
        //redirect
        $this->redirectToURI($uri);
    }

    /**
     * add assets
     *
     * @return void
     */
    protected function addAssets()
    {
        //initialize PageRenderer
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        //add stylesheets
        if (!empty($this->settings['dialog']['assets']['css'])) {
            foreach ($this->settings['dialog']['assets']['css'] as $stylesheet) {
                $pageRenderer->addCssFile(
                    $stylesheet['file'],
                    'stylesheet',
                    'all',
                    '',
                    true,
                    $stylesheet['forceOnTop'],
                    '',
                    true
                );
            }
        }
    }

    /**
     * action disable
     *
     * @return void
     */
    public function disableAction()
    {

        if ($this->user !== NULL && $this->user->getUid()) {
            $arguments = $this->request->getArguments();

            $this->view->assignMultiple([
                'step' => $arguments['step']
            ]);

            if ($arguments['disable']) {

                // first send confirmation about deactivation of account
                $variables = ['user' => $this->user, 'settings' => $this->settings, 'hash' => HashUtility::createHashForUser($this->user)];

                $this->sendMailService->send(
                    'confirmDisableAction',
                    StringUtility::makeEmailArray($this->user->getEmail(), $this->user->getFirstName() . ' ' . $this->user->getLastName()),
                    StringUtility::makeEmailArray(
                        $this->settings['adminEmail'],
                        $this->settings['adminName']
                    ),
                    'Your account has been disabled',
                    $variables,
                    $this->config['disable.']['email.']
                );

                // second disable the account
                $this->user->setDisable(true);
                $this->user->setInactivemessageTstamp(time());
                $this->userRepository->update($this->user);
                $this->persistenceManager->persistAll();

                $uriBuilder = $this->uriBuilder;
                $uri = $uriBuilder
                    ->setTargetPageUid($this->settings['pids']['rootPage'])
                    ->build();
                try {
                    $this->redirectToUri($uri, null, 404);
                } catch (StopActionException $e) {
                }
            }
        }
    }
}
