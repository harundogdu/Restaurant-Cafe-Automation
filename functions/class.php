<?php

class System
{
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

    private function isFullQuery($database, $table)
    {
        $bgColor = 'danger';
        $isFullQuery = $this->mainQuery($database, 'select * from orders where tableId=' . $table["id"]);
        if ($isFullQuery) {
            $bgColor = "success";
        }
        return $bgColor;
    }

    public function getTables($database)
    {
        $tables = $this->mainQuery($database, "select * from tables");
        foreach ($tables as $table) {
            $bgColor = $this->isFullQuery($database, $table);
            echo "              
                    <div  class='col-md-2 my-3'>
                         <a href='pages/details.php?id=" . $table['id'] . "' >
                            <div class='text-white bg-" . $bgColor . " p-5 text-center font-weight-light table-name'>" . $table["name"] . "</div>
                         </a>
                    </div>               
                ";
        }
    }

    public function getDBTableCount($database, $tableName, $type = 1)
    {
        if ($tableName === "orders" && $type !== 2) {
            $query = $database->query("select SUM(amount) as 'totalAmount' from  orders")->fetch(PDO::FETCH_ASSOC);
            return $query["totalAmount"];

        } else {
            if ($type === 2) {
                $query = $database->prepare("select distinct tableId from  $tableName");
            } else {
                $query = $database->prepare("select * from  $tableName");
            }
            $query->execute();
            return $query->rowCount();
        }

    }

    public function getSolidityRatio($database)
    {
        $tableCount = $this->getDBTableCount($database, 'tables');
        $orderCount = $this->getDBTableCount($database, 'orders', 2);

        if ($orderCount !== 0) {
            return round($orderCount * 100 / $tableCount) . '%';
        } else {
            return "0%";
        }
    }

    public function getCategories($database)
    {
        $categories = $this->mainQuery($database, "Select * From categories");
        foreach ($categories as $category) {
            echo "
            <button class='btn bgColorOrange m-1 btn-category' value='" . $category['id'] . "'>" . $category['name'] . "</button>
            ";
        }
    }
}

?>