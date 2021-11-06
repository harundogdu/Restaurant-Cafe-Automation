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
                <td class='border-right'>" . $product['price'] . "₺</td>
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
}
