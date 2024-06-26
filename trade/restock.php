<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("function.php");
    accessDenied($sys_id);

    $sys_id_role = $sys_role;          // 取出$_session引用

        // 刪除表單
        if(isset($_POST["delete_trade"])){
            $check_delete_result = delete_trade($_REQUEST);
            if($check_delete_result){
                echo "<script>alert('trade進貨申請單 -- 已刪除');</script>";
                header("refresh:0;url=index.php");
                exit;		// 請注意
            }else{
                echo "<script>alert('trade領用申請單 -- 刪除失敗!!');</script>";
            }
        }

        // if(isset($_POST["delete_log"])){ updateLogs($_REQUEST); } // 更新log

    // 決定表單開啟方式
    $action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : 'create';   // 有action就帶action，沒有action就新開單

    if(isset($_REQUEST["id"])){
        $trade_row = show_trade($_REQUEST);
        if(empty($trade_row)){
            echo "<script>alert('id-error：{$_REQUEST["id"]}')</script>";
            header("refresh:0;url=index.php");
            exit;
        }
        // logs紀錄鋪設前處理 
        $logs_dec = json_decode($trade_row["logs"]);
        $logs_arr = (array) $logs_dec;

    }else{
        $trade_row = array( "id" => "" );           // 預設trade_row[id]=空array
        $logs_arr = [];                             // 預設logs_arr=空array
        $action = 'create';                         // 因為沒有id，列為新開單，防止action outOfspc
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
            '3' => 'ppe發放人',            // 1.依廠區需求可能非一人簽核權限 2.發放人有調整發放數量後簽核權限
            '4' => '業務承辦',
            '5' => '環安主管',

            '6' => 'normal',
            '7' => 'ppe窗口',
            '8' => 'ppe pm',
            '9' => '系統管理員',
            '10'=> '轉呈簽核'
        ];

        // 決定表單開啟 $step身份
        if(isset($trade_row["created_emp_id"]) && ($trade_row["created_emp_id"] == $auth_emp_id)){
            $step_index = '0';}             // 填單人
        if(isset($trade_row["emp_id"]) && ($trade_row["emp_id"] == $auth_emp_id)){
            $step_index = '1';}             // 申請人
        if(isset($trade_row["omager"]) && ($trade_row["omager"] == $auth_emp_id)){
            $step_index = '2';}             // 申請人主管
        
        if(empty($step_index)){
            if(!isset($sys_id_role) ||($sys_id_role) == 3){
                $step_index = '6';}         // noBody
            if(isset($sys_id_role) && ($sys_id_role) == 2){
                $step_index = '7';}         // ppe site user
            if(isset($sys_id_role) && ($sys_id_role) == 1){
                $step_index = '8';}         // ppe pm
            if(isset($sys_id_role) && ($sys_id_role) == 0){
                $step_index = '9';}         // 系統管理員
            if($action == 'create'){
                $step_index = '0';}         // 填單人
        }
        
        // $step套用身份
        $step = $step_arr[$step_index];
    // }

?>

<?php include("../template/header.php"); ?>
<!-- <php include("../template/nav.php"); ?> -->
<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
        <link rel="stylesheet" type="text/css" href="../../libs/dataTables/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="../../libs/dataTables/jquery.dataTables.js"></script>
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script src="../../libs/jquery/mloading_init.js"></script>

</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-11 border rounded px-3 py-4" style="background-color: #D4D4D4;">
                <!-- 表頭1 -->
                <div class="row px-2">
                    <div class="col-12 col-md-6 py-0">
                        <h3><i class="fa-solid fa-2"></i>&nbsp<b>其他入庫</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <button type="button" class="btn btn-secondary" onclick="return confirm('確認返回？') && closeWindow()"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        出入單號：<?php echo ($action == 'create') ? "(尚未給號)": "trade_aid_".$trade_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$trade_row['out_date']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $auth_emp_id." / ".$_SESSION["AUTH"]["cname"] : $trade_row["out_user_id"];?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <?php if(($sys_id_role <= 1 ) && (isset($trade_row['idty']) && $trade_row['idty'] != 0)){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $trade_row["id"];?>">
                                <input type="submit" name="delete_trade" value="刪除 (Delete)" title="刪除申請單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
                            </form>
                        <?php }?>
                    </div>
                </div>
    
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
                                                <th>需求&nbsp<i class="fa-solid fa-cart-plus"></i>&nbsp購物車</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($catalogs as $catalog){ ?>
                                                <tr>
                                                    <td class="unblock"><?php echo $catalog["cate_no"];?></td>
                                                    <td><img src="../catalog/images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></td>
                                                    <td style="text-align: left;">
                                                        <button type="button" id="cata_info_<?php echo $catalog['SN'];?>" value="<?php echo $catalog['SN'];?>" data-bs-toggle="modal" data-bs-target="#cata_info" 
                                                            class="cata_info_btn" onclick="info_module('catalogs',this.value);"><h5><b><?php echo $catalog["pname"];?></b></h5></button>
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
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" id="<?php echo $catalog['SN'];?>" class="form-control amount t-center"
                                                                placeholder="數量" min="0" max="999" maxlength="3" oninput="if(value.length>3)value=value.slice(0,3)">
                                                            <button type="button" name="<?php echo $catalog['SN'];?>" id="add_<?php echo $catalog['SN'];?>" class="btn btn-outline-secondary add_btn" value="" 
                                                                title="加入購物車" onclick="add_item(this.name,this.value,'off');"><i class="fa fa-plus"></i></button>
                                                        </div>
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
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">器材用品/數量單位：<sup class="text-danger"> *</sup></label>
                                        </div>
                                        <div class="col-12 col-md-6 text-end">
                                            <button type="button" id="load_excel_btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#load_excel"><i class="fa fa-upload" aria-hidden="true"></i> 上傳Excel檔</button>
                                        </div>
                                    </div>
                                    <div class=" rounded border bg-light" id="shopping_cart">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>select</th>
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
                                        <div class="col-6 col-md-6 px-2">
                                            請申請人填入相關資料：
                                        </div>
                                        <div class="col-6 col-md-6 px-2 text-end">
                                            <?php if($sys_id_role <= 2){ ?>
                                                <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                                            <?php } ?>
                                            <button type="button" class="btn btn-secondary" onclick="return confirm('確認返回？') && closeWindow()"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
                                        </div>
                                        <hr>
                                    </div>

                                    <!-- 表列3 進貨站點 -->
                                    <div class="row p-1 rounded bg-warning">
                                        
                                        <div class="col-6 col-md-6 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="po_no" id="po_no" class="form-control t-center" placeholder="請填PO編號" maxlength="12" required>
                                                <label for="po_no" class="form-label">PO編號：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>

                                        <div class="col-6 col-md-6 px-2">
                                            <div class="form-floating">
                                                <select name="in_local" id="in_local" class="form-control" required>
                                                    <option value="" selected hidden>--請選擇 進貨 儲存點--</option>
                                                    <?php foreach($allLocals as $allLocal){ ?>
                                                        <?php if($sys_id_role <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                                            <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" >
                                                                <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                                <label for="in_local" class="form-label">進貨廠區：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>

                                        <div class="col-12 px-2">
                                            <label for="out_remark" class="form-check-label">備註說明：</label>
                                            <textarea name="remark" id="remark" class="form-control" rows="2">(其他入庫)</textarea>
                                        </div>

                                    </div>
                                    
                                    <!-- 表列5 說明 -->
                                    <div class="row">
                                        <hr>
                                        <div class="col-12 py-1">
                                            備註：
                                            </br>&nbsp1.填入申請單位、部門名稱、申請日期、器材、數量及用途說明。
                                            </br>&nbsp2.簽核：申請人=>申請部門三級主管=>環安單位承辦人=>環安單位(課)主管=>發放人及進貨人=>各廠環安單位存查3年。 
                                            </br>&nbsp3.需求類別若是[緊急]，必須說明事故原因，並通報防災中心。 
                                            </br>&nbsp4.以上若有填報不實，將於以退件。 
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-6 col-md-6 py-1 px-2">
                                            
                                        </div>
                                        <div class="col-6 col-md-6 py-1 px-2 text-end">
                                            <?php if($sys_id_role <= 3){ ?>
                                                <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                                            <?php } ?>
                                            <button type="button" class="btn btn-secondary" onclick="return confirm('確認返回？') && closeWindow()"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
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
                                        <h5 class="modal-title">Do you submit this 進貨申請：</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-5">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="cname"       id="cname"      value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                        <input type="hidden" name="out_user_id" id="out_user_id" value="<?php echo $auth_emp_id;?>">
                                        <input type="hidden" name="stock_remark"                value="(PO編號)">
                                        <input type="hidden" name="form_type"   id="form_type"  value="import">
                                        <input type="hidden" name="action"      id="action"     value="<?php echo $action;?>">
                                        <input type="hidden" name="idty"        id="idty"       value="1">
                                        <input type="hidden" name="step"        id="step"       value="<?php echo $step;?>">

                                        <input type="hidden" name="updated_user" id="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                        <input type="hidden" name="id"          id="id"         value="">
                                        <?php if($sys_id_role <= 2){ ?>
                                            <button type="submit" value="Submit" name="trade_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
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
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div style="font-size: 6px;" class="text-end">
                                logs-end
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="liveToast" class="toast align-items-center bg-warning" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="500">
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    <!-- 互動視窗 load_excel -->
        <div class="modal fade" id="load_excel" tabindex="-1" aria-labelledby="load_excel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">上傳Excel檔：</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form name="excelInput" action="../_Format/upload_excel.php" method="POST" enctype="multipart/form-data" target="api" onsubmit="return restockExcelForm()">
                        <div class="modal-body px-4">
                            <div class="row">
                                <div class="col-6 col-md-8 py-0">
                                    <label for="excelFile" class="form-label">需求清單 <span>&nbsp<a href="../_Format/restock_example.xlsx" target="_blank">上傳格式範例</a></span> 
                                        <sup class="text-danger"> * 限EXCEL檔案</sup></label>
                                    <div class="input-group">
                                        <input type="file" name="excelFile" id="excelFile" style="font-size: 16px; max-width: 350px;" class="form-control form-control-sm" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                        <button type="submit" name="excelUpload" id="excelUpload" class="btn btn-outline-secondary" value="stock">上傳</button>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 py-0">
                                    <p id="warningText" name="warning" >＊請上傳需求單Excel檔</p>
                                    <p id="sn_list" name="warning" >＊請確認Excel中的資料</p>
                                </div>
                            </div>
                                
                            <div class="row" id="excel_iframe">
                                <iframe id="api" name="api" width="100%" height="30" style="display: none;" onclick="restockExcelForm()"></iframe>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="import_excel_btn" class="btn btn-success unblock" data-bs-dismiss="modal">載入</button>
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">返回</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
        <div id="gotop">
            <i class="fas fa-angle-up fa-2x"></i>
        </div>
    
</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/openUrl/openUrl.js"></script>           <!-- 彈出子畫面 -->

<script>
// 開局設定init
    var action      = '<?=$action?>';                                // Edit選染 // 引入action資料
    var catalogs    = <?=json_encode($catalogs)?>;                   // 第一頁：info modal function 引入catalogs資料
    var trade_row   = <?=json_encode($trade_row)?>;                  // Edit選染 // 引入trade_row資料作為Edit
    var json        = <?=json_encode($logs_arr)?>;                   // 鋪設logs紀錄 240124-改去除JSON.parse
    var id          = '<?=$trade_row["id"]?>';                       // 鋪設logs紀錄
    
// 以下為控制 iframe
    var realName         = document.getElementById('realName');           // 上傳後，JSON存放處(給表單儲存使用)
    var iframe           = document.getElementById('api');                // 清冊的iframe介面
    var warningText      = document.getElementById('warningText');        // 清冊未上傳的提示
    var sn_list          = document.getElementById('sn_list');            // 清冊中有誤的提示
    var excel_json       = document.getElementById('excel_json');         // 清冊中有誤的提示
    var excelFile        = document.getElementById('excelFile');          // 上傳檔案名稱
    var excelUpload      = document.getElementById('excelUpload');        // 上傳按鈕
    var import_excel_btn = document.getElementById('import_excel_btn');   // 載入按鈕

</script>

<script src="trade_restock.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>