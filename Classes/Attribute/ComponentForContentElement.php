<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Attribute;

/**
 * Registers a component class as the renderer of a content element type.
 *
 * The extension generates the TypoScript for the given CType, so no manual
 * `tt_content.<cType> = WEBCOMPONENT` is needed. Repeat the attribute to render
 * several content element types with the same component class.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class ComponentForContentElement
{
    public const TAG_NAME = 'webcomponents.componentForContentElement';

    public function __construct(
        public readonly string $cType,
    ) {}
}
