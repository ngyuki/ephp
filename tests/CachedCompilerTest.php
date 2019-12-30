<?php
namespace Test;

use ngyuki\Ephp\CachedCompiler;
use PHPUnit\Framework\TestCase;

class CachedCompilerTest extends TestCase
{
    function test()
    {
        $compiled = (new CachedCompiler(__DIR__ . '/_files/', __DIR__ . '/_files/cache', 'htmlspecialchars', true))
            ->compile(__DIR__ . '/_files/01.phtml');

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
    }

    /**
     * @test
     */
    public function inc()
    {
        $compiled = (new CachedCompiler(__DIR__ . '/_files/', __DIR__ . '/_files/cache', 'htmlspecialchars', true))
            ->compile(__DIR__ . '/_files/inc1.phtml');

        ob_start();
        try {
            require $compiled;
        } finally {
            $output = ob_get_clean();
        }
        $output = strtr($output, [DIRECTORY_SEPARATOR => '/']);
        $expected = implode("\n", [
            'this is &amp;inc1.phtml',
            'this is &amp;inc2.phtml',
            'this is &amp;inc3.phtml',
        ]);
        $this->assertStringMatchesFormat($expected, $output);
    }
}
