<?php
    require_once("../pdo.php");
    require_once("function.php");

    $swal_json = storeUser($_REQUEST);

?>

<?php include("../template/header.php"); ?>
<head>
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>    <!-- Jquery -->
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>                         <!-- 引入 SweetAlert -->
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
    
    var swal_json = <?=json_encode($swal_json)?>;                                      // 引入swal_json值
    var url = 'index.php';

    $(document).ready(function () {
        if(swal_json.length != 0){
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});         // 3秒
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});           // 關閉畫面
            if(swal_json['action'] == 'success'){
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{location.href = 'login.php'});
            }else if(swal_json['action'] == 'error'){
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back()});     
            }
        }else{
            // location.href = url;
            history.back();
        }
    })

</script>