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
    private $noAnnot;

    /**
     * Some small desc
     *
     * @param string  $stringArg This was my string arg desc.
     * @param integer $intArg    This was my int arg desc.
     *
     * @Route(name="_some_symfony_type_thing", url="/one/{two}")
     *
     * @todo I don't like using easy to read routes!
     *
     * @Annot\Some\Custom\Thing Blabla
     *
     * @return void
     */
    public function blub(Tokenizer $tok, string $stringArg, int $intArg): array
    {
        return [$stringArg, $intArg];
    }
}