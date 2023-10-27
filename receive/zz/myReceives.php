<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 組合查詢陣列
        $basic_query_arr = array(
            'sys_id' => $sys_id,
            'role'   => $_SESSION[$sys_id]["role"],
            'fab_id' => "All",                          // 預設值=All
            'emp_id' => $_SESSION["AUTH"]["emp_id"]     // 套自身主emp_id
        );
        
    //處理 $_2我待簽清單 
        $basic_query_arr["fun"] = "inSign" ;                                // 指定fun = inSign
        $my_inSign_lists = show_my_receive($basic_query_arr);

    //處理 $_1我的申請單 *** 有分頁的要擺在分頁工具前!!
        $basic_query_arr["fun"] = "myReceive" ;                             // 指定fun = myReceive
        $my_myReceive_lists = show_my_receive($basic_query_arr);

    // <!-- 20211215分頁工具 -->
        $per_total = count($my_myReceive_lists);        // 計算總筆數 $_1我的申請單 
        $per = 25;                                      // 每頁筆數
        $pages = ceil($per_total/$per);                 // 計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        if(!isset($_GET['page'])){                      // !isset 判斷有沒有$_GET['page']這個變數
            $page = 1;	  
        }else{
            $page = $_GET['page'];
        }
        $start = ($page-1)*$per;                        // 每一頁開始的資料序號(資料庫序號是從0開始)
        // 合併嵌入分頁工具
            $receive_page_div = array(
                'start' => $start,
                'per' => $per
            );
            array_push($basic_query_arr, $receive_page_div);

        $my_myReceive_lists = show_my_receive($basic_query_arr);
        $page_start = $start +1;                        // 選取頁的起始筆數
        $page_end = $start + $per;                      // 選取頁的最後筆數
        if($page_end>$per_total){                       // 最後頁的最後筆數=總筆數
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
        .unblock{
            display: none;
            /* transition: 3s; */
        }
        .page_title{
            color: white;
            /* text-shadow:3px 3px 9px gray; */
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
        }
        .op_tab_btn {
            /* 將圖示的背景色設置為透明並添加陰影 */
            background-color: transparent; 
            text-shadow: 0px 0px 1px #fff;
            color: white;
            /* 將圖示的背景色設置為按鈕的背景色 */
            /* background-color: inherit; */
        }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 rounded mx-1 p-3 pt-0" style="background-color: rgba(255, 200, 100, .6);">
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-6 col-md-6 pb-1 page_title">
                        <div style="display:inline-block;">
                            <h3><i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b></h3>
                        </div>
                    </div>

                    <div class="col-6 col-md-6 pb-1 text-end">
                        <?php if(isset($_SESSION[$sys_id]["role"])){ ?>
                            <?php if($_SESSION[$sys_id]["role"] <= 3){ ?>
                                <a href="form.php?action=create" title="管理員限定" class="btn btn-primary"><i class="fa fa-edit" aria-hidden="true"></i> 填寫領用申請</a>
                            <?php } ?>
                            <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                <a href="index.php" title="管理員限定" class="btn btn-warning"><i class="fa-brands fa-stack-overflow"></i> 待簽總表</a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <!-- table -->
                <div class="row">
                    <!-- 左邊統計 -->
                    <div class="col-12 col-md-4 px-2 pt-0">
                        <div class="border rounded px-3 py-2" style="background-color: #D4D4D4;">
                            <div class="col-12 px-0 pb-0">
                                <h5>我的待簽清單：<sup>- inSign </sup>
                                    <?php echo count($my_inSign_lists) >0 ? "<sup><span class='badge rounded-pill bg-warning text-dark'> +".count($my_inSign_lists)."</sup></span>" :"" ;?>
                                </h5>
                            </div>
                            <div class="col-12 px-0 pt-0 pb-1">
                                <!-- <簡易表單流程> -->
                                <div class="rounded bg-success text-white p-2">
                                    <span><b>簡易表單流程：</b>
                                    <button type="button" id="sign_remark_btn" class="op_tab_btn" value="sign_remark" onclick="op_tab(this.value)" title="訊息收折"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                    <div id="sign_remark">
                                        1.(user)填寫需求單+送出 =>  2.待簽/申請人主管簽核 =>  3.待領/申請人領貨+發貨人確認送出 =>
                                        4.待簽/業務負責人簽核 =>  5.待簽/環安主管簽核 => 表單結案~
                                        <div class="text-end">** 簽核時若遇退件，請user重新編輯後再送單。</div>
                                    </div>
                                </div>
                            </div>
                            <?php if(count($my_inSign_lists) >0){ ?>
                                <table class="table">
                                    <thead>
                                        <tr class="table-dark">
                                            <th>開單日期</th>
                                            <th>申請單位</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($my_inSign_lists as $my_inSign){ ?>
                                            <tr>
                                                <td title="<?php echo $my_inSign['id'];?>"><?php echo substr($my_inSign['created_at'],0,10)." <sup>".$my_inSign['id']."</sup>";?></td>
                                                <td style="word-break: break-all;"><a href="show.php?uuid=<?php echo $my_inSign['uuid'];?>&action=sign" title="aid:<?php echo $my_inSign['id'];?>">
                                                    <?php echo $my_inSign['fab_title']." / ".$my_inSign['dept']." ".$my_inSign["cname"];?></a></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php }else{ ?>
                                <div class="col-12 rounded bg-white text-center text-danger"> [ 您沒有待簽核的文件! ] </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <!-- 右邊清單 -->
                    <div class="col-12 col-md-8 px-2 pt-0">
                        <!-- 內頁 -->
                        <div class="border rounded px-3 py-2 bg-light">
                            <div class="row">
                                <div class="col-12 pb-0">
                                    <h5>我的申請文件：<sup>- myReceives </sup></h5>
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
                                        <th>提貨廠區</th>
                                        <th>申請單位...</th>
                                        <th>申請人...</th>
                                        <th>簽單日期</th>
                                        <th>類別</th>
                                        <th>表單狀態</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($my_myReceive_lists as $receive){ ?>
                                        <tr>
                                            <td title="<?php echo $receive['id'];?>"><?php echo substr($receive['created_at'],0,10)." <sup>".$receive['id']."</sup>"; ?></td>
                                            <td><?php echo $receive['fab_title'].' ('.$receive['fab_remark'].')';?></td>
                                            <!-- <td style="font-size: 12px; word-break: break-all;"> -->
                                            <td><?php echo $receive["plant"]." / ".$receive["dept"];?></td>
                                            <td><?php echo $receive["cname"]; echo $receive["emp_id"] ? " (".$receive["emp_id"].")":"";?></td>
                                            <td><?php echo substr($receive["updated_at"],0,10); ?></td>
                                            <td><?php echo $receive['ppty'];
                                                    switch($receive['ppty']){
                                                        case "0":   echo '.臨時';  break;
                                                        case "1":   echo '.定期';  break;
                                                        case "3":   echo '.緊急';  break;
                                                        // default:    echo '錯誤';   break;
                                                    } ;?></td>
                                            <td><?php $sys_role = (($receive['in_sign'] == $_SESSION["AUTH"]["emp_id"]) || ($_SESSION[$sys_id]['role'] <= 1));
                                                        switch($receive['idty']){
                                                            case "0"    : echo $sys_role ? '<span class="badge rounded-pill bg-warning text-dark">待領</span>':"待領";      break;
                                                            case "1"    : echo $sys_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"待簽";                 break;
                                                            case "2"    : echo "退件";                  break;
                                                            case "3"    : echo "取消";                  break;
                                                            case "4"    : echo "編輯";                  break;
                                                            case "10"   : echo "結案";                  break;
                                                            case "11"   : echo "轉PR";                  break;
                                                            case "12"   : echo "<span class='badge rounded-pill bg-success'>待收</span>";        break;
                                                            default     : echo $receive['idty']."na";   break;
                                                        }; ?>
                                            </td>
                                            <td>
                                                <!-- Action功能欄 -->
                                                <?php if(($receive['idty'] == '1') && $sys_role){ ?> 
                                                    <!-- 待簽：in_local對應人員 -->
                                                    <a href="show.php?uuid=<?php echo $receive['uuid'];?>&action=sign" class="btn btn-sm btn-xs btn-primary">簽核</a>
                                                <?php } else { ?>
                                                    <!-- siteUser功能 -->
                                                    <a href="show.php?uuid=<?php echo $receive['uuid'];?>&action=review" class="btn btn-sm btn-xs btn-info">檢視</a>
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

<!-- 彈出畫面說明模組 review表單流程-->
    <div class="modal fade" id="review_role" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">preView 預覽：</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5" style="text-align:center;" >
                    <!-- <img src="role.png" style="width: 100%;" class="img-thumbnail"> -->
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
    // tab_table的顯示關閉功能
    function op_tab(tab_value){
        console.log(tab_value);
        $("#"+tab_value+"_btn .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
        var tab_table = document.getElementById(tab_value);
        if (tab_table && (tab_table.style.display === "none")) {
            tab_table.style.display = "table";
        } else {
            tab_table.style.display = "none";
        }
    }
</script>

<?php include("../template/footer.php"); ?>