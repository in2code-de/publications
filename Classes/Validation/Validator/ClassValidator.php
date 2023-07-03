<?php

declare(strict_types=1);

namespace In2code\Publications\Validation\Validator;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Class ClassValidator
 */
class ClassValidator extends AbstractValidator
{
    /**
     * @param mixed $value
     * @return void
     */
    public function isValid(mixed $value): void
    {
        if (!class_exists($value)) {
            $this->addError(
                LocalizationUtility::translate(
                    'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:validator.class.notExist',
                    null,
                    [$value]
                ),
                1564674822,
                [],
                LocalizationUtility::translate(
                    'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:validator.class.notExist.title'
                )
            );
        }
    }
}
