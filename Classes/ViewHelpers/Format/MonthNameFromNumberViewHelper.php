<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Format;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class MonthNameFromNumberViewHelper
 */
class MonthNameFromNumberViewHelper extends AbstractViewHelper implements SingletonInterface
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('month', 'string', 'Name or number', true);
        $this->registerArgument('limit', 'int', 'Optional limit for characters', false, 0);
        $this->registerArgument('suffix', 'string', 'Any suffix character needed?', false, '.');
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $month = $this->arguments['month'];
        $monthlower = strtolower($month);
        $monthletter = substr($monthlower, 0, 3);
        if ($monthletter == 'jan') {
            $month = '1';
        }
        if ($monthletter == 'feb') {
            $month = '2';
        }
        if ($monthletter == 'mar' || $monthletter == 'mär') {
            $month = '3';
        }
        if ($monthletter == 'apr') {
            $month = '4';
        }
        if ($monthletter == 'may' || $monthletter == 'mai') {
            $month = '5';
        }
        if ($monthletter == 'jun') {
            $month = '6';
        }
        if ($monthletter == 'jul') {
            $month = '7';
        }
        if ($monthletter == 'aug') {
            $month = '8';
        }
        if ($monthletter == 'sep') {
            $month = '9';
        }
        if ($monthletter == 'oct' || $monthletter == 'okt') {
            $month = '10';
        }
        if ($monthletter == 'nov') {
            $month = '11';
        }
        if ($monthletter == 'dec' || $monthletter == 'dez') {
            $month = '12';
        }
        if ($month > 0) {
            if (MathUtility::canBeInterpretedAsInteger($month)) {
                $month = LocalizationUtility::translate('month.' . $month, 'publications');
            }
            if ($this->arguments['limit'] > 0 && strlen($month) > $this->arguments['limit']) {
                $month = substr($month, 0, (int)$this->arguments['limit']);
                $month .=  $this->arguments['suffix'];
            }
        } else {
            $month = '';
        }
        return $month;
    }
}
