<?php
namespace Test;

use ngyuki\Ephp\Compiler;
use ngyuki\Ephp\StreamFilter;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @test
     */
    public function stream_filter()
    {
        $filter = new StreamFilter();

        ob_start();
        try {
            (function ($filename, $variables) use ($filter) {
                extract($variables, EXTR_SKIP);
                /** @noinspection PhpIncludeInspection */
                require $filter->path($filename);
            })(__DIR__ . '/_files/example.phtml', ['name' => 'a & b']);
        } finally {
            $output = ob_get_clean();
        }
        $this->assertEquals('<strong>A &amp; B</strong>', trim($output));
    }

    /**
     * @test
     */
    public function compiler()
    {
        $sourceDir = __DIR__ . '/_files/';
        $compiledDir = __DIR__ . '/_files/cache';
        $compiler = new Compiler($sourceDir, $compiledDir, 'htmlspecialchars', true);

        ob_start();
        try {
            (function ($filename, $variables) use ($compiler) {
                extract($variables, EXTR_SKIP);
                /** @noinspection PhpIncludeInspection */
                require $compiler->compile($filename);
            })(__DIR__ . '/_files/example.phtml', ['name' => 'a & b']);
        } finally {
            $output = ob_get_clean();
        }
        $this->assertEquals('<strong>A &amp; B</strong>', trim($output));
    }
}
