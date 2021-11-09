<?php

if(isset($_GET['start']) && isset($_GET['end'])){
    include_once '../../functions/connection.php';
    include_once '../../functions/class.php';
    $system = new System();
    include_once '../../parts/header.php';
    $startDate = htmlspecialchars($_GET['start']);
    $endDate = htmlspecialchars($_GET['end']);

    $tableDatas = $system->mainQuery($database, "SELECT products.name as pName, tables.name as tName,SUM(amount) as amount, products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date BETWEEN '$startDate' AND '$endDate' GROUP BY tName ORDER BY date");

    $productDatas = $system->mainQuery($database, "SELECT products.name as pName, tables.name as tName,SUM(amount) as amount,  products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date BETWEEN '$startDate' AND '$endDate' GROUP BY pName ORDER BY date");
    
    echo "<table class='table text-center col-md-8 mx-auto'>
    <thead>
        <tr><td colspan='6'><div class='alert alert-info'>".  $startDate." - ".  $endDate."</div></td></tr>
    </thead>
    <tbody>
        <tr>
            <td colspan='3'>
                <table class='table text-center table-striped'>
                    <thead class='table-dark'>
                        <tr>
                            <th colspan='2'>Masa adet ve Hasılat</th>
                        </tr>
                        <tr class='table-danger text-dark font-weight-bold'>
                            <td>Masa İsmi</td>
                            <td>Hasılat</td>
                        </tr>
                    </thead>
                    <tbody>";
                    if($tableDatas){                                        
                        $totalPrice = 0;
                        foreach($tableDatas as $data){   
                            $total = $data['price'];
                            $totalPrice += $total;                                     
                            echo "<tr>
                                    <td>".$data['tName']."</td>
                                    <td>".number_format( $total,2,',','.')."₺</td>
                                  </tr>";
                        }
                        echo "
                        <tr class='table-danger text-dark font-weight-bold'>
                            <td>Toplam</td>
                            <td>".number_format( $totalPrice,2,',','.')."₺</td>
                        </tr>
                    ";
                    }
                    else{
                        echo "<tr><td colspan='2'><div class='alert alert-warning'>Kayıt Bulunamadı!</div></td></tr>";
                    }
                    echo"</tbody>
                </table>
            </td>                 
            <td colspan='4'>
                <table class='table text-center table-striped'>
                    <thead class='table-dark'>
                        <tr>
                            <th colspan='3'>Ürün adet ve Hasılat</th>
                        </tr>
                        <tr class='table-danger text-dark font-weight-bold'>
                            <td>Ürün İsmi</td>
                            <td>Ürün Adet</td>
                            <td>Hasılat</td>
                        </tr>
                    </thead>
                    <tbody>";
                        if($productDatas){
                            $totalAmount = 0;
                            $totalPrice = 0;
                            foreach($productDatas as $data){                                                  
                                $total = $data['amount'] * $data['price'];
                                $totalPrice += $total;
                                $totalAmount += $data['amount'];
                                echo "<tr>
                                        <td>".$data['pName']."</td>
                                        <td>". $data['amount']."</td>
                                        <td>".number_format( $total,2,',','.')."₺</td>
                                    </tr>";
                            }
                            echo "
                                <tr class='table-danger text-dark font-weight-bold'>
                                    <td>Toplam</td>
                                    <td>".$totalAmount."</td>
                                    <td>".number_format( $totalPrice,2,',','.')."₺</td>
                                </tr>
                            ";
                        }
                        else{
                            echo "<tr><td colspan='3'><div class='alert alert-warning'>Kayıt Bulunamadı!</div></td></tr>";
                        }
                    echo "</tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>";    
   ?>

   <script>
       $(document).ready(function () {
           window.print();
       });
   </script>
   <?php
include_once '../../parts/footer.php';    
}else{
    header("Location:http://localhost/Restaurant-Cafe-Automation/admin/pages/dashboard.php");
}
