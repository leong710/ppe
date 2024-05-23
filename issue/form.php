<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("function.php");
    accessDenied($sys_id);

    $form_type   = "issue";
    // 組合查詢陣列
    $query_arr = array(
        'form_type' => $form_type
    );
    extract(show_plan($query_arr));                 // 查詢表單計畫 20240118 == 讓表單呈現 true 或 false

    // 去年年份
    $thisYear = date('Y')-1 ;                       // 這裡要減1才會找出去年的用量

    // 刪除表單
    if(!empty($_POST["delete_issue"])){
        $check_delete_result = delete_issue($_REQUEST);
        if($check_delete_result){
            echo "<script>alert('issue請購需求單 -- 已刪除');</script>";
            header("refresh:0;url=index.php");
            exit;		                            // 請注意
        }else{
            echo "<script>alert('issue請購需求單 -- 刪除失敗!!');</script>";
        }
    }

    // 決定表單開啟方式
    $action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : 'create';    // 有action就帶action，沒有action就新開單

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
        $issue_row  = array( "id" => "" );          // 預設issue_row[id]=空array
        $logs_arr   = [];                           // 預設logs_arr=空array
        $action     = 'create';                     // 因為沒有id，列為新開單，防止action outOfspc
    }

    if(!empty($_REQUEST["local_id"])){
        $select_local = select_local($_REQUEST);
        $buy_ty = $select_local["buy_ty"];
        $catalogs = read_local_stock($_REQUEST);    // 後來改用這個讀取catalog清單外加該local的儲存量，作為需求首頁目錄
        $local_low_level = (array) json_decode($select_local["low_level"]);
        // 組合查詢條件陣列
        $list_issue_setting = array(
            'local_id'  => $_REQUEST["local_id"],
            'thisYear'  => $thisYear
        );
        $myReceives = show_my_receive($list_issue_setting);         // 列出這個fab_id、今年度的領用單

    }else if(!empty($issue_row["in_local"])){
        $list_issue_setting = array(
            'local_id'  => $issue_row["in_local"],
            'thisYear'  => $thisYear
        );
        $select_local = select_local($list_issue_setting);
        $buy_ty = $select_local["buy_ty"];
        $catalogs = read_local_stock($list_issue_setting);          // 後來改用這個讀取catalog清單外加該local的儲存量，作為需求首頁目錄
        $local_low_level = (array) json_decode($select_local["low_level"]);
        $myReceives = show_my_receive($list_issue_setting);         // 列出這個fab_id、今年度的領用單

    }else{
        $select_local    = array( 'id' => ''  );
        $buy_ty          = "";
        $catalogs        = [];
        $local_low_level = [];
        $myReceives      = [];
    }

    $allLocals = show_allLocal();                   // 所有儲存站點

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
            if($sys_role >= 2.5){
                $step_index = '6';}         // noBody
            if($sys_role == 2){
                $step_index = '7';}         // ppe site user
            if($sys_role == 1){
                $step_index = '8';}         // ppe pm
            if($sys_role == 0){
                $step_index = '9';}         // 系統管理員
            if($action == 'create'){
                $step_index = '0';}         // 填單人
        }
        
        // $step套用身份
        $step = $step_arr[$step_index];

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
    <style>
        #emp_id, #excelFile{    
            margin-bottom: 0px;
            text-align: center;
        }
    </style>
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
                        <button type="button" class="btn btn-secondary" onclick="return confirm('確認返回？') && closeWindow()"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        需求單號：<?php echo ($action == 'create') ? "(尚未給號)": "issue_aid_".$issue_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$issue_row['create_date']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $auth_emp_id." / ".$auth_cname : $issue_row["in_user_id"]." / ".$issue_row["cname_i"] ;?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <!-- 表頭：右側上=選擇收貨廠區 -->
                        <?php if($action == 'create' && !$_inplan){ ?>
                            <div class="col-12 text-center text-danger">
                                <h3>表單尚未開放申請，請洽PPE大PM</h3>
                            </div>
                        <?php } else { ?>
                            <form action="" method="post">
                                <div class="form-floating">
                                    <select name="local_id" id="select_local_id" class="form-control" required style='width:90%;' onchange="this.form.submit()">
                                        <option value="" hidden>--請選擇 需求 儲存點--</option>
                                        <?php foreach($allLocals as $allLocal){ ?>
                                            <?php if($sys_role <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                                <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" <?php echo $allLocal["id"] == $select_local["id"] ? "selected":""; ?>>
                                                    <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <label for="select_local_id" class="form-label">in_local/需求廠區：</label>
                                </div>
                            </form>
                        <?php } ?>
                        <?php if(($sys_role <= 1 ) && (isset($issue_row['idty']) && $issue_row['idty'] != 0)){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $issue_row["id"];?>">
                                <input type="submit" name="delete_issue" value="刪除 (Delete)" title="刪除申請單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
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
                                                <th>名稱&nbsp<i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                                <th>分類</th>
                                                <th>尺寸</th>
                                                <th data-toggle="tooltip" data-placement="bottom" title="<?php echo $thisYear;?>年總累計">年領用 x <?php echo (!empty($buy_ty)) ? $buy_ty:"?";?></br>= 建議值</th>
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
                                                            class="cata_info_btn" onclick="info_module('catalog',this.value);"><h5><b><?php echo $catalog["pname"];?></b></h5></button>
                                                        <?php 
                                                            echo $catalog["SN"] ? '</br>SN：'.$catalog["SN"]:'</br>';
                                                            echo $catalog["part_no"] ? '</br>part_no：'.$catalog["part_no"]:'</br>';
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
                                                            echo $catalog["size"]  ? "尺寸：".$catalog["size"]:"--";
                                                            echo $catalog["unit"]  ? "</br>單位：".$catalog["unit"]:"";
                                                            echo $catalog["OBM"]   ? "</br>品牌/製造商：".$catalog["OBM"]:"";
                                                            echo $catalog["model"] ? "</br>型號：".$catalog["model"]:""; 
                                                        ?></td>
                                                    <td id="receive_<?php echo $select_local["id"].'_'.$catalog['SN'];?>"> -- </td>
                                                    <td>
                                                        <?php 
                                                            // 20231229_更新目錄或換約時會出現，找不到的狀況 // 因為low_level儲存時用了array_filter濾除了安量0的項目導致錯誤
                                                            // $buy_qty = (!empty($catalog["stock_stand"])) ? $catalog["stock_stand"] : $local_low_level[$catalog["SN"]]; 
                                                            $local_low_level_cata_SN = (!empty($local_low_level[$catalog["SN"]])) ? $local_low_level[$catalog["SN"]] : "0";
                                                            $buy_qty = (!empty($catalog["stock_stand"])) ? $catalog["stock_stand"] : $local_low_level_cata_SN; 
                                                        ?>

                                                        <div class="col-12 text-center py-0 " style="color:<?php echo ($catalog['amount'] <= $catalog['stock_stand']) ? "red":"blue";?>">
                                                            <b><?php echo "安量:&nbsp".$buy_qty."&nbsp/&nbsp現量: "; echo (!empty($catalog["amount"])) ? $catalog["amount"] : "0"; ?></b>
                                                        </div>
                                                        <div class="input-group">
                                                            <input type="number" id="<?php echo $catalog['SN'];?>" class="form-control amount t-center"
                                                                placeholder="<?php echo "限購： ".$buy_qty."&nbsp/&nbsp".$catalog["unit"];?>" 
                                                                min="1" 
                                                                <?php if($sys_role <= 1){ ?>
                                                                    onblur="add_cart_btn(this.id, this.value);" 
                                                                <?php } else { ?>
                                                                    max="<?php echo $buy_qty;?>" maxlength="<?php echo strlen($buy_qty);?>" 
                                                                    oninput="if(value.length><?php echo strlen($buy_qty);?>)value=value.slice(0,<?php echo strlen($buy_qty);?>)"
                                                                    onblur="if(value >= <?php echo $buy_qty;?>)value=<?php echo $buy_qty;?>; add_cart_btn(this.id, this.value);" 
                                                                <?php } ?>
                                                                >
                                                            <button type="button" name="<?php echo $catalog['SN'];?>" id="add_<?php echo $catalog['SN'];?>" class="btn btn-outline-secondary add_btn" value=""
                                                                title="加入購物車" onclick="add_item(this.name, this.value, 'off');"><i class="fa fa-plus"></i></button>
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
                                            <?php if(!empty($select_local["id"])){ ?>
                                                <button type="button" id="load_excel_btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#load_excel"><i class="fa fa-upload" aria-hidden="true"></i> 上傳Excel檔</button>
                                            <?php } ?>
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
                                            <?php if($sys_role <= 3){ ?>
                                                <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                                            <?php } ?>
                                            <button type="button" class="btn btn-secondary" onclick="return confirm('確認返回？') && closeWindow()"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
                                        </div>
                                        <hr>
                                    </div>
    
                                    <!-- 表列1 申請人 -->
                                    <div class="row">
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating input-group">
                                                <input type="text" name="emp_id" id="emp_id" class="form-control" required placeholder="工號" value="<?php echo $auth_emp_id;?>">
                                                <label for="emp_id" class="form-label">emp_id/工號：<sup class="text-danger"> *</sup></label>
                                                <button type="button" class="btn btn-outline-primary" onclick="search_fun('emp_id');" data-toggle="tooltip" data-placement="bottom" title="以工號自動帶出其他資訊" ><i class="fa-solid fa-magnifying-glass"></i> 搜尋</button>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="cname" id="cname" class="form-control" required placeholder="申請人姓名" value="<?php echo $auth_cname;?>">
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
                                                <input type="text" class="form-control" readonly
                                                    value="<?php echo $select_local['id'].'：'.$select_local['site_title'].' '.$select_local['fab_title'].'_'.$select_local['local_title']; 
                                                            echo ($select_local['flag'] == 'Off') ? '(已關閉)':''; ?>">
                                                <label for="in_local" class="form-label">in_local/需求廠區：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div style="display: flex;">
                                                <label for="ppty" class="form-label">ppty/需求類別：</label></br>&nbsp
                                                <input type="radio" name="ppty" value="0" id="ppty_0" class="form-check-input" required disabled >
                                                <label for="ppty_0" class="form-check-label">&nbsp臨時&nbsp&nbsp</label>
                                                <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required checked >
                                                <label for="ppty_1" class="form-check-label">&nbsp定期&nbsp&nbsp</label>
                                                <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" required disabled >
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
                                            <input type="hidden" name="in_signName" id="in_signName" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <!-- 表列5 說明 -->
                                    <div class="row">
                                        <div class="col-12 px-2">
                                            <div class="form-floating">
                                                <textarea name="issue_remark" id="issue_remark" class="form-control" style="height: 150px;" placeholder="(由申請單位填寫用品/器材請領原由)"></textarea>
                                                <label for="issue_remark" class="form-label">issue_remark/用途說明：<sup class="text-danger"> * (由申請單位填寫用品/器材請領原由)</sup></label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-12 py-1">
                                            備註：
                                            </br>&nbsp1.填入申請人工號、姓名、需求廠區、需求類別、器材數量。
                                            </br>&nbsp2.簽核：申請人=>申請人主管=>PPEpm=>PR待轉=>轉PR=>PPEpm交貨=>申請人驗收=>商品入庫+表單結案。 
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <hr>
                                        <div class="col-6 col-md-6 py-1 px-2">
                                            
                                        </div>
                                        <div class="col-6 col-md-6 py-1 px-2 text-end">
                                            <?php if($sys_role <= 2){ ?>
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
                                        <h5 class="modal-title">Do you submit this 請購需求：</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-5">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="created_emp_id"  id="created_emp_id" value="<?php echo $auth_emp_id;?>">
                                        <input type="hidden" name="created_cname"   id="created_cname"  value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="in_local"        id="in_local"       value="<?php echo $select_local["id"];?>">          <!-- in_local/出庫廠區 -->   
                                        <input type="hidden" name="id"              id="id"             value="">
                                        <input type="hidden" name="step"            id="step"           value="<?php echo $step;?>">
                                        <input type="hidden" name="action"          id="action"         value="<?php echo $action;?>">
                                        <input type="hidden" name="idty"            id="idty"           value="1">

                                        <?php if($sys_role <= 2){ ?>
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
                            <div style="font-size: 12px;" class="text-end">
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
                                <div class="col-6 col-md-8 py-0">
                                    <label for="excelFile" class="form-label">需求清單 <span>&nbsp<a href="../_Format/issue_example.xlsx" target="_blank">上傳格式範例</a></span> 
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
// // // info modal function
    var action          = '<?=$action?>';                                // 引入action資料
    var catalog         = <?=json_encode($catalogs)?>;                   // 引入catalogs資料
    var issue_row       = <?=json_encode($issue_row)?>;                  // 引入issue_row資料作為Edit
    // var json            = JSON.parse('<=json_encode($logs_arr)?>');    // 鋪設logs紀錄 240124-JSON.parse長度有bug
    var json            = <?=json_encode($logs_arr)?>;                   // 鋪設logs紀錄 240124-改去除JSON.parse
    var id              = '<?=$issue_row["id"]?>';
    var myReceives      = <?=json_encode($myReceives)?>;                  // 引入myReceives資料，算年領用量
    var receiveAmount   = [];                                             // 宣告變數陣列，承裝Receives年領用量
    var buy_ty          = '<?=$buy_ty?>';                                 // 取得fab的安全倍數
    
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

<script src="issue_form.js?v=<?=time()?>"></script>

<?php include("../template/footer.php"); ?>