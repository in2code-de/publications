<?php

declare(strict_types=1);

namespace In2code\Publications\Domain\Service;

use In2code\Publications\Domain\Model\Dto\Filter;
use In2code\Publications\Utility\FrontendUtility;
use TYPO3\CMS\Core\Routing\InvalidRouteArgumentsException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

readonly class PublicationService
{
    private const EXTENSION_NAME = 'publications';
    private const PLUGIN_NAMESPACE = 'tx_publications_pi1';

    public function __construct(
        protected FrontendUtility $frontendUtility
    ) {
    }

    /**
     * @throws InvalidRouteArgumentsException
     */
    public function getGroupedPublicationLinks(
        array $publications,
        string $groupBy,
        int $ceIdentifier,
        int $itemsPerPage
    ): array {
        $groupingConfig = $this->resolveGroupingConfiguration($groupBy);

        if ($groupingConfig === null) {
            return [];
        }

        $groupLinks = [];
        [$method, $prefix] = $groupingConfig;
        $count = 0;
        $page = 0;

        foreach ($publications as $publication) {
            if ($count % $itemsPerPage === 0) {
                $page++;
            }

            if (!method_exists($publication, $method)) {
                $count++;
                continue;
            }

            $groupValue = (string)$publication->{$method}();

            if ($groupValue === '' || array_key_exists($groupValue, $groupLinks)) {
                $count++;
                continue;
            }

            $url = $this->frontendUtility->buildUrlToPageWithArguments(
                $this->frontendUtility->getCurrentPageIdentifier(),
                [self::PLUGIN_NAMESPACE => ['currentPage' => $page]],
                $this->frontendUtility->getSiteFromPageIdentifier($this->frontendUtility->getCurrentPageIdentifier())
            );

            $localizedBibType =
                LocalizationUtility::translate('bibtype.' . $groupValue, self::EXTENSION_NAME);

            $groupLinks[$groupValue] = [
                'title' => $groupValue,
                'link' => $url . '#' . $prefix . $groupValue . '-' . $ceIdentifier,
            ];

            if ($groupBy === Filter::GROUP_BY_TYPE && !empty($localizedBibType)) {
                $groupLinks[$groupValue]['title'] = $localizedBibType;
            }

            $count++;
        }

        return $groupLinks;
    }

    private function resolveGroupingConfiguration(string $groupBy): ?array
    {
        return match ($groupBy) {
            Filter::GROUP_BY_NONE => null,
            Filter::GROUP_BY_YEAR, Filter::GROUP_BY_YEAR_AND_TYPE => ['getYear', 'c'],
            Filter::GROUP_BY_TYPE => ['getBibtype', ''],
            default => $this->resolveCustomGroupingConfiguration($groupBy),
        };
    }

    private function resolveCustomGroupingConfiguration(string $fieldPath): ?array
    {
        if (str_contains($fieldPath, '.')) {
            return null;
        }

        return ['get' . ucfirst($fieldPath), 'custom-group-'];
    }
}
