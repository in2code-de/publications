<?php

declare(strict_types=1);

namespace In2code\Publications\Import\Importer;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class AbstractImporter implements ImporterInterface
{
    /**
     * map publication fields from -> to
     */
    protected array $additionalPublicationMapping = [];

    protected function fieldMapping(array $publications): array
    {
        $mappedPublications = [];

        foreach ($publications as $publication) {
            $mappedPublication = $publication;

            foreach ($this->additionalPublicationMapping as $from => $to) {
                if (array_key_exists($from, $mappedPublication)) {
                    $mappedPublication[$to] = $mappedPublication[$from];
                    unset($mappedPublication[$from]);
                }
            }

            $this->specialMapping($mappedPublication);

            $mappedPublications[] = $mappedPublication;
        }

        return $mappedPublications;
    }

    /**
     * override this function to do a special mapping for a publication
     * e.g. convert dates etc.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function specialMapping(array &$publication): void
    {
    }
}
