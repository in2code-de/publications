<?php

declare(strict_types = 1);

namespace In2code\Publications\ViewHelpers\Format;

use In2code\Publications\Utility\BibTexUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class EncodeBibTexViewHelper
 */
class EncodeBibTexViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render(): string
    {
        return BibTexUtility::encode($this->renderChildren());
    }
}
