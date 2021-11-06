<?php
try {
    $database = new PDO("mysql:host=localhost;dbname=ordertracking", "root", "");
    $database->exec("SET CHARACTER SET utf8");
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print $e->getMessage();
}