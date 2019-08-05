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
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Publications\Domain\Model\Author>
     */
    protected $authors = null;

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
     * @param \DateTime $date
     * @return Publication
     */
    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
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
}
