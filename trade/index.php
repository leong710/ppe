<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用
    $sys_fab_id  = $_SESSION[$sys_id]["fab_id"];     
    $sys_sfab_id = $_SESSION[$sys_id]["sfab_id"];  
    $form_type   = "trade";
    
        // 身分選擇功能：定義user進來要看到的項目
        $is_emp_id = "All";    // 預設值=All
        $is_fab_id = "All";    // 預設值=All
        
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
        // *** 篩選組合項目~~
        if(isset($_REQUEST["_year"])){
            $_year = $_REQUEST["_year"];
        }else{
            // $_year = date('Y');                         // 今年
            $_year = "All";                                // 全年
        }
    // 組合查詢陣列
        $query_arr = array(
            '_year'     => $_year,
            'sys_id'    => $sys_id,
            'role'      => $sys_role,
            'sign_code' => $_SESSION["AUTH"]["sign_code"],
            'fab_id'    => $is_fab_id,
            'emp_id'    => $auth_emp_id,
            'is_emp_id' => $is_emp_id,
            'fun'       => "myReceive"
        );

        $row_lists    = show_trade_list($query_arr);
        $trade_years  = show_trade_GB_year();               // 取出trade年份清單 => 供首頁面篩選
        $my_inSign_lists = [];

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

        $row_lists_div = show_trade_list($query_arr);
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
            <div class="col_xl_12 col-12 p-3 pb-5 rounded" style="background-color: rgba(200, 255, 100, .6);" >
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
                            <a class="nav-link" href="../issue/" aria-current="page" data-toggle="tooltip" data-placement="bottom" title="issue">
                                <i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b><span id="nav_bob_1"></span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../trade/" aria-current="page" data-toggle="tooltip" data-placement="bottom" title="trade">
                                <i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b><span id="nav_bob_2"></span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../receive/" aria-current="page" data-toggle="tooltip" data-placement="bottom" title="receive">
                                <i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b><span id="nav_bob_3"></span></a>
                        </li>
                    </ul>
                </div>

                <!-- 內頁 -->
                <!-- 2.出入作業總表 -->
                <div class="col-12 bg-white">
                    <!-- tab head -->
                    <div class="row">
                        <div class="col-12 col-md-3 py-1">

                        </div>
                        <div class="col-6 col-md-6 py-1">
                            <form action="" method="POST">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-search"></i>&nbsp篩選</span>
                                    <select name="_year" class="form-select">
                                        <option value="All" <?php if($_year == "All"){ ?>selected<?php } ?> >-- 年度 / All --</option>
                                        <?php foreach($trade_years as $trade_year){ ?>
                                            <option value="<?php echo $trade_year["_year"];?>" <?php if($trade_year["_year"] == $_year){ ?>selected<?php } ?>>
                                                <?php echo $trade_year["_year"]."y";?></option>
                                        <?php } ?>
                                    </select>
                                    <select name="emp_id" id="sort_emp_id" class="form-select">
                                        <option value="All" <?php echo $is_emp_id == "All" ? "selected":"";?>>-- [ All user ] --</option>
                                        <option value="<?php echo $auth_emp_id;?>" <?php echo $is_emp_id == $auth_emp_id ? "selected":"";?>>
                                            <?php echo $auth_emp_id."_".$auth_cname;?></option>
                                    </select>
                                    <button type="submit" class="btn btn-outline-secondary">查詢</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-6 col-md-3 py-1 text-end">
                            <?php if($sys_role <= 2){ ?>
                                <a href="form.php?action=create" class="btn btn-primary"><i class="fa-solid fa-upload" aria-hidden="true"></i> 調撥出庫</a>
                            <?php } ?>
                            <?php if($sys_role <= 1){ ?>
                                <a href="restock.php?action=create" class="btn btn-success" ><i class="fa-solid fa-download"></i> 其他入庫</a>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- tab body -->
                    <div class="row">

                        <!-- 右邊清單 -->
                        <div class="col-12 px-3">
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
                            <table id="trade_lists" class="table table-hover">
                                <thead>
                                    <tr class="table-danger text-danger">
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
                                <tbody>
                                    <?php foreach($row_lists_div as $row){ ?>
                                        <tr>
                                            <!-- <td style="font-size: 10px;"><php echo $row['id']; ?></td> -->
                                            <td title="aid: <?php echo $row['id'];?>"><?php echo substr($row['out_date'],0,10); ?></td>
                                            <td style="font-size: 14px; word-break: break-all;">
                                                <?php if(!empty($row["fab_o_title"])){ echo $row['fab_o_title'].'('.$row['fab_o_remark'].')';
                                                    }else{
                                                        echo "<b>".($row["out_local"])."</b>";
                                                    }?>
                                            </td>
                                            <td><?php echo $row['cname_o'];?></td>
                                            <td class="t-left"><?php echo $row["fab_i_title"].'('.$row['fab_i_remark'].')'.'_'.$row["local_i_title"];?></td>
                                            <td><?php echo $row['cname_i'];?></td>
                                            <td style="font-size: 10px;"><?php echo substr($row['in_date'],0,10); ?></td>
                                            <td><?php $fab_role = ($row['fab_i_id'] == $sys_fab_id || (in_array($row['fab_i_id'], $sys_sfab_id)));
                                                switch($row['idty']){
                                                    case "0"    : echo "完成";                  break;
                                                    case "1"    : echo $fab_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"待簽"; break;
                                                    case "2"    : echo "退件";                  break;
                                                    case "3"    : echo "取消";                  break;
                                                    case "4"    : echo "編輯";                  break;
                                                    // case "10"   : echo "pr進貨";                break;
                                                    case "10"   : echo "結案";                  break;
                                                    default     : echo $row['idty']."na";       break;
                                                }?></td>
                                            <td>
                                                <!-- Action功能欄 -->
                                                <?php if((($row['fab_i_id'] == $sys_fab_id) || (in_array($row['fab_i_id'], $sys_sfab_id))) 
                                                        && ($row['idty'] == '1')){ ?> 
                                                    <!-- 待簽：in_local對應人員 -->
                                                    <a href="show.php?id=<?php echo $row['id'];?>&action=acceptance" class="btn btn-sm btn-xs btn-success">驗收</a>
                                                <?php }else if((($row['fab_o_id'] == $sys_fab_id) || (in_array($row['fab_i_id'], $sys_sfab_id)))
                                                        && ($row['idty'] == '2')){ ?>
                                                    <!-- 待簽：out_local對應人員 -->
                                                    <a href="show.php?id=<?php echo $row['id'];?>&action=review" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                                <?php }else if((($row['fab_o_id'] == $sys_fab_id) || (in_array($row['fab_o_id'], $sys_sfab_id)))
                                                        && ($row['idty'] == '4')){ ?>
                                                    <!-- 待簽：out_local對應人員 -->
                                                    <a href="form.php?id=<?php echo $row['id'];?>&action=edit" class="btn btn-sm btn-xs btn-success">編輯</a>
                                                <?php }else{ ?>
                                                    <!-- siteUser功能 -->
                                                    <a href="show.php?id=<?php echo $row['id'];?>" class="btn btn-sm btn-xs btn-info">檢視</a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php if($per_total <= 0){ ?>
                                <div class="col-12 border rounded bg-white text-center text-danger"> [ 查無篩選 <?php echo $_year." 或 ".$is_emp_id;?> 的文件! ] </div>
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
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

</script>

<?php include("../template/footer.php"); ?>

