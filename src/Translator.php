<?php
namespace ngyuki\Ephp;

use Microsoft\PhpParser\Node;

/**
 * @internal
 */
class Translator
{
    /**
     * @var string
     */
    private $echo;

    /**
     * @var callable|null
     */
    private $includeWrapper;

    public function __construct($echo = 'htmlspecialchars', callable $includeWrapper = null)
    {
        $this->echo = $echo;
        $this->includeWrapper = $includeWrapper;
    }

    public function translate(string $source, string $filename = null): string
    {
        return (new NodeTraverser())->traverse($source, function (Node $node, callable $next) use ($filename) {
            if ($node instanceof Node\Expression\EchoExpression) {
                if ($node->echoKeyword === null) {
                    return $this->echo . '(' . $next($node->expressions, $filename) . ')';
                }
            } elseif ($node instanceof Node\Expression\ScriptInclusionExpression) {
                if ($this->includeWrapper !== null) {
                    return $node->requireOrIncludeKeyword->getFullText($node->getFileContents())
                        .  ' (' . ($this->includeWrapper)($next($node->expression, $filename)) . ')';
                }
            } elseif ($node instanceof Node\QualifiedName) {
                if ($filename !== null) {
                    if ($node->getText() === '__DIR__') {
                        return $node->getLeadingCommentAndWhitespaceText() . var_export(dirname($filename), true);
                    }
                    if ($node->getText() === '__FILE__') {
                        return $node->getLeadingCommentAndWhitespaceText() . var_export($filename, true);
                    }
                }
            }
            return null;
        });
    }
}
