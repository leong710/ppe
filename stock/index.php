<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 複製本頁網址藥用
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁
    
    $auth_cname  = $_SESSION["AUTH"]["cname"];      // 取出$_session引用
    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用
    $form_type   = "stock";

    // add module function --
        $swal_json = array();
        // stock-crud
        if(isset($_POST["add_stock_submit"])) { $swal_json = store_stock($_REQUEST);  }     // 新增add
        if(isset($_POST["edit_stock_submit"])){ $swal_json = update_stock($_REQUEST); }     // 編輯Edit
        if(isset($_POST["delete_stock"]))     { $swal_json = delete_stock($_REQUEST); }     // 刪除delete
        
        $catalogs = show_catalogs();                        // 取得所有catalog項目，供create使用
        $allLocals = show_allLocal();                       // 取得所有的Local儲存點，供create使用

    // 組合查詢陣列 -- 把fabs讀進來作為[篩選]的select option
        // 1-1a 將fab_id加入sfab_id
        $sfab_id = get_sfab_id($sys_id, "arr");     // 1-1c sfab_id是陣列
            // 1-1c sfab_id是陣列，要轉成字串
            $sfab_id_str = implode(",",$sfab_id);                   // 1-1c sfab_id是陣列，要轉成字串

        // 1-2 組合查詢條件陣列
            $sort_fab_setting = array(
                'sfab_id'   => $sfab_id_str,                        // 1-2.將字串sfab_id加入組合查詢陣列中
                'fab_id'    => "All"
            );
            $fabs = show_fab($sort_fab_setting);                    // 篩選查詢清單用

    // 查詢篩選條件：fab_id 
        $fab_id = (isset($_SESSION[$sys_id]["fab_id"])) ? $_SESSION[$sys_id]["fab_id"] : "0";   // 1-1.取fab_id
        $sort_fab_id = (isset($_REQUEST["fab_id"])) ? $_REQUEST["fab_id"] : $fab_id;    // 有帶查詢，套查詢參數，沒有就先給預設值
        // 查詢篩選條件：cate_no
        $sort_cate_no = (isset($_REQUEST["cate_no"])) ? $_REQUEST["cate_no"] : "All";

        $thisYear = date('Y');                      // 今年年份
        $half = (date('m') <= 6 ) ? "H1" : "H2";    // 半年分界線
        
    // 組合查詢條件陣列
        $query_arr = array(
            'fab_id'        => $sort_fab_id,
            'cate_no'       => $sort_cate_no,
            'thisYear'      => $thisYear,
            'checked_year'  => $thisYear,                  // 建立查詢陣列for顯示今年點檢表
            'half'          => $half,                      // 建立查詢陣列for顯示今年點檢表
            'form_type'     => $form_type
        );
 
        $stocks         = show_stock($query_arr);          // 依查詢條件儲存點顯示存量
        $categories     = show_categories();               // 取得所有分類item
        $sum_categorys  = show_sum_category($query_arr);   // 統計分類與數量
        $myReceives     = show_my_receive($query_arr);     // 列出這個fab_id、今年度的領用單

        $check_yh_list  = check_yh_list($query_arr);       // 查詢自己的點檢紀錄：半年檢
        $check_yh_list_num = count($check_yh_list);        // 計算自己的點檢紀錄筆數：半年檢

        $sortFab        = show_fab($query_arr);            // 查詢fab的細項結果

        extract(show_plan($query_arr));                    // 查詢表單計畫 20240118 == 讓表單呈現 true 或 false

        $per_total = count($stocks);                       //計算總筆數
    
    // 今年年份
        $thisYear = date('Y');
    // 初始化半年後日期，讓系統判斷與highLight
        $toDay = date('Y-m-d');
        $half_month = date('Y-m-d', strtotime($toDay."+6 month -1 day"));   // strtotime()将任何字符串的日期时间描述解析为 Unix 时间戳
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

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
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 p-4 rounded" style="background-color: rgba(255, 255, 255, .8);">
                <div class="row">
                    <div class="col-md-4 py-0">
                        <h5><?php echo isset($sortFab["id"]) ? $sortFab["id"].".".$sortFab["fab_title"]." (".$sortFab["fab_remark"].")":"";?>_庫存管理： </h5>
                    </div>
                    <!-- sort/groupBy function -->
                    <div class="col-md-4 py-0">
                        <form action="" method="POST">
                            <div class="input-group">
                                <span class="input-group-text">篩選</span>
                                <select name="fab_id" id="groupBy_fab_id" class="form-select" onchange="this.form.submit()">
                                    <option value="" hidden>-- 請選擇local --</option>
                                    <?php foreach($fabs as $fab){ ?>
                                        <?php if($sys_role <= 1 || (in_array($fab["id"], $sfab_id)) ){ ?>  
                                            <option value="<?php echo $fab["id"];?>" <?php echo $fab["id"] == $sortFab["id"] ? "selected":"";?>>
                                                <?php echo $fab["id"]."：".$fab["site_title"]."&nbsp".$fab["fab_title"]."( ".$fab["fab_remark"]." )"; echo ($fab["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <!-- 表頭按鈕 -->
                    <div class="col-md-4 py-0 text-end inb">
                        <?php if(isset($_SESSION[$sys_id]) && isset($sortFab["id"])){ ?>
                            <?php if($_inplan && $sys_role <= 2 && ($check_yh_list_num == 0)){?>
                                <div class="inb">
                                    <button type="button" id="checkList_btn" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#checkList"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>&nbsp點檢表</button>
                                </div>
                            <?php } ?>
                            <?php if($sys_role <= 1 ){ ?>
                                <div class="inb">
                                    <button type="button" id="add_stock_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_stock" onclick="add_module('stock')"><i class="fa fa-plus"></i> 新增</button>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php if($per_total != 0){ ?>
                            <div class="inb">
                                <!-- 20231128 下載Excel -->
                                <form id="myForm" method="post" action="../_Format/download_excel.php">
                                    <input type="hidden" name="htmlTable" id="htmlTable" value="">
                                    <button type="submit" name="submit" class="btn btn-success" title="<?php echo isset($sortFab["id"]) ? $sortFab["fab_title"]." (".$sortFab["fab_remark"].")":"";?>" value="stock" onclick="submitDownloadExcel('stock')" >
                                        <i class="fa fa-download" aria-hidden="true"></i> 匯出Excel</button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- Bootstrap Alarm -->
                    <div id="liveAlertPlaceholder" class="col-12 mb-0 pb-0"></div>
                </div>

                <!-- NAV分頁標籤與統計 -->
                <div class="col-12 pb-0 px-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $sort_cate_no == 'All' ? 'active':'';?>" href="?fab_id=<?php echo $sortFab["id"];?>&cate_no=All">
                                All&nbsp<span class="badge bg-secondary"><?php echo $per_total;?></span></a>
                        </li>
                        <?php foreach($sum_categorys as $sum_cate){ ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $sort_cate_no == $sum_cate["cate_no"] ? 'active':'';?>" href="?fab_id=<?php echo $sortFab["id"];?>&cate_no=<?php echo $sum_cate["cate_no"];?>">
                                    <?php echo $sum_cate["cate_no"].".".$sum_cate["cate_title"];?>
                                    <span class="badge bg-secondary"><?php echo $sum_cate["stock_count"];?></span></a>
                            </li>
                        <?php  } ?>
                    </ul>
                </div>
                <!-- by各Local儲存點： -->
                <div class="col-12 bg-white">
                    <table id="stock_list" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><i class="fa fa-check" aria-hidden="true"></i> 儲存點位置</th>
                                <th>分類</th>
                                <th>名稱</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="<?php echo $thisYear;?>今年總累計">年領用</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="
                                    <?php echo ($sys_role <= 1 ) ? "編輯後按Enter才能儲存":"未有編輯權限";?>
                                    ">現量</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="同儲區&同品項將安全存量合併成一筆計算">安量</th>
                                <th>備註說明</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="效期小於6個月將highlight">批號/期限</th>
                                <th>PO no</th>
                                <th>最後更新</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $check_item ="";
                            ?>
                            <?php foreach($stocks as $stock){ ?>
                                <tr <?php if($check_item != $stock['local_title']){?>style="border-top:3px #FFD382 solid;"<?php } ?>>
                                    <td class="word_bk" title="aid_<?php echo $stock['id'];?>"><?php echo $stock['fab_title']."_".$stock['local_title'];?></td>
                                    <td><span class="badge rounded-pill <?php switch($stock["cate_id"]){
                                            case "1": echo "bg-primary"; break;
                                            case "2": echo "bg-success"; break;
                                            case "3": echo "bg-warning text-dark"; break;
                                            case "4": echo "bg-danger"; break;
                                            case "5": echo "bg-info text-dark"; break;
                                            case "6": echo "bg-dark"; break;
                                            case "7": echo "bg-secondary"; break;
                                            default: echo "bg-light text-success"; break;
                                        }?>"><?php echo $stock["cate_no"].".".$stock["cate_title"];?></span></td>
                                    <td class="word_bk"><?php echo $stock["SN"]."_".$stock['pname'];
                                                              echo ($stock["cata_flag"] == "Off") ? "<sup class='text-danger'>-已關閉</sup>":"";?></td>
                                    <td id="receive_<?php echo $stock['local_id'].'_'.$stock['cata_SN'];?>">--</td>

                                    <td id="<?php echo $stock['id'];?>" name="amount" class="fix_amount <?php echo ($stock["amount"] < $stock['standard_lv']) ? "alert_itb":"" ;?>" 
                                        <?php if($sys_role <= 1 ){ ?> contenteditable="true" <?php } ?>>
                                        <?php echo $stock['amount'];?></td>
                                    <td class="<?php echo ($stock["amount"] < $stock['standard_lv']) ? "alert_it":"";?>"><?php echo $stock['standard_lv'];?></td>
                                    <td class="word_bk"><?php echo $stock['stock_remark'];?></td>
                                    <td <?php if($stock["lot_num"] < $half_month){ ?> style="background-color:#FFBFFF;color:red;" data-toggle="tooltip" data-placement="bottom" title="有效期限小於：<?php echo $half_month;?>" <?php } ?>>
                                        <?php echo $stock['lot_num'];?></td>
                                    <td style="font-size: 12px;"><?php echo $stock['po_no'];?></td>
                                    <td style="width:8%;font-size: 12px;" title="最後編輯: <?php echo $stock['updated_user'];?>">
                                        <?php if(isset($stock['id'])){ ?>
                                            <?php if($sys_role <= 1 ){ ?>
                                                    <button type="button" id="edit_stock_btn" value="<?php echo $stock["id"];?>" data-bs-toggle="modal" data-bs-target="#edit_stock" 
                                                        onclick="edit_module('stock', this.value)" ><?php echo $stock['updated_at'];?></button>
                                            <?php }else{ echo $stock['updated_at']; } ?>
                                        <?php } ?></td>
                                </tr>
                                <?php $check_item = $stock['local_title'];?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <hr>
            </div>
        </div>
    </div>
   
<!-- 模組 新增、編輯stock品項 -->
    <div class="modal fade" id="edit_stock" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="modal_action"></span>&nbsp儲存品</h4>

                    <form action="" method="post">
                        <input type="hidden" name="id" id="stock_delete_id">
                        &nbsp&nbsp&nbsp&nbsp&nbsp
                        <span id="modal_delect_btn" class="<?php echo ($sys_role <= 1) ? '':'unblock';?>"></span>
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
                                        <select name="local_id" id="edit_local_id" class="form-control" required onchange="select_local(this.value)">
                                            <option value="" selected hidden>-- 請選擇儲存點 --</option>
                                            <?php foreach($allLocals as $local){ ?>
                                                <?php if($sys_role <= 1 || (in_array($local["fab_id"], $sfab_id))){ ?>  
                                                    <option value="<?php echo $local["id"];?>" >
                                                        <?php echo $local["id"]."：".$local["site_title"]."&nbsp".$local["fab_title"]."_".$local["local_title"]; echo ($local["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label for="edit_local_id" class="form-label">local_id/儲存位置：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <select name="cata_SN" id="edit_cata_SN" class="form-control" required onchange="update_standard_lv(this.value)">
                                            <option value="" selected hidden>-- 請選擇器材 --</option>
                                            <?php foreach($catalogs as $catalog){ ?>
                                                <option value="<?php echo $catalog["SN"];?>" title="<?php echo $catalog["cata_remark"];?>"><?php echo $catalog["SN"]."：".$catalog["pname"];?></option>
                                            <?php } ?>
                                        </select>
                                        <label for="edit_cata_SN" class="form-label">cata_SN/器材名稱：<sup class="text-danger">*</sup></label>
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
                                        <input type="number" name="standard_lv" id="edit_standard_lv" class="form-control t-center" placeholder="標準數量(管理員填)" min="1" max="400"
                                            <?php echo $sys_role <= 1 ? "":"readonly"; ?> >
                                        <label for="edit_standard_lv" class="form-label">standard_lv/安全存量：<sup class="text-danger"><?php echo ($sys_role >= 1) ? " *":" - disabled";?></sup></label>
                                    </div>
                                </div>
                                <!-- 右側-批號 -->
                                <div class="col-12 col-md-6 py-0">
                                    <div class="form-floating">
                                        <input type="number" name="amount" id="edit_amount" class="form-control t-center" required placeholder="正常數量" min="0" max="999">
                                        <label for="edit_amount" class="form-label">amount/現場存量：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="po_no" id="edit_po_no" class="form-control" placeholder="PO採購編號">
                                        <label for="edit_po_no" class="form-label">po_no/PO採購編號：</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="pno" id="edit_pno" class="form-control" placeholder="料號">
                                        <label for="edit_pno" class="form-label">pno/料號：</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <textarea name="stock_remark" id="edit_stock_remark" class="form-control" style="height: 100px"></textarea>
                                        <label for="edit_stock_remark" class="form-label">stock_remark/備註說明：</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating pb-0">
                                        <input type="date" name="lot_num" id="edit_lot_num" class="form-control" required>
                                        <label for="edit_lot_num" class="form-label">lot_num/批號/期限：<sup class="text-danger">*</sup></label>
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
                        <div class="col-12 text-end p-0" id="edit_stock_info"></div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="id" id="stock_edit_id" >
                            <input type="hidden" name="fab_id" value="<?php echo $sortFab["id"];?>">
                            <input type="hidden" name="cate_no" value="<?php echo isset($_REQUEST['cate_no']) ? $_REQUEST['cate_no'] : 'All' ;?>">
                            <input type="hidden" name="updated_user" value="<?php echo $auth_cname;?>">
                            <span id="modal_button" class="<?php echo ($sys_role <= 2) ? '':'unblock';?>"></span>
                            <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- 模組 所屬區域器材儲存量總表for年檢用 -->
    <div class="modal fade" id="checkList" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">PPE儲存量總表年檢：</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="store_checkList.php" method="post" onsubmit="this.ccOmager.disabled=false;">
                    <div class="modal-body p-4">
                        <div class="col-12 rounded bg-light">
                            <div class="row">
                                <div class="col-md-7 py-0">
                                    <div>
                                        點檢廠區：<?php echo $sortFab["fab_title"]; echo $sortFab["fab_remark"] ? " (".$sortFab["fab_remark"].")":"";?></br>
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

                            <div class="row">
                                <div class="col-md-6 py-0">
                                </div>
                                <div class="col-md-6 py-0">
                                    <h5>
                                        <input type="checkbox" name="ccOmager" value="1" id="ccOmager" class="form-check-input" checked <?php echo $sys_role == 0 ? "":" disabled";?>>
                                        <label for="ccOmager" class="form-label" <?php echo $sys_role == 0 ? "":" title='您無權修改!'";?>>mapp知會所屬主管!</label>
                                    </h5>
                                    <h5>
                                        <input type="checkbox" name="iAgree" value="0" id="iAgree" class="form-check-input" required>
                                        <label for="iAgree" class="form-label">我已確認完畢，現況數量無誤!</label>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="action"          value="store_checkList">
                            <input type="hidden" name="form_type"       value="<?php echo $form_type;?>">
                            <input type="hidden" name="up_href"         value="<?php echo $up_href;?>">
                            <input type="hidden" name="fab_id"          value="<?php echo $sortFab["id"];?>">
                            <input type="hidden" name="fab_title"       value="<?php echo $sortFab["fab_title"];?>">
                            <input type="hidden" name="fab_remark"      value="<?php echo $sortFab["fab_remark"];?>">
                            <input type="hidden" name="sign_code"       value="<?php echo $sortFab["sign_code"];?>">
                            <input type="hidden" name="emp_id"          value="<?php echo $auth_emp_id;?>">
                            <input type="hidden" name="cname"           value="<?php echo $auth_cname; ?>">
                            <input type="hidden" name="checked_year"    value="<?php echo $today_year;?>">
                            <input type="hidden" name="half"            value="<?php echo $half;?>">
                            <input type="hidden" name="cate_no"         value="<?php echo $sort_cate_no;?>">
                            <input type="hidden" name="updated_user"    value="<?php echo $auth_cname;?>">
                            <span id="modal_button" class="<?php echo ($sys_role <= 2) ? '':'unblock';?>">
                                <input type="submit" value="Submit" name="submit" class="btn btn-primary">
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

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>
// // // 開局導入設定檔
    var allLocals   = <?=json_encode($allLocals)?>;                   // 引入所有local的allLocals值
    var low_level   = [];                                              // 宣告low_level變數
    var stock       = <?=json_encode($stocks)?>;                       // 引入div_stocks資料
    var stock_item  = ['id','local_id','cata_SN','standard_lv','amount','po_no','pno','stock_remark','lot_num'];    // 交給其他功能帶入 delete_supp_id
    var swal_json   = <?=json_encode($swal_json)?>;                   // 引入swal_json值
    
// 先定義一個陣列(裝輸出資料使用)for 下載Excel
    var listData        = <?=json_encode($stocks)?>;                   // 引入stocks資料
// 找出Local_id算SN年領用量
    var myReceives      = <?=json_encode($myReceives)?>;               // 引入myReceives資料，算年領用量
    var receiveAmount   = [];                                           // 宣告變數陣列，承裝Receives年領用量

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

<script src="stock.js?v=<?=time()?>"></script>

<?php include("../template/footer.php"); ?>