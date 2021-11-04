<?php
ob_start();
if (isset($_GET)) {
    if ($_GET['operation']) {
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
                            <table class='table text-center table-striped'>
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
                                        <td>" . $order["name"] . "</td>
                                        <td>" . $order["amount"] . "</td>
                                        <td>" . $order["price"] . "₺</td>
                                        <td><a href='' class='text-danger font-weight-bolder'>Sil</a></td>
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
                            <button class='btn bgColorOrange w-100 font-weight-bold mb-3'>Hesabı Al</button>            
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
        }
    } else {
        header('Refresh:1, url=' . SITE_URL);
    }
} else {
    header('Refresh:1, url=' . SITE_URL);
}
