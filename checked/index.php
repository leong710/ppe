<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 先給預設值
    $auth_fab_id = $_SESSION[$sys_id]["fab_id"];
    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];         // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];          // 取出$_session引用

    if(isset($_POST["checked_delete"])){ $swal_json = delete_checked_item($_REQUEST); }      // 刪除delete

        // 今年年份 // 今年
        $thisYear = date('Y');                          
        // // *** 篩選組合項目~~
        $checked_year = (isset($_REQUEST["checked_year"])) ? $_REQUEST["checked_year"] : $thisYear;   // 今年
        // $_year = "All";                              // 全部

        $half = (date('m') <= 6 ) ? "H1" : "H2";        // 半年分界線

    $query_arr = array(                                 // 組合查詢陣列
        'fab_id'        => $auth_fab_id,
        'emp_id'        => $auth_emp_id,
        'checked_year'  => $checked_year,               // 建立查詢陣列for顯示今年點檢表
        'half'          => $half                        // 建立查詢陣列for顯示今年點檢表
    );

    $checked_lists      = show_checked($query_arr);       // 調閱點檢表
    $allchecked_years   = show_allchecked_year();         // 取出checked年份清單 => 供checked頁面篩選
    $fabs               = show_fab();                     // index thead
    $checked_years      = show_checked_year($query_arr);  // index tbody


    // <!-- 20211215分頁工具 -->
        $per_total = count($checked_lists);     //計算總筆數
        $per = 25;                              //每頁筆數
        $pages = ceil($per_total/$per);         //計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        if(!isset($_GET['page'])){              //!isset 判斷有沒有$_GET['page']這個變數
            $page = 1;	  
        }else{
            $page = $_GET['page'];
        }
        $start = ($page-1)*$per;                //每一頁開始的資料序號(資料庫序號是從0開始)
            // 合併嵌入分頁工具
            $query_arr["start"] = $start;
            $query_arr["per"] = $per;

        $div_checkeds = show_checked($query_arr);
        $page_start = $start +1;                //選取頁的起始筆數
        $page_end = $start + $per;              //選取頁的最後筆數
        if($page_end>$per_total){               //最後頁的最後筆數=總筆數
            $page_end = $per_total;
        }
    // <!-- 20211215分頁工具 -->
 
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .TOP {
            background-image: URL('../images/checked3.jpg');
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            /* overflow: hidden; 會影響表頭黏貼功能*/
            background-attachment: fixed;
            /* background-position: center top; 對齊*/
            background-position: left top;
            background-repeat: no-repeat;
            /* background-size: cover; */
            background-size: contain;
            padding-top: 50px;
        }
        #reviewBtn {
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <div class="col-12">
        <!-- 表頭 -->
        <div class="row justify-content-center">
            <div class="col_xl_11 col-11 rounded p-3" style="background-color: rgba(255, 255, 255, .8);">
                <!-- 表頭 -->
                <div class="row px-2">
                    <div class="col-md-4">
                        <div>
                            <h3><b>點檢紀錄總表</b></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form action="" method="post">
                            <input type="hidden" name="activeTab" value="0">
                            <div class="input-group">
                                <span class="input-group-text">篩選年度</span>
                                <select name="checked_year" id="groupBy_cate" class="form-select">
                                    <option value="" hidden >-- 年度 / All --</option>
                                    <?php foreach($allchecked_years as $checked_y){ ?>
                                        <option value="<?php echo $checked_y["checked_year"];?>" <?php echo ($checked_y["checked_year"] == $checked_year) ? "selected":"";?>>
                                            <?php echo $checked_y["checked_year"]."y";?></option>
                                    <?php } ?>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary">查詢</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">

                    </div>
                </div>
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder" class="col-11 mb-0 p-0"></div>
                <!-- 歷史清單 -->
                <div class="row" >
                    <!-- 歷史點檢紀錄list -->
                    <div class="col-12">
                        <div class="col-xl-12 col-12 rounded bg-light">
                            <!-- 20211215分頁工具 -->               
                                <div class="row">
                                    <div class="col-12 col-md-6">	
                                        <?php
                                            //每頁顯示筆數明細
                                            echo '顯示 '.$page_start.' 到 '.$page_end.' 筆 共 '.$per_total.' 筆，目前在第 '.$page.' 頁 共 '.$pages.' 頁'; 
                                        ?>
                                    </div>
                                    <div class="col-12 col-md-6 text-end">
                                        <?php
                                            if($pages>1){  //總頁數>1才顯示分頁選單
    
                                                //分頁頁碼；在第一頁時,該頁就不超連結,可連結就送出$_GET['page']
                                                if($page=='1'){
                                                    echo "首頁 ";
                                                    echo "上一頁 ";		
                                                }else{
                                                    echo "<a href=?page=1>首頁 </a> ";
                                                    echo "<a href=?page=".($page-1).">上一頁 </a> ";		
                                                }
    
                                                //此分頁頁籤以左、右頁數來控制總顯示頁籤數，例如顯示5個分頁數且將當下分頁位於中間，則設2+1+2 即可。若要當下頁位於第1個，則設0+1+4。也就是總合就是要顯示分頁數。如要顯示10頁，則為 4+1+5 或 0+1+9，以此類推。	
                                                for($i=1 ; $i<=$pages ;$i++){ 
                                                    $lnum = 2;  //顯示左分頁數，直接修改就可增減顯示左頁數
                                                    $rnum = 2;  //顯示右分頁數，直接修改就可增減顯示右頁數
    
                                                    //判斷左(右)頁籤數是否足夠設定的分頁數，不夠就增加右(左)頁數，以保持總顯示分頁數目。
                                                    if($page <= $lnum){
                                                        $rnum = $rnum + ($lnum-$page+1);
                                                    }
    
                                                    if($page+$rnum > $pages){
                                                        $lnum = $lnum + ($rnum - ($pages-$page));
                                                    }
                                                    //分頁部份處於該頁就不超連結,不是就連結送出$_GET['page']
                                                    if($page-$lnum <= $i && $i <= $page+$rnum){
                                                        if($i==$page){
                                                            echo $i.' ';
                                                        }else{
                                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                        }
                                                    }
                                                }
                                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                                if($page==$pages){
                                                    echo " 下一頁";
                                                    echo " 末頁";
                                                }else{
                                                    echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?page=".$pages."> 末頁</a>";		
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                            <!-- 20211215分頁工具 -->
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>點檢年度/分類</th>
                                        <?php foreach($fabs as $fab){
                                            echo "<th title='aid_".$fab["fab_id"]."'>".$fab["fab_title"]."</th>";
                                        }?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($checked_years as $checked_year){ ?>
                                        <tr>
                                            <td><?php echo $checked_year['checked_year']."_".$checked_year['half']."</br>".$checked_year['form_type'];
                                                echo $checked_year['form_type'] == "stock" ? " (個人防護具)" : " (除汙器材)" ;
                                                ?></td>
                                            <?php foreach($fabs as $fab){
                                                echo "<td id='{$checked_year['checked_year']}_{$checked_year['half']}_{$checked_year['form_type']}_{$fab['fab_id']}'> </td>";
                                            }?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <hr>
                            <!-- 20211215分頁工具 -->               
                                <div class="row">
                                    <div class="col-12 col-md-6 pt-0">	
                                        <?php
                                            //每頁顯示筆數明細
                                            echo '顯示 '.$page_start.' 到 '.$page_end.' 筆 共 '.$per_total.' 筆，目前在第 '.$page.' 頁 共 '.$pages.' 頁'; 
                                        ?>
                                    </div>
                                    <div class="col-12 col-md-6 pt-0 text-end">
                                        <?php
                                            if($pages>1){  //總頁數>1才顯示分頁選單
    
                                                //分頁頁碼；在第一頁時,該頁就不超連結,可連結就送出$_GET['page']
                                                if($page=='1'){
                                                    echo "首頁 ";
                                                    echo "上一頁 ";		
                                                }else{
                                                    echo "<a href=?page=1>首頁 </a> ";
                                                    echo "<a href=?page=".($page-1).">上一頁 </a> ";		
                                                }
    
                                                //此分頁頁籤以左、右頁數來控制總顯示頁籤數，例如顯示5個分頁數且將當下分頁位於中間，則設2+1+2 即可。若要當下頁位於第1個，則設0+1+4。也就是總合就是要顯示分頁數。如要顯示10頁，則為 4+1+5 或 0+1+9，以此類推。	
                                                for($i=1 ; $i<=$pages ;$i++){ 
                                                    $lnum = 2;  //顯示左分頁數，直接修改就可增減顯示左頁數
                                                    $rnum = 2;  //顯示右分頁數，直接修改就可增減顯示右頁數
    
                                                    //判斷左(右)頁籤數是否足夠設定的分頁數，不夠就增加右(左)頁數，以保持總顯示分頁數目。
                                                    if($page <= $lnum){
                                                        $rnum = $rnum + ($lnum-$page+1);
                                                    }
    
                                                    if($page+$rnum > $pages){
                                                        $lnum = $lnum + ($rnum - ($pages-$page));
                                                    }
                                                    //分頁部份處於該頁就不超連結,不是就連結送出$_GET['page']
                                                    if($page-$lnum <= $i && $i <= $page+$rnum){
                                                        if($i==$page){
                                                            echo $i.' ';
                                                        }else{
                                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                        }
                                                    }
                                                }
                                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                                if($page==$pages){
                                                    echo " 下一頁";
                                                    echo " 末頁";
                                                }else{
                                                    echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?page=".$pages."> 末頁</a>";		
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                            <!-- 20211215分頁工具 -->
                        </div>                  
                    </div>                  
                </div>
            </div>
            
        </div>
    </div>

<!-- 彈出畫面模組 除汙器材領用 品項 -->
    <div class="modal fade" id="review_checked" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <!-- <div class="modal-dialog modal-fullscreen"> -->
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header add_mode_bgc">
                    <h4 class="modal-title"><span id="modal_action"></span>檢視點檢紀錄</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body bg-light p-2">
                    <!-- 第一列 文件表頭 -->
                    <div class="col-xl-12 col-12 py-0">
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    點檢單號：<span id="aid_id"></span></br>
                                    點檢日期：<span id="created_at"></span></br>
                                    點檢廠區：<span id="fab_title"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <!-- // 這裡的updated_user指的是點檢表單儲存人 -->
                                    點檢人員：<span id="updated_user"></span></br>
                                    表單分類：<span id="form_type"></span></br>
                                    點檢年度：<span id="checked_year"></span>_<span id="half"></span>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <!-- 第二排 歷史儲存狀況table -->
                    <div class="col-xl-12 col-12 py-0">
                        <table class="for-table">
                            <thead>
                                <tr>
                                    <th>儲存點位置</th>
                                    <th>分類</th>
                                    <th>名稱</th>
                                    <th>size</th>
                                    <th>安量</th>
                                    <th>存量</th>
                                    <th>備註說明</th>
                                    <th>批號/PO</th>
                                    <th>更新日期</th>
                                </tr>
                            </thead>
                            <tbody id="checked_tbody">
                                <!-- 鋪設內容 -->
                            </tbody>
                        </table>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <label for="checked_remark" class="form-check-label" >點檢備註說明：</label>
                                <textarea name="checked_remark" id="checked_remark" class="form-control" rows="5" readonly ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <form action="" method="POST">
                                <input type="hidden" name="id" id="checked_delete_id" value="">
                                <?php if($sys_role <= 1){ ?>
                                    <input type="submit" name="checked_delete" value="刪除紀錄" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                                <?php } ?>
                            </form>
                        </div>
                        <div class="col-12 col-md-6 text-end">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- 彈出畫面模組2 匯出CSV-->
    <div class="modal fade" id="doCSV" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">匯出儲存點存量紀錄(csv)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- 20220606測試CSV匯入/匯出 -->
                <form id="addform" action="docsv.php?action=exportfab" method="post"> 
                    <div class="modal-body p-4" >
                        <div class="col-12">
                            <label for="" class="form-label">請選擇您要查詢下載的fab：<sup class="text-danger"> *</sup></label>
                            <select name="fab_id" id="fab_id" class="form-control" required >
                                <option value="" selected hidden>--請選擇fab--</option>
                                <?php foreach($fabs as $fab){ ?>
                                    <option value="<?php echo $fab["id"];?>"><?php echo $fab["id"];?>: <?php echo $fab["fab_title"];?> (<?php echo $fab["remark"];?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <input type="submit" class="btn btn-warning" value="匯出fab存量CSV"> 
                        </div>
                        <hr>
                        <div class="col-12 text-end">
                            <a href="docsv.php?action=export" title="匯出tn存量CSV" class="btn btn-success"><i class="fa fa-download" aria-hidden="true"></i> 匯出tn存量CSV</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script>
    // <php echo "var check_yh_list_num ='$check_yh_list_num';";?>
    // 半年檢
    var check_yh_list_num   = 'x';
    var thisYear            = '<?=$thisYear?>';
    var half                = '<?=$half?>';
    var checked_lists       = <?=json_encode($checked_lists)?>;
    var checked_item = ["id", "created_at", "fab_title", "fab_remark", "updated_user", "form_type", "checked_year", "half", "checked_remark", "stocks_log"]    // 定義要抓的key

</script>

<script src="checked.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>