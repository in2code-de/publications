<?php

declare(strict_types = 1);

namespace In2code\Publications\Import\Importer;

interface ImporterInterface
{
    /**
     * converts an given string into an array
     *
     * @param string $filePath absolute path to the uploaded file in the php temp dir
     * @return array array which contains the publications.
     */
    public function convert(string $filePath): array;
}
