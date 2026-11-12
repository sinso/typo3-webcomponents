<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto;

/**
 * HTML that is written into the page as is. Plain strings are escaped wherever they meet Markup.
 */
final readonly class Markup implements \JsonSerializable, \Stringable
{
    public function __construct(
        private string $html,
    ) {}

    /**
     * Concatenates the parts. Strings are escaped, Markup is kept as is, null is skipped.
     */
    public static function join(Markup|string|null ...$parts): self
    {
        $html = '';
        foreach ($parts as $part) {
            if ($part === null) {
                continue;
            }
            $html .= $part instanceof self ? $part->html : htmlspecialchars($part, ENT_QUOTES | ENT_SUBSTITUTE);
        }
        return new self($html);
    }

    public function isEmpty(): bool
    {
        return $this->html === '';
    }

    public function jsonSerialize(): string
    {
        return $this->html;
    }

    public function __toString(): string
    {
        return $this->html;
    }
}
