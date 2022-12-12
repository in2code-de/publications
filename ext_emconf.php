<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'publications',
    'description' => 'Publication reference extension with im- and export with bibtex and xml files',
    'category' => 'plugin',
    'version' => '5.2.0',
    'author' => 'Alex Kellner',
    'author_email' => 'alexander.kellner@in2code.de',
    'author_company' => 'in2code.de',
    'state' => 'stable',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ]
];
