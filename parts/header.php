<?php
const SITE_URL = "http://localhost/Restaurant-Cafe-Automation";
ob_start();
date_default_timezone_set('Europe/Istanbul');
?>
<!doctype html>
<html lang="en">
<head>
    <base href="<?=SITE_URL?>/">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Restaurant Sipariş Sistemi</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/jquery.slim.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</head>
<body>