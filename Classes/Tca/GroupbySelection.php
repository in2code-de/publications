<?php

declare(strict_types=1);

namespace In2code\Publications\Tca;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class GroupbySelection extends AbstractSelection
{
    public function getFields(array &$params): void
    {
        $params['items'] = [];
        $tsConfig = BackendUtility::getPagesTSconfig($this->getPageIdentifier($params));

        $fieldOptions = $this->getFieldOptionsFromTsConfig(
            $params,
            (array)($tsConfig['tx_publications.']['flexForm.']['groupby.'] ?? [])
        );
        foreach ($fieldOptions as $key => $label) {
            $params['items'][] = [
                $label,
                $key,
            ];
        }
    }
}
