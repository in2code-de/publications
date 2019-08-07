<?php

namespace In2code\Publications\Import\Processor;

use RenanBr\BibTexParser\Processor\NamesProcessor;
use RenanBr\BibTexParser\Processor\TagCoverageTrait;

class AuthorProcessor extends NamesProcessor
{
    use TagCoverageTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setTagCoverage(['author']);
    }
}
