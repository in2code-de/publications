<?php
declare(strict_types=1);
namespace In2code\Publications\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Publication
 */
class Publication extends AbstractEntity
{
    const TABLE_NAME = 'tx_publications_domain_model_publication';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var \DateTime
     */
    protected $date = null;

    /**
     * @var string
     */
    protected $bibtype = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Publications\Domain\Model\Author>
     */
    protected $authors = null;

    /**
     * @var int
     * @transient
     */
    protected $_number = 0;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Publication
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getYearFromDate(): int
    {
        $date = $this->getDate();
        if (is_a($date, \DateTime::class)) {
            return (int)$date->format('Y');
        }
        return 0;
    }

    /**
     * @param \DateTime $date
     * @return Publication
     */
    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getBibtype(): string
    {
        return $this->bibtype;
    }

    /**
     * @param string $bibtype
     * @return Publication
     */
    public function setBibtype(string $bibtype): self
    {
        $this->bibtype = $bibtype;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAuthors(): ObjectStorage
    {
        return $this->authors;
    }

    /**
     * @param ObjectStorage $authors
     * @return Publication
     */
    public function setAuthors(ObjectStorage $authors): self
    {
        $this->authors = $authors;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->_number;
    }

    /**
     * @param int $number
     * @return Publication
     */
    public function setNumber(int $number): self
    {
        $this->_number = $number;
        return $this;
    }
}
