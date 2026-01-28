<?php

declare(strict_types=1);

namespace In2code\Publications\Upgrades;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Upgrades\AbstractListTypeToCTypeUpdate;

#[UpgradeWizard('publicationCTypeUpdate')]
final class PublicationCTypeUpdate extends AbstractListTypeToCTypeUpdate
{
    public function getTitle(): string
    {
        return 'Migrate "Publication" plugins to content elements.';
    }

    public function getDescription(): string
    {
        return 'The "Publication" plugins are now registered as content element. Update migrates existing records and backend user permissions.';
    }

    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'publications_pi1' => 'publications_pi1',
        ];
    }
}
