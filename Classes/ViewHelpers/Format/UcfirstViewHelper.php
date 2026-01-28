<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Format;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class UcfirstViewHelper extends AbstractViewHelper
{
    public function render(): string
    {
        return ucfirst($this->renderChildren());
    }
}
