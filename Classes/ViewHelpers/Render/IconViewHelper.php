<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Render;

use TYPO3\CMS\Core\SystemResource\Exception\CanNotResolvePublicResourceException;
use TYPO3\CMS\Core\SystemResource\Exception\CanNotResolveSystemResourceException;
use TYPO3\CMS\Core\SystemResource\Publishing\SystemResourcePublisherInterface;
use TYPO3\CMS\Core\SystemResource\Publishing\UriGenerationOptions;
use TYPO3\CMS\Core\SystemResource\SystemResourceFactory;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class IconViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly SystemResourceFactory $systemResourceFactory,
        private readonly SystemResourcePublisherInterface $resourcePublisher,
    ) {}

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('filename', 'string', 'filename', true);
        $this->registerArgument('class', 'string', 'css class', false);
        $this->registerArgument('style', 'string', 'css style', false);
    }

    /**
     * @throws CanNotResolvePublicResourceException
     * @throws CanNotResolveSystemResourceException
     */
    public function render(): string
    {
        $iconCode = '';
        $filename = explode('.', $this->arguments['filename']);
        $fileType = end($filename);
        $docxResource = $this->systemResourceFactory->createPublicResource('EXT:publications/Resources/Public/Icons/docx.svg');
        $pdfResource = $this->systemResourceFactory->createPublicResource('EXT:publications/Resources/Public/Icons/pdf.svg');

        switch ($fileType) {
            case 'doc':
            case 'docx':
                $iconCode = '<img src="' . $this->resourcePublisher->generateUri($docxResource, null, new UriGenerationOptions(absoluteUri: true)) . '" '
                    . $this->buildAttributes()
                    . ' alt="' . $fileType . '">';
                break;
            case 'pdf':
                $iconCode = '<img src="' . $this->resourcePublisher->generateUri($pdfResource, null, new UriGenerationOptions(absoluteUri: true)) . '" '
                    . $this->buildAttributes()
                    . ' alt="' . $fileType . '">';
                break;
        }

        return $iconCode;
    }

    /**
     * @return string
     */
    protected function buildAttributes(): string
    {
        $attributes = '';

        if ($this->arguments['style']) {
            $attributes = ' style="' . $this->arguments['style'] . '" ';
        }

        if ($this->arguments['class']) {
            $attributes = ' class="' . $this->arguments['class'] . '" ';
        }

        return $attributes;
    }
}
