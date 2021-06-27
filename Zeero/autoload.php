<?php

spl_autoload_register(function($classname){

    if(strpos($classname , "Zeero") === 0) 
    {
        $name = str_replace("Zeero\\" , '' , $classname);
        $name = str_replace("\\" , DIRECTORY_SEPARATOR , $name);
        $file = __DIR__ .DIRECTORY_SEPARATOR . $name . ".php";

        if(!file_exists($file)) {
            throw new Exception("Class $classname not found");
        } else require $file;

    }
});