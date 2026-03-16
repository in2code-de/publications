<?php

declare(strict_types=1);

namespace In2code\Publications\Tca;

use In2code\Publications\Utility\ObjectUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

abstract class AbstractSelection
{
    protected function getFieldOptionsFromTsConfig(array $params, array $configuration, string $labelFallback = ''): array
    {
        if (!empty($configuration)) {
            foreach (array_keys($configuration) as $key) {
                $configuration[$key] = $this->getLabel($configuration[$key], $labelFallback . $key);
            }
        }

        return $configuration;
    }

    protected function getLabel(string $label, string $fallback): string
    {
        if (str_starts_with($label, 'LLL:')) {
            $label = ObjectUtility::getLanguageService()->sL($label);
        }
        if (empty($label)) {
            $label = $fallback;
        }
        return $label;
    }

    protected function getPageIdentifier(array $params): int
    {
        $pageIdentifier = 0;
        if (!empty($params['row']['pid'])) {
            $pageIdentifier = (int)$params['row']['pid'];
        }
        if (!empty($params['flexParentDatabaseRow']['pid'])) {
            $pageIdentifier = (int)$params['flexParentDatabaseRow']['pid'];
        }
        return $pageIdentifier;
    }
}
