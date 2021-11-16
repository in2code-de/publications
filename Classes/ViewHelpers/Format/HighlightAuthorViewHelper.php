<?php

declare(strict_types=1);

namespace In2code\Publications\ViewHelpers\Format;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use In2code\Publications\Domain\Model\Author;
use In2code\Publications\Domain\Repository\AuthorRepository;
use In2code\Publications\Utility\ObjectUtility;
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
        $this->registerArgument('searchterms', 'mixed', 'Searchterms, Authors selected in Filter in flexform/TS ', false);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $value = implode(PHP_EOL, GeneralUtility::trimExplode(PHP_EOL, $this->renderChildren(), true));
        if (!empty($value)) {
            if (!empty($this->arguments['searchterms'])) {
                $filterauthors = $this->getAuthors($this->arguments['searchterms']);
                $author[] = $this->arguments['author'];
                $match = array_intersect($filterauthors, $author); //intersect_assoc is too strict
                if (!empty($match)) {
                    return $this->wrapText($value);
                }
            }
        }
        return $value;
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


    /**
     * @param string $filter
     * @return array
     */
    protected function getAuthors(string $filter): array
    {
        $authors = [];
        $authorRepository = ObjectUtility::getObjectManager()->get(AuthorRepository::class);
        foreach (GeneralUtility::intExplode(',', $filter, true) as $identifier) {
            $authors[] = $authorRepository->findByUid($identifier);
        }
        return $authors;
    }
}
