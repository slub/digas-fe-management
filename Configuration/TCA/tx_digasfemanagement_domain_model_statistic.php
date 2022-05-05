<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_statistic',
        'label' => 'uid',
        'hideTable' => false,
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
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'download_pages' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_statistic.download_pages',
            'config' => [
                'type' => 'input',
                'default' => 0,
                'readOnly' => true,
            ],
        ],
        'download_work' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_statistic.download_work',
            'config' => [
                'type' => 'input',
                'default' => 0,
                'readOnly' => true,
            ],
        ],
        'work_views' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_statistic.work_views',
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
        '1' => ['showitem' => 'uid,document,fe_user,download_pages,download_work,work_views,
         --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden'],
    ],
];
