<?php

//add gender "diverse" to selection
$GLOBALS['TCA']['fe_users']['columns']['gender']['config']['items'] = [
    [
        'LLL:EXT:femanager/Resources/Private/Language/locallang_db.xlf:' .
        'tx_femanager_domain_model_user.gender.item0',
        '0'
    ],
    [
        'LLL:EXT:femanager/Resources/Private/Language/locallang_db.xlf:' .
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
            'type' => 'select',
            'renderType' => 'selectSingle',
            'eval' => 'int',
            'default' => 0,
            'items' => [
                [
                    '',
                    '0'
                ],
                [
                    'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:' .
                    'tx_femanager_domain_model_user.company_type.item1',
                    '1'
                ],
                [
                    'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:' .
                    'tx_femanager_domain_model_user.company_type.item2',
                    '2'
                ],
                [
                    'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:' .
                    'tx_femanager_domain_model_user.company_type.item3',
                    '3'
                ],
                [
                    'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:' .
                    'tx_femanager_domain_model_user.company_type.item4',
                    '4'
                ]
            ]
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
    'kitodo_feuser_access' => [
        'exclude' => true,
        'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.kitodo_feuser_access',
        'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 15,
            'eval' => 'trim'
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
    ]
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
    'inactivemessage_tstamp',
    '',
    'after:lastlogin'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    'old_account',
    '',
    'after:tx_femanager_confirmedbyuser'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    '--div--;LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.tab.kitodo,kitodo_feuser_access',
    '',
    ''
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    '--div--;LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_femanager_domain_model_user.tab.saved_searches,saved_searches',
    '',
    ''
);
