<?php

declare(strict_types=1);

namespace In2code\Publications\Import\Importer;

/**
 * Class AbstractImporter
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class AbstractImporter implements ImporterInterface
{
    /**
     * map publication fields from -> to
     *
     * @var array
     */
    protected array $additionalPublicationMapping = [];

    /**
     * @param array $publications
     * @return array
     */
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
     * @param array $publication
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function specialMapping(array &$publication)
    {
    }
}
