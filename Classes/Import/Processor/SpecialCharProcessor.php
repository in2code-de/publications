<?php

declare(strict_types=1);

namespace In2code\Publications\Import\Processor;

use In2code\Publications\Utility\BibTexUtility;
use RenanBr\BibTexParser\Processor\TagCoverageTrait;

/**
 * Class SpecialCharProcessor
 */
class SpecialCharProcessor
{
    use TagCoverageTrait;

    /**
     * @param array $entry
     *
     * @return array
     */
    public function __invoke(array $entry): array
    {
        $this->setTagCoverage(['_original'], 'blacklist');
        $covered = $this->getCoveredTags(array_keys($entry));
        foreach ($covered as $tag) {
            $entry[$tag] = BibTexUtility::decode($entry[$tag]);
        }
        return $entry;
    }
}
