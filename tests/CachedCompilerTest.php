<?php
namespace Test;

use ngyuki\Ephp\CachedCompiler;
use PHPUnit\Framework\TestCase;

class CachedCompilerTest extends TestCase
{
    function test()
    {
        $file = __DIR__ . '/_files/01.phtml';
        $cache = __DIR__ . '/_files/01.phtml.cache';

        try {
            $compiled = (new CachedCompiler('htmlspecialchars'))->compile($file, $cache);

            ob_start();
            try {
                require $compiled;
            } finally {
                $output = ob_get_clean();
            }
            $output = strtr($output, [DIRECTORY_SEPARATOR => '/']);
            $expected = implode("\n", [
                '<s>&lt;br&gt;</s>',
                '<s><br></s>',
                '<s>%s/tests/_files</s>',
                '<s>%s/tests/_files/01.phtml</s>',
            ]);
            $this->assertStringMatchesFormat($expected, $output);
        } finally {
            unlink($cache);
        }
    }
}
