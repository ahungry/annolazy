<?php
namespace Fake\Service;

interface InterfaceMyClass
{


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
    static public function blub(
        Tokenizer $tok,
        string $stringArg,
        int $intArg
    ): array;



}