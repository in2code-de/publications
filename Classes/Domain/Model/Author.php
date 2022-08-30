<?php

declare(strict_types = 1);

namespace In2code\Publications\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Author
 */
class Author extends AbstractEntity
{
    public const TABLE_NAME = 'tx_publications_domain_model_author';

    /**
     * @var string
     */
    protected string $lastName = '';

    /**
     * @var string
     */
    protected string $firstName = '';

    /**
     * @var string
     */
    protected string $url = '';

    /**
     * @var string
     */
    protected string $orcid = '';

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return Author
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Author
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Author
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrcid(): string
    {
        return $this->orcid;
    }

    /**
     * @param string $orcid
     * @return Author
     */
    public function setOrcid(string $orcid): self
    {
        $this->orcid = $orcid;
        return $this;
    }
}
