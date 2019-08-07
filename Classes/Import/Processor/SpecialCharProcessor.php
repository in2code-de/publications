<?php

namespace In2code\Publications\Import\Processor;

use RenanBr\BibTexParser\Processor\TagCoverageTrait;

class SpecialCharProcessor
{
    use TagCoverageTrait;

    private $from = ['{\\"A}', '{\\"O}', '{\\"U}', '{\\"a}', '{\\"o}', '{\\"u}', '{\\"s}'];

    private $to = ['Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ß'];

    /**
     * @param array $entry
     *
     * @return array
     */
    public function __invoke(array $entry)
    {
        $this->setTagCoverage(['_original'], 'blacklist');
        $covered = $this->getCoveredTags(array_keys($entry));

        foreach ($covered as $tag) {
            $entry[$tag] = str_replace($this->from, $this->to, $entry[$tag]);

        }

        return $entry;
    }
}
