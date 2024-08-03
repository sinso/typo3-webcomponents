<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContentObject;

use Sinso\Webcomponents\DataProviding\Traits\RenderComponent;
use Sinso\Webcomponents\Dto\ComponentRenderingData;

class WebcomponentContentObject extends AbstractWebcomponentContentObject
{
    use RenderComponent;

    protected function getDataProvidingStrategies(): iterable
    {
        return [
            $this->evaluateTypoScriptConfiguration(...),
            $this->evaluateComponentClass(...),
        ];
    }

    /**
     * @param array<string, mixed> $conf
     */
    protected function evaluateComponentClass(ComponentRenderingData $componentRenderingData, array $conf): ComponentRenderingData
    {
        $componentClassName = $this->cObj?->stdWrapValue('component', $conf, null);
        if (!is_string($componentClassName)) {
            return $componentRenderingData;
        }
        return self::evaluateComponent($componentRenderingData, $componentClassName, $this->cObj);
    }

    /**
     * @param array<string, mixed> $conf
     */
    protected function evaluateTypoScriptConfiguration(ComponentRenderingData $componentRenderingData, array $conf): ComponentRenderingData
    {
        if (is_array($conf['properties.'] ?? null)) {
            foreach ($conf['properties.'] as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $componentRenderingData->setTagProperty($key, $this->cObj?->cObjGetSingle($value, $conf['properties.'][$key . '.']));
            }
        }
        $tagName = $this->cObj?->stdWrapValue('tagName', $conf);
        if (is_string($tagName) && $tagName !== '') {
            $componentRenderingData->setTagName($tagName);
        }
        return $componentRenderingData;
    }
}
