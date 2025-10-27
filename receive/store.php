<?php
    require_once("../pdo.php");
    require_once("function.php");

    $swal_json = array();

    switch($_REQUEST["action"]){
        case "create":      // 開新表單
            $swal_json = store_receive($_REQUEST);
            break;
        case "edit":        // 編輯
            $swal_json = update_receive($_REQUEST);
            break;
        case "sign":        // 簽核
            if($_REQUEST["idty"] == 5 && empty($_REQUEST["in_sign"])){
                $swal_json = array(
                    "fun"     => "sign_receive",
                    "action"  => "error",
                    "content" => '領用申請--轉呈失敗'
                );
            }else{
                $swal_json = sign_receive($_REQUEST);
            }
            break;
        case "assignSign":        // 編輯
            $swal_json = assignSign_receive($_REQUEST);
            break;
        case "return":        // 20240429 退貨
                $swal_json = sign_receive($_REQUEST);
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
    <script src="../../libs/jquery/jquery.mloading.js"></script>                            <!-- mloading JS 1/3 -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                    <!-- mloading CSS 2/3 -->
    <script src="../../libs/jquery/mloading_init.js"></script>                              <!-- mLoading_init.js 3/3 -->
    <script src="../../libs/openUrl/openUrl.js"></script>                                   <!-- 彈出子畫面 -->
    <style>
        body{
            color: white;
        }
    </style>
</head>

<body>
    <div class="col-12">store_receive...</div>
</body>

<script>    
    
    var swal_json = <?=json_encode($swal_json);?>;                                      // 引入swal_json值
    var url = 'index.php';

    $(document).ready(function () {
        if(swal_json.length != 0){
            $("body").mLoading("hide");
            if(swal_json['action'] == 'success'){
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:2000}).then(()=>{closeWindow(true)});      // 關閉畫面+更新
            }else if(swal_json['action'] == 'error'){
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back()});     // 關閉畫面
            }
        }else{
            location.href = url;
        }
    })

</script>
