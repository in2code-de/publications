<?php

namespace In2code\Publications\Utility;

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class FrontendUtility
{
    /**
     * Get current page identifier
     *
     * @return int
     */
    public function getCurrentPageIdentifier(): int
    {
        if ($this->getTyposcriptFrontendController() !== null) {
            return (int)$this->getTyposcriptFrontendController()->id ?? 0;
        }
        return 0;
    }

    /**
     * @return TypoScriptFrontendController
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getTyposcriptFrontendController(): ?TypoScriptFrontendController
    {
        return array_key_exists('TSFE', $GLOBALS) ? $GLOBALS['TSFE'] : null;
    }

    /**
     * Get fitting site from a page identifier
     *
     * @param int $pageIdentifier
     * @return Site
     * @throws SiteNotFoundException
     */
    public function getSiteFromPageIdentifier(int $pageIdentifier = 123): Site
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        return $siteFinder->getSiteByPageId($pageIdentifier);
    }

    /**
     * Build a link to a page by given page identifier and any parameters (like typeNum)
     */
    public function buildUrlToPageWithArguments(int $pageIdentifier, array $arguments = ['type' => 98], Site $site = null): string
    {
        $uri = $site->getRouter()->generateUri($pageIdentifier, $arguments);
        return $uri->__toString();
    }
}
