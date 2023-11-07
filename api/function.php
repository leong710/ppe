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


    // // 第一階段：search頁帶入$_REQUEST
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
    // 第一階段：search頁帶入$_REQUEST  // 模糊搜尋
    function search($request){
        $pdo = pdo_hrdb();
        extract($request);
        $search = "%".$search."%";      // 採用模糊搜尋 工號、姓名、NT_ID
        $sql = "SELECT u.* 
                FROM staff u
                WHERE ( u.emp_id LIKE ? OR u.[user] LIKE ? OR u.cname LIKE ? )
                ORDER BY u.emp_id DESC ";
        $stmt = $pdo->prepare($sql);
        try{
            $stmt->execute([$search, $search, $search]);
            $search = $stmt->fetchAll();
            return $search;
        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }
    // 第二階段：show頁帶入$_REQUEST    // 進入查看1：由staff帶出可檢視資料
    function showStaff($request){
        $pdo = pdo_hrdb();
        extract($request);
        // 未含： emg_contect、emg_contect_number、emg_contect_title、home_phone 這四個欄位，改由HR API供應
        // $sql = "SELECT st.emp_id, st.cname, st.emp_scope, st.emp_sub_scope, st.emp_group, st.emp_type, st.dept_no, st.emp_dept, st.omager, st.updated_at,  
        $sql = "SELECT u.*,  
                    s2.emp_id AS s2_emp_id, s2.cname AS s2_cname, s2.emp_scope AS s2_emp_scope, s2.dept_no AS s2_dept_no, s2.emp_dept AS s2_emp_dept, s2.emp_sub_scope AS s2_emp_sub_scope
                FROM staff u
                LEFT JOIN staff s2 ON u.omager = s2.emp_id
                WHERE u.emp_id = ? ";
        $stmt = $pdo->prepare($sql);
        try{
            $stmt->execute([$emp_id]);
            $search = $stmt->fetch();
            return $search;
        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }