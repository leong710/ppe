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

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_id_role = $_SESSION[$sys_id]["role"];      // 取出$_session引用

        // 刪除表單
        if(!empty($_POST["delete_issue"])){
            $check_delete_result = delete_issue($_REQUEST);
            if($check_delete_result){
                echo "<script>alert('issue請購需求單 -- 已刪除');</script>";
                header("refresh:0;url=index.php");
                exit;		// 請注意
            }else{
                echo "<script>alert('issue請購需求單 -- 刪除失敗!!');</script>";
            }
        }
        // 更新log
        // if(!empty($_POST["delete_log"])){
        //     updateLogs($_REQUEST);
        // }

    // 決定表單開啟方式
    if(!empty($_REQUEST["action"])){
        $action = $_REQUEST["action"];              // 有action就帶action
    }else{
        $action = 'create';                         // 沒有action就新開單
    }

    if(!empty($_REQUEST["id"])){                    // 編輯 或 閱讀時
        $issue_row = show_issue($_REQUEST);
        if(empty($issue_row)){                      // 防呆
            echo "<script>alert('id-error：{$_REQUEST["id"]}')</script>";
            header("refresh:0;url=index.php");
            exit;
        }
        // logs紀錄鋪設前處理 
        $logs_dec = json_decode($issue_row["logs"]);
        $logs_arr = (array) $logs_dec;

    }else{
        $issue_row = array( "id" => "" );           // 預設issue_row[id]=空array
        $logs_arr = [];                             // 預設logs_arr=空array
        $action = 'create';                         // 因為沒有id，列為新開單，防止action outOfspc
    }

    if(!empty($_POST["local_id"])){
        $_REQUEST["local_id"] = $_POST["local_id"];
        $select_local = select_local($_REQUEST);
        $buy_ty = $select_local["buy_ty"];
        $catalogs = read_local_stock($_REQUEST);    // 後來改用這個讀取catalog清單外加該local的儲存量，作為需求首頁目錄
    
    }else if(!empty($issue_row["in_local"])){
        $query_local = array(
            'local_id' => $issue_row["in_local"]
        );
        $select_local = select_local($query_local);
        $buy_ty = $select_local["buy_ty"];
        $catalogs = read_local_stock($query_local);    // 後來改用這個讀取catalog清單外加該local的儲存量，作為需求首頁目錄

    }else{
        $select_local = array(
            'id' => ''
        );
        $catalogs = [];
    }

    $allLocals = show_allLocal();                   // 所有儲存站點

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
        if(isset($issue_row["in_user_id"]) && ($issue_row["in_user_id"] == $auth_emp_id)){
            $step_index = '0';}             // 填單人
        if(isset($issue_row["in_user_id"]) && ($issue_row["in_user_id"] == $auth_emp_id)){
            $step_index = '1';}             // 申請人
        if(isset($issue_row["omager"]) && ($issue_row["omager"] == $auth_emp_id)){
            $step_index = '2';}             // 申請人主管
        
        if(empty($step_index)){
            if($sys_id_role == 3){
                $step_index = '6';}         // noBody
            if($sys_id_role == 2){
                $step_index = '7';}         // ppe site user
            if($sys_id_role == 1){
                $step_index = '8';}         // ppe pm
            if($sys_id_role == 0){
                $step_index = '9';}         // 系統管理員
            if($action == 'create'){
                $step_index = '0';}         // 填單人
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
                        <h3><i class="fa-solid fa-1"></i>&nbsp<b>請購需求</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <!-- <a href="index.php" class="btn btn-success"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回總表</a> -->
                        <a href="index.php" class="btn btn-secondary" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i> 返回</a>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        需求單號：<?php echo ($action == 'create') ? "(尚未給號)": "issue_aid_".$issue_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$issue_row['create_date']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $auth_emp_id." / ".$_SESSION["AUTH"]["cname"] : $issue_row["in_user_id"]." / ".$issue_row["cname_i"] ;?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <!-- 表頭：右側上=選擇收貨廠區 -->
                        <form action="" method="post">
                            <div class="form-floating">
                                <select name="local_id" id="select_local_id" class="form-control" required style='width:80%;' onchange="this.form.submit()">
                                    <option value="" hidden>--請選擇 需求 儲存點--</option>
                                    <?php foreach($allLocals as $allLocal){ ?>
                                        <?php if($sys_id_role <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                            <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" <?php echo $allLocal["id"] == $select_local["id"] ? "selected":""; ?>>
                                                <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label for="select_local_id" class="form-label">in_local/需求廠區：</label>
                            </div>
                        </form>

                        <?php if(($sys_id_role <= 1 ) && (isset($issue_row['idty']) && $issue_row['idty'] != 0)){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $issue_row["id"];?>">
                                <input type="submit" name="delete_issue" value="刪除" title="刪除申請單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
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
                                                            echo $catalog["cata_remark"] ? '</br>敘述：'.$catalog["cata_remark"]:'</br>';?></td>
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
                                                        <div class="col-12 text-center py-0 " style="color:<?php echo ($catalog['amount'] <= $catalog['stock_stand']) ? "red":"blue";?>">
                                                                <b><?php echo "安全: "; echo (!empty($catalog["stock_stand"])) ? $catalog["stock_stand"]:"0";
                                                                         echo " / 存量: "; echo (!empty($catalog["amount"])) ? $catalog["amount"]:"0"; ?></b>
                                                        </div>
                                                        <input type="number" id="<?php echo $catalog['SN'];?>" class="form-control amount t-center"
                                                            placeholder="限購： <?php switch($buy_ty){
                                                                                        case 'a':echo $catalog['buy_a']; $buy_qty = $catalog['buy_a']; break;
                                                                                        case 'b':echo $catalog['buy_b']; $buy_qty = $catalog['buy_b']; break;
                                                                                        default :echo $catalog['buy_a']; $buy_qty = $catalog['buy_a']; break; }
                                                                                    echo "&nbsp/&nbsp".$catalog["unit"];?>" 
                                                            min="1" max="<?php echo $buy_qty;?>" maxlength="<?php echo strlen($buy_qty);?>" 
                                                            oninput="if(value.length><?php echo strlen($buy_qty);?>)value=value.slice(0,<?php echo strlen($buy_qty);?>)"
                                                            onblur="if(value >= <?php echo $buy_qty;?>)value=<?php echo $buy_qty;?>; add_cart_btn(this.id, this.value);" >
                                                    </td>
                                                    <td>
                                                        <button type="button" name="<?php echo $catalog['SN'];?>" id="add_<?php echo $catalog['SN'];?>" class="add_btn" value="" title="加入購物車" onclick="add_item(this.name, this.value, 'off');"><h5><i class="fa-regular fa-square-plus"></i></h5></button>
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
                                        <div class="col-12">
                                            請申請人填入相關資料：
                                        </div>
                                        <hr>
                                    </div>
    
                                    <!-- 表列1 請購需求單站點 -->
                                    <div class="row">
                                        <!-- 表頭：右側下=選擇入庫廠區 -->
                                        <div class="col-12 col-md-6 py-3 px-2">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" readonly
                                                    value="<?php echo $select_local['id'].'：'.$select_local['site_title'].' '.$select_local['fab_title'].'_'.$select_local['local_title']; 
                                                            echo ($select_local['flag'] == 'Off') ? '(已關閉)':''; ?>">
                                                <label for="in_local" class="form-label">in_local/需求廠區：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <!-- 表頭：右側下=選擇入庫廠區 -->
                                        <div class="col-12 col-md-6 py-3 px-2">
                                            <div style="display: flex;">
                                                <label for="ppty" class="form-label">ppty/需求類別：</label></br>&nbsp
                                                <input type="radio" name="ppty" value="0" id="ppty_0" class="form-check-input" required >
                                                <label for="ppty_0" class="form-check-label">&nbsp臨時&nbsp&nbsp</label>
                                                <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required checked >
                                                <label for="ppty_1" class="form-check-label">&nbsp一般&nbsp&nbsp</label>
                                                <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" required disabled >
                                                <label for="ppty_3" class="form-check-label" data-toggle="tooltip" data-placement="bottom" title="注意：事故須先通報防災!!">&nbsp緊急</label>
                                            </div>
                                        </div>
                                    </div>
                                    

                                    
                                    <!-- 表列5 說明 -->
                                    <div class="row">
                                        <hr>
                                        <div class="col-12 py-1">
                                            備註：
                                            </br>&nbsp1.填入申請人工號、姓名、需求廠區、需求類別、器材數量。
                                            </br>&nbsp2.簽核：申請人=>承辦人=>PR待轉=>轉PR=>表單結案。 
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <hr>
                                        <div class="col-6 col-md-6 py-1 px-2">
                                            
                                        </div>
                                        <div class="col-6 col-md-6 py-1 px-2 text-end">
                                            <?php if($sys_id_role <= 2){ ?>
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
                                        <h5 class="modal-title">Do you submit this 請購需求：</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-5">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="updated_emp_id"  id="updated_emp_id" value="<?php echo $auth_emp_id;?>">
                                        <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                        <input type="hidden" name="cname"                               value="<?php echo $_SESSION["AUTH"]["cname"];?>">   <!-- cname/出庫填單人cname -->
                                        <input type="hidden" name="in_user_id"                          value="<?php echo $auth_emp_id;?>">                 <!-- in_user_id/出庫填單人emp_id -->
                                        <input type="hidden" name="in_local"                            value="<?php echo $select_local["id"];?>">          <!-- in_local/出庫廠區 -->   
                                        <input type="hidden" name="action"          id="action"         value="<?php echo $action;?>">
                                        <input type="hidden" name="idty"            id="idty"           value="1">
                                        <input type="hidden" name="step"            id="step"           value="<?php echo $step;?>">
                                        <input type="hidden" name="id"              id="id"             value="">
                                        <?php if($sys_id_role <= 2){ ?>
                                            <button type="submit" value="Submit" name="issue_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
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
                        <table class="for-table logs table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Step</th>
                                    <th>Signer</th>
                                    <th>Time Signed</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                    <!-- <th>action</th> -->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div style="font-size: 6px;" class="text-end">
                            logs-end
                        </div>
                    </div>
                </div>
    
                <!-- 尾段：debug訊息 -->
                <?php if(isset($_REQUEST["debug"])){
                    include("debug_board.php"); 
                } ?>

            </div>
        </div>
    </div>
    <!-- toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="liveToast" class="toast bg-warning text-dark" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="2000">
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
                                <div class="col-12 col-md-6 py-0">
                                    <label for="excelFile" class="form-label">需求清單 <span>&nbsp<a href="../_Format/restock_example.xlsx" target="_blank">上傳格式範例</a></span> 
                                        <sup class="text-danger"> * 限EXCEL檔案</sup></label>
                                    <div class="input-group">
                                        <input type="file" name="excelFile" id="excelFile" style="font-size: 16px; max-width: 250px;" class="form-control form-control-sm" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                        <button type="submit" name="excelUpload" id="excelUpload" class="btn btn-outline-secondary">上傳</button>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-0">
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
// // // info modal function
    var action = '<?=$action;?>';                       // 引入action資料
    var catalog = <?=json_encode($catalogs);?>;                        // 引入catalogs資料
    var issue_row = <?=json_encode($issue_row);?>;                        // 引入issue_row資料作為Edit
    var json = JSON.parse('<?=json_encode($logs_arr)?>');
    var id = '<?=$issue_row["id"]?>';

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

<script src="issue_form.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>