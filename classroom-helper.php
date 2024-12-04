<?php

require_once "classroom-data.php";
require_once "classroom.php";
function getData(){
    return DATA;
}

function generateSchoolbook($data){
    if (isset($_SESSION["schoolBook"])){
        return $_SESSION["schoolBook"];
    }
    $return = [];
    foreach($data["classes"] as $class){
        
        
        $numberOfPeople = rand(10,15);
        for($i = 0; $i <= $numberOfPeople; $i++){
            $student = []; //name [0], class [1], $gender [2], $subjects [3]
            $gender = "";
            $name = "";
            $gender = rand(1,2) == 1 ? "W" : "M";
            if($gender == "W"){
                $firstname = $data["firstnames"]["women"][rand(0,count($data["firstnames"]["women"])-1)];
            }
            else{
                $firstname = $data["firstnames"]["men"][rand(0,count($data["firstnames"]["men"])-1)];
            }
            $name = $firstname . " " . $data["lastnames"][rand(0,count($data["lastnames"])-1)];
            

            $subjects = [];
            
            foreach($data["subjects"] as $subject){
                $grades = [];
                for($j = 0; $j < rand(0,5); $j++){
                    $grades[] = rand(1,5);
                }
                $subjects[$subject] = $grades; 
            }
            $student[] = $name;
            $student[] = $class;
            $student[] = $gender;
            $student[] = $subjects;
            $return[] = $student;
            
        }
        
    }
    $_SESSION["schoolBook"] = $return;
    return $return;
}
function htmlHead(){
    echo'<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osztálynapló</title>
    <link rel="stylesheet" href="bootstrap.css">
</head>';
}
//Véletlenszerű névgenerálás, kiíratás. Tömbbe kell tenni, utána kiíratás. előbb fiú vagy lány név egy osztályba 0-15 közötti személy 6 osztály van