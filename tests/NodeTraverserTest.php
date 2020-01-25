<?php

namespace Test;

use ngyuki\Ephp\NodeTraverser;
use PHPUnit\Framework\TestCase;
use function PHPUnit\assertEquals;

class NodeTraverserTest extends TestCase
{    /**
     * @test
     */
    public function multi_inline_html()
    {
        $input = <<<'EOS'
            <strong><?= strtoupper($name) ?></strong>
            <strong><?= strtolower($name) ?></strong>
            <strong><?= strtolower($name) ?></strong>
EOS;
        $output = (new NodeTraverser())->traverse($input);
        assertEquals($input, $output);
    }

    /**
     * @test
     */
    public function short_echo_in_if_statement()
    {
        $input = <<<'EOS'
    <?php if ($ok): ?>
        <span><?= $ok ?></span>
    <?php endif; ?>
EOS;
        $output = (new NodeTraverser())->traverse($input);
        assertEquals($input, $output);
    }
}
