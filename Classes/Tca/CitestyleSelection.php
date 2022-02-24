<?php

declare(strict_types=1);

namespace In2code\Publications\Tca;

use In2code\Publications\Utility\ObjectUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class CitestyleSelection
 */
class CitestyleSelection
{

    /**
     * @param array $params
     * @return void
     */
    public function getStyles(array &$params)
    {
        $params['items'] = [];
        foreach ($this->getFieldOptionsFromTsConfig($params) as $key => $label) {
            $params['items'][] = [
                $label,
                $key
            ];
        }
    }

    /**
     * Get field options from page TSConfig
     *
     * @param array $params
     * @return array
     */
    protected function getFieldOptionsFromTsConfig(array $params)
    {
        $tsConfig = BackendUtility::getPagesTSconfig($this->getPageIdentifier($params));
        $styleConfiguration = (array)$tsConfig['tx_publications.']['flexForm.']['citestyle.'];

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
     *
     * @param null|string $label
     * @param string $fallback
     * @return string
     */
    protected function getLabel($label, $fallback)
    {
        if (strpos($label, 'LLL:') === 0) {
            $label = ObjectUtility::getLanguageService()->sL($label);
        }
        if (empty($label)) {
            $label = $fallback;
        }
        return $label;
    }

    /**
     * Get current PID (starting from TCA or FlexForm)
     *
     * @param array $params
     * @return int
     */
    protected function getPageIdentifier(array $params)
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
