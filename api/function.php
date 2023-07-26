<?php

    function accessDeniedAdmin(){
        session_start();
        if(!isset($_SESSION["AUTH"]) || $_SESSION["AUTH"]["role"] != 0){
            header('location:../view/index.php');
            return;
        }
    }
    // 20221003 fun.1 隱藏或開啟 forAPI
    function changePlan_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT {$table}.* FROM {$table} WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE {$table} SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                // 'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230306 fun.2 將個人業務預設行程存到myTodo forAPI
    function store_mytodo($request){
        $pdo = pdo();
        extract($request);
        
        //確認機制，看是不是有重複no值
        $sql_check = "SELECT mytodo.* 
                      FROM mytodo 
                      WHERE emp_id=? AND table_id=? AND table_name=? AND sys_name=? AND year(mytodo.created_at)=? AND month(mytodo.created_at)=? ";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$emp_id ,$table_id ,$table_name ,$sys_name ,$thisYear ,$thisMonth]);
        $row = $stmt_check -> fetch();
        // 去除 /r/n換行符號
            // $pm_task = str_replace(array("\r\n","\r","\n"), "", $pm_task);
            // $item = str_replace(array("\r\n","\r","\n"), "", $item);

        // 確認no是否已經被註冊掉，用rowCount最快~不要用fetch
        if($stmt_check -> rowCount() >0 ){ 
            // 20230309_1.個人業務預設行程維護 > 設定或更新預設值 > 發現資料已經存在，將不進行update，以免造成資料被覆蓋。
                // echo "<script>alert('No已存在，重新update~')</script>";
                // $sql = "UPDATE mytodo
                //         SET pm_task=? ,item=? ,remark=? ,content=? ,schedule=? ,level=? ,flag=? ,updated_at=now() 
                //         WHERE mytodo.id=? ";
                // $stmt = $pdo->prepare($sql);
                // try {
                //     $stmt->execute([$pm_task ,$item ,$remark ,$content ,$schedule ,$level ,$flag ,$row['id']]);
                //     return "id:".$row['id']." update done";
                    
                // }catch(PDOException $e){
                //     echo $e->getMessage();
                // }
            return "id:".$row['id']." passed~";

        }else{
        //     echo "<script>alert('空的要新增~')</script>";
            $sql = "INSERT INTO mytodo(emp_id ,sys_name ,table_name ,table_id ,pm_task ,item ,remark ,content ,schedule ,level ,link ,flag ,created_at ,updated_at)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW()) ";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$emp_id ,$sys_name ,$table_name ,$table_id ,$pm_task ,$item ,$remark ,$content ,$schedule ,$level ,$link ,$flag]);
                return "store done";
            }catch(PDOException $e){
                echo $e->getMessage();
            }
   
        }
    }
    // 20230306 fun-3.edit_myTodo 編輯myTodo，起始前讀取該內容
    function edit_myTodo($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT mytodo.* ,year(mytodo.created_at) AS p_year ,month(mytodo.created_at) AS p_month
                FROM mytodo 
                WHERE mytodo.id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $edit_myTodo = $stmt->fetch();
            return $edit_myTodo;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230306 fun-4.cheng_flag 編輯myTodo，快速切換上架On/下架Off
    function change_myTodo_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT mytodo.* FROM mytodo WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE mytodo SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                // 'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230306 fun-5.delete_mytodo 將個人myTodo刪除 forAPI
    function delete_mytodo($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM mytodo WHERE emp_id=? AND table_id=? AND table_name='{$table_name}' AND sys_name='{$sys_name}' ";
        $stmt = $pdo -> prepare($sql);
        try {
            $stmt -> execute([$emp_id ,$table_id]);
            return "emp_id:{$emp_id} delete done";

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230308 fun-6.update_mytodo 將個人myTodo更新 forAPI
    function update_mytodo($request){
        $pdo = pdo();
        extract($request);

        if(!empty($content_new)){
            if(!empty($content)){
                // $content .= "\r\n".date("Y-m-d H:i:s")."_".$cname."：".$content_new;
                $content .= "\r\n".date("Y-m-d H:i")."_".$cname."：".$content_new;
            }else{
                $content .= date("Y-m-d H:i")."_".$cname."：".$content_new;
            }
        }  
        $sql = "UPDATE mytodo
                SET content=?, flag=?, updated_at=now() 
                WHERE id=? ";
        $stmt = $pdo -> prepare($sql);
        try {
            $stmt -> execute([$content, $flag, $id]);
            // return "id:{$id} update done";
            return "id: update done";

        }catch(PDOException $e){
            echo $e->getMessage();
        }

    }
    // 20230306 fun-7.load_user_mytodo_list 將個人myTodo_list讀出來
    function load_user_mytodo_list($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT mytodo.* ,year(mytodo.created_at) AS thisYear ,month(mytodo.created_at) AS thisMonth
                FROM mytodo 
                WHERE mytodo.emp_id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$emp_id]);
            $user_mytodo_list = $stmt->fetchAll();
            return $user_mytodo_list;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    // 20230411 fun-8.swap/index tab-3:預覽與查詢
    function review_mytodo($request){
        $pdo = pdo();
        extract($request);
        // 查詢個人 指定年月 
        $sql = "SELECT mytodo.* ,users.sign_code ,users.cname, year(mytodo.created_at) AS p_year, month(mytodo.created_at) AS p_month
                FROM mytodo 
                LEFT JOIN users ON mytodo.emp_id = users.emp_id
                WHERE mytodo.emp_id=? AND ((year(mytodo.created_at)=? AND month(mytodo.created_at)=?)) ";
        $stmt = $pdo -> prepare($sql);
        try {
            $stmt -> execute([$emp_id, $reviewYear, $reviewMonth]);
            $review_mytodo = $stmt -> fetchAll();
            return $review_mytodo;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230503 fun-9.pmView/index tab-3:預覽與查詢
    function review_pmtodo($request){
        $pdo = pdo();
        extract($request);
        // 查詢pm_id 指定年月 
        $sql = "SELECT mt.*, year(mt.created_at) AS p_year, month(mt.created_at) AS p_month , pp.pm_id ,users.user ,users.cname ,users.sign_code ,dp.sign_dept, d1.sign_dept AS up_sign_dept
                FROM `mytodo` mt
                LEFT JOIN pmplan pp ON mt.table_id = pp.id
                LEFT JOIN users ON mt.emp_id = users.emp_id
                LEFT JOIN dept dp ON users.sign_code = dp.sign_code
                LEFT JOIN dept d1 ON dp.up_dep = d1.sign_code
                WHERE pp.pm_id = ? AND ((year(mt.created_at)=? AND month(mt.created_at)=?)) 
                ORDER BY d1.up_dep, users.sign_code, users.emp_id ASC ";

        $stmt = $pdo -> prepare($sql);
        try {
            $stmt -> execute([$pm_id, $reviewYear, $reviewMonth]);
            $review_pmtodo = $stmt -> fetchAll();
            return $review_pmtodo;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // 第一階段：search頁帶入$_REQUEST
    function searchUser($request){
        $pdo = pdo();
        extract($request);
        $key_word = "%".$key_word."%";      // 採用模糊搜尋
        $sql = "SELECT u.* ,dp.sign_dept, d1.sign_dept AS up_sign_dept
                FROM `users` u
                LEFT JOIN dept dp ON u.sign_code = dp.sign_code
                LEFT JOIN dept d1 ON dp.up_dep = d1.sign_code
                WHERE ( u.emp_id LIKE ? OR u.user LIKE ? OR u.cname LIKE ?) AND idty < 2 AND role <> '' 
                ORDER BY u.sign_code ASC, u.emp_id DESC ";
        $stmt = $pdo->prepare($sql);
        try{
            $stmt->execute([$key_word ,$key_word ,$key_word]);
            $searchResult = $stmt->fetchAll();
            return $searchResult;

        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }