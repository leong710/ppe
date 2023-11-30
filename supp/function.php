<?php

// supp
    // supp項目--新增 230703
    function store_supp($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _supp(scname, sname, supp_remark, inv_title, comp_no, _address, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$scname, $sname, $supp_remark, $inv_title, $comp_no, $_address, $flag, $updated_user]);
            $result = TRUE;
        }catch(PDOException $e){
            echo $e->getMessage();
            $result = FALSE;
        }
        return $result;
    }
    // from edit_supp.php 依ID找出要修改的supp內容
    function edit_supp($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _supp WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $supp = $stmt->fetch();
            return $supp;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from edit_supp.php call update_supp 修改完成的edit_supp 進行Update
    function update_supp($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE _supp
                SET scname=?, sname=?, supp_remark=?, inv_title=?, comp_no=?, _address=?, flag=?, updated_user=?, updated_at=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$scname, $sname, $supp_remark, $inv_title, $comp_no, $_address, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    function delete_supp($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _supp WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 隱藏或開啟
    function changesupp_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _supp.* FROM _supp WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _supp SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_supp(){
        $pdo = pdo();
        $sql = "SELECT _supp.*
                FROM _supp
                ORDER BY _supp.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $supps = $stmt->fetchAll();
            return $supps;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //分頁工具 == > 暫時沒用到
    function page_div_supp($start, $per){
        $pdo = pdo();
        $sql = "SELECT _supp.*
                FROM _supp
                ORDER BY _supp.id ASC";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            $stmt->execute();
            $rs = $stmt->fetchAll();
            return $rs;
        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }
// supp

// _contact
    // contact項目--新增 230703
    function store_contact($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _contact(cname, phone, email, fax, comp_no, contact_remark, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$cname, $phone, $email, $fax, $comp_no, $contact_remark, $flag, $updated_user]);
            $result = TRUE;
        }catch(PDOException $e){
            echo $e->getMessage();
            $result = FALSE;
        }
        return $result;
    }
    // from edit_contact.php 依ID找出要修改的contact內容
    function edit_contact($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _contact WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $contact = $stmt->fetch();
            return $contact;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from edit_contact.php  call update_contact 修改完成的edit_contact 進行Update
    function update_contact($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE _contact
                SET cname=?, phone=?, email=?, fax=?, comp_no=?, contact_remark=?, flag=?, updated_user=?, updated_at=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$cname, $phone, $email, $fax, $comp_no, $contact_remark, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function delete_contact($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _contact WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 隱藏或開啟
    function changecontact_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _contact.* FROM _contact WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _contact SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_contact(){
        $pdo = pdo();
        $sql = "SELECT _contact.*, _supp.scname, _supp.flag AS supp_flag
                FROM _contact
                LEFT JOIN _supp ON _contact.comp_no = _supp.comp_no
                ORDER BY _contact.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $contacts = $stmt->fetchAll();
            return $contacts;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //分頁工具
    function page_div_contact($start, $per){
        $pdo = pdo();
        $sql = "SELECT _contact.*
                FROM _contact
                ORDER BY _contact.id ASC ";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            $stmt->execute();
            $rs = $stmt->fetchAll();
            return $rs;
        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }
// _contact

