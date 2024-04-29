<?php
    // 身份陣列
    $step_arr = [
        '0' => '填單人',
        '1' => '申請人',
        '2' => '申請人主管',
        '3' => 'PPE發放人',            // 1.依廠區需求可能非一人簽核權限 2.發放人有調整發放數量後簽核權限
        '4' => '業務承辦',
        '5' => '環安主管',
        
        '6' => 'normal',
        '7' => 'PPE窗口',
        '8' => 'PPEpm',
        '9' => '系統管理員',
        '10'=> '轉呈簽核'
    ];

    // 決定表單開啟 $step身份
    if($receive_row["idty"] < 10){       // ** 未交貨後的頭銜
        // 表單交易狀態：0完成/1待收/2退貨/3取消/12發貨
        switch($receive_row['idty']){
            case "0":   // $act = '同意 (Approve)';
            case "1":   // $act = '送出 (Submit)';
                if($receive_row["in_sign"] == $auth_emp_id){
                    if($receive_row["flow"] == "Manager"){
                        $step_index = '2';      // 2.申請人主管
                    }else if( ($receive_row["flow"] == "forward")  ){   
                        $step_index = '10';     // 10.轉呈簽核
                    }
                }else{
                    if($sys_role == 2){
                        $step_index = '7';      // 7.PPE窗口
                    } else if($sys_role == 1){
                        $step_index = '8';      // 8.PPEpm
                    } else if($sys_role == 0){
                        $step_index = '9';      // 9.系統管理員
                    }
                }
                break;
            case "2":   // $act = '退回 (Reject)';
                if($receive_row["created_emp_id"] == $auth_emp_id){
                    $step_index = '0';      // 填單人
                } else if($receive_row["emp_id"] == $auth_emp_id){
                    $step_index = '1';      // 申請人
                }
                break;
            case "3":   // $act = '作廢 (Abort)'; 
            case "4":   // $act = '編輯 (Edit)';  
            case "5":   // $act = '轉呈 (Forwarded)';
            case "6":   // $act = '暫存 (Save)';  
            default:    // $act = '錯誤 (Error)';
                $step_index = '6';      // 6.normal
                break;
        }

    } else if($receive_row["idty"] >= 10){   // ** 已交貨後的頭銜
        // 表單交易狀態：0完成/1待收/2退貨/3取消/12發貨
        switch($receive_row['idty']){
            case "10":                      // $act = '結案 (Close)'; 
                break;
            case "11":                      // $act = '承辦 (Undertake)';
                if($receive_row["fab_id"] == $sys_fab_id){
                    $step_index = '4';      // 業務承辦
                }else if($receive_row["in_sign"] == $auth_emp_id){
                    $step_index = '5';      // 環安主管
                }
                break;
            case "12":                      // $act = '待收發貨 (Awaiting collection)'; 
                if($receive_row['flow'] == 'collect' && in_array($receive_row["fab_id"], $sys_sfab_id)){
                    $step_index = '3';      // ppe發放人
                } 
                break;
            case "13":                      // $act = '交貨 (Delivery)';
                if($receive_row["fab_id"] == $sys_fab_id){
                    $step_index = '4';      // 業務承辦
                }  
                break;
            default:    // $act = '錯誤 (Error)';         
                    $step_index = '6';      // 6.normal
                    return;
        }
    } else {
        if($issue_row["created_emp_id"] == $auth_emp_id){
            $step_index = '0';      // 填單人
        } else if($issue_row["in_user_id"] == $auth_emp_id){
            $step_index = '1';      // 申請人
        }     
    }

    if(!isset($step_index)){
        if(!isset($sys_role) || ($sys_role) >= 2.5){
            $step_index = '6';}         // normal
        if(isset($sys_role)){
            if($sys_role == 2){
                $step_index = '7';}      // PPE窗口
            if($sys_role == 1){
                $step_index = '8';}      // PPEpm
            if($sys_role == 0){
                $step_index = '9';}      // 系統管理員
        }
        if($action == 'create'){
            $step_index = '0';}         // 填單人
    }
    
    // $step套用身份
    $step = $step_arr[$step_index];