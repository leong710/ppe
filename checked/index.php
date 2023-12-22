<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 先給預設值
    $sort_fab_id = $fab_id = $_SESSION[$sys_id]["fab_id"];
    $sort_emp_id = $emp_id = $_SESSION["AUTH"]["emp_id"];
    // 今年年份
    $today_year = date('Y');
    // 半年分界線
        if(date('m') <= 6 ){
            $half = "H1";
        }else{
            $half = "H2";
        }

    $list_setting = array(    // 組合查詢陣列
        'fab_id' => $sort_fab_id,
        'emp_id' => $sort_emp_id,
        'checked_year' => $today_year,      // 建立查詢陣列for顯示今年點檢表
        'half' => $half                     // 建立查詢陣列for顯示今年點檢表
    );

    $stocks = show_stock($list_setting);            // 調閱器材庫存，依儲存點顯示
    $myfab = show_fab($list_setting);               // 調閱我的fab
    $checked_lists = show_checked($list_setting);   // 調閱點檢表
    
    $check_yh_list = check_yh_list($list_setting);      // 查詢自己的點檢紀錄
    $check_yh_list_num = count($check_yh_list);         // 計算自己的點檢紀錄筆數

    $stocksLog_arr = [];    // 定義app陣列，總total
    $stocksLog_table = [];  // 定義app陣列，總total
    $check_item ="";

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

        $div_checkeds = checked_page_div($start, $per, $list_setting);
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
    <!-- goTop滾動畫面aos.css 1/4-->
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
    </style>
</head>
<body>
    <div class="col-12">
        <!-- 表頭 -->
        <div class="row justify-content-center">
            <div class="col_xl_11 col-11 rounded p-3" style="background-color: rgba(255, 255, 255, .8);">
                <!-- 表頭 -->
                <div class="row px-2">
                    <div class="col-md-6">
                        <div>
                            <h3><b>點檢紀錄總表</b></h3>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div>
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                <button type="button" id="checkItem" class="btn btn-sm btn-xs btn-success" onclick="open_check_list()" >open check list</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder" class="col-11 mb-0 p-0"></div>
                
                <!-- unblock設在這裡 -->
                <div class="row unblock" id="checkList" style="background-color: #D4D4D4;">                    
                    <div class="col-12 py-0" >
                        <form action="store.php" method="post">
                            <!-- 文件表頭 -->
                            <div class="row px-2">
                                <div class="col-md-6">
                                    <h3>所屬區域器材儲存量總表for年檢用</h3>
                                </div>
                                <div class="col-md-6 text-end">
                                    <?php if(isset($_SESSION[$sys_id])){
                                        if(count($stocks) > 0){ ?>
                                            <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                                <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                                                <?php }else{
                                                    echo "<snap style='color: red;'>** 只限fabUser才能完成年檢動作!</snap>";
                                             } ?>
                                        <?php }else{
                                            echo "<snap style='color: red;'>** 請先建立品項，再來完成年檢動作!</snap>";
                                        }?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row px-2 py-0">
                                <div class="col-md-6">
                                    <div>
                                        點檢單號：(尚未給號)</br>
                                        點檢日期：<?php echo date('Y-m-d H:i'); ?> (實際以送出當下時間為主)</br>
                                        點檢人員：<?php echo $_SESSION["AUTH"]["cname"];?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        fab廠區：<?php echo $myfab["fab_title"]; echo $myfab["fab_remark"] ? " (".$myfab["fab_remark"].")":"";?></br>
                                        點檢年度：<?php echo $today_year;?></br>
                                        上下年度：<?php echo $half;?>
                                    </div>
                                </div>
                            </div>
                            <!-- 儲存現況table=產生點檢表 -->
                            <div class="col-xl-12 col-12 rounded bg-light">
                                <table class="for-table">
                                    <thead>
                                        <tr>
                                            <th>儲存點位置</th>
                                            <th>分類</th>
                                            <th>名稱</th>
                                            <th>size</th>
                                            <th>安全存量</th>
                                            <th>現場存量</th>
                                            <th>備註說明</th>
                                            <th>批號/期限</th>
                                            <th>PO no</th>
                                            <th>更新日期</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 鋪設內容 -->
                                        <?php foreach($stocks as $stock){ ?>
                                            <tr <?php if($check_item != $stock['fab_title']){?>style="border-top:3px #FFD382 solid;"<?php } ?>>
                                                <td style="text-align: left;"><?php echo $stock['fab_title']."_".$stock['local_title'];?></td>
                                                <td><?php echo $stock['cate_no'].".".$stock['cate_title'];?></td>
                                                <td style="text-align: left;" title="id：<?php echo $stock['id'];?>"><?php echo $stock['cata_SN']."</br>".$stock['pname'];?></td>
                                                <td><?php echo $stock['size'];?></td>
                                                <td><?php echo $stock['standard_lv'];?></td>
                                                <td><?php echo $stock['amount'];?></td>
                                                <td style="text-align: left; word-break: break-all;"><?php echo $stock['stock_remark'];?></td>
                                                <td style="font-size: 12px;"><?php echo $stock['lot_num'];?></td>
                                                <td style="font-size: 12px;"><?php echo $stock['po_no'];?></td>
                                                <td style="font-size: 12px;"><?php echo substr($stock['updated_at'],0,10)."</br>by：".$stock['updated_user'];?></td>
                                            </tr>
                                            <?php 
                                                $check_item = $stock['fab_title'];
                                                // 因為remark=textarea會包含html符號，必須用strip_tags移除html標籤
                                                $s_remark = strip_tags($stock["stock_remark"]);
                                                // 因為remark=textarea會包含換行符號，必須用str_replace置換/n標籤
                                                $s_remark = str_replace("\r\n", " ", $s_remark);
    
                                                // <!-- 這個工作是將stocks逐筆轉成array，以便進行鋪陳和存檔 -->
                                                $stock_arr = [];                              // 定義app陣列，單筆
                                                $stock_arr = array(
                                                    'local_id'      => $stock["local_id"], 
                                                    'local_title'   => $stock["local_title"], 
                                                    'fab_id'        => $stock["fab_id"], 
                                                    'fab_title'     => $stock["fab_title"], 
                                                    'cate_no'       => $stock["cate_no"],
                                                    'cate_title'    => $stock["cate_title"], 
                                                    'cata_SN'       => $stock["cata_SN"], 
                                                    'pname'         => $stock["pname"], 
                                                    'stock_id'      => $stock["id"], 
                                                    'size'          => $stock["size"], 
                                                    'standard_lv'   => $stock["standard_lv"], 
                                                    'amount'        => $stock["amount"],
                                                    'stock_remark'  => $s_remark,
                                                    'lot_num'       => $stock["lot_num"],
                                                    'po_no'         => $stock["po_no"],
                                                    'updated_at'    => $stock["updated_at"],
                                                    'updated_user'  => $stock["updated_user"]
                                                );
                                                $stock_enc = JSON_encode($stock_arr);           // 小陣列要先編碼才能塞進去大陣列forStore儲存
                                                // $stock_dec = JSON_decode($stock_enc);           // 小陣列要先編碼才能塞進去大陣列forStore儲存
                                                // $stock_str = implode("-," , (array) $stock_dec); // 陣列轉成字串進行儲存到mySQL
                                                // array_unshift($stocksLog_arr, $stock_arr);   // 插到前面
                                                array_push($stocksLog_arr, $stock_enc);         // 插到後面
                                        } ?>
                                    </tbody>
                                </table>
                                <hr>
                            </div>
                            <!-- 這個工作是將stocks逐筆轉成array，以便進行鋪陳和存檔 -->
                                <?php 
                                    // 1、explode()把字串打散為陣列：
                                    // 2、implode()把陣列元素組合為字串：
                                    $logs_str = implode("_," , $stocksLog_arr);               // 陣列轉成字串進行儲存到mySQL
                                ?>
                                <textarea type="hidden" name="stocks_log" readonly style="display:none"><?php echo $logs_str;?></textarea>
                                <input    type="hidden" name="fab_id"         value="<?php echo $stock["fab_id"];?>">
                                <input    type="hidden" name="emp_id"         value="<?php echo $_SESSION["AUTH"]["emp_id"];?>">
                                <input    type="hidden" name="cname"          value="<?php echo $_SESSION["AUTH"]["cname"]; ?>">
                                <input    type="hidden" name="checked_year"   value="<?php echo $today_year;?>">
                                <input    type="hidden" name="half"           value="<?php echo $half;?>">
    
                            <!-- 彈出畫面說明模組 saveSubmit-->
                            <div class="modal fade" id="saveSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">衛材儲存量總表年檢：</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body px-5">
                                            <label for="checked_remark" class="form-check-label" >點檢備註說明：</label>
                                            <textarea name="checked_remark" id="checked_remark" class="form-control" rows="5"></textarea>
                                            <input type="checkbox" name="iAgree" value="0" id="iAgree" class="form-check-input" required>
                                            <label for="iAgree" class="form-label">我已確認完畢，數量無誤!</label>
                                        </div>
                                        <div class="modal-footer">
                                            <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                                <input type="submit" value="Submit" name="submit" class="btn btn-primary">
                                            <?php } ?>
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- 歷史清單 -->
            <div class="col_xl_11 col-11 rounded p-3" style="background-color: #D4D4D4;">
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
                                        <th>ai</th>
                                        <th>fab</th>
                                        <th>點檢年度</th>
                                        <th>點檢日期/點檢人</th>
                                        <th>備註說明</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- 鋪設內容 -->
                                    <?php foreach($div_checkeds as $checked_list){ ?>
                                        <tr>
                                            <td style="font-size: 6px;"><?php echo $checked_list['id'];?></td>
                                            <td><?php echo $checked_list['fab_title'];?></td>
                                            <td><?php if(isset($checked_list['checked_year'])){
                                                            echo $checked_list['checked_year']."_".$checked_list['half'];}?></td>
                                            <td style="text-align: left;"><?php if(isset($checked_list['created_at'])){ 
                                                            echo $checked_list['created_at']." / ".$checked_list['updated_user'];}?></td>
                                            <td style="width:30%; text-align: left; word-break: break-all;"><?php echo $checked_list['checked_remark'];?></td>
                                            <td><a href="read.php?id=<?php echo $checked_list['id'];?>&from2=index" class="btn btn-sm btn-xs btn-info">檢視</a></td>
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
<!-- 彈出畫面模組x 匯出CSV-->
    <div class="modal fade" id="csvx" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">匯出CSV紀錄</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- 20220606測試CSV匯入/匯出 -->
                <form id="addform" action="../csv/do.php?action=import" method="post" enctype="multipart/form-data"> 
                    <div class="modal-body p-4">
                        請選擇要匯入的CSV檔案：
                        <input type="file" name="file">
                        <br><br>
                        ** 請注意CSV欄位需一致，第一列表頭將不被匯入!
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="submit" class="btn btn-primary" value="匯入CSV"> 
                            <input type="button" class="btn btn-info" value="匯出CSV" onclick="window.location.href='../csv/do.php?action=export'">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
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
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<script>
    var checkList = document.getElementById("checkList");   // 檢點表單
    var alertPlaceholder = document.getElementById("liveAlertPlaceholder")      // Bootstrap Alarm

    // 神奇PHP變數帶入js方法
    <?php echo "var check_yh_list_num ='$check_yh_list_num';";?>

    // Bootstrap Alarm function
    function alert(message, type) {
        var wrapper = document.createElement('div')
        wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message + '<a href="#"  id="checkItem" class="alert-link" onclick="open_check_list()" >[打開點檢表]</a>' + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'

        alertPlaceholder.append(wrapper)
    }
    // 假如index找不到當下存在已完成的表單，就alarm它!
    if (check_yh_list_num == '0') {
        alert('*** <?php echo $today_year;?>_<?php echo $half;?> 年度 衛材儲存量確認開始了! 請務必在指定時間前完成確認~ ', 'danger')
    }

    // 在清單上建立按鈕 open check list
    function open_check_list(){
        if($("#checkList").css("display")=="none"){
            $("#checkList").css("display","block")
            return;
        }
        if($("#checkList").css("display")=="block"){
            $("#checkList").css("display","none")
            return;
        }
    }

</script>

<?php include("../template/footer.php"); ?>