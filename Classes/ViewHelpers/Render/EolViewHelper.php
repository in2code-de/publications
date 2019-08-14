<?php
declare(strict_types=1);
namespace In2code\Publications\ViewHelpers\Render;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class EolViewHelper
 */
class EolViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render(): string
    {
        return PHP_EOL;
    }
}
