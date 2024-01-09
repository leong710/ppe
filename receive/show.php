<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 複製本頁網址藥用
    $receive_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // 回本頁
    if(isset($_SERVER["HTTP_REFERER"])){
        $up_href = $_SERVER["HTTP_REFERER"];            // 回上頁
    }else{
        $up_href = $receive_url;                        // 回本頁
    }
    
    $auth_cname     = $_SESSION["AUTH"]["cname"];      // 取出$_session引用
    $auth_emp_id    = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role       = $_SESSION[$sys_id]["role"];      // 取出$_session引用
    $sys_fab_id     = $_SESSION[$sys_id]["fab_id"];     
    $sys_sfab_id    = $_SESSION[$sys_id]["sfab_id"];    

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
    $pm_emp_id = $receive_row["pm_emp_id"];         // *** 廠區業務窗口
    $pm_emp_id_arr = explode(",",$pm_emp_id);       // 資料表是字串，要炸成陣列

    $catalogs = show_catalogs();                    // 器材=All

        // 4.組合我的廠區到$sys_sfab_id => 包含原sfab_id、fab_id和sign_code所涵蓋的廠區
            if(!in_array($sys_fab_id, $sys_sfab_id)){                       // 4-1.當fab_id不在sfab_id，就把部門代號id套入sfab_id
                array_push($sys_sfab_id, $sys_fab_id);                      // 4-1.*** 取sfab_id (此時已包含fab_id)
            }
            // 組合查詢陣列
            $query_arr = array(
                'sign_code' => $_SESSION["AUTH"]["sign_code"],
            );
            $coverFab_lists = show_coverFab_lists($query_arr);              // 4-2.呼叫fun 用$sign_code模糊搜尋
            if(!empty($coverFab_lists)){                                    // 4-2.當清單不是空值時且不在sfab_id，就把部門代號id套入sfab_id
                foreach($coverFab_lists as $coverFab){ 
                    if(!in_array($coverFab["id"], $sys_sfab_id)){
                        array_push($sys_sfab_id, $coverFab["id"]);
                    }
                }
            }

    // 身份陣列
    $step_arr = [
        '0' => '填單人',
        '1' => '申請人',
        '2' => '申請人主管',
        '3' => 'PPE發放人',            // 1.依廠區需求可能非一人簽核權限 2.發放人有調整發放數量後簽核權限
        '4' => '業務承辦',
        '5' => '環安主管',
        '6' => 'normal',
        '7' => 'PPE窗口',
        '8' => 'PPEpm',
        '9' => '系統管理員',
        '10'=> '轉呈簽核'
    ];

    // 決定表單開啟 $step身份
    if($receive_row["idty"] < 10){       // ** 未交貨後的頭銜
        // 表單交易狀態：0完成/1待收/2退貨/3取消/12發貨
        switch($receive_row['idty']){
            case "0":   // $act = '同意 (Approve)';
            case "1":   // $act = '送出 (Submit)';
                if($receive_row["in_sign"] == $auth_emp_id){
                    if($receive_row["flow"] == "Manager"){
                        $step_index = '2';      // 2.申請人主管
                    }else if( ($receive_row["flow"] == "forward")  ){   
                        $step_index = '10';     // 10.轉呈簽核
                    }
                }else{
                    if($sys_role == 2){
                        $step_index = '7';      // 7.PPE窗口
                    } else if($sys_role == 1){
                        $step_index = '8';      // 8.PPEpm
                    } else if($sys_role == 0){
                        $step_index = '9';      // 9.系統管理員
                    }
                }
                break;
            case "2":   // $act = '退回 (Reject)';
                if($receive_row["created_emp_id"] == $auth_emp_id){
                    $step_index = '0';      // 填單人
                } else if($receive_row["emp_id"] == $auth_emp_id){
                    $step_index = '1';      // 申請人
                }
                break;
            case "3":   // $act = '作廢 (Abort)'; 
            case "4":   // $act = '編輯 (Edit)';  
            case "5":   // $act = '轉呈 (Forwarded)';
            case "6":   // $act = '暫存 (Save)';  
            default:    // $act = '錯誤 (Error)';
                $step_index = '6';      // 6.normal
                break;
        }

    } else if($receive_row["idty"] >= 10){   // ** 已交貨後的頭銜
        // 表單交易狀態：0完成/1待收/2退貨/3取消/12發貨
        switch($receive_row['idty']){
            case "10":                      // $act = '結案 (Close)'; 
                break;
            case "11":                      // $act = '承辦 (Undertake)';
                if($receive_row["fab_id"] == $sys_fab_id){
                    $step_index = '4';      // 業務承辦
                }else if($receive_row["in_sign"] == $auth_emp_id){
                    $step_index = '5';      // 環安主管
                }
                break;
            case "12":                      // $act = '待收發貨 (Awaiting collection)'; 
                if($receive_row['flow'] == 'collect' && in_array($receive_row["fab_id"], $sys_sfab_id)){
                    $step_index = '3';      // ppe發放人
                } 
                break;
            case "13":                      // $act = '交貨 (Delivery)';
                if($receive_row["fab_id"] == $sys_fab_id){
                    $step_index = '4';      // 業務承辦
                }  
                break;
            default:    // $act = '錯誤 (Error)';         
                return;
        }
    } else {
        if($issue_row["created_emp_id"] == $auth_emp_id){
            $step_index = '0';      // 填單人
        } else if($issue_row["in_user_id"] == $auth_emp_id){
            $step_index = '1';      // 申請人
        }     
    }

    if(!isset($step_index)){
        if(!isset($sys_role) || ($sys_role) == 3){
            $step_index = '6';}         // normal
        if(isset($sys_role)){
            if($sys_role == 2){
                $step_index = '7';}      // PPE窗口
            if($sys_role == 1){
                $step_index = '8';}      // PPEpm
            if($sys_role == 0){
                $step_index = '9';}      // 系統管理員
        }
        if($action == 'create'){
            $step_index = '0';}         // 填單人
    }
    
    // $step套用身份
    $step = $step_arr[$step_index];
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
                    <div class="col-12 col-md-4 py-0">
                        <h3><i class="fa-solid fa-3"></i>&nbsp<b>領用申請</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-4 py-0 t-center">
                        <?php 
                            echo "<h3><span class='badge rounded-pill ";
                                switch($receive_row['idty']){
                                    case "0" : echo "bg-info'>待領";                break;
                                    case "1" : echo "bg-primary'>待簽";             break;
                                    case "2" : echo "bg-warning text-dark'>退回";   break;
                                    case "3" : echo "bg-dark'>取消";                break;
                                    case "10": echo "bg-secondary'>結案";           break;
                                    case "11": echo "bg-primary'>待簽";             break;
                                    case "12": echo "bg-success'>待收";             break;
                                    case "13": echo "bg-danger'>待簽";              break;
                                    default  : echo "'>na";                         break; 
                                }
                            // echo "<sup> ".$receive_row['idty']."</sup>";
                            echo !empty($receive_row['in_sign']) ? "：".$receive_row['in_sign']." " :"";
                            echo !empty($receive_row['flow']) ? " / ".$receive_row['flow']." " :"";
                            // echo " ... ".$step;
                            echo "</span></h3>";
                        ?>
                    </div>
                    <div class="col-12 col-md-4 py-0 text-end">
                        <button type="button" class="btn btn-secondary" onclick="location.href='index.php'"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
                        <button type="button" class="btn btn-secondary" onclick="location.href='<?php echo $up_href;?>'"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</button>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-4">
                        需求單號：<?php echo ($receive_row['id'])             ? "receive_aid_".$receive_row['id'] : "(尚未給號)";?></br>
                        開單日期：<?php echo ($receive_row['created_at'])     ? $receive_row['created_at'] : date('Y-m-d H:i')."&nbsp(實際以送出時間為主)";?></br>
                        填單人員：<?php echo ($receive_row["created_emp_id"]) ? $receive_row["created_emp_id"]." / ".$receive_row["created_cname"] : $auth_emp_id." / ".$auth_cname;?>
                    </div>
                    <div class="col-12 col-md-8 text-end">
                        <?php if( (($receive_row['idty'] == 1) && ($receive_row['in_sign'] == $auth_emp_id)) || $sys_role <= 1 ){ ?>
                            <?php if(in_array($receive_row['idty'], [ 1 ])){ // 1.簽核中 ?>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitModal" value="0" onclick="submit_item(this.value, this.innerHTML);">同意 (Approve)</button>
                                <?php if( ($receive_row["flow"] != "forward")  ){   ?>
                                    <button type="button" class="btn btn-info"    data-bs-toggle="modal" data-bs-target="#submitModal" value="5" onclick="submit_item(this.value, this.innerHTML);">轉呈 (Forwarded)</button>
                                <?php } ?>
                                <button type="button" class="btn btn-danger"  data-bs-toggle="modal" data-bs-target="#submitModal" value="2" onclick="submit_item(this.value, this.innerHTML);">退回 (Reject)</button>
                        <?php } } ?>
                        <?php // 這裡取得發放權限 idty=12.待領、待收 => 13.交貨 (Delivery)
                            $receive_collect_role = ($receive_row['idty'] == 12 && $receive_row['flow'] == 'collect' && in_array($receive_row["fab_id"], $sys_sfab_id)); 
                            if($receive_collect_role){ ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal" value="13" onclick="submit_item(this.value, this.innerHTML);">交貨 (Delivery)</button>
                        <?php } ?>

                        <?php // 承辦+主管簽核選項 idty=13.交貨delivery => 11.承辦簽核 (Undertake)
                            $receive_delivery_role = ($receive_row['flow'] == 'PPEpm' && (in_array($auth_emp_id, $pm_emp_id_arr) || $sys_role <= 1));
                            if($receive_row['idty'] == 13 && $receive_delivery_role){ ?> 
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal" value="11" onclick="submit_item(this.value, this.innerHTML);">承辦同意 (Approve)</button>
                        <?php } ?>
                        <?php // 承辦+主管簽核選項 idty=11.承辦簽核 => 10.結案 (Close)
                            if( $receive_row['idty'] == 11 && ( $receive_row['in_sign'] == $auth_emp_id || $sys_role <= 0 )){ ?> 
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal" value="10" onclick="submit_item(this.value, this.innerHTML);">主管同意 (Approve)</button>
                        <?php } ?>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 p-0">
                    <!-- 內頁 -->
                    <form action="store.php" method="post" >
                    <!-- <form action="./zz/debug.php" method="post"> -->
                                            
                        <!-- 3.申請單成立 -->
                        <div class="tab-pane bg-white rounded fade show active" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                            <div class="col-12 py-3 px-5">
                                <div class="row">
                                    <!-- 表頭 -->
                                    <div class="col-6 col-md-6">
                                        <b>申請人相關資訊：</b>
                                        <button type="button" id="info_btn" class="op_tab_btn" value="info" onclick="op_tab(this.value)" title="訊息收折"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="col-6 col-md-6 text-end">
                                        <!-- 限定表單所有人：created_emp_id開單人、emp_id申請人、ppe pm、admin -->
                                        <?php if( ($receive_row['created_emp_id'] == $auth_emp_id) || ($receive_row['emp_id'] == $auth_emp_id) || ($sys_role <= 1) ){ ?>
                                            <!-- 表單狀態：2退回 4編輯 6暫存 -->
                                            <?php if(in_array($receive_row['idty'], [ 2, 4, 6 ])){ ?>
                                                <a href="form.php?uuid=<?php echo $receive_row['uuid'];?>&action=edit" class="btn btn-primary">編輯 (Edit)</a>
                                            <?php ;} ?>
                                            <!-- 表單狀態：1送出 2退回 4編輯 5轉呈 6暫存 -->
                                            <?php if(in_array($receive_row['idty'], [ 1, 2, 4, 5, 6 ])){ ?>
                                                <button type="button" class="btn bg-warning text-dark" data-bs-toggle="modal" data-bs-target="#submitModal" value="3" onclick="submit_item(this.value, this.innerHTML);">作廢 (Abort)</button>
                                            <?php ;} ?>
                                        <?php ;} ?>
                                        <?php if($receive_row['idty'] == 12 && $receive_row['flow'] == 'collect'  // 12.待領、待收
                                                    && (in_array($receive_row["fab_id"], $sys_sfab_id) || in_array($auth_emp_id, [$receive_row['emp_id'], $receive_row['created_emp_id']])) ){ ?>
                                            <button type="button" class="btn btn-success" onclick='push_mapp(`<?php echo $auth_emp_id;?>`)' data-toggle="tooltip" data-placement="bottom" title="mapp給自己"><i class="fa-brands fa-facebook-messenger"></i> 推送 (Push)</button>
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
                                                        <th style="text-align: left;">SN / 品名</th>
                                                        <th>型號</th>
                                                        <th>尺寸</th>
                                                        <th>申請量 / 單位</th>
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

                        <!-- 彈出畫面模組 submitModal-->
                        <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Do you submit this：<span id="idty_title"></span>&nbsp?</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <div class="modal-body px-5">
                                        <!-- 第二排的功能 : 搜尋功能 -->
                                        <div class="row unblock" id="forwarded">
                                            <div class="col-12" id="searchUser_table">
                                                <div class="input-group search" id="select_inSign_Form">
                                                    <!-- <button type="button" class="btn btn-outline-secondary form-label" onclick="resetMain();">清除</button> -->
                                                    <span class="input-group-text form-label">轉呈</span>
                                                    <input type="text" name="in_sign" id="in_sign" class="form-control" placeholder="請輸入工號"
                                                            aria-label="請輸入查詢對象工號" onchange="search_fun(this.id, this.value);">
                                                    <div id="in_sign_badge"></div>
                                                    <input type="hidden" name="in_signName" id="in_signName" class="form-control">
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="updated_emp_id"  id="updated_emp_id" value="<?php echo $auth_emp_id;?>">
                                        <input type="hidden" name="uuid"            id="uuid"           value="<?php echo $receive_row['uuid'];?>">
                                        <input type="hidden" name="fab_sign_code"   id="fab_sign_code"  value="<?php echo $receive_row['fab_sign_code'];?>">
                                        <input type="hidden" name="action"          id="action"         value="<?php echo $action;?>">
                                        <input type="hidden" name="step"            id="step"           value="<?php echo $step;?>">
                                        <input type="hidden" name="idty"            id="idty"           value="">
                                        <input type="hidden" name="old_idty"        id="old_idty"       value="<?php echo $receive_row['idty'];?>">
                                        <?php if($sys_role <= 3){ ?>
                                            <button type="submit" name="receive_submit" value="Submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> Agree</button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <!-- 尾段logs訊息 -->
                    <div class="col-12 pt-0 rounded bg-light" id="logs_div">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                表單記錄：
                            </div>
                            <div class="col-6 col-md-6">
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
                                            <th>Comment</th>
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

                <!-- 尾段：deBug訊息 -->
                <?php if(isset($_REQUEST["debug"])){
                    include("debug_board.php"); 
                } ?>
                
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
    var catalogs             = <?=json_encode($catalogs);?>;                 // 第一頁：info modal function 引入catalogs資料
    var action               = '<?=$action;?>';                              // Edit選染    // 引入action資料
    var receive_row          = <?=json_encode($receive_row);?>;              // Edit選染    // 引入receive_row資料作為Edit
    var receive_collect_role = '<?=$receive_collect_role?>';                 // collect選染 // 引入receive_row_發放人權限作為渲染標記
    var json                 = JSON.parse('<?=json_encode($logs_arr)?>');    // 鋪設logs紀錄
    var receive_url          = '<?=$receive_url;?>';                         // push訊息    // 本文件網址
</script>

<script src="receive_show.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>