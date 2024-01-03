<?php
    require_once("../pdo.php");
    require_once("function.php");

    $swal_json = array();

        switch($_REQUEST["action"]){
            case "create": 
                $swal_json = store_issue($_REQUEST);      
                break;          // 開單
            case "edit": 
                $swal_json = update_issue($_REQUEST);     
                break;          // 編輯
            case "sign":        // 簽核
                if($_REQUEST["idty"] == 5 && empty($_REQUEST["in_sign"])){
                    $swal_json = array(
                        "fun" => "sign_issue",
                        "action" => "error",
                        "content" => '領用申請--轉呈失敗'
                    );
                }else{
                    $swal_json = sign_issue($_REQUEST);  
                }     
                break;
            // case "pr2fab": 
            //     $swal_json = update_pr2fab($_REQUEST);    
            //     break;      // $_POST["pr2fab_submit"] 發貨 => 12

            // case "getIssue": 
            //     $swal_json = update_getIssue($_REQUEST);  
            //     break;      // $_POST["getIssue_submit"] 收貨 => 10

            default:             // 預定失效 
                echo "bg-light text-success";             
                break;
        }

?>
<?php include("../template/header.php"); ?>
<head>
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
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
    <div class="col-12">store_issue...</div>
</body>

<script>    

    var swal_json = <?=json_encode($swal_json);?>;                                          // 引入swal_json值
    var url = 'index.php';

    $(document).ready(function () {

        if(swal_json.length != 0){
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});    // 3秒
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});     // 關閉畫面
            
            if(swal_json['action'] == 'success'){
                // location.href = this.url;
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{location.href = url;});     // 關閉畫面
                
            }else if(swal_json['action'] == 'error'){
                // history.back();
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back();});     // 關閉畫面
            }
    
        }else{

            location.href = url;
        }
        
    })
    

</script>
