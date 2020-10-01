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

 use Slub\DigasFeManagement\Domain\Model\User;

/**
 * Class InvitationController
 */
class InvitationController extends \In2code\Femanager\Controller\InvitationController
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
     * action create
     *
     * @param User|\In2code\Femanager\Domain\Model\User $user
     * @validate $user In2code\Femanager\Domain\Validator\ServersideValidator
     * @validate $user In2code\Femanager\Domain\Validator\PasswordValidator
     * @validate $user In2code\Femanager\Domain\Validator\CaptchaValidator
     * @return void
     */
    public function createAction(\In2code\Femanager\Domain\Model\User $user)
    {
        parent::createAction($user);
    }

    /**
     * action update
     * Set setTxFemanagerConfirmedbyuser=true
     *
     * @param \In2code\Femanager\Domain\Model\User $user
     * @validate $user In2code\Femanager\Domain\Validator\ServersideValidator
     * @validate $user In2code\Femanager\Domain\Validator\PasswordValidator
     * @return void
     */
    public function updateAction($user)
    {
        $user->setTxFemanagerConfirmedbyuser(true);
        parent::updateAction($user);
    }
}
