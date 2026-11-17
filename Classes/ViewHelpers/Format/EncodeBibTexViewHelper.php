<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Format;

use In2code\Publications\Domain\Model\Publication;
use In2code\Publications\Utility\BibTexUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class EncodeBibTexViewHelper
 */
class EncodeBibTexViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('property', 'string', 'Export field of the value, link fields are not encoded', false, '');
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $value = (string)$this->renderChildren();
        return in_array($this->arguments['property'], Publication::LINK_FIELDS, true)
            ? BibTexUtility::encodeUrl($value)
            : BibTexUtility::encode($value);
    }
}
