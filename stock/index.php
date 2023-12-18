<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_id_role = $_SESSION[$sys_id]["role"];      // 取出$_session引用

            // if(isset($_GET["local_id"])){
                //     $select_local = select_local($_REQUEST);

                // }else{
                //     $select_local = array(
                //         'id' => '0',
                //         'fab_id' => '0'
                //     );
                // }

                // if(empty($select_local)){              // 查無資料時返回指定頁面
                //     header("location:../");     // 用這個，因為跳太快
                //     exit;
                // }
                // // create時用自己區域 // 將自己的fab_id設為初始值 

    // add module function --
        $swal_json = array();
        // 新增add
        if(isset($_POST["add_stock_submit"])){
            $swal_json = store_stock($_REQUEST);
        }       
        // 編輯Edit
        if(isset($_POST["edit_stock_submit"])){
            $swal_json = update_stock($_REQUEST);
        }
        // 刪除delete
        if(isset($_POST["delete_stock"])){
            $swal_json = delete_stock($_REQUEST);
        }
        
        $catalogs = show_catalogs();                        // 取得所有catalog項目，供create使用
        $allLocals = show_allLocal();                       // 取得所有的Local儲存點，供create使用
        // $allLocals = show_local($list_issue_setting);       // 取得Fab下的Local儲存點，供create使用 => 停用原因：改卡權限

    // 組合查詢陣列 -- 把fabs讀進來作為[篩選]的select option
        // 1-1a 將fab_id加入sfab_id
            if(isset($_SESSION[$sys_id]["fab_id"])){
                $fab_id = $_SESSION[$sys_id]["fab_id"];              // 1-1.取fab_id
            }else{
                $fab_id = "0";
            }
            $sfab_id = $_SESSION[$sys_id]["sfab_id"];                // 1-1.取sfab_id
            if(!in_array($fab_id, $sfab_id)){                        // 1-1.當fab_id不在sfab_id，就把部門代號id套入sfab_id
                array_push($sfab_id, $fab_id);
            }
            // 1-1b 將sign_code涵蓋的fab_id加入sfab_id
            if(isset($_SESSION["AUTH"]["sign_code"])){
                $sort_fab_setting["sign_code"] = $_SESSION["AUTH"]["sign_code"];
                $coverFab_lists = show_coverFab_lists($sort_fab_setting);
                if(!empty($coverFab_lists)){
                    foreach($coverFab_lists as $coverFab){
                        array_push($sfab_id, $coverFab["id"]);
                    }
                }
            }
            // 1-1c sfab_id是陣列，要轉成字串
            $sfab_id_str = implode(",",$sfab_id);                   // 1-1c sfab_id是陣列，要轉成字串

        // 1-2 組合查詢條件陣列
            $sort_fab_setting = array(
                'sfab_id'   => $sfab_id_str,                        // 1-2.將字串sfab_id加入組合查詢陣列中
                'fab_id'    => "All"
            );
            $fabs = show_fab($sort_fab_setting);                    // 篩選查詢清單用

    // 查詢篩選條件：fab_id
        if(isset($_REQUEST["fab_id"])){    // 有帶查詢，套查詢參數
            $sort_fab_id = $_REQUEST["fab_id"];
        }else{                          // 先給預設值
            $sort_fab_id = $fab_id;
        }
        // 查詢篩選條件：cate_no
        if(isset($_REQUEST["cate_no"])){
            $sort_cate_no = $_REQUEST["cate_no"];
        }else{
            $sort_cate_no = "All";
        }
    // 組合查詢條件陣列
        $list_issue_setting = array(
            'fab_id'    => $sort_fab_id,
            'cate_no'   => $sort_cate_no,
            'thisYear'  => date('Y')
        );
 
        $stocks = show_stock($list_issue_setting);                  // 依查詢條件儲存點顯示存量
        $categories = show_categories();                            // 取得所有分類item
        $sum_categorys = show_sum_category($list_issue_setting);    // 統計分類與數量
        $myReceives = show_my_receive($list_issue_setting);         // 列出這個fab_id、今年度的領用單

        $sortFab = show_fab($list_issue_setting);                   // 查詢fab的細項結果
        if(empty($sortFab)){                                        // 查無資料時返回指定頁面
            echo "<script>history.back()</script>";                 // 用script導回上一頁。防止崩煃
        }


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
            //     $list_issue_setting['start'] = $start;
            //     $list_issue_setting['per'] = $per;

            // $div_stocks = show_stock($list_issue_setting);
            // // $div_stocks = stock_page_div($start, $per, $list_issue_setting);
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
        $half_month = date('Y-m-d', strtotime($toDay."+6 month -1 day"));

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

        .body > ul {
            padding-left: 0px;
        }

        /* 凸顯可編輯欄位 */
            .fix_amount:hover {
                /* font-size: 1.05rem; */
                font-weight: bold;
                text-shadow: 3px 3px 5px rgba(0,0,0,.5);
            }
        /* 新增與編輯 module表頭顏色 */
            .add_mode_bgc {          
                background-color: #ADD8E6;
            }
            .edit_mode_bgc {
                background-color: #FFFACD;
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
    </style>
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                // icon: "../../libs/jquery/Wedges-3s-120px.gif",
                icon: "../../libs/jquery/loading.gif",
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
            <div class="col_xl_12 col-12 p-4 rounded" style="background-color: rgba(255, 255, 255, .8);">
                <div class="row">
                    <div class="col-md-4 py-0">
                        <h5><?php echo isset($sortFab["id"]) ? $sortFab["id"].".".$sortFab["fab_title"]." (".$sortFab["fab_remark"].")":"";?>_庫存管理： </h5>
                    </div>
                    <!-- sort/groupBy function -->
                    <div class="col-md-4 py-0">
                        <form action="" method="get">
                            <div class="input-group">
                                <span class="input-group-text">篩選</span>
                                <select name="fab_id" id="groupBy_fab_id" class="form-select" onchange="this.form.submit()">
                                    <option value="" hidden>-- 請選擇local --</option>
                                    <?php foreach($fabs as $fab){ ?>
                                        <?php if($sys_id_role <= 0 || (in_array($fab["id"], $sfab_id)) ){ ?>  
                                            <option value="<?php echo $fab["id"];?>" <?php echo $fab["id"] == $sortFab["id"] ? "selected":"";?>>
                                                <?php echo $fab["id"]."：".$fab["site_title"]."&nbsp".$fab["fab_title"]."( ".$fab["fab_remark"]." )"; echo ($fab["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <!-- <button type="submit" class="btn btn-outline-secondary">查詢</button> -->
                            </div>
                        </form>
                    </div>
                    <!-- 表頭按鈕 -->
                    <div class="col-md-4 py-0 text-end">
                        <?php if(isset($_SESSION[$sys_id]) && isset($sortFab["id"])){ ?>
                            <?php if($sys_id_role <= 1 || ( $sys_id_role <= 2 && ( ($sortFab["id"] == $_SESSION[$sys_id]["fab_id"]) || (in_array($sortFab["id"], $_SESSION[$sys_id]["sfab_id"])) ) ) ){ ?>
                                <button type="button" id="add_stock_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_stock" onclick="add_module('stock')"><i class="fa fa-plus"></i> 單筆新增</button>
                                <button type="button" id="doCSV_btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#doCSV"><i class="fa fa-download" aria-hidden="true"></i>&nbsp匯出清單</button>
                            <?php } ?>
                        <?php } ?>
                    </div>
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
                    <!-- 20211215分頁工具 -->               
                    <table id="stock_list" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <!-- <th>ai</th> -->
                                <th><i class="fa fa-check" aria-hidden="true"></i> 儲存點位置</th>
                                <th>分類</th>
                                <th>名稱</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="<?php echo $thisYear;?>今年總累計">年領用</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="編輯後按Enter才能儲存">現量</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="同儲區&同品項將安全存量合併成一筆計算">安量</th>
                                <th>備註說明</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="效期小於6個月將highlight">批號/期限</th>
                                <th>PO no</th>
                                <th>最後更新</th>
                            </tr>
                        </thead>
                        <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                        <tbody>
                            <?php 
                                $check_item ="";
                            ?>
                            <?php foreach($stocks as $stock){ ?>
                                <tr <?php if($check_item != $stock['local_title']){?>style="border-top:3px #FFD382 solid;"<?php } ?>>
                                    <!-- <td style="font-size: 12px;"><php echo $stock['id'];?></td> -->
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
                                    <td class="word_bk"><?php echo $stock["SN"]."_".$stock['pname'];?></td>

                                    <td id="receive_<?php echo $stock['local_id'].'_'.$stock['cata_SN'];?>">--</td>

                                    <td id="<?php echo $stock['id'];?>" name="amount" class="fix_amount <?php echo ($stock["amount"] < $stock['standard_lv']) ? "alert_itb":"" ;?> " contenteditable="true">
                                        <?php echo $stock['amount'];?></td>
                                    <td class="<?php echo ($stock["amount"] < $stock['standard_lv']) ? "alert_it":"";?>"><?php echo $stock['standard_lv'];?></td>
                                    <td class="word_bk"><?php echo $stock['stock_remark'];?></td>
                                    <td <?php if($stock["lot_num"] < $half_month){ ?> class="background-color:#FFBFFF;color:red;" data-toggle="tooltip" data-placement="bottom" title="有效期限小於：<?php echo $half_month;?>" <?php } ?>>
                                        <?php echo $stock['lot_num'];?></td>
                                    <td style="font-size: 12px;"><?php echo $stock['po_no'];?></td>
                                    <td style="width:8%;font-size: 12px;" title="最後編輯: <?php echo $stock['updated_user'];?>">
                                        <?php if(isset($stock['id'])){ ?>
                                            <?php if($sys_id_role <= 1 || ( $sys_id_role <= 2 && 
                                                ($_SESSION[$sys_id]["fab_id"] == $sortFab["id"] || in_array($sortFab["id"], $_SESSION[$sys_id]["sfab_id"])) )){ ?>
                                                    <button type="button" id="edit_stock_btn" value="<?php echo $stock["id"];?>" data-bs-toggle="modal" data-bs-target="#edit_stock" 
                                                        onclick="edit_module('stock', this.value)" ><?php echo $stock['updated_at'];?></button>
                                            <?php }else{ echo $stock['updated_at']; } ?>
                                        <?php } ?></td>
                                </tr>
                                <?php $check_item = $stock['local_title'];?>
                            <?php } ?>
                        </tbody>
                    </table>
                    <!-- 20211215分頁工具 -->               
                </div>
                <hr>
                 <!-- 尾段：debug訊息 -->
                 <?php if(isset($_REQUEST["debug"])){
                    include("debug_board.php"); 
                } ?>
            </div>
        </div>
    </div>
   
<!-- 彈出畫面模組 新增、編輯stock品項 -->
    <div class="modal fade" id="edit_stock" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="modal_action"></span>&nbsp儲存品</h4>

                    <form action="" method="post">
                        <input type="hidden" name="id" id="stock_delete_id">
                        <?php if($sys_id_role <= 1){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span id="modal_delect_btn"></span>
                        <?php } ?>
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
                                                <?php if($sys_id_role <= 1 || $local["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($local["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
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
                                            <?php echo $sys_id_role <= 1 ? "":"readonly"; ?> >
                                        <label for="edit_standard_lv" class="form-label">standard_lv/安全存量：<sup class="text-danger"><?php echo ($sys_id_role >= 1) ? " *":" - disabled";?></sup></label>
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
                        <!-- 最後編輯資訊 -->
                        <div class="col-12 text-end p-0" id="edit_stock_info"></div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="id" id="stock_edit_id" >
                            <input type="hidden" name="fab_id" value="<?php echo $sortFab["id"];?>">
                            <input type="hidden" name="cate_no" value="<?php echo isset($_REQUEST['cate_no']) ? $_REQUEST['cate_no'] : 'All' ;?>">
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($sys_id_role <= 2){ ?>   
                                <span id="modal_button"></span>
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

<!-- 彈出畫面模組 匯出CSV-->
    <div class="modal fade" id="doCSV" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title">匯出儲存點存量清單</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4" >
                    <!-- 20220606 匯出csv -->
                    <div class="col-12 border rounded add_mode_bgc px-3">
                        <form id="doCSVform" action="docsv.php" method="post"> 
                            <div class="row">
                                <div class="col-12 py-0">
                                    <label for="" class="form-label">請選擇您要查詢下載的fab：<sup class="text-danger"> *</sup></label>
                                    <select name="fab_id" id="fab_id" class="form-control" required >
                                        <option value="All" selected>-- 全部fab --</option>
                                        <?php foreach($fabs as $fab){ ?>
                                            <option value="<?php echo $fab["id"];?>">
                                            <?php echo $fab["id"]."：".$fab["site_title"]."&nbsp".$fab["fab_title"]."( ".$fab["fab_remark"]." )"; echo ($fab["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 py-0">
                                    <label for="" class="form-label">存量狀態：<sup class="text-danger"> *</sup></label>
                                    <input type="radio" name="state" value="0" id="0" class="form-check-input" checked>
                                    <label for="0" class="form-check-label">全部</label>&nbsp&nbsp
                                </div>
                                <div class="col-12 col-md-6 py-0 text-end">
                                    <input type="hidden" name="action" value="export"> 
                                    <button type="submit" class="btn btn-warning" data-bs-dismiss="modal" ><i class="fa fa-download" aria-hidden="true"></i> 匯出&nbspCSV</button>
                                </div>
                            </div>
                        </form> 
                    </div>
                    <!-- 20231128 下載Excel -->
                    <?php if($per_total != 0){ ?>
                        <hr>
                        <form id="myForm" method="post" action="../_Format/download_excel.php">
                            <div class="row">
                                <div class="col-12 text-end">
                                    <!-- 下載EXCEL的觸發 -->
                                    <input type="hidden" name="htmlTable" id="htmlTable" value="">
                                    <button type="submit" name="submit" class="btn btn-success" data-bs-dismiss="modal" value="stock" onclick="submitDownloadExcel('stock')" >
                                        <i class="fa fa-download" aria-hidden="true"></i> 匯出&nbsp<?php echo isset($sortFab["id"]) ? $sortFab["fab_title"]." (".$sortFab["fab_remark"].")":"";?>Excel</button>
                                </div>
                            </div>
                        </form>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    </div>
                </div>
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
    var allLocals   = <?=json_encode($allLocals);?>;                   // 引入所有local的allLocals值
    var low_level   = [];                                              // 宣告low_level變數
    var stock       = <?=json_encode($stocks);?>;                       // 引入div_stocks資料
    var stock_item  = ['id','local_id','cata_SN','standard_lv','amount','po_no','pno','stock_remark','lot_num'];    // 交給其他功能帶入 delete_supp_id
    var swal_json   = <?=json_encode($swal_json);?>;                   // 引入swal_json值
    
// 先定義一個陣列(裝輸出資料使用)for 下載Excel
    var listData = <?=json_encode($stocks);?>;                         // 引入stocks資料
// 找出Local_id算SN年領用量
    var myReceives  = <?=json_encode($myReceives);?>;                  // 引入myReceives資料，算年領用量
    var receiveAmount = [];                                             // 宣告變數陣列，承裝Receives年領用量

    // 彙整出SN年領用量
    Object(myReceives).forEach(function(row){
        let csa = JSON.parse(row['cata_SN_amount']);
        Object.keys(csa).forEach(key =>{
            let pay = Number(csa[key]['pay']);
            let l_key = row['local_id'] +'_'+ key;
            if(receiveAmount[l_key]){
                receiveAmount[l_key] += pay;
            }else{
                receiveAmount[l_key] = pay;
            }
            console.log(l_key, pay)
        })
    });
    // 選染到Table上指定欄位
    Object.keys(receiveAmount).forEach(key => {
        let value = receiveAmount[key];
        $('#receive_'+key).empty();
        $('#receive_'+key).append(value);
    })


    

</script>

<script src="stock.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>