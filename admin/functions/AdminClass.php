<?php

class AdminClass
{
    const PANEL_URL = "http://localhost/Restaurant-Cafe-Automation/admin/";
    const DASHBOARD_URL = "http://localhost/Restaurant-Cafe-Automation/admin/pages/dashboard.php";

    public function mainQuery($database, $queryFrom, $isSingle = false)
    {
        if (!$isSingle) {
            $query = $database->query($queryFrom, PDO::FETCH_ASSOC);
            if ($query->rowCount()) {
                return $query;
            }
            return 0;
        } else {
            $query = $database->query($queryFrom)->fetch(PDO::FETCH_ASSOC);
            if ($query) {
                return $query;
            } else {
                return 0;
            }
        }
    }

    private function redirectToLink($link)
    {
        header("Location:$link");
    }

    public function loginControl($database, $username, $password)
    {
        $password = md5(sha1(md5($password)));
        $query = $database->prepare("select * from users where username=:username AND password=:password");
        $query->execute(['username' => $username, 'password' => $password]);
        $user = $query->fetch();
        if ($user) {
            setcookie("username", md5(sha1(md5($username))), time() + 60 * 60 * 24);
            $this->redirectToLink(self::DASHBOARD_URL);
        } else {
            $this->redirectToLink(self::PANEL_URL);
        }
    }

    public function cookieControl($database, $state = false)
    {
        if (isset($_COOKIE["username"])) {
            $query = $database->prepare("select * from users where id=1");
            $query->execute();
            $user = $query->fetch();

            if (md5(sha1(md5($user['username']))) === $_COOKIE['username']) {
                if ($state) {
                    $this->redirectToLink(self::DASHBOARD_URL);
                }
            } else {
                setcookie('username', null, -1, '/Restaurant-Cafe-Automation/admin');
                $this->redirectToLink(self::PANEL_URL);
            }
        } else {
            if (!$state) {
                $this->redirectToLink(self::PANEL_URL);
            }
        }
    }

    public function getUserName($database)
    {
        $user = $this->mainQuery($database, "select * from users where id=1", true);
        return $user['username'];
    }

    public function logOut()
    {
        setcookie('username', null, -1, '/Restaurant-Cafe-Automation/admin');
        $this->redirectToLink(self::PANEL_URL);
    }
    /* Masa Yönetimi */
    public function tables($database)
    {
        echo "
            <table class='table table-striped text-center'>                
                <div class='text-right'>
                    <a href='dashboard.php?page=add-tables' class='btn btn-success mb-2'>Masa Ekle</a>
                </div>
                <thead class='table-dark'>
                    <tr>
                        <th>Masalar</th>
                        <th>İşlemler</th>
                    </tr>  
                </thead>
                <tbody>";
        $tables = $this->mainQuery($database, 'select * from tables');

        foreach ($tables as $table) {
            echo "
            <tr>
                <td class='border-right'>" . $table['name'] . "</td>
                <td>
                    <a href='dashboard.php?page=update-tables&id=" . $table['id'] . "' class='btn btn-warning'>Güncelle</a>
                    <a href='dashboard.php?page=delete-tables&id=" . $table['id'] . "' class='btn btn-danger'>Sil</a>
                </td>
            </tr>
            ";
        }

        echo "</tbody>
            </table>
            ";
    }

    public function addTables($database)
    {
        if (isset($_POST['tableName'])) {
            $tableName = $_POST['tableName'];
            if ($this->mainQuery($database, "insert into tables (name) VALUES ('$tableName')")) {
                $this->redirectToLink(self::DASHBOARD_URL . "?page=tables");
            }
        } else {
            echo "
                <h5 class='text-center'>Masa Ekle</h5>
                    <div class='row'>
                        <div class='col-md-4 mx-auto mt-5 p-3 border'>
                            <form action='' method='post'>
                                <div class='form-row my-2'>
                                    <input type='text' name='tableName' placeholder='Masa adı giriniz.' class='form-control'/>
                                </div>
                                <div class='form-row d-flex align-items-center justify-content-end'>
                                    <input type='submit' name='btnUpdate' value='Ekle' class='btn btn-info' />
                                </div>
                            </form>
                        </div>
                    </div>
                ";
        }
    }

    public function updateTables($database)
    {
        if (isset($_POST['tableId'])) {
            $tableId = $_POST['tableId'];
            $tableName = $_POST['tableName'];
            if ($this->mainQuery($database, "update tables SET name='$tableName' where id=$tableId")) {
                $this->redirectToLink(self::DASHBOARD_URL . "?page=tables");
            }
        } else {
            if (isset($_GET['id'])) {
                $tableId = $_GET['id'];
                $table = $this->mainQuery($database, 'select * from tables where id=' . $tableId, true);
                echo "
                <h5 class='text-center'>Masa Güncelle</h5>
                    <div class='row'>
                        <div class='col-md-4 mx-auto mt-5 p-3 border'>
                            <form action='' method='post'>
                                <div class='form-row my-2'>
                                    <input type='text' name='tableName' placeholder='Masa adı giriniz.' class='form-control' value='" . $table['name'] . "' />
                                </div>
                                <div class='form-row d-flex align-items-center justify-content-end'>
                                    <input type='hidden' name='tableId' value='" . $tableId . "' />
                                    <input type='submit' name='btnUpdate' value='Güncelle' class='btn btn-info' />
                                </div>
                            </form>
                        </div>
                    </div>
                ";
            }
        }
    }

    public function deleteTables($database)
    {
        if (isset($_GET['id'])) {
            $tableId = $_GET['id'];
            if (!$this->mainQuery($database, 'select * from orders where tableId=' . $tableId)) {
                $this->mainQuery($database, 'delete from tables where id=' . $tableId);
            }
            $this->redirectToLink(self::DASHBOARD_URL . "?page=tables");
        }
    }
    /* Ürün Yönetimi */
    public function products($database)
    {
        echo "
            <table class='table table-striped text-center'>                
                <div class='text-right'>
                    <a href='dashboard.php?page=add-products' class='btn btn-success mb-2'>Ürün Ekle</a>
                </div>
                <thead class='table-dark'>
                    <tr>
                        <th>Ürün Adı</th>
                        <th>Ürün Fiyatı</th>
                        <th>İşlemler</th>
                    </tr>  
                </thead>
                <tbody>";
        $products = $this->mainQuery($database, 'select * from products');

        foreach ($products as $product) {
            echo "
            <tr>
                <td class='border-right'>" . $product['name'] . "</td>
                <td class='border-right'>" . number_format($product['price'],2,',','.'). "₺</td>
                <td>
                    <a href='dashboard.php?page=update-products&id=" . $product['id'] . "' class='btn btn-warning'>Güncelle</a>
                    <a href='dashboard.php?page=delete-products&id=" . $product['id'] . "' class='btn btn-danger'>Sil</a>
                </td>
            </tr>
            ";
        }

        echo "</tbody>
            </table>
            ";
    }

    public function addProducts($database)
    {
        if (isset($_POST['productName'])) {
            $productName = $_POST['productName'];
            $productPrice = $_POST['productPrice'];
            $categoryId = $_POST['categoryId'];
            if ($this->mainQuery($database, "insert into products (categoryId,name,price) VALUES ($categoryId,'$productName',$productPrice)")) {
                $this->redirectToLink(self::DASHBOARD_URL . "?page=products");
            }
        } else {
            $categories = $this->mainQuery($database, 'select * from categories');
            echo "
                <h5 class='text-center'>Ürün Ekle</h5>
                    <div class='row'>
                        <div class='col-md-4 mx-auto mt-5 p-3 border'>
                            <form action='' method='post'>
                                <div class='form-row my-2'>
                                    <input type='text' name='productName' placeholder='Ürün adı giriniz.' class='form-control'/>
                                </div>
                                <div class='form-row my-2'>
                                    <input type='text' name='productPrice' placeholder='Ürün fiyatı giriniz.' class='form-control'/>
                                </div>
                                <div class='form-row my-2'>
                                    <select class='form-control' name='categoryId'>";
                                    foreach ($categories as $category) {
                                        echo "<option value='" . $category['id'] . "'>" . $category['name'] . "</option>";
                                    }
                                    echo "</select>
                                </div>
                                <div class='form-row d-flex align-items-center justify-content-end'>
                                    <input type='submit' name='btnUpdate' value='Ürün Ekle' class='btn btn-info' />
                                </div>
                            </form>
                        </div>
                    </div>
                ";
        }
    }

    public function updateProducts($database)
    {
        if (isset($_POST['productId'])) {
            $productId = $_POST['productId'];
            $productName = $_POST['productName'];
            $productPrice = $_POST['productPrice'];
            $categoryId = $_POST['categoryId'];
            if ($this->mainQuery($database, "update products SET categoryId=$categoryId,name='$productName',price=$productPrice where id=$productId")) {
                $this->redirectToLink(self::DASHBOARD_URL . "?page=products");
            }
        } else {
            if (isset($_GET['id'])) {
                $productId = $_GET['id'];
                $product = $this->mainQuery($database, 'select * from products where id=' . $productId, true);
                $currentCategory = $this->mainQuery($database, 'select * from categories where id=' . $product['categoryId'], true);
                $categories = $this->mainQuery($database, 'select * from categories');
                echo "
                    <h5 class='text-center'>Ürün Ekle</h5>
                        <div class='row'>
                            <div class='col-md-4 mx-auto mt-5 p-3 border'>
                                <form action='' method='post'>
                                    <div class='form-row my-2'>
                                        <input type='text' name='productName' placeholder='Ürün adı giriniz.' class='form-control' value='" . $product['name'] . "'/>
                                    </div>
                                    <div class='form-row my-2'>
                                        <input type='text' name='productPrice' placeholder='Ürün fiyatı giriniz.' class='form-control' value='" . $product['price'] . "'/>
                                    </div>
                                    <div class='form-row my-2'>
                                        <select class='form-control' name='categoryId'>
                                            <option value='" . $product['categoryId'] . "'>" . $currentCategory['name'] . "</option>
                                        ";
                foreach ($categories as $category) {
                    echo "<option value='" . $category['id'] . "'>" . $category['name'] . "</option>";
                }
                echo "</select>
                                    </div>
                                    <div class='form-row d-flex align-items-center justify-content-end'>
                                        <input type='hidden' name='productId' value='" . $product['id'] . "' />
                                        <input type='submit' name='btnUpdate' value='Ürün Güncelle' class='btn btn-info' />
                                    </div>
                                </form>
                            </div>
                        </div>
                    ";
            }
        }
    }

    public function deleteProducts($database)
    {
        if (isset($_GET['id'])) {
            $productId = $_GET['id'];
            if (!$this->mainQuery($database, 'select * from orders where productId=' . $productId)) {
                $this->mainQuery($database, 'delete from products where id=' . $productId);
            }
            $this->redirectToLink(self::DASHBOARD_URL . "?page=products");
        }
    }
    /* Kategori Yönetimi */
    public function categories($database)
    {
        echo "
            <table class='table table-striped text-center'>                
                <div class='text-right'>
                    <a href='dashboard.php?page=add-categories' class='btn btn-success mb-2'>Kategori Ekle</a>
                </div>
                <thead class='table-dark'>
                    <tr>
                        <th>Kategori Adı</th>
                        <th>Ürün Sayısı</th>
                        <th>İşlemler</th>
                    </tr>  
                </thead>
                <tbody>";
        $categories = $this->mainQuery($database, 'select * from categories');

        foreach ($categories as $category) {
            echo "
            <tr>
                <td class='border-right'>" . $category['name'] . "</td>
                <td class='border-right'>";
            if ($categoryAmount = $this->mainQuery($database, 'SELECT COUNT(id) as "amount" FROM products where categoryId=' . $category['id'] . ' GROUP BY categoryId', true)) {
                echo $categoryAmount['amount'];
            } else {
                echo "0";
            }
            echo "</td>
                <td>
                    <a href='dashboard.php?page=update-categories&id=" . $category['id'] . "' class='btn btn-warning'>Güncelle</a>
                    <a href='dashboard.php?page=delete-categories&id=" . $category['id'] . "' class='btn btn-danger'>Sil</a>
                </td>
            </tr>
            ";
        }

        echo "</tbody>
            </table>
            ";
    }

    public function addCategories($database)
    {
        if (isset($_POST['categoryName'])) {
            $categoryName = $_POST['categoryName'];
            if ($this->mainQuery($database, "insert into categories (name) VALUES ('$categoryName')")) {
                $this->redirectToLink(self::DASHBOARD_URL . "?page=categories");
            }
        } else {
            echo "
                <h5 class='text-center'>Kategori Ekle</h5>
                    <div class='row'>
                        <div class='col-md-4 mx-auto mt-5 p-3 border'>
                            <form action='' method='post'>
                                <div class='form-row my-2'>
                                    <input type='text' name='categoryName' placeholder='Kategori adı giriniz.' class='form-control' required/>
                                </div>
                                <div class='form-row d-flex align-items-center justify-content-end'>
                                    <input type='submit' name='btnUpdate' value='Kategori Ekle' class='btn btn-info' />
                                </div>
                            </form>
                        </div>
                    </div>
                ";
        }
    }

    public function updateCategories($database)
    {
        if (isset($_POST['categoryId'])) {
            $categoryId = $_POST['categoryId'];
            $categoryName = $_POST['categoryName'];
            if ($this->mainQuery($database, "update categories SET name='$categoryName' where id=$categoryId")) {
                $this->redirectToLink(self::DASHBOARD_URL . "?page=categories");
            }
        } else {
            if (isset($_GET['id'])) {
                $categoryId = $_GET['id'];
                $category = $this->mainQuery($database, 'select * from categories where id=' . $categoryId, true);
                echo "
                <h5 class='text-center'>Kategori Güncelle</h5>
                    <div class='row'>
                        <div class='col-md-4 mx-auto mt-5 p-3 border'>
                            <form action='' method='post'>
                                <div class='form-row my-2'>
                                    <input type='text' name='categoryName' placeholder='Kategori adı giriniz.' class='form-control' required value=" . $category['name'] . " />
                                </div>
                                <div class='form-row d-flex align-items-center justify-content-end'>
                                    <input type='hidden' name='categoryId' value='" . $category['id'] . "' />
                                    <input type='submit' name='btnUpdate' value='Kategori Güncelle' class='btn btn-info' />
                                </div>
                            </form>
                        </div>
                    </div>
                ";
            }
        }
    }

    public function deleteCategories($database)
    {
        if (isset($_GET['id'])) {
            $categoryId = $_GET['id'];
            if (!$this->mainQuery($database, 'SELECT COUNT(id) as "amount" FROM products where categoryId=' . $categoryId . ' GROUP BY categoryId')) {
                $this->mainQuery($database, 'delete from categories where id=' . $categoryId);
            }
            $this->redirectToLink(self::DASHBOARD_URL . "?page=categories");
        }
    }
    /* Şifre Değiştir */
    public function changePassword($database)
    {

        if (isset($_POST['btnChangePassword'])) {
            $oldPassword = htmlspecialchars($_POST['oldPassword']);
            $newPassword = htmlspecialchars($_POST['newPassword']);
            $newPasswordAgain = htmlspecialchars($_POST['newPasswordAgain']);

            if (!$this->mainQuery($database, "select * from users where id=1 AND password='" . md5(sha1(md5($oldPassword))) . "'", true)) {
                echo "
                    <div class='alert alert-danger text-center'>
                        Eski şifre hatalı!
                    </div>
                ";
                header("Refresh:1; url=" . self::DASHBOARD_URL . "?page=password");
            } else if ($newPassword !== $newPasswordAgain) {
                echo "
                <div class='alert alert-danger text-center'>
                    Yeni şifreler eşleşmiyor!
                </div>
            ";
                header("Refresh:1; url=" . self::DASHBOARD_URL . "?page=password");
            } else {
                if ($this->mainQuery($database, "update users SET password='" . md5(sha1(md5($newPassword))) . "' where id=1")) {
                    echo "
                        <div class='alert alert-success text-center'>
                            Şifre başarıyla değiştirildi!
                        </div>
                    ";
                }
                $this->redirectToLink(self::DASHBOARD_URL);
            }
        } else {
            echo "
                <div class='col-md-6 mx-auto'>
                    <div class='border p-3'>
                        <h3>Şifre Değiştir</h3>
                        <form action='' method='post'>
                            <div class='form-row'>
                                <input type='password' name='oldPassword' placeholder='Eski şifrenizi giriniz... ' class='form-control' required />
                            </div>
                            <div class='form-row'>
                                <input type='password' name='newPassword' placeholder='Yeni şifrenizi giriniz... ' class='form-control my-2' required />
                                <input type='password' name='newPasswordAgain' placeholder='Yeni şifrenizi tekrar giriniz... ' class='form-control' required />
                            </div> 
                            <div class='form-row'>
                                <input name='btnChangePassword' type='submit' class='btn btn-success mt-2 ml-auto' value='Parola Değiştir' />
                            </div>                       
                        </form>
                    </div>
                </div>
            ";
        }
    }
    /* Rapor Yönetimi */
    public function reports($database)
    {
        if ($_GET) {    
            $time = @$_GET['time'];
            switch ($time) {
                case "today":
                    $tableDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName, reports.amount as amount, SUM(products.price * reports.amount) as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date = CURDATE() GROUP BY tName ORDER BY date');

                    $productDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName,SUM(amount) as amount,  products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date = CURDATE() GROUP BY pName ORDER BY date');
                    break;
                case "yesterday":
                    $tableDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName,SUM(amount) as amount, products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date =  DATE_SUB(CURDATE(), INTERVAL 1 DAY) GROUP BY tName ORDER BY date');

                    $productDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName,SUM(amount) as amount,  products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date =  DATE_SUB(CURDATE(), INTERVAL 1 DAY) GROUP BY pName ORDER BY date');                    
                    break;
                case 'week':
                    $tableDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName,SUM(amount) as amount, products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE  YEARWEEK(date) = YEARWEEK(CURRENT_DATE) GROUP BY tName ORDER BY date');

                    $productDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName,SUM(amount) as amount,  products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE  YEARWEEK(date) = YEARWEEK(CURRENT_DATE) GROUP BY pName ORDER BY date');   
                    break;
                case 'month':
                    $tableDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName,SUM(amount) as amount, products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) GROUP BY tName ORDER BY date');

                    $productDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName,SUM(amount) as amount,  products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date  >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) GROUP BY pName ORDER BY date'); 
                    break;
                case 'selected':
                    if(isset($_POST['start'])){
                    $start = htmlspecialchars($_POST['start']);
                    $end = htmlspecialchars($_POST['end']);

                    $tableDatas = $this->mainQuery($database, "SELECT products.name as pName, tables.name as tName,SUM(amount) as amount, products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date BETWEEN '$start' AND '$end' GROUP BY tName ORDER BY date");

                    $productDatas = $this->mainQuery($database, "SELECT products.name as pName, tables.name as tName,SUM(amount) as amount,  products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date BETWEEN '$start' AND '$end' GROUP BY pName ORDER BY date");
                    }
                    break;
                case 'all':
                default:
                    $tableDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName,SUM(amount) as amount, products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) GROUP BY tName ORDER BY date');
                                
                    $productDatas = $this->mainQuery($database, 'SELECT products.name as pName, tables.name as tName,SUM(amount) as amount,  products.price as price FROM products INNER JOIN reports ON products.id =  reports.productId INNER JOIN tables ON reports.tableId = tables.id  WHERE reports.date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) GROUP BY pName ORDER BY date'); 
                    break;                   
            }
                echo "
                <table class='table text-center'>
                    <thead class='table-dark'>
                        <tr>
                            <th style='vertical-align:middle'><a class='text-white' href='dashboard.php?page=reports&time=today'>Bugün</a></th>
                            <th style='vertical-align:middle'><a class='text-white' href='dashboard.php?page=reports&time=yesterday'>Dün</a></th>
                            <th style='vertical-align:middle'><a class='text-white' href='dashboard.php?page=reports&time=week'>Bu Hafta</a></th>
                            <th style='vertical-align:middle'><a class='text-white' href='dashboard.php?page=reports&time=month'>Bu Ay</a></th>
                            <th style='vertical-align:middle'><a class='text-white' href='dashboard.php?page=reports&time=all'>Tüm Zamanlar</a></th>
                            <th class='w-25 border-left'>
                                <form action='".self::DASHBOARD_URL."?page=reports&time=selected' method='POST'>
                                    <div class='form-row'>
                                        <input type='date' name='start' class='form-control' required />
                                        <input type='date' name='end' class='form-control my-1' required />
                                    </div>
                                    <div class='form-row d-flex justify-content-end'>
                                        <input type='submit' class='btn btn-warning' value='Göster' />
                                        ";
                                        if(isset($_GET['time']) && $_GET['time'] === 'selected'){
                                            ?>
                                                <button type='button' class='btn btn-primary ml-2' onclick="pageWindow('<?=self::PANEL_URL?>functions/printReports.php?start=<?=$_POST['start']?>&end=<?=$_POST['end']?>','Raportlar',900,600,true)"> Yazdır </button>
                                            <?php
                                        }
                                    echo"</div>
                                </form>
                            </th>  
                        </tr>
                    </thead>
                    <tbody>";
                        if(isset($_GET['time']) && $_GET['time'] === 'selected'){
                            echo "<tr><td colspan='6'><div class='alert alert-info'>".$_POST['start']." - ". $_POST['end']." Arası Günler Gösteriliyor.</div></td></tr>";
                        }
                        echo"<tr>
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
                                                    <td>".number_format($total,2,',','.')."₺</td>
                                                  </tr>";
                                        }
                                        echo "
                                        <tr class='table-danger text-dark font-weight-bold'>
                                            <td>Toplam</td>
                                            <td>".number_format($totalPrice,2,',','.')."₺</td>
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
                                                        <td>".number_format($total,2,',','.')."₺</td>
                                                    </tr>";
                                            }
                                            echo "
                                                <tr class='table-danger text-dark font-weight-bold'>
                                                    <td>Toplam</td>
                                                    <td>".$totalAmount."</td>
                                                    <td>".number_format($totalPrice,2,',','.')."₺</td>
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
                </table>
                    ";            
        }
    }
}
?>

<script>        
    let popupWindow = null;
    function pageWindow(url,winName,width,heigth,scroll){
        let LeftPosition = screen.width ? (screen.width - width) / 2 : 0;
        let TopPosition = screen.height ? (screen.height - heigth) / 2 : 0;
        let settings = 'heigth='+heigth+',width='+width+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable';
        popupWindow = window.open(url,winName,settings);
    }        
</script>