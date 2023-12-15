<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    // accessDenied();
    if(!isset($_SESSION)){                                              // 確認session是否啟動
        session_start();
    }

    if(isset($_SESSION[$sys_id]["role"])){ 
        $sys_id_role = $_SESSION[$sys_id]["role"];                      // 取出$_session引用
    }else{
        $sys_id_role = false;                                           // 取出$_session引用
    };

    if(!empty($_REQUEST['fun'])){       // 確認有帶數值才執行
        $fun = $_REQUEST['fun'];        // 先抓操作功能   
    }else{
        $fun = null;
    }

    // 確認電腦IP是否受認證
    $fa_check = '<snap id="fa_check"><i class="fa fa-check" aria-hidden="true"></i> </snap>';       // 打勾符號
    $fa_remove = '<snap id="fa_remove"><i class="fa fa-remove" aria-hidden="true"></i> </snap>';    // 打叉符號
    $pc = $_REQUEST["ip"] = $_SERVER['REMOTE_ADDR'];
    $check_ip = check_ip($_REQUEST);

    // 載入所有待簽名單
    $inSign_lists = inSign_list();

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer" ></script>
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <style>
        #fa_check {
            color: #00ff00;
        }
        #fa_remove {
            color: #ff0000;
        }
        #result {
            text-align: center;
        }
    </style>
    <script>    
        // mloading function
        function mloading(){
            $("body").mLoading({    // 開啟loading
                icon: "../../libs/jquery/Wedges-3s-120px.gif",
                // icon: "../../libs/jquery/loading.gif",
                text: "上傳中"                              // 指示器顯示的文本
                // html: true,                              // 指示器顯示的內容是否為 HTML 代碼
                // mask: true,                              // 是否顯示蒙層
                // delay: 5000,                             // 5秒後自動隱藏
                // css: {
                //     background: "transparent",           // 指示器的背景顏色
                //     color: "#fff" // 指示器的文本顏色
                // }
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
            <div class="col-8">
                <!-- 表頭 -->
                <div class="row" style="vertical-align:bottom;color:#FFFFFF;">
                    <div class="col-12 col-md-4 py-0">
                        <h3><i class="fa-solid fa-comment-sms"></i>&nbspMAPP待簽發報</h3>
                    </div>
                    <div class="col-6 col-md-4 py-0 text-center">
                        <div id="myMessage">
                            <?php if(!empty($fun)){ echo "** 自動模式 **"; }else{ echo "** 手動模式 **"; }?>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 py-0 text-end">
                        <?php if($sys_id_role == 0){ echo "* [管理者模式]"; }else{ echo "* [路人模式]"; }?>
                        <?php echo $check_ip ? $fa_check:$fa_remove; echo " ".$pc;?>
                    </div>
                </div>

                <div class="col-12 border rounded p-4" style="background-color: #D4D4D4;">
                    <!-- 1.領用申請單待簽名冊(receive) -->
                    <div id="nav-receive" class="col-12 bg-white border rounded">
                        <div class="row">
                            <div class="col-12 col-md-8 py-0 text-primary">
                                <?php echo "1. ?fun=receive -- 領用申請待簽名單共：".count($inSign_lists)." 筆";?>
                            </div>
                            <div class="col-12 col-md-4 py-0 text-end">
                                <?php if($sys_id_role == 0 && $check_ip){ ?>
                                    <button type="button" id="upload_myTodo_btn" class="btn btn-sm btn-xs btn-primary" data-toggle="tooltip" data-placement="bottom" 
                                        title="TN PPC(mapp)" onclick="step_0()"><i class="fa fa-paper-plane" aria-hidden="true"></i> 傳送MAPP</button>
                                <?php } ?>
                                <button type="button" id="user_lists_btn" title="訊息收折" class="op_tab_btn" value="user_lists" onclick="op_tab(this.value)"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            </div>
                        </div>

                        <div id="user_lists" class="user_lists col-12 mt-2 border rounded">
                            <table style="width: 80%;">
                                <thead>
                                    <tr>
                                        <th>姓名(工號)</th>
                                        <th>待簽</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($inSign_lists as $inSign_list){ ?>
                                        <tr>
                                            <td id="<?php echo 'id_'.$inSign_list['emp_id'];?>"><?php echo $inSign_list["cname"]." (".$inSign_list["emp_id"].")";?></td>
                                            <td><?php echo $inSign_list["waiting"];?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 col-md-6 py-0">
                            <b>執行訊息：</b>
                        </div>
                        <div class="col-6 col-md-6 py-0 text-end">
                        </div>
                    </div>
                    <!-- append執行訊息 -->
                    <div class="col-12 bg-white border rounded py-2 my-0" id="result">
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
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>
<script>
    // init
        var fa_OK = '<snap id="fa_check"><i class="fa fa-check" aria-hidden="true"></i> </snap>';    // 打勾符號
        var fa_NG = '<snap id="fa_remove"><i class="fa fa-remove" aria-hidden="true"></i> </snap>'; // 打叉符號
        var fun         = '<?=$fun?>';
        var check_ip    = <?=$check_ip?>;
        
        var inSign_lists = <?=json_encode($inSign_lists);?>;
        // var inSign_lists = [];
        var ppe_url  = 'http://tw059332n.cminl.oa/ppe/receive/';
        var int_msg1 = '** 測試 ~ 測試 **\n【環安PPE系統】待您簽核文件提醒\n';
        var int_msg2 = ' 您有 ';
        var int_msg3 = ' 件待簽核文件尚未處理，如已簽核完畢，請忽略此訊息！';
        var int_msg4 = '\n** 請至以下連結查看待簽核文件：\n';
        var int_msg5 = '\n溫馨提示：\n1.登錄過程中如出現提示輸入帳號密碼，請以cminl\\NT帳號格式\n<此訊息為系統自動發出，請勿回覆>';
        var mapp_result = {
                'success' : 0,
                'error'   : 0
            }

        var Today = new Date();
        const thisToday = Today.getFullYear() +'/'+ String(Today.getMonth()+1).padStart(2,'0') +'/'+ String(Today.getDate()).padStart(2,'0');             // 20230406_bug-fix: 定義出今天日期，padStart(2,'0'))=未滿2位數補0
        const thisTime = String(Today.getHours()).padStart(2,'0') +':'+ String(Today.getMinutes()).padStart(2,'0');                                       // 20230406_bug-fix: 定義出今天日期，padStart(2,'0'))=未滿2位數補0
        // console.log('thisNow:', thisToday, thisTime);
    // 子功能
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
        // fun_2 倒數 n秒自動關閉視窗功能
        function CountDown() {
            let delayTime = 1000;   // 1000=1秒
            let i = 15;             // 15次==15秒
            const loop = () => {
                if (i >= 0) {
                    document.getElementById("myMessage").innerHTML = "視窗關閉倒數 "+ i +" 秒";
                    setTimeout(loop, delayTime);
                } else {
                    // callback();                  // 要執行的程式
                    document.getElementById("myMessage").innerHTML = "視窗關閉！";
                    window.open('', '_self', '');
                    window.close();
                }
                i--;
            };
            loop();
        }
        // fun_3 延遲模組
        function delayedLoop(i, callback) {
            if(i==0 || i==null){
                i = 10;             // 10次==10秒
            }
            const loop = () => {
                if (i >= 0) {
                    document.getElementById("myMessage").innerHTML = "Fun: "+ callback +" 執行倒數 "+ i +" 秒";
                    setTimeout(loop, 1000);
                } else {
                    document.getElementById("myMessage").innerHTML = "Fun: "+ callback +" 執行！";
                    window[callback]();                  // 要執行的程式
                }
                i--;
            };
            loop();
        }

    // 主技能
        // 2023/12/13 step_2 寫入log記錄檔~
        function toLog(logs_msg){
            $.ajax({
                url:'../autolog/log.php',
                method:'post',
                async: false,                                               // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    function : 'storeLog',
                    thisDay  : thisToday,
                    sys      : 'ppe/receive',
                    logs     : logs_msg,
                    t_stamp  : ''
                },
                success: function(res){
                    console.log("toLog -- success：", res);
                    toLog_result_check = true; 
                },
                error: function(res){
                    console.log("toLog -- error：", res);
                    toLog_result_check = false;
                }
            });
            return toLog_result_check;
        }
        // 2023/12/13 step_1 將訊息推送到TN PPC(mapp)給對的人~
        function push_mapp(user_emp_id, mg_msg){
            $.ajax({
                url:'http://10.53.248.167/SendNotify',                      // 20230505 正式修正要去掉port 801
                method:'post',
                async: false,                                               // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    // eid : user_emp_id,                                      // 傳送對象
                    eid : '10008048',                                       // 傳送對象 = 測試期間 只發給我
                    message : mg_msg                                        // 傳送訊息
                },
                success: function(res){
                    // console.log("push_mapp -- success：",res);
                    mapp_result['success']++;
                    mapp_result_check = true; 
                },
                error: function(res){
                    // console.log("push_mapp -- error：",res);
                        // mapp_result['error']++; 
                        // mapp_result_check = false;
                    // ** 受到CORS阻擋，但實際上已完成發送... 所以全部填success
                    mapp_result['success']++;
                    mapp_result_check = true; 
                }
            });
            return mapp_result_check;
        }
        // 2023/12/13 step_0 整理訊息、發送、顯示發送結果。
        function step_0(){
            var user_logs = [];                                                 // 宣告儲存Log用的 大-陣列Logs
            $('#result').empty();                                               // 清空執行訊息欄位        
            if(inSign_lists){
                if(inSign_lists.length >= 1){                                   // 有件數 > 0 舊執行通知
                    // 逐筆把清單繞出來
                    Object(inSign_lists).forEach(function(user){
                        var user_emp_id = String(user['emp_id']).trim();            // 定義 user_emp_id + 去空白
                        var user_log = {                                            // 宣告儲存Log內的單筆 小-物件log
                            emp_id  : user['emp_id'],
                            cname   : user['cname'],
                            waiting : user['waiting']
                        }
    
                        // 確認工號是否有誤
                        if(!user_emp_id || (user_emp_id.length < 8)){
                            alert("工號字數有誤 !!");
                            $("body").mLoading("hide");
                            mapp_result['error']++; 
                            return false;
    
                        } else {
                            // 組合訊息文字
                            var mg_msg  = int_msg1;
                                mg_msg += "(" + user['cname'] + ")";
                                mg_msg += int_msg2 + user['waiting'] + int_msg3;
                                mg_msg += int_msg4 + ppe_url + int_msg5;
                            user_log['mg_msg']   = mg_msg;                                        // 小-物件log 紀錄mg_msg訊息
                            user_log['thisTime'] = thisTime;                                      // 小-物件log 紀錄thisTime
    
                            // 發送mapp
                            mapp_result_check = push_mapp(user_emp_id, mg_msg);               // *** call fun.step_1 將訊息推送到TN PPC(mapp)給對的人~
                                // if(user_emp_id != '10008048'){                                   // 測試要過濾
                                //     // mapp_result_check = push_mapp(user_emp_id, mg_msg);      // call fun.step_1 將訊息推送到TN PPC(mapp)給對的人~
                                //     mapp_result['success']++;
                                //     mapp_result_check = true; 
                                // }else{
                                //     mapp_result['error']++; 
                                //     mapp_result_check = false; 
                                // }
    
                            // 標記emp_id位置，顯示OK或NG，並顯示執行訊息
                            if(mapp_result_check){                                              // 判斷是否發送成功
                                user_log['mapp_res'] = 'OK';
                            }else{
                                user_log['mapp_res'] = 'NG';
                            }
                            var action_id = document.querySelector('#id_'+user_emp_id);         // 定義user所在td位置
                            action_id.innerHTML = window['fa_'+user_log['mapp_res']] + action_id.innerText;              // 插入打叉符號
                            $('#result').append(window['fa_'+user_log['mapp_res']] + user['cname'] + "("+user['emp_id'] + ")" + ' ... '+user_log['mapp_res']+'</br>'); // 插入下方顯示
                        }
                        user_logs.push(user_log);                                               // 將log單筆小物件 塞入 logs大陣列中
                    })

                }else{                                                                          // 沒件數 == 0 就不用執行通知，但依樣要生成Log
                    var user_log = {                                                            // 宣告儲存Log內的單筆 小-物件log
                            emp_id  : '',
                            cname   : '',
                            waiting : '0',
                            mg_msg  : '(無待簽文件)',
                            mapp_res: 'OK',
                            thisTime: thisTime
                        }
                    user_logs.push(user_log);                                                   // 將log單筆小物件 塞入 logs大陣列中 
                    $('#result').append(fa_OK + '(無待簽文件) ... done'+'</br>');                // 插入下方顯示
                }
            }
            // 打包整理Logs的陣列
                user_logs_obj = {
                    thisDay  : thisToday,
                    autoLogs : user_logs
                }
                user_logs_json = JSON.stringify(user_logs_obj);                                 // logs大陣列轉JSON字串
                toLog(user_logs_json);                                                          // *** call fun.step_2 寫入log記錄檔

            // swal組合訊息，根據發送結果選用提示內容與符號
            var swal_title = '領用申請單-發放訊息';
                if((mapp_result['error'] == 0) && (mapp_result['success'] != 0)){
                    var swal_content = '推送成功：'+ mapp_result['success'];
                    var swal_action = 'success';
                } else if((mapp_result['error'] != 0) && (mapp_result['success'] == 0)){
                    var swal_content = '推送失敗：'+ mapp_result['error'];
                    var swal_action = 'error';
                } else {
                    var swal_content = '推送成功：'+ mapp_result['success'] +'、錯誤：'+ mapp_result['error'];
                    var swal_action = 'warning';
                }

            $("body").mLoading("hide");                                                         // 關閉mLoading圖示
            swal(swal_title ,swal_content ,swal_action, {timer:5000});                          // popOut swal + 自動關閉
            // 將其歸零，避免汙染
            mapp_result = {
                'success' : 0,
                'error'   : 0
            }
        }

    // fun啟動自動執行
    $(document).ready( function () {
        // op_tab('user_lists');   // 關閉清單
        if(check_ip && fun){
            switch (fun) {
                case 'receive':         // MAPP待簽發報
                    (async () => {
                        await step_0();     // 等待 func1 執行完畢  // step_0 整理訊息、發送、顯示發送結果。
                        CountDown();        // 當 func1 執行完畢後才會執行 func2    // 倒數 n秒自動關閉視窗~
                    })();
                    break;

                default:
                    $('#result').append('MAPP待簽發報 : function error!</br>');
            }

        }else{
            $('#result').append('MAPP待簽發報 : standBy...</br>');
        }

    } );

</script>

<?php include("../template/footer.php"); ?>
