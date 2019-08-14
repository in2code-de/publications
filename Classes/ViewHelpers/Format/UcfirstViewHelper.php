<?php
declare(strict_types=1);
namespace In2code\Publications\ViewHelpers\Format;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class UcfirstViewHelper
 */
class UcfirstViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render(): string
    {
        return ucfirst($this->renderChildren());
    }
}
