<?php
include 'PageParser.php';

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

$pageParser = new PageParser('https://www.amazon.co.uk/Winning-Moves-29612-Trivial-Pursuit/dp/B075716WLM/');
$json = $pageParser->parse()->getDataInJSON();

var_dump(json_decode($json, true));

