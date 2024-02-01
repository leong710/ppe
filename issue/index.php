<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用
    $sys_fab_id  = $_SESSION[$sys_id]["fab_id"];     
    $sys_sfab_id = $_SESSION[$sys_id]["sfab_id"];  
    $form_type   = "issue";
    
    // 身分選擇功能：定義user進來要看到的項目
    // $is_emp_id = "All";    // 預設值=All
    $is_emp_id = $auth_emp_id;                      // 預設值 = 自己
    $is_fab_id = "All";                             // 預設值=All

    // 2-1.篩選：檢視allMy或All、其他廠區內表單
        if(isset($_REQUEST["fab_id"])){
            $is_fab_id = $_REQUEST["fab_id"];       // 有帶查詢fab_id，套查詢參數   => 只看要查詢的單一廠
        }else{
            $is_fab_id = "allMy";                   // 其他預設值 = allMy   => 有關於我的轄區廠(fab_id + sfab_is)
        }
    // 2-2.篩選身分：定義user進來要看到的項目
        if(isset($_REQUEST["emp_id"])){             // 有帶查詢，套查詢參數
            $is_emp_id = $_REQUEST["emp_id"];
        }else if($sys_role >=2){                    // 沒帶查詢，含2以上=套自身主fab_id
            $is_emp_id = $auth_emp_id;
        }
    // 2-3.篩選年分~~
        if(isset($_REQUEST["_year"])){
            $_year = $_REQUEST["_year"];
        }else{
            // $_year = date('Y');                         // 今年
            $_year = "All";                                // 全年
        }
        
    // 組合查詢陣列
        $query_arr = array(
            'sys_id'    => $sys_id,
            'role'      => $sys_role,
            'sign_code' => $_SESSION["AUTH"]["sign_code"],
            'emp_id'    => $auth_emp_id,
            'fab_id'    => $is_fab_id,
            'is_emp_id' => $is_emp_id,
            '_year'     => $_year,
            'form_type' => $form_type
        );

    // 3.組合我的廠區到$sys_sfab_id => 包含原sfab_id、fab_id和sign_code所涵蓋的coverFab廠區
        if(!in_array($sys_fab_id, $sys_sfab_id)){                       // 4-1.當fab_id不在sfab_id，就把部門代號id套入sfab_id
            array_push($sys_sfab_id, $sys_fab_id);                      // 4-1.*** 取sfab_id (此時已包含fab_id)
        }
        $coverFab_lists = show_coverFab_lists($query_arr);              // 4-2.呼叫fun 用$sign_code模糊搜尋
        if(!empty($coverFab_lists)){                                    // 4-2.當清單不是空值時且不在sfab_id，就把部門代號id套入sfab_id
            foreach($coverFab_lists as $coverFab){ 
                if(!in_array($coverFab["id"], $sys_sfab_id)){
                    array_push($sys_sfab_id, $coverFab["id"]);
                }
            }
        }

        $cover_fab_id = $sys_sfab_id;                               // 4-3.*** 取sfab_id  (此時已包含fab_id、coverFab)
        $cover_fab_id = implode(",",$cover_fab_id);                 // 4-3.sfab_id是陣列，要儲存前要轉成字串

    // 4.(左下)我的轄區清單 = 套 allMy、$cover_fab_id
        $query_arr["sfab_id"] = $cover_fab_id;                      // 4-4.將字串$cover_fab_id加入組合查詢陣列中
        $query_arr["fab_id"]  = 'All';                               // 管理員、大PM = 全部廠區
        $myFab_lists = show_myFab_lists($query_arr);                // 我的轄區-篩選功能

        $query_arr["fab_id"]  = $is_fab_id;                      // selectFab

        $row_lists       = show_issue_list($query_arr);
        $sum_issues_ship = show_sum_issue_ship($query_arr);    // 統計看板--下：轉PR單
        $issue_years     = show_issue_GB_year();               // 取出issue年份清單 => 供首頁面篩選
        // $_inplan         = show_plan($query_arr);              // 查詢表單計畫 20240118 == 讓表單呈現 true 或 false
        extract(show_plan($query_arr));                        // 查詢表單計畫 20240118 == 讓表單呈現 true 或 false

        $query_inSign_arr = array(
            'fun'       => "inSign",
            'sys_role'  => $sys_role,
            'emp_id'    => $auth_emp_id
        );
        $my_inSign_lists = show_my_inSign($query_inSign_arr);
    // <!-- 20211215分頁工具 -->
        $per_total = count($row_lists);        // 計算總筆數
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

        $row_lists_div = show_issue_list($query_arr);
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
        .page_title, .op_tab_btn {
            color: white;
            /* text-shadow:3px 3px 9px gray; */
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
        }
        a, .nav-link {
            color: black;
        }
        .bsod {
            box-shadow: 3px 3px 5px rgba(0,0,0,.5);
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
            <div class="col_xl_12 col-12 p-3 pb-5 rounded" style="background-color: rgba(200, 255, 255, .6);" >
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-md-6 py-1 page_title">
                        <h3><b>PPE表單匯總：</b><?php echo $form_type;?></h3>
                    </div>
                    <div class="col-md-6 py-1 text-end">
                    </div>
                </div>
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder" class="col-12 text-center mb-0 p-0"></div>

                <!-- NAV 分頁標籤 -->
                <div class="col-12 p-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="../issue/" ><i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b><span id="nav_bob_1"></span></a>
                        </li>
                        <?php if($sys_role <= 2.5 ){ ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../trade/" ><i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b><span id="nav_bob_2"></span></a>
                            </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../receive/" ><i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b><span id="nav_bob_3"></span></a>
                        </li>
                    </ul>
                </div>

                <!-- 內頁 -->
                <!-- 1.請購需求總表 -->
                <div class="col-12 bg-white">
                    <!-- tab head -->
                    <div class="row">
                        <!-- 篩選功能 -->
                        <div class="col-8 col-md-9 py-1">
                            <form action="" method="POST">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-search"></i>&nbsp篩選</span>
                                    <select name="_year" id="sort_year" class="form-select">
                                        <option for="sort_year" value="All" <?php if($_year == "All"){ ?>selected<?php } ?> >-- 年度 / All --</option>
                                        <?php foreach($issue_years as $issue_year){ ?>
                                            <option for="sort_year" value="<?php echo $issue_year["_year"];?>" <?php if($issue_year["_year"] == $_year){ ?>selected<?php } ?>>
                                                <?php echo $issue_year["_year"]."y";?></option>
                                        <?php } ?>
                                    </select>
                                    <select name="fab_id" id="sort_fab_id" class="form-select" >
                                        <option for="sort_fab_id" value="All" <?php echo $is_fab_id == "All" ? "selected":"";?>>-- All fab --</option>
                                        <?php if($sys_role <= 2 ){ ?>
                                            <option for="sort_fab_id" value="allMy" <?php echo $is_fab_id == "allMy" ? "selected":"";?>>-- All my fab --</option>
                                        <?php } ?>
                                        <?php foreach($myFab_lists as $myFab){ ?>
                                            <option for="sort_fab_id" value="<?php echo $myFab["id"];?>" title="fab_id:<?php echo $myFab["id"];?>" <?php echo $is_fab_id == $myFab["id"] ? "selected":"";?>>
                                                <?php echo $myFab["fab_title"]." (".$myFab["fab_remark"].")"; echo $myFab["flag"] == "Off" ? "(已關閉)":"";?></option>
                                        <?php } ?>
                                    </select>
                                    <select name="emp_id" id="sort_emp_id" class="form-select" >
                                        <?php if($sys_role <= 2 ){ ?>
                                            <option for="sort_emp_id" value="All" <?php echo $is_emp_id == "All" ? "selected":"";?>>-- All user --</option>
                                        <?php } ?>
                                        <option for="sort_emp_id" value="<?php echo $auth_emp_id;?>" <?php echo $is_emp_id == $auth_emp_id ? "selected":"";?>>
                                            <?php echo $auth_emp_id."_".$auth_cname;?></option>
                                    </select>
                                    <button type="submit" class="btn btn-outline-secondary">查詢</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-4 col-md-3 py-1 text-end">
                            <?php if($sys_role <= 1){ ?>
                                <a href="show_issueAmount.php" title="管理員限定" class="btn btn-warning"><i class="fa-brands fa-stack-overflow"></i> 待轉PR總表</a>
                            <?php } ?>
                            <?php if($sys_role <= 2 && $_inplan){ ?>
                                <a href="form.php?action=create" class="btn btn-primary"><i class="fa fa-edit" aria-hidden="true"></i> 填寫請購需求</a>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- tab body -->
                    <div class="row">
                        <!-- 左邊統計 -->
                        <!-- L左邊 -->
                        <div class="col-12 col-md-4 px-1">
                            <div class="row">
                                <div class="col-6 col-md-12 pt-0">
                                    <!-- L1.我的待簽清單 -->
                                    <div class="rounded bg-light px-3 py-2 bsod">
                                        <div class="col-12 px-0 pb-0">
                                            <h5><i class="fa-brands fa-stack-overflow"></i> 我的待簽清單：<sup>- inSign </sup>
                                                <?php echo count($my_inSign_lists) >0 ? "<sup><span class='badge rounded-pill bg-warning text-dark'> +".count($my_inSign_lists)."</sup></span>" :"" ;?>
                                            </h5>
                                        </div>
                                        <div class="col-12 px-0 pb-1 pt-0">
                                            <!-- <簡易表單流程> -->
                                            <div class="rounded bg-success text-white p-2">
                                                <span><b>簡易表單流程：</b>
                                                <button type="button" id="sign_remark_btn" class="op_tab_btn" value="sign_remark" onclick="op_tab(this.value)" title="訊息收折"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                                <div id="sign_remark">
                                                    <div>
                                                        <span>1.(user)填單+送出 => 2.主管簽核 => 3.PM簽核 => 4.(PM)待轉PR+PR開單確認 5.轉PR => 6.(PM)交貨 => 7.(user)驗收 => 8.表單結案~</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-end">** PM交貨時，請確實填寫交貨數量，表單送出交貨後，無法退件或取消。</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(count($my_inSign_lists) >0){ ?>
                                            <table class="table">
                                                <thead>
                                                    <tr class="table-dark">
                                                        <th>開單日期</th>
                                                        <th>提貨廠區/申請單位/申請人</th>
                                                        <th>狀態</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($my_inSign_lists as $my_inSign){ ?>
                                                        <tr>
                                                            <td title="aid:<?php echo $my_inSign['id'];?>"><?php echo substr($my_inSign['create_date'],0,10);?></td>
                                                            <td class="word_bk"><a href="show.php?id=<?php echo $my_inSign['id'];?>&action=sign" title="aid:<?php echo $my_inSign['id'];?>">
                                                                <?php echo $my_inSign['fab_title']." / ".$my_inSign['dept']." / ".$my_inSign["cname"];?></a></td>
                                                            <td><?php $sign_sys_role = (($my_inSign['in_sign'] == $auth_emp_id) || ($sys_role <= 1));
                                                                switch($my_inSign['idty']){     // 處理 $_2我待簽清單  idty = 1申請送出、11發貨後送出、13發貨
                                                                    case "1"    : echo '<span class="badge rounded-pill bg-danger">待簽</span>';        break;
                                                                    case "11"   : echo '<span class="badge rounded-pill bg-warning text-dark">待結</span>';        break;
                                                                    case "13"   : echo '<span class="badge rounded-pill bg-warning text-dark">待結</span>';        break;
                                                                    default     : echo $my_inSign['idty']."--";   break;
                                                                }; ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        <?php }else{ ?>
                                            <div class="col-12 rounded bg-white text-center text-danger"> [ 您沒有待簽核的文件! ] </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-6 col-md-12 pt-0">
                                    <!-- L2.已轉PR清單 -->
                                    <div class="rounded bg-light px-3 py-2 bsod">
                                        <h5>已轉PR清單：<sup class="text-danger"> * Limit 25</sup></h5>
                                        <table class="table">
                                            <thead>
                                                <tr class="table-dark">
                                                    <th>結單日期</th>
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
                        <div class="col-12 col-md-8 px-3">
                            <!-- 20211215分頁工具 -->               
                            <div class="row">
                                <div class="col-12 col-md-6 pt-1">	
                                    <?php
                                        //每頁顯示筆數明細
                                        echo '顯示 '.$page_start.' 到 '.$page_end.' 筆 共 '.$per_total.' 筆，目前在第 '.$page.' 頁 共 '.$pages.' 頁'; 
                                    ?>
                                </div>
                                <div class="col-12 col-md-6 pt-1 text-end">
                                    <?php
                                        if($pages>1){  //總頁數>1才顯示分頁選單
        
                                            //分頁頁碼；在第一頁時,該頁就不超連結,可連結就送出$_GET['page']
                                            if($page=='1'){
                                                echo "首頁 ";
                                                echo "上一頁 ";		
                                            }else{
                                                $page_h = "<a href=?page=1";
                                                $page_u = "<a href=?page=".($page-1);
                                                    if(isset($_year)){
                                                        $page_h .= "&_year=".$_year;
                                                        $page_u .= "&_year=".$_year;		
                                                    }
                                                    if(isset($is_emp_id)){
                                                        $page_h .= "&emp_id=".$is_emp_id;
                                                        $page_u .= "&emp_id=".$is_emp_id;		
                                                    }
                                                echo $page_h.">首頁 </a> ";
                                                echo $page_u.">上一頁 </a> ";	
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
                                                        $page_n = '<a href=?page='.$i;
                                                        if(isset($_year)){
                                                            $page_n .= "&_year=".$_year;
                                                        }
                                                        if(isset($is_emp_id)){
                                                            $page_n .= "&emp_id=".$is_emp_id;
                                                        }
                                                        echo $page_n.'>'.$i.'</a> ';
                                                    }
                                                }
                                            }
                                            //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                            if($page==$pages){
                                                echo " 下一頁";
                                                echo " 末頁";	
                                            }else{
                                                $page_d = "<a href=?page=".($page+1);
                                                $page_e = "<a href=?page=".$pages;
                                                    if(isset($_year)){
                                                        $page_d .= "&_year=".$_year;
                                                        $page_e .= "&_year=".$_year;		
                                                    }
                                                    if(isset($is_emp_id)){
                                                        $page_d .= "&emp_id=".$is_emp_id;
                                                        $page_e .= "&emp_id=".$is_emp_id;		
                                                    }
                                                echo $page_d."> 下一頁</a> ";
                                                echo $page_e."> 末頁</a> ";
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <!-- 20211215分頁工具 -->
                            <table id="table_div" class="table table-hover">
                                <thead>
                                    <tr class="table-primary text-danger">
                                        <th>開單日期</th>
                                        <th>需求廠區</th>
                                        <th>需求者</th>
                                        <th>發貨廠區</th>
                                        <th>發貨人</th>
                                        <th>結單日期</th>
                                        <th>類別</th>
                                        <th>貨態</th>
                                        <th>狀態</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($row_lists_div as $row){ ?>
                                        <tr>
                                            <td title="aid: <?php echo $row['id'];?>"><?php echo substr($row['create_date'],0,10); ?></td>
                                            <td><?php echo $row['fab_i_title'].'('.$row['fab_i_remark'].')';?></td>
                                            <td><?php echo $row['cname_i'];?></td>
                                            <td style="font-size: 12px; word-break: break-all;">
                                                <?php echo (!empty($row["fab_o_title"])) ? $row["fab_o_title"].'('.$row['fab_o_remark'].')' : $row["out_local"];?>
                                            </td>
                                            <td><?php echo $row['cname_o'];?></td>
                                            <td style="font-size: 6px;"><?php echo substr($row['in_date'],0,10); ?></td>
                                            <td><?php echo $row['ppty'];
                                                    switch($row['ppty']){
                                                        case "0": echo '.臨時'; break;
                                                        case "1": echo '.定期'; break;
                                                        case "3": echo '.緊急'; break;
                                                        default:  echo '錯誤' ; break;
                                                    };?></td>
                                            <td style="font-size: 12px; word-break: break-all;"><?php echo $row['_ship'];?>
                                                <?php if($row['_ship'] == '0'){?>結案<?php ;}?>
                                                <?php if($row['_ship'] == '1'){?>轉PR<?php ;}?>
                                            </td>
                                            <td><?php $form_role = ($sys_role <= 1) || (($row['fab_i_id'] == $sys_fab_id) || (in_array($row['fab_i_id'], $sys_sfab_id)));
                                                switch($row['idty']){
                                                    case "0"    : echo $form_role ? '<span class="badge rounded-pill bg-warning text-dark">待轉</span>':"待轉";  break;
                                                    case "1"    : echo $form_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"待簽";             break;
                                                    case "2"    : echo "退件";                  break;
                                                    case "3"    : echo "取消";                  break;
                                                    case "4"    : echo "編輯";                  break;
                                                    case "10"   : echo "結案";                  break;
                                                    case "11"   : echo "轉PR";                  break;
                                                    case "13"   : echo "<span class='badge rounded-pill bg-success'>待收</span>";  break;
                                                    default     : echo $row['idty']."na";       break;
                                                }?>
                                            </td>
                                            <td>
                                                <!-- Action功能欄 -->
                                                <?php if(($row['idty'] == '1') && ($sys_role <= 1)){ // 1待簽 ?>        
                                                    <!-- 待簽：PM+管理員功能 -->
                                                    <a href="show.php?id=<?php echo $row['id'];?>&action=sign" class="btn btn-sm btn-xs btn-primary">簽核</a>
                                                    <!-- siteUser功能 -->
                                                <?php } else if(($row['idty'] == '11') && ($sys_role <= 1)){ ?>
                                                    <a href="show.php?id=<?php echo $row['id'];?>&action=sign" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                                    <!-- siteUser功能 -->
                                                <?php } else if((in_array($row['idty'], [2, 13])) && (($row['fab_i_id'] == $sys_fab_id) || (in_array($row['fab_i_id'], $sys_sfab_id))) ){ ?>
                                                    <a href="show.php?id=<?php echo $row['id'];?>&action=sign" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                                <?php } else { ?>
                                                    <!-- siteUser功能 -->
                                                    <a href="show.php?id=<?php echo $row['id'];?>&action=review" class="btn btn-sm btn-xs btn-info">檢視</a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php if($per_total <= 0){ ?>
                                <div class="col-12 border rounded bg-white text-center text-danger"> [ 查無 <?php echo isset($is_emp_id) ? "$is_emp_id":"";?> 的篩選文件! ] </div>
                            <?php } ?>
                            <hr>
                            <!-- 20211215分頁工具 -->               
                            <div class="row">
                                <div class="col-12 col-md-6 pt-1">	
                                    <?php
                                        //每頁顯示筆數明細
                                        echo '顯示 '.$page_start.' 到 '.$page_end.' 筆 共 '.$per_total.' 筆，目前在第 '.$page.' 頁 共 '.$pages.' 頁'; 
                                    ?>
                                </div>
                                <div class="col-12 col-md-6 pt-1 text-end">
                                    <?php
                                        if($pages>1){  //總頁數>1才顯示分頁選單
        
                                            //分頁頁碼；在第一頁時,該頁就不超連結,可連結就送出$_GET['page']
                                            if($page=='1'){
                                                echo "首頁 ";
                                                echo "上一頁 ";		
                                            }else{
                                                $page_h = "<a href=?page=1";
                                                $page_u = "<a href=?page=".($page-1);
                                                    if(isset($_year)){
                                                        $page_h .= "&_year=".$_year;
                                                        $page_u .= "&_year=".$_year;		
                                                    }
                                                    if(isset($is_emp_id)){
                                                        $page_h .= "&emp_id=".$is_emp_id;
                                                        $page_u .= "&emp_id=".$is_emp_id;		
                                                    }
                                                echo $page_h.">首頁 </a> ";
                                                echo $page_u.">上一頁 </a> ";	
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
                                                        $page_n = '<a href=?page='.$i;
                                                        if(isset($_year)){
                                                            $page_n .= "&_year=".$_year;
                                                        }
                                                        if(isset($is_emp_id)){
                                                            $page_n .= "&emp_id=".$is_emp_id;
                                                        }
                                                        echo $page_n.'>'.$i.'</a> ';
                                                    }
                                                }
                                            }
                                            //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                            if($page==$pages){
                                                echo " 下一頁";
                                                echo " 末頁";	
                                            }else{
                                                $page_d = "<a href=?page=".($page+1);
                                                $page_e = "<a href=?page=".$pages;
                                                    if(isset($_year)){
                                                        $page_d .= "&_year=".$_year;
                                                        $page_e .= "&_year=".$_year;		
                                                    }
                                                    if(isset($is_emp_id)){
                                                        $page_d .= "&emp_id=".$is_emp_id;
                                                        $page_e .= "&emp_id=".$is_emp_id;		
                                                    }
                                                echo $page_d."> 下一頁</a> ";
                                                echo $page_e."> 末頁</a> ";
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
    // init
    var sys_role    = '<?=$sys_role?>';
    var case_title  = '<?=$case_title?>';
    var _inplan     = '<?=$_inplan?>';
    var start_time  = '<?=$start_time?>';
    var end_time    = '<?=$end_time?>';
                                    
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
    // tab_table的顯示關閉功能
    function op_tab(tab_value){
        $("#"+tab_value+"_btn .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
        var tab_table = document.getElementById(tab_value);
        if (tab_table && (tab_table.style.display === "none")) {
            tab_table.style.display = "table";
        } else {
            tab_table.style.display = "none";
        }
    }
    // Bootstrap Alarm function
    function alert(message, type) {
        var alertPlaceholder = document.getElementById("liveAlertPlaceholder")      // Bootstrap Alarm
        var wrapper = document.createElement('div')
        wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message 
                            + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        alertPlaceholder.append(wrapper)
    }

    $(document).ready(function () {
        op_tab('sign_remark');
        // op_tab('scope_remark');

        // 假如index找不到當下存在已完成的表單，就alarm它!
        if (_inplan && (sys_role <= 2)) {
            let message  = '*** <b>'+case_title+'</b> 開放申請時間：<b><u>'+ start_time +'</u></b>&nbsp至&nbsp<b><u>'+ end_time +'</u></b>&nbsp有需求請務必在指定時間前完成申請&nbsp~&nbsp';
            message += '&nbsp<i class="fa-solid fa-right-long"></i>&nbsp<a href="form.php?action=create">'+'<b>打開請購需求單</b></a>';
            alert( message, 'warning')
        }

    })
</script>

<?php include("../template/footer.php"); ?>

