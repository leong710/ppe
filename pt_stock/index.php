<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);
    // accessDeniedAdmin($sys_id);

    // 複製本頁網址藥用
    if(isset($_SERVER["HTTP_REFERER"])){
        $up_href = $_SERVER["HTTP_REFERER"];            // 回上頁
    }else{
        $up_href = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // 回本頁
    }

    $auth_cname  = $_SESSION["AUTH"]["cname"];      // 取出$_session引用
    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用
    $form_type   = "ptstock";

    // CRUD module function --
        $swal_json = array();
        // ptstock-crud
        if(isset($_POST["ptstock_store"])) { $swal_json = store_ptstock($_REQUEST);  }      // 新增add    
        if(isset($_POST["ptstock_update"])){ $swal_json = update_ptstock($_REQUEST); }      // 編輯Edit
        if(isset($_POST["ptstock_delete"])){ $swal_json = delete_ptstock($_REQUEST); }      // 刪除delete
        // receivr-crud
        if(isset($_POST["ptreceive_store"])){ $swal_json = store_ptreceive($_REQUEST); }    // 新增add
        // if(isset($_POST["edit_ptstock_submit"])){ $swal_json = update_ptstock($_REQUEST); } // 編輯Edit
        // if(isset($_POST["delete_ptstock"])){ $swal_json = delete_ptstock($_REQUEST); }      // 刪除delete

        
    // 組合查詢陣列 -- 把fabs讀進來作為[篩選]的select option
            if($sys_role <=1 ){
                $fab_scope = "All";                 // All
            }else{
                $fab_scope = "allMy";               // allMy
            }
        // 查詢篩選條件：fab_id
            if(isset($_REQUEST["select_fab_id"])){     // 有帶查詢，套查詢參數
                $select_fab_id = $_REQUEST["select_fab_id"];
            }else{                              // 先給預設值
                $select_fab_id = $fab_scope;                // All
            }
        
        // 1-1 將sys_fab_id加入sfab_id
            if(!isset($sfab_id_str) && isset($_SESSION[$sys_id]["sfab_id_str"])){
                // 1-1 將sys_fab_id加入sfab_id
                $sfab_id_str = $_SESSION[$sys_id]["sfab_id_str"];     // 1-1c sfab_id是陣列，要轉成字串str
            }else{
                $sfab_id_str = get_sfab_id($sys_id, "str");          // 1-1c sfab_id是陣列，要轉成字串str
            }   
            $sfab_id_arr = explode(',', $sfab_id_str);

        // 1-2 組合查詢條件陣列
            // if($sys_role <= 1 || $select_fab_id == "All"){
            //     $sort_sfab_id = "All";                // All
            // } else{
            //     $sort_sfab_id = $sfab_id_str;         // allMy 1-2.將字串sfab_id加入組合查詢陣列中
            // }

        // 今年年份
            $thisYear = date('Y');
            // 半年分界線
            if(date('m') <= 6 ){
                $half = "H1";
            }else{
                $half = "H2";
            }
        
    // 組合查詢條件陣列
        $query_arr = array(
            'select_fab_id' => $select_fab_id,
            'fab_id'        => $select_fab_id,
            'fab_scope'     => $fab_scope,
            'sfab_id'       => $sfab_id_str,
            'thisYear'      => $thisYear,
            'checked_year'  => $thisYear,               // 建立查詢陣列for顯示今年點檢表
            'half'          => $half,                   // 建立查詢陣列for顯示今年點檢表
            'form_type'     => $form_type
        );
 
        // init.1_index fab_list：role <=1 ? All+all_fab : sFab_id+allMy => select_fab_id
            $fabs = show_fab_list($query_arr);               // index FAB查詢清單用

        // init.2_create：local by select_fab_id / edit：local by All/allMy
            // $create_locals = show_local2create($query_arr);   // create：取得select_fab_id下的Local儲存點
            // $edit_locals = show_local2edit($query_arr);       // edit：取得sFab_id下所有的Local儲存點
            $locals = show_fabs_local($query_arr);

        // init.3_create/edit catalog by cate_no = J
            $catalogs  = show_ptcatalogs();                   // 取得所有catalog - J項目，供create使用

        // init.4_
            $stocks     = show_ptstock($query_arr);            // 依查詢條件儲存點顯示存量
        // init.5_
            $ptreceives = show_ptreceive($query_arr);         // 列出這個fab_id、今年度的領用單
        // init.6_
            $check_yh_list = check_yh_list($query_arr);        // 查詢自己的點檢紀錄：半年檢
            $check_yh_list_num = count($check_yh_list);                 // 計算自己的點檢紀錄筆數：半年檢
        // init.7_
            $select_fab = [];
            if($select_fab_id != 'All' && $select_fab_id != "allMy"){
                $select_fab = show_select_fab($query_arr);                   // 查詢fab的細項結果
            }
            // if(empty($select_fab)){                                        // 查無資料時返回指定頁面
            //     echo "<script>history.back()</script>";                 // 用script導回上一頁。防止崩煃
            // }
        extract(show_plan($query_arr));                        // 查詢表單計畫 20240118 == 讓表單呈現 true 或 false

    // <!-- 20211215分頁工具 -->
        $per_total = count($stocks);        //計算總筆數
            // $per = 25;                          //每頁筆數
            // $pages = ceil($per_total/$per);     //計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
            //     if(!isset($_GET['page'])){      //!isset 判斷有沒有$_GET['page']這個變數
            //         $page = 1;	  
            //     }else{
            //         $page = $_GET['page'];
            //     }
            //     $start = ($page-1)*$per;            //每一頁開始的資料序號(資料庫序號是從0開始)
            //     // 合併嵌入分頁工具
            //     $query_arr['start'] = $start;
            //     $query_arr['per'] = $per;

            // $div_stocks = show_stock($query_arr);
            // // $div_stocks = stock_page_div($start, $per, $query_arr);
            // $page_start = $start +1;            //選取頁的起始筆數
            // $page_end = $start + $per;          //選取頁的最後筆數
            //     if($page_end>$per_total){       //最後頁的最後筆數=總筆數
            //         $page_end = $per_total;
            //     }
    // <!-- 20211215分頁工具 -->
    
    // 今年年份
        $thisYear = date('Y');
    // 初始化半年後日期，讓系統判斷與highLight
        $toDay = date('Y-m-d');
        $half_month = date('Y-m-d', strtotime($toDay."+3 month -1 day"));   // strtotime()将任何字符串的日期时间描述解析为 Unix 时间戳 // 20240201 定90天
        
    // 取得swal內容
        if(isset($swal_json["fun"]) && ($swal_json["fun"] == "store_ptreceive") && ($swal_json["action"] == "success")){
            $ppe_pms = show_PPE_PM();
        }else{
            $ppe_pms = array();
        }
        // $ppe_pms_arr = array();
        $ppe_pms = show_PPE_PM();
        // if(!empty($ppe_pms)){
        //     foreach($ppe_pms as $ppe_pm){
        //         if(!in_array($ppe_pm["emp_id"], $ppe_pms_arr)){
        //             array_push($ppe_pms_arr, $ppe_pm["emp_id"]);
        //         }
        //     }
        // }
        // $ppe_pms_str = implode(",", $ppe_pms_arr);                   // 1-1c sfab_id是陣列，要轉成字串


        echo "<pre>";
        print_r($ppe_pms);
        // print_r($query_arr);
        // print_r($stocks);
        // print_r($_REQUEST);
        // echo "<hr>";
        // print_r($swal_json);
        echo "</pre>";
        // echo $half_month;
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
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <style>
        .body > ul {
            padding-left: 0px;
        }

        /* 凸顯可編輯欄位 */
            .fix_amount:hover {
                /* font-size: 1.05rem; */
                font-weight: bold;
                text-shadow: 3px 3px 5px rgba(0,0,0,.5);
            }
        /* 警示項目 amount、lot_num */
            .alert_itb {
                background-color: #FFBFFF;
                color: red;
                font-size: 1.2em;
            }
            .alert_it {
                background-color: #FFBFFF;
                color: red;
            }
        /* inline */
            .inb {
                display: inline-block;
            }
            .inf {
                display: inline-flex;
            }
    </style>
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                icon: "../../libs/jquery/Wedges-3s-120px.gif",
                // icon: "../../libs/jquery/loading.gif",
            }); 
        }
        // finished loading關閉mLoading提示
        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
        });
        mloading();    // 畫面載入時開啟loading
    </script>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 rounded" style="background-color: rgba(255, 255, 255, .8);">
                <!-- NAV分頁標籤與統計 -->
                <div class="col-12 pb-0 px-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link active" href="index.php">除汙器材庫存管理</span></a></li>
                        <li class="nav-item"><a class="nav-link" href="pt_receive.php">領用記錄</span></a></li>
                        <?php if($sys_role <= 1){?>
                            <li class="nav-item"><a class="nav-link " href="pt_local.php">除汙儲存點管理</span></a></li>
                            <li class="nav-item"><a class="nav-link " href="low_level.php">儲存點安量管理</span></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <!-- 內頁 -->
                <div class="col-12 bg-white">
                    <!-- by各Local儲存點： -->
                    <div class="row">
                        <div class="col-md-4 pb-0 ">
                            <h5><?php echo isset($select_fab["id"]) ? $select_fab["id"].".".$select_fab["fab_title"]." (".$select_fab["fab_remark"].")":"$select_fab_id";?>_除汙器材庫存管理： </h5>
                        </div>
                        <!-- sort/groupBy function -->
                        <div class="col-md-4 pb-0 ">
                            <form action="" method="POST">
                                <div class="input-group">
                                    <span class="input-group-text">篩選</span>
                                    <select name="select_fab_id" id="select_fab_id" class="form-select" onchange="this.form.submit()">
                                        <option value="" hidden selected >-- 請選擇local --</option>
                                        <?php if($sys_role <= 1 ){ ?>
                                            <option for="select_fab_id" value="All" <?php echo $select_fab_id == "All" ? "selected":"";?>>-- All fab --</option>
                                        <?php } ?>
                                        <option for="select_fab_id" value="allMy" <?php echo $select_fab_id == "allMy" ? "selected":"";?>>
                                            -- All my fab <?php echo $sfab_id_str ? "(".$sfab_id_str.")":"";?> --</option>
                                        <?php foreach($fabs as $fab){ ?>
                                            <option for="select_fab_id" value="<?php echo $fab["id"];?>" <?php echo $fab["id"] == $select_fab_id ? "selected":"";?>>
                                                <?php echo $fab["id"]."：".$fab["site_title"]."&nbsp".$fab["fab_title"]."( ".$fab["fab_remark"]." )"; echo ($fab["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                        <?php } ?>
                                    </select>
                                    <!-- <button type="submit" class="btn btn-outline-secondary">查詢</button> -->
                                </div>
                            </form>
                        </div>
                        <!-- 表頭按鈕 -->
                        <div class="col-md-4 pb-0 text-end inb">
                            <?php if(isset($select_fab["id"])){ ?>
                                <?php if($_inplan && $sys_role <= 2 && ($check_yh_list_num == 0)){?>
                                    <div class="inb">
                                        <button type="button" id="checkList_btn" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#checkList"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>&nbsp點檢表</button>
                                    </div>
                                <?php } ?>
                                <?php if($sys_role <= 1 || ( $sys_role <= 2 && ((in_array($select_fab["id"], $sfab_id_arr)) ) ) ){ ?>
                                    <div class="inb">
                                        <button type="button" id="add_ptstock_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_ptstock" onclick="add_module('ptstock')"><i class="fa fa-plus"></i> 新增</button>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <div class="inb">
                                <button type="button" id="receive_btn" class="btn btn-danger disabled" data-bs-toggle="modal" data-bs-target="#receive"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>&nbsp領用</button>
                            </div>
                            <div class="inb">
                                <!-- 20231128 下載Excel -->
                                <?php if($per_total != 0){ ?>
                                    <form id="myForm" method="post" action="../_Format/download_excel.php">
                                        <input type="hidden" name="htmlTable" id="htmlTable" value="">
                                        <button type="submit" name="submit" class="btn btn-success" title="<?php echo isset($select_fab["id"]) ? $select_fab["fab_title"]." (".$select_fab["fab_remark"].")":"";?>" value="stock" onclick="submitDownloadExcel('stock')" >
                                            <i class="fa fa-download" aria-hidden="true"></i> 匯出</button>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- Bootstrap Alarm -->
                        <div id="liveAlertPlaceholder" class="col-12 text-center mb-0 pb-0"></div>
                    </div>
                    <!-- <hr> -->
                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->               
                    <table id="stock_list" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <!-- <th>ai</th> -->
                                <th><i class="fa fa-check" aria-hidden="true"></i> 儲存點位置</th>
                                <th>PIC</th>
                                <th>名稱</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="<?php echo $thisYear;?>今年總累計">年領用</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="
                                    <?php echo !empty($select_fab["id"]) && ($sys_role <= 1 || ( $sys_role <= 2 && (in_array($select_fab["id"], $sfab_id_arr)) ) ) ? "編輯後按Enter才能儲存":"未有編輯權限";?>
                                    ">現量</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="同儲區&同品項將安全存量合併成一筆計算">安量</th>
                                <th>選用</th>
                                <th>備註說明</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="效期小於3個月將highlight">批號/期限</th>
                                <th>Flag</th>
                                <!-- <th>PO no</th> -->
                                <th>最後更新</th>
                            </tr>
                        </thead>
                        <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                        <tbody>
                            <?php 
                                $check_item ="";
                            ?>
                            <?php foreach($stocks as $stock){ ?>
                                <tr <?php echo ($check_item != $stock['local_id']) ? 'style="border-top:3px #FFD382 solid;"':'';?>>
                                    <!-- <td style="font-size: 12px;"><php echo $stock['id'];?></td> -->
                                    <td class="word_bk" title="aid_<?php echo $stock['id'];?>"><?php echo $stock['fab_title']."</br>".$stock['local_title'];?></td>
                                    <td><img src="../catalog/images/<?php echo $stock["PIC"];?>" class="img-thumbnail"></td>
         
                                    <td class="word_bk"><?php echo $stock["SN"]."</br>".$stock['pname'];?></td>
                                <!-- 年領用 -->
                                    <td id="ptreceive_<?php echo $stock['stk_id'].'_'.$stock['cata_SN'];?>">--</td>

                                    <td id="<?php echo $stock['id'];?>" name="amount" class="fix_amount <?php echo ($stock["amount"] < $stock['standard_lv']) ? "alert_itb":"" ;?>"
                                        <?php if($sys_role <= 1 || ( $sys_role <= 2 && (in_array($stock["fab_id"], $sfab_id_arr)) ) ){ ?> contenteditable="true" <?php } ?>>
                                        <?php echo $stock['amount'];?></td>
                                    <td class="<?php echo ($stock["amount"] < $stock['standard_lv']) ? "alert_it":"";?>"><?php echo $stock['standard_lv'];?></td>
                                <!-- 選用 -->
                                    <td><input type="checkbox" class="select_item" name="<?php echo $stock["SN"].'_'.$stock["stk_id"];?>" id="<?php echo 'add_'.$stock['SN'].'_'.$stock['stk_id'];?>" 
                                            value="<?php echo $stock['amount'];?>" onchange="add_item(this.name, this.value, 'off');" 
                                            <?php echo $stock["amount"] <="0" ? " disabled":""; echo isset($select_fab["id"]) ? "":" disabled";?>></td>
                                    
                                    <td class="word_bk"><?php echo $stock['stock_remark'];?></td>
                                    <td <?php if($stock["lot_num"] < $half_month){ ?> style="background-color:#FFBFFF;color:red;" data-toggle="tooltip" data-placement="bottom" title="有效期限小於：<?php echo $half_month;?>" <?php } ?>>
                                        <?php echo $stock['lot_num'];?></td>
                                    <td><?php echo $stock['flag'];?></td>
                                    <!-- <td style="font-size: 12px;"><php echo $stock['po_no'];?></td> -->
                                    <td style="width:8%;font-size: 12px;" title="最後編輯: <?php echo $stock['updated_cname'];?>">
                                        <?php if(isset($stock['id'])){ ?>
                                            <?php if($sys_role <= 1 || ( $sys_role == 2 && (in_array($stock["fab_id"], $sfab_id_arr)) )){ ?>
                                                    <button type="button" id="edit_ptstock_btn" value="<?php echo $stock["id"];?>" data-bs-toggle="modal" data-bs-target="#edit_ptstock" 
                                                        onclick="edit_module('ptstock', this.value)" ><?php echo $stock['updated_at'];?></button>
                                            <?php }else{ echo $stock['updated_at']; } ?>
                                        <?php } ?></td>
                                </tr>
                                <?php $check_item = $stock['local_id'];?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                </br>

                <!-- 尾段：debug訊息 -->
                <?php if(isset($_REQUEST["debug"])){
                    echo "<hr>";
                    include("debug_board.php"); 
                } ?>
            </div>
        </div>
    </div>
   
<!-- 彈出畫面模組 新增、編輯ptstock品項 -->
    <div class="modal fade" id="edit_ptstock" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="modal_action"></span>&nbsp除汙/器材儲存品</h4>
                    &nbsp&nbsp&nbsp&nbsp&nbsp
                    <form action="" method="post">
                        <input type="hidden" name="select_fab_id" value="<?php echo $select_fab_id;?>">
                        <input type="hidden" name="id" id="ptstock_delete_id">
                        <span id="modal_delect_btn" class="<?php echo ($sys_role <= 1) ? "":" unblock ";?>"></span>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="" method="post">
                    <div class="modal-body p-4 pb-0">
                        <!-- 第一列 儲存區/器材名稱 -->
                        <div class="col-12 rounded py-1" style="background-color: #D3FF93;">
                            <div class="row" >
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <select name="local_id" id="local_id" class="form-control" required onchange="select_local(this.value)">
                                            <option value="" selected hidden>-- 請選擇儲存點 --</option>
                                            <?php foreach($locals as $local){ ?>
                                                <option value="<?php echo $local["id"];?>" >
                                                    <?php echo $local["id"]."：".$local["fab_title"]."_".$local["local_title"]."(".$local["local_remark"].")"; echo ($local["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                            <?php } ?>
                                        </select>
                                        <label for="local_id" class="form-label">local_id/儲存位置：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <select name="cata_SN" id="cata_SN" class="form-control" required onchange="update_standard_lv(this.value)">
                                            <option value="" selected hidden>-- 請選擇器材 --</option>
                                            <?php foreach($catalogs as $catalog){ ?>
                                                <option value="<?php echo $catalog["SN"];?>" title="<?php echo $catalog["cata_remark"];?>"><?php echo $catalog["SN"]."：".$catalog["pname"];?></option>
                                            <?php } ?>
                                        </select>
                                        <label for="cata_SN" class="form-label">cata_SN/器材名稱：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <!-- 第二排數據 -->
                        <div class="col-12 rounded bg-light">
                            <div class="row">
                                <!-- 左側-數量 -->
                                <div class="col-12 col-md-6 py-0">
                                    <div class="form-floating">
                                        <input type="number" name="standard_lv" id="standard_lv" class="form-control t-center" placeholder="標準數量(管理員填)" min="1" max="999" value="1"
                                            <?php echo $sys_role <= 1 ? " ":"readonly"; ?> >
                                        <label for="standard_lv" class="form-label">standard_lv/安全存量：<sup class="text-danger"><?php echo ($sys_role <= 1) ? " ":" - disabled";?></sup></label>
                                    </div>
                                </div>
                                <!-- 右側-批號 -->
                                <div class="col-12 col-md-6 py-0">
                                    <div class="form-floating">
                                        <input type="number" name="amount" id="amount" class="form-control t-center" required placeholder="正常數量" min="0" max="999">
                                        <label for="amount" class="form-label">amount/現場存量：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="po_no" id="po_no" class="form-control" placeholder="PO採購編號">
                                        <label for="po_no" class="form-label">po_no/PO採購編號：</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="pno" id="pno" class="form-control" placeholder="料號">
                                        <label for="pno" class="form-label">pno/料號：</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <textarea name="stock_remark" id="stock_remark" class="form-control" style="height: 100px"></textarea>
                                        <label for="stock_remark" class="form-label">stock_remark/備註說明：</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating pb-0">
                                        <input type="date" name="lot_num" id="lot_num" class="form-control" required>
                                        <label for="lot_num" class="form-label">lot_num/批號/期限：<sup class="text-danger">*</sup></label>
                                    </div>
                                    <div class="col-12 pt-0 text-end">
                                        <button type="button" id="edit_toggle_btn" class="btn btn-sm btn-xs btn-warning text-dark" onclick="chenge_lot_num('edit')">永久</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 第三排提示 -->
                        <div class="col-12 rounded bg-light pt-0">
                            *.注意：相同 儲存位置、器材、採購編號、批號期限 將合併計算!
                        </div>
                        <!-- 最後編輯資訊 -->
                        <div class="col-12 text-end p-0" id="edit_ptstock_info"></div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="id" id="ptstock_edit_id" >
                            <input type="hidden" name="select_fab_id" value="<?php echo $select_fab_id;?>">
                            <input type="hidden" name="updated_cname" value="<?php echo $auth_cname;?>">
                            <span id="modal_button" class="<?php echo ($sys_role <= 2) ? "":" unblock ";?>"></span>
                            <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- 彈出畫面模組 所屬區域器材儲存量總表for年檢用 -->
    <div class="modal fade" id="checkList" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">除汙劑點檢紀錄：</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="store_checkList.php" method="post">
                <!-- <form action="" method="post"> -->
                    <div class="modal-body p-4">
                        <div class="col-12 rounded bg-light">
                            <div class="row">
                                <div class="col-md-7 py-0">
                                    <div>
                                        點檢廠區：<?php echo $select_fab["fab_title"]; echo $select_fab["fab_remark"] ? " (".$select_fab["fab_remark"].")":"";?></br>
                                        點檢日期：<?php echo date('Y-m-d H:i'); ?> (實際以送出時間為主)
                                    </div>
                                </div>
                                <div class="col-md-5 py-0">
                                    <div>
                                        點檢人員：<?php echo $auth_cname;?></br>
                                        點檢年度：<?php echo $today_year." / ".$half;?>
                                    </div>
                                </div>

                                <div class="col-12 ">
                                    <label for="checked_remark" class="form-check-label" >點檢備註說明：</label>
                                    <textarea name="checked_remark" id="checked_remark" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-12 py-0 text-end">
                                <h5>
                                    <input type="checkbox" name="iAgree" value="0" id="iAgree" class="form-check-input" required>
                                    <label for="iAgree" class="form-label">我已確認完畢，現況數量無誤!</label>
                                </h5>
                            </div>
                        </div>
                        <!-- 最後編輯資訊 -->
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="action"          value="store_ptcheckList">
                            <input type="hidden" name="form_type"       value="<?php echo $form_type;?>">
                            <input type="hidden" name="up_href"         value="<?php echo $up_href;?>">
                            <input type="hidden" name="fab_id"          value="<?php echo $select_fab["id"];?>">
                            <input type="hidden" name="emp_id"          value="<?php echo $auth_emp_id;?>">
                            <input type="hidden" name="cname"           value="<?php echo $auth_cname; ?>">
                            <input type="hidden" name="checked_year"    value="<?php echo $today_year;?>">
                            <input type="hidden" name="half"            value="<?php echo $half;?>">
                            <!-- <input type="hidden" name="cate_no"         value="<php echo $sort_cate_no;?>"> -->
                            <input type="hidden" name="updated_user"    value="<?php echo $auth_cname;?>">
                            <?php if($sys_role <= 2){ ?>   
                                <input type="submit" value="Submit" name="submit" class="btn btn-primary">
                            <?php } ?>
                            <!-- <input type="submit" name="edit_stock_submit" class="btn btn-primary" value="儲存" > -->
                            <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- 彈出畫面模組 除汙器材領用 品項 -->
    <div class="modal fade" id="receive" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header add_mode_bgc">
                    <h4 class="modal-title"><span id="modal_action"></span>&nbsp除汙器材領用&nbsp</h4><span id="shopping_count" class="badge rounded-pill bg-danger"></span>
                    &nbsp&nbsp&nbsp&nbsp&nbsp
                    <form action="" method="post">
                        <input type="hidden" name="select_fab_id" value="<?php echo $select_fab_id;?>">
                        <input type="hidden" name="id" id="receive_delete_id">
                        <span id="modal_delect_btn" class="<?php echo ($sys_role <= 1) ? "":" unblock ";?>"></span>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="" method="post">
                    <div class="modal-body p-4 pb-0">
                        <!-- 第一列 購物車 -->
                        <div class="col-12 rounded p-0" id="receive_shopping_cart">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>select</th>
                                        <th>儲存點位置</th>
                                        <th>名稱</th>
                                        <th>數量</th>
                                        <th>批號/期限</th>
                                    </tr>
                                </thead>
                                <tbody id="shopping_cart_tbody">
                                </tbody>
                            </table>
                        </div>
                
                        <!-- 第二排 申請數據 -->
                        <div class="col-12 rounded " style="background-color: #D3FF93;">
                            <div class="row">
                                <div class="col-6 col-md-6 py-1">
                                    <div class="form-floating pb-0">
                                        <input type="datetime-local" class="form-control" name="app_date" id="receive_app_date" value="<?php echo date('Y-m-d\TH:i');?>" required>
                                        <label for="receive_app_date" class="form-label">領用日期：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6 py-1">
                                    <div style="display: flex;">
                                        <label for="ppty" class="form-label">ppty/需求類別：</label></br>&nbsp
                                        <input type="radio" name="ppty" value="0" id="ppty_0" class="form-check-input" >
                                        <label for="ppty_0" class="form-check-label">&nbsp臨時&nbsp&nbsp</label>
                                        <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required checked >
                                        <label for="ppty_1" class="form-check-label">&nbsp定期&nbsp&nbsp</label>
                                        <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" >
                                        <label for="ppty_3" class="form-check-label text-danger" data-toggle="tooltip" data-placement="bottom" title="注意：事故須先通報防災!!">&nbsp緊急</label>
                                    </div>
                                </div>
                                <div class="col-12 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="receive_remark" id="receive_remark" class="form-control" required>
                                        <label for="receive_remark" class="form-label">receive_remark/領用說明：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 第三排提示 -->
                        <div class="col-12 rounded bg-light pt-0">
                            *** 注意：事故請務必填寫詳細案件名稱~!
                        </div>
                        <!-- 最後編輯資訊 -->
                        <div class="col-12 text-end p-0" id="edit_receive_info"></div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="id" id="receive_edit_id" >
                            <input type="hidden" name="select_fab_id" value="<?php echo $select_fab["id"];?>">
                            <input type="hidden" name="fab_title"     value="<?php echo $select_fab["fab_title"];?>">
                            <input type="hidden" name="created_cname" value="<?php echo $auth_cname;?>">
                            <input type="hidden" name="emp_id"        value="<?php echo $auth_emp_id;?>">
                            <input type="hidden" name="idty"          value="1"> <!-- idty:1 扣帳 -->
                            <span id="modal_button" class="<?php echo ($sys_role <= 2) ? "":" unblock ";?>">
                                <input type="submit" class="btn btn-primary disabled" name="ptreceive_store" value="送出" id="receive_submit">
                            </span>
                            <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center bg-warning" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="1000">
            <div class="d-flex">
                <div class="toast-body" id="toast-body"></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>
// // // 開局導入設定檔
    var allLocals     = <?=json_encode($locals)?>;                      // 引入所有local的allLocals值
    var low_level     = [];                                             // 宣告low_level變數
    var ptstock       = <?=json_encode($stocks)?>;                      // 引入div_stocks資料
    var ptstock_item  = ['id','local_id','cata_SN','standard_lv','amount','po_no','pno','stock_remark','lot_num'];    // 交給其他功能帶入 delete_supp_id
    var swal_json     = <?=json_encode($swal_json)?>;                   // 引入swal_json值
    var ppe_pms       = <?=json_encode($ppe_pms)?>;                     // 引入ppe_pms值
    var action        = 'create';

// 先定義一個陣列(裝輸出資料使用)for 下載Excel
    var listData      = <?=json_encode($stocks)?>;                      // 引入stocks資料
    
// 找出Local_id算SN年領用量
    var ptreceives    = <?=json_encode($ptreceives)?>;                  // 引入ptreceives資料，算年領用量
    var receiveAmount = [];                                             // 宣告變數陣列，承裝Receives年領用量

// 半年檢
    var check_yh_list_num   = '<?=$check_yh_list_num?>';
    var thisYear            = '<?=$thisYear?>';
    var half                = '<?=$half?>';
    var sys_role            = '<?=$sys_role?>';
    var case_title          = '<?=$case_title?>';
    var _inplan             = '<?=$_inplan?>';
    var start_time          = '<?=$start_time?>';
    var end_time            = '<?=$end_time?>';

</script>

<script src="pt_stock.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>