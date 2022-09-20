<?php

declare(strict_types=1);

namespace In2code\Publications\Import\Processor;

use RenanBr\BibTexParser\Processor\NamesProcessor;
use RenanBr\BibTexParser\Processor\TagCoverageTrait;

/**
 * Class AuthorProcessor
 */
class AuthorProcessor extends NamesProcessor
{
    use TagCoverageTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setTagCoverage(['author']);
    }
}
