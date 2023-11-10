<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 1.決定開啟表單的功能：
        if(isset($_REQUEST["fun"]) && $_REQUEST["fun"] != "myReceive"){
            // $fun = $_REQUEST["fun"];                         // 有帶fun，套查詢參數
            $fun = "myFab";                                     // 有帶fun，直接套用 myFab = 3轄區申請單 (管理頁面)
        }else{
            $fun = "myReceive";                                 // 沒帶fun，預設套 myReceive = 2我的申請單 (預設頁面)
        }

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_id_role = $_SESSION[$sys_id]["role"];      // 取出$_session引用

    // 2-1.身分選擇功能：定義user進來要看到的項目
        // if($_SESSION[$sys_id]["role"] <= 1 ){                     
        //     if(isset($_REQUEST["emp_id"])){         
        //         $is_emp_id = $_REQUEST["emp_id"];               // pm、管理員 有帶emp_id，套查詢參數
        //     } else{
        //         $is_emp_id = "All";                             // pm、管理員 沒帶emp_id，套用 emp_id = All   => 看全部的申請單
        //     }
        // }else{
        //     $is_emp_id = $_SESSION["AUTH"]["emp_id"];           // 其他人、site_user含2以上 = 套自身emp_id    => 只看關於自己的申請單
        // }
        if(isset($_REQUEST["emp_id"])){         
            $is_emp_id = $_REQUEST["emp_id"];                   // 有帶emp_id，套查詢參數
        } else{
            if($sys_id_role <= 1 ){                     
                $is_emp_id = "All";                             // 沒帶emp_id，pm、管理員 套用 emp_id = All   => 看全部的申請單
            }else{
                $is_emp_id = $auth_emp_id;       // 沒帶emp_id，其他人、site_user含2以上 = 套自身emp_id    => 只看關於自己的申請單
            }
        }
        
    // 2-2.檢視廠區內表單
        if(isset($_REQUEST["fab_id"])){
            $is_fab_id = $_REQUEST["fab_id"];                   // 有帶查詢fab_id，套查詢參數   => 只看要查詢的單一廠
        }else{
            $is_fab_id = "allMy";                               // 其他預設值 = allMy   => 有關於我的轄區廠(fab_id + sfab_is)
        }

    // 3.組合查詢陣列
        $basic_query_arr = array(
            'sys_id'    => $sys_id,
            'role'      => $sys_id_role,
            'sign_code' => $_SESSION["AUTH"]["sign_code"],
            'fab_id'    => $is_fab_id,
            'emp_id'    => $auth_emp_id,
            'is_emp_id' => $is_emp_id
        );
            // $receives = show_receive_list($basic_query_arr);
            // $sum_receives = show_sum_receive($basic_query_arr);              // 左統計看板--上：表單核簽狀態
            // $sum_receives_ship = show_sum_receive_ship($basic_query_arr);    // 左統計看板--下：轉PR單
            //$basic_query_arr["emp_id"] = $auth_emp_id;

    // 4.篩選清單呈現方式，預設allMy
        // if($is_fab_id == "All" && $sys_id_role == 0){
        //     $myFab_lists = show_allFab_lists();                              // All
        //     echo "All";
        // }else 
        // if($_SESSION[$sys_id]["fab_id"] == 0 && $_SESSION["AUTH"]["role"] >= 3){                                          // 一般user、主管
        //     $myFab_lists = show_coverFab_lists($basic_query_arr);                       // myCover show_coverFab_lists用$sign_code模糊搜尋
        //     if(count($myFab_lists) > 0 && empty($_SESSION[$sys_id]["sfab_id"])){         // 當noBody登入，把她所屬的部門代號廠區套入sfab
        //         foreach($myFab_lists as $myFab){ 
        //             array_push($_SESSION[$sys_id]["sfab_id"], $myFab["id"]);
        //         }
        //         $_SESSION[$sys_id]["fab_id"] = NULL;
        //     }
        // }else{
        //     $myFab_lists = show_myFab_lists($basic_query_arr);                  // allMy
        // }
            $fab_id = $_SESSION[$sys_id]["fab_id"];                                 // 4-1.取fab_id
            if(!in_array($fab_id, $_SESSION[$sys_id]["sfab_id"])){                  // 4-1.當fab_id不在sfab_id，就把部門代號id套入sfab_id
                array_push($_SESSION[$sys_id]["sfab_id"], $fab_id);
            }
    
            $coverFab_lists = show_coverFab_lists($basic_query_arr);                // 4-2.呼叫fun 用$sign_code模糊搜尋
            if(!empty($coverFab_lists)){                                            // 4-2.當清單不是空值時且不在sfab_id，就把部門代號id套入sfab_id
                foreach($coverFab_lists as $coverFab){ 
                    if(!in_array($coverFab["id"], $_SESSION[$sys_id]["sfab_id"])){
                        array_push($_SESSION[$sys_id]["sfab_id"], $coverFab["id"]);
                    }
                }
            }

            $sfab_id = $_SESSION[$sys_id]["sfab_id"];                               // 4-3.取sfab_id
                // $sfab_id = array_filter($sfab_id);                                  // 4-3.去除空陣列 // 他會把0去掉
                $sfab_id = implode(",",$sfab_id);                                   // 4-3.sfab_id是陣列，要儲存前要轉成字串

            $basic_query_arr["sfab_id"] = $sfab_id;                                 // 4-4.將字串sfab_id加入組合查詢陣列中
            $basic_query_arr["fab_id"] = 'allMy';
            $coverFab_lists = show_myFab_lists($basic_query_arr);                   // allMy
            $basic_query_arr["fab_id"] = $is_fab_id;
            $myFab_lists = show_myFab_lists($basic_query_arr);                      // allMy

    // 5-L1.處理 $_1我待簽清單 
        $basic_query_arr["fun"] = "inSign" ;                                // 指定fun = inSign
        $my_inSign_lists = show_my_receive($basic_query_arr);
    // 5-L2.處理 $_5我的待領清單
        $basic_query_arr["fun"] = 'myCollect';
        $my_collect_lists = show_my_receive($basic_query_arr);
    // 5-2.處理 fun = myReceive $_2我的申請單
    // 5-3.處理 fun = myFab $_3轄區申請單： fab_id=allMy => emp_id=my ； fab_id = All or fab.id => emp_id = All or is_emp_id
        //  ** 有分頁的要擺在分頁工具前!!
        $basic_query_arr["fun"] = $fun ;                                    // 指定fun = $fun = myReceive / myFab
        $receive_lists = show_my_receive($basic_query_arr);

    // <!-- 20211215分頁工具 -->
        $per_total = count($receive_lists);     // 計算總筆數
        $per = 25;                              // 每頁筆數
        $pages = ceil($per_total/$per);         // 計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        if(!isset($_GET['page'])){              // !isset 判斷有沒有$_GET['page']這個變數
            $page = 1;	  
        }else{
            $page = $_GET['page'];
        }
        $start = ($page-1)*$per;                // 每一頁開始的資料序號(資料庫序號是從0開始)
        // 合併嵌入分頁工具
            $basic_query_arr['start'] = $start;
            $basic_query_arr['per'] = $per;

        $receive_lists = show_my_receive($basic_query_arr);
        $page_start = $start +1;                // 選取頁的起始筆數
        $page_end = $start + $per;              // 選取頁的最後筆數
        if($page_end>$per_total){               // 最後頁的最後筆數=總筆數
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
                        <?php if(isset($_SESSION[$sys_id])){ ?>
                            <a href="form.php?action=create" class="btn btn-primary"><i class="fa fa-edit" aria-hidden="true"></i> 填寫領用申請</a>
                            <?php if($sys_id_role <= 2){ ?>
                                <!-- <a href="show_receiveAmount.php" title="管理員限定" class="btn btn-warning"><i class="fa-brands fa-stack-overflow"></i> 待轉PR總表</a> -->
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <!-- table -->
                <div class="row">
                    <!-- L左邊 -->
                    <div class="col-12 col-md-4 px-2 pt-0">
                        <!-- L1.我的待簽清單 -->
                        <div class="border rounded px-3 py-2" style="background-color: #D4D4D4;">
                            <div class="col-12 px-0 pb-0">
                                <h5><i class="fa-brands fa-stack-overflow"></i> 我的待簽清單：<sup>- inSign </sup>
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
                                            <th>提貨廠區/申請單位/申請人</th>
                                            <th>表單狀態</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($my_inSign_lists as $my_inSign){ ?>
                                            <tr>
                                                <td title="<?php echo $my_inSign['id'];?>"><?php echo substr($my_inSign['created_at'],0,10)." <sup>".$my_inSign['id']."</sup>";?></td>
                                                <td style="text-align: left; word-break: break-all;"><a href="show.php?uuid=<?php echo $my_inSign['uuid'];?>&action=sign" title="aid:<?php echo $my_inSign['id'];?>">
                                                    <?php echo $my_inSign['fab_title']." / ".$my_inSign['dept']." / ".$my_inSign["cname"];?></a></td>
                                                <td><?php $sys_role = (($my_inSign['in_sign'] == $auth_emp_id) || ($sys_id_role <= 1));
                                                    switch($my_inSign['idty']){     // 處理 $_2我待簽清單  idty = 1申請送出、11發貨後送出、13發貨
                                                        case "1"    : echo '<span class="badge rounded-pill bg-danger">待簽</span>';        break;
                                                        case "2"    : echo "退件";                  break;
                                                        case "10"   : echo "結案";                  break;
                                                        case "11"   : echo '<span class="badge rounded-pill bg-warning text-dark">待結</span>';        break;
                                                        case "13"   : echo '<span class="badge rounded-pill bg-warning text-dark">待結</span>';        break;
                                                        case "12"   : echo (in_array($auth_emp_id, [$my_inSign['emp_id'], $my_inSign['created_emp_id']]) ||
                                                        ($my_inSign['fab_id'] == $_SESSION[$sys_id]['fab_id']) || in_array($my_inSign['fab_id'], $_SESSION[$sys_id]['sfab_id'])) 
                                                                                ? '<span class="badge rounded-pill bg-warning text-dark">待領</span>':"待領";      break;
                                                        case "13"   : echo "交貨";                  break;
                                                        default     : echo $my_inSign['idty']."na";   break;
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

                        <!-- L2.待領清單 -->
                        <div class="border rounded px-3 py-2 my-2" style="background-color: #D4D4D4;">
                            <div class="col-12 px-0 pb-0">
                                <h5><i class="fa-solid fa-restroom"></i> 廠區待領清單：<sup>- collect </sup>
                                    <?php echo count($my_collect_lists) != 0 ? "<sup><span class='badge rounded-pill bg-warning text-dark'> +".count($my_collect_lists)."</sup></span>" :"" ;?>
                                </h5>
                            </div>
                            <div class="col-12 px-0 pt-0 pb-1">
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
                                        <?php foreach($my_collect_lists as $my_collect){ ?>
                                            <tr>
                                                <td title="<?php echo $my_collect['id'];?>"><?php echo substr($my_collect['created_at'],0,10)." <sup>".$my_collect['id']."</sup>";?></td>
                                                <td style="text-align: left; word-break: break-all;"><a href="show.php?uuid=<?php echo $my_collect['uuid'];?>&action=collect" title="aid:<?php echo $my_collect['id'];?>">
                                                    <?php echo $my_collect['fab_title']." / ".$my_collect['dept']." / ".$my_collect["cname"];?></a></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php }else{ ?>
                                <div class="col-12 rounded bg-white text-center text-danger"> [ 您沒有待發放的文件! ] </div>
                            <?php } ?>
                        </div>

                    </div>

                    <!-- 右邊清單 -->
                    <div class="col-12 col-md-8 px-2 pt-0">
                        <!-- 內頁 -->
                        <div class="border rounded px-3 py-2 bg-light">
                            <div class="row">
                                <?php if($fun != 'myFab'){ ?>
                                    <!-- 功能1 -->
                                    <div class="col-12 col-md-12 pb-0">
                                        <?php if($sys_id_role <= 2){ ?>
                                            <a href="?fun=myFab" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="切換到-轄區文件"><i class="fa-solid fa-right-left"></i></a>
                                        <?php } ?>
                                        <h5 style="display: inline;">我的申請文件：<sup>- myReceives </sup></h5>
                                    </div>
                                <?php } else { ?>
                                    <!-- 功能2 -->
                                    <div class="col-12 col-md-5 pb-0">
                                        <a href="?fun=myReceive" class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="切換到-申請文件"><i class="fa-solid fa-right-left"></i></a>
                                        <h5 style="display: inline;">我的轄區文件：<sup>- myFab's Receive </sup></h5>
                                    </div>
                                    <!-- 篩選功能?? -->
                                    <div class="col-12 col-md-7 pb-0">
                                        <?php if($sys_id_role <= 2 ){ ?>
                                            <form action="" method="get">
                                                <div class="input-group">
                                                    <select name="fab_id" id="sort_fab_id" class="form-select" >$myFab_lists
                                                        <option for="sort_fab_id" value="All" <?php echo $is_fab_id == "All" ? "selected":""; echo $sys_id_role >= 2 ? "hidden":""; ?>>-- [ All fab ] --</option>
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
                                                $page_h = "<a href=?page=1";
                                                $page_u = "<a href=?page=".($page-1);
                                                    if(isset($fun)){
                                                        $page_h .= "&fun=".$fun;
                                                        $page_u .= "&fun=".$fun;		
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
                            <table class=" ">
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
                                <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                <tbody>
                                    <?php foreach($receive_lists as $receive){ ?>
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
                                            <td><?php $sys_role = (($receive['in_sign'] == $auth_emp_id) || ($sys_id_role <= 1));
                                                        switch($receive['idty']){
                                                            case "0"    : echo "<span class='badge rounded-pill bg-success'>待續</span>";                           break;
                                                            case "1"    : echo $sys_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"簽核中";        break;
                                                            case "2"    : echo "退件";                  break;
                                                            case "3"    : echo "取消";                  break;
                                                            case "4"    : echo "編輯";                  break;
                                                            case "10"   : echo "結案";                  break;
                                                            case "11"   : echo "業務承辦";              break;
                                                            case "12"   : echo (in_array($auth_emp_id, [$receive['emp_id'], $receive['created_emp_id']]) ||
                                                            ($receive['fab_id'] == $_SESSION[$sys_id]['fab_id']) || in_array($receive['fab_id'], $_SESSION[$sys_id]['sfab_id'])) 
                                                                                 ? '<span class="badge rounded-pill bg-warning text-dark">待領</span>':"待領";      break;
                                                            case "13"   : echo "交貨";                  break;
                                                            default     : echo $receive['idty']."na";   break;
                                                        }; ?>
                                            </td>
                                            <td>
                                                <!-- Action功能欄 -->
                                                <?php if((($receive['local_id'] == $_SESSION[$sys_id]['fab_id']) || (in_array($receive['local_id'], $_SESSION[$sys_id]["sfab_id"]))) 
                                                        && ($receive['idty'] == '1') && $sys_role){ ?> 
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
                                                $page_h = "<a href=?page=1";
                                                $page_u = "<a href=?page=".($page-1);
                                                    if(isset($fun)){
                                                        $page_h .= "&fun=".$fun;
                                                        $page_u .= "&fun=".$fun;		
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
                        <!-- DEBUG -->
                        <div class="rounded px-3 py-2 my-2 bg-secondary text-white text-start">
                            <pre>
                                <?php
                                    echo "basic_query_arr: ";
                                    print_r($basic_query_arr);
                                    echo "<hr>";
                                    
                                    echo "myFab_lists: ";
                                    print_r($myFab_lists);
                                    echo count($myFab_lists)." 件";
                                    echo "<hr>";

                                    echo "myFab_lists--sfab_id 加工: ";
                                    $sfab_id = $_SESSION[$sys_id]["sfab_id"];
                                    print_r($sfab_id);
                                    echo count($sfab_id)." 件";
                                    echo "<hr>";

                                    $my_signCoed = $_SESSION["AUTH"]["sign_code"];
                                    echo "我的部門代號：".$my_signCoed;
                                    echo "<hr>";

                                    $cover_signCoed = substr($my_signCoed, 0, -2);
                                    echo "我的涵蓋：".$cover_signCoed;
                                    if(preg_match('/'.$cover_signCoed.'/i' ,$my_signCoed)){
                                        echo "</br>居然一樣!!";
                                    }else{
                                        echo "</br>可想而知~~~不一樣!!";
                                    }
                                    echo "<hr>";

                                    $sfab_id = $_SESSION[$sys_id]["sfab_id"];
                                    $sfab_id = implode(",",$sfab_id);       //副pm是陣列，要儲存前要轉成字串
                                    echo $sfab_id." 串";

                                    // php base64如何進行URL字串編碼和解碼？
                                        // $string = "?page=3&fun=myFab&emp_id=All&fab_id=allMy";
                                        // echo "string: ".$string;
                                        // echo "</br>";
                                        // // 編碼
                                        // $data_en = base64_encode($string);
                                        // $data_en = str_replace(array('+','/','='),array('-','_',''),$data_en);
                                        // echo "encode: ".$data_en;
                                        // echo "</br>";
                                        // // 解碼
                                        // $data_de = str_replace(array('-','_'),array('+','/'),$data_en);
                                        // $mod4 = strlen($data_de) % 4;
                                        // if ($mod4) {
                                        //     $data_de .= substr('====', $mod4);
                                        // }
                                        // echo "decode: ". base64_decode($data_de);
                                ?>
                            </pre>
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
        $("#"+tab_value+"_btn .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
        var tab_table = document.getElementById(tab_value);
        if (tab_table && (tab_table.style.display === "none")) {
            tab_table.style.display = "table";
        } else {
            tab_table.style.display = "none";
        }
    }

    $(document).ready(function () {
        op_tab('sign_remark');
    })
</script>

<?php include("../template/footer.php"); ?>