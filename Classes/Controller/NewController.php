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

use In2code\Femanager\Utility\LocalizationUtility;
use Slub\DigasFeManagement\Domain\Model\User;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;

/**
 * Class NewController
 */
class NewController extends \In2code\Femanager\Controller\NewController
{
    /**
     * @return void
     */
    public function initializeCreateAction()
    {
        if ($this->arguments->hasArgument('user')) {
            // Workaround to avoid php7 warnings of wrong type hint.
            /** @var \Slub\DigasFeManagement\Xclass\Extbase\Mvc\Controller\Argument $user */
            $user = $this->arguments['user'];
            $user->setDataType(User::class);
        }
    }

    /**
     * Render registration form
     *
     * @param User|\In2code\Femanager\Domain\Model\User $user
     * @return void
     */
    public function newAction(\In2code\Femanager\Domain\Model\User $user = null)
    {
        //get kitodo ID if set
        $kitodoParams = GeneralUtility::_GET('tx_dlf');
        $this->view->assign('kitodoParams', $kitodoParams);

        $this->view->assign('currentUser', $this->user);
        parent::newAction($user);
    }

    /**
     * action create
     *
     * @param User|\In2code\Femanager\Domain\Model\User $user
     * @TYPO3\CMS\Extbase\Annotation\Validate("In2code\Femanager\Domain\Validator\ServersideValidator", param="user")
     * @TYPO3\CMS\Extbase\Annotation\Validate("In2code\Femanager\Domain\Validator\PasswordValidator", param="user")
     * @TYPO3\CMS\Extbase\Annotation\Validate("In2code\Femanager\Domain\Validator\CaptchaValidator", param="user")
     * @return void
     */
    public function createAction(\In2code\Femanager\Domain\Model\User $user)
    {

        //change e-mail
        if ($this->settings['new']['changeEmail']) {

            //get new e-mail-address
            $newEmailAddress = $user->getEmail();

            //clone user record
            $properties = $this->user->_getProperties();
            unset($properties['uid']);
            foreach ($properties as $key => $value) {
                $user->_setProperty($key, $value);
            }

            //set properties
            $user->setEmail($newEmailAddress);
            $user->setUsername($newEmailAddress);
            $user->setUsergroup($this->user->getUsergroup());
            $user->setOldAccount($this->user->getUid());
            $user->setTxFemanagerConfirmedbyuser(false);
        }

        parent::createAction($user);
    }

    /**
     * Send email to user for confirmation
     *
     * @param User|\In2code\Femanager\Domain\Model\User $user
     * @return void
     * @throws UnsupportedRequestTypeException
     */
    protected function createUserConfirmationRequest(\In2code\Femanager\Domain\Model\User $user)
    {
        $this->sendCreateUserConfirmationMail($user);
        $this->addFlashMessage(LocalizationUtility::translate($this->settings['new']['changeEmail'] ? 'emailChangedRequestWaitingForUserConfirm' : 'createRequestWaitingForUserConfirm', 'DigasFeManagement'));
        $this->redirectByAction('new', 'requestRedirect');
    }
}
