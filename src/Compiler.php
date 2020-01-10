<?php
namespace ngyuki\Ephp;

use RuntimeException;

class Compiler
{
    /**
     * @var string
     */
    private $sourceDir;

    /**
     * @var string
     */
    private $compiledDir;

    /**
     * @var string
     */
    private $echo;

    /**
     * @var bool
     */
    private $forceCompile;

    public function __construct(string $sourceDir, string $compiledDir, string $echo = 'htmlspecialchars', bool $forceCompile = false)
    {
        $this->sourceDir = realpath($sourceDir);
        if ($this->sourceDir === false) {
            throw new RuntimeException("Unable source dir realpath \"$sourceDir\"");
        }
        $this->compiledDir = $compiledDir;
        if (!is_dir($this->compiledDir)) {
            throw new RuntimeException("Directory not exists \"$compiledDir\"");
        }
        $this->sourceDir = rtrim($this->sourceDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->compiledDir = rtrim($this->compiledDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->echo = $echo;
        $this->forceCompile = $forceCompile;
    }

    public function compile(string $filename)
    {
        $sourceFile = realpath($filename);
        if ($sourceFile === false) {
            throw new RuntimeException("Unable source file realpath \"$filename\"");
        }
        if (substr($sourceFile, 0, strlen($this->sourceDir)) !== $this->sourceDir) {
            throw new RuntimeException("Source \"$filename\" should be inside \"$this->sourceDir\"");
        }
        $compiledFile = $this->compiledDir . substr($sourceFile, strlen($this->sourceDir));

        if (!$this->forceCompile && file_exists($compiledFile) && (filemtime($sourceFile) <= filemtime($compiledFile))) {
            return $compiledFile;
        }

        $compiler = new Translator($this->echo, function (string $expr) {
            $serialize = var_export(serialize($this), true);
            return '(unserialize(' . $serialize . '))->compile(' . $expr . ')';
        });

        $compiledDir = dirname($compiledFile);
        if (!is_dir($compiledDir)) {
            mkdir($compiledDir, 0777, true);
        }

        $source = file_get_contents($sourceFile);
        $compiled = $compiler->translate($source, $sourceFile);
        file_put_contents($compiledFile, $compiled);
        return $compiledFile;
    }
}
