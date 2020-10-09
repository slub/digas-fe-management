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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ViewHelper to explode a string by its linebreaks as delimiter
 *
 * # Example: Basic example
 * <code>
 * <digas:kitodoAccess id="{gp-id}" />
 * </code>
 *
 * <codeinline>
 * {digas:kitodoAccess(id:'{gp-id}')}
 * </codeinline>
 *
 * <output>
 * Will return true / false
 * </output>
 *
 * @package TYPO3
 */

class KitodoAccessViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('id', 'int', 'Kitodo presentation id', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return bool access granted / denied
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get('TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface');
        $typoscriptConfiguration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        //check general access
        if (!empty($settings = $typoscriptConfiguration['plugin.']['tx_digasfemanagement.']['settings.'])) {
            $kitodoGroupsAccess = array_intersect(explode(',', $GLOBALS['TSFE']->fe_user->user['usergroup']), explode(',', $typoscriptConfiguration['plugin.']['tx_digasfemanagement.']['settings.']['kitodoAccessGroups']));
        }

        if(!empty($kitodoGroupsAccess)) {
            return true;
        }

        if(GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user','isLoggedIn')) {
            $accessIds = explode("\r\n", $GLOBALS['TSFE']->fe_user->user['kitodo_feuser_access']);
            return in_array($arguments['id'], $accessIds);
        }
        return false;
    }
}
