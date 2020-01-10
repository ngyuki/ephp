<?php
namespace Test;

use ngyuki\Ephp\Compiler;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CompilerTest extends TestCase
{
    public function test()
    {
        $compiled = (new Compiler(__DIR__ . '/_files/', __DIR__ . '/_files/cache', 'htmlspecialchars', true))
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
        $compiled = (new Compiler(__DIR__ . '/_files/', __DIR__ . '/_files/cache', 'htmlspecialchars', true))
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

    /**
     * @test
     */
    public function fail_missing_source_dir()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('%^Unable source dir realpath "\S+"$%');

        new Compiler(__DIR__ . '/XXX/', __DIR__ . '/_files/cache');
    }

    /**
     * @test
     */
    public function fail_missing_compiled_dir()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('%^Directory not exists "\S+"$%');

        new Compiler(__DIR__ . '/_files/', __DIR__ . '/XXX/');
    }

    /**
     * @test
     */
    public function fail_missing_source_file()
    {
        $compiler = new Compiler(__DIR__ . '/_files/', __DIR__ . '/_files/cache');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('%^Unable source file realpath "\S+"$%');

        $compiler->compile(__DIR__ . '/XXX/');
    }

    /**
     * @test
     */
    public function fail_outside_source_file()
    {
        $compiler = new Compiler(__DIR__ . '/_files/', __DIR__ . '/_files/cache');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('%^Source "\S+" should be inside "\S+"$%');

        $compiler->compile(__FILE__);
    }

    /**
     * @test
     */
    public function mkdir_compile_dir()
    {
        $root = vfsStream::setup();
        $compileDir = $root->url() . '/cache';
        mkdir($compileDir);

        $compiler = new Compiler(__DIR__, $compileDir);
        $compiler->compile(__DIR__ . '/_files/01.phtml');

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function use_compiled_file()
    {
        $root = vfsStream::setup();

        $compiler = new Compiler(__DIR__, $root->url());
        $compiler->compile(__DIR__ . '/_files/01.phtml');
        $compiler->compile(__DIR__ . '/_files/01.phtml');

        $this->assertTrue(true);
    }
}
