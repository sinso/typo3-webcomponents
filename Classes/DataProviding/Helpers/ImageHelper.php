<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as ExtbaseFileReference;
use TYPO3\CMS\Extbase\Service\ImageService;

class ImageHelper
{
    public function __construct(
        private readonly ImageService $imageService,
    ) {}

    public function getImageUri(ExtbaseFileReference|FileReference|null $image, int|string $width, int|string $height, string $cropVariant = 'default', bool $absolute = false): string
    {
        if ($image instanceof ExtbaseFileReference) {
            $image = $image->getOriginalResource();
        }
        if (!$image instanceof FileReference) {
            return '';
        }

        $processingInstructions = [
            'width' => $width,
            'height' => $height,
        ];

        $croppingArea = $this->getCroppingArea($image, $cropVariant);
        if ($croppingArea !== null) {
            $processingInstructions['crop'] = $croppingArea;
        }

        $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
        return $this->imageService->getImageUri($processedImage, $absolute);
    }

    private function getCroppingArea(FileReference $image, string $cropVariant): ?Area
    {
        if (!$image->hasProperty('crop') || empty($image->getProperty('crop'))) {
            return null;
        }

        /** @var string $cropValue */
        $cropValue = $image->getProperty('crop');
        $cropVariantCollection = CropVariantCollection::create($cropValue);
        $cropArea = $cropVariantCollection->getCropArea($cropVariant);
        if ($cropArea->isEmpty()) {
            return null;
        }
        return $cropArea->makeAbsoluteBasedOnFile($image);
    }
}
