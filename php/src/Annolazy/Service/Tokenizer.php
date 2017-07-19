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

    /**
     * The constructor, duh!
     *
     * @param DocGenerator $generator Used to query out/setup user comments.
     *
     * @return void
     */
    public function __construct(DocGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Loads up a file for parsing.
     *
     * @param string $fileName The file to load.
     *
     * @return self
     */
    public function loadFile(string $fileName): self
    {
        $this->source = file_get_contents($fileName);
        $this->tokens = token_get_all($this->source);

        return $this;
    }

    /**
     * Seek to the next occurrence of a token.
     *
     * This is used for look ahead/behind with token parsing.
     *
     * @param array   $tokens    The existing tokens array to parse.
     * @param integer $type      Which token type (see T_* constants).
     * @param mixed   $direction Positive number to go forward, negative to go back.
     *
     * @return mixed
     */
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

    /**
     * Keep finding tokens until a specific token is hit.
     *
     * @param array $tokens    The existing tokens array to parse.
     * @param mixed $type      Which token type (see T_* constants).
     * @param mixed $direction Positive number to go forward, negative to go back.
     * @param array $keep      Which token types to keep.  Default: [] (all).
     *
     * @return array The collection of found tokens.
     */
    public function findTokensUntil(
        array $tokens,
        $type,
        $direction = 1,
        array $keep = []
    ): array {
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

    /**
     * Handles parsing the tokens in a PHP source code file.
     *
     * Probably pretty error prone, use at your own risk!
     *
     * @return string
     */
    public function parse(): string
    {
        $ctx = [
            'namespace' => null,
            'class' => null,
        ];

        $out = '';
        $buf = '';

        // Iterate with a C style iteration, as we may need to get
        // access to previous/next elements.
        for ($c = 0; $c < count($this->tokens); $c++) {
            $token = $this->tokens[$c];

            if (is_string($token)) {
                // simple 1-character token
                if (empty($buf)) {
                    $out .= $token;
                } else {
                    $buf .= $token;
                }
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

                  case T_INTERFACE:
                  case T_TRAIT:
                  case T_CLASS:
                      $name = $this->findToken(
                          array_slice($this->tokens, $c + 1),
                          T_STRING,
                          self::NEXT
                      );

                      $ctx['class'] = $name;
                      $out .= $buf;
                      $out .= $text;

                      // clear the keyword buffer
                      $buf = '';

                      break;

                      // If we've started a type of function, just save the
                      // output buffer to spit out data when we hit the name,
                      // after the function keyword.
                  case T_PRIVATE:
                  case T_PUBLIC:
                  case T_PROTECTED:
                  case T_ABSTRACT:
                  case T_STATIC:
                      $buf .= $text;

                      break;

                  case T_FUNCTION:
                      $name = $this->findToken(
                          array_slice($this->tokens, $c + 1),
                          T_STRING,
                          self::NEXT
                      );

                      $class = $ctx['namespace'] . '\\' . $ctx['class'];
                      $this->generator->loadClass($class);
                      $comment = $this->generator->getComment(
                          $class,
                          'methods',
                          $name
                      );

                      $out .= $comment;      // comment
                      $out .= '    ' . $buf; // method signature
                      $out .= $text;         // actual method name

                      // Clear the buffer
                      $buf = '';

                      break;

                  case T_VARIABLE:
                      // if we were working on the buf, clear it
                      // so that we avoid annotating class level annotations.
                      $out .= $buf;
                      $out .= $text;
                      $buf = '';

                      break;

                  case T_STRING:
                  default:
                      if (empty($buf)) {
                          $out .= $text;
                      } else {
                          $buf .= $text;
                      }

                      break;
                }
            }
        }

        // Clean out all trailing space, and double newlines.
        $lines = explode("\n", $out);
        $lines = array_map('rtrim', $lines);
        $lines = implode("\n", $lines);

        // Clean out any double newlines.  If we did this during the token
        // iteration, it would probably be some speed gain.
        $out = preg_replace("/\n{3,}/sim", "\n\n", $lines);
        $out = preg_replace("/^{\n{2,}/sim", "{\n", $out);
        $out = preg_replace("/\n{2,}}$/sim", "\n}", $out);

        return $out;
    }
}