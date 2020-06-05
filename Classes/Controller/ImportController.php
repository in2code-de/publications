<?php
declare(strict_types=1);
namespace In2code\Publications\Controller;

use Doctrine\DBAL\DBALException;
use In2code\Publications\Service\ImportService;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * Class ImportController
 */
class ImportController extends ActionController
{
    /**
     * @return void
     */
    public function overviewAction()
    {
        $this->view->assignMultiple(
            [
                'availableImporter' => $this->getExistingImporter(),
                'pid' => GeneralUtility::_GP('id')
            ]
        );
    }

    /**
     * @param array $file
     * @param string $importer
     * @Extbase\Validate("\In2code\Publications\Validation\Validator\UploadValidator", param="file")
     * @Extbase\Validate("\In2code\Publications\Validation\Validator\ClassValidator", param="importer")
     * @throws DBALException
     */
    public function importAction(array $file, string $importer)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $importService = $this->objectManager->get(
            ImportService::class,
            $file['tmp_name'],
            $this->objectManager->get($importer)
        );
        $importService->import();
        $this->view->assignMultiple(
            [
                'import' => $importService
            ]
        );
    }

    /**
     * @return string
     * @throws StopActionException
     */
    public function errorAction()
    {
        $this->clearCacheOnError();
        $this->addValidationErrorMessages();
        $this->addErrorFlashMessage();
        $this->forwardToReferringRequest();

        return $this->getFlattenedValidationErrorMessage();
    }

    /**
     * @return bool|string
     */
    protected function getErrorFlashMessage()
    {
        return false;
    }

    /**
     * Adds the Validation error Messages to the FlashMessage Queue
     */
    protected function addValidationErrorMessages()
    {
        if ($this->controllerContext->getArguments()->getValidationResults()->hasErrors()) {
            $validationErrors = $this->controllerContext->getArguments()->getValidationResults()->getFlattenedErrors();
            foreach ($validationErrors as $errors) {
                if (!empty($errors)) {
                    /** @var Error $error */
                    foreach ($errors as $error) {
                        /** @var FlashMessage $message */
                        $message = GeneralUtility::makeInstance(
                            FlashMessage::class,
                            $error->getMessage(),
                            $error->getTitle(),
                            FlashMessage::ERROR,
                            true
                        );
                        $this->controllerContext->getFlashMessageQueue()->addMessage($message);
                    }
                }
            }
        }
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getExistingImporter(): array
    {
        $importer = [];

        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer'])
        ) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer'] as $importerTitle => $importerClass) {
                if (!class_exists($importerClass)) {
                    $this->addFlashMessage(
                        'Importer class "' . $importerClass . '" was not found',
                        'Importer class not found',
                        AbstractMessage::ERROR
                    );
                } else {
                    $importer[$importerClass] = $importerTitle;
                }
            }

        }

        return $importer;
    }
}
