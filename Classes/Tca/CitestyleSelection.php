<?php

declare(strict_types=1);

namespace In2code\Publications\Tca;

use In2code\Publications\Utility\ObjectUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class CitestyleSelection
{
    public function getStyles(array &$params): void
    {
        $params['items'] = [];
        foreach ($this->getFieldOptionsFromTsConfig($params) as $key => $label) {
            $params['items'][] = [
                $label,
                $key
            ];
        }
    }

    protected function getFieldOptionsFromTsConfig(array $params): array
    {
        $tsConfig = BackendUtility::getPagesTSconfig($this->getPageIdentifier($params));
        $styleConfiguration = (array)($tsConfig['tx_publications.']['flexForm.']['citestyle.'] ?? []);

        if (!empty($styleConfiguration)) {
            foreach (array_keys($styleConfiguration) as $key) {
                $styleConfiguration[$key] = $this->getLabel($styleConfiguration[$key], 'citeStyle' . $key);
            }
        }

        return $styleConfiguration;
    }

    /**
     * Return label
     *        if LLL parse
     *        if empty take value
     */
    protected function getLabel($label, $fallback): string
    {
        if (str_starts_with($label, 'LLL:')) {
            $label = ObjectUtility::getLanguageService()->sL($label);
        }
        if (empty($label)) {
            $label = $fallback;
        }
        return $label;
    }

    /**
     * Get current PID (starting from TCA or FlexForm)
     */
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
