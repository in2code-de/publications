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
        $monthlower = mb_strtolower($month);
        $monthletter = mb_substr($monthlower, 0, 3);
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
            $monthNumber = MathUtility::canBeInterpretedAsInteger($month) ? (int)$month : 0;
            if ($monthNumber > 0) {
                $month = (string)LocalizationUtility::translate('month.' . $monthNumber, 'publications');
            }
            $limit = (int)$this->arguments['limit'];
            if ($limit > 0 && mb_strlen($month) > $limit) {
                $month = $this->abbreviate($month, $monthNumber, $limit) . $this->arguments['suffix'];
            }
        } else {
            $month = '';
        }
        return $month;
    }

    /**
     * Use the localized abbreviation (e.g. "Mrz" for "März") if it fits into the limit, because cutting off the month
     * name does not produce the common abbreviation in every language. Otherwise, cut off by characters, not bytes.
     *
     * @param string $month
     * @param int $monthNumber
     * @param int $limit
     * @return string
     */
    protected function abbreviate(string $month, int $monthNumber, int $limit): string
    {
        $abbreviation = '';
        if ($monthNumber > 0) {
            $abbreviation = (string)LocalizationUtility::translate('month.short.' . $monthNumber, 'publications');
        }
        if ($abbreviation === '' || mb_strlen($abbreviation) > $limit) {
            $abbreviation = mb_substr($month, 0, $limit);
        }
        return $abbreviation;
    }
}
