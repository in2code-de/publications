<?php

declare(strict_types=1);

namespace In2code\Publications\Domain\Service;

use In2code\Publications\Domain\Model\Publication;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkFactory;
use TYPO3\CMS\Frontend\Typolink\UnableToLinkException;

readonly class PublicationExportService
{
    private const LINK_FIELDS = ['web_url', 'web_url2', 'file_url'];

    public function __construct(
        private LinkFactory $linkFactory
    ) {
    }

    /**
     * @param Publication[] $publications
     * @return array<int, array<string, string>>
     */
    public function getPropertiesForBibExport(array $publications, ServerRequestInterface $request): array
    {
        return $this->getPropertiesForExport(
            $publications,
            $request,
            static fn (Publication $publication): array => $publication->getPropertiesForBibExport()
        );
    }

    /**
     * @param Publication[] $publications
     * @return array<int, array<string, string>>
     */
    public function getPropertiesForXmlExport(array $publications, ServerRequestInterface $request): array
    {
        return $this->getPropertiesForExport(
            $publications,
            $request,
            static fn (Publication $publication): array => $publication->getPropertiesForXmlExport()
        );
    }

    /**
     * @param Publication[] $publications
     * @param callable(Publication): array<string, string> $propertiesOfPublication
     * @return array<int, array<string, string>>
     */
    private function getPropertiesForExport(
        array $publications,
        ServerRequestInterface $request,
        callable $propertiesOfPublication
    ): array {
        $contentObject = $this->getContentObject($request);
        $propertiesByUid = [];
        foreach ($publications as $publication) {
            $propertiesByUid[$publication->getUid()] = $this->resolveLinks(
                $propertiesOfPublication($publication),
                $contentObject
            );
        }
        return $propertiesByUid;
    }

    /**
     * @param array<string, string> $properties
     * @return array<string, string>
     */
    private function resolveLinks(array $properties, ContentObjectRenderer $contentObject): array
    {
        foreach (array_intersect(self::LINK_FIELDS, array_keys($properties)) as $field) {
            $url = $this->getAbsoluteUrl($properties[$field], $contentObject);
            if ($url === '') {
                unset($properties[$field]);
            } else {
                $properties[$field] = $url;
            }
        }
        return $properties;
    }

    private function getAbsoluteUrl(string $linkParameter, ContentObjectRenderer $contentObject): string
    {
        try {
            $url = $this->linkFactory->create(
                '',
                ['parameter' => $linkParameter, 'forceAbsoluteUrl' => true],
                $contentObject
            )->getUrl();
        } catch (UnableToLinkException) {
            $url = '';
        }
        return $url;
    }

    private function getContentObject(ServerRequestInterface $request): ContentObjectRenderer
    {
        $contentObject = $request->getAttribute('currentContentObject');
        if ($contentObject instanceof ContentObjectRenderer === false) {
            $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObject->setRequest($request);
        }
        return $contentObject;
    }
}
