<?php
namespace In2code\Publications\Utility;

class Labels
{
    /**
     * returns the main label for each author in the backend table view of all db entries,
     * in this case "first_name last_name"
     * @return void
     */
    function getAuthorLabel(&$params, &$pObj)
    {
        if ($params['table'] != 'tx_publications_domain_model_author') {
            return '';
        }
        // get complete record
        $rec = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($params['table'], $params['row']['uid']);
        // write to the label
        $params['title'] = $rec['first_name'] . ' ' . $rec['last_name'];
    }
}
