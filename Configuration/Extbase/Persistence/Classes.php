<?php
declare(strict_types=1);

return [
    \In2code\Femanager\Domain\Model\User::class => [
        'subclasses' => [
            \Slub\DigasFeManagement\Domain\Model\User::class
        ]
    ],
    \Slub\DigasFeManagement\Domain\Model\User::class => [
        'tableName' => \In2code\Femanager\Domain\Model\User::TABLE_NAME,
        'recordType' => 0
    ]
];
