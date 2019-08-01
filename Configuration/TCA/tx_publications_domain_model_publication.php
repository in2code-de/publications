<?php
use In2code\Publications\Domain\Model\Publication;

$tca = [
    'ctrl' => [
        'title' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME,
        'label' => 'status',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY crdate DESC',
        'delete' => 'deleted',
        'iconfile' => 'EXT:publications/Resources/Public/Icons/' . Publication::TABLE_NAME . '.svg',
        'rootLevel' => -1
    ],
    'interface' => [
        'showRecordFieldList' => 'will be filled below...',
    ],
    'types' => [
        '1' => ['showitem' =>
            '--palette--;;palette_title,' .
            '--palette--;;palette_type,' .
            '--palette--;;palette_status,' .
            '--palette--;;palette_reviewed,' .
            '--div--;Identification,' .
            '--palette--;;palette_identification,' .
            '--div--;Organization,' .
            '--palette--;;palette_organization,' .
            '--div--;Publishing,' .
            '--palette--;;palette_publishing,' .
            '--palette--;;palette_event,' .
            '--palette--;;palette_number,' .
            '--div--;Relations,' .
            '--palette--;;palette_relations,' .
            '--div--;Misc,' .
            '--palette--;;palette_note,' .
            '--palette--;;palette_library,' .
            ''
        ],
    ],
    'palettes' => [
        'palette_title' => [
            'showitem' => 'title,abstract'
        ],
        'palette_type' => [
            'showitem' => 'bibtype,type'
        ],
        'palette_status' => [
            'showitem' => 'status,date'
        ],
        'palette_reviewed' => [
            'showitem' => 'reviewed,language'
        ],
        'palette_identification' => [
            'showitem' => 'citeid,isbn,--linebreak--,issn,doi'
        ],
        'palette_organization' => [
            'showitem' => 'organization,school,--linebreak--,institution,institute'
        ],
        'palette_publishing' => [
            'showitem' =>
                'booktitle,journal,--linebreak--,edition,volume,--linebreak--,publisher,address,' .
                '--linebreak--,chapter,series,--linebreak--,edition,howpublished,--linebreak--,editor,pages' .
                '--linebreak--,affiliation,extern'
        ],
        'palette_event' => [
            'showitem' => 'event_name,event_place'
        ],
        'palette_number' => [
            'showitem' => 'number,number2'
        ],
        'palette_relations' => [
            'showitem' => 'keywords,tags,--linebreak--,web_url,web_url2,--linebreak--,web_url_date,file_url'
        ],
        'palette_note' => [
            'showitem' => 'note,annotation,--linebreak--,miscellaneous,miscellaneous2'
        ],
        'palette_library' => [
            'showitem' => 'in_library,borrowed_by'
        ]
    ],
    'columns' => [
        'bibtype' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.bibtype',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['Unknown', ''],
                    ['Article', 'article'],
                    ['Book', 'book'],
                    ['Booklet', 'booklet'],
                    ['Conference', 'conference'],
                    ['Contribution to Book', 'inbook'],
                    ['Contribution to Collection', 'incollection'],
                    ['Contribution to Proceeding', 'inproceedings'],
                    ['Manual', 'manual'],
                    ['Manuscript', 'manuscript'],
                    ['Master-Thesis', 'mastersthesis'],
                    ['Miscellaneous', 'misc'],
                    ['PhD-Thesis', 'phdthesis'],
                    ['Poster', 'poster'],
                    ['Proceedings', 'proceedings'],
                    ['Report', 'report'],
                    ['Techreport', 'techreport'],
                    ['Thesis', 'thesis'],
                    ['Unpublished', 'unpublished'],
                    ['URL', 'url'],
                ],
                'size' => 1,
                'maxitems' => 1,
                'default' => ''
            ]
        ],
        'type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.type',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'citeid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.citeid',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'status' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['Published', 0],
                    ['Accepted', 1],
                    ['Submitted', 2],
                    ['To be published', 3],
                    ['In preperation', 4]
                ],
                'size' => 1,
                'maxitems' => 1,
                'default' => 0
            ]
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.title',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'abstract' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.abstract',
            'config' => [
                'type' => 'text',
                'cols' => 32,
                'rows' => 5,
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'journal' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.journal',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.date',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'datetime',
                'default' => 0
            ]
        ],
        'volume' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.volume',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'number' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.number',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'number2' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.number2',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'pages' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.pages',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'affiliation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.affiliation',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'note' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.note',
            'config' => [
                'type' => 'text',
                'cols' => 32,
                'rows' => 5,
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'annotation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.annotation',
            'config' => [
                'type' => 'text',
                'cols' => 32,
                'rows' => 5,
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'keywords' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.keywords',
            'config' => [
                'type' => 'text',
                'cols' => 32,
                'rows' => 5,
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'tags' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.tags',
            'config' => [
                'type' => 'text',
                'cols' => 32,
                'rows' => 5,
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'file_url' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.file_url',
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
        'web_url' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.web_url',
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
        'web_url2' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.web_url2',
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
        'web_url_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.web_url_date',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'miscellaneous' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.miscellaneous',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'miscellaneous2' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.miscellaneous2',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'editor' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.editor',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'publisher' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.publisher',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'series' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.series',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'address' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.address',
            'config' => [
                'type' => 'text',
                'cols' => 32,
                'rows' => 5,
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'edition' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.edition',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'chapter' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.chapter',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'extern' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.extern',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'reviewed' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.reviewed',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'in_library' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.in_library',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'borrowed_by' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.borrowed_by',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'howpublished' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.howpublished',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'event_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.event_name',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'event_place' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.event_place',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'language' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.language',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'booktitle' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.booktitle',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'organization' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.organization',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'school' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.school',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'institution' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.institution',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'institute' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.institute',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'isbn' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.isbn',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'issn' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.issn',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'doi' => [
            'exclude' => true,
            'label' => 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . Publication::TABLE_NAME
                . '.doi',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
    ]
];

$tca['interface']['showRecordFieldList'] = implode(',', array_keys($tca['columns']));
return $tca;
