<?php

declare(strict_types=1);

namespace In2code\Publications\Tca;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class CitestyleSelection extends AbstractSelection
{
    public function getStyles(array &$params): void
    {
        $params['items'] = [];
        $tsConfig = BackendUtility::getPagesTSconfig($this->getPageIdentifier($params));

        $fieldOptions = $this->getFieldOptionsFromTsConfig(
            $params,
            (array)($tsConfig['tx_publications.']['flexForm.']['citestyle.'] ?? []),
            'citeStyle'
        );
        foreach ($fieldOptions as $key => $label) {
            $params['items'][] = [
                $label,
                $key
            ];
        }
    }
}
