<?php
try {
    $database = new PDO("mysql:host=localhost;dbname=ordertracking", "root", "");
    $database->exec("SET CHARACTER SET utf8");
} catch (PDOException $e) {
    print $e->getMessage();
}