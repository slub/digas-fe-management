<?php

namespace Slub\DigasFeManagement\Domain\Validator;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ServersideValidator
 */
class ServersideValidator extends \In2code\Femanager\Domain\Validator\ServersideValidator
{
    /**
     * @return void
     */
    protected function setPluginVariables()
    {
        $this->pluginVariables = GeneralUtility::_GP('tx_digasfemanagement_administration');
    }

    /**
     * Get controller name in lowercase
     * Add custom controller "Administration" for FeUser administration
     *
     * @return string
     */
    protected function getControllerName(): string
    {
        $controllerName = 'new';
        if ($this->pluginVariables['__referrer']['@controller'] === 'Edit') {
            $controllerName = 'edit';
        } elseif ($this->pluginVariables['__referrer']['@controller'] === 'Invitation') {
            $controllerName = 'invitation';
        } elseif ($this->pluginVariables['__referrer']['@controller'] === 'Administration') {
            $controllerName = 'administration';
        }
        return $controllerName;
    }
}
