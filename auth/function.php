<?php

// // // user-CRUD

    function storeUser($request){
        $pdo = pdo();
        extract($request);

        $user = trim($user);
        // 檢查user是否已經被註冊
            $sql_check = "SELECT * FROM _users WHERE user = ?";
            $stmt_check = $pdo -> prepare($sql_check);
            $stmt_check -> execute([$user]);
            if($stmt_check -> rowCount() >0){     
                // 確認帳號是否已經被註冊掉，用rowCount最快~不要用fetch
                echo "<script>alert('帳號已存在，請重新選擇帳號~')</script>";
                header("refresh:0;url=register.php");
                return;
            }

        if(!isset($role)){
            $role = 2;
        }
        if(!isset($idty)){
            $idty = 1;
        }
        if(!empty($sfab_id)){
            $sfab_id = implode(",",$sfab_id);       //副fab是陣列，要儲存前要轉成字串
        }else{
            $sfab_id = "";
        }

        $sql = "INSERT INTO _users(emp_id, user, cname, fab_id, sfab_id, role, idty, created_at)VALUES(?,?,?,?,?,?,?,now())";
        $stmt = $pdo -> prepare($sql);
        try{
            $stmt -> execute([$emp_id, $user, $cname, $fab_id, $sfab_id, $role, $idty]);
            echo "<script>alert('註冊成功，請重新登入')</script>";
            if(!isset($_SESSION["AUTH"])){
                header("refresh:0;url=login.php");
                return;
            }else{
                header("refresh:0;url=../dashBoard/");
                return;
            }
        }catch(PDOException $e){
            echo $e -> getMessage();
        }

    }

    function editUser($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _users WHERE user = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$user]);
            $user = $stmt->fetch();
            return $user;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function updateUser($request){
        $pdo = pdo();
        extract($request);
        $user = trim($user);

        if(!empty($sfab_id)){
            $sfab_id = implode(",",$sfab_id);       //副pm是陣列，要儲存前要轉成字串
        }else{
            $sfab_id = "";
        }

        $sql = "UPDATE _users SET emp_id=?, user=?, cname=?, fab_id=?, sfab_id=?, role=?, idty=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$emp_id, $user, $cname, $fab_id, $sfab_id, $role, $idty, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    function deleteUser($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }


    function showAllUsers($request){
        $pdo = pdo();
        // extract($request);
        $sql = "SELECT _users.*, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
                FROM _users
                LEFT JOIN _fab ON _users.fab_id = _fab.id ";

        switch($request){
            case "none":
                $sql .= " WHERE _users.role <= 2 AND _users.role <> '' ";
                break;
            case "new":
                $sql .= " WHERE _users.role > 2 ";
                break;
            case "pause":
                $sql .= " WHERE _users.role = '' ";
                break;
            default:
                break;
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _users.role, _users.fab_id, _users.id ASC";
                // CAST(_users.pm_id AS UNSIGNED) = 把文字型態轉成數字
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $users = $stmt->fetchAll();
            return $users;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_site(){
        $pdo = pdo();
        $sql = "SELECT _site.* 
                FROM _site
                WHERE _site.flag = 'On'
                ORDER BY _site.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $sites = $stmt->fetchAll();
            return $sites;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    function show_fab(){
        $pdo = pdo();
        $sql = "SELECT _fab.* 
                FROM _fab
                -- WHERE _fab.flag = 'On'
                ORDER BY _fab.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $fabs = $stmt->fetchAll();
            return $fabs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

?>