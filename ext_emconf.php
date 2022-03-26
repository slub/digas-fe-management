<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'SLUB DiGAS FE Management',
    'description' => 'TYPO3 extension for the DiGAS frontend user management.',
    'category' => 'templates',
    'author' => 'Stephan Gonder, Felix Franz, Alexander Bigga',
    'author_email' => 'typo3@slub-dresden.de',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.28-9.5.99',
            'femanager' => '5.4.0',
            'fe_change_pwd' => '2.0.0-2.99.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ]
];
