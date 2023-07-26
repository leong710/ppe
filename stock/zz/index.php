<?php
    require_once("../pdo.php");
    require_once("function.php");
    accessDenied();

    if(isset($_GET["site_id"])){    // 有帶查詢，套查詢參數
        $sort_site_id = $_GET["site_id"];
    }else{                          // 先給預設值
        $sort_site_id = $_SESSION["AUTH"]["site_id"];
    }
    if(isset($_GET["category_id"])){
        $sort_category_id = $_GET["category_id"];
    }else{
        $sort_category_id = "All";
    }
    $list_issue_setting = array(    // 組合查詢陣列
        'site_id' => $sort_site_id,
        'category_id' => $sort_category_id
    );
    $stocks = show_stock($list_issue_setting);                  // 依儲存點顯示
    $categories = showAllCategories();                          // 分類
    $sum_categorys = show_sum_category($list_issue_setting);    // 統計分類與數量

    // 初始化半年後日期，讓系統判斷與highLight
        $toDay = date('Y-m-d');
        $half_month = date('Y-m-d', strtotime($toDay."+6 month -1 day"));

    // 把sites讀進來作為查詢的select option
        $sort_site_setting = array(    // 組合查詢陣列
            'site_id' => "All"
        );
        $sites = show_site($sort_site_setting);         // 查詢清單用
        $sortSite = show_site($list_issue_setting);     // 查詢結果
    if(empty($sortSite)){                               // 查無資料時返回指定頁面
        echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃
    }
    // <!-- 20211215分頁工具 -->
        //分頁設定
        // $per_total = $rs -> rowCount();  //計算總筆數
        $per_total = count($stocks);  //計算總筆數
        $per = 20;  //每頁筆數
        $pages = ceil($per_total/$per);  //計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        if(!isset($_GET['page'])){  //!isset 判斷有沒有$_GET['page']這個變數
            $page = 1;	  
        }else{
            $page = $_GET['page'];
        }
        $start = ($page-1)*$per;  //每一頁開始的資料序號(資料庫序號是從0開始)

        $div_stocks = stock_page_div($start, $per, $list_issue_setting);
        // $rs = page_div($start,$per);
        $page_start = $start +1;  //選取頁的起始筆數
        $page_end = $start + $per;  //選取頁的最後筆數
        if($page_end>$per_total){  //最後頁的最後筆數=總筆數
            $page_end = $per_total;
        }
    // <!-- 20211215分頁工具 -->

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        .TOP {
            background-image: URL('../images/first-aid-kit-items.jpg');
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            /* overflow: hidden; 會影響表頭黏貼功能*/
            background-attachment: fixed;
            background-position: center top;
            /* background-position: left top; */
            background-repeat: no-repeat;
            background-size: cover;
            /* background-size: contain; */
            padding-top: 100px;
        }
        .unblock{
            display: none;
        }
        .body > ul {
            padding-left: 0px;
        }
    </style>
</head>
<body>
    <div class="TOP">
        <div class="col-12">
            <div class="row justify-content-center">
                <div class="col_xl_11 col-11 rounded p-3 py-2" style="background-color: rgba(255, 255, 255, .7);">
                    <div class="col-12 py-0">
                        <!-- 表頭按鈕 -->
                        <div class="row">
                            <div class="col-12 col-md-8">
                                <h3><?php echo $sortSite["id"].".".$sortSite["site_title"]." (".$sortSite["remark"].")";?>_存量管理： </h3>
                            </div>
                            <div class="col-12 col-md-4 text-end">
                                <?php if(isset($_SESSION["AUTH"])){ ?>
                                    <?php if($_SESSION["AUTH"]["role"] <= 1 || ( $_SESSION["AUTH"]["role"] <= 2 && 
                                            ($_SESSION["AUTH"]["site_id"] == $sortSite["id"] || in_array($sortSite["id"], $_SESSION["AUTH"]["ssite_id"])) )){ ?>
                                        <a href="create.php" title="新增" class="btn btn-primary"> <i class="fa fa-plus"></i> 新增</a>
                                    <?php } ?>
                                    <a href="#" target="_blank" title="匯出CSV" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#doCSV"> <i class="fa fa-download" aria-hidden="true"></i> 下載CSV</a>
                                    <a href="#" target="_blank" title="查詢" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#query_stock"> <i class="fa fa-search" aria-hidden="true"></i> 查詢</a>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- 分類 -->
                        <div class="col-12 text-center py-0">
                            <a href="?site_id=<?php echo $sortSite["id"];?>&category_id=All">
                            <span class="badge rounded-pill bg-dark">All&nbsp<span class="badge bg-secondary"><?php echo $per_total;?></span></span></a>&nbsp&nbsp
                            <?php foreach($sum_categorys as $sum_cate){ ?>
                                <div class="py-1" style="display: inline-block;">
                                    <a href="?site_id=<?php echo $sortSite["id"];?>&category_id=<?php echo $sum_cate["cate_id"];?>">
                                    <span class="badge rounded-pill 
                                        <?php switch($sum_cate["cate_id"]){
                                                case "1": echo "bg-primary"; break;
                                                case "2": echo "bg-success"; break;
                                                case "3": echo "bg-warning text-dark"; break;
                                                case "4": echo "bg-danger"; break;
                                                case "5": echo "bg-info text-dark"; break;
                                                case "6": echo "bg-dark"; break;
                                                case "7": echo "bg-secondary"; break;
                                                default: echo "bg-light text-success"; break;
                                            }?>"><?php echo $sum_cate["cate_id"].".".$sum_cate["cate_title"];?>
                                        <span class="badge bg-secondary"><?php echo $sum_cate["stock_count"];?></span>
                                    </span></a>&nbsp&nbsp</div>
                            <?php  } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_11 col-11 rounded p-4" style="background-color: rgba(255, 255, 255, .7);">
                <div class=" pt-0">
                    <!-- by各Local儲存點： -->
                    <div class="justify-content-center">
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
                                            }else if(isset($sortSite["id"]) && isset($sort_category_id)){
                                                echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=1>首頁 </a> ";
                                                echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=".($page-1).">上一頁 </a> ";
                                            }else if(isset($sortSite["id"])){
                                                echo "<a href=?site_id=".$sortSite["id"]."&page=1>首頁 </a> ";
                                                echo "<a href=?site_id=".$sortSite["id"]."&page=".($page-1).">上一頁 </a> ";		
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
                                                    }else if(isset($sortSite["id"]) && isset($sort_category_id)){
                                                        echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=".$i.'>'.$i.'</a> ' ;
                                                    }else{
                                                        echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                    }
                                                }
                                            }
                                            //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                            if($page==$pages){
                                                echo " 下一頁";
                                                echo " 末頁";
                                            }else if(isset($sortSite["id"]) && isset($sort_category_id)){
                                                echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=".($page+1)."> 下一頁</a>";
                                                echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=".$pages."> 末頁</a>";
                                            }else if(isset($sortSite["id"])){
                                                echo "<a href=?site_id=".$sortSite["id"]."&page=".($page+1)."> 下一頁</a>";
                                                echo "<a href=?site_id=".$sortSite["id"]."&page=".$pages."> 末頁</a>";
                                            }else{
                                                echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                                echo "<a href=?page=".$pages."> 末頁</a>";		
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                        <!-- 20211215分頁工具 -->
                        <table>
                            <thead>
                                <tr class="">
                                    <th>ai</th>
                                    <th><i class="fa fa-check" aria-hidden="true"></i> 儲存點位置</th>
                                    <th>衛材</br>分類</th>
                                    <th>衛材名稱</th>
                                    <th data-toggle="tooltip" data-placement="bottom" title="相同儲區&相同品項將安全存量合併成一筆計算">安全</br>存量 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                    <th>現場</br>存量</th>
                                    <th>備註說明</th>
                                    <th data-toggle="tooltip" data-placement="bottom" title="效期小於6個月將highlight">批號/期限 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                    <th>PO no</th>
                                    <th>最後更新 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                </tr>
                            </thead>
                            <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                            <tbody>
                                <?php 
                                    $check_item ="";
                                ?>
                                <?php foreach($div_stocks as $stock){ ?>
                                    <tr <?php if($check_item != $stock['local_title']){?>style="border-top:3px #FFD382 solid;"<?php } ?>>
                                        <td style="font-size: 12px;"><?php echo $stock['id'];?></td>
                                        <td style="text-align: left;"><?php echo $stock['fab_title'];?>&nbsp<?php echo $stock['site_title'];?>_<?php echo $stock['local_title'];?></td>
                                        <td><span class="badge rounded-pill <?php switch($stock["cate_id"]){
                                                case "1": echo "bg-primary"; break;
                                                case "2": echo "bg-success"; break;
                                                case "3": echo "bg-warning text-dark"; break;
                                                case "4": echo "bg-danger"; break;
                                                case "5": echo "bg-info text-dark"; break;
                                                case "6": echo "bg-dark"; break;
                                                case "7": echo "bg-secondary"; break;
                                                default: echo "bg-light text-success"; break;
                                            }?>"><?php echo $stock["cate_id"].".".$stock["cate_title"];?></span></td>
                                        <td style="text-align: left;"><?php echo $stock["catalog_id"].".".$stock['catalog_title'];?></td>
                                        <td><?php echo $stock['standard_lv'];?></td>
                                        <td <?php if($stock["amount"] < $stock['standard_lv']){ ?> style="background-color:#FFBFFF;color:red;font-size:1.2em;"<?php }?>>
                                            <?php echo $stock['amount'];?></td>
                                        <td style="width:20%;text-align: left;"><?php echo $stock['remark'];?></td>
                                        <td <?php if($stock["lot_num"] < $half_month){ ?> style="background-color:#FFBFFF;color:red;" data-toggle="tooltip" data-placement="bottom" title="有效期限小於：<?php echo $half_month;?>" <?php } ?>>
                                            <?php echo $stock['lot_num'];?></td>
                                        <td style="font-size: 12px;"><?php echo $stock['po_num'];?></td>
                                        <td style="width:8%;font-size: 12px;" title="最後編輯: <?php echo $stock['cname'];?>">
                                            <?php if(isset($stock['id'])){ ?>
                                                <?php if($_SESSION["AUTH"]["role"] <= 1 || ( $_SESSION["AUTH"]["role"] <= 2 && 
                                                    ($_SESSION["AUTH"]["site_id"] == $sortSite["id"] || in_array($sortSite["id"], $_SESSION["AUTH"]["ssite_id"])) )){ ?>
                                                        <a href="edit.php?id=<?php echo $stock['id'];?>&from2=index"><?php echo $stock['updated_at'];?></a>
                                                    <?php }else{ echo $stock['updated_at']; } ?>
                                            <?php } ?></td>
                                    </tr>
                                    <?php $check_item = $stock['local_title'];?>
                                <?php } ?>
                            </tbody>
                        </table>
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
                                            }else if(isset($sortSite["id"]) && isset($sort_category_id)){
                                                echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=1>首頁 </a> ";
                                                echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=".($page-1).">上一頁 </a> ";
                                            }else if(isset($sortSite["id"])){
                                                echo "<a href=?site_id=".$sortSite["id"]."&page=1>首頁 </a> ";
                                                echo "<a href=?site_id=".$sortSite["id"]."&page=".($page-1).">上一頁 </a> ";	
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
                                                    }else if(isset($sortSite["id"]) && isset($sort_category_id)){
                                                        echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=".$i.'>'.$i.'</a> ' ;
                                                    }else{
                                                        echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                    }
                                                }
                                            }
                                            //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                            if($page==$pages){
                                                echo " 下一頁";
                                                echo " 末頁";
                                            }else if(isset($sortSite["id"]) && isset($sort_category_id)){
                                                echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=".($page+1)."> 下一頁</a>";
                                                echo "<a href=?site_id=".$sortSite["id"]."&category_id=".$sort_category_id."&page=".$pages."> 末頁</a>";
                                            }else if(isset($sortSite["id"])){
                                                echo "<a href=?site_id=".$sortSite["id"]."&page=".($page+1)."> 下一頁</a>";
                                                echo "<a href=?site_id=".$sortSite["id"]."&page=".$pages."> 末頁</a>";	
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
<!-- 彈出畫面模組1 查詢其他stock-->
    <div class="modal fade" id="query_stock" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">query_stock</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="get" onsubmit="this.site_id.disabled=false">
                    <div class="modal-body p-4">
                        <div class="row mb-0">
                            <div class="col-12">
                                <label for="" class="form-label">site_id：<sup class="text-danger"> *</sup></label>
                                <select name="site_id" id="" class="form-control" required onchange="this.form.submit()">
                                    <option value="" selected hidden>--請選擇site類別--</option>
                                    <?php foreach($sites as $site){ ?>
                                        <?php if($site["flag"] == "On" || $_SESSION["AUTH"]["role"] == 0 ){ ?>  
                                            <option value="<?php echo $site["id"];?>" <?php echo $site["id"] == $sortSite["id"] ? "selected":""; ?>>
                                                <?php echo $site["id"].":".$site["site_title"]." (".$site["remark"]; echo ($site["flag"] == "Off") ? "-已關閉)":")";?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">查詢</button>
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
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
                <!-- 20220606測試匯出csv -->
                <!-- <form id="addform" action="docsv.php?action=export" method="post">  -->
                <form id="addform" action="docsv.php?action=export_xls" method="post"> 
                    <div class="modal-body p-4" >
                        <div class="col-12">
                            <label for="" class="form-label">請選擇您要查詢下載的site：<sup class="text-danger"> *</sup></label>
                            <select name="site_id" id="site_id" class="form-control" required >
                                <option value="All" selected>--全部site--</option>
                                <?php foreach($sites as $site){ ?>
                                    <option value="<?php echo $site["id"];?>"><?php echo $site["id"];?>: <?php echo $site["site_title"];?> (<?php echo $site["remark"];?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="" class="form-label">存量狀態：<sup class="text-danger"> *</sup></label>
                            <input type="radio" name="state" value="0" id="0" class="form-check-input" checked>
                            <label for="0" class="form-check-label">全部</label>&nbsp&nbsp
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="submit" class="btn btn-success" value="匯出CSV" onclick="doCSV.hide()"> 
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../css/script.js"></script>
<!-- 封存技能：可編輯table之script -->
<!-- http://www.santii.com/article/214.html -->
<script>
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>
<?php include("../template/footer.php"); ?>