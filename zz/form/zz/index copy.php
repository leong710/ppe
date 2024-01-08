<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用
    $sys_fab_id  = $_SESSION[$sys_id]["fab_id"];     
    $sys_sfab_id = $_SESSION[$sys_id]["sfab_id"];  
    
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

        $issue_lists    = show_issue_list($query_arr);
        $trade_lists    = show_trade_list($query_arr);
        $receive_lists  = show_receive_list($query_arr);

        $issue_lists_count    = count($issue_lists);
        $trade_lists_count    = count($trade_lists);
        $receive_lists_count  = count($receive_lists);
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
            <div class="col_xl_12 col-12 p-3 rounded" style="background-color: rgba(255, 255, 255, .6);" >
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-md-6 py-1 page_title">
                        <h3><b>Form表單匯總：</b></h3>
                    </div>
                    <div class="col-md-6 py-1">
     
                    </div>
                </div>
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder" class="col-11 mb-0 p-0"></div>

                <!-- NAV 分頁標籤 -->
                <div class="row pb-0">
                    <form method="post">
                        <nav>
                            <div class="nav nav-tabs pt-2 pb-0" id="nav-tab" role="tablist">
                                <button type="button" class="nav-link active" id="nav-tab_1" data-bs-toggle="tab" data-bs-target="#tab_1" role="tab" aria-controls="tab_1" aria-selected="true" >
                                    <i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b><span id="nav_bob_1"></span></button>

                                <button type="button" class="nav-link       " id="nav-tab_2" data-bs-toggle="tab" data-bs-target="#tab_2" role="tab" aria-controls="tab_2"                      >
                                    <i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b><span id="nav_bob_2"></span></button>

                                <button type="button" class="nav-link       " id="nav-tab_3" data-bs-toggle="tab" data-bs-target="#tab_3" role="tab" aria-controls="tab_3"                      >
                                    <i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b><span id="nav_bob_3"></span></button>

                            </div>
                        </nav>
                    </form>
                </div>

                <!-- 內頁 -->
                <div class="tab-content" id="nav-tabContent">
                    <!-- 1.請購需求總表 -->
                    <div class="tab-pane bg-white fade p-2 show active" id="tab_1" role="tabpanel" aria-labelledby="nav-tab_1">
                        <div class="col-12 bg-white">
                            <!-- tab head -->
                            <div class="row">
                                <div class="col-12 col-md-6 pt-0">
                                    <i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b>
                                </div>
                                <div class="col-12 col-md-6 pt-0 text-end">
                                    <?php if($sys_role <= 2){ ?>
                                        <a href="../issue/form.php?action=create" class="btn btn-primary"><i class="fa fa-edit" aria-hidden="true"></i> 填寫請購需求</a>
                                    <?php } ?>
                                    <?php if($sys_role <= 1){ ?>
                                        <a href="../issue/show_issueAmount.php" title="管理員限定" class="btn btn-warning"><i class="fa-brands fa-stack-overflow"></i> 待轉PR總表</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if($issue_lists_count > 0){ ?>
                                <table id="issue_lists" class="table table-hover">
                                    <thead>
                                        <tr class="table-primary text-danger">
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
                                    <tbody>
                                        <?php foreach($issue_lists as $issue){ ?>
                                            <tr>
                                                <td title="aid: <?php echo $issue['id'];?>"><?php echo substr($issue['create_date'],0,10); ?></td>
                                                <td><?php echo $issue['fab_i_title'].'('.$issue['fab_i_remark'].')';?></td>
                                                <td><?php echo $issue['cname_i'];?></td>
                                                <td style="font-size: 12px; word-break: break-all;">
                                                    <?php echo (!empty($issue["fab_o_title"])) ? $issue["fab_o_title"].'('.$issue['fab_o_remark'].')' : $issue["out_local"];?>
                                                </td>
                                                <td><?php echo $issue['cname_o'];?></td>
                                                <td style="font-size: 6px;"><?php echo substr($issue['in_date'],0,10); ?></td>
                                                <td><?php echo $issue['ppty'];
                                                        switch($issue['ppty']){
                                                            case "0": echo '.臨時'; break;
                                                            case "1": echo '.定期'; break;
                                                            case "3": echo '.緊急'; break;
                                                            default:  echo '錯誤' ; break;
                                                        };?></td>
                                                <td><?php $sys_role = ($sys_role <= 1) || (($issue['fab_i_id'] == $sys_fab_id) || (in_array($issue['fab_i_id'], $sys_sfab_id)));
                                                    switch($issue['idty']){
                                                        case "0"    : echo $sys_role ? '<span class="badge rounded-pill bg-warning text-dark">待轉</span>':"待轉";  break;
                                                        case "1"    : echo $sys_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"待簽";             break;
                                                        case "2"    : echo "退件";                  break;
                                                        case "3"    : echo "取消";                  break;
                                                        case "4"    : echo "編輯";                  break;
                                                        case "10"   : echo "結案";                  break;
                                                        case "11"   : echo "轉PR";                  break;
                                                        case "13"   : echo "<span class='badge rounded-pill bg-success'>待收</span>";                  break;
                                                        default     : echo $issue['idty']."na";     break;
                                                    }?>
                                                </td>
                                                <td style="font-size: 12px; word-break: break-all;"><?php echo $issue['_ship'];?>
                                                    <?php if($issue['_ship'] == '0'){?>結案<?php ;}?>
                                                    <?php if($issue['_ship'] == '1'){?>轉PR<?php ;}?>
                                                </td>
                                                <td>
                                                    <!-- Action功能欄 -->
                                                    <?php if(($issue['idty'] == '1') && ($sys_role <= 1)){ // 1待簽 ?>        
                                                        <!-- 待簽：PM+管理員功能 -->
                                                        <a href="../issue/show.php?id=<?php echo $issue['id'];?>&action=sign" class="btn btn-sm btn-xs btn-primary">簽核</a>
                                                        <!-- siteUser功能 -->
                                                    <?php } else if(($issue['idty'] == '11') && ($sys_role <= 1)){ ?>
                                                        <a href="../issue/show.php?id=<?php echo $issue['id'];?>&action=sign" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                                        <!-- siteUser功能 -->
                                                    <?php } else if((in_array($issue['idty'], [2, 13])) && (($issue['fab_i_id'] == $sys_fab_id) || (in_array($issue['fab_i_id'], $sys_sfab_id))) ){ ?>
                                                        <a href="../issue/show.php?id=<?php echo $issue['id'];?>&action=sign" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                                    <?php } else { ?>
                                                        <!-- siteUser功能 -->
                                                        <a href="../issue/show.php?id=<?php echo $issue['id'];?>&action=review" class="btn btn-sm btn-xs btn-info">檢視</a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php }else{ ?>
                                <div class="col-12 border rounded bg-white text-center text-danger"> [ 您沒有待簽核的文件! ] </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- 2.出入作業總表 -->
                    <div class="tab-pane bg-white fade p-2 " id="tab_2" role="tabpanel" aria-labelledby="nav-tab_2">
                        <div class="col-12 bg-white">
                            <!-- tab head -->
                            <div class="row">
                                <div class="col-12 col-md-6 pt-0">
                                    <i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b>
                                </div>
                                <div class="col-12 col-md-6 pt-0 text-end">
                                    <?php if($sys_role <= 2){ ?>
                                        <a href="../trade/form.php?action=create" class="btn btn-primary"><i class="fa-solid fa-upload" aria-hidden="true"></i> 調撥出庫</a>
                                    <?php } ?>
                                    <?php if($sys_role <= 1){ ?>
                                        <a href="../trade/restock.php?action=create" class="btn btn-success" ><i class="fa-solid fa-download"></i> 其他入庫</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if($trade_lists_count > 0){ ?>
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
                                        <?php foreach($trade_lists as $trade){ ?>
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
                                                <td><?php $fab_role = ($trade['fab_i_id'] == $sys_fab_id || (in_array($trade['fab_i_id'], $sys_sfab_id)));
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
                                                    <?php if((($trade['fab_i_id'] == $sys_fab_id) || (in_array($trade['fab_i_id'], $sys_sfab_id))) 
                                                            && ($trade['idty'] == '1')){ ?> 
                                                        <!-- 待簽：in_local對應人員 -->
                                                        <a href="../trade/show.php?id=<?php echo $trade['id'];?>&action=acceptance" class="btn btn-sm btn-xs btn-success">驗收</a>
                                                    <?php }else if((($trade['fab_o_id'] == $sys_fab_id) || (in_array($trade['fab_i_id'], $sys_sfab_id)))
                                                            && ($trade['idty'] == '2')){ ?>
                                                        <!-- 待簽：out_local對應人員 -->
                                                        <a href="../trade/show.php?id=<?php echo $trade['id'];?>&action=review" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                                    <?php }else if((($trade['fab_o_id'] == $sys_fab_id) || (in_array($trade['fab_o_id'], $sys_sfab_id)))
                                                            && ($trade['idty'] == '4')){ ?>
                                                        <!-- 待簽：out_local對應人員 -->
                                                        <a href="../trade/form.php?id=<?php echo $trade['id'];?>&action=edit" class="btn btn-sm btn-xs btn-success">編輯</a>
                                                    <?php }else{ ?>
                                                        <!-- siteUser功能 -->
                                                        <a href="../trade/show.php?id=<?php echo $trade['id'];?>" class="btn btn-sm btn-xs btn-info">檢視</a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php }else{ ?>
                                <div class="col-12 border rounded bg-white text-center text-danger"> [ 您沒有待簽核的文件! ] </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- 3.領用申請總表 -->
                    <div class="tab-pane bg-white fade p-2 " id="tab_3" role="tabpanel" aria-labelledby="nav-tab_3">
                        <div class="col-12 bg-white">
                            <!-- tab head -->
                            <div class="row">
                                <div class="col-12 col-md-6 pt-0">
                                    <i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b>
                                </div>
                                <div class="col-12 col-md-6 pt-0 text-end">
                                    <?php if(isset($sys_role)){ ?>
                                        <a href="../receive/form.php?action=create" class="btn btn-primary"><i class="fa fa-edit" aria-hidden="true"></i> 填寫領用申請</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if($receive_lists_count > 0){ ?>
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
                                        <?php foreach($receive_lists as $receive){ 
                                            $pm_emp_id = $receive["pm_emp_id"];                                         // *** 廠區業務窗口
                                            $pm_emp_id_arr = explode(",",$pm_emp_id);                                   //資料表是字串，要炸成陣列
                                            $sign_sys_role = (($receive['in_sign'] === $auth_emp_id) || ($sys_role <= 1));
                                            ?>
                                            <tr>
                                                <td title="aid:<?php echo $receive['id'];?>"><?php echo substr($receive['created_at'],0,10);?></td>
                                                <td><?php echo $receive['fab_title'].' ('.$receive['fab_remark'].')';?></td>
                                                <td class="word_bk"><?php echo $receive["plant"]." / ".$receive["dept"];?></td>
                                                <td><?php echo $receive["cname"]; echo $receive["emp_id"] ? " (".$receive["emp_id"].")":"";?></td>
                                                <td><?php echo substr($receive["updated_at"],0,10); ?></td>
                                                <td><?php 
                                                        switch($receive['ppty']){
                                                            case "0":   echo '<span class="text-primary">臨時</span>';    break;
                                                            case "1":   echo '定期';                                      break;
                                                            case "3":   echo '<span class="text-danger">緊急</span>';     break;
                                                            // default:    echo '錯誤';   break;
                                                        } ;?></td>
                                                <td><?php 
                                                        switch($receive['idty']){
                                                            case "0"    : echo "<span class='badge rounded-pill bg-success'>待續</span>";                                break;
                                                            case "1"    : echo $sign_sys_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"簽核中";        break;
                                                            case "2"    : echo (in_array($auth_emp_id, [$receive['emp_id'], $receive['created_emp_id']])) 
                                                                                        ? '<span class="badge rounded-pill bg-warning text-dark">退件</span>':"退件";                  break;
                                                            case "3"    : echo "取消";                  break;
                                                            case "4"    : echo "編輯";                  break;
                                                            case "10"   : echo "結案";                  break;
                                                            case "11"   : echo "環安主管";              break;
                                                            case "12"   : echo (in_array($auth_emp_id, [$receive['emp_id'], $receive['created_emp_id']]) ||
                                                                                    ($receive['fab_id'] == $sys_fab_id) || in_array($receive['fab_id'], $sys_sfab_id)) 
                                                                                        ? '<span class="badge rounded-pill bg-warning text-dark">待領</span>':"待領";      break;
                                                            case "13"   : echo (in_array($auth_emp_id, $pm_emp_id_arr)) 
                                                                                        ? '<span class="badge rounded-pill bg-danger">承辦簽核</span>': "承辦簽核";               break;
                                                            default     : echo $receive['idty']."na";   break;
                                                        }; ?>
                                                </td>
                                                <td>
                                                    <!-- Action功能欄 -->
                                                    <?php if(in_array($receive['idty'], [1 ]) && $sign_sys_role){ ?> 
                                                        <!-- 待簽：in_local對應人員 -->
                                                        <a href="../receive/show.php?uuid=<?php echo $receive['uuid'];?>&action=sign" class="btn btn-sm btn-xs btn-primary">簽核</a>
                                                    <!-- siteUser功能 -->
                                                    <?php } else if((in_array($receive['idty'], [13 ])) && (in_array($auth_emp_id, $pm_emp_id_arr) || $sign_sys_role 
                                                            || ( ($receive['local_id'] == $sys_fab_id) || (in_array($receive['local_id'], [$sys_sfab_id])) )
                                                        )){ ?>
                                                        <a href="../receive/show.php?uuid=<?php echo $receive['uuid'];?>&action=sign" class="btn btn-sm btn-xs btn-primary">簽核</a>

                                                    <?php } else if((in_array($receive['idty'], [2 ])) && ($receive['emp_id'] == $auth_emp_id) ){ ?>

                                                        <a href="../receive/show.php?uuid=<?php echo $receive['uuid'];?>&action=sign" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                                    <?php } else { ?>
                                                        <!-- siteUser功能 -->
                                                        <a href="../receive/show.php?uuid=<?php echo $receive['uuid'];?>&action=review" class="btn btn-sm btn-xs btn-info">檢視</a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php }else{ ?>
                                <div class="col-12 border rounded bg-white text-center text-danger"> [ 您沒有待簽核的文件! ] </div>
                            <?php } ?>
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


</script>

<?php include("../template/footer.php"); ?>

