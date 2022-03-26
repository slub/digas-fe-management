<?php

// adjust "gender" field to select "salutation"
$GLOBALS['TCA']['fe_users']['columns']['gender']['config']['items'] = [
    [
        'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:' .
        'tx_femanager_domain_model_user.gender.item0',
        '0'
    ],
    [
        'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:' .
        'tx_femanager_domain_model_user.gender.item1',
        '1'
    ],
    [
        'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:' .
        'tx_femanager_domain_model_user.gender.item2',
        '2'
    ]
];

//override label of company
$GLOBALS['TCA']['fe_users']['columns']['company']['label'] = 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.company';

//add fe_user fields
$tmp_columns = [
    'company_type' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.company_type',
        'config' => [
            'type' => 'input',
            'size' => 10,
            'eval' => 'trim',
            'max' => 20
        ]
    ],
    'inactivemessage_tstamp' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.inactivemessage_tstamp',
        'config' => [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'size' => 30,
            'eval' => 'datetime',
            'readOnly' => true
        ]
    ],
    'old_account' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.old_account',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'fe_users',
            'eval' => 'int',
            'default' => 0,
            'items' => [
                ['','0']
            ]
        ]
    ],
    'saved_searches' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.saved_searches',
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_digasfemanagement_domain_model_search',
            'foreign_field' => 'fe_user',
            'foreign_default_sortby' => 'ORDER BY uid DESC',
            'maxitems' => 1000,
            'eval' => 'int',
            'default' => 0,
            'appearance' => [
                'collapseAll' => 1,
                'expandSingle' => 1,
                'useSortable' => false,

                'enabledControls' => [
                    'info' => true,
                    'new' => false,
                    'dragdrop' => false,
                    'sort' => false,
                    'hide' => true,
                    'delete' => true,
                ],
            ],
        ]
    ],
    'locale' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.locale',
        'config' => [
            'type' => 'input',
            'size' => 10,
            'eval' => 'trim',
            'max' => 20
        ]
    ],
    'pw_changed_on_confirmation' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.pw_changed_on_confirmation',
        'config' => [
            'type' => 'check',
            #'readOnly' => 1
        ]
    ],
    'temp_user_ordering_party' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.temp_user_ordering_party',
        'config' => [
            'type' => 'input',
            'size' => 50,
            'eval' => 'trim'
        ]
    ],
    'temp_user_area_location' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.temp_user_area_location',
        'config' => [
            'type' => 'text',
            'rows' => 3,
            'cols' => 50,
            'eval' => 'trim'
        ]
    ],
    'temp_user_purpose' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.temp_user_purpose',
        'config' => [
            'type' => 'text',
            'rows' => 3,
            'cols' => 50,
            'eval' => 'trim'
        ]
    ],
    'kitodo_document_access' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.kitodo_document_access',
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_digasfemanagement_domain_model_access',
            'foreign_field' => 'fe_user',
            'foreign_default_sortby' => 'ORDER BY crdate DESC',
            'maxitems' => 1000,
            'eval' => 'int',
            'default' => 0,
            'appearance' => [
                'collapseAll' => 1,
                'expandSingle' => 1,
                'useSortable' => false,

                'enabledControls' => [
                    'info' => true,
                    'new' => true,
                    'dragdrop' => false,
                    'sort' => false,
                    'hide' => true,
                    'delete' => true,
                ],
            ],
        ]
    ],
    'district' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.district',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'max' => 50
        ]
    ],
];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tmp_columns);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    'company_type',
    '',
    'after:company'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    'district',
    '',
    'after:city'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    'inactivemessage_tstamp',
    '',
    'after:lastlogin'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    'locale',
    '',
    'after:inactivemessage_tstamp'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    'old_account',
    '',
    'after:tx_femanager_confirmedbyuser'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    '--div--;LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.tab.kitodo,kitodo_document_access',
    '',
    ''
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    '--div--;LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.tab.saved_searches,saved_searches',
    '',
    ''
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'fe_users',
    'password_settings',
    'pw_changed_on_confirmation',
    'after:password_expiry_date'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    '--div--;LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.tab.temp_user,temp_user_ordering_party,temp_user_area_location,temp_user_purpose',
    '',
    'after:saved_searches'
);
