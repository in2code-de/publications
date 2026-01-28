<?php

declare(strict_types = 1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'extension-publications' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:publications/Resources/Public/Icons/Extension-300.svg'
    ],
    'extension-publications-wizard-icon' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:publications/Resources/Public/Icons/ModuleImport-inverted.svg'
    ]
];
