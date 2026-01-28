<?php

declare(strict_types=1);

namespace In2code\Publications\Validation\Validator;

use In2code\Publications\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Http\UploadedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\NoServerRequestGivenException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class UploadValidator extends AbstractValidator
{
    /**
     * @throws NoServerRequestGivenException
     */
    public function isValid(mixed $value): void
    {
        $this->validateForIndividualUploadErrors($value);
        $this->validateForSystemUploadErrors($value);
    }

    /**
     * @throws NoServerRequestGivenException
     */
    protected function validateForIndividualUploadErrors(UploadedFile $value): void
    {
        $this->validateFileExtension($value);
        $this->validateSize($value);
    }

    /**
     * @throws NoServerRequestGivenException
     */
    protected function validateFileExtension(UploadedFile $value): void
    {
        $allowedExtensions = GeneralUtility::trimExplode(
            ',',
            strtolower(ConfigurationUtility::getSetting('upload.validation.extensions')),
            true
        );
        $extension = pathinfo($value->getClientFilename(), PATHINFO_EXTENSION);
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

    protected function validateSize(UploadedFile $value): void
    {
        if ($value->getSize() > (int)ConfigurationUtility::getSetting('upload.validation.size')) {
            $this->addError(
                LocalizationUtility::translate(
                    'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:validator.uploadValidator.size'
                ),
                1566307831
            );
        }
    }

    protected function validateForSystemUploadErrors(UploadedFile $value): void
    {
        if ($value->getError() !== UPLOAD_ERR_OK) {
            $messageKey = match ($value['error']) {
                UPLOAD_ERR_INI_SIZE => 'validator.uploadValidator.maxFileSize.php',
                UPLOAD_ERR_FORM_SIZE => 'validator.uploadValidator.maxFileSize.html',
                UPLOAD_ERR_PARTIAL => 'validator.uploadValidator.partialUpload',
                UPLOAD_ERR_NO_FILE => 'validator.uploadValidator.noFile',
                UPLOAD_ERR_NO_TMP_DIR => 'validator.uploadValidator.noTempDir',
                UPLOAD_ERR_CANT_WRITE => 'validator.uploadValidator.noWritePermission',
                UPLOAD_ERR_EXTENSION => 'validator.uploadValidator.extensionPreventUpload',
                default => 'validator.uploadValidator.unknownError',
            };

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
