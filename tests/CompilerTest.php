<?php
namespace Test;

use ngyuki\Ephp\Compiler;
use PHPUnit\Framework\TestCase;

class CompilerTest extends TestCase
{
    function test()
    {
        $file = __DIR__ . '/_files/01.phtml';
        $code = (new Compiler('App\\Strings::escape'))->compile(file_get_contents($file), '/path/to/file.php');
        $this->assertIsString($code);
        $expected = implode("\n", [
            "<s><?=App\Strings::escape( '<br>' )?></s>",
            "<s><?php echo '<br>' ?></s>",
            "<s><?php echo '/path/to' ?></s>",
            "<s><?php echo '/path/to/file.php' ?></s>",
        ]);
        $this->assertEquals($expected, trim($code));
    }
}
