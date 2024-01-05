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

        $row_lists    = show_trade_list($query_arr);

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
            <div class="col_xl_12 col-12 p-3 rounded" style="background-color: rgba(200, 255, 100, .6);" >
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
                <div class="col-12 p-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link" href="issue.php" aria-current="page" data-toggle="tooltip" data-placement="bottom" title="bar">
                                <i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b><span id="nav_bob_1"></span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="trade.php" aria-current="page" data-toggle="tooltip" data-placement="bottom" title="bar">
                                <i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b><span id="nav_bob_2"></span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php" aria-current="page" data-toggle="tooltip" data-placement="bottom" title="stack">
                                <i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b><span id="nav_bob_3"></span></a>
                        </li>
                    </ul>
                </div>

                <!-- 內頁 -->
                <!-- 2.出入作業總表 -->
                <div class="col-12 bg-white">
                    <!-- tab head -->
                    <div class="row">
                        <div class="col-12 col-md-6 py-1">
                            <i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b>
                        </div>
                        <div class="col-12 col-md-6 py-1 text-end">
                            <?php if($sys_role <= 2){ ?>
                                <a href="../<?php echo $form_type;?>/form.php?action=create" class="btn btn-primary"><i class="fa-solid fa-upload" aria-hidden="true"></i> 調撥出庫</a>
                            <?php } ?>
                            <?php if($sys_role <= 1){ ?>
                                <a href="../<?php echo $form_type;?>/restock.php?action=create" class="btn btn-success" ><i class="fa-solid fa-download"></i> 其他入庫</a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if($per_total > 0){ ?>
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
                                        <!-- <td style="font-size: 6px;"><php echo $row['id']; ?></td> -->
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
                                        <td style="font-size: 6px;"><?php echo substr($row['in_date'],0,10); ?></td>
                                        <td><?php $fab_role = ($row['fab_i_id'] == $sys_fab_id || (in_array($row['fab_i_id'], $sys_sfab_id)));
                                            switch($row['idty']){
                                                case "0"    : echo "完成";                  break;
                                                case "1"    : echo $fab_role ? '<span class="badge rounded-pill bg-danger">待簽</span>':"待簽"; break;
                                                case "2"    : echo "退件";                  break;
                                                case "3"    : echo "取消";                  break;
                                                case "4"    : echo "編輯";                  break;
                                                // case "10"   : echo "pr進貨";                break;
                                                case "10"   : echo "結案";                  break;
                                                default     : echo $row['idty']."na";     break;
                                            }?></td>
                                        <td>
                                            <!-- Action功能欄 -->
                                            <?php if((($row['fab_i_id'] == $sys_fab_id) || (in_array($row['fab_i_id'], $sys_sfab_id))) 
                                                    && ($row['idty'] == '1')){ ?> 
                                                <!-- 待簽：in_local對應人員 -->
                                                <a href="../<?php echo $form_type;?>/show.php?id=<?php echo $row['id'];?>&action=acceptance" class="btn btn-sm btn-xs btn-success">驗收</a>
                                            <?php }else if((($row['fab_o_id'] == $sys_fab_id) || (in_array($row['fab_i_id'], $sys_sfab_id)))
                                                    && ($row['idty'] == '2')){ ?>
                                                <!-- 待簽：out_local對應人員 -->
                                                <a href="../<?php echo $form_type;?>/show.php?id=<?php echo $row['id'];?>&action=review" class="btn btn-sm btn-xs btn-warning">待辦</a>
                                            <?php }else if((($row['fab_o_id'] == $sys_fab_id) || (in_array($row['fab_o_id'], $sys_sfab_id)))
                                                    && ($row['idty'] == '4')){ ?>
                                                <!-- 待簽：out_local對應人員 -->
                                                <a href="../<?php echo $form_type;?>/form.php?id=<?php echo $row['id'];?>&action=edit" class="btn btn-sm btn-xs btn-success">編輯</a>
                                            <?php }else{ ?>
                                                <!-- siteUser功能 -->
                                                <a href="../<?php echo $form_type;?>/show.php?id=<?php echo $row['id'];?>" class="btn btn-sm btn-xs btn-info">檢視</a>
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

