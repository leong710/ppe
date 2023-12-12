<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 複製本頁網址藥用
    $trade_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // 回本頁
    if(isset($_SERVER["HTTP_REFERER"])){
        $up_href = $_SERVER["HTTP_REFERER"];            // 回上頁
    }else{
        $up_href = $trade_url;                          // 回本頁
    }

    $auth_emp_id    = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_id_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用
    $sys_id_fab_id  = $_SESSION[$sys_id]["fab_id"];     
    $sys_id_sfab_id = $_SESSION[$sys_id]["sfab_id"];    

    // 決定表單開啟方式
    if(isset($_REQUEST["action"])){
        $action = $_REQUEST["action"];              // 有action就帶action
    }else{
        $action = 'review';                         // 沒有action就新開單
    }

    if(!empty($_REQUEST["id"])){
        $trade_row = show_trade($_REQUEST);                 // 讀取表單
        if(empty($trade_row)){
            echo "<script>alert('id-error：{$_REQUEST["id"]}')</script>";
            header("refresh:0;url=index.php");
            exit;
        }
        // logs紀錄鋪設前處理 
        $logs_dec = json_decode($trade_row["logs"]);
        $logs_arr = (array) $logs_dec;
    }else{
        $trade_row = array( "id" => "" );                   // 預設trade_row[id]=空array
        $logs_arr = [];                                     // 預設logs_arr=空array
        $action = 'create';                                 // 因為沒有id，列為新開單，防止action outOfspc
    }

    if($trade_row["form_type"] != "import"){                // import=入庫、export=出庫；僅針對入庫做特別管理，因為入庫是PO號碼!不是local_id

        if(!empty($trade_row["out_local"])){                    // edit trade表單，get已選擇出庫廠區站點
            $query_local = array(
                'local_id' => $trade_row["out_local"]
            );
            $select_local = select_local($query_local);         // 讀出已被選擇出庫廠區站點的器材存量限制
        
        }else{
            $select_local = array('id' => '');
            
        }
    }

    if(!empty($trade_row["in_local"])){                    // edit trade表單，get已選擇出庫廠區站點

        $query_in_local = array(
            'local_id' => $trade_row["in_local"]
        );
        $select_in_local = select_local($query_in_local);   // 讀出已被選擇入庫廠區站點的器材存量限制
    
        // $catalogs = show_local_stock($query_local);      // 後來改用這個讀取catalog清單外加該local的儲存量，作為需求首頁目錄
        $catalogs = show_catalogs();                        // 後來改用這個讀取catalog清單，作為需求首頁目錄

    }else{
        $select_in_local = array('id' => '');
        $catalogs = [];
    }
    
    $fab_o_id = $trade_row["fab_o_id"];                 // 取表單上出貨的fab_id
    $fab_i_id = $trade_row["fab_i_id"];                 // 取表單上收貨的fab_id

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
        if(isset($trade_row["in_user_id"]) && ($trade_row["out_user_id"] == $auth_emp_id)){
            $step_index = '1';      // 申請人
        }      

        if($trade_row["idty"] < 10){            // ** 未交貨後的頭銜
            // 表單交易狀態：0完成/1待收/2退貨/3取消/12發貨
            switch($trade_row["idty"]){
                case 0 :   // $act = '同意 (Approve)';
                    break;
                case 1 :   // $act = '送出 (Submit)';
                    if(( $fab_i_id == $sys_id_fab_id) || (in_array($fab_i_id, $sys_id_sfab_id))){
                        $step_index = '7';      // ppe site user
                    }
                    break;
                case 2 :   // $act = '退回 (Reject)';
                case 3 :   // $act = '作廢 (Abort)'; 
                case 4 :   // $act = '編輯 (Edit)';  
                case 5 :   // $act = '轉呈 (Forwarded)';
                case 6 :   // $act = '暫存 (Save)';  
                default:    // $act = '錯誤 (Error)';         
                    break;
            }

        } else if($trade_row["idty"] >= 10){   // ** 已交貨後的頭銜
            // 表單交易狀態：0完成/1待收/2退貨/3取消/12發貨
            switch($trade_row["idty"]){
                case 10 :  // $act = '結案 (Close)'; 
                case 11 :  // $act = '承辦 (Undertake)';
                case 12 :  // $act = '待收發貨 (Awaiting collection)'; 
                case 13 :  // $act = '交貨 (Delivery)';
                default:    // $act = '錯誤 (Error)';         
                    break;
            }
        }

        if(!isset($step_index)){
            if(!isset($sys_id_role) || ($sys_id_role) == 3){
                $step_index = '6';}      // normal
            if(isset($sys_id_role)){
                if($sys_id_role == 2){
                    $step_index = '7';}      // PPE窗口
                if($sys_id_role == 1){
                    $step_index = '8';}      // PPEpm
                if($sys_id_role == 0){
                    $step_index = '9';}      // 系統管理員
            }
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
                        <h3><i class="fa-solid fa-2"></i>&nbsp<b><?php echo ($trade_row["form_type"] == "import") ? "請購入庫":"調撥出庫" ?></b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-4 py-0 t-center">
                        <?php 
                            echo "<h3><span class='badge rounded-pill ";
                                switch($trade_row['idty']){
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
                            // echo "<sup> ".$trade_row['idty']."</sup>";
                            // echo " ... ".$step;
                            echo "</span></h3>";
                        ?>
                    </div>
                    <div class="col-12 col-md-4 py-0 text-end">
                        <button type="button" class="btn btn-secondary" onclick="location.href='index.php'"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回上頁</button>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-4">
                        出入單號：<?php echo ($trade_row['id'])          ? "trade_aid_".$trade_row['id'] : "(尚未給號)";?></br>
                        開單日期：<?php echo ($trade_row['out_date'])    ? $trade_row['out_date'] : date('Y-m-d H:i')."&nbsp(實際以送出時間為主)";?></br>
                        填單人員：<?php echo ($trade_row["out_user_id"]) ? $trade_row["out_user_id"]." / ".$trade_row["cname_o"] : $auth_emp_id." / ".$_SESSION["AUTH"]["cname"];?>
                    </div>
                    <div class="col-12 col-md-8 text-end">
                        <?php if($trade_row['idty'] == 1){  // 1.簽核中 ?>
                            <?php if(($trade_row["in_local"] == $sys_id_fab_id || in_array($trade_row["in_local"], $sys_id_sfab_id)) || $sys_id_role <= 1 ){ ?>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitModal" value="0" onclick="submit_item(this.value, this.innerHTML);">同意 (Approve)</button>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#submitModal" value="2" onclick="submit_item(this.value, this.innerHTML);">退回 (Reject)</button>
                        <?php } } ?>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 p-0">
                    <!-- 內頁 -->
                    <form action="store.php" method="post">
                    <!-- <form action="./zz/debug.php" method="post"> -->

                        <!-- 3.申請單成立 -->
                        <div class="bg-white rounded" id="nav-review" >
                            <div class="col-12 py-3 px-5">
                                <div class="row">
                                    <!-- 表頭 -->
                                    <div class="col-6 col-md-6">
                                        <b>批量調撥相關資訊：</b>
                                        <button type="button" id="info_btn" class="op_tab_btn" value="info" onclick="op_tab(this.value)" title="訊息收折"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="col-6 col-md-6 text-end">
                                        <!-- 限定表單所有人：開單人、ppe pm、admin -->
                                        <?php if( $trade_row['out_user_id'] == $auth_emp_id || $sys_id_role <= 1 || (( $fab_o_id == $sys_id_fab_id) || (in_array($fab_o_id, $sys_id_sfab_id)))){ ?>
                                            <!-- 表單狀態：2退回 4編輯 6暫存 -->
                                            <?php if(in_array($trade_row['idty'], [ 2, 4, 6 ])){ ?>
                                                <?php if($trade_row["form_type"] == "import"){ ?>
                                                    <a href="restock.php?id=<?php echo $trade_row['id'];?>&action=edit" class="btn btn-primary" title="restock">編輯 (Edit)</a>
                                                <?php }else if($trade_row["form_type"] == "export"){ ?>
                                                    <a href="form.php?id=<?php echo $trade_row['id'];?>&action=edit" class="btn btn-primary" title="form">編輯 (Edit)</a>
                                                <?php }?>
                                            <?php ;} ?>
                                            <!-- 表單狀態：2退回 4編輯 6暫存 -->
                                            <?php if(in_array($trade_row['idty'], [ 2, 4, 6 ])){ ?>
                                                <button type="button" class="btn bg-warning text-dark" data-bs-toggle="modal" data-bs-target="#submitModal" value="3" onclick="submit_item(this.value, this.innerHTML);">作廢 (Abort)</button>
                                            <?php ;} ?>
                                        <?php ;} ?>
                                    </div>
                                    <hr>
                                    <!-- 相關資訊說明 -->
                                    <div class="col-12 py-1" id="info_table"> 
                                        <!-- 表列1 站點 -->
                                        <div class="row">
                                            <!-- 左側：out_local/出庫廠區 -->
                                            <div class="col-12 col-md-6 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" readonly
                                                        <?php if($trade_row["form_type"] != "import"){ ?>
                                                            value="<?php echo $select_local['id'].'：'.$select_local['site_title'].' '.$select_local['fab_title'].'_'.$select_local['local_title']; 
                                                                    echo ($select_local['flag'] == 'Off') ? '(已關閉)':''; ?>"
                                                        <?php } else { ?>
                                                            value="<?php echo $trade_row["out_local"]; ?>"
                                                        <?php } ?>
                                                    >
                                                    <label for="out_local" class="form-label">out_local/出庫廠區：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <!-- 表頭：in_local/需求廠區 -->
                                            <div class="col-12 col-md-6 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" readonly
                                                        value="<?php echo $select_in_local['id'].'：'.$select_in_local['site_title'].' '.$select_in_local['fab_title'].'_'.$select_in_local['local_title']; 
                                                            echo ($select_in_local['flag'] == 'Off') ? '(已關閉)':''; ?>">
                                                    <label for="in_local" class="form-label">in_local/需求廠區：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 表列2 備註說明 -->
                                        <div class="row">
                                            <div class="col-12 col-md-12 py-2 px-2">
                    
                                            </div>
                                            <hr>
                                            <div class="col-12 py-1">
                                                <b>備註：</b>
                                                </br>&nbsp1.填入申請人工號、姓名、需求廠區、需求類別、器材數量。
                                                </br>&nbsp2.簽核：申請人(出貨人)=>收貨人=>驗收=>表單結案。 
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
                                                        <th>數量 / 單位</th>
                                                        <th>PO no</th>
                                                        <th>批號/效期</th>
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
                                            <label for="sign_comm" class="form-check-label" >command：</label>
                                            <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                            <input type="hidden" name="updated_emp_id"  id="updated_emp_id" value="<?php echo $auth_emp_id;?>">
                                            <input type="hidden" name="id"              id="id"             value="">
                                            <input type="hidden" name="action"          id="action"         value="<?php echo $action;?>">
                                            <input type="hidden" name="step"            id="step"           value="<?php echo $step;?>">
                                            <input type="hidden" name="form_type"       id="form_type"      value="">
                                            <input type="hidden" name="idty"            id="idty"           value="">
                                            <input type="hidden" name="old_idty"        id="old_idty"       value="<?php echo $trade_row["idty"];?>">
                                            <?php if($sys_id_role <= 2){ ?>
                                                <button type="submit" value="Submit" name="trade_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> Agree</button>
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
                                            <th >Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div style="font-size: 6px;" class="text-end">
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
    
    var action = '<?=$action;?>';                                   // 引入action資料
    var catalogs = <?=json_encode($catalogs);?>;                    // 引入catalogs資料
    var trade_row = <?=json_encode($trade_row);?>;                  // 引入trade_row資料作為Edit
    var json = JSON.parse('<?=json_encode($logs_arr)?>');           // 鋪設logs紀錄

</script>

<script src="trade_show.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>