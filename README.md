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

Convention: When the tag name is not set, the web component will not be rendered at all.

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
