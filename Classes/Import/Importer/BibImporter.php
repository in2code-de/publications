<?php

namespace In2code\Publications\Import\Importer;

use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;
use RenanBr\BibTexParser\Processor\NamesProcessor;

class BibImporter implements ImporterInterface
{
    const FORMAT = 'bib';

    protected $additionalMapping = [
        'citation-key' => 'citeid',
        'url' => 'web_url'
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
        $listener->addProcessor(new NamesProcessor());
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
            if (array_key_exists('author', $mappedPublication)) {
                $mappedPublication['authors'] = $mappedPublication['author'];
                unset($mappedPublication['author']);
            }

            foreach ($this->additionalMapping as $from => $to) {
                if (array_key_exists($from, $mappedPublication)) {
                    $mappedPublication[$to] = $mappedPublication[$from];
                    unset($mappedPublication[$from]);
                }
            }

            $mappedPublications[] = $mappedPublication;
        }

        return $mappedPublications;
    }
}
