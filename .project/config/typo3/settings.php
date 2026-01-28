<?php
return [
    'BE' => [
        'debug' => false,
        'installToolPassword' => '$argon2i$v=19$m=65536,t=16,p=1$Y0Zkc2tFVUFzaWs3S1JUMA$VUYXEG49usrpbDP9cKfC0GrhRAtGICi5B7HGJ4LVYtg',
        'passwordHashing' => [
            'className' => 'TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\Argon2iPasswordHash',
            'options' => [],
        ],
    ],
    'DB' => [
        'Connections' => [
            'Default' => [
                'charset' => 'utf8mb4',
                'dbname' => 'db',
                'driver' => 'mysqli',
                'host' => 'db',
                'password' => 'db',
                'port' => 3306,
                'tableoptions' => [
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                ],
                'user' => 'db',
            ],
        ],
    ],
    'EXTENSIONS' => [
        'backend' => [
            'backendFavicon' => '',
            'backendLogo' => '',
            'loginBackgroundImage' => '',
            'loginFootnote' => '',
            'loginHighlightColor' => '',
            'loginLogo' => '',
            'loginLogoAlt' => '',
        ],
        'extensionmanager' => [
            'automaticInstallation' => '1',
            'offlineMode' => '0',
        ],
        'indexed_search' => [
            'catdoc' => '/usr/bin/',
            'deleteFromIndexAfterEditing' => '1',
            'disableFrontendIndexing' => '0',
            'flagBitMask' => '192',
            'fullTextDataLength' => '0',
            'ignoreExtensions' => '',
            'indexExternalURLs' => '0',
            'maxExternalFiles' => '5',
            'minAge' => '24',
            'pdf_mode' => '20',
            'pdftools' => '/usr/bin/',
            'ppthtml' => '/usr/bin/',
            'unrtf' => '/usr/bin/',
            'unzip' => '/usr/bin/',
            'useMysqlFulltext' => '0',
            'xlhtml' => '/usr/bin/',
        ],
        'scheduler' => [
            'maxLifetime' => '1440',
            'showSampleTasks' => '1',
        ],
        'styleguide' => [
            'boolean_1' => '0',
            'boolean_2' => '1',
            'boolean_3' => '',
            'boolean_4' => '0',
            'color_1' => 'black',
            'color_2' => '#000000',
            'color_3' => '000000',
            'color_4' => '',
            'compat_default_1' => 'value',
            'compat_default_2' => '',
            'compat_input_1' => 'value',
            'compat_input_2' => '',
            'int_1' => '1',
            'int_2' => '',
            'int_3' => '-100',
            'int_4' => '2',
            'intplus_1' => '1',
            'intplus_2' => '',
            'intplus_3' => '2',
            'nested' => [
                'input_1' => 'aDefault',
                'input_2' => '',
            ],
            'offset_1' => 'x,y',
            'offset_2' => 'x',
            'offset_3' => ',y',
            'offset_4' => '',
            'options_1' => 'default',
            'options_2' => 'option_2',
            'options_3' => '',
            'predefined' => [
                'boolean_1' => '1',
                'int_1' => '42',
            ],
            'small_1' => 'value',
            'small_2' => '',
            'string_1' => 'value',
            'string_2' => '',
            'user_1' => '0',
            'wrap_1' => 'value',
            'wrap_2' => '',
            'zeroorder_input_1' => 'value',
            'zeroorder_input_2' => '',
            'zeroorder_input_3' => '',
        ],
        'translate_locallang' => [
            'allowedExts' => '*',
            'autoTranslate' => '0',
            'clearCache' => '0',
            'defaultLangKey' => 'en',
            'extFilter' => '*',
            'langKeys' => 'de,fr,it',
            'modifyDefaultLang' => '0',
            'modifyKeys' => '0',
            'sortOnSave' => '0',
            'translatorInfo' => '',
        ],
    ],
    'FE' => [
        'debug' => false,
        'disableNoCacheParameter' => true,
        'passwordHashing' => [
            'className' => 'TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\Argon2iPasswordHash',
            'options' => [],
        ],
    ],
    'GFX' => [
        'processor' => 'ImageMagick',
        'processor_effects' => true,
        'processor_enabled' => true,
        'processor_path' => '/usr/bin/',
    ],
    'LOG' => [
        'TYPO3' => [
            'CMS' => [
                'deprecations' => [
                    'writerConfiguration' => [
                        'notice' => [
                            'TYPO3\CMS\Core\Log\Writer\FileWriter' => [
                                'disabled' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'MAIL' => [
        'transport' => 'sendmail',
        'transport_sendmail_command' => '/usr/local/bin/mailhog sendmail test@example.org --smtp-addr 127.0.0.1:1025',
        'transport_smtp_encrypt' => '',
        'transport_smtp_password' => '',
        'transport_smtp_server' => '',
        'transport_smtp_username' => '',
    ],
    'SYS' => [
        'caching' => [
            'cacheConfigurations' => [
                'hash' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                ],
                'imagesizes' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'options' => [
                        'compression' => true,
                    ],
                ],
                'pages' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'options' => [
                        'compression' => true,
                    ],
                ],
                'rootline' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'options' => [
                        'compression' => true,
                    ],
                ],
            ],
        ],
        'devIPmask' => '',
        'displayErrors' => 0,
        'encryptionKey' => 'd38c6c3eba5b63a952a6c91238205abd7fa52e89d4ae98cb66dcd908f0ce441570b26ad56c54417947f2bf09d2897e69',
        'exceptionalErrors' => 4096,
        'features' => [
            'unifiedPageTranslationHandling' => true,
            'yamlImportsFollowDeclarationOrder' => true,
        ],
        'sitename' => 'EXT:publications -- DDEV',
        'systemMaintainers' => [
            1,
        ],
    ],
];
