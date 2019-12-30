<?php
namespace ngyuki\Ephp;

use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\Token;

class Compiler
{
    /**
     * @var string
     */
    private $echo;

    /**
     * @var callable
     */
    private $includeWrapper;

    public function __construct($echo = 'htmlspecialchars', callable $includeWrapper = null)
    {
        $this->echo = $echo;
        $this->includeWrapper = $includeWrapper;
    }

    public function compile(string $source, string $filename = null): string
    {
        $parser = new Parser();
        $astNode = $parser->parseSourceFile($source);
        return $this->visit($astNode, $filename);
    }

    private function visit(Node $node, ?string $filename)
    {
        $output = '';
        if ($node instanceof Node\Expression\EchoExpression) {
            if ($node->echoKeyword === null) {
                $output .= $this->echo . '(';
                $output .= $this->visit($node->expressions, $filename);
                $output .= ')';
                return $output;
            }
        } elseif ($node instanceof Node\Expression\ScriptInclusionExpression) {
            if ($this->includeWrapper !== null) {
                $output .= $node->requireOrIncludeKeyword->getFullText($node->getFileContents());
                $output .= ' (' . ($this->includeWrapper)($this->visit($node->expression, $filename)) . ')';
                return $output;
            }
        } elseif ($node instanceof Node\QualifiedName) {
            if ($filename !== null) {
                if ($node->getText() === '__DIR__') {
                    $output .= $node->getLeadingCommentAndWhitespaceText();
                    $output .= var_export(dirname($filename), true);
                    return $output;
                }
                if ($node->getText() === '__FILE__') {
                    $output .= $node->getLeadingCommentAndWhitespaceText();
                    $output .= var_export($filename, true);
                    return $output;
                }
            }
        }

        foreach ($node->getChildNodesAndTokens() as $child) {
            if ($child instanceof Node) {
                $output .= $this->visit($child, $filename);
            } elseif ($child instanceof Token) {
                $output .= $child->getFullText($node->getFileContents());
            }
        }
        return $output;
    }
}
