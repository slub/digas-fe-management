<?php
/**
 * Femanager extend: disable user
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.DigasFeManagement',
    'Femanagerextended',
    'FE_Manager extended'
);

/**
 * Extend search: save, list, delete
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.DigasFeManagement',
    'Search',
    'Extend search'
);


/**
 * Flexform
 */
$pluginSignature = 'digasfemanagement_search';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['digasfemanagement_search'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:digas_fe_management/Configuration/FlexForms/FlexFormSearch.xml'
);

/**
 * Disable non needed fields in tt_content
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['digasfemanagement_administration'] = 'select_key';


/**
 * Femanager Administration
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.DigasFeManagement',
    'Administration',
    'FE_Manager Administration'
);

/**
 * Flexform
 */
$pluginSignature = 'digasfemanagement_administration';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['digasfemanagement_administration'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:digas_fe_management/Configuration/FlexForms/FlexFormAdministration.xml'
);

/**
 * Femanager Access Kitodo Documents
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.DigasFeManagement',
    'Access',
    'FE_Manager Access Kitodo Documents'
);

/**
 * Disable non needed fields in tt_content
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['digasfemanagement_administration'] = 'select_key';

/**
 * Femanager User Statistic
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.DigasFeManagement',
    'Statistic',
    'FE_Manager Statistic'
);

/**
 * Flexform
 */
$pluginSignature = 'digasfemanagement_statistic';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['digasfemanagement_statistic'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:digas_fe_management/Configuration/FlexForms/FlexFormStatistic.xml'
);


/**
 * Femanager Basket Kitodo Documents
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.DigasFeManagement',
    'Basket',
    'FE_Manager Basket'
);

/**
 * Flexform
 */
$pluginSignature = 'digasfemanagement_basket';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['digasfemanagement_basket'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:digas_fe_management/Configuration/FlexForms/FlexFormBasket.xml'
);

/**
 * Disable non needed fields in tt_content
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['digasfemanagement_statistic'] = 'select_key';
