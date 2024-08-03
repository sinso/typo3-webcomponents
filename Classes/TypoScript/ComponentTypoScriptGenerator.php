<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\TypoScript;

final class ComponentTypoScriptGenerator
{
    /**
     * @param array<string, class-string> $componentsByCType
     */
    public function generate(array $componentsByCType): string
    {
        $lines = [];
        foreach ($componentsByCType as $cType => $componentClassName) {
            // The reset drops any rendering definition of other extensions, for example fluid_styled_content.
            $lines[] = 'tt_content.' . $cType . ' >';
            $lines[] = 'tt_content.' . $cType . ' = WEBCOMPONENT';
            $lines[] = 'tt_content.' . $cType . '.component = ' . $componentClassName;
        }
        if ($lines === []) {
            return '';
        }
        return implode("\n", $lines) . "\n";
    }
}
