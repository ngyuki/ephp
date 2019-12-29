<?php
namespace Test;

use ngyuki\Ephp\StreamFilter;
use PHPUnit\Framework\TestCase;

class StreamFilterTest extends TestCase
{
    function test()
    {
        $file = __DIR__ . '/_files/01.phtml';
        ob_start();
        try {
            require StreamFilter::path($file, 'htmlspecialchars');
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
}
