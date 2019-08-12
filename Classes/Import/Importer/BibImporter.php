<?php

namespace In2code\Publications\Import\Importer;

use DateTime;
use ErrorException;
use In2code\Publications\Import\Processor\AuthorProcessor;
use In2code\Publications\Import\Processor\SpecialCharProcessor;
use RenanBr\BibTexParser\Exception\ParserException;
use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;

/**
 * Class BibImporter
 *
 * @package In2code\Publications\Import\Importer
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class BibImporter extends AbstractImporter
{
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
     * @throws ErrorException
     * @throws ParserException
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

        return $this->fieldMapping($publications);
    }

    /**
     * @param array $publication
     */
    protected function specialMapping(array &$publication)
    {
        $this->authorMapping($publication);
        $this->publishingDateMapping($publication);
        $this->additionalTypeMapping($publication);
    }

    protected function additionalTypeMapping(array &$publication)
    {
        if ($publication['bibtype'] === 'Technical Report') {
            $publication['bibtype'] = 'report';
        }
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
     * @return bool|DateTime
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
        return DateTime::createFromFormat($format, '1/' . $month . '/' . $year . ' 00:00:00');
    }
}
