<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Render;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class AsciiViewHelper
 */
class AsciiViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('code', 'string', 'ascii code', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return chr($this->arguments['code']);
    }
}
