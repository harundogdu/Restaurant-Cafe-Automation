<?php
require_once '../functions/connection.php';
require_once 'functions/AdminClass.php';
$system = new AdminClass();
if (isset($database)) {
    $system->cookieControl($database,true);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Giriş Ekranı</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container-fluid bg-light">
    <div class="row d-flex justify-content-center align-items-center p-3" style="height: 100vh">
        <?php

        if (isset($_POST['btnLogin'])) {
            $username = htmlspecialchars(strip_tags($_POST['username']));
            $password = htmlspecialchars(strip_tags($_POST['password']));
            $system->loginControl($database, $username, $password);
        } else {
            ?>
            <div class="col-md-4 mx-auto border">
                <h3 class="display-4 text-center mb-3">Hoşgeldiniz!</h3>
                <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                    <div class="form-row my-2">
                        <input type="text" name="username" class="form-control" placeholder="Kullanıcı adını giriniz..."
                               required>
                    </div>
                    <div class="form-row my-2">
                        <input type="password" name="password" class="form-control"
                               placeholder="Kullanıcı parolonızı giriniz..."
                               required>
                    </div>
                    <div class="form-row my-2">
                        <input name="btnLogin" type="submit" class="btn btn-success btn-block" value="Giriş Yap">
                    </div>
                </form>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<script src="../assets/js/jquery.slim.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>