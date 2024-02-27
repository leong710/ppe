<?php
    require_once("../pdo.php");
    // require_once("function.php");
    require_once("function_pt_local.php");
    extract($_REQUEST);
   
    $swal_json = array();

    switch($action){
        // fun-1.更新Local安全存量設定
        case "store_lowLevel":      
            if(empty($select_local_id) || empty($low_level)){                          // 資料判斷 
                echo "<script>alert('參數錯誤_1 !!! (你沒有選Local或填數量)');</script>";
                header("refresh:0;url=low_level.php?select_fab_id={$select_fab_id}&select_local_id={$select_local_id}");                          // 用script導回上一頁。防止崩煃
                return;
            }else{
                $swal_json = store_lowLevel($_REQUEST);
            }

            if(!isset($update_stock_stand_lv_option) || ($update_stock_stand_lv_option != "on")){
                // 停止向下
                break;
            }   // if = on 就繼續往下!

        // fun-2.更新現有庫存之安全存量
        case "update_stock_stand_lv":        // 編輯

            // step-1.把local中的low_level安全水位叫出來
                $select_local = select_local($_REQUEST);
                if(empty($select_local)){                                       // 查無資料時返回指定頁面
                    echo "<script>alert('參數錯誤_2 !!! (你選的Local有誤)');</script>";
                    header("refresh:0;url=low_level.php?select_fab_id={$select_fab_id}&select_local_id={$select_local_id}");                      // 用script導回上一頁。防止崩煃
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
                        "select_local_id"      => $select_local_id,
                        "cata_SN"       => $row["cata_SN"]
                    );
                    $swal_json = update_stock_stand_lv($process_local);
                }
            break;

        default:            // 預定失效 
            echo "bg-light text-success"; 
            break;
    }


    // 複製本頁網址藥用
    $up_href = "low_level.php"; // 回本頁

    if(!empty($select_fab_id)){
        if(stripos($up_href, "?")){
            $up_href .= "&";
        }else{
            $up_href .= "?";
        }
        $up_href .= "select_fab_id=".$select_fab_id;
    }
    if(!empty($select_local_id)){
        if(stripos($up_href, "?")){
            $up_href .= "&";
        }else{
            $up_href .= "?";
        }
        $up_href .= "select_local_id=".$select_local_id;
    }

    echo $up_href;

?>
<?php include("../template/header.php"); ?>
<head>
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>    <!-- Jquery -->
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>                         <!-- 引入 SweetAlert -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>                            <!-- mloading JS 1/3 -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                    <!-- mloading CSS 2/3 -->
    <script src="../../libs/jquery/mloading_init.js"></script>                              <!-- mLoading_init.js 3/3 -->
    <style>
        body{
            color: white;
        }
    </style>
</head>

<body>
    <div class="col-12">store_...</div>
</body>

<script>    
    
    var swal_json = <?=json_encode($swal_json);?>;                                      // 引入swal_json值
    var url = '<?=$up_href?>';

    $(document).ready(function () {
        
        if(swal_json.length != 0){
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});         // 3秒
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});           // 關閉畫面
            if(swal_json['action'] == 'success'){
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




