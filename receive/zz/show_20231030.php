<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    $receive_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 複製本頁網址藥用
    if(isset($_SERVER["HTTP_REFERER"])){
        $up_href = $_SERVER["HTTP_REFERER"];            // 回上頁
    }else{
        $up_href = $receive_url;                        // 回本頁
    }

    // 決定表單開啟方式
    if(isset($_REQUEST["action"])){
        $action = $_REQUEST["action"];              // 有action就帶action
    }else{
        $action = 'review';                         // 沒有action就新開單
    }

    if(!empty($_REQUEST["uuid"])){
        $receive_row = show_receive($_REQUEST);
        if(empty($receive_row)){
            echo "<script>alert('uuid-error：{$_REQUEST["uuid"]}')</script>";
            header("refresh:0;url={$up_href}");
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

    if(!empty($receive_row["local_id"])){           // edit trade表單，get已選擇出庫廠區站點
        $query_local = array(
            'local_id' => $receive_row["local_id"]
        );
        $select_local = select_local($query_local);   // 讀出已被選擇出庫廠區站點

    }else{
        $select_local = array('id' => '');
    }


    $catalogs = show_catalogs();                    // 器材=All

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
            '10'=> '轉呈簽核'
        ];
        
        // 決定表單開啟 $step身份
        if($receive_row["created_emp_id"] == $_SESSION["AUTH"]["emp_id"]){
            $step_index = '0';  // 填單人
        } else if($receive_row["emp_id"] == $_SESSION["AUTH"]["emp_id"]){
            $step_index = '1';  // 申請人
        }      
        if($receive_row["omager"] == $_SESSION["AUTH"]["emp_id"]){
            $step_index = '2';  // 申請人主管
        } else if($receive_row["in_sign"] == $_SESSION["AUTH"]["emp_id"]){
            $step_index = '10';  // 轉呈簽核
        }

        // if(empty($step_index)){
        if(!isset($step_index)){
            if(!isset($_SESSION[$sys_id]["role"]) ||($_SESSION[$sys_id]["role"]) == 3){
                $step_index = '6';}      // noBody
            if(isset($_SESSION[$sys_id]["role"]) && ($_SESSION[$sys_id]["role"]) == 2){
                $step_index = '7';}      // ppe site user
            if(isset($_SESSION[$sys_id]["role"]) && ($_SESSION[$sys_id]["role"]) == 1){
                $step_index = '8';}      // ppe pm
            if(isset($_SESSION[$sys_id]["role"]) && ($_SESSION[$sys_id]["role"]) == 0){
                $step_index = '9';}      // 系統管理員
        }
        
        // $step套用身份
        $step = $step_arr[$step_index];
    // }

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
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
        #checkEye , #omager_badge , #in_sign_badge {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            z-index: 999;
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
        .op_tab_btn {
            /* 將圖示的背景色設置為透明並添加陰影 */
            background-color: transparent; 
            text-shadow: 0px 0px 1px #fff;
            color: blue;
            /* 將圖示的背景色設置為按鈕的背景色 */
            /* background-color: inherit; */
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
    </style>
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                icon: "../../libs/jquery/Wedges-3s-120px.gif",
            }); 
        }
        // mloading();    // 畫面載入時開啟loading
    </script>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-11 border rounded px-3 py-4" style="background-color: #D4D4D4;">
                <!-- 表頭1 -->
                <div class="row px-2">
                    <div class="col-12 col-md-4 py-0">
                        <h3><i class="fa-solid fa-3"></i>&nbsp<b>領用申請</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-4 py-0 t-center">
                        <?php 
                            echo "身分: ".$step." / idty:".$receive_row['idty']." ";
                            switch($receive_row['idty']){
                                case "0" : echo '<span class="badge rounded-pill bg-warning text-dark">待領</span>'; break;
                                case "1" : echo '<span class="badge rounded-pill bg-danger">待簽</span>'; break;
                                case "2" : echo "退件"; break;
                                case "3" : echo "取消"; break;
                                case "10": echo "結案"; break;
                                case "11": echo "轉PR"; break;
                                case "12": echo '<span class="badge rounded-pill bg-success">待收</span>'; break;
                                default  : echo "na"; break; }

                            echo !empty($receive_row['in_sign']) ? " / wait: ".$receive_row['in_sign']." " :"";
                            echo !empty($receive_row['flow']) ? " / flow: ".$receive_row['flow']." " :"";
                        ?>
                    </div>
                    <div class="col-12 col-md-4 py-0 text-end">
                        <button class="btn btn-secondary" onclick="location.href='<?php echo $up_href;?>'"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回上頁</button>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-4">
                        需求單號：<?php echo ($receive_row['id'])             ? "aid_".$receive_row['id'] : "(尚未給號)";?></br>
                        開單日期：<?php echo ($receive_row['created_at'])     ? $receive_row['created_at'] : date('Y-m-d H:i')."&nbsp(實際以送出時間為主)";?></br>
                        填單人員：<?php echo ($receive_row["created_emp_id"]) ? $receive_row["created_emp_id"]." / ".$receive_row["created_cname"] : $_SESSION["AUTH"]["emp_id"]." / ".$_SESSION["AUTH"]["cname"];?>
                    </div>
                    <div class="col-12 col-md-8 text-end">
                        <?php if($receive_row['idty'] == 1){ // 1.簽核中 ?>
                            <?php if( ($receive_row['in_sign'] == $_SESSION["AUTH"]["emp_id"]) || $_SESSION[$sys_id]["role"] == 0 ){ ?>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitModal" value="0" onclick="submit_item(this.value, this.innerHTML);">同意 (Approve)</button>
                            <?php } if( ($receive_row['in_sign'] == $_SESSION["AUTH"]["emp_id"]) || $_SESSION[$sys_id]["role"] <= 1 ){ ?>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#submitModal" value="5" onclick="submit_item(this.value, this.innerHTML);">轉呈 (forwarded)</button>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#submitModal" value="2" onclick="submit_item(this.value, this.innerHTML);">退回 (Reject)</button>
                        <?php } } ?>
                        <?php if($receive_row['idty'] == 0){ // 0.待領、待收 ?>
                            
                        <?php } ?>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 p-0">
                    <!-- 內頁 -->
                        <div class="tab-content rounded bg-light" id="nav-tabContent">
                            <!-- 3.申請單成立 -->
                            <div class="tab-pane bg-white fade show active" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                                <div class="col-12 py-3 px-5">
                                    <div class="row">
                                        <!-- 表頭 -->
                                        <div class="col-6 col-md-6">
                                            <b>申請人相關資訊：</b>
                                            <button type="button" id="info_btn" class="op_tab_btn" value="info" onclick="op_tab(this.value)" title="訊息收折"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                        </div>
                                        <div class="col-6 col-md-6 text-end">
                                            <!-- 限定表單所有人：created_emp_id開單人、emp_id申請人、ppe pm、admin -->
                                            <?php if( ($receive_row['created_emp_id'] == $_SESSION["AUTH"]["emp_id"]) ||
                                                    ($receive_row['emp_id'] == $_SESSION["AUTH"]["emp_id"]) || ($_SESSION[$sys_id]["role"] <= 1) ){ ?>
                                                <!-- <php if(($receive_row['idty'] == 2) || ($receive_row['idty'] == 4) || ($receive_row['idty'] == 6)){ ?> -->
                                                <!-- 表單狀態：2退回 4編輯 6暫存 -->
                                                <?php if(in_array($receive_row['idty'], [ 2, 4, 6 ])){ ?>
                                                    <a href="form.php?uuid=<?php echo $receive_row['uuid'];?>&action=edit" class="btn btn-primary">編輯 (Edit)</a>
                                                <?php ;} ?>
                                                <!-- 表單狀態：1送出 2退回 4編輯 5轉呈 6暫存 -->
                                                <?php if(in_array($receive_row['idty'], [ 1, 2, 4, 5, 6 ])){ ?>
                                                    <button class="btn bg-warning text-dark" data-bs-toggle="modal" data-bs-target="#submitModal" value="3" onclick="submit_item(this.value, this.innerHTML);">作廢 (Abort)</button>
                                                <?php ;} ?>
                                            <?php ;} ?>
                                            <?php if($receive_row['idty'] == 0){ ?>
                                                <button class="btn btn-success" onclick='push_mapp(`<?php echo $_SESSION["AUTH"]["emp_id"];?>`)' data-toggle="tooltip" data-placement="bottom" title="發給正在看此單的人"><i class="fa-brands fa-facebook-messenger"></i> 推送 (Push)</button>
                                            <?php } ?>
                                        </div>
                                        <hr>
                                        <!-- 相關資訊說明 -->
                                        <div class="col-12 py-1" id="info_table"> 

                                            <!-- 表列1 申請人 -->
                                            <div class="row">
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div class="form-floating">
                                                        <input type="text" name="emp_id" id="emp_id" class="form-control" required placeholder="工號" value="<?php echo $receive_row["emp_id"];?>" readonly >
                                                        <label for="emp_id" class="form-label">emp_id/工號：<sup class="text-danger"> *</sup></label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div class="form-floating">
                                                        <input type="text" name="cname" id="cname" class="form-control" required placeholder="申請人姓名" value="<?php echo $receive_row["cname"];?>" readonly >
                                                        <label for="cname" class="form-label">cname/申請人姓名：<sup class="text-danger"> *</sup></label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div class="form-floating">
                                                        <input type="text" name="extp" id="extp" class="form-control" required placeholder="分機" readonly >
                                                        <label for="extp" class="form-label">extp/分機：<sup class="text-danger"> *</sup></label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 表列2 申請單位 -->
                                            <div class="row">
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div class="form-floating">
                                                        <input type="text" name="plant" id="plant" class="form-control" required placeholder="申請單位" readonly >
                                                        <label for="plant" class="form-label">plant/申請單位：<sup class="text-danger"> *</sup></label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div class="form-floating">
                                                        <input type="text" name="dept" id="dept" class="form-control" required placeholder="部門名稱" readonly >
                                                        <label for="dept" class="form-label">dept/部門名稱：<sup class="text-danger"> *</sup></label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div class="form-floating">
                                                        <input type="text" name="sign_code" id="sign_code" class="form-control" required placeholder="部門代號" readonly >
                                                        <label for="sign_code" class="form-label">sign_code/部門代號：<sup class="text-danger"> *</sup></label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 表列3 領用站點 -->
                                            <div class="row">
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="local_id" readonly
                                                            value="<?php echo $select_local['id'].'：'.$select_local['fab_title'].'_'.$select_local['local_title']; 
                                                                echo ($select_local['flag'] == 'Off') ? '(已關閉)':''; ?>">
                                                        <label for="local_id" class="form-label">local_id/領用站點：<sup class="text-danger"> *</sup></label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div style="display: flex;">
                                                        <label for="ppty" class="form-label">ppty/需求類別：</label></br>&nbsp
                                                        <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required disabled>
                                                        <label for="ppty_1" class="form-check-label">&nbsp一般&nbsp&nbsp</label>
                                                        <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" required disabled>
                                                        <label for="ppty_3" class="form-check-label" data-toggle="tooltip" data-placement="bottom" title="注意：事故須先通報防災!!">&nbsp緊急</label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div class="form-floating">
                                                        <input type="text" name="omager" id="omager" class="form-control" required disabled placeholder="主管工號">
                                                        <label for="omager" class="form-label">omager/主管工號：<sup class="text-danger"> *</sup></label>
                                                        <div id="omager_badge"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 表列5 說明 -->
                                            <div class="row">
                                                <div class="col-12 col-md-12 py-2 px-2">
                                                    <div class="form-floating">
                                                        <textarea name="receive_remark" id="receive_remark" class="form-control" style="height: 150px;" placeholder="(由申請單位填寫用品/器材請領原由)" disabled></textarea>
                                                        <label for="receive_remark" class="form-label">receive_remark/用途說明：<sup class="text-danger"> * (由申請單位填寫用品/器材請領原由)</sup></label>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="col-12 py-1">
                                                    <b>備註：</b>
                                                    </br>&nbsp1.填入申請單位、部門名稱、申請日期、器材、數量及用途說明。
                                                    </br>&nbsp2.簽核：申請人(送出)=>申請部門三級主管=>發放人及領用人(發放)=>環安單位承辦人=>環安單位(課)主管=>各廠環安單位存查3年(結案)。 
                                                    </br>&nbsp3.需求類別若是[緊急]，必須說明事故原因，並通報防災中心。 
                                                    </br>&nbsp4.以上若有填報不實，將於以退件。 
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>

                                    <!-- 表列4 購物車 -->
                                    <div class="row">
                                        <div class="col-12 border rounded bg-info">
                                            <label class="form-label">器材用品/數量單位：&nbsp<span id="shopping_count" class="badge rounded-pill bg-danger"></span></label>
                                            <div class=" rounded border bg-light" id="shopping_cart">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th>select</th>
                                                            <th>SN / 品名</th>
                                                            <th>型號</th>
                                                            <th>尺寸</th>
                                                            <th>數量</th>
                                                            <th>單位</th>
                                                            <th>實發量</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="shopping_cart_tbody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div style="font-size: 6px;" class="py-2 text-end">
                                            
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- 彈出畫面模組 submitModal-->
                        <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable ">
                                <form action="store.php" method="post">

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Do you submit this：<span id="idty_title"></span>&nbsp?</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        
                                        <div class="modal-body px-5">
                                            <div class="row unblock" id="forwarded">
                                                <div class="col-12" id="searchUser_table">
                                                    <!-- 第二排的功能 : 搜尋功能 -->
                                                    <div class="input-group search" id="select_inSign_Form">
                                                        <!-- <button type="button" class="btn btn-outline-secondary form-label" onclick="resetMain();">清除</button> -->
                                                        <span class="input-group-text form-label">轉呈</span>
                                                        <input type="text" name="in_sign" id="in_sign" class="form-control" placeholder="請輸入工號"
                                                             aria-label="請輸入查詢對象工號" onchange="search_fun(this.id, this.value);">
                                                        <div id="in_sign_badge"></div>
                                                    </div>
                                                </div>
                                            <hr>
                                            </div>
                                            <label for="sign_comm" class="form-check-label" >command：</label>
                                            <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="updated_user" id="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                            <input type="hidden" name="uuid" id="uuid" value="<?php echo $receive_row['uuid'];?>">
                                            <input type="hidden" name="action" id="action" value="<?php echo $action;?>">
                                            <input type="hidden" name="step" id="step" value="<?php echo $step;?>">
                                            <input type="hidden" name="idty" id="idty" value="">
                                            <?php if($_SESSION[$sys_id]["role"] <= 3){ ?>
                                                <button type="submit" value="Submit" name="receive_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> Agree</button>
                                            <?php } ?>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <hr>
                    <!-- 尾段logs訊息 -->
                    <div class="col-12 pt-0 rounded bg-light" id="logs_div">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                表單Log記錄：
                            </div>
                            <div class="col-12 col-md-6">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 py-1 px-4">
                                <table class="for-table logs table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Step</th>
                                            <th>Signer</th>
                                            <th>Time Signed</th>
                                            <th>Status</th>
                                            <th >Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div style="font-size: 10px;" class="text-end">
                                logs-end
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- 尾段：duBug訊息 -->
                <div class="row block">
                    <div class="col-12 mb-0">
                        <div style="font-size: 14px;">
                            <?php
                                // if($_REQUEST){
                                    echo "<pre>";
                                    print_r($select_local);
                                    // print_r($_REQUEST);
                                    // print_r($receive_row);
                                    echo "</pre>text-end";
                                // }
                            ?>
                        </div>
                    </div>
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
    var catalogs = <?=json_encode($catalogs);?>;                    // 第一頁：info modal function 引入catalogs資料
    var action = '<?=$action;?>';                                   // Edit選染 // 引入action資料
    var receive_row = <?=json_encode($receive_row);?>;              // Edit選染 // 引入receive_row資料作為Edit
    var json = JSON.parse('<?=json_encode($logs_arr)?>');           // 鋪設logs紀錄
    var receive_url = '<?=$receive_url;?>';                         // push訊息 // 本文件網址
</script>

<script src="receive_show.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>