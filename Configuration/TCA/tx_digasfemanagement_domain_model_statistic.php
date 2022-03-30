<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_statistic',
        'label' => 'uid',
        'hideTable' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'iconfile' => 'EXT:dlf/Resources/Public/Icons/txdlfdocuments.png',
        'rootLevel' => 0,
        'searchFields' => 'uid,document,fe_user',
    ],
    'interface' => [
        'maxDBListItems' => 25,
        'maxSingleDBListItems' => 50,
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'downloads' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'input',
                'default' => 0,
                'readOnly' => true,
            ],
        ],
        'document' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_statistic.document',
            'config' => [
                'type' => 'select',
                'readOnly' => true,
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_dlf_documents',
                'items' => [
                    ['', '0']
                ]
            ]
        ],
        'fe_user' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_statistic.fe_user',
            'config' => [
                'type' => 'select',
                'readOnly' => true,
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'items' => [
                    ['', '0']
                ]
            ]
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'uid,document,fe_user,tstamp,
         --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden'],
    ],
];
