<?php

return [
    'web_publications' => [
        'parent' => 'web',
        'position' => [],
        'access' => 'user',
        'icon' => 'EXT:publications/Resources/Public/Icons/ModuleImport.svg',
        'labels' => 'LLL:EXT:publications/Resources/Private/Language/locallang_mod_import.xlf',
        'path' => '/module/web/publications',
        'extensionName' => 'Publications',
        'controllerActions' => [
            \In2code\Publications\Controller\ImportController::class => [
                'overview',
                'import'
            ]
        ]
    ],
];
