<?php

declare(strict_types = 1);

namespace In2code\Publications\Validation\Validator;

use In2code\Publications\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Class UploadValidator
 */
class UploadValidator extends AbstractValidator
{
    /**
     * @param mixed $value
     * @return void
     */
    public function isValid($value)
    {
        $this->validateForIndividualUploadErrors($value);
        $this->validateForSystemUploadErrors($value);
    }

    /**
     * @param array $value
     * @return void
     */
    protected function validateForIndividualUploadErrors(array $value)
    {
        $this->validateFileExtension($value);
        $this->validateSize($value);
    }

    /**
     * @param array $value
     * @return void
     * @throws Exception
     */
    protected function validateFileExtension(array $value)
    {
        $allowedExtensions = GeneralUtility::trimExplode(
            ',',
            strtolower(ConfigurationUtility::getSetting('upload.validation.extensions')),
            true
        );
        $extension = strtolower(pathinfo($value['name'])['extension']);
        if (in_array($extension, $allowedExtensions) === false) {
            $this->addError(
                LocalizationUtility::translate(
                    'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:' .
                    'validator.uploadValidator.extension'
                ),
                1566309068
            );
        }
    }

    /**
     * @param array $value
     * @return void
     * @throws Exception
     */
    protected function validateSize(array $value)
    {
        if ($value['size'] > (int)ConfigurationUtility::getSetting('upload.validation.size')) {
            $this->addError(
                LocalizationUtility::translate(
                    'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:validator.uploadValidator.size'
                ),
                1566307831
            );
        }
    }

    /**
     * @param array $value
     * @return void
     */
    protected function validateForSystemUploadErrors(array $value)
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
