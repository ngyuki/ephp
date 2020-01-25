<?php
namespace ngyuki\Ephp;

use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\Token;

/**
 * @internal
 */
class NodeTraverser
{
    public function traverse(string $source, callable $callback = null)
    {
        $recursive = function (Node $node) use (&$recursive, $source, $callback) {
            if ($callback) {
                $output = $callback($node, $recursive);
                if (is_string($output)) {
                    return $output;
                }
            }

            $output = '';

            $children = [];
            foreach ($node->getChildNodesAndTokens() as $child) {
                $children[] = $child;
                if ($child instanceof Node\Statement\InlineHtml && $child->echoStatement) {
                    $children[] = $child->echoStatement;
                    $child->echoStatement = null;
                }
            }

            usort($children, function ($a, $b) {
                $aa = 0;
                $bb = 0;
                if ($a instanceof Node) {
                    $aa = $a->getFullStart();
                } elseif ($a instanceof Token) {
                    $aa = $a->getFullStart();
                }
                if ($b instanceof Node) {
                    $bb = $b->getFullStart();
                } elseif ($b instanceof Token) {
                    $bb = $b->getFullStart();
                }
                return $aa <=> $bb;
            });

            foreach ($children as $child) {
                if ($child instanceof Node) {
                    $output .= $recursive($child);
                } elseif ($child instanceof Token) {
                    $output .= $child->getFullText($source);
                }
            }

            return $output;
        };

        $ast = (new Parser())->parseSourceFile($source);
        return $recursive($ast);
    }
}
