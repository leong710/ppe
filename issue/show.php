<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 複製本頁網址藥用
    $issue_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // 回本頁
    if(isset($_SERVER["HTTP_REFERER"])){
        $up_href = $_SERVER["HTTP_REFERER"];            // 回上頁
    }else{
        $up_href = $issue_url;                          // 回本頁
    }
    
    $auth_cname  = $_SESSION["AUTH"]["cname"];          // 取出$_session引用
    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];         // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];          // 取出$_session引用
    $sys_fab_id  = $_SESSION[$sys_id]["fab_id"];     
    $sys_sfab_id = $_SESSION[$sys_id]["sfab_id"];    

    // 決定表單開啟方式
    if(isset($_REQUEST["action"])){
        $action = $_REQUEST["action"];                  // 有action就帶action
    }else{
        $action = 'review';                             // 沒有action就新開單
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

    if(!empty($issue_row["in_local"])){                    // edit issue表單，get已選擇出庫廠區站點
        $query_local = array(
            'local_id' => $issue_row["in_local"]
        );
        $select_in_local = select_local($query_local);   // 讀出已被選擇出庫廠區站點的器材存量限制

    }else{
        $select_in_local = array('id' => '');
    }

    $pm_emp_id = $issue_row["fab_i_pm_emp_id"];         // *** 廠區業務窗口
    $pm_emp_id_arr = explode(",",$pm_emp_id);       // 資料表是字串，要炸成陣列

    $catalogs = show_catalogs();                    // 器材=All
    // $allLocals = show_allLocal();                   // 所有儲存站點
    // $categories = show_categories();                // 分類
    // $sum_categorys = show_sum_category();           // 統計分類與數量

    // $fab_o_id = $issue_row["fab_o_id"];                 // 取表單上出貨的fab_id
    $fab_i_id = $issue_row["fab_i_id"];                 // 取表單上收貨的fab_id

        // 身份陣列
        $step_arr = [
            '0' => '填單人',
            '1' => '申請人',
            '2' => '申請人主管',
            '3' => 'ppe發放人',            // 1.依廠區需求可能非一人簽核權限 2.發放人有調整發放數量後簽核權限
            '4' => '業務承辦',
            '5' => '環安主管',
            '6' => 'normal',
            '7' => 'PPE窗口',
            '8' => 'PPEpm',
            '9' => '系統管理員',
            '10'=> '轉呈簽核'
        ];

        // 決定表單開啟 $step身份
        if($issue_row["idty"] < 10){            // ** 未交貨後的頭銜
            // 表單交易狀態：0完成/1待收/2退貨/3取消/12發貨
            switch($issue_row["idty"]){
                case "0":   // $act = '同意/待PR';
                case "1":   // $act = '送出 (Submit)';
                    // if(( $fab_i_id == $sys_fab_id) || (in_array($fab_i_id, $sys_sfab_id)) && ($issue_row["in_user_id"] == $auth_emp_id) ){
                    //     $step_index = '7';      // ppe site user
                    // }

                    if($issue_row["in_sign"] == $auth_emp_id){
                        if($issue_row["flow"] == "Manager"){  
                            $step_index = '2';      // 2.申請人主管
                        } else if($issue_row["flow"] == "Forwarded"){ 
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
                    if($issue_row["created_emp_id"] == $auth_emp_id){
                        $step_index = '0';      // 0.填單人
                    } else if($issue_row["in_user_id"] == $auth_emp_id){
                        $step_index = '1';      // 1.申請人
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
        } else if($issue_row["idty"] >= 10){    // ** 已交貨後的頭銜
            // 表單交易狀態：0完成/1待收/2退貨/3取消/12發貨
            switch($issue_row["idty"]){
                case "10":                      // $act = '結案 (Close)'; 
                    break;
                case "11":                      // 11已PR待交貨;
                    if($issue_row["created_emp_id"] == $auth_emp_id){
                        $step_index = '0';      // 0.填單人
                    } else if($issue_row["in_user_id"] == $auth_emp_id){
                        $step_index = '1';      // 1.申請人
                    } 
                    
                    if($sys_role == 2){
                        $step_index = '7';      // 7.PPE窗口
                    } else if($sys_role == 1){
                        $step_index = '8';      // 8.PPEpm
                    } else if($sys_role == 0){
                        $step_index = '9';      // 9.系統管理員
                    }

                    // if($issue_row["fab_i_id"] == $sys_fab_id){
                    //     $step_index = '4';      // 4.業務承辦
                    // }else if($issue_row["in_sign"] == $auth_emp_id){
                    //     $step_index = '5';      // 5.環安主管
                    // }
                    break;
                case "12":                      // $act = '待收發貨 (Awaiting collection)'; 
                    if($issue_row['flow'] == 'collect' && in_array($issue_row["fab_i_id"], $sys_sfab_id)){
                        $step_index = '3';      // 3.ppe發放人
                    } 
                    break;
                case "13":                      // $act = '交貨 (Delivery)';
                    if($issue_row["fab_i_id"] == $sys_fab_id){
                        $step_index = '1';      // 1.申請人
                    }  
                    break;
                default:    // $act = '錯誤 (Error)';         
                    $step_index = '6';      // 6.normal'
                    break;
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
                        <h3><i class="fa-solid fa-1"></i>&nbsp<b>請購需求</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-4 py-0 t-center">
                        <?php 
                            echo "<h3><span class='badge rounded-pill ";
                                switch($issue_row['idty']){
                                    case "0" : echo "bg-info'>待轉";                break;
                                    case "1" : echo "bg-primary'>待簽";             break;
                                    case "2" : echo "bg-warning text-dark'>退回";   break;
                                    case "3" : echo "bg-dark'>取消";                break;
                                    case "10": echo "bg-secondary'>結案";           break;
                                    case "11": echo "bg-primary'>待交貨";           break;
                                    case "12": echo "bg-success'>待收";             break;
                                    case "13": echo "bg-danger'>待驗收";            break;
                                    default  : echo "'>na";                         break; 
                                }
                            // echo "<sup> ".$trade_row['idty']."</sup>";
                            echo !empty($issue_row['in_sign']) ? "：".$issue_row['in_sign']." " :"";
                            echo !empty($issue_row['flow']) ? " / ".$issue_row['flow']." " :"";
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
                        需求單號：<?php echo ($issue_row['id'])          ? "issue_aid_".$issue_row['id'] : "(尚未給號)"; ?></br>
                        開單日期：<?php echo ($issue_row['create_date']) ? $issue_row['create_date'] : date('Y-m-d H:i')."&nbsp(實際以送出時間為主)"; ?></br>
                        填單人員：<?php echo ($issue_row["created_emp_id"])  ? $issue_row["created_emp_id"]." / ".$issue_row["created_cname"] : $auth_emp_id." / ".$auth_cname;?>
                    </div>
                    <div class="col-12 col-md-8 text-end">
                        <?php if( (($issue_row['idty'] == 1) && ($issue_row['in_sign'] == $auth_emp_id)) || $sys_role <= 1 ){ ?>
                            <?php if(in_array($issue_row['idty'], [ 1 ])){  // 1.簽核中  ?>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitModal" value="0" onclick="submit_item(this.value, this.innerHTML);">同意 (Approve)</button>
                                <?php if( ($issue_row["flow"] == "Manager")  ){ ?>
                                    <button type="button" class="btn btn-info"    data-bs-toggle="modal" data-bs-target="#submitModal" value="5" onclick="submit_item(this.value, this.innerHTML);">轉呈 (Forwarded)</button>
                                <?php } ?>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#submitModal" value="2" onclick="submit_item(this.value, this.innerHTML);">退回 (Reject)</button>
                        <?php } } ?>
                        <?php // 這裡取得發放權限 idty=12.待領、待收 => 13.交貨 (Delivery)
                            // $issue_role = ($issue_row["fab_i_id"] == $_SESSION[$sys_id]["fab_id"] ) || in_array($issue_row["fab_i_id"], $_SESSION[$sys_id]["sfab_id"]); 
                            $issue_collect_role = ($issue_row['idty'] == 11 && $issue_row['flow'] == 'collect' && in_array($issue_row["fab_i_id"], $sys_sfab_id)); 
                            if($issue_collect_role){ ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal" value="13" onclick="submit_item(this.value, this.innerHTML);">交貨 (Delivery)</button>
                        <?php } ?>

                        <?php // 承辦+主管簽核選項 idty=13.交貨delivery => 申請人驗收 (acceptance) => 10.結案 (Close);
                            $issue_delivery_role = ($issue_row['flow'] == 'acceptance' && (in_array($auth_emp_id, $pm_emp_id_arr) || $sys_role <= 1));
                            if( $issue_row['idty'] == 13 && $issue_delivery_role){ ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal" value="10" onclick="submit_item(this.value, this.innerHTML);">申請人同意 (Approve)</button>
                        <?php } ?>

                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 p-0">
                    <!-- 內頁 -->
                    <form action="store.php" method="post">
                    <!-- <form action="./zz/debug.php" method="post"> -->

                        <!-- 3.申請單成立 -->
                        <div class="tab-pane bg-white rounded fade show active" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                            <div class="col-12 py-3 px-5">
                                <div class="row">
                                    <!-- 表頭 -->
                                    <div class="col-6 col-md-6">
                                        <b>請購相關資訊：</b>
                                        <button type="button" id="info_btn" class="op_tab_btn" value="info" onclick="op_tab(this.value)" title="訊息收折"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="col-6 col-md-6 text-end">
                                        <!-- 限定表單所有人：created_emp_id開單人、emp_id申請人、ppe pm、admin -->
                                        <?php if( ($sys_role <= 1) || ($issue_row['in_user_id'] == $auth_emp_id) || ($issue_row['created_emp_id'] == $auth_emp_id) ){ ?> 
                                            <!-- 表單狀態：2退回 4編輯 6暫存 -->
                                            <?php if(in_array($issue_row['idty'], [ 2, 4, 6 ])){ ?>
                                                <a href="form.php?id=<?php echo $issue_row['id'];?>&action=edit" class="btn btn-primary">編輯 (Edit)</a>
                                            <?php ;} ?>
                                            <!-- 表單狀態：1送出 2退回 4編輯 5轉呈 6暫存 -->
                                            <?php if(in_array($issue_row['idty'], [ 1, 2, 4, 5, 6 ])){ ?>
                                                <button type="button" class="btn bg-warning text-dark" data-bs-toggle="modal" data-bs-target="#submitModal" value="3" onclick="submit_item(this.value, this.innerHTML);">作廢 (Abort)</button>
                                            <?php ;} ?>
                                        <?php ;} ?>
                                        <?php if( (($issue_row['idty'] == 11 && $issue_row['flow'] == 'collect') || ($issue_row['idty'] == 13 && $issue_row['flow'] == 'acceptance'))  // 11待發放、13待驗收
                                                    && (in_array($issue_row["fab_i_id"], $sys_sfab_id) || in_array($auth_emp_id, [$issue_row['in_user_id'], $issue_row['created_emp_id']])) ){ ?>
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
                                                    <input type="text" name="in_user_id" id="in_user_id" class="form-control" required placeholder="工號" readonly >
                                                    <label for="in_user_id" class="form-label">emp_id/工號：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="cname" id="cname_i" class="form-control" required placeholder="申請人姓名" readonly >
                                                    <label for="cname_i" class="form-label">cname/申請人姓名：<sup class="text-danger"> *</sup></label>
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
                                                        value="<?php echo $select_in_local['id'].'：'.$select_in_local['site_title'].' '.$select_in_local['fab_title'].'_'.$select_in_local['local_title']; 
                                                            echo ($select_in_local['flag'] == 'Off') ? '(已關閉)':''; ?>">
                                                    <label for="in_local" class="form-label">in_local/需求廠區：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4 py-1 px-2">
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
                                                </br>&nbsp1.填入申請人工號、姓名、需求廠區、需求類別、器材數量。
                                                </br>&nbsp2.簽核：申請人1=>承辦人0=>PR待轉11=>轉PR==>11交貨13==>驗收12+16=>表單結案10。 
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
                            <div class="modal-dialog modal-dialog-scrollable modal-l">

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
                                            <!-- 第二排的功能 -->
                                            <div class="row">
                                                <div class="col-12 py-0 block" id="po_no_form">
                                                </div>
                                                <div class="col-12 py-0">
                                                    <label for="sign_comm" class="form-check-label" >command：</label>
                                                    <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $auth_cname;?>">
                                            <input type="hidden" name="updated_emp_id"  id="updated_emp_id" value="<?php echo $auth_emp_id;?>">
                                            <input type="hidden" name="id"              id="id"             value="">
                                            <input type="hidden" name="fab_sign_code"   id="fab_sign_code"  value="<?php echo $issue_row['fab_i_sign_code'];?>">
                                            <input type="hidden" name="action"          id="action"         value="<?php echo $action;?>">
                                            <input type="hidden" name="step"            id="step"           value="<?php echo $step;?>">
                                            <input type="hidden" name="idty"            id="idty"           value="">
                                            <input type="hidden" name="old_idty"        id="old_idty"       value="<?php echo $issue_row["idty"];?>">
                                            <?php if($sys_role <= 2){ ?>
                                                <button type="submit" value="Submit" name="issue_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> Agree</button>
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
                <?php 
                    // if(isset($_REQUEST["debug"])){
                        include("debug_board.php"); 
                    // } 
                ?>
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

    var catalogs            = <?=json_encode($catalogs);?>;                 // 引入catalogs資料
    var action              = '<?=$action;?>';                              // 引入action資料
    var issue_row           = <?=json_encode($issue_row);?>;                // 引入issue_row資料作為Edit
    var issue_collect_role  = '<?=$issue_collect_role?>';                   // collect選染 // 引入issue_row_發放人權限作為渲染標記
    var json                = JSON.parse('<?=json_encode($logs_arr)?>');    // 鋪設logs紀錄
    var issue_url           = '<?=$issue_url;?>';                           // push訊息 // 本文件網址

</script>

<script src="issue_show.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>