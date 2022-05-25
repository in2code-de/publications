<?php

declare(strict_types = 1);

namespace In2code\Publications\Import\Importer;

/**
 * Class XmlImporter
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class XmlImporter extends AbstractImporter
{
    /**
     * map publication fields from -> to
     *
     * @var array
     */
    protected array $additionalPublicationMapping = [
        'ISBN' => 'isbn',
        'ISSN' => 'issn',
        'DOI' => 'doi',
        'misc' => 'miscellaneous',
        'misc2' => 'miscellaneous2'
    ];

    /**
     * map author fields from -> to
     *
     * @var array
     */
    protected array $personMapping = [
        'fn' => 'first_name',
        'sn' => 'last_name'
    ];

    /**
     * @param string $filePath
     * @return array
     */
    public function convert(string $filePath): array
    {
        $data = $this->xml2array(file_get_contents($filePath));
        $publications = $data['reference'];

        return $this->fieldMapping($publications);
    }

    /**
     * @param array $publication
     * @return void
     */
    protected function specialMapping(array &$publication)
    {
        $this->personMapping($publication, 'authors');
    }

    /**
     * @param array $publication
     * @param string $personKey
     */
    protected function personMapping(array &$publication, string $personKey)
    {
        if (!empty($publication[$personKey])) {
            foreach ($this->personMapping as $from => $to) {
                if (array_key_exists('fn', $publication[$personKey]['person'])) {
                    // single author
                    if (array_key_exists($from, $publication[$personKey]['person'])) {
                        $publication[$personKey][0][$to] = $publication[$personKey]['person'][$from];
                    }
                } else {
                    // multiple authors
                    foreach ($publication[$personKey]['person'] as $key => $person) {
                        if (array_key_exists($from, $person)) {
                            $publication[$personKey][$key][$to] = $person[$from];
                        }
                    }
                }
            }
            unset($publication[$personKey]['person']);
        }
    }

    /**
     * @param string $xmlString
     * @return mixed
     */
    protected function xml2array(string $xmlString)
    {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        return json_decode($json, true);
    }
}
