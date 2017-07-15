<?php
/**
 * Auto-generate documentation for a user.
 *
 * Who wants to waste time writing out what can be inferred?
 *
 * PHP version 7+
 *
 * @category Laziness
 * @package  Annolazy
 * @author   Matthew Carter <m@ahungry.com>
 * @license  AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 * @link     http://ahungry.com
 */
namespace Annolazy\Service;

/*
 * Copied from: https://secure.php.net/manual/en/tokenizer.examples.php
 *
 * T_ML_COMMENT does not exist in PHP 5.
 * The following three lines define it in order to
 * preserve backwards compatibility.
 *
 * The next two lines define the PHP 5 only T_DOC_COMMENT,
 * which we will mask as T_ML_COMMENT for PHP 4.
 */
if (!defined('T_ML_COMMENT')) {
    define('T_ML_COMMENT', T_COMMENT);
} else {
    define('T_DOC_COMMENT', T_ML_COMMENT);
}

/**
 * Auto-generate documentation for a user.
 *
 * Who wants to waste time writing out what can be inferred?
 *
 * PHP version 7+
 *
 * @category Laziness
 * @package  Annolazy\DocGenerator
 * @author   Matthew Carter <m@ahungry.com>
 * @license  AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 * @link     http://ahungry.com
 */
class Tokenizer
{
    const NEXT = 1;
    const PREV = -1;

    private $source;
    private $tokens;
    private $generator;

    public function __construct(DocGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function loadFile($fileName)
    {
        $this->source = file_get_contents($fileName);
        $this->tokens = token_get_all($this->source);
    }

    public function findToken(array $tokens, int $type, $direction = 1)
    {
        // Re-index at 0
        $tokens = array_values($tokens);

        for ($c = 0; $c < count($tokens); $c += $direction) {
            $token = $tokens[$c];

            if (is_string($token)) {
                continue;
            }

            list($id, $text) = $token;

            if ($id === $type) {
                return $text;
            }
        }

        return null;
    }

    public function findTokensUntil(
        array $tokens,
        $type,
        $direction = 1,
        array $keep = []
    ) {
        // Re-index at 0
        $tokens = array_values($tokens);
        $collect = [];

        for ($c = 0; $c < count($tokens); $c += $direction) {
            $token = $tokens[$c];

            if (is_string($token)) {
                if ($type === $token) {
                    return $collect;
                }

                if (empty($keep) || in_array($token, $keep)) {
                    $collect[] = $token;
                }

                continue;
            }

            list($id, $text) = $token;

            if ($id === $type) {
                return $collect;
            }

            if (empty($keep) || in_array($id, $keep)) {
                $collect[] = $text;
            }
        }

        return $collect;
    }

    public function parse()
    {
        $ctx = [
            'namespace' => null,
            'class' => null,
        ];

        $out = '';

        // Iterate with a C style iteration, as we may need to get
        // access to previous/next elements.
        for ($c = 0; $c < count($this->tokens); $c++) {
            $token = $this->tokens[$c];

            if (is_string($token)) {
                // simple 1-character token
                $out .= $token;
            } else {
                // token array
                list($id, $text) = $token;

                // https://secure.php.net/manual/en/tokens.php
                switch ($id) {
                    //case T_COMMENT:
                    //case T_ML_COMMENT: // we've defined this
                  case T_DOC_COMMENT: // and this
                      // If we aren't in a class context, don't drop comments
                      if (empty($ctx['class'])) {
                          $out .= $text;
                      }

                      break;

                  case T_NAMESPACE:
                      $name = $this->findTokensUntil(
                          array_slice($this->tokens, $c + 1),
                          ';',
                          self::NEXT,
                          [T_STRING, T_NS_SEPARATOR]
                      );

                      $ctx['namespace'] = implode('', $name);
                      $out .= $text;

                      break;

                  case T_CLASS:
                      $name = $this->findToken(
                          array_slice($this->tokens, $c + 1),
                          T_STRING,
                          self::NEXT
                      );

                      $ctx['class'] = $name;
                      $out .= $text;

                      break;

                      // If we have a function, get a comment for it.
                  case T_PRIVATE:
                  case T_PUBLIC:
                  case T_PROTECTED:
                      if (T_FUNCTION !== $this->tokens[$c + 2][0]) {
                          $out .= $text;

                          break;
                      }

                      //case T_FUNCTION:
                      //$name = next($this->tokens);
                      $name = $this->findToken(
                          array_slice($this->tokens, $c + 1),
                          T_STRING,
                          self::NEXT
                      );

                      $class = $ctx['namespace'] . '\\' . $ctx['class'];
                      $this->generator->loadClass($class);
                      $comment = $this->generator->getComment($class, 'methods', $name);

                      $out .= $comment;
                      $out .= '    ' . $text;

                      break;

                  case T_STRING:
                  default:
                      $out .= $text;

                      break;
                }
            }
        }

        return $out;
    }
}