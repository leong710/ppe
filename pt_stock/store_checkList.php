<?php
    require_once("../pdo.php");
    require_once("function_checked.php");
    extract($_REQUEST);

    $swal_json = array();

    switch($action){
        // fun-1.儲存checkList
        case "store_ptcheckList": 
            if(empty($fab_id)){                                     // 資料判斷 
                echo "<script>alert('參數錯誤_1 !!! (你的fab_id有誤)');</script>";
                header("refresh:0;url=index.php");                  // 用script導回上一頁。防止崩煃
                return;
            }else{

                $stocksLog_arr = [];                                // 定義app陣列，總total
                $stocks = show_ptstock_forCheck($_REQUEST);           // 調閱器材庫存，依儲存點顯示

                // 鋪設內容
                foreach($stocks as $stock){
                    $check_item = $stock['fab_title'];
                    // 因為remark=textarea會包含html符號，必須用strip_tags移除html標籤
                    $s_remark = strip_tags($stock["stock_remark"]);
                    // 因為remark=textarea會包含換行符號，必須用str_replace置換/n標籤
                    $s_remark = str_replace("\r\n", " ", $s_remark);

                    // 這個工作是將stocks逐筆轉成array，以便進行鋪陳和存檔
                    $stock_arr = [];                                // 定義app陣列，單筆
                    $stock_arr = array(
                        'local_id'      => $stock["local_id"], 
                        'local_title'   => $stock["local_title"], 
                        'fab_id'        => $stock["fab_id"], 
                        'fab_title'     => $stock["fab_title"], 
                        'cate_no'       => $stock["cate_no"],
                        'cate_title'    => $stock["cate_title"], 
                        'cata_SN'       => $stock["cata_SN"], 
                        'pname'         => $stock["pname"], 
                        'stock_id'      => $stock["id"], 
                        'size'          => $stock["size"], 
                        'standard_lv'   => $stock["standard_lv"], 
                        'amount'        => $stock["amount"],
                        'stock_remark'  => $s_remark,
                        'lot_num'       => $stock["lot_num"],
                        'po_no'         => $stock["po_no"],
                        'updated_at'    => $stock["updated_at"],
                        'updated_user'  => $stock["updated_cname"]
                    );
                    $stock_enc = JSON_encode($stock_arr);           // 小陣列要先編碼才能塞進去大陣列forStore儲存
                        // $stock_dec = JSON_decode($stock_enc);            // 小陣列要先編碼才能塞進去大陣列forStore儲存
                        // $stock_str = implode("-," , (array) $stock_dec); // 陣列轉成字串進行儲存到mySQL
                        // array_unshift($stocksLog_arr, $stock_arr);       // 插到前面
                    array_push($stocksLog_arr, $stock_enc);         // 插到後面
                }

                $logs_str = implode("_," , $stocksLog_arr);               // 陣列轉成字串進行儲存到mySQL

                $_REQUEST["stocks_log"] = $logs_str;

                $swal_json = store_ptchecked($_REQUEST);
                // 取得swal內容 // 20240220_增加mapp推播訊息
                if(isset($swal_json["fun"]) && ($swal_json["fun"] == "store_ptchecked") && ($swal_json["action"] == "success" && (isset($ccOmager) && $ccOmager == true))){
                    $myOmager = query_FAB_omager($sign_code);
                }else{
                    $myOmager = array();
                }
            }
            break;  // 停止向下

        // fun-2.更新現有庫存之安全存量
        case "update_stock_stand_lv":        // 編輯

            // step-1.把local中的low_level安全水位叫出來
                $select_local = select_local($_REQUEST);
                if(empty($select_local)){                                       // 查無資料時返回指定頁面
                    echo "<script>alert('參數錯誤_2 !!! (你選的Local有誤)');</script>";
                    header("refresh:0;url=low_level.php");                      // 用script導回上一頁。防止崩煃
                    return;
                }
                $buy_ty = $select_local["buy_ty"];                              // 限購規模
                $low_level = json_decode($select_local["low_level"]);           // 安全存量
                if(is_object($low_level)) { $low_level = (array)$low_level; }   // 物件轉陣列

            // step-2.把local中的low_level安全水位叫出來
                $stock_cata_SN = show_stock_cata_SN($_REQUEST);       // 列出目前stock中對象local裡的cata_SN清單

            // step-3.
                foreach($stock_cata_SN AS $row){
                    $process_local = array(
                        "standard_lv"   => $low_level[$row["cata_SN"]],
                        "local_id"      => $local_id,
                        "cata_SN"       => $row["cata_SN"]
                    );
                    $swal_json = update_stock_stand_lv($process_local);
                }
            break;

        default:            // 預定失效 
            echo "bg-light text-success"; 
            break;
    }

?>
<?php include("../template/header.php"); ?>
<head>
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>    <!-- Jquery -->
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>                         <!-- 引入 SweetAlert -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>                            <!-- mloading JS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                    <!-- mloading CSS -->
    <style>
        body{
            color: white;
        }
    </style>
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                icon: "../../libs/jquery/Wedges-3s-120px.gif",
            }); 
        }
        // All resources finished loading! // 關閉mLoading提示
        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
        });
        mloading();    // 畫面載入時開啟loading
    </script>
</head>

<body>
    <div class="col-12">store_...</div>
</body>

<script>    
    
    var swal_json = <?=json_encode($swal_json);?>;                   // 引入swal_json值
    var myOmager  = <?=json_encode($myOmager)?>;                     // 引入myOmager值
    var url = 'index.php?select_fab_id=<?=$fab_id?>';

    // 2023/12/13 step_1 將訊息推送到TN PPC(mapp)給對的人~ // 20240220_增加mapp推播訊息
    function push_mapp(user_emp_id, mg_msg){
        $.ajax({
            url:'http://10.53.248.167/SendNotify',                      // 20230505 正式修正要去掉port 801
            method:'post',
            async: false,                                               // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                eid : user_emp_id,                                      // 傳送對象
                // eid : '10008048',                                       // 傳送對象 = 測試期間 只發給我
                message : mg_msg                                        // 傳送訊息
            },
            success: function(res){
                // console.log("push_mapp -- success：",res);
                mapp_result_check = true; 
            },
            error: function(res){
                // console.log("push_mapp -- error：",res);
                // ** 受到CORS阻擋，但實際上已完成發送... 所以全部填success
                mapp_result_check = false;
            }
        });
        console.log("mapp_emp_id:", user_emp_id);
        return mapp_result_check;
    }

    $(document).ready(function () {
        
        if(swal_json.length != 0){
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});         // 3秒
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});           // 關閉畫面
            if(swal_json['action'] == 'success'){

                if(swal_json['fun'] == 'store_ptchecked' && myOmager.length !=0 ){
                    var mg_msg = swal_json['msg'];
                    var myOmager_emp_id = String(myOmager['OMAGER']).trim();            // 定義 user_emp_id + 去空白
                    push_mapp(myOmager_emp_id, mg_msg);
                }
                // location.href = this.url;
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{location.href = url;});     // 關閉畫面
                
            }else if(swal_json['action'] == 'error'){
                // history.back();
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back();});          // 關閉畫面
            }
    
        }else{
            location.href = url;
        }
        
    })
    
</script>




