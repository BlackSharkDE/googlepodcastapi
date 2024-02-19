<?php

//Test-Beispiele
$testStrings = array(
    //Ohne Anführungszeichen
    '{fu:"bar",baz:["bat"]}',
    '{rec:{rec:{rec:false}}}',
    '{foo:[1,2,[3,4]]}',
    '{fu:{fu:"bar"},bar:{fu:"bar"}}',
    '{"quoted key":[1,2,3]}',
    '{und:undefined,"baz":[1,2,"3"]}',
    '{arr:["a","b"],"baz":"foo","gar":{"faz":false,t:"2"},f:false}',

    //Mit Anführungszeichen
    '{"fu":"bar","baz":["bat"]}',
    '{"rec":{"rec":{"rec":false}}}',
    '{"foo":[1,2,[3,4]]}',
    '{"fu":{"fu":"bar"},"bar":{"fu":"bar"}}',
    '{"quoted key":[1,2,3]}',
    '{"und":undefined,"baz":[1,2,"3"]}',
    '{"arr":["a","b"],"baz":"foo","gar":{"faz":false,t:"2"},"f":false}',

    //Fehlerhaftes
    '{}',
    '[]',
    null,
    '{fu-bar}',
    ''
);

//--------------------------------------------------------------------------------------------------------------------------

function test_variant_1() {
    
    global $testStrings;
    
    echo("<hr><h1>variant_1.php</h1>");
    require 'variant_1.php';
    
    foreach($testStrings as $testString) {
        $result = loose_json_decode($testString);
        var_dump($result);
        echo('<br><br>');
    }
}

//test_variant_1();

//--------------------------------------------------------------------------------------------------------------------------

function test_variant_1_modified() {
    
    global $testStrings;

    echo("<hr><h1>variant_1_modified.php</h1>");
    require 'variant_1_modified.php';
    
    foreach($testStrings as $testString) {
        $result = loose_json_decode($testString);
        var_dump($result);
        echo('<br><br>');
    }
}

test_variant_1_modified();

//--------------------------------------------------------------------------------------------------------------------------

function test_variant_2() {

    global $testStrings;

    echo("<hr><h1>variant_2.php</h1>");
    require 'variant_2.php';
    foreach($testStrings as $testString) {
        $parsed = array();
        parse_jsobj($testString, $parsed);
        var_export($parsed);
        echo('<br><br>');
    }
}

test_variant_2();