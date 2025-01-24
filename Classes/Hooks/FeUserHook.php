<?php

namespace Slub\DigasFeManagement\Hooks;

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

use Slub\DigasFeManagement\Domain\Model\User;
use Slub\DigasFeManagement\Domain\Repository\UserRepository;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Class FeUserHook
 */
class FeUserHook {

    /**
     * @var User $user
     */
    protected $user = null;

    /**
     * @var UserRepository $userRepository
     */
    protected $userRepository = null;

    /**
     * @var PersistenceManager $persistenceManager
     */
    protected $persistenceManager = null;

    /**
     * initialize UserRepository
     *
     * @return void
     */
    public function initUserRepository()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->userRepository = $objectManager->get(UserRepository::class);
        $this->persistenceManager = $objectManager->get(PersistenceManager::class);
        $this->user = $this->userRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
    }

    /**
     * check if user has changed e-mail/username on login
     *
     * @param array $params
     * @return void
     */
    public function checkChangedUsername(&$params)
    {
        //
        // detect if user is "temporary" and therefore linked to another account
        // that has requested to changed username / email
        //
        if ($GLOBALS['TSFE']->fe_user->user['old_account']) {
            //initialize UserRepository
            $this->initUserRepository();

            //test if user exists
            /** @var User $userToChange */
            $userToChange = $this->userRepository->findByUid($this->user->getOldAccount());
            if ($userToChange !== null) {

                //set new values to existing user record
                $userToChange->setEmail($this->user->getEmail());
                $userToChange->setUsername($this->user->getUsername());

                //update existing user in database
                try {
                    $this->userRepository->update($userToChange);
                    $this->persistenceManager->persistAll();

                    //delete temporary user (currently logged in)
                    $this->userRepository->remove($this->user);
                    $this->persistenceManager->persistAll();

                    //login existing user with new username / email
                    $this->loginUser($userToChange);
                } catch (UnknownObjectException $e) {
                    throw new UnknownObjectException('User could not be updated. ' . $e->getMessage());
                } catch (IllegalObjectTypeException $e) {
                    throw new IllegalObjectTypeException('User could not be updated. ' . $e->getMessage());
                }
            }
        }

        return $params['content'];
    }

    /**
     * unset inactivemessage_tstamp on login
     *
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @return void
     */
    public function unsetInactiveMessageTstamp()
    {
        //check if inactivemessage_tstamp is set - and reset
        if ($GLOBALS['TSFE']->fe_user->user['inactivemessage_tstamp']) {

            //initialize UserRepository
            $this->initUserRepository();

            $this->user->setInactivemessageTstamp(NULL);

            //update
            $this->userRepository->update($this->user);

            //persist
            $this->persistenceManager->persistAll();
        }
    }

    /**
     * login user by id
     *
     * @param User $user
     * @return void
     */
    public function loginUser(User $user) {
        if ($user !== null) {
            $GLOBALS['TSFE']->fe_user->checkPid = 0;
            $GLOBALS['TSFE']->fe_user->createUserSession(['uid' => $user->getUid()]);
            $GLOBALS['TSFE']->fe_user->user = $GLOBALS['TSFE']->fe_user->fetchUserSession();
            $GLOBALS['TSFE']->fe_user->fetchGroupData();
            $GLOBALS['TSFE']->fe_user->setAndSaveSessionData('temp_fe_typo_user', TRUE);
        }
    }
}
