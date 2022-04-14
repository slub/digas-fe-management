<?php

return [
    'digas-fe-management:deleteInactiveAccounts' => [
        'class' => \Slub\DigasFeManagement\Command\DeleteInactiveAccountsCommand::class,
    ],
    'digas-fe-management:remindUnusedAccounts' => [
        'class' => \Slub\DigasFeManagement\Command\RemindUnusedAccountsCommand::class,
    ],
    'digas-fe-management:deleteDeactivatedAccountsCommand' => [
        'class' => \Slub\DigasFeManagement\Command\DeleteDeactivatedAccountsCommand::class,
    ],
    'digas-fe-management:kitodoAccessGrantedNotification' => [
        'class' => \Slub\DigasFeManagement\Command\KitodoAccessGrantedNotification::class,
    ],
    'digas-fe-management:kitodoAccessExpirationNotification' => [
        'class' => \Slub\DigasFeManagement\Command\KitodoAccessExpirationNotification::class,
    ],
    'digas-fe-management:DeleteTemporaryUsersCommand' => [
        'class' => \Slub\DigasFeManagement\Command\DeleteTemporaryUsersCommand::class,
    ]
];

