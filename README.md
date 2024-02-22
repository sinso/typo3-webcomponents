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

    public function getContent(array $inputData): ?string
    {
        return '';
    }

    public function getProperties(array $inputData): ?array
    {
        return [
            'title' => $inputData['header'],
            'greeting' => 'Hello World!',
        ];
    }

    public function getTagName(): ?string
    {
        return 'my-web-component';
    }
}
```

The 3 methods `getContent()`, `getProperties()` and `getTagName()` have a convention: They can return null and when any of them does that the web component is not rendered at all.
