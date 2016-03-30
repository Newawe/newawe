<?php

session_start();

require __DIR__ . "/../../classes/helpers/hash.php";
require __DIR__ . "/../../classes/helpers/credentials.php";

$DbConf = require __DIR__ . "/../../configs/mysql.php";

// Setup mysql connection
$mysqli = new mysqli($DbConf['host'], $DbConf['user'], $DbConf['password'], $DbConf['database']);

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['country'])) {
    if (!credentials::userExist($_POST['username'],$mysqli)) {
        /* create a prepared statement */
        $stmt = $mysqli->stmt_init();
        if ($stmt->prepare("INSERT INTO nw_users(username, password, email, country) values (?,?,?,?)")) {
            $hash = PasswordHash::hash($_POST['password']);
            $stmt->bind_param("ssss", $_POST['username'], $hash, $_POST['email'], $_POST['country']);
            $stmt->execute();
            $stmt->close();
            $data = ["success"];
        }
    } else {
        $data = ["error" => ["code" => 2, "message" => "Username already taken"]];
    }
} else {
    $data = ["error" => ["code" => 3, "message" => "Please fill out the form completely"]];
}

include "../index.php";