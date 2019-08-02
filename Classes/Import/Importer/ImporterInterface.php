<?php

namespace In2code\Publications\Import\Importer;

interface ImporterInterface
{
    /**
     * converts an given string into an array
     *
     * @param string $filePath absolute path to the uploaded file in the php temp dir
     * @return array
     */
    public function convert(string $filePath): array;
}
