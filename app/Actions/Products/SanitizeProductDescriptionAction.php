<?php

namespace App\Actions\Products;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

class SanitizeProductDescriptionAction
{
    /** @var array<int, string> */
    private const ALLOWED_TAGS = [
        'p', 'br', 'h2', 'h3', 'h4', 'strong', 'em', 's', 'u', 'a',
        'ul', 'ol', 'li', 'blockquote', 'sup', 'sub',
    ];

    /** @var array<int, string> */
    private const ALIGNABLE_TAGS = ['p', 'h2', 'h3', 'h4'];

    /** @var array<int, string> */
    private const REMOVED_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed'];

    public function handle(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrors = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="product-description">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        $matchingNodes = (new DOMXPath($document))->query('//*[@id="product-description"]');
        $root = $matchingNodes === false ? null : $matchingNodes->item(0);

        if (! $root instanceof DOMElement) {
            return null;
        }

        $this->sanitizeChildren($root);
        $sanitizedHtml = '';

        foreach ($root->childNodes as $child) {
            $sanitizedHtml .= $document->saveHTML($child);
        }

        $plainText = trim(str_replace("\u{00A0}", ' ', html_entity_decode(strip_tags($sanitizedHtml))));

        return $plainText === '' ? null : $sanitizedHtml;
    }

    private function sanitizeChildren(DOMNode $parent): void
    {
        $children = [];

        foreach ($parent->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof DOMComment) {
                $parent->removeChild($child);

                continue;
            }

            if (! $child instanceof DOMElement) {
                continue;
            }

            $tagName = strtolower($child->tagName);

            if (in_array($tagName, self::REMOVED_WITH_CONTENT, true)) {
                $parent->removeChild($child);

                continue;
            }

            $this->sanitizeChildren($child);

            if (! in_array($tagName, self::ALLOWED_TAGS, true)) {
                while ($child->firstChild !== null) {
                    $parent->insertBefore($child->firstChild, $child);
                }

                $parent->removeChild($child);

                continue;
            }

            $this->sanitizeAttributes($child, $tagName);
        }
    }

    private function sanitizeAttributes(DOMElement $element, string $tagName): void
    {
        $href = $element->getAttribute('href');
        $target = $element->getAttribute('target');
        $style = $element->getAttribute('style');

        while ($element->attributes->length > 0) {
            $attribute = $element->attributes->item(0);

            if ($attribute !== null) {
                $element->removeAttributeNode($attribute);
            }
        }

        if ($tagName === 'a' && $this->isAllowedHref($href)) {
            $element->setAttribute('href', $href);

            if ($target === '_blank') {
                $element->setAttribute('target', '_blank');
                $element->setAttribute('rel', 'noopener noreferrer nofollow');
            }
        }

        if (in_array($tagName, self::ALIGNABLE_TAGS, true)
            && preg_match('/(?:^|;)\s*text-align\s*:\s*(left|center|right|justify)\s*(?:;|$)/i', $style, $matches) === 1) {
            $element->setAttribute('style', 'text-align: '.strtolower($matches[1]));
        }
    }

    private function isAllowedHref(string $href): bool
    {
        if ($href === '') {
            return false;
        }

        $scheme = parse_url($href, PHP_URL_SCHEME);

        return $scheme === null
            || $scheme === false
            || in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel'], true);
    }
}
