<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access',
        'label' => 'dlf_document',
        'default_sortby' => 'ORDER BY crdate DESC',
        'hideTable' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => '',
        'iconfile' => 'EXT:digas_fe_management/Resources/Public/Icons/tx_digasfemanagement_domain_model_access.gif'
    ],
    'types' => [
        '1' => [
            'showitem' => '--palette--;;dlf_document,--palette--;;access_dlf_documents,rejected_reason,fe_user,--palette--;;access_notifications'
        ],
    ],
    'palettes' => [
        'dlf_document' => [
            'showitem' => 'dlf_document,record_id,',
        ],
        'access_dlf_documents' => [
            'showitem' => 'hidden,rejected,--linebreak--,starttime,endtime,--linebreak--,',
        ],
        'access_notifications' => [
            'showitem' => 'access_granted_notification,expire_notification, inform_user',
        ],
    ],
    'columns' => [
        'hidden' => [
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
                'default' => 1
            ],
        ],
        'starttime' => [
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'record_id' => [
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access.record_id',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'required,trim'
            ],
        ],
        'dlf_document' => [
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access.dlf_document',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_dlf_documents',
                'foreign_table_where' => 'AND tx_dlf_documents.restrictions="ja"',
                'items' => [
                    ['','']
                ],
                'minitems' => 1,
                'maxitems' => 1,
                'eval' => 'required,int'
            ],
        ],
        'fe_user' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'access_granted_notification' => [
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access.access_granted_notification',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'expire_notification' => [
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access.expire_notification',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'rejected' => [
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access.rejected',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle'
            ],
        ],
        'rejected_reason' => [
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access.rejected_reason',
            'config' => [
                'type' => 'text',
                'rows' => 2,
                'cols' => 50,
            ],
        ],

        'inform_user' => [
            'label' => 'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:tx_digasfemanagement_domain_model_access.inform_user',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle'
            ],
        ],

    ],
];
