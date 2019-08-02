<?php

namespace In2code\Publications\Validation\Validator;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class ClassValidator extends AbstractValidator
{
    public function isValid($value)
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
