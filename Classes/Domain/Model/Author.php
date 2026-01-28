<?php

declare(strict_types=1);

namespace In2code\Publications\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Author extends AbstractEntity
{
    public const TABLE_NAME = 'tx_publications_domain_model_author';

    protected string $lastName = '';
    protected string $firstName = '';
    protected string $url = '';
    protected string $orcid = '';

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getOrcid(): string
    {
        return $this->orcid;
    }

    public function setOrcid(string $orcid): self
    {
        $this->orcid = $orcid;
        return $this;
    }
}
