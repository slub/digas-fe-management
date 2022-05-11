<?php

namespace Slub\DigasFeManagement\ViewHelpers;

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

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ViewHelper to check if user is logged in or has confirmed a dialog
 *
 * # Example: Basic example
 * <code>
 * <digas:kitodoSession cookieName="fe_kitodo_dialog">
 *  <f:then></f:then>
 *  <f:else></f:else>
 * </digas:kitodoSession>
 * </code>
 *
 * <codeinline>
 * no inline code available
 * </codeinline>
 *
 * <output>
 * Will return true / false
 * </output>
 *
 * @package TYPO3
 */
class KitodoSessionViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('cookieName', 'string', 'Kitodo cookie to check if private user has seen the dialog', false, 'fe_kitodo_dialog');
    }

    /**
     * This method decides if the condition is TRUE or FALSE. It can be overridden in extending viewhelpers to adjust functionality.
     *
     * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
     * @return bool
     * @throws AspectNotFoundException
     */
    protected static function evaluateCondition($arguments = null)
    {
        $user = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user', 'id', 0) > 0;

        //user is logged in
        if ($user) {
            return true;
        }

        //cookie is set
        return $GLOBALS["TSFE"]->fe_user->getKey("ses", $arguments['cookieName']);
    }
}
