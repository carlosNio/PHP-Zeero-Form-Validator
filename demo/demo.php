<?php
require_once "../Zeero/autoload.php";
require_once "Request.php";

use Zeero\Validator\Rule;
use Zeero\Validator\Validator;



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

var_dump($validator->fail());

var_dump($validator->success());

print_r($validator->tests());

print_r($validator->errors());


// OR VIA A CLASS THAT EXTENDS FROM Validator\Form

$request = new Request;

print_r($request->tests());
print_r($request->errors());
var_dump($request->success());
var_dump($request->fail());