<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

        // 刪除表單
        if(isset($_POST["delete_receive"])){
            $check_delete_result = delete_receive($_REQUEST);
            if($check_delete_result){
                echo "<script>alert('receive領用申請單 -- 已刪除');</script>";
                header("refresh:0;url=index.php");
                exit;		// 請注意
            }else{
                echo "<script>alert('receive領用申請單 -- 刪除失敗!!');</script>";
            }
        }
        // 更新log
        if(isset($_POST["delete_log"])){
            updateLogs($_REQUEST);
        }

    // 決定表單開啟方式
    if(isset($_REQUEST["action"])){
        $action = $_REQUEST["action"];              // 有action就帶action
    }else{
        $action = 'create';                         // 沒有action就新開單
        
    }

    if(isset($_REQUEST["uuid"])){
        $receive_row = show_receive($_REQUEST);
        if(empty($receive_row)){
            echo "<script>alert('uuid-error：{$_REQUEST["uuid"]}')</script>";
            header("refresh:0;url=index.php");
            exit;
        }
        // logs紀錄鋪設前處理 
        $logs_dec = json_decode($receive_row["logs"]);
        $logs_arr = (array) $logs_dec;

    }else{
        $receive_row = array( "uuid" => "" );       // 預設receive_row[uuid]=空array
        $logs_arr = [];                             // 預設logs_arr=空array
        $action = 'create';                         // 因為沒有uuid，列為新開單，防止action outOfspc
    }

    $allLocals = show_allLocal();                   // 所有儲存站點
    $catalogs = show_catalogs();                    // 器材=All
    $categories = show_categories();                // 分類
    $sum_categorys = show_sum_category();           // 統計分類與數量
    
    // function select_step(){
        // 身份陣列
        $step_arr = [
            '0' => '填單人',
            '1' => '申請人',
            '2' => '申請人主管',
            '3' => '發放人',            // 1.依廠區需求可能非一人簽核權限 2.發放人有調整發放數量後簽核權限
            '4' => '環安業務',
            '5' => '環安主管',

            '6' => 'noBody',
            '7' => 'ppe site user',
            '8' => 'ppe pm',
            '9' => '系統管理員',
        ];

        // 決定表單開啟 $step身份
        if(isset($receive_row["created_emp_id"]) && ($receive_row["created_emp_id"] == $_SESSION["AUTH"]["emp_id"])){
            $step_index = '0';}             // 填單人
        if(isset($receive_row["emp_id"]) && ($receive_row["emp_id"] == $_SESSION["AUTH"]["emp_id"])){
            $step_index = '1';}             // 申請人
        if(isset($receive_row["omager"]) && ($receive_row["omager"] == $_SESSION["AUTH"]["emp_id"])){
            $step_index = '2';}             // 申請人主管
        
        if(empty($step_index)){
            if(!isset($_SESSION[$sys_id]["role"]) ||($_SESSION[$sys_id]["role"]) == 3){
                $step_index = '6';}         // noBody
            if(isset($_SESSION[$sys_id]["role"]) && ($_SESSION[$sys_id]["role"]) == 2){
                $step_index = '7';}         // ppe site user
            if(isset($_SESSION[$sys_id]["role"]) && ($_SESSION[$sys_id]["role"]) == 1){
                $step_index = '8';}         // ppe pm
            if(isset($_SESSION[$sys_id]["role"]) && ($_SESSION[$sys_id]["role"]) == 0){
                $step_index = '9';}         // 系統管理員
            if($action = 'create'){
                $step_index = '0';}         // 填單人
        }
        
        // $step套用身份
        $step = $step_arr[$step_index];
    // }
        $up_href = $_SERVER["HTTP_REFERER"];    // 回上頁

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <!-- dataTable參照 https://ithelp.ithome.com.tw/articles/10230169 -->
        <!-- data table CSS+JS -->
        <link rel="stylesheet" type="text/css" href="../../libs/dataTables/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="../../libs/dataTables/jquery.dataTables.js"></script>
    <!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <style>
        .unblock{
            display: none;
            /* transition: 3s; */
        }
        /*眼睛*/
        #checkEye , #omager_badge {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
        }
        .tag{
            display: inline-block;
            /* 粉紅 */
            /* background-color: #fa0e7e; */
            /* 粉藍 */
            background-color: #0e7efa;
            font: 14px;
            color: white;
            border-radius: 5px;
            padding: 0px 3px 0px 7px;
            margin-right: 5px;
            margin-bottom:5px;
            /* 粉紅 */
            /* box-shadow: 0 5px 15px -2px rgba(250 , 14 , 126 , .7); */
            /* 粉藍 */
            box-shadow: 0 5px 15px -2px rgba(3 , 65 , 134 , .7);
        }
        .tag .remove {
            margin: 0 7px 3px;
            display: inline-block;
            cursor: pointer;
        }
        #catalog_list img {
            max-width: 100px;
            /* max-height: 100px; */
            max-height: 100px;
        }
        .badge {
            /* 標籤增加陰影辨識度 */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .cata_info_btn , .add_btn{
            /* 將圖示的背景色設置為透明並添加陰影 */
            background-color: transparent; 
            text-shadow: 0px 0px 1px #fff;
            color: blue;
            /* 將圖示的背景色設置為按鈕的背景色 */
            /* background-color: inherit; */
        }
        .cata_info_btn:hover , .add_btn:hover{
            /* color: red; */
            transition: .5s;
            font-weight: bold;
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
        }
        tr > th {
            color: blue;
            text-align: center;
            vertical-align: top; 
            word-break: break-all; 
            background-color: white;
            font-size: 16px;
        }
        tr > td {
            vertical-align: middle; 
        }
        #emp_id{    
            margin-bottom: 0px;
            text-align: center;
        }
        .autoinput {
            /* background-color: greenyellow; */
            border: 2px solid greenyellow;
            padding: 5px;
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
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-11 border rounded px-3 py-4" style="background-color: #D4D4D4;">
                <!-- 表頭1 -->
                <div class="row px-2">
                    <div class="col-12 col-md-6 py-0">
                        <h3><i class="fa-solid fa-3"></i>&nbsp<b>領用申請</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <!-- <a href="index.php" class="btn btn-success"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回總表</a> -->
                        <a href="<?php echo $up_href;?>" class="btn btn-secondary" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i> 返回</a>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        領用單號：<?php echo ($action == 'create') ? "(尚未給號)": "aid_".$receive_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$receive_row['created_at']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $_SESSION["AUTH"]["emp_id"]." / ".$_SESSION["AUTH"]["cname"] : $receive_row["created_emp_id"]." / ".$receive_row["created_cname"] ;?>
                        </br>表單身分：<?php echo $step;?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <?php if(($_SESSION[$sys_id]["role"] <= 1 ) && (isset($receive_row['idty']) && $receive_row['idty'] != 0)){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="uuid" value="<?php echo $receive_row["uuid"];?>">
                                <input type="submit" name="delete_receive" value="刪除" title="刪除申請單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
                            </form>
                        <?php }?>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 p-0">
                    <!-- 分頁標籤 -->
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" 
                                aria-controls="nav-home" aria-selected="true">1.選取器材用品</button>
                            <button class="nav-link" id="nav-shopping_cart-tab" data-bs-toggle="tab" data-bs-target="#nav-shopping_cart" type="button" role="tab" 
                                aria-controls="nav-shopping_cart" aria-selected="false">2.購物車&nbsp<span id="shopping_count" class="badge rounded-pill bg-danger"></span></button>
                            <button class="nav-link disabled" id="nav-review-tab" data-bs-toggle="tab" data-bs-target="#nav-review" type="button" role="tab" 
                                aria-controls="nav-review" aria-selected="false">3.申請單成立</button>
                        </div>
                    </nav>
                    <!-- 內頁 -->
                    <!-- <form action="debug.php" method="post"> -->
                    <form action="store.php" method="post">
                        <div class="tab-content rounded bg-light" id="nav-tabContent">
                            <!-- 1.商品目錄 -->
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="col-12 px-4">
                                    <table id="catalog_list" class="catalog_list table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="unblock">cate_no</th>
                                                <th>PIC</th>
                                                <th style="width: 30%;">名稱&nbsp<i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                                <th>分類</th>
                                                <th>尺寸</th>
                                                <th>需求</th>
                                                <th><i class="fa-solid fa-cart-plus"></i>&nbsp購物車</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($catalogs as $catalog){ ?>
                                                <tr>
                                                    <td class="unblock"><?php echo $catalog["cate_no"];?></td>
                                                    <td><img src="../catalog/images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></td>
                                                    <td style="text-align: left;">
                                                        <button type="button" id="cata_info_<?php echo $catalog['SN'];?>" value="<?php echo $catalog['SN'];?>" data-bs-toggle="modal" data-bs-target="#cata_info" 
                                                            class="cata_info_btn" onclick="info_module('catalog',this.value);"><h5><b><?php echo $catalog["pname"];?></b></h5></button>
                                                        <?php 
                                                            echo $catalog["SN"] ? '</br>SN：'.$catalog["SN"]:'</br>';
                                                            echo $catalog["cata_remark"] ? '</br>敘述：'.$catalog["cata_remark"]:'</br>';?>
                                                    </td>
                                                    <td><span class="badge rounded-pill <?php switch($catalog["cate_id"]){
                                                                                case "1": echo "bg-primary"; break;
                                                                                case "2": echo "bg-success"; break;
                                                                                case "3": echo "bg-warning text-dark"; break;
                                                                                case "4": echo "bg-danger"; break;
                                                                                case "5": echo "bg-info text-dark"; break;
                                                                                case "6": echo "bg-dark"; break;
                                                                                case "7": echo "bg-secondary"; break;
                                                                                default: echo "bg-light text-success"; break;
                                                                            }?>">
                                                                <?php echo $catalog["cate_no"].".".$catalog["cate_title"];?></span></td>
                                                    <td style="text-align: left; word-break: break-all;">
                                                        <?php 
                                                            echo $catalog["size"] ? "尺寸：".$catalog["size"]:"--";
                                                            echo $catalog["unit"] ? "</br>單位：".$catalog["unit"]:"";
                                                            echo $catalog["OBM"] ? "</br>品牌/製造商：".$catalog["OBM"]:"";
                                                            echo $catalog["model"] ? "</br>型號：".$catalog["model"]:""; 
                                                        ?></td>
                                                    <td><input type="number" id="<?php echo $catalog['SN'];?>" class="form-control amount t-center"
                                                            placeholder="數量" min="0" max="999" maxlength="3" oninput="if(value.length>3)value=value.slice(0,3)"></td>
                                                    <td>
                                                        <button type="button" name="<?php echo $catalog['SN'];?>" id="add_<?php echo $catalog['SN'];?>" class="add_btn" value="" title="加入購物車" onclick="add_item(this.name,this.value,'off');"><h5><i class="fa-regular fa-square-plus"></i></h5></button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
    
                            <!-- 2.購物車 -->
                            <div class="tab-pane fade" id="nav-shopping_cart" role="tabpanel" aria-labelledby="nav-shopping_cart-tab">
                                <div class="col-12 px-4">
                                    <label class="form-label">器材用品/數量單位：<sup class="text-danger"> *</sup></label>
                                    <div class=" rounded border bg-light" id="shopping_cart">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>select_id</th>
                                                    <th>SN</th>
                                                    <th>品名</th>
                                                    <th>型號</th>
                                                    <th>尺寸</th>
                                                    <th>數量 / 單位</th>
                                                </tr>
                                            </thead>
                                            <tbody id="shopping_cart_tbody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
    
                            <!-- 3.申請單成立 -->
                            <div class="tab-pane bg-white fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                                <div class="col-12 py-4 px-5">
                                    <!-- 表列0 說明 -->
                                    <div class="row">
                                        <div class="col-12">
                                            請申請人填入相關資料：
                                        </div>
                                        <hr>
                                    </div>
    
                                    <!-- 表列1 申請人 -->
                                    <div class="row">
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating input-group">
                                                <input type="text" name="emp_id" id="emp_id" class="form-control" required placeholder="工號" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>">
                                                <label for="emp_id" class="form-label">emp_id/工號：<sup class="text-danger"> *</sup></label>
                                                <button type="button" class="btn btn-outline-primary" onclick="search_fun('emp_id');" data-toggle="tooltip" data-placement="bottom" title="以工號自動帶出其他資訊" ><i class="fa-solid fa-magnifying-glass"></i> 搜尋</button>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="cname" id="cname" class="form-control" required placeholder="申請人姓名" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                                <label for="cname" class="form-label">cname/申請人姓名：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="extp" id="extp" class="form-control" required placeholder="分機">
                                                <label for="extp" class="form-label">extp/分機：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 表列2 申請單位 -->
                                    <div class="row">
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="plant" id="plant" class="form-control" required placeholder="申請單位">
                                                <label for="plant" class="form-label">plant/申請單位：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="dept" id="dept" class="form-control" required placeholder="部門名稱">
                                                <label for="dept" class="form-label">dept/部門名稱：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="sign_code" id="sign_code" class="form-control" required placeholder="部門代號" onblur="this.value = this.value.toUpperCase();">
                                                <label for="sign_code" class="form-label">sign_code/部門代號：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <!-- 表列3 領用站點 -->
                                    <div class="row">
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <select name="local_id" id="local_id" class="form-select" required>
                                                    <option value="" hidden>-- [請選擇 領用站點] --</option>
                                                    <?php foreach($allLocals as $allLocal){ ?>
                                                        <!-- <php if($_SESSION[$sys_id]["role"] <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>   -->
                                                        <?php if($allLocal["flag"] != "off"){ ?>  
                                                            <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" >
                                                                <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                                <label for="local_id" class="form-label">local_id/領用站點：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div style="display: flex;">
                                                <label for="" class="form-label">ppty/需求類別：</label></br>&nbsp
                                                <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required checked>
                                                <label for="ppty_1" class="form-check-label">&nbsp一般&nbsp&nbsp</label>
                                                <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" required>
                                                <label for="ppty_3" class="form-check-label" data-toggle="tooltip" data-placement="bottom" title="注意：事故須先通報防災!!">&nbsp緊急</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="omager" id="omager" class="form-control" required placeholder="主管工號"
                                                        data-toggle="tooltip" data-placement="bottom" title="輸入主管工號"
                                                        onchange="search_fun(this.value);">
                                                <label for="omager" class="form-label">omager/上層主管工號：<sup class="text-danger"> *</sup></label>
                                                <!-- <h5><span id="omager_badge" class="badge pill bg-primary"></span></h5> -->
                                                <div id="omager_badge"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 表列5 說明 -->
                                    <div class="row">
                                        <div class="col-12 px-2">
                                            <div class="form-floating">
                                                <textarea name="receive_remark" id="receive_remark" class="form-control" style="height: 150px;" placeholder="(由申請單位填寫用品/器材請領原由)"></textarea>
                                                <label for="receive_remark" class="form-label">receive_remark/用途說明：<sup class="text-danger"> * (由申請單位填寫用品/器材請領原由)</sup></label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-12 py-1">
                                            備註：
                                            </br>&nbsp1.填入申請單位、部門名稱、申請日期、器材、數量及用途說明。
                                            </br>&nbsp2.簽核：申請人=>申請部門三級主管=>環安單位承辦人=>環安單位(課)主管=>發放人及領用人=>各廠環安單位存查3年。 
                                            </br>&nbsp3.需求類別若是[緊急]，必須說明事故原因，並通報防災中心。 
                                            </br>&nbsp4.以上若有填報不實，將於以退件。 
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-6 col-md-6 py-1 px-2">
                                            
                                        </div>
                                        <div class="col-6 col-md-6 py-1 px-2 text-end">
                                            <?php if($_SESSION[$sys_id]["role"] <= 3){ ?>
                                                <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                                            <?php } ?>
                                            <a class="btn btn-secondary" href="index.php"><i class="fa fa-caret-up" aria-hidden="true"></i> 回總表</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 彈出畫面模組 saveSubmit-->
                        <div class="modal fade" id="saveSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Do you submit this 領用申請：</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-5">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="created_emp_id" id="created_emp_id" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>">
                                        <input type="hidden" name="created_cname" id="created_cname" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                        <input type="hidden" name="updated_user" id="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                        <input type="hidden" name="uuid" id="uuid" value="">
                                        <input type="hidden" name="step" id="step" value="<?php echo $step;?>">
                                        <input type="hidden" name="action" id="action" value="<?php echo $action;?>">
                                        <input type="hidden" name="idty" id="idty" value="1">
                                        <?php if($_SESSION[$sys_id]["role"] <= 3){ ?>
                                            <!-- <input type="submit" value="Submit" name="receive_submit" class="btn btn-primary"> -->
                                            <button type="submit" value="Submit" name="receive_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <!-- 尾段logs訊息 -->
                    <div class="col-12 pt-0 rounded bg-light unblock" id="logs_div">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                表單Log記錄：
                            </div>
                            <div class="col-12 col-md-6">
                        
                            </div>
                        </div>
                        <table class="for-table logs table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Step</th>
                                    <th>Signer</th>
                                    <th>Time Signed</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?><th>action</th><?php } ?>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div style="font-size: 6px;" class="text-end">
                            logs訊息text-end
                        </div>
                    </div>

                </div>
    
                <!-- 尾段：衛材訊息 -->
                <div class="row unblock">
                    <div class="col-12 mb-0">
                        <div style="font-size: 6px;">
                            <?php
                                if($_REQUEST){
                                    echo "<pre>";
                                    // print_r($_REQUEST);
                                    echo "</pre>text-end";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <!-- <div id="liveToast" class="toast bg-warning text-dark" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="2000"> -->
            <div id="liveToast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="1000">
                <div class="d-flex">
                    <div class="toast-body" id="toast-body">
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <!-- 彈出說明模組 cata_info -->
        <div class="modal fade" id="cata_info" tabindex="-1" aria-labelledby="cata_info" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">細項說明：</h5>
                        <button type="button" class="btn-close" aria-label="Close" data-bs-target="#catalog_modal" data-bs-toggle="modal"></button>
                    </div>
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-6 col-md-4" id="pic_append"></div>
                            <div class="col-6 col-md-8" id="info_append"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">返回</button>
                    </div>
                </div>
            </div>
        </div>
    
    <!-- goTop滾動畫面DIV 2/4-->
        <div id="gotop">
            <i class="fas fa-angle-up fa-2x"></i>
        </div>
    
</body>

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>


<script>
    var catalog = <?=json_encode($catalogs);?>;                     // 第一頁：info modal function 引入catalogs資料
    var action = '<?=$action;?>';                                   // Edit選染 // 引入action資料
    var receive_row = <?=json_encode($receive_row);?>;              // Edit選染 // 引入receive_row資料作為Edit
    var json = JSON.parse('<?=json_encode($logs_arr)?>');           // 鋪設logs紀錄
    var uuid = '<?=$receive_row["uuid"]?>';                         // 鋪設logs紀錄

</script>

<script src="receive_form.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>