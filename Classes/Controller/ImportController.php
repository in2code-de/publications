<?php

declare(strict_types=1);

namespace In2code\Publications\Controller;

use Doctrine\DBAL\DBALException;
use In2code\Publications\Service\ImportService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Error;

/**
 * Class ImportController
 */
class ImportController extends ActionController
{
    protected ModuleTemplateFactory $moduleTemplateFactory;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
    ) {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * @return ResponseInterface
     */
    public function overviewAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->assignMultiple(
            [
                'languages' => $this->getLanguages(),
                'availableImporter' => $this->getExistingImporter(),
                'pid' => GeneralUtility::_GP('id')
            ]
        );
        return $moduleTemplate->renderResponse('Backend/Import/Overview');
    }

    /**
     * @TYPO3\CMS\Extbase\Annotation\Validate("\In2code\Publications\Validation\Validator\UploadValidator", param="file")
     * @TYPO3\CMS\Extbase\Annotation\Validate("\In2code\Publications\Validation\Validator\ClassValidator", param="importer")
     * @throws DBALException
     */
    public function importAction(array $file, string $importer, array $importOptions): void
    {
        $importService =
            GeneralUtility::makeInstance(
                ImportService::class,
                $file['tmp_name'],
                GeneralUtility::makeInstance($importer),
                $importOptions
            );
        $importService->import();
        $this->view->assignMultiple(
            [
                'import' => $importService
            ]
        );
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
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

    protected function getLanguages(): array
    {
        $languages = [
            -1 => [
                'title' => LocalizationUtility::translate('LLL:EXT:core/Resources/Private/Language/locallang_mod_web_list.xlf:multipleLanguages'),
                'uid' => -1,
                'iconIdentifier' => 'global'
            ]
        ];

        if ($this->request->getAttribute('site') instanceof Site) {
            /** @var SiteLanguage $language */
            foreach ($this->request->getAttribute('site')->getLanguages() as $language) {
                $languages[$language->getLanguageId()] = [
                    'title' => $language->getNavigationTitle(),
                    'uid' => $language->getLanguageId(),
                    'iconIdentifier' => $language->getFlagIdentifier()
                ];
            }
        }

        return $languages;
    }

    /**
     * Adds the Validation error Messages to the FlashMessage Queue
     *
     * @return void
     */
    protected function addValidationErrorMessages(): void
    {
        if ($this->controllerContext->getArguments()->validate()->hasErrors()) {
            $validationErrors = $this->controllerContext->getArguments()->validate()->getFlattenedErrors();
            foreach ($validationErrors as $errors) {
                if (!empty($errors)) {
                    /** @var Error $error */
                    foreach ($errors as $error) {
                        /** @var FlashMessage $message */
                        $message = GeneralUtility::makeInstance(
                            FlashMessage::class,
                            $error->getMessage(),
                            $error->getTitle(),
                            ContextualFeedbackSeverity::ERROR,
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
                        ContextualFeedbackSeverity::ERROR
                    );
                } else {
                    $importer[$importerClass] = $importerTitle;
                }
            }
        }

        return $importer;
    }
}
