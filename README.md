# TYPO3 Web Components Rendering

This extension provides tools to render Web Components with TYPO3.

## TypoScript based rendering

```
tt_content.tx_myext_mycontentelement = WEBCOMPONENT
tt_content.tx_myext_mycontentelement {
  tagName = my-web-component
  properties {
    title.data = header
    greeting = Hello World!
  }
}
```

Generates the output:

```html
<my-web-component
    title="This is the title from the content element record"
    greeting="Hello World!"
></my-web-component>
```

## Component class based rendering

You can populate the web component with PHP:

```
tt_content.tx_myext_mycontentelement = WEBCOMPONENT
tt_content.tx_myext_mycontentelement.component = Acme\MyExt\Components\MyContentElement
```

```php
<?php

namespace Acme\MyExt\Components;

use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\DataProviding\Traits\ContentObjectRendererTrait;
use Sinso\Webcomponents\Dto\ComponentRenderingData;

class MyContentElement implements ComponentInterface
{
    use ContentObjectRendererTrait;

    public function provide(ComponentRenderingData $componentRenderingData): WebcomponentRenderingData
    {
        $record = $componentRenderingData->getContentRecord();
        $properties = [
            'title' => $record['header'],
            'greeting' => 'Hello World!',
        ];

        $componentRenderingData->setTagName('my-web-component');
        $componentRenderingData->setTagProperties($properties);
    }
}
```

## Abort rendering

The component classes can use the `\Sinso\Webcomponents\DataProviding\Traits\Assert` trait to abort rendering, for example if the record is not available:

```php
<?php

namespace Acme\MyExt\Components;

use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\DataProviding\Traits\Assert;
use Sinso\Webcomponents\DataProviding\Traits\ContentObjectRendererTrait;
use Sinso\Webcomponents\DataProviding\Traits\FileReferences;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Core\Resource\FileReference;

class Image implements ComponentInterface
{
    use Assert;
    use ContentObjectRendererTrait;
    use FileReferences;

    public function provide(ComponentRenderingData $componentRenderingData): WebcomponentRenderingData
    {
        $record = $componentRenderingData->getContentRecord();
        $image = $this->loadFileReference($record, 'image');

        // rendering will stop here if no image is found
        $this->assert($image instanceof FileReference, 'No image found for record ' . $record['uid']);

        $componentRenderingData->setTagName('my-image');
        $componentRenderingData->setTagProperties(['imageUrl' => $image->getPublicUrl()]);
    }
}
```

## Render a web component in Fluid

```html
<html
    xmlns:wc="http://typo3.org/ns/Sinso/Webcomponents/ViewHelpers"
    data-namespace-typo3-fluid="true"
>

<wc:render
    component="Acme\\MyExt\\Components\\LocationOverview"
    inputData="{'header': 'This is the header'}"
/>

</html>
```
