<?php

namespace In2code\Publications\Import\Importer;

use In2code\Publications\Import\Processor\AuthorProcessor;
use In2code\Publications\Import\Processor\SpecialCharProcessor;
use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;

class BibImporter implements ImporterInterface
{
    const FORMAT = 'bib';

    /**
     * map publication fields from -> to
     *
     * @var array
     */
    protected $additionalPublicationMapping = [
        'citation-key' => 'citeid',
        'url' => 'web_url',
        'author' => 'authors',
        'type' => 'bibtype'
    ];

    /**
     * map author fields from -> to
     *
     * @var array
     */
    protected $additionalAuthorMapping = [
        'first' => 'first_name',
        'last' => 'last_name'
    ];

    /**
     * @param string $filePath
     * @return array
     *
     * @throws \ErrorException
     * @throws \RenanBr\BibTexParser\Exception\ParserException
     */
    public function convert(string $filePath): array
    {
        $parser = new Parser();
        $listener = new Listener();

        $listener->addProcessor(new SpecialCharProcessor());
        $listener->addProcessor(new AuthorProcessor());

        $parser->addListener($listener);
        $parser->parseFile($filePath);
        
        $publications = $listener->export();

        return $this->mapping($publications);
    }

    protected function mapping(array $publications)
    {
        $mappedPublications = [];

        foreach ($publications as $publication) {
            $mappedPublication = $publication;

            foreach ($this->additionalPublicationMapping as $from => $to) {
                if (array_key_exists($from, $mappedPublication)) {
                    $mappedPublication[$to] = $mappedPublication[$from];
                    unset($mappedPublication[$from]);
                }
            }

            $this->authorMapping($mappedPublication);
            $this->publishingDateMapping($mappedPublication);

            $mappedPublications[] = $mappedPublication;
        }

        return $mappedPublications;
    }

    /**
     * @param array $publication
     */
    protected function authorMapping(array &$publication)
    {
        if (!empty($publication['authors'])) {
            foreach ($this->additionalAuthorMapping as $from => $to) {
                foreach ($publication['authors'] as $key => $author) {
                    if (array_key_exists($from, $author)) {
                        $publication['authors'][$key][$to] = $author[$from];
                        unset($publication['authors'][$key][$from]);
                    }
                }
            }
        }
    }

    /**
     * @param array $publication
     * @return void
     */
    protected function publishingDateMapping(array &$publication)
    {
        // create timestamp from fields month, year
        $timestamp = $this->createTimestamp($publication['year'], $publication['month']);
        $publication['date'] = $timestamp->getTimestamp();
        unset($publication['month'], $publication['year']);
    }

    /**
     * @param $year
     * @param $month
     * @return bool|\DateTime
     */
    protected function createTimestamp($year, $month)
    {
        $monthMapping = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12,
        ];

        if (!empty($month)) {
            $month = $monthMapping[$month];
        } else {
            // if no month is set we use january as default
            $month = 1;
        }

        $format = "d/m/Y H:i:s";
        return \DateTime::createFromFormat($format, '1/' . $month . '/' . $year . ' 00:00:00');
    }
}
