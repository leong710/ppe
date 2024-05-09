 
    // 子功能
        $(function () {
            // 在任何地方啟用工具提示框
            $('[data-toggle="tooltip"]').tooltip();
        })
        // fun_1 tab_table的顯示關閉功能
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
            let delayTime = 1000;                   // 1000=1秒
            let i = 15;                             // 15次==15秒
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
                i = 10;                                 // 10次==10秒
            }
            const loop = () => {
                if (i >= 0) {
                    document.getElementById("myMessage").innerHTML = "Fun: "+ callback +" 執行倒數 "+ i +" 秒";
                    setTimeout(loop, 1000);
                } else {
                    document.getElementById("myMessage").innerHTML = "Fun: "+ callback +" 執行！";
                    window[callback]();                 // 要執行的程式
                }
                i--;
            };
            loop();
        }

    // 主技能
        // 20231213 寫入log記錄檔~
        function toLog(logs_msg){
            $.ajax({
                url      : '../autolog/log.php',
                method   : 'post',
                async    : false,
                dataType : 'json',
                data     : {
                    function : 'storeLog',
                    thisDay  : thisToday,
                    sys      : 'ppe',
                    logs     : logs_msg,
                    t_stamp  : ''
                },
                success: function(res){
                    toLog_result_check = true; 
                },
                error: function(res){
                    console.log("toLog -- error：", res);
                    toLog_result_check = false;
                }
            });
            return toLog_result_check;
        }
        // 20240314 將訊息推送到TN PPC(mapp)給對的人~
        function push_mapp(user_emp_id, mg_msg) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url:'http://tneship.cminl.oa/api/pushmapp/index.php',       // 正式2024新版
                    method:'post',
                    async: false,                                               // ajax取得數據包後，可以return的重要參數
                    dataType:'json',
                    data:{
                        uuid    : '752382f7-207b-11ee-a45f-2cfda183ef4f',       // ppe
                        eid     : user_emp_id,                                  // 傳送對象
                        message : mg_msg                                        // 傳送訊息
                    },
                    success: function(res){
                        var mapp_result_check = true; 
                        resolve(true);                                          // 成功時解析為 true 
                    },
                    error: function(res){
                        var mapp_result_check = false;
                        console.log("push_mapp -- error：",res);
                        reject(false);                                          // 失敗時拒絕 Promise
                    }
                });
                return mapp_result_check;
            });
        }
        // 20240314 將訊息郵件發送給對的人~
        function sendmail(user_email, mg_msg){
            return new Promise((resolve, reject) => {
                $.ajax({
                    url:'http://tneship.cminl.oa/api/sendmail/index.php',       // 正式2024新版
                    method:'post',
                    async: false,                                               // ajax取得數據包後，可以return的重要參數
                    dataType:'json',
                    data:{
                        uuid    : '752382f7-207b-11ee-a45f-2cfda183ef4f',       // ppe
                        sysName : 'PPE',                                        // 貫名
                        to      : user_email,                                   // 傳送對象
                        subject : int_msg1,                                     // 信件標題
                        body    : mg_msg                                        // 訊息內容
                    },
                    success: function(res){
                        var mail_result_check = true; 
                        resolve(true);                                          // 成功時解析為 true 
                    },
                    error: function(res){
                        var mail_result_check = false;
                        console.log("send_mail -- error：",res);
                        reject(false);                                          // 失敗時拒絕 Promise
                    }
                });
                return mail_result_check;
            });
        }
        // 20240314 search user_empid return email
        function search_fun(search){
            mloading("show");                       // 啟用mLoading
            var comid2 = null;

            if(!search || (search.length < 8)){
                alert("查詢工號字數最少 8 個字以上!!");
                $("body").mLoading("hide");
                return false;
            } 

            $.ajax({
            // url:'http://tneship.cminl.oa/hrdb/api/index.php',        // 正式舊版
                url:'http://tneship.cminl.oa/api/hrdb/index.php',           // 正式2024新版
                method:'post',
                async: false,                                               // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    functionname : 'showStaff',                             // 操作功能
                    uuid         : '752382f7-207b-11ee-a45f-2cfda183ef4f',  // ppe
                    emp_id       : search                                   // 查詢對象key_word  // 使用開單人工號查詢
                },
                success: function(res){
                    var obj_val = res["result"];
                    // 將結果進行渲染
                    if (obj_val !== '') {
                        comid2 = obj_val.comid2;
                        $('#id_'+search).append(' '+comid2);                // 將欄位帶入數值comid2 = email
                        $("body").mLoading("hide");
                        return comid2;

                    }else{
                        // alert("查無工號["+search+"]!!");
                        console.log("查無工號["+search+"]!!");
                    }
                },
                error(err){
                    console.log("search error:", err);
                }
            })
            $("body").mLoading("hide");
            return comid2;
        }
        // 20240314 配合await將swal外移
        function show_swal_fun(user_logs, push_result){
            // 打包整理Logs的陣列
            user_logs_obj = {
                thisDay  : thisToday,
                autoLogs : user_logs
            }
            user_logs_json = JSON.stringify(user_logs_obj);                                   // logs大陣列轉JSON字串
            toLog(user_logs_json);                                                            // *** call fun.step_2 寫入log記錄檔

            // swal組合訊息，根據發送結果選用提示內容與符號
            var swal_title = '領用申請單-發放訊息';
            
            if((push_result['email']['error'] == 0) && (push_result['email']['success'] != 0)){
                var swal_content = '寄送成功：'+ push_result['email']['success'];
                var swal_action = 'success';
            } else if((push_result['email']['error'] != 0) && (push_result['email']['success'] == 0)){
                var swal_content = '寄送失敗：'+ push_result['email']['error'];
                var swal_action = 'error';
            } else {
                var swal_content = '寄送成功：'+ push_result['email']['success'] +'、錯誤：'+ push_result['email']['error'];
                var swal_action = 'warning';
            }

            if((push_result['mapp']['error'] == 0) && (push_result['mapp']['success'] != 0)){
                swal_content += ' 、 推送成功：'+ push_result['mapp']['success'];
                swal_action = 'success';
            } else if((push_result['mapp']['error'] != 0) && (push_result['mapp']['success'] == 0)){
                swal_content += ' 、 推送失敗：'+ push_result['mapp']['error'];
                swal_action = 'error';
            } else {
                swal_content += ' 、 推送成功：'+ push_result['mapp']['success'] +'、錯誤：'+ push_result['mapp']['error'];
                swal_action = 'warning';
            }

            $("body").mLoading("hide");                                                       // 關閉mLoading圖示
            swal(swal_title ,swal_content ,swal_action, {timer:5000});                        // popOut swal + 自動關閉
        }

        // 2024/05/09 notify_insign()整理訊息、發送、顯示發送結果。
        function notify_insign(){
            var user_logs = [];                                                 // 宣告儲存Log用的 大-陣列Logs
            $('#result').empty();                                               // 清空執行訊息欄位        
            if(inSign_lists && inSign_lists.length >= 1){                       // 有件數 > 0 舊執行通知
                var promises = [];                                              // 存储所有异步操作的 Promise
                var totalUsers = inSign_lists.length;                           // 总用户数量
                var completedUsers = 0;                                         // 已完成发送操作的用户数量

                // step.0 逐筆把清單繞出來
                Object(inSign_lists).forEach(function(user){
                    var user_emp_id = String(user['emp_id']).trim();            // 定義 user_emp_id + 去空白
                    var user_email  = String(user['email']).trim();             // 定義 user_email + 去空白
                    var user_mapp   = (user['ppty_3_count'] > 0) ? true : false;// 當3急件數量!=0，就使用mapp加急通知!

                    var user_log = {                                            // 宣告儲存Log內的單筆 小-物件log
                        emp_id          : user['emp_id'],
                        cname           : user['cname'],
                        email           : user_email,
                        issue_waiting   : user['issue_waiting'],
                        receive_waiting : user['receive_waiting'],
                        waiting         : user['total_waiting'],
                        emergency       : user['ppty_3_count'],
                    }

                    // step.1 確認工號是否有誤
                    if(!user_emp_id || (user_emp_id.length < 8)){
                        // alert("工號字數有誤 !!");                            // 避免無人職守時被alert中斷，所以取消改console.log
                        // $("body").mLoading("hide");
                        console.log("工號字數有誤：", user_emp_id);
                        push_result['mapp']['error']++; 
                        push_result['email']['error']++; 
                        return false;

                    } else {
                        // step.1-1 組合訊息文字
                        var mg_msg  = int_msg1;
                            mg_msg += "(" + user['cname'] + ")";
                            mg_msg += int_msg2 + user['total_waiting'] + int_msg3 + '(';    // 20240112 新添加 請購和領用
                            if(user['issue_waiting'] > 0){
                                mg_msg += '請購'+user['issue_waiting']+'件'
                            }
                            if(user['receive_waiting'] > 0){
                                if(user['issue_waiting'] > 0){
                                    mg_msg += '、';
                                }
                                mg_msg += '領用'+user['receive_waiting']+'件'
                            }
                            mg_msg += (user['ppty_3_count'] > 0) ? '、急件'+user['ppty_3_count']+'件)' : ')';
                            mg_msg += int_msg4 + receive_url +'\n'+issue_url + int_msg5;
                        user_log['mg_msg']   = mg_msg;                                      // 小-物件log 紀錄mg_msg訊息
                        user_log['thisTime'] = thisTime;                                    // 小-物件log 紀錄thisTime

                        // step.2 執行通知 --
                        // *** 2-1 發送mail
                        const mail_result_check = async () => {
                            // *** call fun.step_1 將訊息推送到TN PPC(mail)給對的人~
                            let mail_result_check = (user_email) ? await sendmail(user_email, mg_msg) : false;
                            return mail_result_check;
                        };

                        // *** 2-2 發送mapp
                        const mapp_result_check = async () => {
                            // *** call fun.step_1 將訊息推送到TN PPC(mapp)給對的人~
                            let mapp_result_check = (user_mapp) ? await push_mapp(user_emp_id, mg_msg) : false;
                            return mapp_result_check;
                        };

                        // step.3 存储每个用户的异步操作 Promise
                        promises.push(
                            // 等待mapp_result_check()和mail_result_check()都完成后再执行自定义的代码
                            Promise.all([mapp_result_check(), mail_result_check()])
                            .then(results => {
                                const [mappResult, mailResult] = results;
                                // 处理 mapp/mail 结果 // 標記結果顯示OK或NG，並顯示執行訊息
                                    var action_id = document.querySelector('#id_' + user_emp_id);
                                // mail處理
                                    if(user_email){
                                        user_log.mail_res = mailResult ? 'OK' : 'NG';
                                        mailResult ? push_result['email']['success']++ : push_result['email']['error']++; 
                                        var fa_icon_mail = window['mail_' + user_log.mail_res];
                                        action_id.innerHTML = fa_icon_mail + action_id.innerText;
                                        console_log = user.cname + " (" + user.emp_id + ")" + ' ...  sendMail： ' + fa_icon_mail + user_log.mail_res;
                                    }
                                // mapp處理
                                    if(user_mapp){
                                        user_log.mapp_res = mappResult ? 'OK' : 'NG';
                                        mappResult ? push_result['mapp']['success']++ : push_result['mapp']['error']++; 
                                        var fa_icon_mapp = window['fa_' + user_log.mapp_res];
                                        action_id.innerHTML = fa_icon_mapp + fa_icon_mail + action_id.innerText;
                                        console_log += '  /  pushMapp： ' + fa_icon_mapp + user_log.mapp_res;
                                    }

                                // 自定义的代码在这里执行 -- 執行訊息渲染                                                           
                                    $('#result').append(console_log + '</br>');

                                // 这里可以执行其他自定义的操作
                                user_logs.push(user_log);                                               // 將log單筆小物件 塞入 logs大陣列中

                                completedUsers++;                                                       // 增加已完成发送操作的用户数量
                                if (completedUsers == totalUsers) {                                     // 检查是否所有用户的发送操作都已完成
                                    show_swal_fun(user_logs, push_result);                              // 所有发送操作完成后调用 show_swal_fun
                                }
                            })
                            .catch(error => {
                                console.log('Error:', error);
                            })
                        );
                    }
                });

            }else{                                                                          // 沒件數 == 0 就不用執行通知，但依樣要生成Log
                var user_log = {                                                            // 宣告儲存Log內的單筆 小-物件log
                    emp_id   : '',
                    cname    : '',
                    waiting  : '0',
                    mg_msg   : '(無待簽文件)',
                    mapp_res : 'OK',
                    thisTime : thisTime
                }
                user_logs.push(user_log);                                                   // 將log單筆小物件 塞入 logs大陣列中 
                $('#result').append(fa_OK + '(無待簽文件) ... done'+'</br>');                // 插入下方顯示
                show_swal_fun(user_logs, push_result);                                      // 没有用户需要通知时直接调用 show_swal_fun
            }
            
            // 將其歸零，避免汙染
            push_result = {
                'mapp' : {
                    'success' : 0,
                    'error'   : 0
                },
                'email' : {
                    'success' : 0,
                    'error'   : 0
                }
            }

            $("body").mLoading("hide");                                                         // 關閉mLoading圖示
        }

    $(function () {
        // 把所有名單上的人頭代上email
        for(i=0; i < inSign_lists.length; i++ ){
            q_emp_id = inSign_lists[i]['emp_id'];
            q_email = search_fun(q_emp_id);
            if(q_email){
                inSign_lists[i]['email'] = q_email;
            }
        }
    })

    // fun啟動自動執行
    $(document).ready( function () {
        // op_tab('user_lists');   // 關閉清單
        if(check_ip && fun){
            switch (fun) {
                case 'notify_insign':         // MAPP待簽發報
                    (async () => {
                        await notify_insign();     // 等 func1 執行完畢  // notify_insign 整理訊息、發送、顯示發送結果。
                        CountDown();        // 當 func1 執行完畢後才會執行 func2    // 倒數 n秒自動關閉視窗~
                    })();
                    break;

                default:
                    $('#result').append('等待發報 : function error!</br>');
            }

        }else{
            $('#result').append('等待發報 : ...standBy...</br>');
        }
    } );