<?php

use Slub\DigasFeManagement\Domain\Model\Log;

$administrationLogItems = [
    [
        'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:' .
        'tx_femanager_domain_model_log.state.10000',
        '--div--'
    ],
    [
        'LLL:EXT:digas_fe_management/Resources/Private/Language/locallang_db.xlf:' .
        'tx_femanager_domain_model_log.state.10001',
        Log::STATUS_ADMINISTRATION_PROFILE_DEACTIVATE
    ]
];

$GLOBALS['TCA']['tx_femanager_domain_model_log']['columns']['state']['config']['items'] = array_merge(
    $GLOBALS['TCA']['tx_femanager_domain_model_log']['columns']['state']['config']['items'],
    $administrationLogItems
);
