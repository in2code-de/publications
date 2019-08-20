<?php
declare(strict_types=1);
namespace In2code\Publications\ViewHelpers\Format;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RemoveEmptyLinesViewHelper
 */
class RemoveEmptyLinesViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return string
     */
    public function render(): string
    {
        return implode(PHP_EOL, GeneralUtility::trimExplode(PHP_EOL, $this->renderChildren(), true));
    }
}
