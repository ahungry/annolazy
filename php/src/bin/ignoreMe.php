<?php

require __DIR__ . '/../t/fixtures/AbstractMyClass.php';
require __DIR__ . '/../t/fixtures/MyClass.php';
require_once __DIR__ . '/../../vendor/autoload.php';
//require_once __DIR__ . '/../Annolazy/Service/DocGenerator.php';
//require_once __DIR__ . '/../Annolazy/Service/Tokenizer.php';

use Annolazy\Service\DocGenerator;
use Annolazy\Service\Tokenizer;
use Fake\Service\AbstractMyClass;

$generator = new DocGenerator();
//$generator->loadClass(MyClass::class);
//echo $generator->getComment(MyClass::class, 'methods', 'foo');

$tokenizer = new Tokenizer($generator);
//$tokenizer->loadFile(__DIR__ . '/../t/fixtures/AbstractMyClass.php');
$tokenizer->loadFile(__DIR__ . '/../t/fixtures/MyClass.php');
$output = $tokenizer->parse();
echo $output;

//$tokenizer->loadFile(__DIR__ . '/../Annolazy/Service/DocGenerator.php');
//echo $tokenizer->parse();