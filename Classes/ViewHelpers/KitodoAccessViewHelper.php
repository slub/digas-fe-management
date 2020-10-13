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

use TYPO3\CMS\Core\Database\ConnectionPool;
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
        // return false if no id is given
        if (!isset($arguments['id'])) {
            return false;
        }

        // check group access - return TRUE if access by group is granted
        if (!empty($settings = KitodoAccessViewHelper::getSettings())) {
            $kitodoGroupsAccess = array_intersect(
                explode(',', $GLOBALS['TSFE']->fe_user->user['usergroup']),
                explode(',', $settings['kitodoAccessGroups'])
            );
        }
        if (!empty($kitodoGroupsAccess)) {
            return true;
        }

        // fetch document by uid
        $kitodoDocument = KitodoAccessViewHelper::getDlfDocument(intval($arguments['id']));

        // check if document could be fetched
        if ($kitodoDocument === false) {
            return false;
        }

        // check if document has public access
        // restrictions === 'nein' means, there are no restrictions.
        if ($kitodoDocument['restrictions'] === 'nein' ) {
            return true;
        }

        // check decided access for current fe-user
        if (GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user','isLoggedIn')) {
            $accessIds = explode("\r\n", $GLOBALS['TSFE']->fe_user->user['kitodo_feuser_access']);
            return in_array($kitodoDocument['record_id'], $accessIds);
        }
        return false;
    }

    /**
     * get extension settings
     *
     * @return void
     */
    protected static function getSettings() {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get('TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface');
        $typoScriptConfiguration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        return $typoScriptConfiguration['plugin.']['tx_digasfemanagement.']['settings.'];
    }

    /**
     * fetch dlf document by uid
     *
     * @param int $uid
     * @return void
     */
    protected static function getDlfDocument($uid) {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_dlf_documents');

        return $queryBuilder
            ->select('record_id', 'restrictions')
            ->from('tx_dlf_documents')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
            )
            ->execute()->fetch();
    }
}
