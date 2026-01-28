<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Render;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class AsciiViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('code', 'string', 'ascii code', true);
    }

    public function render(): string
    {
        return chr($this->arguments['code']);
    }
}
