<?php
use In2code\Publications\Domain\Model\Author;

$llPrefix = 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:';
$llTable = $llPrefix . Author::TABLE_NAME;

$tca = [
    'ctrl' => [
        'title' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Author::TABLE_NAME,
        'label' => 'first_name',
        'label_alt' => 'last_name',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY last_name ASC',
        'delete' => 'deleted',
        'iconfile' => 'EXT:publications/Resources/Public/Icons/' . Author::TABLE_NAME . '.svg',
        'searchFields' => 'last_name,first_name,url,orcid'
    ],
    'interface' => [
        'showRecordFieldList' => 'will be filled below...',
    ],
    'types' => [
        '1' => [
            'showitem' =>
                '--palette--;;palette_author,url,orcid,--div--;' . $llTable . '.tab.system,--palette--;;palette_system,'
        ],
    ],
    'palettes' => [
        'palette_author' => [
            'showitem' => 'first_name,last_name'
        ],
        'palette_system' => [
            'showitem' => 'hidden,sys_language_uid,l10n_parent'
        ]
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'special' => 'languages',
                'renderType' => 'selectSingle',

                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'default' => 0,
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ]
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => Author::TABLE_NAME,
                'foreign_table_where' => 'AND ' . Author::TABLE_NAME . '.pid=###CURRENT_PID### AND ' .
                    Author::TABLE_NAME . '.sys_language_uid IN (-1,0)',
                'default' => 0
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ]
        ],

        'last_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Author::TABLE_NAME
                . '.last_name',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'first_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Author::TABLE_NAME
                . '.first_name',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'orcid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Author::TABLE_NAME
                . '.orcid',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'url' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Author::TABLE_NAME
                . '.url',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => '',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => 'URL',
                        'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_link.gif',
                        'module' => [
                            'name' => 'wizard_link',
                        ],
                        'JSopenParams' => 'height=800,width=600,status=0,menubar=0,scrollbars=1'
                    ]
                ],
                'softref' => 'typolink'
            ]
        ],
    ]
];

$tca['interface']['showRecordFieldList'] = implode(',', array_keys($tca['columns']));
return $tca;
