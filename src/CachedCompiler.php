<?php
namespace ngyuki\Ephp;

use RuntimeException;

class CachedCompiler
{
    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * @var bool
     */
    private $forceCompile;

    public function __construct(string $echo, $forceCompile = false)
    {
        $this->compiler = new Compiler($echo);
        $this->forceCompile = $forceCompile;
    }

    public function compile(string $sourceFile, string $compiledFile)
    {
        if (!$this->forceCompile && file_exists($compiledFile) && (filemtime($sourceFile) <= filemtime($compiledFile))) {
            return $compiledFile;
        }

        $compiledDir = dirname($compiledFile);
        if (!is_dir($compiledDir)) {
            mkdir($compiledDir, 0777 , true);
        }

        $source = file_get_contents($sourceFile);
        $compiled = $this->compiler->compile($source, $sourceFile);
        file_put_contents($compiledFile, $compiled);
        return $compiledFile;
    }
}
