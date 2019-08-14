<?php
declare(strict_types=1);

use In2code\Publications\Command\ClearCommand;

return [
    'publications:clean' => [
        'class' => ClearCommand::class,
        'schedulable' => true
    ]
];
