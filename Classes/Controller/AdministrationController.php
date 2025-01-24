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
use In2code\Femanager\Domain\Model\UserGroup;
use In2code\Femanager\Utility\LocalizationUtility;
use In2code\Femanager\Utility\LogUtility;
use In2code\Femanager\Utility\ObjectUtility;
use Slub\DigasFeManagement\Domain\Model\Log;
use Slub\DigasFeManagement\Domain\Model\User;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility as ExtbaseLocalizationUtility;

/**
 * Class AdministrationController
 */
class AdministrationController extends AbstractController
{
    public function initializeAction()
    {
        parent::initializeAction();

        // remove "disable" column to show hidden records
        unset($GLOBALS['TCA']['fe_users']['ctrl']['enablecolumns']['disabled']);
    }

    /**
     * List existing fe_users
     *
     * @param array $filter
     */
    public function listAction(array $filter = [])
    {
        $arguments = $this->request->getArguments();

        $this->view->assignMultiple(
            [
                'users' => $this->userRepository->findByUsergroups(
                    $this->settings['list']['usergroup'],
                    $this->settings,
                    $filter
                ),
                'filter' => isset($arguments['accessPending']) ? [] : $filter,
                'accessPending' => isset($arguments['accessPending'])
            ]
        );
        $this->assignForAll();

    }

    /**
     * Show detailed information about a fe_user
     *
     * @param User $user
     * @return void
     */
    public function showAction(User $user)
    {
        if (!empty($user->getUid())) {
            $this->view->assign('user', $this->userRepository->findByUid($user->getUid()));
            $this->assignForAll();
        }
    }

    /**
     * Edit specific fe_user
     *
     * @param User $user
     */
    public function editUserAction(User $user)
    {
        if (!empty($user->getUid())) {
            $token = GeneralUtility::hmac((string)$user->getUid(), (string)$user->getCrdate()->getTimestamp());

            /** @var UserGroup[] $feUserGroup */
            $feUserGroup = $user->getUsergroup()->getArray();
            if (!empty($feUserGroup[0])) {
                $feUserGroup = $feUserGroup[0]->getUid();
            }

            $this->view->assignMultiple([
                'user' => $user,
                'token' => $token,
                'currentFeUserGroup' => $feUserGroup ?: null,
                'allUserGroups' => $this->userGroupRepository->findAllForFrontendSelection('')
            ]);
            $this->assignForAll();
        }
    }

    /**
     * Update specific fe_user
     *
     * @param User $user
     * @TYPO3\CMS\Extbase\Annotation\Validate("Slub\DigasFeManagement\Domain\Validator\ServersideValidator", param="user")
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function updateUserAction(User $user)
    {
        // update inactivemessageTstamp for scheduler tasks (RemindUnusedAccountsCommand)
        if ($user->getDisable() === true) {
            $user->setInactivemessageTstamp(new \DateTime());
        } else {
            $user->setInactivemessageTstamp(null);
        }
        $this->updateAllConfirmed($user);
        $this->redirect('editUser', null, null, ['user' => $user]);
    }

    /**
     * Deactivate fe_user
     *
     * @param User $user
     * @param boolean $setActiveState
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function deactivateUserAction(User $user, $setActiveState = false)
    {
        if (!empty($user->getUid())) {
            $this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, [$user, $this]);
            $logUtility = GeneralUtility::makeInstance(LogUtility::class);
            $logUtility->log(Log::STATUS_ADMINISTRATION_PROFILE_DEACTIVATE, $user);
            if ($setActiveState) {
                $flashMessageText = ExtbaseLocalizationUtility::translate(
                    'tx_femanager_domain_model_log.state.' . Log::STATUS_ADMINISTRATION_PROFILE_ACTIVATE,
                    'digas_fe_management',
                    [$user->getEmail()]
                );
            } else {
                $flashMessageText = ExtbaseLocalizationUtility::translate(
                    'tx_femanager_domain_model_log.state.' . Log::STATUS_ADMINISTRATION_PROFILE_DEACTIVATE,
                    'digas_fe_management',
                    [$user->getEmail()]
                );
            }
            $this->addFlashMessage($flashMessageText);

            try {
                if ($setActiveState) {
                    $user->setDisable(false);
                    $user->setInactivemessageTstamp(new \DateTime());
                } else {
                    $user->setDisable(true);
                    $user->setInactivemessageTstamp(new \DateTime());
                }
                $this->userRepository->update($user);
                $this->persistenceManager->persistAll();

                $this->redirectByAction('deactivateUser');
                $this->redirect('list');
            } catch (Exception $e) {
            }
        }
    }

    /**
     * Check: If there are no changes, simple redirect back
     * @param User $user
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @see \In2code\Femanager\Controller\EditController::redirectIfDirtyObject()
     *
     */
    protected function redirectIfDirtyObject(User $user)
    {
        if (!ObjectUtility::isDirtyObject($user)) {
            $this->addFlashMessage(LocalizationUtility::translate('noChanges', 'DigasFeManagement'), '', FlashMessage::NOTICE);
            $this->redirect('editUser');
        }
    }
}
