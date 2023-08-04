<?php
    require_once("../pdo.php");
    require_once("function.php");

    $swal_json = array();
    switch($_REQUEST["action"]){
        case "new": 
            $swal_json = store_receive($_REQUEST);
            break;
        case "edit": 
            $swal_json = update_receive($_REQUEST);
            break;
        default: 
            echo "bg-light text-success"; 
            break;
    }
?>
<?php include("../template/header.php"); ?>

<div class="col-12">store_receive...</div>

<!-- Jquery -->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>
<!-- mloading JS -->
<script src="../../libs/jquery/jquery.mloading.js"></script>
<!-- mloading CSS -->
<link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
<script>    
    // 畫面載入時開啟loading
    $("body").mLoading({ icon: "../../libs/jquery/Wedges-3s-120px.gif", });    
    // 引入swal_json值
    var swal_json = <?=json_encode($swal_json);?>;
    var url = 'index.php';

    if(swal_json.length != 0){
        $("body").mLoading("hide");
        // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});    // 3秒
        // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});     // 關閉畫面
        
        if(swal_json['action'] == 'success'){
            // location.href = this.url;
            swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{location.href = this.url;});     // 關閉畫面
            
        }else if(swal_json['action'] == 'error'){
            // history.back();
            swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back();});     // 關閉畫面
        }

    }else{
        // All resources finished loading! 關閉mLoading提示
        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
        });
        location.href = this.url;
    }
    

</script>
