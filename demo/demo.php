<?php
require_once "../Zeero/autoload.php";
require_once "Request.php";

use Zeero\Validator\Validator;


// You can validate inline
$rules = [
    'name' => 'min:5',
    'id' => 'required|pattern:digit|same:_id2'
];


$messages = [
    'required' => 'Please fill all fields',
    'name.min' => "Name must be greatest or equal to {min} chars",
    'id.pattern' => 'id must be a digit'
];


$data = [ 'name' => 'carlos', 'id' => 21, 'id2' => 2 ];

$validator = new Validator($rules , $messages);

$validator->make($data);

// true if fails
var_dump($validator->fail());

// true if success
var_dump($validator->success());

// array of tests
print_r($validator->tests());

// array of messages
print_r($validator->errors());



// or via child class of Zeero\Validator\Form

$request = new Request;

print_r($request->tests());
print_r($request->errors());
var_dump($request->success());
var_dump($request->fail());