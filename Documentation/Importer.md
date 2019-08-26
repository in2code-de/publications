# Importer
per default are two importer available (BibTex, XML). 

## BibTex
the BibTex format must correspond to the BibTex format *http://www.bibtex.org/Format/*.    

## XML
the XML format is a simple format that mirrors all fields.

## Own Importer

you can simply add own importer. You need to register new importer at the TYPO3_CONF_VARS.

```php
       $GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer']['TXT'] =
            \YourExtension\Import\Importer\TxtImporter::class;
```

Importer must implement the Interface *\In2code\Publications\Import\Importer\ImporterInterface*. 
If you need to map certain fields you can extend the *\In2code\Publications\Import\Importer\AbstractImporter*. 
This class provides an mapping function for simple field name mapping. For further information you can checkout 
the *\In2code\Publications\Import\Importer\BibImporter* or *\In2code\Publications\Import\Importer\XmlImporter*.

The array which are returned from the function *convert* must correspond to a certain structure:

1. the array keys of an publication must correspond to the database fields of *tx_publications_domain_model_publication*
2. the array key *authors* contains an array of the publication authors
3. array keys which are matching the database fields will be ignored at the import 


example: 

```php
$returnArray = [
    0 => [
        'authors' => [
            0 => [
                'first_name' => 'Sebastian',
                'last_name' => 'Stein'
            ],
        ]
        'title' => 'This is my first publication',
        'bibtype' => 'article',
        'publisher' => 'In2code GmbH'
    ],
]
```
