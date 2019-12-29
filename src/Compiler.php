<?php
namespace ngyuki\Ephp;

class Compiler
{
    /**
     * @var string
     */
    private $echo;

    public function __construct($echo = 'htmlspecialchars')
    {
        $this->echo = $echo;
    }

    public function compile(string $source, string $filename = null): string
    {
        $tokens = token_get_all($source);

        if ($filename === null) {
            $dirname = null;
        } else {
            $dirname = var_export(dirname($filename), true);
            $filename = var_export($filename, true);
        }

        $open = 0;
        $output = '';

        foreach ($tokens as $token) {
            if (is_array($token)) {
                list($id, $code) = $token;
                if ($id === T_OPEN_TAG_WITH_ECHO) {
                    $open++;
                    $code = $code . $this->echo . '(';
                } elseif ($id === T_CLOSE_TAG && $open && --$open === 0) {
                    $code = ')' . $code;
                } elseif ($id === T_DIR && $dirname !== null) {
                    $code = $dirname;
                } elseif ($id === T_FILE && $filename !== null) {
                    $code = $filename;
                }
                $output .= $code;
            } else {
                $output .= $token;
            }
        }

        return $output;
    }
}
