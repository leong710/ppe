<?php
    require_once("../pdo.php");
    require_once("function.php");
    extract($_REQUEST);

    // for整合確認：確認supp/comp_no、contact/cname是否已經建立
    function store_something($to_module, $store_item){
        $pdo = pdo();
        // step.1_選擇確認模式
        switch($to_module){
            case "supp":
                $sql_check = "SELECT * FROM _supp WHERE comp_no = ?";
                $check_item = $store_item["comp_no"];
                break;
            case "contact":
                $sql_check = "SELECT * FROM _contact WHERE cname = ?";
                $check_item = $store_item["cname"];
                break;
            default:            // 預定失效 
                return; 
                break;
        }
        $stmt_check = $pdo -> prepare($sql_check);

        // step.2_確認是否已經註冊--action
        try {
            $stmt_check -> execute([$check_item]);

            if($stmt_check -> rowCount() > 0){     
                // 已經有資料--退出
                $result = FALSE;

            }else{
                // 沒有資料--儲存
                switch($to_module){
                    case "supp":
                        $result = store_supp($store_item);
                        break;
                    case "contact":
                        $result = store_contact($store_item);
                        break;
                    default:            // 預定失效 
                        $result = FALSE;
                        break;
                }
            }
            return $result;

        }catch(PDOException $e){
            echo $e->getMessage();
            return FALSE;
        }
    }

    // 回上一頁/本頁網址藥用
        if(isset($_SERVER["HTTP_REFERER"])){
            $up_href = $_SERVER["HTTP_REFERER"];                                    // 回上頁
        }else{
            $up_href = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];     // 回本頁
        }
    // 
    if(!empty($import_excel)){
        $to_module = $import_excel;
    }else{
        $to_module = "";
    }

    $swal_json = array(
        "fun" => "store_".$to_module
    );
    switch($to_module){
        case "supp":
            $swal_json["content"] = "(供應商)";
            break;
        case "contact":
            $swal_json["content"] = "(聯絡人)";
            break;
        default:            // 預定失效 
            $swal_json["content"] = "(-有毛病-)";
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
        // 畫面載入時開啟loading
        mloading();
    </script>
</head>

<body>
    <?php
        $excelTable = (array) json_decode($excelTable);

        foreach($excelTable as $row){ 
            // StdObject轉換成Array
            if(is_object($row)) { $row = (array)$row; }

            $row["flag"]         = "On";
            $row["updated_user"] = $updated_user;

            $result = store_something($to_module, $row);
        }

        if($result){
            $swal_json["action"] = "success";
            $swal_json["content"] .= "_成功";
        }else{
            $swal_json["action"] = "error";
            $swal_json["content"] .= "_失敗";
        }
            
    ?>

    <div class="col-12"><a href="<?php echo $up_href;?>" class="btn btn-sm btn-xs btn-success">回上頁</a>&nbspimport_excel_<?php echo ($to_module == "supp") ? $to_module."(供應商)" : $to_module."(聯絡人)" ?>.....</div>
    
</body>

<script>    
    
    var swal_json = <?=json_encode($swal_json);?>;                                      // 引入swal_json值
    var url = 'index.php';
    var up_url = '<?=$up_href;?>';

    $(document).ready(function () {
        
        if(swal_json.length != 0){
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});     // 3秒
            // swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{window.close();});       // 關閉畫面
            
            if(swal_json['action'] == 'success'){
                // location.href = this.url;
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{location.href = up_url;});     // 關閉畫面
                
            }else if(swal_json['action'] == 'error'){
                // history.back();
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action']).then(()=>{history.back();});     // 關閉畫面
            }
    
        }else{

            location.href = url;
        }
        
    })
    
</script>


