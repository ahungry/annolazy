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
    private $fake;

    /**
     * This is a short description, so keep me.
     *
     * This is a long one, keep me as well.
     * I really hope you do.
     * @param SomeType $argTwo This is a description.
     * @return Foo my return value.
     * @todo Make this better
     */
    public function foo(Tokenizer $argOne, $argTwo, string $argThree = ''): string
    {
        // Some inline comment that we hope sticks around...
        if (1 == 2) {
            throw new \Exception();
        }
    }

    /**
     * @Route(name="_some_symfony_type_thing", url="/one/{two}")
     * @todo I don't like using easy to read routes!
     * @Annot\Some\Custom\Thing Blabla
     */
    public function blub(string $stringArg, int $intArg)
    {
        return [$stringArg, $intArg];
    }
}