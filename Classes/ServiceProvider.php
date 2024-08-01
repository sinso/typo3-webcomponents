<?php

declare(strict_types=1);

namespace Sinso\Webcomponents;

use Psr\Container\ContainerInterface;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\Core\Package\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{

    protected static function getPackagePath(): string
    {
        return __DIR__ . '/../';
    }

    protected static function getPackageName(): string
    {
        return 'sinso/webcomponents';
    }

    public function getFactories(): array
    {
        return [];
    }

    public function getExtensions(): array
    {
        return [
            'content-blocks.typoscript' => static::overwriteContentBlocksTypoScript(...),
        ] + parent::getExtensions();
    }

    public static function overwriteContentBlocksTypoScript(ContainerInterface $container, \ArrayObject $typoScriptArrayObject): \ArrayObject
    {
        $contentBlockRegistry = $container->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
                if ($tableDefinition->getContentType() !== ContentType::CONTENT_ELEMENT) {
                    continue;
                }
                $contentBlockExtPath = $contentBlockRegistry->getContentBlockExtPath($typeDefinition->getName());
                $typoScript = <<<HEREDOC
tt_content.{$typeDefinition->getTypeName()} >
tt_content.{$typeDefinition->getTypeName()} = WEBCOMPONENT
tt_content.{$typeDefinition->getTypeName()}.componentFolder = $contentBlockExtPath
HEREDOC;
                $typoScriptArrayObject->append($typoScript);
            }
        }

        return $typoScriptArrayObject;
    }
}