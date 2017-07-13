<?php

require __DIR__ . '/../t/fixtures/MyClass.php';
require __DIR__ . '/../Annolazy/Service/DocGenerator.php';
require __DIR__ . '/../Annolazy/Service/Tokenizer.php';

use Annolazy\Service\DocGenerator;
use Annolazy\Service\Tokenizer;
use Fake\Service\MyClass;

$generator = new DocGenerator();
$generator->loadClass(MyClass::class);
//echo $generator->getComment(MyClass::class, 'methods', 'foo');

$tokenizer = new Tokenizer($generator);
$tokenizer->loadFile(__DIR__ . '/../t/fixtures/MyClass.php');
echo $tokenizer->parse();