<?php
require_once '../../functions/connection.php';
require_once '../functions/AdminClass.php';
require_once '../../functions/class.php';
$system = new AdminClass();
$statistics = new System();
if (isset($database)) {
    $system->cookieControl($database);
};
ob_start();
?>
<!doctype html>
<html lang="en">

<head>
    <base href="http://localhost/Restaurant-Cafe-Automation/admin/pages/dashboard.php">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
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
                    <a class="btn btn-secondary btn-block rounded-0" href="http://localhost/Restaurant-Cafe-Automation/" target="_blank">Sisteme Git</a>
                </div>
                <div class="py-4 text-center border-bottom bg-info bgColorOrange">
                    Hoşgeldiniz! - <?= $system->getUserName($database) ?>
                </div>
                <div class="p-2">
                    <div class="mb-1 p-2 border-bottom text-primary font-weight-bold">
                        <a href="dashboard.php?page=tables" class="text-primary text-decoration-none">Masa Yönetimi</a>
                    </div>
                    <div class="my-1 p-2 border-bottom text-primary font-weight-bold">
                        <a href="dashboard.php?page=products" class="text-primary text-decoration-none">Ürün Yönetimi</a>
                    </div>
                    <div class="my-1 p-2 border-bottom text-primary font-weight-bold">
                        <a href="dashboard.php?page=categories" class="text-primary text-decoration-none">Kategori Yönetimi</a>
                    </div>

                    <div class="my-1 p-2 border-bottom text-primary font-weight-bold">
                        <a href="dashboard.php?page=reports" class="text-primary text-decoration-none">Rapor Yönetimi</a>
                    </div>
                    <div class="my-1 p-2 border-bottom text-primary font-weight-bold">
                        <a href="dashboard.php?page=password" class="text-primary text-decoration-none">Şifre Değiştir</a>
                    </div>
                    <div class="my-1 p-2">
                        <a href="dashboard.php?page=logout" id="btnLogOut" class="text-danger text-decoration-none font-weight-bold">Çıkış
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
                        /* Logout */
                    case 'logout':
                        $system->logOut();
                        break;
                        /* Tables */
                    case 'tables':
                        $system->tables($database);
                        break;
                    case 'add-tables':
                        $system->addTables($database);
                        break;
                    case 'update-tables':
                        $system->updateTables($database);
                        break;
                    case 'delete-tables':
                        $system->deleteTables($database);
                        break;
                        /* Products */
                    case 'products':
                        $system->products($database);
                        break;
                    case 'add-products':
                        $system->addProducts($database);
                        break;
                    case 'update-products':
                        $system->updateProducts($database);
                        break;
                    case 'delete-products':
                        $system->deleteProducts($database);
                        break;
                        /* Categories */
                    case 'categories':
                        $system->categories($database);
                        break;
                    case 'add-categories':
                        $system->addCategories($database);
                        break;
                    case 'update-categories':
                        $system->updateCategories($database);
                        break;
                    case 'delete-categories':
                        $system->deleteCategories($database);
                        break;
                        /* Password */
                    case 'password':
                        $system->changePassword($database);
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