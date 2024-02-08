<?php
    include_once "include/database.php";

    function loadUsername($token, $db) {
        $userTokenStatement = $db->prepare("select username from `user_sessions` where token = ?");
        $userTokenStatement->bind_param("s", $token);

        $userTokenStatement->execute();
        $userResult = $userTokenStatement->get_result();
        $row = $userResult->fetch_assoc();

        $result = null;
        if ($row) {
            $result = $row["username"];
        }

        $userResult->close();
        $userTokenStatement->close();

        return $result;
    }

    class UserData {
        public $username;
        public $pfp_url;
        public $premium;
        public $admin;
        public $keep_history;
    
        public function __construct($username, $pfp_url, $premium, $admin, $keep_history) {
            $this->username = $username;
            $this->pfp_url = $pfp_url;
            $this->premium = $premium;
            $this->admin = $admin;
            $this->keep_history = $keep_history;
        }
    }
    

    function loadUserInfo($username, $db) {
        $userInfoStatement = $db->prepare("select pfp_url, premium, admin, keep_history from `users` where username = ?");
        $userInfoStatement->bind_param("s", $username);

        $userInfoStatement->execute();
        $userResult = $userInfoStatement->get_result();
        $row = $userResult->fetch_assoc();

        $result = null;
        if ($row) {
            $result = new UserData($username, $row["pfp_url"], !!$row["premium"], !!$row["admin"], !!$row["keep_history"]);
        }

        $userResult->close();
        $userInfoStatement->close();

        return $result;
    }

    function loadUsernameCookieCheck($db) {
        if (isset($_COOKIE["usertoken"])) {
            $username = loadUsername($_COOKIE["usertoken"], $db);
            return $username;
        }
        return null;
    }

    function loadUserPasswordHash($username, $db) {
        $passwordHashStatement = $db->prepare("select password_hash from `users` where username = ?");
        $passwordHashStatement->bind_param("s", $username);

        $passwordHashStatement->execute();
        $passwordHashResult = $passwordHashStatement->get_result();
        $row = $passwordHashResult->fetch_assoc();

        $hash = null;

        if ($row) {
            $hash = $row["password_hash"];
        }

        $passwordHashResult->close();
        $passwordHashStatement->close();

        return $hash;
    }

    function deleteAccount($username, $db) {
        $deleteUserStatement = $db->prepare("delete from users where username = ?");
        $deleteUserStatement->bind_param("s", $username);
        $deleteUserStatement->execute();
        $deleteUserStatement->close();
    }

    function updateUserAdmin($username, $admin, $db) {        
        $updateUserStatement = $db->prepare("update users set admin = ? where username = ?");
        $updateUserStatement->bind_param("is", $admin, $username);
        $updateUserStatement->execute();
        $updateUserStatement->close();
    }

    function updateUserKeepHistory($username, $keep_history, $db) {        
        $updateUserStatement = $db->prepare("update users set keep_history = ? where username = ?");
        $updateUserStatement->bind_param("is", $keep_history, $username);
        $updateUserStatement->execute();
        $updateUserStatement->close();
    }

    function updateUserPremium($username, $premium, $db) {        
        $updateUserStatement = $db->prepare("update users set premium = ? where username = ?");
        $updateUserStatement->bind_param("is", $premium, $username);
        $updateUserStatement->execute();
        $updateUserStatement->close();
    }

    function loadUserInfoMulti($offset, $limit, $db) {
        $userInfoStatement = $db->prepare("select username, pfp_url, premium, admin, keep_history from `users` limit ? offset ?");
        $offset = $offset * $limit;
        $userInfoStatement->bind_param("ii", $limit, $offset);

        $userInfoStatement->execute();
        $userResult = $userInfoStatement->get_result();
        $result = array();
        $row = $userResult->fetch_assoc();

        while ($row) {
            array_push($result, new UserData($row["username"], $row["pfp_url"], !!$row["premium"], !!$row["admin"], !!$row["keep_history"]));
            $row = $userResult->fetch_assoc();
        }

        $userResult->close();
        $userInfoStatement->close();

        return $result;
    }

    $username = loadUsernameCookieCheck($databaseConnection);
    $userinfo = null;
    if ($username) {
        $userinfo = loadUserInfo($username, $databaseConnection);
    }
?>