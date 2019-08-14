<?php
declare(strict_types=1);
namespace In2code\Publications\Import\Importer;

use ErrorException;
use In2code\Publications\Import\Processor\AuthorProcessor;
use In2code\Publications\Import\Processor\SpecialCharProcessor;
use RenanBr\BibTexParser\Exception\ParserException;
use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;

/**
 * Class BibImporter
 *
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
        $this->additionalTypeMapping($publication);
    }

    /**
     * @param array $publication
     * @return void
     */
    protected function additionalTypeMapping(array &$publication)
    {
        if ($publication['bibtype'] === 'Technical Report') {
            $publication['bibtype'] = 'report';
        }
        $publication['bibtype'] = strtolower($publication['bibtype']);
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
}
