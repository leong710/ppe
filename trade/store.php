<?php
    require_once("../pdo.php");
    require_once("function.php");

    $swal_json = array();
    switch($_REQUEST["action"]){
        case "create":      // 開新表單
            $swal_json = store_trade($_REQUEST);
            break;
        case "edit":        // 編輯
            $swal_json = update_trade($_REQUEST);
            break;
        case "sign":        // 簽核
            // $swal_json = sign_trade($_REQUEST);
            $swal_json = deBug($_REQUEST);
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
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                icon: "../../libs/jquery/Wedges-3s-120px.gif",
            }); 
        }
        mloading();    // 畫面載入時開啟loading
    </script>
</head>

<body>
    <div class="col-12">store_trade...</div>
    
</body>

<script>    
    // All resources finished loading! // 關閉mLoading提示
    window.addEventListener("load", function(event) {
        $("body").mLoading("hide");
    });
    
    var swal_json = <?=json_encode($swal_json);?>;                                      // 引入swal_json值
    var url = 'index.php';

    // $(document).ready(function () {
        
    //     if(swal_json.length != 0){
    //         // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});     // 3秒
    //         // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});       // 關閉畫面
            
    //         if(swal_json['action'] == 'success'){
    //             // location.href = this.url;
    //             swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{location.href = this.url;});     // 關閉畫面
                
    //         }else if(swal_json['action'] == 'error'){
    //             // history.back();
    //             swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back();});     // 關閉畫面
    //         }
    
    //     }else{

    //         location.href = this.url;
    //     }
        
    // })
    
</script>
        






