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
}

?>