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
        $this->publishingDateMapping($publication);
    }

    /**
     * @param array $publication
     * @return void
     */
    protected function publishingDateMapping(array &$publication)
    {
        if (!empty($publication['date'])) {
            $date = null;
            $format = 'Y-m-d H:i:s';
            // key 0 = year, key 1 = month, key 2 = day
            $dateArray = explode('-', $publication['date']);

            // format: Y-m-d e.g. 2019-08-09
            if (!empty($dateArray[1]) && !empty($dateArray[2])) {
                $date = DateTime::createFromFormat($format, $publication['date'] . ' 00:00:00');
            }

            // format: Y-m e.g. 2019-08
            if (empty($dateArray[2]) && !empty($dateArray[1])) {
                $date = DateTime::createFromFormat($format, $publication['date'] . '-01 00:00:00');
            }

            // format: Y e.g. 2019
            if (empty($dateArray[1]) && empty($dateArray[2])) {
                $date = DateTime::createFromFormat($format, $publication['date'] . '-01-01 00:00:00');
            }

            if ($date instanceof DateTime) {
                $publication['date'] = $date->getTimestamp();
            }
        } else {
            unset($publication['date']);
        }
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
