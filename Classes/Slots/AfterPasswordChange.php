<?php

namespace Slub\DigasFeManagement\Slots;

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

use In2code\Femanager\Domain\Service\SendMailService;
use In2code\Femanager\Utility\HashUtility;
use In2code\Femanager\Utility\StringUtility;
use Slub\DigasFeManagement\Domain\Model\User;
use Slub\DigasFeManagement\Domain\Repository\UserRepository;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;

class AfterPasswordChange
{
    /**
     * @var UserRepository $userRepository
     */
    protected $userRepository = null;

    /**
     * typoscript settings from femanager
     * @var array $settings
     */
    protected $settings = [];

    /**
     * config from femanager
     * @var array $config
     */
    protected $config = [];

    /**
     * @var SendMailService $sendMailService
     */
    protected $sendMailService = null;


    /**
     * initialize userRepository, user, settings, femanagerConfiguration, sendMailService
     * @return void
     */
    public function init()
    {
        // initialize objectManager
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        // initialize UserRepository
        $this->userRepository = $objectManager->get(UserRepository::class);

        // initialize configurationManager
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);

        // get settings for femanager mail service
        $this->settings = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'femanager') ?? [];

        // get config for femanager mail service
        $this->config = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT)['plugin.']['tx_femanager.']['settings.'] ?? [];

        // invoke sendMailService
        $this->sendMailService = $objectManager->get(SendMailService::class);
    }

    /**
     *
     * @param $changePassword
     * @param $passwordPasswordController
     * @return void
     */
    public function createUserNotifyMail($changePassword, $passwordPasswordController)
    {

        //check if confirmation mail was already sent
        if ($GLOBALS['TSFE']->fe_user->user['pw_changed_on_confirmation'] === 0) {

            //initialize
            $this->init();

            /**
             * @var User $userToUpdate
             */
            $userToUpdate = $this->userRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);

            //send mail service from fe Manger
            $this->sendMail($userToUpdate);

            //confirm, password was changed
            $userToUpdate->setPwChangedOnConfirmation(true);

            try {
                //update user
                $this->userRepository->update($userToUpdate);

            } catch (UnknownObjectException $e) {
                new UnknownObjectException('User could not be updated. UnknownObjectException');
                return;
            } catch (IllegalObjectTypeException $e) {
                new Exception('User could not be updated. IllegalObjectTypeException');
                return;
            }
        }
    }

    /**
     * send mail service from femanager
     * as found in In2code\Femanager\Controller\AbstractController, line 222ff
     * @param User $user
     */
    public function sendMail(User $user=null)
    {
        if (!empty($this->settings) && !empty($this->config) && $user) {
            $variables = ['user' => $user, 'settings' => $this->settings, 'hash' => HashUtility::createHashForUser($user)];

            $this->sendMailService->send(
                'createUserNotifyAfterPwChange',
                StringUtility::makeEmailArray($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName()),
                StringUtility::makeEmailArray(
                    $this->settings['new']['email']['createUserNotifyAfterPwChange']['sender']['email']['value'],
                    $this->settings['new']['email']['createUserNotifyAfterPwChange']['sender']['name']['value']
                ),
                'Profile creation',
                $variables,
                $this->config['new.']['email.']['createUserNotifyAfterPwChange.']
            );
        }
    }
}
