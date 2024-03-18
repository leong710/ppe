<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDeniedAdmin($sys_id);
    if(!isset($_SESSION)){                                                          // 確認session是否啟動
        session_start();
    }

    $sys_role = (isset($_SESSION[$sys_id]["role"])) ? $_SESSION[$sys_id]["role"] : "";      // 取出$_session引用

    // 編輯功能
        if(isset($_POST["deleteLog"])){ deleteLog($_REQUEST); }                     // 刪除整大串
        if(isset($_POST["delLog_item"])){ delLog_item($_REQUEST); }                 // 刪除小項

    // 取得年參數
        $_year = (isset($_REQUEST["_year"])) ? $_REQUEST["_year"] : date('Y');      // 今年
    // 取得月參數
        $_month = (isset($_REQUEST["_month"])) ? $_REQUEST["_month"] : date('m');   // 今月 // $_month = "All";  
        
    // 組合查詢陣列
        $query_arr = array(                           // 組合查詢陣列 -- 建立查詢陣列for顯示今年領用單
            '_year' => $_year,
            '_month' => $_month
        );

    $row_lists    = show_log_list($query_arr);
    $row_lists_yy = show_log_GB_year();              // 取Logs所有年月作為篩選

    // <!-- 20211215分頁工具 -->
        $per_total = count($row_lists);     //計算總筆數
        $per = 5;                           //每頁筆數
        $pages = ceil($per_total/$per);     //計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        // !isset 判斷有沒有$_GET['page']這個變數
        $page = (!isset($_GET['page'])) ? 1 : $_GET['page'];
        $start = ($page-1)*$per;            //每一頁開始的資料序號(資料庫序號是從0開始)
        // 合併嵌入分頁工具
            $query_arr["start"]  = $start;
            $query_arr["per"]    = $per;
        $row_lists_div = show_log_list($query_arr);
        $page_start = $start +1;            //選取頁的起始筆數
        $page_end = $start + $per;          //選取頁的最後筆數
        if($page_end>$per_total){           //最後頁的最後筆數=總筆數
            $page_end = $per_total;
        }
    // <!-- 20211215分頁工具 -->

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <style>
        /* tr > td {
            text-align: left;
        } */
        .mg_msg {
            width: 75%;
        }
        .NG {
            background-color: pink;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 rounded p-3" style="background-color: rgba(255, 255, 255, .6);">
                <div id="table">
                    <!-- send_list -->
                    <div id="send_list_table" class="col-12">
                        <div class="row">
                            <div class="col-12 col-md-4 py-0">
                                <h3>MAPP發報記錄管理</h3>
                            </div>
                            <div class="col-6 col-md-4 py-0">
                                <form action="" method="post">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-search"></i>&nbsp篩選</span>
                                        <select name="_year" id="_year" class="form-select">
                                            <option for="_year" value="All" <?php if($_year == "All"){ ?>selected<?php } ?> >-- 年度 / All --</option>
                                            <?php foreach($row_lists_yy as $row_list_yy){ ?>
                                                <option for="_year" value="<?php echo $row_list_yy["_year"];?>" <?php echo ($row_list_yy["_year"] == $_year) ? "selected":"";?>>
                                                    <?php echo $row_list_yy["_year"]."y";?></option>
                                            <?php } ?>
                                        </select>
                                        <select name="_month" id="_month" class="form-select">
                                            <option for="_month" value="All" <?php echo ($_month == "All") ? "selected":"";?> >-- 全年度 / All --</option>
                                            <?php foreach (range(1, 12) as $item) {
                                                    // $item_str = str_pad($item, 2, '0', STR_PAD_LEFT);
                                                    echo "<option for='_month' value='{$item}'";
                                                    echo ($item == $_month ) ? "selected":"";
                                                    echo " >{$item}m</option>";
                                                } ?>
                                        </select>
                                        <button type="submit" class="btn btn-outline-secondary">查詢</button>
                                    </div>
                                </form>  
                            </div>
                            <div class="col-6 col-md-4 py-0 text-end">
                                <?php if($sys_role <= 1){ ?>
                                    <a href="../insign_msg/" title="MAPP發報" class="btn btn-success" >待簽清單統計與發報&nbsp<i class="fa-solid fa-comment-sms"></i></a>
                                <?php } ?>
                                <a href="#access_info" target="_blank" title="連線說明" class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#access_info">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i> API連線說明</a>
                            </div>
                        </div>
                        <hr>
                        <div class="col-12 rounded bg-light">
                            <div class="col-12 p-0">
                                <!-- 20211215分頁工具 -->               
                                <div class="row">
                                    <div class="col-12 col-md-6 pb-0">	
                                        <?php
                                            //每頁顯示筆數明細
                                            echo '顯示 '.$page_start.' 到 '.$page_end.' 筆 共 '.$per_total.' 筆，目前在第 '.$page.' 頁 共 '.$pages.' 頁'; 
                                        ?>
                                    </div>
                                    <div class="col-12 col-md-6 pb-0 text-end">
                                        <?php
                                            if($pages>1){  //總頁數>1才顯示分頁選單
        
                                                //分頁頁碼；在第一頁時,該頁就不超連結,可連結就送出$_GET['page']
                                                if($page=='1'){
                                                    echo "首頁 ";
                                                    echo "上一頁 ";		
                                                }else if(isset($list_ym)){
                                                    echo "<a href=?list_ym=".$list_ym."&page=1>首頁 </a> ";
                                                    echo "<a href=?list_ym=".$list_ym."&page=".($page-1).">上一頁 </a> ";	
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
                                                        }else if(isset($list_ym)){
                                                            echo '<a href=?list_ym='.$list_ym.'&page='.$i.'>'.$i.'</a> ';
                                                        }else{
                                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                        }
                                                    }
                                                }
                                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                                if($page==$pages){
                                                    echo " 下一頁";
                                                    echo " 末頁";
                                                }else if(isset($list_ym)){
                                                    echo "<a href=?list_ym=".$list_ym."&page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?list_ym=".$list_ym."&page=".$pages."> 末頁</a>";		
                                                }else{
                                                    echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?page=".$pages."> 末頁</a>";		
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                                <!-- 20211215分頁工具 -->
                                <table id="log_table" class="display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th data-toggle="tooltip" data-placement="bottom" title="記錄敘述">thisInfo</th>
                                            <th data-toggle="tooltip" data-placement="bottom" title="記錄事項" style="width: 80%;">logs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($row_lists_div as $log){ 
                                              $logs_log = json_decode($log['logs']);                        // 1.把json字串反解成物件或陣列
                                              if(is_object($logs_log)) { $logs_log = (array)$logs_log; }    // 2.判斷 物件轉陣列
                                              $logs_json = $logs_log['autoLogs'];                           // 3.只取autoLogs的部分
                                        ?>
                                            <tr id="<?php echo $log['id']; ?>">
                                                <!-- 第1格.thisInfo 紀錄敘述 -->
                                                <td>
                                                    <?php echo "(aid:".$log['id'].")&nbsp".$log['thisDay']."</br>".$log['sys']." => ".count($logs_json)."次"."</br>".$log['t_stamp']; ?>
                                                    <?php if($sys_role == 0){ ?>
                                                        <form action="" method="post">
                                                            <input type="hidden" name="list_ym" value="<?php echo $list_ym; ?>">
                                                            <input type="hidden" name="page" value="<?php echo $page == '1' ? '1':$page; ?>">
                                                            <input type="hidden" name="id" value="<?php echo $log['id']; ?>">
                                                            <input type="submit" name="deleteLog" value="刪除" class="btn btn-sm btn-xs btn-secondary" onclick="return confirm('確認刪除？')">
                                                        </form>
                                                    <?php }?>
                                                </td>
                                                <!-- 第2格.Logs 紀錄內容 -->
                                                <td>
                                                    <table>
                                                        <?php $i = 0;
                                                            foreach($logs_json AS $l){
                                                                if(is_object($l)) { $l = (array)$l; } 
                                                                echo "<tr><td class='".$l["mapp_res"]."'>".($i+1)."_"."&nbsp".$l["thisTime"]." => ".$l["mapp_res"]."</br>";
                                                                echo !empty($l["cname"]) ? $l["cname"]." (".$l["emp_id"].") ".$l["waiting"] : "" ;
                                                                if($sys_role == 0){ ?>
                                                                    <form action="" method="post">
                                                                        <input type="hidden" name="list_ym"     value="<?php echo $list_ym; ?>">
                                                                        <input type="hidden" name="page"        value="<?php echo $page == '1' ? '1':$page; ?>">
                                                                        <input type="hidden" name="log_id"      value="<?php echo $i;?>">
                                                                        <input type="hidden" name="id"          value="<?php echo $log['id'];?>">
                                                                        <input type="submit" name="delLog_item" value="刪除" class="btn btn-sm btn-xs btn-secondary" onclick="return confirm('確認刪除？')">
                                                                    </form>
                                                                <?php } 
                                                                echo "</td>" ;
                                                                echo "<td class='word_bk mg_msg' >".$l["mg_msg"]."</td></tr>";
                                                                $i++;
                                                            } 
                                                        ?>
                                                    </table>
                                                </td>
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
                                                }else if(isset($list_ym)){
                                                    echo "<a href=?list_ym=".$list_ym."&page=1>首頁 </a> ";
                                                    echo "<a href=?list_ym=".$list_ym."&page=".($page-1).">上一頁 </a> ";	
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
                                                        }else if(isset($list_ym)){
                                                            echo '<a href=?list_ym='.$list_ym.'&page='.$i.'>'.$i.'</a> ';
                                                        }else{
                                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                        }
                                                    }
                                                }
                                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                                if($page==$pages){
                                                    echo " 下一頁";
                                                    echo " 末頁";
                                                }else if(isset($list_ym)){
                                                    echo "<a href=?list_ym=".$list_ym."&page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?list_ym=".$list_ym."&page=".$pages."> 末頁</a>";		
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
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- 模組-API連線說明 -->
    <div class="modal fade" id="access_info" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">API連線說明</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-12 py-0 px-4">
                        <div>
                            method：POST / JSON
                        </div>
                        <table>
                            <thead> 
                                <tr>
                                    <th>SET</th>
                                    <th>KEY</th>
                                    <th>VALUE</th>
                                    <th>REMARK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>function</td>
                                    <td>storeLog</br>editLog</br>deleteLog</br>updateLog</td>
                                    <td>1.storeLog 儲存log</br>2.editLog 讀取log</br>3.deleteLog 刪除log</br>4.updateLog 更新log</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>thisDay</td>
                                    <td>紀錄日期</td>
                                    <td>這筆紀錄的所屬日期</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>sys</td>
                                    <td>系統別</td>
                                    <td>什麼系統驅動此事</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>logs</td>
                                    <td>紀錄事項</td>
                                    <td>系統發送了什麼事要記錄</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>T_STAMP</td>
                                    <td>now()_value</td>
                                    <td>進資料庫時間(系統追查用,可不填此欄位)</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>

</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>

<script>

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

</script>

<?php include("../template/footer.php"); ?>