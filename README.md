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
Turn some ugly class method like this:
```php
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

```

into a beautiful PEAR compliant file like this:

```php
    /**
     * Some small desc
     *
     * Long description here TODO
     *
     * @param Tokenizer $tok       Some description here TODO
     * @param string    $stringArg This was my string arg desc.
     * @param integer   $intArg    This was my int arg desc.
     *
     * @Route(name="_some_symfony_type_thing", url="/one/{two}")
     *
     * @todo I don't like using easy to read routes!
     *
     * @Annot\Some\Custom\Thing Blabla
     *
     * @return array
     */
    public function blub(Tokenizer $tok, string $stringArg, int $intArg): array
    {
        return [$stringArg, $intArg];
    }

```

# Setup

Somewhat usable, as long as you're using against a VCS repository so
you can audit what it changes!

# TODO
## PHP
- Handle abstract/static keywords on functions
- User customizations for width/space etc.
- Wordwrap
- Un-hardcode 4 space indentation for class files (have Tokenizer
  figure out spacing)
- Allow more user customizations for short/long/param PHer descs
- Infer the '@throws' comments based on what throw calls are in function.

# About
## Maintainer
You can reach me at Matthew Carter <m@ahungry.com> or file an issue here.

## License
AGPLv3
