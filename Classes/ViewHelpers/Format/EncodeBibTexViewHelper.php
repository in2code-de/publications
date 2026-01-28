<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Format;

use In2code\Publications\Utility\BibTexUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class EncodeBibTexViewHelper extends AbstractViewHelper
{
    public function render(): string
    {
        return BibTexUtility::encode($this->renderChildren());
    }
}
