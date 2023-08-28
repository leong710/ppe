<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

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

    if(!empty($receive_row["local_id"])){                    // edit trade表單，get已選擇出庫廠區站點
        $query_local = array(
            'local_id' => $receive_row["local_id"]
        );
        $select_local = select_local($query_local);   // 讀出已被選擇出庫廠區站點的器材存量限制

    }else{
        $select_local = array('id' => '');
    }


    $catalogs = show_catalogs();                    // 器材=All

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
        #checkEye , #in_sign_badge {
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
                    <div class="col-12 col-md-6 py-0">
                        <h3><i class="fa-solid fa-3"></i>&nbsp<b>領用申請</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <a href="index.php" class="btn btn-success"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回總表</a>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-4">
                        需求單號：<?php echo ($receive_row['id'])             ? "aid_".$receive_row['id'] : "(尚未給號)";?></br>
                        開單日期：<?php echo ($receive_row['created_at'])     ? $receive_row['created_at'] : date('Y-m-d H:i')."&nbsp(實際以送出時間為主)";?></br>
                        填單人員：<?php echo ($receive_row["created_emp_id"]) ? $receive_row["created_emp_id"]." / ".$receive_row["created_cname"] : $_SESSION["AUTH"]["emp_id"]." / ".$_SESSION["AUTH"]["cname"];?>
                    </div>
                    <div class="col-12 col-md-8 text-end">
                        <?php if($_SESSION[$sys_id]["role"] <= 2 && $receive_row['idty'] == 1){ ?>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#submitModal" value="1" onclick="submit_item(this.value, this.innerHTML);">轉呈 (forwarded)</button>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitModal" value="0" onclick="submit_item(this.value, this.innerHTML);">核准 (Approve)</button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#submitModal" value="2" onclick="submit_item(this.value, this.innerHTML);">退回 (Reject)</button>
                            <!-- <button class="btn bg-warning text-dark" data-bs-toggle="modal" data-bs-target="#submitModal" value="3" onclick="submit_item(this.value, this.innerHTML);">作廢 (Abort)</button> -->
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
                                            <?php if(($_SESSION[$sys_id]["role"] <= 1 ) || (isset($receive_row['idty']) && $receive_row['idty'] != 0)){ ?>
                                                <a href="form.php?uuid=<?php echo $receive_row['uuid'];?>&action=edit" class="btn btn-primary">編輯 (Edit)</a>
                                                <button class="btn bg-warning text-dark" data-bs-toggle="modal" data-bs-target="#submitModal" value="3" onclick="submit_item(this.value, this.innerHTML);">作廢 (Abort)</button>
                                            <?php }?>
                                        </div>
                                        <hr>
                                        <!-- 相關資訊說明 -->
                                        <div class="col-12 py-1" id="info_table"> 
                                            <!-- 表列1 申請單位 -->
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
                                            
                                            <!-- 表列2 申請人 -->
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
                                            
                                            <!-- 表列3 領用站點 -->
                                            <div class="row">
                                                <div class="col-6 col-md-4 py-1 px-2">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" readonly
                                                            value="<?php echo $select_local['id'].'：'.$select_local['site_title'].' '.$select_local['fab_title'].'_'.$select_local['local_title']; 
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
                                                        <input type="text" name="in_sign" id="in_sign" class="form-control" required disabled placeholder="上層主管工號"
                                                                data-toggle="tooltip" data-placement="bottom" title="輸入上層主管工號" >
                                                        <label for="in_sign" class="form-label">in_sign/上層主管工號：<sup class="text-danger"> *</sup></label>
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
                                                    </br>&nbsp2.簽核：申請人=>申請部門三級主管=>環安單位承辦人=>環安單位(課)主管=>發放人及領用人=>各廠環安單位存查3年。 
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
                                                            <th>SN</th>
                                                            <th>品名</th>
                                                            <th>型號</th>
                                                            <th>尺寸</th>
                                                            <th>數量</th>
                                                            <th>單位</th>
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
                                                        <input type="text" name="in_sign" id="key_word" class="form-control" style="height: auto;" placeholder="請輸入工號、姓名或NT帳號"
                                                             aria-label="請輸入查詢對象" onchange="search_fun(this.value);">
                                                            <!-- <h5><span id="in_sign_badge" class="badge pill bg-primary"></span></h5> -->
                                                            <div id="in_sign_badge"></div>
                                                    </div>
                                                </div>
                                            <hr>
                                            </div>
                                            <label for="sin_comm" class="form-check-label" >command：</label>
                                            <textarea name="sin_comm" id="sin_comm" class="form-control" rows="5"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="updated_user" id="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                            <input type="hidden" name="uuid" id="uuid" value="">
                                            <input type="hidden" name="action" id="action" value="<?php echo $action;?>">
                                            <input type="hidden" name="idty" id="idty" value="">
                                            <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
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
                            <div style="font-size: 6px;" class="text-end">
                                logs訊息text-end
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- 尾段：duBug訊息 -->
                <div class="row block">
                    <div class="col-12 mb-0">
                        <div style="font-size: 6px;">
                            <?php
                                if($_REQUEST){
                                    echo "<pre>";
                                    // print_r($_REQUEST);
                                    print_r($receive_row);
                                    echo "</pre>text-end";
                                }
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
    // 引入catalogs資料
    var catalogs = <?=json_encode($catalogs);?>;
    // 加入購物車清單
    function add_item(cata_SN, add_amount, swal_flag){
        var swal_title = '加入購物車清單';
        // swal_flag=off不顯示swal、其他是預設1秒
        if(swal_flag == 'off'){
            var swal_time = 0;
        }else{
            var swal_time = 1 * 1000;
        }

        if(add_amount <= 0 ){
            var swal_content = cata_SN+' 沒有填數量!'+' 加入失敗';
            var swal_action = 'error';
            swal(swal_title ,swal_content ,swal_action);      // swal需要按鈕確認

        }else{
            var check_item_return = check_item(cata_SN, 0);    // call function 查找已存在的項目，並予以清除。
            Object(catalogs).forEach(function(cata){          
                if(cata['SN'] === cata_SN){
                    var input_cb = '<input type="checkbox" name="cata_SN_amount['+cata['SN']+']" id="'+cata['SN']+'" class="select_item" value="'+add_amount+'" checked onchange="check_item(this.id)" disabled>';
                    var add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+'</td><td>'+cata['SN']+'</td><td>'+cata['pname']+'</td><td>'+cata['model']+'</td><td>'+cata['size']+'</td><td>'+add_amount+'</td><td>'+cata['unit']+'</td></tr>';
                    $('#shopping_cart_tbody').append(add_cata_item);
                    return;         // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了
                }
            })
            // 根據check_item_return來決定使用哪個swal型態；true = 有找到數值=更新、false = 沒找到數值=加入
            if(check_item_return){
                var swal_content = ' 更新成功';
                var swal_action = 'info';
            }else{
                var swal_content = ' 加入成功';
                var swal_action = 'success';
            }
            // swal_time>0才顯示swal，主要過濾edit時的渲染導入
            if(swal_time > 0){
                swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:swal_time});        // swal自動關閉
            }
            
        }
        check_shopping_count();
    }

    // 查找購物車清單已存在的項目，並予以清除
    function check_item(cata_SN, swal_time) {
        // swal_time = 是否啟動swal提示 ： 0 = 不啟動
        if(!swal_time){
            swal_time = 1;
        }
        var shopping_cart_list = document.querySelectorAll('#shopping_cart_tbody > tr');
        if (shopping_cart_list.length > 0) {
            // 使用for迴圈遍歷NodeList，而不是Object.keys()
            for (var i = 0; i < shopping_cart_list.length; i++) {
                var trElement = shopping_cart_list[i];
                if (trElement.id === 'item_' + cata_SN) {
                    // 從父節點中移除指定的<tr>元素
                    trElement.parentNode.removeChild(trElement);
                    if(swal_time != 0){
                        var swal_title = '移除購物車項目';
                        var swal_content = ' 移除成功';
                        var swal_action = 'warning';
                        swal_time = swal_time * 1000;
                        swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:swal_time});        // swal自動關閉
                    }
                    return true; // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了  // true = 有找到數值
                }
            }
        }
        return false;       // false = 沒找到數值
    }
    
    // 清算購物車件數，顯示件數，切換申請單按鈕
    function check_shopping_count(){
        var shopping_cart_list = document.querySelectorAll('#shopping_cart_tbody > tr');
        $('#shopping_count').empty();
        if(shopping_cart_list.length > 0){
            $('#shopping_count').append(shopping_cart_list.length);
        }
    }
    // 簽核類型渲染
    function submit_item(idty, idty_title){
        $('#idty, #idty_title, #action').empty();
        document.getElementById('action').value = 'sign';
        document.getElementById('idty').value = idty;
        $('#idty_title').append(idty_title);
        var forwarded_div = document.getElementById('forwarded');
        if(forwarded_div && (idty == 1)){
            forwarded_div.classList.remove('unblock');           // 按下轉呈 = 解除 加簽
        }else{
            forwarded_div.classList.add('unblock');              // 按下其他 = 隱藏
        }
    }

// // // searchUser function 
    // 第一-階段：search Key_word
    function search_fun(search){
        mloading("show");                       // 啟用mLoading

        var fun = 'in_sign_badge';
        search = search.trim();
        $('#in_sign_badge').empty();

        if(!search || (search.length < 8)){
            alert("查詢工號字數最少 8 個字以上!!");
            $("body").mLoading("hide");
            return false;
        } 

        $.ajax({
            // url:'http://tneship.cminl.oa/hrdb/api/index.php',
            url:'http://localhost/hrdb/api/index.php',
            method:'get',
            dataType:'json',
            data:{
                functionname: 'search',                     // 操作功能
                uuid: '39aad298-a041-11ed-8ed4-2cfda183ef4f',
                search: search                              // 查詢對象key_word
            },
            success: function(res){
                var res_r = res["result"];
                // 將結果進行渲染
                if (res_r !== '') {
                    var obj_val = res_r[0];                                         // 取Object物件0

                    if(fun == 'in_sign_badge'){     // 搜尋申請人上層主管emp_id
                        if(obj_val){                            
                            $('#in_sign_badge').append('<div class="tag">' + obj_val.cname + '<span class="remove">x</span></div>');
                        }else{
                            // alert("查無工號["+search+"]!!");
                            document.getElementById('key_word').value = '';                         // 將欄位cname清除
                            alert('查無工號：'+ search +' !!');
                        }
                    }
                }
            },
            error (){
                console.log("search error");
            }
        })
        $("body").mLoading("hide");
    }
    
    // 第二階段：移除單項模組
    $('#in_sign_badge').on('click', '.remove', function() {
        $(this).closest('.tag').remove();   // 自畫面中移除
        document.getElementById('key_word').value = '';                         // 將欄位cname清除
        // $('#in_sign_badge').empty();
    });
// // // searchUser function 

// // // Edit選染
    // 引入action資料
    var action = '<?=$action;?>';
    function edit_item(){
        // 引入receive_row資料作為Edit
        var receive_row = <?=json_encode($receive_row);?>;
        var receive_item = {
            "plant"          : "plant/申請單位", 
            "dept"           : "dept/部門名稱", 
            "sign_code"      : "sign_code/部門代號", 
            "emp_id"         : "emp_id/工號",
            "cname"          : "cname/申請人姓名",
            "extp"           : "extp/分機",
            // "local_id"       : "local_id/領用站點",              // 改由php echo產生
            "ppty"           : "** ppty/需求類別",
            "in_sign"        : "in_sign/上層主管工號",
            "receive_remark" : "receive_remark/用途說明",
            // "created_emp_id" : "created_emp_id/開單人工號",
            // "created_cname"  : "created_cname/開單人姓名",
            // "idty"           : "idty",
            "uuid"           : "uuid",
            "cata_SN_amount" : "** cata_SN_amount"
            // "sin_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原陣列逐筆繞出來
        Object.keys(receive_item).forEach(function(receive_key){
            if(receive_key == 'ppty'){                      // ppty/需求類別
                document.querySelector('#'+receive_key+'_'+receive_row[receive_key]).checked = true;
                
            }else if(receive_key == 'cata_SN_amount'){      //cata_SN_amount 購物車
                var receive_row_cart = JSON.parse(receive_row[receive_key]);
                Object.keys(receive_row_cart).forEach(function(cart_key){
                    add_item(cart_key, receive_row_cart[cart_key], 'off');
                })
            }else{
                document.querySelector('#'+receive_key).value = receive_row[receive_key]; 
            }
        })

        // 鋪設logs紀錄
        var json = JSON.parse('<?=json_encode($logs_arr)?>');
        // var uuid = '<=$receive_row["uuid"]?>';
        var forTable = document.querySelector('.logs tbody');
        for (var i = 0, len = json.length; i < len; i++) {
            forTable.innerHTML += 
                '<tr><td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + '</td><td>' + json[i].remark + '</td></tr>';
        }
    }

    // tab_table的顯示關閉功能
    function op_tab(tab_value){
        $("#"+tab_value+"_btn .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
        var tab_table = document.getElementById(tab_value+"_table");
        if (tab_table.style.display === "none") {
            tab_table.style.display = "table";
        } else {
            tab_table.style.display = "none";
        }
    }
    
    // All resources finished loading! // 關閉mLoading提示
    window.addEventListener("load", function(event) {
        $("body").mLoading("hide");
    });

    $(document).ready(function () {

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        })
        
        edit_item();        // 啟動鋪設畫面

        // 20230817 禁用Enter鍵表單自動提交 
        document.onkeydown = function(event) { 
            var target, code, tag; 
            if (!event) { 
                event = window.event;       //針對ie瀏覽器 
                target = event.srcElement; 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "TEXTAREA") { return true; } 
                    else { return false; } 
                } 
            } else { 
                target = event.target;      //針對遵循w3c標準的瀏覽器，如Firefox 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "INPUT") { return false; } 
                    else { return true; } 
                } 
            } 
        };
        


    })

</script>

<?php include("../template/footer.php"); ?>