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

        $returns = null;

        if (preg_match('/- Return \[ (.*?) \]/', $export, $m)) {
            $returns = $m[1];
        }

        return compact('params', 'returns');
    }

    /**
     * Generate documentation for a class.
     *
     * It will be fun.
     *
     * @param string $className The class to generate for.
     *
     * @return $this
     */
    public function loadClass(string $className)
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
            // This method just echos output, wtf.
            ob_start();
            \ReflectionMethod::export($className, $method->getName());
            $export = ob_get_clean();

            $inferred = $this->parseMethodExport($export);
            var_dump ($inferred); die;

            var_dump ($method->export($className, $method->getName())); die;

            $comments[$method->getName()] = $method->getDocComment();
        }

        var_dump ($comments); die;
    }
}