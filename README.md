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

## DataProvider based rendering

You can populate the web component with PHP:

```
tt_content.tx_myext_mycontentelement = WEBCOMPONENT
tt_content.tx_myext_mycontentelement.dataProvider = Acme\MyExt\DataProvider\MyContentElementDataProvider
```

```php
<?php

namespace Acme\MyExt\DataProvider;

use Sinso\Webcomponents\DataProvider\DataProviderInterface;
use Sinso\Webcomponents\DataProvider\Traits\ContentObjectRendererTrait;

class MyContentElementDataProvider implements DataProviderInterface
{
    use ContentObjectRendererTrait;

    public function provide(WebcomponentRenderingData $webcomponentRenderingData): WebcomponentRenderingData
    {
        $record = $webcomponentRenderingData->getContentRecord();
        $properties = [
            'title' => $record['header'],
            'greeting' => 'Hello World!',
        ];

        $webcomponentRenderingData->setTagName('my-web-component');
        $webcomponentRenderingData->setTagProperties($properties);
    }
}
```

Convention: When the tag name is not set, the web component will not be rendered at all.
