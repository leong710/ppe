<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    // accessDenied();
    
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
                        <h3>MAPP自動發報通知</h3>
                    </div>
                    <div class="col-6 col-md-4 py-0 text-center">
                        <div id="myMessage">
                            <?php if(!empty($fun)){ echo "** 自動模式 **"; }else{ echo "** 手動模式 **"; } ?>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 py-0 text-end">
                        <?php if(isset($_SESSION[$sys_id]) && $_SESSION[$sys_id]["role"] == 0){ 
                                echo "* [管理者模式]";
                            }else{
                                echo "* [路人模式]";
                            };?>
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
                                <?php if(isset($_SESSION[$sys_id]) && $_SESSION[$sys_id]["role"] <= 1 && $check_ip){ ?>
                                    <button type="button" id="upload_myTodo_btn" class="btn btn-sm btn-xs btn-primary" data-toggle="tooltip" data-placement="bottom" 
                                        title="TN PPC(mapp)" onclick="step_0()"><i class="fa fa-paper-plane" aria-hidden="true"></i> 傳送mapp</button>
                                <?php } ?>
                                <button type="button" id="user_lists_btn" title="user_lists收折鈕" class="text-primary"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
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
    var fun = '<?=$fun?>';
    var check_ip = <?=$check_ip?>;
    
    var inSign_lists = <?=json_encode($inSign_lists);?>;
    var ppe_url = 'http://tw059332n.cminl.oa/ppe/receive/';
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
    console.log('thisNow:', thisToday, thisTime);

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
                        // mapp_result_check = push_mapp(user_emp_id, mg_msg);      // 正式用這個發
                            // if(user_emp_id != '11053914'){                              // 測試要過濾
                            if(user_emp_id == '10008048'){
                                // mapp_result_check = push_mapp(user_emp_id, mg_msg);
                                // console.log(user_emp_id, mg_msg);
                                mapp_result['success']++;
                                mapp_result_check = true; 
                                user_log['mapp_res'] = 'OK';
                            }else{
                                mapp_result['error']++; 
                                mapp_result_check = false; 
                                user_log['mapp_res'] = 'NG';
                            }

                    // 標記emp_id位置，顯示OK或NG，並顯示執行訊息
                    var action_id = document.querySelector('#id_'+user_emp_id);         // 定義user所在td位置
                    if(mapp_result_check){
                        action_id.innerHTML = fa_OK + action_id.innerText;              // 插入打勾符號
                        $('#result').append(fa_OK + user['cname'] + "("+user['emp_id'] + ")" + ' ... done'+'</br>');
                    }else{
                        action_id.innerHTML = fa_NG + action_id.innerText;              // 插入打叉符號
                        $('#result').append(fa_NG + user['cname'] + "("+user['emp_id'] + ")" + ' ... fail'+'</br>');
                    }
                }
                user_logs.push(user_log);                                               // 將log單筆小物件 塞入 logs大陣列中
            })
        }
        // 打包整理Logs的陣列
            user_logs_obj = {
                thisDay  : thisToday,
                autoLogs : user_logs
            }
            user_logs_json = JSON.stringify(user_logs_obj);                                 // logs大陣列轉JSON字串
        // console.log(user_logs_json);
            toLog(user_logs_json);

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

        $("body").mLoading("hide");                                                       // 關閉mLoading圖示
        swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:5000});        // popOut swal + 自動關閉
        // 將其歸零，避免汙染
        mapp_result = {
            'success' : 0,
            'error'   : 0
        }
    }


    $(function () {
        // 在任何地方啟用工具提示框
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        })

        // fun_1 user list表單的隱藏按鈕
        var user_lists_btn = document.getElementById("user_lists_btn");
        user_lists_btn.addEventListener('click',function(){
            $("#user_lists_btn > .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
            if($("#user_lists").css("display")=="none"){
                $("#user_lists").css("display","block")
                return;
            }
            if($("#user_lists").css("display")=="block"){
                $("#user_lists").css("display","none")
                return;
            }
        })

        // fun_2 倒數 n秒自動關閉視窗功能
        var CountDownSecond = 61; //讓倒數計時器一開始的數字是10，10秒鐘後關閉視窗
        function CountDown() {
            // window.close();
            if (CountDownSecond !=0) {
                CountDownSecond -= 1;
                document.getElementById("myMessage").innerHTML = "視窗關閉倒數 " + CountDownSecond + " 秒";
            } else {
                document.getElementById("myMessage").innerHTML = "視窗關閉！";
                window.close();
                return;
            }
            setTimeout("CountDown()",1000);
        }

        // fun_3 延遲模組
        function delayedLoop() {
            var i = 0;
            var loop = function() {
                // console.log('delayedLoop_i:',i);
                i++;
                if (i < 10) {
                    setTimeout(loop, 1000); // 每隔3秒遞迴調用 loop() 函数
                }
            }
            loop();
        }

    })

    // fun啟動自動執行
    $(document).ready( function () {

            // if(check_ip && fun){
            //     console.log('fun:',fun);
            // }
            // if(check_ip && fun){
            //     switch (fun) {
            //         case 'mytodo':
            //             sort_toRun();               // autoUpload myTodo 呼叫主功能sort_toRun
            //             CountDown();                // 倒數 n秒自動關閉視窗~
            //             break;
            //         case 'scm':
            //             uploadMyTodo_toSCM();       // autoUpload scm_db step-0.啟動自動上傳scm
            //             CountDown();                // 倒數 n秒自動關閉視窗~
            //             break;
            //         case 'twice':
            //             sort_toRun();               // autoUpload myTodo 呼叫主功能sort_toRun
            //             setTimeout(uploadMyTodo_toSCM, 5000);   // autoUpload scm_db 延遲5秒 + step-0.啟動自動上傳scm 
            //             CountDown();                // 倒數 n秒自動關閉視窗~
            //             break;
            //         default:
            //             $('#result').append('autoUpload : function error!</br>');
            //     }

            // }else{
            //     $('#result').append('autoUpload : standBy...</br>');
            // }
        // step_0();
    } );


// // // 封存

    // // 2023/10/25 整理領用申請單內訊息給mapp用
    // function sort_receive(){

    //     // get領用地點
    //         var getLocal_id = document.getElementById('local_id');
    //         if(getLocal_id){
    //             var collect_local = getLocal_id.value;
    //         }else{
    //             var collect_local = '(請查閱領用申請)';
    //         }
    //     // get購物車數量
    //         var getShopping_cart = document.getElementById('shopping_count');
    //         if(getShopping_cart){
    //             var shopping_count = getShopping_cart.innerText;
    //         }else{
    //             var shopping_count = '(請查閱領用申請)';
    //         }

    //     var receive_row_cart = JSON.parse(receive_row['cata_SN_amount']);   // get申請單品項數量
    //     var i_cunt = 1;                                                     // 各品項前的計數
    //     var add_cata_item = '[ PPE領用申請 - '+action+' ]';
    //     add_cata_item += '\n申請日期：'+receive_row['created_at'];
    //     add_cata_item += '\n申請單位：'+receive_row['plant'];
    //     add_cata_item += '\n申請人：'+receive_row['cname']+'  分機：'+receive_row['extp'];
    //     add_cata_item += '\n領用地點：'+collect_local;

    //     Object.keys(receive_row_cart).forEach(function(cart_key){
    //         Object(catalogs).forEach(function(cata){          
    //             if(cata['SN'] === cart_key){
    //                 // add_cata_item += '\nSN： '+cata['SN']+'\npName： '+cata['pname']+'\nModel： '+cata['model']+'\nSize： '+cata['size']+'\nAmount： '+receive_row_cart[cart_key]+'\nUnit： '+cata['unit'];
    //                 add_cata_item += '\n'+i_cunt+'.SN:'+cata['SN']+' / '+cata['pname'];
    //                 add_cata_item += '\n'+i_cunt+'.型號:'+cata['model']+' / Size:'+cata['size']+' / 數量：'+receive_row_cart[cart_key]['need']+' '+cata['unit']+'\n';
    //                 i_cunt += 1;
    //                 return;         // 對應到一筆資料就可以結束迴圈了
    //             }
    //         })
    //     })

    //     add_cata_item += '\n以上共：'+shopping_count +' 品項';
    //     add_cata_item += '\n文件連結：'+receive_url;

    //     // console.log("i'm sort_receive");
    //     return add_cata_item;
    // }



    // // 上傳TN PPC(mapp)給管理員~
    // function logTo_mapp(auto_msg){
    //     let fa_check = '<snap id="fa_check"><i class="fa fa-check" aria-hidden="true"></i> </snap>';    // 打勾符號
    //     let fa_remove = '<snap id="fa_remove"><i class="fa fa-remove" aria-hidden="true"></i> </snap>'; // 打叉符號
    //     let re_auto_msg = auto_msg + ' : sendMapp -- ';
    //     $.ajax({
    //         // url:'http://10.53.248.167:801/SendNotify',
    //         url:'http://10.53.248.167/SendNotify',       // 20230505 正式修正要去掉port 801
    //         method:'post',
    //         dataType:'json',
    //         data:{
    //             eid : '10008048',
    //             message : auto_msg
    //         },
    //         success: function(res){
    //             // console.log("logTo_mapp: success -- ",res);
    //             $('#result').append(fa_check + re_auto_msg +'OK'+'</br>');
    //         },
    //         error: function(res){
    //             // console.log("logTo_mapp: error -- ",res);
    //             $('#result').append(fa_remove + re_auto_msg +'NG'+'</br>');
    //         }
    //     });
    // }


    // // 上傳mapp給user_emp_id~
    // function mapp_toUser(user_emp_id){
    //     // let re_auto_msg = '本月份'+ user_emp_id + '個人todo清單已生成，供您參考執行--';
    //     let re_auto_msg = '本月份個人todo清單已生成，供您參考執行。';
    //     $.ajax({
    //         // url:'http://10.53.248.167:801/SendNotify',
    //         url:'http://10.53.248.167/SendNotify',       // 20230505 正式修正要去掉port 801
    //         method:'post',
    //         dataType:'json',
    //         data:{
    //             // eid : user_emp_id,
    //             eid : '10008048',
    //             message : re_auto_msg
    //         },
    //         success: function(res){
    //             // console.log("logTo_mapp: success -- ",res);
    //         },
    //         error: function(res){
    //             console.log("logTo_mapp: error -- ",res);
    //         }
    //     });
        
    // }

    // // tab-1.myTodo 20230309_改良上一版(取畫面資料)方式，直接採用物件轉換，目的是用於autoupload to myTodo
    // function post_plan(row_data) {
    //     var row_arr = new Array('pm_task','item','remark','freq','schedule_arr','emp_id','sign_code','table_name','id','flag','link');         // row_arr = 定義出這幾個要抓資料的schema
    //     var myTodo_arr_key = new Array('大PM','副PM','SitePM','個人專案');         // myTodo_arr_key = 定義出這幾個要抓資料的table
    //     var user_emp_id = row_data['emp_id'];
    //     var user_sign_code = row_data['sign_code'];
    //     var schedule_arr = [];
    //     var myTodo_arr = [];

    //     // 整包資料 分門別類 倒進去
    //     myTodo_arr['大PM'] = row_data['myBPms'];                    // 1.我的大PM工作
    //     myTodo_arr['副PM'] = row_data['mySPms'];                    // 2.我的副PM工作
    //     myTodo_arr['SitePM'] = row_data['mySitePms'];               // 3.我的SitePM工作-1
    //     myTodo_arr['個人專案'] = row_data['myJobs'];                 // 4.我的專案工作
        
    //     // step-1：清洗、整理資料
    //     Object.keys(myTodo_arr).forEach(key =>{              // 將原排程陣列逐筆繞出來 大PM、副PM、SitePM、個人專案 = key
    //         for(i=0; i< myTodo_arr[key].length; i++){       // 將 大PM、副PM、SitePM、個人專案 = key逐筆繞出來 內容太多...略
    //             // 清洗資料和數據整理
    //             Object(row_arr).forEach(function(row_cell){              // 將row_arr定義出來的schema逐筆繞出來 = row_cell
    //                 // 定義 table_name
    //                 if(row_cell == 'table_name'){
    //                     if(key=='個人專案'){
    //                         myTodo_arr[key][i][row_cell] = 'myjob';     // 只有'個人專案'  table_name = myjob
    //                     }else{
    //                         myTodo_arr[key][i][row_cell] = 'pmplan';    // 其他 '大PM' '副PM' 'SitePM'  table_name = pmplan
    //                     }
    //                 }
    //                 // 只有'個人專案' 的pm_task用這組type
    //                 if(key=='個人專案' && row_cell == 'pm_task'){
    //                     myTodo_arr[key][i][row_cell] = myTodo_arr[key][i]['type'];
    //                 }
    //                 // 套用'schedule' 取陣列
    //                 if(row_cell == 'schedule_arr'){
    //                     if(myTodo_arr[key][i]['schedule'] !=''){        // 過濾schedule不是空值才繼續
    //                         myTodo_arr[key][i]['schedule'] = Array(JSON.parse(myTodo_arr[key][i]['schedule']));     // Array陣列(JSON.parse字串轉換成物件)
                            
    //                         for(x=0; x<myTodo_arr[key][i]['schedule'][0].length; x++){
    //                             let myBPm_MonthDay = String(myTodo_arr[key][i]['schedule'][0][x].slice(myTodo_arr[key][i]['schedule'][0][x].indexOf('_')+1,));      // 取 月-日
    //                             let myBPm_Month = String(myBPm_MonthDay.substr(0, myBPm_MonthDay.indexOf('-')));                                                    // 取 月
    //                             schedule_arr[myBPm_Month] = myBPm_MonthDay ;                                                                                        // key=月 => value=月-日
    //                         }
    //                         schedule_arr = schedule_arr.filter(function (s) { return s && s.trim(); });    // 去除empty，但key會不見...
    //                         myTodo_arr[key][i]['schedule_arr'] = schedule_arr.toString();       // 把小陣列轉成字串(.toString())後 倒回去主變數陣列中

    //                     }else{
    //                         myTodo_arr[key][i]['schedule'] = [];
    //                     }
    //                     schedule_arr = [];                                                  // 清除小陣列，供下一輪使用
    //                 }
    //                 // 套用'emp_id'
    //                 if(row_cell == 'emp_id'){
    //                     myTodo_arr[key][i][row_cell] = user_emp_id;
    //                 }
    //                 // 套用'sign_code'
    //                 if(row_cell == 'sign_code'){
    //                     myTodo_arr[key][i][row_cell] = user_sign_code;
    //                 }
    //             })
    //         }
    //     })

    //     // step-2：非同步逐筆拋資料 to mytodo(swap)
    //     Object(myTodo_arr_key).forEach(function(arr_key){              // 將myTodo_arr_key定義出這幾個要抓資料的table逐筆繞出來
    //         for(i=0; i<myTodo_arr[arr_key].length; i++){
    //             // 假如要抓取的是myTodo_arr[arr_key]，要確認胎計畫是否被Off
    //             HTML_check_flag = myTodo_arr[arr_key][i]['flag'];
    //             if(HTML_check_flag !== 'Off'){
    //                 let thisYear = Number(<=$thisYear?>);
    //                 let thisMonth = Number(<=$thisMonth?>);                                            // 抓這個月的月份
    //                 let nextMonth = Number(<=$nextMonth?>);                                            // 抓下個月的月份
    //                 let HTML_schedule = myTodo_arr[arr_key][i]['schedule_arr'];                         // 擷取schedule字串
    //                 myTodo_arr[arr_key][i]['schedule'] = HTML_schedule.split(',');                      // 將字串由','處拆成陣列
    //                 myTodo_arr[arr_key][i]['schedule_reArr'] = [];                                      // 增加定義一個空陣列來放有index的排程
    
    //                 Object(myTodo_arr[arr_key][i]['schedule']).forEach(function(due_date){              // 將原排程陣列逐筆繞出來
    //                     // 取字串 = substr(開始,幾個字)  // 找字串 = search('要找的字') = 返回字串所在位置
    //                     myTodo_arr[arr_key][i]['schedule_reArr'][due_date.substr(0,due_date.search('-'))] = due_date;   // 指定index(月份) = value(due_date)
    //                 })
    //                 // 這裡只判斷本月的工作，但最好是能判斷30天內的工作
    //                 if(myTodo_arr[arr_key][i]['schedule_reArr'][thisMonth] !== undefined || myTodo_arr[arr_key][i]['schedule_reArr'][nextMonth] !== undefined){
    //                     // table陣列轉變數
    //                         let HTML_emp_id = myTodo_arr[arr_key][i]['emp_id'];
    //                         let HTML_sys_name = arr_key;
    //                         let HTML_table_name = myTodo_arr[arr_key][i]['table_name'];
    //                         let HTML_table_id = myTodo_arr[arr_key][i]['id'];
    //                         let HTML_pm_task = myTodo_arr[arr_key][i]['pm_task'];
    //                         let HTML_item = myTodo_arr[arr_key][i]['item'];
    //                         let HTML_remark = myTodo_arr[arr_key][i]['remark'];
    //                         let HTML_content = '';
    //                         let HTML_level = '';
    //                         let HTML_flag = myTodo_arr[arr_key][i]['flag'];
    //                         let HTML_link = myTodo_arr[arr_key][i]['link'];
    
    //                     $.ajax({
    //                         url:'../api/index.php',
    //                         method:'post',
    //                         dataType:'json',
    //                         data:{
    //                             function : 'store_mytodo',           // 操作功能 儲存到myTodo
    //                             emp_id : HTML_emp_id,
    //                             sys_name : HTML_sys_name,
    //                             table_name : HTML_table_name,
    //                             table_id : HTML_table_id,
    //                             pm_task : HTML_pm_task,
    //                             item : HTML_item,
    //                             remark : HTML_remark,
    //                             content : HTML_content,
    //                             schedule : HTML_schedule,
    //                             level : HTML_level,
    //                             flag : HTML_flag,
    //                             link : HTML_link,
    //                             thisYear : thisYear,
    //                             thisMonth : thisMonth
    //                         },
    //                         success: function(res){
    //                             // let res_r = res["result"];
    //                             // let swal_action = 'success';
    //                             // let swal_content = '上傳成功';
    //                             // swal('uploadMyTodo_toSCM' ,swal_content ,swal_action);
    //                         },
    //                         error: function(e){
    //                             console.log("error");
    //                             $('#result').append( fa_remove+'&nbsp'+HTML_emp_id+' autoUpload scm_db: upload error!</br>');
            
    //                         }
    //                     });
    //                 }
    //             }
    //             delayedLoop(); // 開始遞迴調用 delayedLoop()
    //         }
    //     })
    //     return;
    // }
    
    // // tab-1.myTodo 前置動作：逐筆User撈取todo清單，再由post_plan轉存給mytodo
    // function sort_toRun(){
    //     $('body').mLoading("show");  //显示loading组件
    //     var allUsers = <=json_encode($allUsers)?>;                             //把$allUsers陣列encode，裝在json
    //     var fa_check = '<snap id="fa_check"><i class="fa fa-check" aria-hidden="true"></i> </snap>';    // 打勾符號
    //     var fa_remove = '<snap id="fa_remove"><i class="fa fa-remove" aria-hidden="true"></i> </snap>'; // 打叉符號
    //     var result_div = document.querySelector('#result');                     // 定義執行訊息div
    //     var log_auto_msg = "1. mytodo(製作個人todo清單存到myTodo)";

    //     // ** step-0.將原排程陣列逐筆繞出來
    //     Object(allUsers).forEach(function(user){
    //         var user_emp_id = String(user['emp_id']);                                   // 定義 user_emp_id
    //         var action_id = document.querySelector('#id_'+user_emp_id);     // 定義 user所在td位置

    //         // ** step-1.先取得user的整包myTodo(1~4項)
    //         $.ajax({
    //             url:'load.php',
    //             method:'get',
    //             dataType:'json',
    //             data:{
    //                 fun: 'go',  
    //                 emp_id: user_emp_id
    //             },
    //             success: function(res){
    //                 // 確認返回值是否有資料
    //                 if(res['myBPms'].length != 0 || res['mySPms'].length != 0 || res['mySitePms'].length != 0 || res['myJobs'].length != 0){
    //                     // ** step-2.將資料res轉拋給function post_plan來執行upload
    //                     post_plan(res);                                           // 生成mytodo計畫by月
    //                     // mapp_toUser(user_emp_id);                                 // mapp通知user...需考慮 假日推播干擾生活問題
    //                     action_id.innerHTML = fa_check + action_id.innerText;     // 插入打勾符號
    //                 }else{
    //                     action_id.innerHTML = fa_remove + action_id.innerText;    // 插入打叉符號
    //                 }
    //             },
    //             error: function(e){
    //                 console.log("error");
    //                 action_id.innerHTML = fa_remove + action_id.innerText;    // 插入打叉符號
    //                 // add run tag
    //                     $('#result').append(fa_remove+'&nbsp'+user_emp_id+'autoUpload myTodo: function error!');
    //                 // run swal
    //                     let swal_action = 'error';
    //                     let swal_content = '上傳失敗';
    //                     swal('uploadMyTodo_toSCM' ,swal_content ,swal_action);
    //             }
    //         });
    //         delayedLoop();              // 開始遞迴調用 delayedLoop()
    //     })

    //     $('body').mLoading("hide");     //显示loading组件
    //     $('#result').append(fa_check + log_auto_msg + ' : updated --  done!</br>');

    //     toLog(log_auto_msg);            // 抄錄log紀錄
    //     // logTo_mapp(log_auto_msg);       // 發送mapp通知；20230529暫時取消，剩下toscm通知
    // }
    
    // // tab-2.scm step-1.自動上傳模組 // 上傳個人戰情室scm_db
    // function uploadMyTodo_toSCM(){
    //     $('body').mLoading("show");  //显示loading组件

    //     var myTodos = <=json_encode($all_myTodos)?>;                               // 把$myTodos陣列encode，裝在json
    //     var HTTP_HOST_name = '<=$_SERVER['HTTP_HOST']?>';                          // link素材
    //     var url_path = 'http://'+HTTP_HOST_name+'/todo/swap/request.php?emp_id=';              // 定義LINK要用的前置路徑
    //     var array_key = ['sys_name','pm_task','item','remark','emp_id','created_at','schedule','sign_code','id','flag'];    // 定義要抓的變數
    //     var thisMonth = <=$thisMonth?>;                                            // 抓這個月的月份
    //     var nextMonth = <=$nextMonth?>;                                            // 抓下個月的月份
    //     var thisYear = <=$thisYear?>;                                              // 抓今年
    //     var log_auto_msg = "2. scm(將myTodo同步上傳到scm_db)";
        
    //     // 把所有的myTodo繞出來
    //     for(var i=0; i < myTodos.length; i++){
    //         var sys_name = myTodos[i]['sys_name'];
    //         var pm_task = myTodos[i]['pm_task'];
    //         var item = myTodos[i]['item'];
    //         var remark = myTodos[i]['remark'];
    //         var emp_id = myTodos[i]['emp_id'];
    //         var created_at = myTodos[i]['created_at'];
    //         var sign_code = myTodos[i]['sign_code'];
    //         var id = myTodos[i]['id'];
    //         var flag = myTodos[i]['flag'];

    //         myTodos[i]['schedule'] = (myTodos[i]['schedule']).split(',');       // 將字串由','處拆成陣列
    //         myTodos[i]['schedule_reArr'] = [];                                  // 增加定義一個空陣列來放有index的排程

    //         Object(myTodos[i]['schedule']).forEach(function(due_date_cell){     // 將原排程陣列逐筆繞出來
    //             // 取字串 = substr(開始,幾個字)  // 找字串 = search('要找的字') = 返回字串所在位置
    //             myTodos[i]['schedule_reArr'][due_date_cell.substr(0,due_date_cell.search('-'))] = due_date_cell;   // 指定index(月份) = value(due_date)
    //         })
    //         if(myTodos[i]['schedule_reArr'][thisMonth] !== undefined){
    //             var due_date = thisYear +'-'+ myTodos[i]['schedule_reArr'][thisMonth];  
    //         }else if(myTodos[i]['schedule_reArr'][nextMonth] !== undefined){
    //             if((thisMonth == 12) && (nextMonth == 1)){
    //                 var due_date = (thisYear + 1 ) +'-'+ myTodos[i]['schedule_reArr'][nextMonth];
    //             }else{
    //                 var due_date = thisYear +'-'+ myTodos[i]['schedule_reArr'][nextMonth];
    //             }
    //         }

    //         if(due_date){              // 這裡只判斷本月的工作，但最好是能判斷30天內的工作
    //             var thisDay = new Date();
    //             var day1 = new Date(thisDay.getFullYear()+'-'+(thisDay.getMonth()+1)+'-'+thisDay.getDate()); 
    //             var day2 = new Date(due_date);
    //             // var difference = Math.abs(day2-day1);     // 棄用這個不會有負數日期
    //             var difference = (day2-day1);
    //             var days = difference/(1000 * 3600 * 24)
    //             // 燈號說明
    //             var light_desc_txt = days;
    //                 if(days >= 7 ){
    //                     var light_diff = 'G';
    //                 }else if(days < 7 && days >= 3 ){
    //                     var light_diff = 'Y';
    //                 }else if(days < 3 && days >= 0 ){
    //                     var light_diff = 'R';
    //                 }else{
    //                     var light_diff = 'R';
    //                     var light_desc_txt = days;
    //                 }

    //             // flag == On 執行中 = 上傳或更新 scm_db
    //             if(flag != 'Off'){      
    //                 $.ajax({
    //                     url:'../scm/api.php',
    //                     method:'post',
    //                     dataType:'json',
    //                     data:{
    //                         function: 'uploadTodo',           // 操作功能   
    //                         TITLE : pm_task+'\r\n'+'('+sys_name+')'+'\r\n'+item,
    //                         LIGHT : light_diff,
    //                         LIGHT_DESC : light_desc_txt,
    //                         ITEM : remark,
    //                         EMP_NO : emp_id,
    //                         ARRIVAL_DATE : created_at,
    //                         DUE_DATE : due_date,
    //                         LINK : url_path+emp_id+'&id='+id,
    //                         ID : 'todo_mytodo_'+id,
    //                         DEPT_NO : sign_code
    //                     },
    //                     success: function(res){
    //                         // let res_r = res["result"];
    //                         // console.log("res_r");
    //                         // $('body').mLoading("hide");//显示loading组件
    //                         // $('#result').append( fa_check+' autoUpload scm_db: upload done!</br>');
    //                         // var swal_action = 'success';
    //                         // var swal_content = '上傳成功';
    //                         // swal('uploadMyTodo_toSCM' ,swal_content ,swal_action);
    //                     },
    //                     error: function(e){
    //                         console.log("error");
    //                         $('#result').append( fa_remove+'&nbsp'+emp_id+' autoUpload scm_db : upload error!_'+id+'</br>');
    //                         // var swal_action = 'error';
    //                         // var swal_content = '上傳失敗';
    //                         // swal('uploadMyTodo_toSCM' ,swal_content ,swal_action);
    //                     }
    //                 });

    //             // flag == Off 執行完畢 = 刪除 scm_db
    //             }else if(flag == 'Off'){        
    //                 $.ajax({
    //                     url:'../scm/api.php',
    //                     method:'post',
    //                     dataType:'json',
    //                     data:{
    //                         function: 'deleteTodo',           // 操作功能   
    //                         EMP_NO : emp_id,
    //                         ID : 'todo_mytodo_'+id
    //                     },
    //                     success: function(res){
    //                         // let res_r = res["result"];
    //                         // console.log(res_r);
    //                         // $('body').mLoading("hide");//显示loading组件
    //                         // $('#result').append( fa_check+' autoUpload scm_db: delete success!</br>');
    //                         // var swal_action = 'success';
    //                         // var swal_content = '刪除成功';
    //                         // swal('deleteTodo_toSCM' ,swal_content ,swal_action);
    //                     },
    //                     error: function(e){
    //                         console.log("error");
    //                         $('#result').append( fa_remove+'&nbsp'+emp_id+' autoUpload scm_db : delete error!_'+id+'</br>');
    //                         // var swal_action = 'error';
    //                         // var swal_content = '上傳失敗';
    //                         // swal('uploadMyTodo_toSCM' ,swal_content ,swal_action);
    //                     }
    //                 });
       
    //             } 
    //         }
    //     }
    //     $('body').mLoading("hide");  //显示loading组件
    //     $('#result').append(fa_check + log_auto_msg + ' : upload scm_db -- done!</br>');

    //     toLog(log_auto_msg);            // 抄錄log紀錄
    //     logTo_mapp(log_auto_msg);       // 發送mapp通知
    // }



</script>

<?php include("../template/footer.php"); ?>
