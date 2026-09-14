<?php

declare(strict_types=1);

namespace In2code\Publications\Import\Importer;

use In2code\Publications\Import\Processor\AuthorProcessor;
use In2code\Publications\Import\Processor\SpecialCharProcessor;
use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class BibImporter extends AbstractImporter
{
    /**
     * map publication fields from -> to
     */
    protected array $additionalPublicationMapping = [
        'citation-key' => 'citeid',
        'url' => 'web_url',
        'urldate' => 'web_url_date',
        'file' => 'file_url',
        'author' => 'authors',
        'DOI' => 'doi',
        'ISSN' => 'issn',
        'ISBN' => 'isbn',
        'misc' => 'miscellaneous',
        'misc2' => 'miscellaneous2',
        'Title' => 'title'
    ];

    /**
     * map author fields from -> to
     */
    protected array $additionalAuthorMapping = [
        'first' => 'first_name',
        'last' => 'last_name'
    ];

    public function convert(string $filePath): array
    {
        try {
            $parser = new Parser();
            $listener = new Listener();

            $listener->addProcessor(new SpecialCharProcessor());
            $listener->addProcessor(new AuthorProcessor());

            $parser->addListener($listener);
            $parser->parseFile($filePath);

            $publications = $listener->export();

            return $this->fieldMapping($publications);
        } catch (\Exception $exception) {
            throw new \LogicException('File could not be imported: ' . $exception->getMessage(), 1566308498);
        }
    }

    protected function specialMapping(array &$publication): void
    {
        $this->authorMapping($publication);
        $this->additionalTypeMapping($publication);
    }

    /**
     * Add bibtype
     */
    protected function additionalTypeMapping(array &$publication): void
    {
        $this->addBibType($publication);
        $this->removeTypeIfEmpty($publication);
    }

    protected function addBibType(array &$publication): void
    {
        $publication['bibtype'] = $this->getBibTypeFromPublication($publication);
        if ($publication['bibtype'] === 'Technical Report') {
            $publication['bibtype'] = 'report';
        }
        $publication['bibtype'] = strtolower($publication['bibtype']);
    }

    /**
     * Because the parser automatically uses key "type" for bibtype and because there is already another field with
     * the same key, we have to use it in a different way.
     */
    protected function removeTypeIfEmpty(array &$publication): void
    {
        if ($this->isTypeFieldInPublication($publication) === false) {
            unset($publication['type']);
        }
    }

    protected function authorMapping(array &$publication): void
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

    protected function isTypeFieldInPublication(array &$publication): bool
    {
        return stristr($publication['_original'], 'type = {') !== false;
    }

    protected function getBibTypeFromPublication(array &$publication): string
    {
        preg_match('~@([a-zA-Z0-9]+)~', $publication['_original'], $result);
        return $result[1];
    }
}
