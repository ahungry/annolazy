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

use Annolazy\Model\Doc;

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
class DocGenerator
{
    private $classData = [];

    /**
     * Parse out the PHP ReflectionMethod::export output.
     *
     * This expects some data in format such as:

Method [ <user> public method foo ] {
  \@ /home/mcarter/src/annolazy/php/src/t/fixtures/MyClass.php 35 - 40

  - Parameters [3] {
    Parameter #0 [ <required> integer $argOne ]
    Parameter #1 [ <required> $argTwo ]
    Parameter #2 [ <optional> string $argThree = '' ]
  }
  - Return [ string ]
}

     * @param string $export The expected string.
     *
     * @return array
     */
    public function parseMethodExport(string $export): array
    {
        $params = [];

        // @todo Remove the docblock from the export to avoid false positives
        if (preg_match('/- Parameters \[(.*?)\] {/', $export, $count)) {
            for ($i = 0; $i < (int) $count[1]; $i++) {
                // Get the parameter export line.
                preg_match(
                    '/Parameter #'
                    . $i . ' \[ (.*?) \]/',
                    $export,
                    $m
                );

                $parts = explode(' ', $m[1]);

                $required = array_shift($parts);
                $required = $required === '<required>';

                $type = array_shift($parts);

                // We may or may not have a legit type.
                // If it started with a dollar sign, it was untyped.
                if ('$' === $type{0}) {
                    $name = $type;
                    $type = 'mixed';
                } else {
                    $name = array_shift($parts);
                }

                // Now, if we had an optional param, it may have a default.
                $default = empty($parts) ? null : implode($parts, ' ');

                $params[$i] = compact('type', 'name', 'required', 'default');
            }
        }

        $return = null;

        if (preg_match('/- Return \[ (.*?) \]/', $export, $m)) {
            $return = $m[1];
        }

        return compact('params', 'return');
    }

    /**
     * Given inferred method data, and existing data, make a comment.
     *
     * This is error prone atm, as it has only been tested under best
     * conditions.  Use at your own risk!
     *
     * @param array  $methodData  The inferred data.
     * @param string $userComment The user existing comment.
     *
     * @return string
     */
    public function generateMethodComment(
        array $methodData,
        string $userComment
    ): string {
        // Load up the comment in an easier to use document object.
        $doc = new Doc($userComment);

        $shortDesc = empty($doc->getShortDesc())
            ? 'Short description here TODO'
            : $doc->getShortDesc();

        $longDesc  = empty($doc->getLongDesc())
            ? 'Long description here TODO'
            : $doc->getLongDesc();

        $comment =<<<EOT
/**
     * {$shortDesc}
     *
     * {$longDesc}
EOT;

        // First, work out the alignments
        $wType = 0;
        $wName = 0;

        foreach ($methodData['params'] as $param) {
            // @todo Allow full or truncating types based on options
            $type = explode('\\', $param['type']);
            $type = array_pop($type);

            if (($len = strlen($type)) > $wType) {
                $wType = $len;
            }

            if (($len = strlen($param['name'])) > $wName) {
                $wName = $len;
            }
        }

        // Add a gap space
        if ($wType > 0 && $wName > 0) {
            $comment .= PHP_EOL . '     *' . PHP_EOL;
        }

        foreach ($methodData['params'] as $param) {
            // Query up the parameter we are currently working on.
            $docParam = $doc->getParam(substr($param['name'], 1));

            // @todo Allow full or truncating types based on options
            $type = explode('\\', $param['type']);
            $type = array_pop($type);

            $comment .= sprintf(
                '     * @param %-' . $wType . 's %-' . $wName . 's %s' . PHP_EOL,
                $type,
                $param['name'],
                $docParam['desc'] ?? 'Some description here TODO'
            );
        }

        // If we had no params, add the blank line.
        if (!($wType > 0 && $wName > 0)) {
            $comment .= PHP_EOL;
        }

        // Now, spew out the tags we didn't have in params or return.
        foreach ($doc->getUserTags() as $userTag) {
            if (trim($userTag)) {
                $comment .=  '     *' . PHP_EOL;
                $comment .=  '     * @' . $userTag . PHP_EOL;
            }
        }

        $docReturn = $doc->getReturn() ?? ['type' => 'void', 'desc' => ''];

        $comment .= sprintf(
            '     *' . PHP_EOL . '     * @return %s %s' . PHP_EOL,
            empty($methodData['return'])
            ? $docReturn['type'] : $methodData['return'],
            $docReturn['desc']
        );

        $comment .= '     */' . PHP_EOL;

        return $comment;
    }

    /**
     * Generate documentation for a class.
     *
     * It will be fun.
     *
     * @param string $className This is the class name to parse.
     *
     * @return self
     */
    public function loadClass(string $className): self
    {
        $refClass = new \ReflectionClass($className);

        $this->classData[$className] = [
            'comments' => [
                'methods' => [],
            ],
        ];

        $comments =& $this->classData[$className]['comments']['methods'];

        // Build and save the comments for a class.
        foreach ($refClass->getMethods() as $method) {
            // @todo Actually mix this with the inferred comment.
            $userComment = $method->getDocComment();

            /*
            if (!empty($userComment)) {
                $comments[$method->getName()] = $userComment . PHP_EOL;

                continue;
            }
            */

            // This method just echos output, wtf.
            ob_start();
            \ReflectionMethod::export($className, $method->getName());
            $export = ob_get_clean();

            $inferred = $this->parseMethodExport($export);
            $comment = $this->generateMethodComment($inferred, $userComment);

            $comments[$method->getName()] = $comment;
        }

        return $this;
    }

    /**
     * Get a comment for a class.
     *
     * Basically, a public accessor for the Tokenizer.
     *
     * @param string $className The fully namespaced class name.
     * @param string $type      The type of comment, such as 'method'.
     * @param string $name      The name of the method (or comment).
     *
     * @return void
     */
    public function getComment(string $className, string $type, string $name)
    {
        return $this->classData[$className]['comments'][$type][$name];
    }
}