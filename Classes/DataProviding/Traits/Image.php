<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Traits;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as ExtbaseFileReference;
use TYPO3\CMS\Extbase\Service\ImageService;

trait Image
{
    public function getImageUri($image, $width, $height, string $cropVariant = 'default', bool $absolute = false): string
    {
        if ($image instanceof ExtbaseFileReference) {
            $image = $image->getOriginalResource();
        }
        if (!$image instanceof FileReference) {
            return '';
        }
        if ($image->hasProperty('crop') && $image->getProperty('crop')) {
            $cropString = $image->getProperty('crop');
        }

        $cropVariantCollection = CropVariantCollection::create((string)$cropString);
        $cropArea = $cropVariantCollection->getCropArea($cropVariant);
        $processingInstructions = [
            'width' => $width,
            'height' => $height,
            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
        ];
        if (!empty($arguments['fileExtension'])) {
            $processingInstructions['fileExtension'] = $arguments['fileExtension'];
        }

        $imageService = GeneralUtility::makeInstance(ImageService::class);
        $processedImage = $imageService->applyProcessingInstructions($image, $processingInstructions);
        return $imageService->getImageUri($processedImage, $absolute);
    }
}
