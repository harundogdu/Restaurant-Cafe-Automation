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
                    foreach ($orders as $order) {
                        echo "<div class='alert alert-info text-center m-2'>" . $order["name"] . " x " . $order['amount'] . "</div>";
                    }
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
                                <input type='radio' name='productId' value='" . $product['id'] . "' />". $product['name']." 
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

                    $addOperation = $system->mainQuery($database, "insert into orders (tableId,productId,amount) VALUES ($tableId,$productId,$amount)");
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
