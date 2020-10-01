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
    ]
];

