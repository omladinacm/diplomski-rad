<?php
ob_start(); //Turns on output buffering
session_start();

date_default_timezone_set("UTC");

try {
    $con = new PDO("mysql:dbname=diplomski;host=db;port=3306", "diplomski", "secret");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} catch (PDOException $PDOException) {
    echo "Connection failed: " . $PDOException->getMessage();
}