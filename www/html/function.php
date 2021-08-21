<?php

function e(string $str, string $charset = 'UTF-8'): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, $charset);
}

function getDatabase(){
    $user = "root";
    $password = "secret";

    //PDOオブジェクト生成
    $pdo = new PDO("mysql:host=db; dbname=keiziban; charset=utf8", $user, $password);
    
    return $pdo;
}