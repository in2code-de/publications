<?php

namespace In2code\Publications\Utility;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\InvalidRouteArgumentsException;
use TYPO3\CMS\Core\Site\Entity\Site;

class FrontendUtility
{
    public function getCurrentPageIdentifier(): int
    {
        $pageArguments = $this->getRequest()->getAttribute('routing');
        return $pageArguments->getPageId();
    }

    public function getSite(): Site
    {
        return $this->getRequest()->getAttribute('site');
    }

    /**
     * Build a link to a page by given page identifier and any parameters (like typeNum)
     * @throws InvalidRouteArgumentsException
     */
    public function buildUrlToPageWithArguments(int $pageIdentifier, array $arguments = ['type' => 98], Site $site = null): string
    {
        $uri = $site->getRouter()->generateUri($pageIdentifier, $arguments);
        return $uri->__toString();
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
