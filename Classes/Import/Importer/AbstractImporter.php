<?php

namespace In2code\Publications\Import\Importer;

abstract class AbstractImporter implements ImporterInterface
{
    /**
     * map publication fields from -> to
     *
     * @var array
     */
    protected $additionalPublicationMapping = [];

    protected function fieldMapping(array $publications)
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
     */
    protected function specialMapping(array &$publication)
    {
    }
}
