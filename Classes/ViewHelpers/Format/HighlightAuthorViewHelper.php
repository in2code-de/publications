<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Format;

use In2code\Publications\Domain\Repository\AuthorRepository;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class HighlightAuthorViewHelper
 * wraps html around search terms
 */
class HighlightAuthorViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('author', 'mixed', 'Author array', true);
        $this->registerArgument('before', 'string', 'Add this html before Author of interest', false);
        $this->registerArgument('after', 'string', 'Add this html after', false);
        $this->registerArgument('searchterms', 'mixed', 'Authors selected in Filter in flexform/TS', false);
        $this->registerArgument('authorstring', 'string', 'Authorstring from Filter', false);
        $this->registerArgument('settings', 'array', 'Settings from Flexform/TS', false);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $value = implode(PHP_EOL, GeneralUtility::trimExplode(PHP_EOL, $this->renderChildren(), true));
        if (!empty($value)) {
            if ($this->highlightAuthorFromSearch() || $this->highlighAuthorFromFlexform()) {
                return $this->wrapText($value);
            }
        }
        return $value;
    }

    protected function highlightAuthorFromSearch(): bool
    {
        if (ArrayUtility::isValidPath($this->arguments, 'settings/highlightAuthorsFromSearch')) {
            return
                !empty($this->arguments['authorstring'])
                && $this->arguments['settings']['highlightAuthorsFromSearch'] === '1'
                && $this->authorInFrontendSearch()
            ;
        }
        return false;
    }

    protected function highlighAuthorFromFlexform(): bool
    {
        if (ArrayUtility::isValidPath($this->arguments, 'settings/highlightAuthors')) {
            return
                !empty($this->arguments['searchterms'])
                && $this->arguments['settings']['highlightAuthors'] === '1'
                && $this->authorInBackendSet()
            ;
        }
        return false;
    }

    protected function authorInBackendSet(): bool
    {
        $authorRepository = GeneralUtility::makeInstance(AuthorRepository::class);
        $authorUids = GeneralUtility::intExplode(',', $this->arguments['searchterms']);
        foreach ($authorUids as $authorUid) {
            $author = $authorRepository->findByUid($authorUid);
            if ($author === $this->arguments['author']) {
                return true;
            }
        }
        return false;
    }

    protected function authorInFrontendSearch(): bool
    {
        return
            stripos($this->arguments['authorstring'], $this->arguments['author']->getLastName()) !== false ||
            stripos($this->arguments['authorstring'], $this->arguments['author']->getFirstName()) !== false
        ;
    }

    /**
     * @param string $text
     * @return string
     */
    protected function wrapText(string $text): string
    {
        if (!empty($this->arguments['before'])) {
            $text = $this->arguments['before'] . $text;
        }
        if (!empty($this->arguments['after'])) {
            $text .= $this->arguments['after'];
        }
        return $text;
    }
}
