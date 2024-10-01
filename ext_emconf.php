<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'SLUB DiGA.Sax FE Management',
    'description' => 'TYPO3 extension for the DiGA.Sax frontend user management.',
    'category' => 'templates',
    'author' => 'Stephan Gonder, Felix Franz, Alexander Bigga, Beatrycze Volk',
    'author_email' => 'typo3@slub-dresden.de',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
            'femanager' => '7.2.3',
            'fe_change_pwd' => '3.1.0-3.99.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ]
];
