<?php

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

defined('TYPO3_MODE') || die('Access denied.');

//xclass for PHP error thrown because of extended user model
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Extbase\Mvc\Controller\Argument::class] = [
    'className' => Slub\DigasFeManagement\Xclass\Extbase\Mvc\Controller\Argument::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][In2code\Femanager\Controller\NewController::class] = [
    'className' => Slub\DigasFeManagement\Controller\NewController::class,
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][In2code\Femanager\Controller\EditController::class] = [
    'className' => Slub\DigasFeManagement\Controller\EditController::class,
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][In2code\Femanager\Controller\InvitationController::class] = [
    'className' => Slub\DigasFeManagement\Controller\InvitationController::class,
];


//override femanager extension settings
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['femanager']['setCookieOnLogin'] = '1';

//load additional pageTSconfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:digas_fe_management/Configuration/PageTSconfig/setup.typoscript">'
);

//german language file for fe_change_pwd
$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:fe_change_pwd/Resources/Private/Language/locallang.xlf'][] = 'EXT:digas_fe_management/Resources/Private/Language/Overrides/de.locallang_fe_change_pwd.xlf';

//addtional german language file for fe_manager
$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:femanager/Resources/Private/Language/locallang.xlf'][] = 'EXT:digas_fe_management/Resources/Private/Language/de.locallang.xlf';

//plugin FE_Manager extended
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.DigasFeManagement',
    'Femanagerextended',
    [
        'Extend' => 'disable,dialog'
    ],
    [
        'Extend' => 'disable,dialog'
    ]
);

//plugin DigasFeManagement Administration
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.DigasFeManagement',
    'Administration',
    [
        'Administration' => 'list,show,editUser,deactivateUser,updateUser'
    ],
    [
        'Administration' => 'list,show,editUser,deactivateUser,updateUser'
    ]
);

//plugin DigasFeManagement Administration - Access Kitodo Documents
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.DigasFeManagement',
    'Access',
    [
        'Access' => 'list,approve,rejectReason,reject,new,create,informUser'
    ],
    [
        'Access' => 'list,approve,rejectReason,reject,new,create,informUser'
    ]
);

//plugin DigasFeManagement Statistic
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.DigasFeManagement',
    'Statistic',
    [
        'Statistic' => 'downloadLink,view,viewSingle,administration'
    ],
    [
        'Statistic' => 'downloadLink,view,viewSingle,administration'
    ]
);

//plugin save search
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.DigasFeManagement',
    'Search',
    [
        'Search' => 'list,save,delete,create'
    ],
    [
        'Search' => 'list,save,delete,create'
    ]
);

//plugin request kitodo documents from basket
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.DigasFeManagement',
    'Basket',
    [
        'Basket' => 'index,request,overview'
    ],
    [
        'Basket' => 'index,request,overview'
    ]
);

// fe_login hooks
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['login_confirmed'] = [
    'Slub\DigasFeManagement\Hooks\FeUserHook->unsetInactiveMessageTstamp'
];

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['postProcContent'] = [
    'Slub\DigasFeManagement\Hooks\FeUserHook->checkChangedUsername'
];

// fe_change_pwd signal
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
$signalSlotDispatcher->connect(
    \Derhansen\FeChangePwd\Controller\PasswordController::class,
    'updateActionAfterUpdatePassword',
    \Slub\DigasFeManagement\Slots\AfterPasswordChange::class,
    'createUserNotifyMail'
);
