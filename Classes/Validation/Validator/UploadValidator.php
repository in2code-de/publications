<?php

namespace In2code\Publications\Validation\Validator;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class UploadValidator extends AbstractValidator
{
    public function isValid($value)
    {
        if ($value['error'] !== 0) {
            switch ($value['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $messageKey = 'validator.uploadValidator.maxFileSize.php';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $messageKey = 'validator.uploadValidator.maxFileSize.html';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $messageKey = 'validator.uploadValidator.partialUpload';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $messageKey = 'validator.uploadValidator.noFile';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $messageKey = 'validator.uploadValidator.noTempDir';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $messageKey = 'validator.uploadValidator.noWritePermission';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $messageKey = 'validator.uploadValidator.extensionPreventUpload';
                    break;
                default:
                    $messageKey = 'validator.uploadValidator.unknownError';
            }

            $this->addError(
                LocalizationUtility::translate(
                    'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' . $messageKey
                ),
                1564742588,
                [],
                LocalizationUtility::translate(
                    'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:validator.uploadValidator.title'
                )
            );
        }
    }
}
