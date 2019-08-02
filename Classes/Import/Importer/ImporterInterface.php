<?php

namespace In2code\Publications\Import\Importer;

interface ImporterInterface
{
    /**
     * converts an given string into an array
     *
     * @param string $data
     * @return array
     */
    public function convert(string $data): array;
}
