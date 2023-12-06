<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    $auth_emp_id    = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_id_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用
    $sys_id_fab_id  = $_SESSION[$sys_id]["fab_id"];     
    $sys_id_sfab_id = $_SESSION[$sys_id]["sfab_id"];    

    // 身分選擇功能：定義user進來要看到的項目
        $trade_fab_id = "All";    // 預設值=All
        $trade_emp_id = "All";

        if(isset($_REQUEST["emp_id"])){
            // 有帶查詢，套查詢參數
            $trade_emp_id = $_REQUEST["emp_id"];
            if($trade_emp_id == "All"){
                $trade_fab_id = "All";
            }else{
                $trade_fab_id = $sys_id_fab_id;
            }

        } else if($sys_id_role >=2){
            // 沒帶查詢，含2以上=套自身主fab_id
            $trade_fab_id = $sys_id_fab_id;
            $trade_emp_id = $auth_emp_id;
        }

    // 組合查詢陣列
        $trade_list_query = array(
            'fab_id' => $trade_fab_id,
            'emp_id' => $trade_emp_id
        );
        
    $trades = show_trade_list($trade_list_query);
    $sum_trades = show_sum_trade($trade_list_query);              // 統計看板--上：表單核簽狀態

    // <!-- 20211215分頁工具 -->
        $per_total = count($trades);        //計算總筆數
        $per = 25;                          //每頁筆數
        $pages = ceil($per_total/$per);     //計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        if(!isset($_GET['page'])){          //!isset 判斷有沒有$_GET['page']這個變數
            $page = 1;	  
        }else{
            $page = $_GET['page'];
        }
        $start = ($page-1)*$per;            //每一頁開始的資料序號(資料庫序號是從0開始)
        // 合併嵌入分頁工具
            $trade_list_query["start"]  = $start;
            $trade_list_query["per"]    = $per;

        $trades = show_trade_list($trade_list_query);
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
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <style>
        .page_title{
            color: white;
            /* text-shadow:3px 3px 9px gray; */
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
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
        // All resources finished loading! // 關閉mLoading提示
        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
        });
        mloading();    // 畫面載入時開啟loading
    </script>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 rounded mx-1 p-3 pt-0" style="background-color: rgba(200, 255, 100, .6);">
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-12 col-md-4 pb-1 page_title">
                        <div style="display:inline-block;">
                            <h3><i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b></h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 pb-1">
                        <form action="" method="get">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i>&nbsp篩選</span>
                                <select name="emp_id" id="sort_emp_id" class="form-select" onchange="this.form.submit()">
                                    <option value="All" <?php echo $trade_emp_id == "All" ? "selected":"";?>>-- [ All user ] --</option>
                                    <option value="<?php echo $auth_emp_id;?>" <?php echo $trade_emp_id == $auth_emp_id ? "selected":"";?>>
                                        <?php echo $auth_emp_id."_".$_SESSION["AUTH"]["cname"];?></option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-6 col-md-4 pb-1 text-end">
                        <?php if(isset($_SESSION[$sys_id])){ ?>
                            <?php if($sys_id_role <= 2){ ?>
                                <a href="form.php" class="btn btn-primary"><i class="fa-solid fa-upload" aria-hidden="true"></i> 調撥出庫</a>
                            <?php } ?>
                            <?php if($sys_id_role <= 1){ ?>
                                <a href="restock.php" title="PR請購進貨" class="btn btn-success" ><i class="fa-solid fa-download"></i> 請購入庫</a>
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
                                <div class="border rounded px-3 py-2" style="background-color: #D4D4D4;">
                                    <h5>表單狀態：</h5>
                                    <table class="table">
                                        <thead>
                                            <tr class="table-dark">
                                                <th>狀態</th>
                                                <th>count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($sum_trades as $sum_trade){ ?>
                                                <tr>
                                                    <td><?php switch($sum_trade['idty']){
                                                            case "0" : echo '<span class="badge rounded-pill bg-warning text-dark">待領</span>'; break;
                                                            case "1" : echo '<span class="badge rounded-pill bg-danger">待簽</span>'; break;
                                                            case "2" : echo "退件"; break;
                                                            case "3" : echo "取消"; break;
                                                            case "10": echo "結案"; break;
                                                            case "11": echo "轉PR"; break;
                                                            case "12": echo '<span class="badge rounded-pill bg-success">待收</span>'; break;
                                                            default: echo "na"; break;
                                                            // return; 
                                                            }?></td>
                                                    <td>
                                                        <?php echo $sum_trade['idty_count']."&nbsp件";?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <span style="font-size: 14px;">交易狀態：0完成/1待簽/2退件/3取消</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 右邊清單 -->
                    <div class="col-12 col-md-9 px-1">
                        <div class="rounded bg-light">
                            <!-- <hr> -->
                            <div class="col-12">
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
                                                }else if(isset($trade_emp_id)){
                                                    echo "<a href=?emp_id=".$trade_emp_id."&page=1>首頁 </a> ";
                                                    echo "<a href=?emp_id=".$trade_emp_id."&page=".($page-1).">上一頁 </a> ";	
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
                                                        }else if(isset($trade_emp_id)){
                                                            echo '<a href=?emp_id='.$trade_emp_id.'&page='.$i.'>'.$i.'</a> ';
                                                        }else{
                                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                        }
                                                    }
                                                }
                                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                                if($page==$pages){
                                                    echo " 下一頁";
                                                    echo " 末頁";
                                                }else if(isset($trade_emp_id)){
                                                    echo "<a href=?emp_id=".$trade_emp_id."&page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?emp_id=".$trade_emp_id."&page=".$pages."> 末頁</a>";		
                                                }else{
                                                    echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?page=".$pages."> 末頁</a>";		
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                                <!-- 20211215分頁工具 -->
                                <table class="table-hover">
                                    <thead>
                                        <tr class="table-primary text-danger">
                                            <!-- <th>ai</th> -->
                                            <th>發貨日期</th>
                                            <th>發貨廠區</th>
                                            <th>發貨人</th>
                                            <th>收貨廠區</th>
                                            <th>收貨人</th>
                                            <th>收貨日期</th>
                                            <th>狀態</th>
                                            <th>action</th>
                                        </tr>
                                    </thead>
                                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                    <tbody>
                                        <?php foreach($trades as $trade){ ?>
                                            <tr>
                                                <!-- <td style="font-size: 6px;"><php echo $trade['id']; ?></td> -->
                                                <td title="aid: <?php echo $trade['id'];?>"><?php echo substr($trade['out_date'],0,10); ?></td>
                                                <td style="font-size: 14px; word-break: break-all;">
                                                    <?php if(!empty($trade["fab_o_title"])){ echo $trade['fab_o_title'].'('.$trade['fab_o_remark'].')';
                                                        }else{
                                                            echo "<b>".($trade["out_local"])."</b>";
                                                        }?>
                                                </td>
                                                <td><?php echo $trade['cname_o'];?></td>
                                                <td class="t-left"><?php echo $trade["fab_i_title"].'('.$trade['fab_i_remark'].')'.'_'.$trade["local_i_title"];?></td>
                                                <td><?php echo $trade['cname_i'];?></td>
                                                <td style="font-size: 6px;"><?php echo substr($trade['in_date'],0,10); ?></td>
                                                <td><?php $fab_role = ($trade['fab_i_id'] == $sys_id_fab_id || (in_array($trade['fab_i_id'], $sys_id_sfab_id)));
                                                    switch($trade['idty']){
                                                        case "0"    : echo "完成";                  break;
                                                        case "1"    : echo $fab_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"待簽"; break;
                                                        case "2"    : echo "退件";                  break;
                                                        case "3"    : echo "取消";                  break;
                                                        case "4"    : echo "編輯";                  break;
                                                        // case "10"   : echo "pr進貨";                break;
                                                        case "10"   : echo "結案";                  break;
                                                        default     : echo $trade['idty']."na";     break;
                                                    }?></td>
                                                <td>
                                                    <!-- Action功能欄 -->
                                                    <?php if((($trade['fab_i_id'] == $sys_id_fab_id) || (in_array($trade['fab_i_id'], $sys_id_sfab_id))) 
                                                            && ($trade['idty'] == '1')){ ?> 
                                                        <!-- 待簽：in_local對應人員 -->
                                                        <a href="show.php?id=<?php echo $trade['id'];?>&action=acceptance" class="btn btn-sm btn-xs btn-success">驗收</a>
                                                    <?php }else if((($trade['fab_o_id'] == $sys_id_fab_id) || (in_array($trade['fab_i_id'], $sys_id_sfab_id)))
                                                            && ($trade['idty'] == '2')){ ?>
                                                        <!-- 待簽：out_local對應人員 -->
                                                        <a href="show.php?id=<?php echo $trade['id'];?>&action=review" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                                    <?php }else if((($trade['fab_o_id'] == $sys_id_fab_id) || (in_array($trade['fab_o_id'], $sys_id_sfab_id)))
                                                            && ($trade['idty'] == '4')){ ?>
                                                        <!-- 待簽：out_local對應人員 -->
                                                        <a href="form.php?id=<?php echo $trade['id'];?>&action=edit" class="btn btn-sm btn-xs btn-success">編輯</a>
                                                    <?php }else{ ?>
                                                        <!-- siteUser功能 -->
                                                        <a href="show.php?id=<?php echo $trade['id'];?>" class="btn btn-sm btn-xs btn-info">檢視</a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <hr>
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
                                                }else if(isset($trade_emp_id)){
                                                    echo "<a href=?emp_id=".$trade_emp_id."&page=1>首頁 </a> ";
                                                    echo "<a href=?emp_id=".$trade_emp_id."&page=".($page-1).">上一頁 </a> ";		
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
                                                        }else if(isset($trade_emp_id)){
                                                            echo '<a href=?emp_id='.$trade_emp_id.'&page='.$i.'>'.$i.'</a> ';
                                                        }else{
                                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                        }
                                                    }
                                                }
                                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                                if($page==$pages){
                                                    echo " 下一頁";
                                                    echo " 末頁";
                                                }else if(isset($trade_emp_id)){
                                                    echo "<a href=?emp_id=".$trade_emp_id."&page=".($page+1)."> 下一頁</a>";
                                                    echo "<a href=?emp_id=".$trade_emp_id."&page=".$pages."> 末頁</a>";		
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
                            <!-- <hr> -->
                        </div>
                    </div>
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
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<script>  
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
    
    // setInterval('window.location.reload();', 60000);     // 60秒刷新一次

</script>

<?php include("../template/footer.php"); ?>