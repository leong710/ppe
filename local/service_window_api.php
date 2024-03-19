<?php
    $swFile = "../receive/service_window.json";
    
    // 加上安全性檢查，檢查請求的方法是否是 POST、驗證資料等
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $aResult = array();  // 定義結果陣列
        // step-0.function
            function update_service_window($request){
                $sw_json_data = isset($request) ? $request : null;
                if(!empty($sw_json_data)){
                    global $swFile;
                    $swf = fopen($swFile,"w");      //開啟檔案
                    fputs($swf, $sw_json_data);    //初始化sw+寫入
                    fclose($swf);                   //關閉檔案
                    $result = true;
                }else{
                    // echo "<script>alert('參數sw_json_data異常，請重新確認~')</script>";
                    $result = false;
                }
                return $result;
            }

        // step-1.確認基本數值
            $function = !empty($_REQUEST['function']) ? $_REQUEST['function'] : NULL;       // 操作功能
            if (empty($function)) { $aResult['error'] = '未指定function!'; }
            
        // step-2.接收來自前端的資料
            $sw_json = !empty($_REQUEST['sw_json']) ? $_REQUEST['sw_json'] : NULL;          // sw_json

        // step-3.確認基本數值具備且無誤 => 執行function
        if( !isset($aResult['error']) ) {
            $aResult['success'] = 'Run function '.$function.'!';    // 預先定義回傳內容。
            
            switch($function) {
                // fun-1.更新sw清單
                case 'update_service_window':
                    if(empty($sw_json)) {
                        $aResult['error'] = $function.' - sw_json 參數錯誤!';
                    } else {
                        $aResult['result'] = update_service_window($sw_json);
                    }
                    break;
    
                // 無選項反饋sw
                default:
                    $aResult['error'] = 'Not found function '.$function.'!';
                    break;
                }
                if( isset($aResult['error']) ) { unset($aResult['success']); }
        }

        // // api function --- start
        header("Access-Control-Allow-Origin: *");   // 允許跨網域!!
        header("Content-Type: application/json");
        // 將回傳的結果返回給前端
        echo json_encode($aResult , JSON_UNESCAPED_UNICODE );   // 參數：JSON_UNESCAPED_UNICODE 中文不編碼
        // api function --- end

    } else {
        // 如果不是 POST 請求，返回錯誤訊息
        if(!file_exists($swFile)){          //如果檔案不存在
            $sw_json = '{"PPE系統Owner":[{"cname":"鄭羽淳","email":"DORISE.CHENG@INNOLUX.COM","tel_no":"5014-44111"}],"tnESH it":[{"cname":"陳建良","email":"LEONG.CHEN@INNOLUX.COM","tel_no":"5014-42117"}] }';    // 預設值
            $swf = fopen($swFile,"w");      //開啟檔案
            fputs($swf, $sw_json);          //初始化sw
            fclose($swf);                   //關閉檔案
        
        } else{                             //取回當前sw的值
            $swf = fopen($swFile,"r");
            $sw_json = trim(fgets($swf));
            fclose($swf);
        }
        // echo $sw_json;
    }





