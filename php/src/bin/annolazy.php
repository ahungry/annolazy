<?php

if (empty($argv[1]) || !file_exists($argv[1])) {
    echo 'Please pass a valid PHP file as the initial arg.';
    exit(1);
}

//require __DIR__ . '/../t/fixtures/MyClass.php';
require_once $argv[1];
//require_once __DIR__ . '/../Annolazy/Service/DocGenerator.php';
//require_once __DIR__ . '/../Annolazy/Service/Tokenizer.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Annolazy\Service\DocGenerator;
use Annolazy\Service\Tokenizer;
//use Fake\Service\MyClass;

$generator = new DocGenerator();
//$generator->loadClass(MyClass::class);
//echo $generator->getComment(MyClass::class, 'methods', 'foo');

$tokenizer = new Tokenizer($generator);
//$tokenizer->loadFile(__DIR__ . '/../t/fixtures/MyClass.php');
$tokenizer->loadFile($argv[1]);
$output = $tokenizer->parse();
file_put_contents($argv[1], $output);

//$tokenizer->loadFile(__DIR__ . '/../Annolazy/Service/DocGenerator.php');
//echo $tokenizer->parse();