<?php
ob_start();
if (isset($_GET)) {
    if (isset($_GET['operation'])) {
        include_once '../parts/header.php';
        include_once 'connection.php';
        include_once 'class.php';
        $system = new System();
        $operation = htmlspecialchars($_GET['operation']);
        switch ($operation) {
            case 'show':
                $tableId = htmlspecialchars($_GET['id']);
                $orders = $system->mainQuery($database, "SELECT * FROM orders INNER JOIN products ON orders.productId = products.id where tableId='{$tableId}'");
                if ($orders) {
                    $totalAmount = 0;
                    $totalPrice = 0;
                    echo "
                            <table id='table' class='table text-center table-striped'>
                                <thead class='table-dark'>
                                    <tr>
                                        <th>Ürün</th>
                                        <th>Adet</th>
                                        <th>Fiyat</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody class='text-center'>
                        ";
                    foreach ($orders as $order) {
                        $totalAmount += $order['amount'];
                        $totalPrice += $order['price'] * $order['amount'];
                        echo "
                                    <tr>
                                        <td style='vertical-align: middle'>" . $order["name"] . "</td>
                                        <td style='vertical-align: middle'>" . $order["amount"] . "</td>
                                        <td style='vertical-align: middle'>" . $order["price"] . "₺</td>
                                        <td style='vertical-align: middle'>
                                            <button data-cnd='" . $tableId . "' data-id='" . $order['id'] . "' class='btnDelete btn btn-danger font-weight-bolder'>Sil</button>
                                        </td>
                                    </tr>
                                ";
                    }
                    echo "
                                    <tr class='bg-dark text-white'>
                                        <td>Toplam</td>
                                        <td class='border-right border-left border-light'>" . $totalAmount . "</td>
                                        <td colspan='2' class='text-warning font-weight-bold'>" . $totalPrice . "₺</td>
                                    </tr>
                                </tbody>
                            </table>          
                            <button id='btnPay' data-id='" . $tableId . "' class='btn bgColorOrange w-100 font-weight-bold mb-3'>Hesabı Al</button>            
                       ";
                } else {
                    echo "<div class='alert alert-warning text-center mx-3'>Henüz sipariş yok!</div>";
                }
                break;
            case 'product':
                $categoryId = htmlspecialchars($_GET['id']);
                $products = $system->mainQuery($database, "Select * From categories INNER JOIN products ON products.categoryId = categories.id where categories.id=" . $categoryId);
                if ($products) {
                    foreach ($products as $product) {
                        echo " 
                            <label class='btn btn-dark m-1'>
                                <input type='radio' name='productId' value='" . $product['id'] . "' />" . $product['name'] . " 
                            </label>";
                    }
                } else {
                    echo "<div class='alert alert-warning text-center mt-3'>Henüz bu kategoride ürün yok!</div>";
                }
                break;
            case 'add':
                if (isset($_POST['tableId']) && isset($_POST['productId']) && isset($_POST['amount'])) {
                    $tableId = htmlspecialchars($_POST['tableId']);
                    $productId = htmlspecialchars($_POST['productId']);
                    $amount = htmlspecialchars($_POST['amount']);

                    $order = $system->mainQuery($database, "select * from orders INNER JOIN products ON orders.productId = products.id where orders.productId=$productId AND orders.tableId=$tableId", true);
                    if ($order) {
                        $amount += $order["amount"];
                        $addOperation = $system->mainQuery($database, "update orders SET amount=$amount where productId=$productId");
                    } else {
                        $addOperation = $system->mainQuery($database, "insert into orders (tableId,productId,amount) VALUES ($tableId,$productId,$amount)");
                    }
                    if ($addOperation) {
                        echo "200";
                    } else {
                        echo "400";
                    }
                } else {
                    echo "999";
                    return;
                }
                break;
            case 'delete':
                if (isset($_POST)) {
                    $productId = htmlspecialchars($_POST['productId']);
                    $tableId = htmlspecialchars($_POST['tableId']);

                    $query = $system->mainQuery($database, "delete from orders where tableId = $tableId AND productId = $productId");

                } else {
                    header('Location:' . SITE_URL);
                }
                break;
            case 'pay':
                if (isset($_POST)) {
                    $tableId = htmlspecialchars($_POST['tableId']);
                    $date = date("Y-m-d");
                    $orders = $system->mainQuery($database, "select * from orders INNER JOIN tables ON orders.tableId = tables.id where tableId=$tableId");
                    foreach ($orders as $order) {
                        $system->mainQuery($database, "insert into reports (tableId,productId,amount,date) VALUES ('$tableId','" . $order['productId'] . "','" . $order['amount'] . "','$date')");
                    }
                    $query = $system->mainQuery($database, "delete from orders where tableId = $tableId");
                } else {
                    header('Location:' . SITE_URL);
                }
                break;
        }
        ?>
        <script>

            $(document).ready(function () {
                // delete product
                $('.btnDelete').click(function (e) {
                    e.preventDefault();
                    const dataID = $(this).attr('data-id');
                    const tableID = $(this).attr('data-cnd');
                    $.post('functions/operations.php?operation=delete', {
                        productId: dataID,
                        tableId: tableID
                    }, function () {
                        window.location.reload();
                    });
                })

                // pay the bill
                $('#btnPay').click(function (e) {
                    e.preventDefault()
                    const tableId = $(this).attr('data-id');
                    $.post('functions/operations.php?operation=pay', {
                        tableId: tableId
                    }, function () {
                        window.location.href = "<?=SITE_URL?>";
                    });
                })

            })

        </script>
        <?php
        include_once '../parts/footer.php';
    } else {
        header('Location:' . SITE_URL);
    }
} else {
    header('Location:' . SITE_URL);
}
