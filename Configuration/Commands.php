<?php
declare(strict_types=1);

use In2code\Publications\Command\ClearCommand;
use In2code\Publications\Command\MigrationCommand;

return [
    'publications:clean' => [
        'class' => ClearCommand::class,
        'schedulable' => true
    ],
    'publications:migrate' => [
        'class' => MigrationCommand::class,
        'schedulable' => false
    ]
];
