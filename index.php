<?php
require_once './functions/connection.php';
require_once './functions/class.php';
include_once './parts/header.php';
$system = new System();
?>
    <!-- Main Container Start   -->
    <div class="container-fluid">
        <!-- Header Start -->
        <div id="header" class="w-100">
            <div class="row">
                <div class="col-md-3 text-center bg-dark text-white p-2 border-right">
                    <span>Toplam Sipariş :</span>
                    <a href="#" class="text-warning"><?= $system->getDBTableCount($database, "orders") ?></a>
                </div>
                <div class="col-md-3 text-center bg-dark text-white p-2 border-right">
                    <span>Doluluk Oranı :</span>
                    <a href="#" class="text-warning"><?= $system->getSolidityRatio($database) ?></a>
                </div>
                <div class="col-md-3 text-center bg-dark text-white p-2 border-right">
                    <span>Toplam Masa :</span>
                    <a href="#" class="text-warning"><?= $system->getDBTableCount($database, "tables") ?></a>
                </div>
                <div class="col-md-3 text-center bg-dark text-white p-2">
                    <span>Tarih :</span>
                    <a href="#" class="text-warning"><?php echo date('d.m.Y') ?></a>
                </div>

            </div>
        </div>
        <!-- Header End -->
        <!--    Content Start-->
        <div id="content">
            <div class="row">
                <?php $system->getTables($database); ?>
            </div>
        </div>
        <!--    Content End-->

    </div>
    <!-- Main Container End   -->
<?php include_once './parts/footer.php' ?>