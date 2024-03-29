<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'SLUB DiGA.Sax FE Management',
    'description' => 'TYPO3 extension for the DiGA.Sax frontend user management.',
    'category' => 'templates',
    'author' => 'Stephan Gonder, Felix Franz, Alexander Bigga',
    'author_email' => 'typo3@slub-dresden.de',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '3.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'femanager' => '6.3.0-6.3.99',
            'fe_change_pwd' => '2.0.0-2.99.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ]
];
