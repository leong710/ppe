<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 身分選擇功能：定義user進來要看到的項目
        $is_emp_id = "All";    // 預設值=All

        if(isset($_REQUEST["emp_id"])){
            // 有帶查詢，套查詢參數
            $is_emp_id = $_REQUEST["emp_id"];

        }else if($_SESSION[$sys_id]["role"] >=2){
            // 沒帶查詢，含2以上=套自身主site_id
            $is_emp_id = $_SESSION["AUTH"]["emp_id"];
        }

    // 組合查詢陣列
        $list_issue_setting = array(
            'sys_id' => $sys_id,
            'emp_id' => $is_emp_id
        );
        $issues = show_issue_list($list_issue_setting);
        $sum_issues = show_sum_issue($list_issue_setting);              // 統計看板--上：表單核簽狀態
        $sum_issues_ship = show_sum_issue_ship($list_issue_setting);    // 統計看板--下：轉PR單

    // <!-- 20211215分頁工具 -->
        // 分頁設定
        $per_total = count($issues);        // 計算總筆數
        $per = 25;                          // 每頁筆數
        $pages = ceil($per_total/$per);     // 計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        if(!isset($_GET['page'])){          // !isset 判斷有沒有$_GET['page']這個變數
            $page = 1;	  
        }else{
            $page = $_GET['page'];
        }
        $start = ($page-1)*$per;            // 每一頁開始的資料序號(資料庫序號是從0開始)
        $issues = issue_page_div($start, $per, $list_issue_setting);
        $page_start = $start +1;            // 選取頁的起始筆數
        $page_end = $start + $per;          // 選取頁的最後筆數
        if($page_end>$per_total){           // 最後頁的最後筆數=總筆數
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

    </style>
</head>
<body>
    <!-- <div class="container"> -->
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 rounded mx-1 p-3 py-0" style="background-color: rgba(200, 255, 255, .6);">
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-12 col-md-4 pb-1">
                        <div style="display:inline-block;">
                            <h3><i class="fa-solid fa-1"></i> 請購需求單總表</h3>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 pb-1">
                        <form action="" method="get">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i>&nbsp篩選</span>
                                <select name="emp_id" id="sort_emp_id" class="form-select" onchange="this.form.submit()">
                                    <option value="All" <?php echo $is_emp_id == "All" ? "selected":"";?>>-- [ All user ] --</option>
                                    <option value="<?php echo $_SESSION["AUTH"]["emp_id"];?>" <?php echo $is_emp_id == $_SESSION["AUTH"]["emp_id"] ? "selected":"";?>>
                                        <?php echo $_SESSION["AUTH"]["emp_id"]."_".$_SESSION["AUTH"]["cname"];?></option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-4 pb-1 text-end">
                        <?php if(isset($_SESSION[$sys_id])){ ?>
                            <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                <a href="create.php" title="管理員限定" class="btn btn-primary"><i class="fa fa-edit" aria-hidden="true"></i> 填寫需求單</a>
                            <?php } ?>
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                <a href="show_issueAmount.php" title="管理員限定" class="btn btn-warning"><i class="fa-brands fa-stack-overflow"></i> 待轉PR總表</a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <!-- table -->
                <div class="row">
                    <!-- 左邊統計 -->
                    <div class="col-12 col-md-3 px-1">
                        <div class="row">
                            <div class="col-6 col-md-12 pt-0">
                                <div class="border rounded p-4" style="background-color: #D4D4D4;">
                                    <h4>表單狀態：</h4>
                                    <table class="table">
                                        <thead>
                                            <tr class="table-dark">
                                                <th>類別</th>
                                                <th>狀態</th>
                                                <th>count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($sum_issues as $sum_issue){ ?>
                                                <tr>
                                                    <td>
                                                        <?php if($sum_issue['ppty'] == '0'){?>臨時<?php ;}?>
                                                        <?php if($sum_issue['ppty'] == '1'){?>定期<?php ;}?>
                                                    </td>
                                                    <td>
                                                        <?php if($sum_issue['idty'] == '0'){?><span class="badge rounded-pill bg-warning text-dark">待轉</span><?php ;}?>
                                                        <?php if($sum_issue['idty'] == '1'){?><span class="badge rounded-pill bg-danger">待簽</span><?php ;}?>
                                                        <?php if($sum_issue['idty'] == '2'){?>退件<?php ;}?>
                                                        <?php if($sum_issue['idty'] == '3'){?>取消<?php ;}?>
                                                        <?php if($sum_issue['idty'] == '10'){?>結案<?php ;}?>
                                                        <?php if($sum_issue['idty'] == '11'){?>轉PR<?php ;}?>
                                                        <?php if($sum_issue['idty'] == '12'){?><span class="badge rounded-pill bg-success">待收</span><?php ;}?>
                                                    </td>
                                                    <td>
                                                        <?php echo $sum_issue['idty_count']."&nbsp件";?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <span style="font-size: 14px;">交易狀態：0完成/1待簽/2退件/3取消</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-12 pt-0">
                                <div class="border rounded p-4" style="background-color: #D4D4D4;">
                                    <h4>轉PR單：<sup class="text-danger"> * Limit 10</sup></h4>
                                    <table class="table">
                                        <thead>
                                            <tr class="table-dark">
                                                <th>in_date</th>
                                                <th>PR單</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($sum_issues_ship as $sum_issue_ship){ ?>
                                                <tr>
                                                    <td><?php echo substr($sum_issue_ship['in_date'],0,10);?></td>
                                                    <td style="word-break: break-all;"><a href="review_issueAmount.php?pr_no=<?php echo $sum_issue_ship['_ship']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $sum_issue_ship['ship_count']."&nbsp件";?>">
                                                        <?php echo $sum_issue_ship['_ship'];?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 右邊清單 -->
                    <div class="col-12 col-md-9 px-1">
                        <div class="border rounded bg-white">
                            <!-- <hr> -->
                            <div class="col-12">
                                <div class="col-12 rounded bg-success text-white">
                                    <div>
                                        <span><b>簡易表單流程：</b>1.(user)填寫需求單+送出 =>  2.待簽/(pm)簽核 =>  3.待轉/(pm)待轉PR總表+PR開單確認  4.轉PR => 表單結案~</span>
                                    </div>
                                    <div>
                                        <span class="text-end">** pm簽核時若退件，表單即結束流程。請user重新開單。</span>
                                    </div>
                                </div>
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
                                                }else if(isset($is_emp_id)){
                                                    echo "<a href=?emp_id=".$is_emp_id."&page=1>首頁 </a> ";
                                                    echo "<a href=?emp_id=".$is_emp_id."&page=".($page-1).">上一頁 </a> ";		
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
                                                        }else if(isset($is_emp_id)){
                                                            echo '<a href=?emp_id='.$is_emp_id.'&page='.$i.'>'.$i.'</a> ';
                                                        }else{
                                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                        }
                                                    }
                                                }
                                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                                if($page==$pages){
                                                    echo " 下一頁";
                                                    echo " 末頁";	
                                                }else if(isset($is_emp_id)){
                                                    echo "<a href=?emp_id=".$is_emp_id."&page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?emp_id=".$is_emp_id."&page=".$pages."> 末頁</a>";		
                                                }else{
                                                    echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?page=".$pages."> 末頁</a>";		
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                                <!-- 20211215分頁工具 -->
                                <table class="">
                                    <thead>
                                        <tr>
                                            <th>開單日期</th>
                                            <th>需求廠區</th>
                                            <th>需求者</th>
                                            <th>發貨廠區</th>
                                            <th>發貨人</th>
                                            <th>簽單日期</th>
                                            <th>類別</th>
                                            <th>狀態</th>
                                            <th>貨態</th>
                                            <th>action</th>
                                        </tr>
                                    </thead>
                                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                    <tbody>
                                        <?php foreach($issues as $issue){ ?>
                                            <tr>
                                                <td><?php echo substr($issue['create_date'],0,10); ?></td>
                                                <td><?php echo $issue['fab_i_title'].'('.$issue['fab_i_remark'].')';?></td>
                                                <td><?php echo $issue['cname_i'];?></td>
                                                <td style="font-size: 12px; word-break: break-all;">
                                                    <?php echo (!empty($issue["fab_o_title"])) ? $issue["fab_o_title"].'('.$issue['fab_o_remark'].')' : $issue["out_local"];?>
                                                </td>
                                                <td><?php echo $issue['cname_o'];?></td>
                                                <td style="font-size: 6px;"><?php echo substr($issue['in_date'],0,10); ?></td>
                                                <td>
                                                    <?php if($issue['ppty'] == '0'){?>臨時<?php ;}?>
                                                    <?php if($issue['ppty'] == '1'){?>定期<?php ;}?>
                                                </td>
                                                <td>
                                                    <?php if($issue['idty'] == '0'){?><span <?php if($_SESSION[$sys_id]['role'] <= 1 ){ ?>
                                                            class="badge rounded-pill bg-warning text-dark" <?php }?> >待轉</span><?php ;}?>
                                                    <?php if($issue['idty'] == '1'){?><span <?php if($_SESSION[$sys_id]['role'] <= 1 ){ ?>
                                                            class="badge rounded-pill bg-danger" <?php }?> >待簽</span><?php ;}?>
                                                    <?php if($issue['idty'] == '2'){?>退件<?php ;}?>
                                                    <?php if($issue['idty'] == '3'){?>取消<?php ;}?>
                                                    <?php if($issue['idty'] == '10'){?>結案<?php ;}?>
                                                    <?php if($issue['idty'] == '11'){?>轉PR<?php ;}?>
                                                    <?php if($issue['idty'] == '12'){?><span class="badge rounded-pill bg-success">待收</span><?php ;}?>
                                                </td>
                                                <td style="font-size: 12px; word-break: break-all;"><?php echo $issue['_ship'];?>
                                                    <?php if($issue['_ship'] == '0'){?>結案<?php ;}?>
                                                    <?php if($issue['_ship'] == '1'){?>轉PR<?php ;}?>
                                                </td>
                                                <td>
                                                    <!-- Action功能欄 -->
                                                    <?php if(($issue['idty'] == '1') && ($_SESSION[$sys_id]['role'] <= 1)){ ?> 
                                                    <!-- 待簽：PM+管理員功能 -->
                                                        <a href="show.php?id=<?php echo $issue['id'];?>&action=sign" class="btn btn-sm btn-xs btn-primary">簽核</a>
                                                    <?php } else { ?>
                                                    <!-- siteUser功能 -->
                                                        <a href="show.php?id=<?php echo $issue['id'];?>" class="btn btn-sm btn-xs btn-info">檢視</a>
                                                    <?php }?>
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
                                                }else if(isset($is_emp_id)){
                                                    echo "<a href=?emp_id=".$is_emp_id."&page=1>首頁 </a> ";
                                                    echo "<a href=?emp_id=".$is_emp_id."&page=".($page-1).">上一頁 </a> ";
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
                                                        }else if(isset($is_emp_id)){
                                                            echo '<a href=?emp_id='.$is_emp_id.'&page='.$i.'>'.$i.'</a> ';
                                                        }else{
                                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                        }
                                                    }
                                                }
                                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                                if($page==$pages){
                                                    echo " 下一頁";
                                                    echo " 末頁";
                                                }else if(isset($is_emp_id)){
                                                    echo "<a href=?emp_id=".$is_emp_id."&page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?emp_id=".$is_emp_id."&page=".$pages."> 末頁</a>";		
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
    </div>

    <div class="col-12 mt-0 pt-0">
        <div class="row justify-content-center">
        </div>
    </div>

<!-- 彈出畫面說明模組 review表單流程-->
    <div class="modal fade" id="review_role" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">preView 預覽：</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5" style="text-align:center;" >
                    <img src="role.png" style="width: 100%;" class="img-thumbnail">
                </div>
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
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>

<?php include("../template/footer.php"); ?>