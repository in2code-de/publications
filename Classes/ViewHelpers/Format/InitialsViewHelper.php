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
        /** Reformat already stored Initials */
        $names_temp=str_replace('.', ' ', $this->renderChildren());
        /** Pick initial from each name and stich together with spacer*/
        $names = GeneralUtility::trimExplode(' ', $names_temp, true);
        $initials='';
        foreach ($names as $name) {
            $initials .= mb_substr($name, 0, 1) . $this->arguments['suffix'] . $this->arguments['glue'];
        }
        return rtrim($initials, $this->arguments['glue']);
    }
}
