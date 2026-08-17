<?php

namespace App\Helpers;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class NoteContentHelper
{
    public static function sanitize(string $html): string
    {
        static $sanitizer;

        $sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowElement('p')
                ->allowElement('br')
                ->allowElement('strong')
                ->allowElement('em')
                ->allowElement('s')
                ->allowElement('h1')
                ->allowElement('h2')
                ->allowElement('h3')
                ->allowElement('ul')
                ->allowElement('ol')
                ->allowElement('li')
                ->allowElement('blockquote')
                ->allowElement('a', ['href'])
                ->allowLinkSchemes(['https', 'http', 'mailto'])
                ->forceAttribute('a', 'rel', 'noopener noreferrer nofollow')
        );

        return $sanitizer->sanitize($html);
    }
}
