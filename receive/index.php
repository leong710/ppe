<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("function.php");
    require_once("service_window.php");             // service window

    accessDenied($sys_id);

    // 身分選擇功能：定義user進來要看到的項目
    $is_emp_id   = $auth_emp_id;                  // 預設值 = 自己
    $is_fab_id   = "allMy";                       // 預設值 = allMy = coverFab範圍
        
    // 1.決定開啟表單的功能：
    $form_type   = "receive";
    $fun         = "myReceive";                     // 沒帶fun，預設套 myReceive = 2我的申請單 (預設頁面)
    // $fun = "myFab";                              // 有帶fun，直接套用 myFab = 3轄區申請單 (管理頁面)

    // 2-1.篩選：檢視allMy或All、其他廠區內表單
        $is_fab_id = (isset($_REQUEST["fab_id"])) ? $_REQUEST["fab_id"] : $is_fab_id = "All";                   
        // 有帶查詢fab_id，套查詢參數   => 只看要查詢的單一廠
        // 其他預設值 = All   => 全部flag=On的fab
        // $is_fab_id = "allMy";                   // 其他預設值 = allMy   => 有關於我的轄區廠(fab_id + sfab_is + coverFab)
        
    // 2-2.篩選身分：定義user進來要看到的項目
        if(isset($_REQUEST["emp_id"])){             // 有帶查詢，套查詢參數
            $is_emp_id = $_REQUEST["emp_id"];
        }else if($sys_role >=2){                    // 沒帶查詢，含2以上=套自身主fab_id
            $is_emp_id = $auth_emp_id;
        }

    // 2-3.篩選年分~~
        $_year  = (isset($_REQUEST["_year"]))  ? $_REQUEST["_year"]  : date('Y');   // 預設今年
        // $_year = date('Y');                       // 今年    // 全年 All
        $_month = (isset($_REQUEST["_month"])) ? $_REQUEST["_month"] : "All";      // 今月
        // $_month = date('m');                      // 今月
        $idty   = (isset($_REQUEST["idty"]))   ? $_REQUEST["idty"]   : "All";      // 今月

    // 組合查詢陣列
        $query_arr = array(
            // 'sys_id'    => $sys_id,
            'role'      => $sys_role,
            'sign_code' => $_SESSION["AUTH"]["sign_code"],
            'emp_id'    => $auth_emp_id,
            // 'is_fab_id' => $is_fab_id,
            'is_emp_id' => $is_emp_id,
            '_year'     => $_year,
            '_month'    => $_month,
            'idty'      => $idty
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
        // $cover_fab_id = array_filter($cover_fab_id);             // 4-3.去除空陣列 // 他會把0去掉
        $cover_fab_id = implode(",",$cover_fab_id);                 // 4-3.sfab_id是陣列，要儲存前要轉成字串

    // 4.(左下)我的轄區清單 = 套 allMy、$cover_fab_id
        $query_arr["fab_id"] = 'allMy';
        $query_arr["sfab_id"] = $cover_fab_id;                      // 4-4.將字串$cover_fab_id加入組合查詢陣列中
        $coverFab_lists = show_myFab_lists($query_arr);             // (左下)我的轄區清單

    // 4.我的轄區 - 篩選功能 = 套is_fab_id 
            // $query_arr["fab_id"] = ($sys_role <=1 ) ? 'All' : $is_fab_id;           // 管理員、大PM = 全部廠區  // siteUser = coverFab + selectFab
            // // 以上做法，一開始只出現我的轄區，選All之後會出現所有廠區，選廠區後會剩下我的轄區+所選廠區
            // // $myFab_lists = $coverFab_lists;                      // siteUser = coverFab
        $query_arr["fab_id"] = 'All';                               // 管理員、大PM = 全部廠區
        $myFab_lists = show_myFab_lists($query_arr);                // 我的轄區-篩選功能

    // 5-L1.我待簽清單 
        $query_arr["fun"] = "inSign" ;                              // 指定fun = inSign 簽核中
        $my_inSign_lists = show_my_receive($query_arr);

    // 5-L2.我的待領清單
        $query_arr["fun"] = 'myCollect';                            // 指定fun = myCollect 我的待領
        $my_collect_lists = show_my_receive($query_arr);

    // 5-2.處理 fun = myReceive我的申請單 / myFab轄區申請單： 
    // 5-3.fab_id=allMy => emp_id=my ； fab_id = All or fab.id => emp_id = All or is_emp_id
        //  ** 有分頁的要擺在分頁工具前!!
        $query_arr["fun"]       = $fun ;                           // 指定fun = $fun = myReceive
        $query_arr["fab_id"]    = $is_fab_id;                      // selectFab
        $row_lists              = show_my_receive($query_arr);

        $receive_years  = show_receive_GB_year();               // 取出receive年份清單 => 供首頁面篩選
        $sw_arr = (array) json_decode($sw_json);                // service window 物件轉陣列
        
    // <!-- 20211215分頁工具 -->
        $per_total = count($row_lists);     // 計算總筆數
        $per = 25;                          // 每頁筆數
        $pages = ceil($per_total/$per);     // 計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        // !isset 判斷有沒有$_GET['page']這個變數
        $page = (!isset($_GET['page'])) ? 1 : $_GET['page'];
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
    <link href="../../libs/aos/aos.css" rel="stylesheet">                                   <!-- goTop滾動畫面aos.css 1/4-->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>    <!-- Jquery -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>                            <!-- mloading JS 1/3 -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                    <!-- mloading CSS 2/3 -->
    <script src="../../libs/jquery/mloading_init.js"></script>                              <!-- mLoading_init.js 3/3 -->
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
            /* inline */
            .inb {
                display: inline-block;
            }
            .inf {
                display: inline-flex;
            }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 p-3 pb-5 rounded" style="background-color: rgba(255, 200, 100, .6);" >
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-md-6 py-1 page_title">
                        <h3><b>PPE表單匯總：</b><?php echo $form_type;?> </h3>
                    </div>
                    <div class="col-md-6 py-1">
     
                    </div>
                </div>
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder" class="col-11 mb-0 p-0"></div>

                <!-- NAV 分頁標籤 -->
                <div class="col-12 p-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link" href="../issue/" ><i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b><span id="nav_bob_1"></span></a>
                        </li>
                        <?php if($sys_role <= 2.5 ){ ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../trade/" ><i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b><span id="nav_bob_2"></span></a>
                            </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="../receive/" ><i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b><span id="nav_bob_3"></span></a>
                        </li>
                    </ul>
                </div>

                <!-- 內頁 -->
                <!-- 3.領用申請總表 -->
                <div class="col-12 bg-white">
                    <!-- tab head -->
                    <div class="row">
                        <!-- 篩選功能 -->
                        <div class="col-8 col-md-9 py-1">
                            <form action="" method="GET">
                                <div class="input-group">
                                    <input type="hidden" name="fun" id="fun" value="<?php echo $fun;?>">

                                    <span class="input-group-text"><i class="fa fa-search"></i>&nbsp篩選</span>
                                    <select name="_year" id="sort_year" class="form-select">
                                        <?php 
                                            echo "<option for='sort_year' value='All' ".(($_year == "All") ? "selected":"disabled")." >-- 年度 / All --</option>";
                                            foreach($receive_years as $receive_year){ 
                                                echo "<option for='sort_year' value='{$receive_year["_year"]}' ".(($receive_year["_year"] == $_year) ? "selected" : "")." >{$receive_year["_year"]}y</option>";
                                            } ?>
                                    </select>
                                    <select name="_month" id="sort_month" class="form-select">
                                        <?php 
                                            echo "<option value='All' ".(($_month == "All") ? "selected":"" )." >-- 全月份 / All --</option>";
                                            foreach (range(1, 12) as $item) {
                                                $item_str = str_pad($item, 2, '0', STR_PAD_LEFT);
                                                echo "<option value='{$item_str}' ".(($item_str == $_month ) ? "selected":"" )." >{$item_str}m</option>";
                                            } ?>
                                    </select>
                                    <select name="fab_id" id="sort_fab_id" class="form-select" >
                                        <?php 
                                            echo "<option for='sort_fab_id' value='All' ".(($is_fab_id == "All") ? "selected":"").">-- All fab --</option>";
                                            if($sys_role <= 2 ){ 
                                                echo "<option for='sort_fab_id' value='allMy' ".(($is_fab_id == "allMy") ? "selected":"").">-- All my fab --</option>";
                                            } 
                                            foreach($myFab_lists as $myFab){ 
                                                echo "<option for='sort_fab_id' value='{$myFab["id"]}' title='fab_id:{$myFab["id"]}' ".(($is_fab_id == $myFab["id"]) ? "selected":"") ."> 
                                                    {$myFab["fab_title"]} ({$myFab["fab_remark"]})".(($myFab["flag"] == "Off") ? "(已關閉)":"")."</option>";
                                            } ?>
                                    </select>
                                    <select name="emp_id" id="sort_emp_id" class="form-select">
                                        <?php if($sys_role <= 2 ){ 
                                                echo "<option for='sort_emp_id' value='All' ".(($is_emp_id == "All") ? "selected":"").">-- All user --</option>";
                                            } 
                                            echo "<option for='sort_emp_id' value='{$auth_emp_id}' ".(($is_emp_id == $auth_emp_id) ? "selected":"").">{$auth_emp_id}_{$auth_cname}</option>";
                                        ?>
                                    </select>
                                    <select name="idty" id="sort_idty" class="form-select">
                                        <?php 
                                            echo "<option for='sort_idty' value='All' ".(($idty == "All") ? "selected":"").">-- All idty --</option>";
                                            $idty_arr = [
                                                "11" => "環安主管", "12" => "待領", "13" => "業務承辦"
                                            ];
                                            foreach($idty_arr as $idty_key => $idty_value){ 
                                                echo "<option for='sort_idty' value='{$idty_key}' ".(($idty == $idty_key) ? "selected":"").">{$idty_key}_{$idty_value}</option>";
                                            }
                                        ?>
                                    </select>
                                    <button type="submit" class="btn btn-outline-secondary">查詢</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-4 col-md-3 py-1 text-end">
                            <?php if(($per_total != 0) && ($sys_role <= 2.5)){ ?>
                                <div class="inb">
                                    <!-- 20231128 下載Excel -->
                                    <form id="myForm" method="post" action="../_Format/download_excel.php">
                                        <input type="hidden" name="htmlTable" id="htmlTable" value="">
                                        <button type="submit" name="submit" class="btn btn-success" value="receive" onclick="submitDownloadExcel(this.value)" >
                                            <i class="fa fa-download" aria-hidden="true"></i> 匯出</button>
                                    </form>
                                </div>
                            <?php } ?>
                            <button type="button" id="service_window_btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#service_window"><i class="fa-solid fa-circle-info"></i> 聯絡窗口</button>
                            <?php if(isset($sys_role)){ 
                                echo " <button type='button' value='form.php?action=create' class='btn btn-primary' onclick='openUrl(this.value)' ><i class='fa fa-edit' aria-hidden='true'></i> 領用申請</button>";
                            } ?>
                        </div>
                    </div>
                    <!-- tab body -->
                    <div class="row">
                        <!-- L左邊 -->
                        <div class="col-12 col-md-4 px-1">
                            <div class="row">
                                <!-- L1.我的待簽清單 -->
                                <div class="col-6 col-md-12 pt-0">
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
                                                        <th>提貨廠區/申請單位/申請人</th>
                                                        <th>狀態</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($my_inSign_lists as $my_inSign){
                                                        echo "<tr>"."<td title='aid:{$my_inSign["id"]}'>".substr($my_inSign["created_at"],0,10)."</td>";
                                                        echo "<td class='word_bk'>";
                                                        echo "<button type='button' value='show.php?uuid={$my_inSign["uuid"]}&action=sign' title='aid:{$my_inSign["id"]}' onclick='openUrl(this.value)' class='tran_btn'>";
                                                        echo $my_inSign['fab_title']." / ".$my_inSign['dept']." / ".$my_inSign["cname"]."</button>";

                                                            // <a href="show.php?uuid=<?php echo $my_inSign['uuid'];>&action=sign" title="aid:<?php echo $my_inSign['id'];>">
                                                                // <php echo $my_inSign['fab_title']." / ".$my_inSign['dept']." / ".$my_inSign["cname"];></a></td>
                                                        echo "</td><td>";
                                                        $sign_sys_role = (($my_inSign['in_sign'] == $auth_emp_id) || ($sys_role <= 1));
                                                        switch($my_inSign['idty']){     // 處理 $_2我待簽清單  idty = 1申請送出、11發貨後送出、13發貨
                                                            case "1"    : echo '<span class="badge rounded-pill bg-danger">待簽</span>';                   break;
                                                            case "11"   : echo '<span class="badge rounded-pill bg-warning text-dark">待結</span>';        break;
                                                            case "13"   : echo '<span class="badge rounded-pill bg-warning text-dark">待結</span>';        break;
                                                            default     : echo $my_inSign['idty']."--";                                                    break;
                                                        };
                                                        echo "</td></tr>";
                                                    } ?>
                                                </tbody>
                                            </table>
                                        <?php } else {
                                            echo "<div class='col-12 rounded bg-white text-center text-danger'> [ 您沒有待簽核的文件! ] </div>";
                                        } ?>
                                    </div>
                                </div>
                                <!-- L2.廠區待領清單 -->
                                <div class="col-6 col-md-12 pt-0">
                                    <div class="rounded bg-light px-3 py-2 bsod">
                                        <div class="col-12 px-0 pb-0">
                                            <h5><i class="fa-solid fa-restroom"></i> 廠區待領清單：<sup>- collect </sup>
                                                <?php echo count($my_collect_lists) != 0 ? "<sup><span class='badge rounded-pill bg-warning text-dark'> +".count($my_collect_lists)."</sup></span>" :"" ;?>
                                            </h5>
                                        </div>
                                        <div class="col-12 px-0 pb-1 pt-0">
                                            <!-- <轄區清單> -->
                                            <div class="rounded bg-success text-white p-2">
                                                <span><b>轄區清單：</b>
                                                <button type="button" id="scope_remark_btn" class="op_tab_btn" value="scope_remark" onclick="op_tab(this.value)" title="訊息收折"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                                <div id="scope_remark">
                                                    <ol>
                                                        <?php foreach($coverFab_lists as $coverFab){
                                                            echo "<li>".$coverFab["id"].".".$coverFab["fab_title"]." (".$coverFab["fab_remark"].")</li>";
                                                        }?>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(count($my_collect_lists) >0){ ?>
                                            <table class="table">
                                                <thead>
                                                    <tr class="table-dark">
                                                        <th>開單日期</th>
                                                        <th>提貨廠區 / 申請單位 / 申請人</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($my_collect_lists as $my_collect){
                                                        echo "<tr><td title='aid:{$my_collect["id"]}'>".substr($my_collect["created_at"],0,10)."</td><td style='text-align: left; word-break: break-all;'>";
                                                            // <a href="show.php?uuid=<?php echo $my_collect['uuid'];>&action=collect" title="aid:<?php echo $my_collect['id'];>">
                                                                // <?php echo $my_collect['fab_title']." / ".$my_collect['dept']." / ".$my_collect["cname"];></a></td>
                                                        echo "<button type='button' value='show.php?uuid={$my_collect['uuid']}&action=collect' title='aid:{$my_collect["id"]}' onclick='openUrl(this.value)' class='tran_btn'>";
                                                        echo $my_collect['fab_title']." / ".$my_collect['dept']." / ".$my_collect["cname"]."</button>"."</td></tr>";
                                                    } ?>
                                                </tbody>
                                            </table>
                                        <?php } else {
                                            echo "<div class='col-12 rounded bg-white text-center text-danger'> [ 您沒有待發放的文件! ] </div>";
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 右邊清單 -->
                        <div class="col-12 col-md-8 px-3">
                            <!-- 20211215分頁工具 -->               
                            <div class="row">
                                <div class="col-12 col-md-6">	
                                    <?php //每頁顯示筆數明細
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
                                                $page_h = "<a href=?page=1";
                                                $page_u = "<a href=?page=".($page-1);
                                                    if(isset($fun)){
                                                        $page_h .= "&fun=".$fun;
                                                        $page_u .= "&fun=".$fun;		
                                                    }
                                                    if(isset($_year)){
                                                        $page_h .= "&_year=".$_year;
                                                        $page_u .= "&_year=".$_year;		
                                                    }
                                                    if(isset($_month)){
                                                        $page_h .= "&_month=".$_month;
                                                        $page_u .= "&_month=".$_month;		
                                                    }
                                                    if(isset($is_emp_id)){
                                                        $page_h .= "&emp_id=".$is_emp_id;
                                                        $page_u .= "&emp_id=".$is_emp_id;		
                                                    }
                                                    if(isset($is_fab_id)){
                                                        $page_h .= "&fab_id=".$is_fab_id;
                                                        $page_u .= "&fab_id=".$is_fab_id;		
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
                                                            if(isset($fun)){
                                                                $page_n .= "&fun=".$fun;
                                                            }
                                                            if(isset($_year)){
                                                                $page_n .= "&_year=".$_year;		
                                                            }
                                                            if(isset($_month)){
                                                                $page_n .= "&_month=".$_month;		
                                                            }
                                                            if(isset($is_emp_id)){
                                                                $page_n .= "&emp_id=".$is_emp_id;
                                                            }
                                                            if(isset($is_fab_id)){
                                                                $page_n .= "&fab_id=".$is_fab_id;
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
                                                    if(isset($fun)){
                                                        $page_d .= "&fun=".$fun;
                                                        $page_e .= "&fun=".$fun;		
                                                    }
                                                    if(isset($_year)){
                                                        $page_d .= "&_year=".$_year;
                                                        $page_e .= "&_year=".$_year;		
                                                    }
                                                    if(isset($_month)){
                                                        $page_d .= "&_month=".$_month;
                                                        $page_e .= "&_month=".$_month;		
                                                    }
                                                    if(isset($is_emp_id)){
                                                        $page_d .= "&emp_id=".$is_emp_id;
                                                        $page_e .= "&emp_id=".$is_emp_id;		
                                                    }
                                                    if(isset($is_fab_id)){
                                                        $page_d .= "&fab_id=".$is_fab_id;
                                                        $page_e .= "&fab_id=".$is_fab_id;		
                                                    }
                                                echo $page_d."> 下一頁</a> ";
                                                echo $page_e."> 末頁</a> ";		
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
                                                        case "1":   echo '一般';                                      break;
                                                        case "3":   echo '<span class="text-danger">緊急</span>';     break;
                                                        // default:    echo '錯誤';   break;
                                                    } ;?></td>
                                            <td><?php 
                                                    switch($row['idty']){
                                                        case "0"  : echo "<span class='badge rounded-pill bg-success'>待續</span>";                         break;
                                                        case "1"  : echo $form_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"簽核中";     break;
                                                        case "2"  : echo (in_array($auth_emp_id, [$row['emp_id'], $row['created_emp_id']])) 
                                                                            ? '<span class="badge rounded-pill bg-warning text-dark">退件</span>':"退件";    break;
                                                        case "3"  : echo "取消";                                                                             break;
                                                        case "4"  : echo "編輯";                                                                             break;
                                                        case "10" : echo "結案";                                                                             break;
                                                        case "11" : echo "環安主管";                                                                         break;
                                                        case "12" : echo (in_array($auth_emp_id, [$row['emp_id'], $row['created_emp_id']]) ||
                                                                        ($row['fab_id'] == $sys_fab_id) || in_array($row['fab_id'], $sys_sfab_id)) 
                                                                            ? '<span class="badge rounded-pill bg-success">待領</span>':"待領";              break;
                                                        case "13" : echo (in_array($auth_emp_id, $pm_emp_id_arr)) 
                                                                            ? '<span class="badge rounded-pill bg-danger">承辦簽核</span>':"承辦簽核";        break;
                                                        default   : echo $row['idty']."na";                                                                  break;
                                                    }; ?>
                                            </td>
                                            <td>
                                                <!-- Action功能欄 -->
                                                <?php if(in_array($row['idty'], [1 ]) && $form_role){ 
                                                    // 待簽：in_local對應人員
                                                    echo "<button type='button' value='show.php?uuid={$row["uuid"]}&action=sign' class='btn btn-sm btn-xs btn-primary' onclick='openUrl(this.value)'>簽核</button>";
                                                    // siteUser功能
                                                } else if((in_array($row['idty'], [13 ])) && (in_array($auth_emp_id, $pm_emp_id_arr) || $form_role
                                                        || ( ($row['local_id'] == $sys_fab_id) || (in_array($row['local_id'], [$sys_sfab_id])) ) )){ 
                                                    echo "<button type='button' value='show.php?uuid={$row["uuid"]}&action=sign' class='btn btn-sm btn-xs btn-primary' onclick='openUrl(this.value)'>簽核</button>";
                                                } else if((in_array($row['idty'], [2 ])) && ($row['emp_id'] == $auth_emp_id) ){
                                                    echo "<button type='button' value='show.php?uuid={$row["uuid"]}&action=sign' class='btn btn-sm btn-xs btn-warning' onclick='openUrl(this.value)'>待辦</button>";
                                                } else {
                                                    // siteUser功能
                                                    echo "<button type='button' value='show.php?uuid={$row["uuid"]}&action=review' class='btn btn-sm btn-xs btn-info' onclick='openUrl(this.value)'>檢視</button>";
                                                } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php if($per_total <= 0){
                                echo "<div class='col-12 border rounded bg-white text-center text-danger'> [ 查無 ".(isset($is_emp_id) ? "$is_emp_id":"")." 的篩選文件! ] </div>";
                            } ?>
                            <hr>
                            <!-- 20211215分頁工具 -->               
                            <div class="row">
                                <div class="col-12 col-md-6">	
                                    <?php //每頁顯示筆數明細
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
                                                $page_h = "<a href=?page=1";
                                                $page_u = "<a href=?page=".($page-1);
                                                    if(isset($fun)){
                                                        $page_h .= "&fun=".$fun;
                                                        $page_u .= "&fun=".$fun;		
                                                    }
                                                    if(isset($_year)){
                                                        $page_h .= "&_year=".$_year;
                                                        $page_u .= "&_year=".$_year;		
                                                    }
                                                    if(isset($_month)){
                                                        $page_h .= "&_month=".$_month;
                                                        $page_u .= "&_month=".$_month;		
                                                    }
                                                    if(isset($is_emp_id)){
                                                        $page_h .= "&emp_id=".$is_emp_id;
                                                        $page_u .= "&emp_id=".$is_emp_id;		
                                                    }
                                                    if(isset($is_fab_id)){
                                                        $page_h .= "&fab_id=".$is_fab_id;
                                                        $page_u .= "&fab_id=".$is_fab_id;		
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
                                                            if(isset($fun)){
                                                                $page_n .= "&fun=".$fun;
                                                            }
                                                            if(isset($_year)){
                                                                $page_n .= "&_year=".$_year;		
                                                            }
                                                            if(isset($_month)){
                                                                $page_n .= "&_month=".$_month;		
                                                            }
                                                            if(isset($is_emp_id)){
                                                                $page_n .= "&emp_id=".$is_emp_id;
                                                            }
                                                            if(isset($is_fab_id)){
                                                                $page_n .= "&fab_id=".$is_fab_id;
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
                                                    if(isset($fun)){
                                                        $page_d .= "&fun=".$fun;
                                                        $page_e .= "&fun=".$fun;		
                                                    }
                                                    if(isset($_year)){
                                                        $page_d .= "&_year=".$_year;
                                                        $page_e .= "&_year=".$_year;		
                                                    }
                                                    if(isset($_month)){
                                                        $page_d .= "&_month=".$_month;
                                                        $page_e .= "&_month=".$_month;		
                                                    }
                                                    if(isset($is_emp_id)){
                                                        $page_d .= "&emp_id=".$is_emp_id;
                                                        $page_e .= "&emp_id=".$is_emp_id;		
                                                    }
                                                    if(isset($is_fab_id)){
                                                        $page_d .= "&fab_id=".$is_fab_id;
                                                        $page_e .= "&fab_id=".$is_fab_id;		
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

    <!-- 模組 service window 20240319 -->
    <div class="modal fade" id="service_window" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header rounded bg-success text-white p-2 m-2">
                    <h5 class="modal-title"><i class="fa-solid fa-circle-info"></i> Service Window / 各廠聯絡窗口</h5>
                    <button type="button" class="btn-close border rounded mx-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-3 pt-1">
                    <div class="col-12 border rounded p-3">
                        <table id="service_window">
                            <thead>
                                <tr>
                                    <th>FAB</th>
                                    <th>窗口姓名</th>
                                    <th>分機</th>
                                    <th>email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($sw_arr as $sw_key => $sw_value){
                                    $value_length = count($sw_value);
                                    if($value_length < 1){
                                        $append_str = '<tr><td>'.$sw_key.'</td><td>null</td><td></td><td></td></tr>';
                                    }else{
                                        if(is_object($sw_value)) { $sw_value = (array)$sw_value; }                      // 物件轉陣列
                                        $td_key = '<td rowspan="'.$value_length.'">'.$sw_key.'</td>';
                                        $append_str = "";
                                        $i = 1;
                                        foreach($sw_value as $sw_item => $sw_item_value){
                                            if(is_object($sw_item_value)) { $sw_item_value = (array)$sw_item_value; }   // 物件轉陣列
                                            if(!empty($sw_item_value["cname"])){
                                                $td_value = $sw_item_value["cname"].'</td><td>'.$sw_item_value["tel_no"].'</td><td>'.strtolower($sw_item_value["email"]).'</td></tr>';
                                            }else{
                                                $td_value = '</td><td>'.'</td><td>'.'</td></tr>';
                                            }
                                            if($i === 1){
                                                $append_str .= '<tr>'.$td_key.'<td>'.$td_value;
                                            }else{
                                                $append_str .= '<tr><td>'.$td_value;
                                            }
                                            $i++;
                                        }
                                    };
                                    echo $append_str;
                                }?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<script src="../../libs/aos/aos.js"></script>                   <!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos_init.js"></script>              <!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/sweetalert/sweetalert.min.js"></script> <!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/openUrl/openUrl.js?v=<?=time();?>"></script>           <!-- 彈出子畫面 -->
<script>
    $(function () {
        // 在任何地方啟用工具提示框
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

    // 20231128_下載Excel
    function submitDownloadExcel() {
        // 先定義一個陣列(裝輸出資料使用)for 下載Excel
        let listData        = <?=json_encode($row_lists)?>;                   // 引入$row_lists資料
        // 定義要抓的key=>value
        let list_item_keys = {
            "id"             : "aid", 
            "created_at"     : "開單日期", 
            "plant"          : "申請單位", 
            "dept"           : "申請部門", 
            "sign_code"      : "部門代號",
            "cname"          : "領用人", 
            "emp_id"         : "工號", 
            "cata_SN_amount" : "需求清單", 
            "receive_remark" : "用途說明",
            "fab_title"      : "提貨廠區", 
            "fab_remark"     : "提貨廠區說明", 
            "local_title"    : "儲存點",
            "local_remark"   : "儲存點說明",
            "ppty"           : "類別\n0臨時1一般3緊急",
            "idty"           : "狀態\n10結案",
            "updated_at"     : "最後編輯"
        };
        let sort_listData = [];         // 建立陣列
        for(let i=0; i < listData.length; i++){
            sort_listData[i] = {};      // 建立物件
            Object.keys(list_item_keys).forEach(function(item_key){
                sort_listData[i][list_item_keys[item_key]] = listData[i][item_key];
            })
        }
        let htmlTableValue = JSON.stringify(sort_listData);
        document.getElementById('htmlTable').value = htmlTableValue;
    }

    $(document).ready(function () {
        op_tab('sign_remark');
        op_tab('scope_remark');
    })
</script>

<?php include("../template/footer.php"); ?>

