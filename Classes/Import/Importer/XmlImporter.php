<?php
declare(strict_types=1);
namespace In2code\Publications\Import\Importer;

use DateTime;

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
    protected $additionalPublicationMapping = [
        'type' => 'bibtype',
        'creators' => 'authors',
        'date_type' => 'status',
        'publication' => 'journal',
        'official_url' => 'web_url2',
        'event_title' => 'event_name',
        'event_location' => 'event_place',
    ];

    /**
     * map author fields from -> to
     *
     * @var array
     */
    protected $personMapping = [
        'given' => 'first_name',
        'family' => 'last_name'
    ];

    /**
     * @param string $filePath
     * @return array
     */
    public function convert(string $filePath): array
    {
        $data = $this->xml2array(file_get_contents($filePath));
        $publications = $data['eprint'];

        return $this->fieldMapping($publications);
    }

    /**
     * @param array $publication
     * @return void
     */
    protected function specialMapping(array &$publication)
    {
        $this->personMapping($publication, 'authors');
        $this->personMapping($publication, 'editors');
    }

    /**
     * @param array $publication
     * @param string $personKey
     */
    protected function personMapping(array &$publication, string $personKey)
    {
        if (!empty($publication[$personKey])) {
            $persons = $publication[$personKey]['item'];
            unset($publication[$personKey]['item']);
            foreach ($this->personMapping as $from => $to) {
                // only one person
                if (array_key_exists('name', $persons)) {
                    $personInformation = $persons['name'];
                    if (array_key_exists($from, $personInformation)) {
                        $publication[$personKey][0][$to] = $personInformation[$from];
                    }
                } else {
                    // multiple persons
                    foreach ($persons as $key => $person) {
                        $personInformation = $person['name'];
                        if (array_key_exists($from, $personInformation)) {
                            $publication[$personKey][$key][$to] = $personInformation[$from];
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $xmlString
     * @return mixed
     */
    protected function xml2array(string $xmlString)
    {
        $xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        return json_decode($json, true);
    }
}
