<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Format;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class InitialsViewHelper
 */
class InitialsViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('glue', 'string', 'How to separate name parts?', false, ' ');
        $this->registerArgument('suffix', 'string', 'Any suffix character needed?', false, '.');
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $names = GeneralUtility::trimExplode(' ', $this->renderChildren(), true);
        foreach ($names as &$name) {
            $name = $name[0] . $this->arguments['suffix'];
        }
        return implode($this->arguments['glue'], $names);
    }
}
