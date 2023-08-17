<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // if(isset($_POST["pr2fab_submit"])){    // 發貨 => 12
    //     update_pr2fab($_REQUEST);
    //     header("refresh:0;url=index.php");
    //     exit;
    // }

    // if(isset($_POST["getIssue_submit"])){    // 收貨 => 10
    //     update_getIssue($_REQUEST);
    //     header("refresh:0;url=index.php");
    //     exit;
    // }

    // 決定表單開啟方式
    if(isset($_REQUEST["action"])){
        $action = $_REQUEST["action"];              // 有action就帶action
    }else{
        $action = 'review';                         // 沒有action就新開單
    }

    if(isset($_REQUEST["id"])){
        $issue_row = show_issue($_REQUEST);
        if(empty($issue_row)){
            echo "<script>alert('id-error：{$_REQUEST["id"]}')</script>";
            header("refresh:0;url=index.php");
            exit;
        }
        // logs紀錄鋪設前處理 
        $logs_dec = json_decode($issue_row["logs"]);
        $logs_arr = (array) $logs_dec;

    }else{
        $issue_row = array( "id" => "" );       // 預設issue_row[id]=空array
        $logs_arr = [];                             // 預設logs_arr=空array
        $action = 'create';                         // 因為沒有id，列為新開單，防止action outOfspc
    }

    $allLocals = show_allLocal();                   // 所有儲存站點
    $catalogs = show_catalogs();                    // 器材=All
    // $categories = show_categories();                // 分類
    // $sum_categorys = show_sum_category();           // 統計分類與數量

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
                        <h3><b>請購需求單</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <a href="index.php" class="btn btn-success"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回總表</a>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        需求單號：<?php echo ($action == 'create') ? "(尚未給號)": "aid_".$issue_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$issue_row['create_date']; ?></br>
                    </div>
                    <div class="col-12 col-md-6 text-end">

                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 p-0">
                    <!-- 內頁 -->
                        <!-- 3.申請單成立 -->
                        <div class="bg-white rounded" id="nav-review" >
                            <div class="col-12 py-3 px-5">
                                <!-- 表列0 說明 -->
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        申請人相關資料：
                                        <button type="button" id="info_btn" class="op_tab_btn" value="info" onclick="op_tab(this.value)" title="訊息收折"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="col-6 col-md-6 text-end">
                                        <?php if(($_SESSION[$sys_id]["role"] <= 1 ) || (isset($issue_row['idty']) && $issue_row['idty'] != 0)){ ?>
                                            <a href="form.php?id=<?php echo $issue_row['id'];?>&action=edit" class="btn btn-primary">編輯</a>
                                        <?php }?>
                                    </div>
                                    <hr>
                                    <div class="col-12 py-0" id="info_table"> 
                                                    
                                        <!-- 表列2 申請人 -->
                                        <div class="row">
                                            <div class="col-6 col-md-6 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="in_user_id" id="in_user_id" class="form-control" required placeholder="工號" value="" readonly >
                                                    <label for="in_user_id" class="form-label">in_user_id/工號：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="cname_i" id="cname_i" class="form-control" required placeholder="申請人姓名" value="" readonly >
                                                    <label for="cname_i" class="form-label">cname_i/申請人姓名：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- 表列3 領用站點 -->
                                        <div class="row">
                                            <div class="col-12 col-md-6 py-1 px-2">
                                                <div class="form-floating">
                                                    <select name="in_local" id="in_local" class="form-select" required disabled>
                                                        <option value="" hidden>-- 請選擇 需求廠區 儲存點 --</option>
                                                        <?php foreach($allLocals as $allLocal){ ?>
                                                                <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" >
                                                                    <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <label for="in_local" class="form-label">in_local/需求廠區：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-1 px-2">
                                                <div style="display: flex;">
                                                    <label for="ppty" class="form-label">ppty/需求類別：</label></br>&nbsp
                                                    <input type="radio" name="ppty" value="0" id="ppty_0" class="form-check-input" required disabled >
                                                    <label for="ppty_0" class="form-check-label">&nbsp臨時&nbsp&nbsp</label>
                                                    <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required disabled >
                                                    <label for="ppty_1" class="form-check-label">&nbsp一般&nbsp&nbsp</label>
                                                    <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" required disabled >
                                                    <label for="ppty_3" class="form-check-label" data-toggle="tooltip" data-placement="bottom" title="注意：事故須先通報防災!!">&nbsp緊急</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 表列5 說明 -->
                                        <div class="row">
                                            <div class="col-12 col-md-12 py-2 px-2">
                    
                                            </div>
                                            <hr>
                                            <div class="col-12 py-1">
                                                <b>備註：</b>
                                                </br>&nbsp1.填入申請人工號、姓名、需求廠區、需求類別、器材數量。
                                                </br>&nbsp2.簽核：申請人=>承辦人=>PR待轉=>轉PR=>表單結案。 
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
                                    <div class="col-12 py-1 px-2 text-end">
                                        <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitModal" value="0" onclick="submit_item(this.value, this.innerHTML);">同意 (Approve)</button>
                                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#submitModal" value="2" onclick="submit_item(this.value, this.innerHTML);">駁回 (Disapprove)</button>
                                            <!-- <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#submitModal" value="1" onclick="submit_item(this.value, this.innerHTML);">轉呈 (forwarded)</button> -->
                                            <button class="btn bg-warning text-dark" data-bs-toggle="modal" data-bs-target="#submitModal" value="3" onclick="submit_item(this.value, this.innerHTML);">作廢 (Abort)</button>
                                        <?php }else{ ?>
                                            <a class="btn btn-success" href="index.php"><i class="fa fa-caret-up" aria-hidden="true"></i> 回總表</a>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- 彈出畫面模組 submitModal-->
                        <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-l">
                                <form action="store.php" method="post">

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">請購需求單：&nbsp<span id="idty_title"></span></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        
                                        <div class="modal-body px-5">
                                            <label for="sin_comm" class="form-check-label" >command：</label>
                                            <textarea name="sin_comm" id="sin_comm" class="form-control" rows="5"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="updated_user" id="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                            <input type="hidden" name="id" id="id" value="">
                                            <input type="hidden" name="action" id="action" value="<?php echo $action;?>">
                                            <input type="hidden" name="idty" id="idty" value="">
                                            <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                                <button type="submit" value="Submit" name="issue_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
                                            <?php } ?>
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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
    
                <!-- 尾段：deBug訊息 -->
                <div class="row unblock">
                    <div class="col-12 mb-0">
                        <div style="font-size: 6px;">
                            <?php
                                if($issue_row){
                                    echo "<pre>";
                                    // print_r($_REQUEST);
                                    // print_r($issue_row);
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
    }
// // // Edit選染
    // 引入action資料
    var action = '<?=$action;?>';
    function edit_item(){
        // 引入issue_row資料作為Edit
        var issue_row = <?=json_encode($issue_row);?>;
        var issue_item = {
            "in_user_id"     : "in_user_id/工號",
            "cname_i"        : "cname_i/申請人姓名",
            "in_local"       : "in_local/領用站點",
            "ppty"           : "** ppty/需求類別",
            "id"             : "id",
            "item"           : "** item"
            // "sin_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原陣列逐筆繞出來
        Object.keys(issue_item).forEach(function(issue_key){
            if(issue_key == 'ppty' && issue_row[issue_key]){                      // ppty/需求類別
                var ppty = document.querySelector('#'+issue_key+'_'+issue_row[issue_key]);
                if(ppty){
                    document.querySelector('#'+issue_key+'_'+issue_row[issue_key]).checked = true;
                }
                
            }else if(issue_key == 'item' && issue_row[issue_key]){      //item 購物車
                var issue_row_cart = JSON.parse(issue_row[issue_key]);
                Object.keys(issue_row_cart).forEach(function(cart_key){
                    add_item(cart_key, issue_row_cart[cart_key], 'off');
                })
            }else if(issue_row[issue_key]){
                var row_key = document.querySelector('#'+issue_key);
                if(row_key){
                    document.querySelector('#'+issue_key).value = issue_row[issue_key]; 
                }
            }
        })

        // 鋪設logs紀錄
        var json = JSON.parse('<?=json_encode($logs_arr)?>');
        // var id = '<=$issue_row["id"]?>';
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

    $(document).ready(function () {

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        })
        
        edit_item();        // 啟動鋪設畫面

        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
        });

    })

</script>

<?php include("../template/footer.php"); ?>