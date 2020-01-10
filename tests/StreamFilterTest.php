<?php
namespace Test;

use ngyuki\Ephp\StreamFilter;
use PHPUnit\Framework\TestCase;

class StreamFilterTest extends TestCase
{
    public function test()
    {
        $file = __DIR__ . '/_files/01.phtml';
        ob_start();
        try {
            require (new StreamFilter())->path($file);
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
        $file = __DIR__ . '/_files/inc1.phtml';
        ob_start();
        try {
            require (new StreamFilter())->path($file);
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
