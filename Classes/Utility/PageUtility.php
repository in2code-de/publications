<?php
declare(strict_types=1);

namespace In2code\Publications\Utility;

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageUtility
{
    /**
     * Get an array with child pids for an specific level
     *
     * @param array $startPids
     * @param int $recursiveLevel
     * @return array
     */
    public static function extendPidListByChildren(array $startPids = [], int $recursiveLevel = 0): array
    {
        if ($recursiveLevel <= 0) {
            return $startPids;
        }

        $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
        $pidsWithChildren = $startPids;
        foreach ($startPids as $startPid) {
            if ($startPid >= 0) {
                $subPids = $queryGenerator->getTreeList($startPid, $recursiveLevel, 0, 1);
                $pidsWithChildren =
                    array_unique(array_merge($pidsWithChildren, GeneralUtility::intExplode(',', $subPids, true)));
            }
        }

        return $pidsWithChildren;
    }
}
