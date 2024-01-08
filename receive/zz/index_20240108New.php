<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用
    $sys_fab_id  = $_SESSION[$sys_id]["fab_id"];     
    $sys_sfab_id = $_SESSION[$sys_id]["sfab_id"];  
    $form_type   = "receive";

        // 身分選擇功能：定義user進來要看到的項目
        $is_emp_id = "All";    // 預設值=All
        $is_fab_id = "All";    // 預設值=All
        
        // 1.決定開啟表單的功能：
        if(isset($_REQUEST["fun"]) && $_REQUEST["fun"] != "myReceive"){
            // $fun = $_REQUEST["fun"];                         // 有帶fun，套查詢參數
            $fun = "myFab";                                     // 有帶fun，直接套用 myFab = 3轄區申請單 (管理頁面)
        }else{
            $fun = "myReceive";                                 // 沒帶fun，預設套 myReceive = 2我的申請單 (預設頁面)
        }
        // 2-1.身分選擇功能：定義user進來要看到的項目
        if(isset($_REQUEST["emp_id"])){             // 有帶查詢，套查詢參數
            $is_emp_id = $_REQUEST["emp_id"];
            // if($is_emp_id == "All"){
            //     $is_fab_id = "All";
            // }else{
            //     $is_fab_id = $sys_fab_id;
            // }

        }else if($sys_role >=2){                    // 沒帶查詢，含2以上=套自身主fab_id
            $is_emp_id = $auth_emp_id;
            // $is_fab_id = $sys_fab_id;
        }

        // 2-2.檢視廠區內表單
        if(isset($_REQUEST["fab_id"])){
            $is_fab_id = $_REQUEST["fab_id"];                   // 有帶查詢fab_id，套查詢參數   => 只看要查詢的單一廠
        }else{
            $is_fab_id = "allMy";                               // 其他預設值 = allMy   => 有關於我的轄區廠(fab_id + sfab_is)
        }
        
    // 組合查詢陣列
        $query_arr = array(
            'sys_id'    => $sys_id,
            'role'      => $sys_role,
            'sign_code' => $_SESSION["AUTH"]["sign_code"],
            'fab_id'    => $is_fab_id,
            'emp_id'    => $auth_emp_id,
            'is_emp_id' => $is_emp_id,
            'fun'       => "myReceive"
        );

        $row_lists      = show_my_receive($query_arr);
    
    // <!-- 20211215分頁工具 -->
        $per_total = count($row_lists);     // 計算總筆數
        $per = 25;                          // 每頁筆數
        $pages = ceil($per_total/$per);     // 計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        if(!isset($_GET['page'])){          // !isset 判斷有沒有$_GET['page']這個變數
            $page = 1;	  
        }else{
            $page = $_GET['page'];
        }
        $start = ($page-1)*$per;            // 每一頁開始的資料序號(資料庫序號是從0開始)
        // 合併嵌入分頁工具
            $query_arr["start"] = $start;
            $query_arr["per"] = $per;

        $row_lists_div = show_my_receive($query_arr);
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
            <div class="col_xl_12 col-12 p-3 pb-5 rounded" style="background-color: rgba(255, 200, 100, .6);" >
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-md-6 py-1 page_title">
                        <h3><b><?php echo $form_type;?> 表單匯總：</b></h3>
                    </div>
                    <div class="col-md-6 py-1">
     
                    </div>
                </div>
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder" class="col-11 mb-0 p-0"></div>

                <!-- NAV 分頁標籤 -->
                <div class="col-12 p-0">
                    <ul class="nav nav-tabs" id="nav-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" href="../issue/" aria-current="page" data-toggle="tooltip" data-placement="bottom" title="issue">
                                <i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b><span id="nav_bob_1"></span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../trade/" aria-current="page" data-toggle="tooltip" data-placement="bottom" title="trade">
                                <i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b><span id="nav_bob_2"></span></a>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link active" id="nav-tab_3" data-bs-toggle="tab" data-bs-target="#tab_3" role="tab" aria-controls="tab_3" aria-selected="true" >
                                <i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b><span id="nav_bob_3"></span></button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link       " id="nav-tab_4" data-bs-toggle="tab" data-bs-target="#tab_4" role="tab" aria-controls="tab_4"                      >
                                <i class="fa-solid fa-3"></i>&nbsp<b>我的領用申請</b><span id="nav_bob_4"></span></button>
                        </li>
                    </ul>

                </div>

                <!-- 內頁 -->
                <!-- 3.領用申請總表 -->
                <div class="tab-pane bg-white fade p-2 show active" id="tab_3" role="tabpanel" aria-labelledby="nav-tab_3">
                    <div class="col-12 bg-white">
                        <!-- tab head -->
                        <div class="row">
                            <div class="col-12 col-md-8 py-1">
                                <div class="row">
                                    <?php if($fun != 'myFab'){ ?>
                                        <!-- 功能1 -->
                                        <div class="col-12 py-0">
                                            <?php if($sys_role <= 2){ ?>
                                                <a href="?fun=myFab" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="切換到-轄區文件"><i class="fa-solid fa-right-left"></i></a>
                                            <?php } ?>
                                            <h5 style="display: inline;">我的申請文件：<sup>- myReceives </sup></h5>
                                        </div>
                                    <?php } else { ?>
                                        <!-- 功能2 -->
                                        <div class="col-12 col-md-5 py-0">
                                            <a href="?fun=myReceive" class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="切換到-申請文件"><i class="fa-solid fa-right-left"></i></a>
                                            <h5 style="display: inline;">我的轄區文件：<sup>- myFab's Receive </sup></h5>
                                        </div>
                                        <!-- 篩選功能?? -->
                                        <div class="col-12 col-md-7 py-0">
                                            <?php if($sys_role <= 2 ){ ?>
                                                <form action="" method="get">
                                                    <div class="input-group">
                                                        <select name="fab_id" id="sort_fab_id" class="form-select" >$myFab_lists
                                                            <option for="sort_fab_id" value="All" <?php echo $is_fab_id == "All" ? "selected":""; echo $sys_role >= 2 ? "hidden":""; ?>>-- [ All fab ] --</option>
                                                            <option for="sort_fab_id" value="allMy" <?php echo $is_fab_id == "allMy" ? "selected":"";?>>-- [ All my fab ] --</option>
                                                            <?php foreach($myFab_lists as $myFab){ ?>
                                                                <option for="sort_fab_id" value="<?php echo $myFab["id"];?>" title="<?php echo $myFab["fab_title"];?>" <?php echo $is_fab_id == $myFab["id"] ? "selected":"";?>>
                                                                    <?php echo $myFab["id"]."：".$myFab["fab_title"]." (".$myFab["fab_remark"].")"; echo $myFab["flag"] == "Off" ? "(已關閉)":"";?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <select name="emp_id" id="sort_emp_id" class="form-select">
                                                            <option for="sort_emp_id" value="All" <?php echo $is_emp_id == "All" ? "selected":"";?>>-- [ All user ] --</option>
                                                            <option for="sort_emp_id" value="<?php echo $auth_emp_id;?>" <?php echo $is_emp_id == $auth_emp_id ? "selected":"";?>>
                                                                <?php echo $auth_emp_id."_".$_SESSION["AUTH"]["cname"];?></option>
                                                        </select>
                                                        <input type="hidden" name="fun" id="fun" value="<?php echo $fun;?>">
                                                        <button type="submit" class="btn btn-secondary" ><i class="fa-solid fa-magnifying-glass"></i>&nbsp篩選</button>
                                                    </div>
                                                </form>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 py-1 text-end">
                                <?php if(isset($sys_role)){ ?>
                                    <a href="form.php?action=create" class="btn btn-primary"><i class="fa fa-edit" aria-hidden="true"></i> 填寫領用申請</a>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if($per_total > 0){ ?>
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
                            <table id="receive_lists" class="table table-hover">
                                <thead>
                                    <tr class="table-warning text-danger">
                                        <th>開單日期</th>
                                        <th>提貨廠區</th>
                                        <th>申請單位...</th>
                                        <th>申請人...</th>
                                        <th>簽單日期</th>
                                        <th>類別</th>
                                        <th>狀態</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($row_lists_div as $row){ 
                                        $pm_emp_id = $row["pm_emp_id"];                                         // *** 廠區業務窗口
                                        $pm_emp_id_arr = explode(",",$pm_emp_id);                                   //資料表是字串，要炸成陣列
                                        $form_role= (($row['in_sign'] === $auth_emp_id) || ($sys_role <= 1));
                                        ?>
                                        <tr>
                                            <td title="aid:<?php echo $row['id'];?>"><?php echo substr($row['created_at'],0,10);?></td>
                                            <td><?php echo $row['fab_title'].' ('.$row['fab_remark'].')';?></td>
                                            <td class="word_bk"><?php echo $row["plant"]." / ".$row["dept"];?></td>
                                            <td><?php echo $row["cname"]; echo $row["emp_id"] ? " (".$row["emp_id"].")":"";?></td>
                                            <td><?php echo substr($row["updated_at"],0,10); ?></td>
                                            <td><?php 
                                                    switch($row['ppty']){
                                                        case "0":   echo '<span class="text-primary">臨時</span>';    break;
                                                        case "1":   echo '定期';                                      break;
                                                        case "3":   echo '<span class="text-danger">緊急</span>';     break;
                                                        // default:    echo '錯誤';   break;
                                                    } ;?></td>
                                            <td><?php 
                                                    switch($row['idty']){
                                                        case "0"    : echo "<span class='badge rounded-pill bg-success'>待續</span>";                                break;
                                                        case "1"    : echo $form_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"簽核中";        break;
                                                        case "2"    : echo (in_array($auth_emp_id, [$row['emp_id'], $row['created_emp_id']])) 
                                                                                    ? '<span class="badge rounded-pill bg-warning text-dark">退件</span>':"退件";     break;
                                                        case "3"    : echo "取消";                  break;
                                                        case "4"    : echo "編輯";                  break;
                                                        case "10"   : echo "結案";                  break;
                                                        case "11"   : echo "環安主管";              break;
                                                        case "12"   : echo (in_array($auth_emp_id, [$row['emp_id'], $row['created_emp_id']]) ||
                                                                                ($row['fab_id'] == $sys_fab_id) || in_array($row['fab_id'], $sys_sfab_id)) 
                                                                                    ? '<span class="badge rounded-pill bg-success">待領</span>':"待領";      break;
                                                        case "13"   : echo (in_array($auth_emp_id, $pm_emp_id_arr)) 
                                                                                    ? '<span class="badge rounded-pill bg-danger">承辦簽核</span>': "承辦簽核";         break;
                                                        default     : echo $row['idty']."na";   break;
                                                    }; ?>
                                            </td>
                                            <td>
                                                <!-- Action功能欄 -->
                                                <?php if(in_array($row['idty'], [1 ]) && $form_role){ ?> 
                                                    <!-- 待簽：in_local對應人員 -->
                                                    <a href="show.php?uuid=<?php echo $row['uuid'];?>&action=sign" class="btn btn-sm btn-xs btn-primary">簽核</a>
                                                <!-- siteUser功能 -->
                                                <?php } else if((in_array($row['idty'], [13 ])) && (in_array($auth_emp_id, $pm_emp_id_arr) || $form_role
                                                        || ( ($row['local_id'] == $sys_fab_id) || (in_array($row['local_id'], [$sys_sfab_id])) )
                                                    )){ ?>
                                                    <a href="show.php?uuid=<?php echo $row['uuid'];?>&action=sign" class="btn btn-sm btn-xs btn-primary">簽核</a>

                                                <?php } else if((in_array($row['idty'], [2 ])) && ($row['emp_id'] == $auth_emp_id) ){ ?>

                                                    <a href="show.php?uuid=<?php echo $row['uuid'];?>&action=sign" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                                <?php } else { ?>
                                                    <!-- siteUser功能 -->
                                                    <a href="show.php?uuid=<?php echo $row['uuid'];?>&action=review" class="btn btn-sm btn-xs btn-info">檢視</a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
                        <?php }else{ ?>
                            <div class="col-12 border rounded bg-white text-center text-danger"> [ 您沒有待簽核的文件! ] </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="tab-pane bg-white fade p-2 " id="tab_4" role="tabpanel" aria-labelledby="nav-tab_4">
                </div>

            </div>
        </div>
    </div>
<!-- toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="1000">
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
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

</script>

<?php include("../template/footer.php"); ?>

