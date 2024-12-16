<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    // accessDenied();
    if(!isset($_SESSION)){ session_start(); }                                                          // 確認session是否啟動

    $fa_check  = '<snap class="fa_check"><i class="fa fa-check" aria-hidden="true"></i> </snap>';      // 打勾符號
    $fa_remove = '<snap class="fa_remove"><i class="fa fa-remove" aria-hidden="true"></i> </snap>';    // 打叉符號
	$uri       = (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) ? 'https://' : 'http://';  // 取得開頭
	$uri      .= $_SERVER['HTTP_HOST'];                                                                // 組合成http_host
    $pc        = $_REQUEST["ip"] = $_SERVER['REMOTE_ADDR'];                                            // 取得user IP
    $check_ip  = check_ip($_REQUEST);                                                                  // 驗證IP權限 // 確認電腦IP是否受認證

    $sys_role  = (isset($_SESSION[$sys_id]["role"])) ? $_SESSION[$sys_id]["role"] : false;             // 取出$_session引用
    $fun       = (!empty($_REQUEST['fun'])) ? $_REQUEST['fun'] : false ;                               // 先抓操作功能'notify_insign'= MAPP待簽發報 // 確認有帶數值才執行
    $inSign_lists = inSign_list();                                                                     // 載入所有待簽名單
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer" ></script>
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script src="../../libs/jquery/mloading_init.js"></script>
    <style>
        .fa_check {
            color: #00ff00;
        }
        .fa_remove {
            color: #ff0000;
        }
        #result {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <!-- <div class="col-11"> -->
                <div class="col-12 border rounded p-4" style="background-color: #D4D4D4;">
                    <!-- 表頭 -->
                    <div class="row">
                        <div class="col-12 col-md-6 py-0">
                            <h3>待簽清單統計</h3>
                            <span id="dabugTitle" style="color:red;"><b></b></span>
                        </div>
                        <div class="col-12 col-md-6 py-0 text-end">
                            <?php if($sys_role == 0 && $check_ip){ ?>
                                <button type="button" id="upload_myTodo_btn" class="btn btn-sm btn-xs <?php echo !$mailTo_insign ? 'btn-primary':'btn-warning';?>" data-toggle="tooltip" data-placement="bottom" 
                                    title="send notify" onclick="return confirm('確認發報？') && notify_insign()">傳送&nbspEmail&nbsp<i class="fa-solid fa-paper-plane"></i>&nbsp+&nbspMAPP&nbsp<i class="fa-solid fa-comment-sms"></i></button>
                                    
                            <?php } ?>
                            <button type="button" class="btn btn-secondary rtn_btn" onclick="location.href = '../index.php'"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
                        </div>
                    </div>

                        <div id="nav-receive" class="col-12 bg-white border rounded">
                            <!-- 1.領用申請單待簽名冊(receive) -->
                            <div class="row">
                                <div class="col-12 col-md-8 py-0 text-primary">
                                    <?php echo "待簽名單共：".count($inSign_lists)." 筆";?>
                                </div>
                                <div class="col-12 col-md-4 py-0 text-end">
                                    <button type="button" id="inSign_lists_btn" title="訊息收折" class="op_tab_btn" value="inSign_lists" onclick="op_tab(this.value)"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <div id="inSign_lists" class="inSign_lists col-12 mt-2 border rounded">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>姓名(工號)</th>
                                            <th>fab (local)</th>

                                            <th>請購待簽</th>
                                            <th>領用待簽</th>
                                            <th>合計待簽</th>
                                            <th>急件待簽</th>

                                            <th class="text-danger">請購退件</th>
                                            <th class="text-danger">領用退件</th>
                                            <th class="text-danger">合計退件</th>
                                            <th class="text-danger">急件退件</th>

                                            <th class="text-success">待領</th>
                                            <th class="text-success">急件</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($inSign_lists as $inSign_list){ ?>
                                            <tr>
                                                <td id="<?php echo 'id_'.$inSign_list['emp_id'];?>" style="text-align: start;"><?php echo $inSign_list["cname"]." (".$inSign_list["emp_id"].")";?></td>
                                                <td><?php echo $inSign_list["fab_title"]."</br>(". $inSign_list["local_title"].")";?></td>
                                                
                                                <td><?php echo $inSign_list["issue_waiting"] > 0 ? $inSign_list["issue_waiting"] : "" ;?></td>
                                                <td><?php echo $inSign_list["receive_waiting"] > 0 ? $inSign_list["receive_waiting"] : "" ;?></td>
                                                <td><?php echo $inSign_list["total_waiting"] > 0 ? $inSign_list["total_waiting"] : "" ;?></td>
                                                <td class="text-primary"><?php echo $inSign_list["ppty_3_waiting"] > 0 ? $inSign_list["ppty_3_waiting"] : "" ;?></td>
                                                
                                                <td><?php echo $inSign_list["issue_reject"] > 0 ? $inSign_list["issue_reject"] : "" ;?></td>
                                                <td><?php echo $inSign_list["receive_reject"] > 0 ? $inSign_list["receive_reject"] : "" ;?></td>
                                                <td><?php echo $inSign_list["total_reject"] > 0 ? $inSign_list["total_reject"] : "" ;?></td>
                                                <td class="text-danger"><?php echo $inSign_list["ppty_3_reject"] > 0 ? $inSign_list["ppty_3_reject"] : "" ;?></td>

                                                <td><?php echo $inSign_list["total_collect"] > 0 ? $inSign_list["total_collect"] : "" ;?></td>
                                                <td class="text-success"><?php echo $inSign_list["ppty_3_collect"] > 0 ? $inSign_list["ppty_3_collect"] : "" ;?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <div class="row">
                        <div class="col-12 col-md-6 pb-0">
                            <b>執行訊息：</b>
                        </div>
                        <div class="col-12 col-md-6 pb-0 text-end">
                        </div>
                        <!-- append執行訊息 -->
                        <div class="col-12 bg-white border rounded py-2 my-0" id="result">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-4 pb-0">
   
                        </div>
                        <div class="col-6 col-md-4 pb-0 text-center">
                            <div id="myMessage">
                                <?php if(!empty($fun)){ echo "** 自動模式 **"; }else{ echo "** 手動模式 **"; }?>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 pb-0 text-end">
                            <?php echo ($sys_role == 0) ? "* [管理者模式]" : "* [路人模式]";?>
                            <?php echo $check_ip ? $fa_check:$fa_remove; echo " ".$pc;?>
                        </div>
                    </div>
                </div>
            </div>
        <!-- </div> -->
    </div>

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11"></div>

</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/sweetalert/sweetalert.min.js"></script>
<script src="../../libs/openUrl/openUrl.js?v=<?=time();?>"></script>           <!-- 彈出子畫面 -->
<script>
    // init
        var fa_OK        = '<?=$fa_check?>';            // 打勾符號
        var fa_NG        = '<?=$fa_remove?>';           // 打叉符號
        var mail_OK      = '<snap class="fa_check"><i class="fa-solid fa-paper-plane"></i> </snap>';                // 寄信符號
        var mail_NG      = '<snap class="fa_remove"><i class="fa-solid fa-triangle-exclamation"></i> </snap>';      // 打叉符號

        const uri        = '<?=$uri?>';
        var fun          = '<?=$fun?>';                 // 是否啟動寄送信件給待簽人員
        var check_ip     = '<?=$check_ip?>';
        var inSign_lists = <?=json_encode($inSign_lists)?>;
        console.log('inSign_lists...', inSign_lists);
        var lists_obj    = { inSign_lists : inSign_lists }

        var receive_url  = '領用路徑：'+uri+'/ppe/receive/';
        var issue_url    = '請購路徑：'+uri+'/ppe/issue/';

        var int_msg1     = '【環安PPE系統】待您處理文件提醒';
        var int_msg2     = ' 您共有 ';
        var int_msg3     = ' 件待簽核文件尚未處理';
        var ret_msg3     = ' 件被退件文件尚未處理';
        var col_msg3     = ' 件待收發文件尚未處理';
        var int_msg4     = '，如已處理完畢，請忽略此訊息！\n\n** 請至以下連結查看待處理文件：\n';
        var srt_msg4     = '，如已處理完畢，請忽略此訊息！\n\n';
        var int_msg5     = '\n\n溫馨提示：\n    1.登錄過程中如出現提示輸入帳號密碼，請以cminl\\NT帳號格式\n';

        var push_result  = {
                'mapp' : {
                    'success' : 0,
                    'error'   : 0
                },
                'email' : {
                    'success' : 0,
                    'error'   : 0
                }
            }

        var Today       = new Date();
        const thisToday = Today.getFullYear() +'/'+ String(Today.getMonth()+1).padStart(2,'0') +'/'+ String(Today.getDate()).padStart(2,'0');  // 20230406_bug-fix: 定義出今天日期，padStart(2,'0'))=未滿2位數補0
        const thisTime  = String(Today.getHours()).padStart(2,'0') +':'+ String(Today.getMinutes()).padStart(2,'0');                           // 20230406_bug-fix: 定義出今天日期，padStart(2,'0'))=未滿2位數補0

</script>

<script src="insign_msg.js?v=<?=time()?>"></script>


<?php include("../template/footer.php"); ?>
