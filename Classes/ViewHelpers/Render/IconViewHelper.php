<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Render;

use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IconViewHelper
 */
class IconViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('filename', 'string', 'filename', true);
        $this->registerArgument('class', 'string', 'css class', false);
        $this->registerArgument('style', 'string', 'css style', false);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $iconCode = '';

        $fileType = end(explode('.', $this->arguments['filename']));

        switch ($fileType) {
            case 'doc':
            case 'docx':
                $iconCode = '<img src="' . PathUtility::getPublicResourceWebPath('EXT:publications/Resources/Public/Icons/docx.svg') . '" '
                    . $this->buildAttributes()
                    . ' alt="' . $fileType . '">';
                break;
            case 'pdf':
                $iconCode = '<img src="' . PathUtility::getPublicResourceWebPath('EXT:publications/Resources/Public/Icons/pdf.svg') . '" '
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
