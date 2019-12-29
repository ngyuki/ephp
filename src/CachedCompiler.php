<?php
namespace ngyuki\Ephp;

class CachedCompiler
{
    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * @var bool
     */
    private $ignoreTimestamp;

    public function __construct(string $echo, $ignoreTimestamp = false)
    {
        $this->compiler = new Compiler($echo);
        $this->ignoreTimestamp = $ignoreTimestamp;
    }

    public function compile(string $sourceFile, string $compiledFile)
    {
        if (file_exists($compiledFile) && ($this->ignoreTimestamp || filemtime($sourceFile) <= filemtime($compiledFile))) {
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
