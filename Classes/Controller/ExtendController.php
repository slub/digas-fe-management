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

/**
 * Class ExtendController
 */
class ExtendController extends AbstractController {

    /**
     * action disable
     *
     * @return void
     */
    public function disableAction() {

        if($this->user !== NULL && $this->user->getUid()) {
            $arguments = $this->request->getArguments();

            $this->view->assignMultiple([
                'step' => $arguments['step']
            ]);

            if($arguments['disable']) {
                $this->user->setDisable(true);
                $this->user->setInactivemessageTstamp(time());
                $this->userRepository->update($this->user);
                $this->persistenceManager->persistAll();

                $uriBuilder = $this->uriBuilder;
                $uri = $uriBuilder
                    ->setTargetPageUid($this->settings['pids']['rootPage'])
                    ->build();
                $this->redirectToUri($uri, 0, 404);
            }
        }
    }
}
