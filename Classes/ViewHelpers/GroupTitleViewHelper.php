<?php
declare(strict_types=1);
namespace In2code\Publications\ViewHelpers;

use In2code\Publications\Domain\Model\Publication;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GroupTitleViewHelper
 */
class GroupTitleViewHelper extends AbstractViewHelper implements SingletonInterface
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var array
     */
    protected $titles = [];

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('publication', Publication::class, 'Single Publication object', true);
        $this->registerArgument('titleField', 'string', 'Propertyname of publications object', true);
        $this->registerArgument('before', 'string', 'Add this html before', false);
        $this->registerArgument('after', 'string', 'Add this html after', false);
        $this->registerArgument('singeltonKey', 'string', 'Any key in $titles that prevents duplicates', false);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $title = $this->getTitle();
        if (!empty($title)) {
            return $this->wrapText($title);
        }
        return '';
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
     * @return string
     */
    protected function getTitle(): string
    {
        /** @var Publication $publication */
        $publication = $this->arguments['publication'];
        $title = (string)ObjectAccess::getProperty($publication, $this->arguments['titleField']);
        if (empty($this->titles[$this->getSingeltonKey()])
            || in_array($title, $this->titles[$this->getSingeltonKey()]) === false) {
            $this->titles[$this->getSingeltonKey()][] = $title;
            return htmlspecialchars($title);
        }
        return '';
    }

    /**
     * @return string
     */
    protected function getSingeltonKey(): string
    {
        if (!empty($this->arguments['singeltonKey'])) {
            return (string)$this->arguments['singeltonKey'];
        }
        return (string)$this->arguments['titleField'];
    }
}
