# Annolazy

## A lazy way to generate annotations in many languages

Currently working on the PHP implementation.

The idea is that you can just point the program at your source
directory and have docblocks auto-generated and formatted according to
a nice standard/convention, saving the programmer a lot of time in
manual comment editing.

<!-- markdown-toc start - Don't edit this section. Run M-x markdown-toc-refresh-toc -->
**Table of Contents**

- [Annolazy](#annolazy)
    - [A lazy way to generate annotations in many languages](#a-lazy-way-to-generate-annotations-in-many-languages)
- [Examples](#examples)
    - [PHP](#php)
- [Setup](#setup)
- [TODO](#todo)
    - [PHP](#php)
- [About](#about)
    - [Maintainer](#maintainer)
    - [License](#license)

<!-- markdown-toc end -->

# Examples
## PHP
Turn some ugly class like this:
```php
<?php
namespace Fake\Service;

class MyClass
{
    /**
     *
     *
     *
     *
     * @param
     * @return
     */
    public function foo(Tokenizer $argOne, $argTwo, string $argThree = ''): string
    {
        // Some inline comment that we hope sticks around...
        if (1 == 2) {
            throw new \Exception();
        }
    }

    public function blub(string $stringArg, int $intArg)
    {
        return [$stringArg, $intArg];
    }
}

```

into a beautiful PEAR compliant file like this:

```php
<?php
/**
 * Bla
 *
 * Long description.
 *
 * PHP version 7+
 *
 * @category Testing
 * @package  Testing\MyClass
 * @author   Matthew Carter <m@ahungry.com>
 * @license  AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 * @link     http://ahungry.com
 */
namespace Fake\Service;

/**
 * Bla
 *
 * @category Testing
 * @package  Testing\MyClass
 * @author   Matthew Carter <m@ahungry.com>
 * @license  AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 * @link     http://ahungry.com
 */
class MyClass
{

    /**
     * Short description here @todo
     *
     * Long description here @todo
     *
     * @param Tokenizer $argOne   Some description here @todo
     * @param mixed     $argTwo   Some description here @todo
     * @param string    $argThree Some description here @todo
     *
     * @returns string
     */
    public function foo(Tokenizer $argOne, $argTwo, string $argThree = ''): string
    {
        // Some inline comment that we hope sticks around...
        if (1 == 2) {
            throw new \Exception();
        }
    }

    /**
     * Short description here @todo
     *
     * Long description here @todo
     *
     * @param string  $stringArg Some description here @todo
     * @param integer $intArg    Some description here @todo
     *
     * @returns void
     */
    public function blub(string $stringArg, int $intArg)
    {
        return [$stringArg, $intArg];
    }
}

```

# Setup

Don't use this yet, very much WIP.  But stay tuned!

# TODO
## PHP
- Combine existing user docs with inferred docs.
- Un-hardcode 4 space indentation for class files (have Tokenizer
  figure out spacing)
- Allow more user customizations for short/long/param PHer descs
- Infer the '@throws' comments based on what throw calls are in function.

# About
## Maintainer
You can reach me at Matthew Carter <m@ahungry.com> or file an issue here.

## License
AGPLv3
