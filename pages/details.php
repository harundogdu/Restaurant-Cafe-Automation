<?php
include_once '../parts/header.php';
if ($_GET && $_GET['id'] !== null) {
    include_once '../functions/connection.php';
    include_once '../functions/class.php';
    $tableId = $_GET['id'];
    $system = new System();
    $table = $system->mainQuery($database, "SELECT * FROM tables where id= '{$tableId}'", true);
    if ($table) {
        echo "
    <div class='container-fluid h-100' >
    <div class='row'>
            <a href='" . SITE_URL . "' class='btn btn-dark btn-block py-2 rounded-0'>Geri Dön</a> 
        </div>
         <div class='row h-75'>
            <div class='col-md-3 p-0 m-0 detail-first-div'>
                <div class='text-white w-100 p-5 text-center detail-table-name-container'>
                    <p class='table-details-name'>" . $table["name"] . "</p>
                </div>
                <div id='dataFromShow' class='px-3'></div>
            </div>
            <div class='col-md-7 detail-second-div'>
                <form id='addForm'>
                    <div class='row' style='height: 90vh;'>
                        <div class='col-md-12'>
                            <div class='row h-75'>
                                <div class='col-md-12' id='productList'>
                                    <img src='./assets/images/kategoriSec.png' alt='Kategori Seçiniz'>
                                </div> 
                            </div> 
                            <div class='row h-25 d-flex justify-content-between align-items-center'>
                                 <div class='col-md-6 '>
                                    <input type='hidden' value='" . $tableId . "' name='tableId'>
                                    <button id='btn-addProduct' class='btn bgColorPink btn-block'>Ekle</button>
                                 </div>
                                 <div class='col-md-6'>";
        for ($i = 1; $i < 16; $i++) {
            echo "<label class='btn bgColorPurple m-1'>
                                                <input type='radio' name='amount' value='" . $i . "' /> " . $i . "
                                              </label>";
        }
        echo "   
                                 </div>
                            </div>                            
                        </div>                        
                    </div>                
                </form>
            </div>
            <div id='categories' class='col-md-2 detail-third-div'>";
        $system->getCategories($database);
        echo "            
            </div>
        </div>        
    </div>
    ";
        ?>
        <script>
            $(document).ready(function () {
                // products
                const $tableId = <?= $tableId ?>;
                $('#dataFromShow').load("./functions/operations.php?operation=show&id=" + $tableId);
                //    button category
                $('#categories button').click(function () {
                    const categoryId = $(this).attr("value");
                    $('#productList').load("./functions/operations.php?operation=product&id=" + categoryId);
                })
                // add product
                $('#btn-addProduct').click(function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: "./functions/operations.php?operation=add",
                        data: $(`#addForm`).serialize(),
                        success(response) {
                            if (response.indexOf("200")) {
                                $('#dataFromShow').load("./functions/operations.php?operation=show&id=" + $tableId);
                                $('#addForm').trigger('reset');
                            } else if (response.indexOf("400")) {
                                console.log('Başarısız')
                            } else if (response.indexOf("999")) {
                                alert("Ürün ve adet bilgisini giriniz!");
                            }
                        }
                    })
                })
                //
            })
        </script>
        <?php
        include_once '../parts/footer.php';
    } else {
        header("Location:" . SITE_URL);
    }
} else {
    header("Location:" . SITE_URL);
}