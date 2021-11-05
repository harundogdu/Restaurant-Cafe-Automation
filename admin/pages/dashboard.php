<?php
require_once '../../functions/connection.php';
require_once '../functions/AdminClass.php';
require_once '../../functions/class.php';
$system = new AdminClass();
$statistics = new System();
if (isset($database)) {
    $system->cookieControl($database);
}
?>
<!doctype html>
<html lang="en">
<head>
    <base href="http://localhost/Restaurant-Cafe-Automation/admin/pages/dashboard.php">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container-fluid" style="height: 100vh">
    <div class="row h-100">
        <!--        menu-->
        <div class="col-md-2 bg-light p-0 m-0 shadow">
            <div>
                <a class="btn btn-secondary btn-block rounded-0" href="http://localhost/Restaurant-Cafe-Automation/"
                   target="_blank">Sisteme Git</a>
            </div>
            <div class="py-4 text-center border-bottom bg-info bgColorOrange">
                Hoşgeldiniz! - <?= $system->getUserName($database) ?>
            </div>
            <div class="p-2">
                <div class="mb-1 p-2 border-bottom text-primary font-weight-bold">Masa Yönetimi</div>
                <div class="my-1 p-2 border-bottom text-primary font-weight-bold">Ürün Yönetimi</div>
                <div class="my-1 p-2 border-bottom text-primary font-weight-bold">Kategori Yönetimi</div>
                <div class="my-1 p-2 border-bottom text-primary font-weight-bold">Rapor Yönetimi</div>
                <div class="my-1 p-2 border-bottom text-primary font-weight-bold">Şifre Değiştir</div>
                <div class="my-1 p-2">
                    <a href="dashboard.php?page=logout" id="btnLogOut" class="btn-danger btn font-weight-bold">Çıkış
                        yap</a>
                </div>
            </div>
            <div>
                <table class=" table-striped table-bordered w-100">
                    <thead class="table-warning">
                    <tr>
                        <th class="p-2 text-center" colspan="2">Anlık Durum</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="p-2 border-right">Toplam Sipariş</td>
                        <td class="p-2 font-weight-bold text-center"><?= $statistics->getDBTableCount($database, "orders") ?></td>
                    </tr>
                    <tr>
                        <td class="p-2 border-right">Doluluk Oranı</td>
                        <td class="p-2 font-weight-bold text-center"><?= $statistics->getSolidityRatio($database) ?></td>
                    </tr>
                    <tr>
                        <td class="p-2 border-right">Toplam Masa</td>
                        <td class="p-2 font-weight-bold text-center"><?= $statistics->getDBTableCount($database, "tables") ?></td>
                    </tr>
                    <tr>
                        <td class="p-2 border-right">Toplam Kategori</td>
                        <td class="p-2 font-weight-bold text-center"><?= $statistics->getDBTableCount($database, "categories") ?></td>
                    </tr>
                    <tr>
                        <td class="p-2 border-right">Toplam Ürün</td>
                        <td class="p-2 font-weight-bold text-center"><?= $statistics->getDBTableCount($database, 'products') ?></td>
                    </tr>
                    </tbody>
                </table>
                <div class="text-center mt-2">
                    <span class="p-2 border-right">Tarih</span>
                    <span class="p-2 font-weight-bold text-center"><?= date("d.m.Y") ?></span>
                </div>
            </div>
        </div>
        <!--        content area-->
        <div class="col-md-10 p-3">
            <?php
            switch (@$_GET['page']) {
                case 'logout':
                    $system->logOut();
                    break;
                default:
                    echo 'Welcome to dashboard!';
                    break;
            }
            ?>
        </div>
    </div>
</div>

<!--    scripts tags-->
<script src="../../assets/js/jquery.slim.min.js"></script>
<script src="../../assets/js/popper.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<!--my js-->
</body>
</html>