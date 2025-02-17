
// // // 第n頁：catalog_modal 篩選 function
    // 加入購物車清單
    function add_item(cata_SN, add_amount, swal_flag){
        var swal_title = '加入購物車清單';
        // swal_flag=off不顯示swal、其他是預設1秒
        if(swal_flag == 'off'){
            var swal_time = 0;
        }else{
            var swal_time = 1 * 1000;
        }

        if(add_amount['need'] <= 0 ){
            var swal_content = cata_SN+' 沒有填數量!'+' 加入失敗';
            var swal_action = 'error';
            swal(swal_title ,swal_content ,swal_action);      // swal需要按鈕確認

        }else{
            var check_item_return = check_item(cata_SN, 0);    // call function 查找已存在的項目，並予以清除。form-control
            Object(catalogs).forEach(function(cata){          
                if(cata['SN'] === cata_SN){
                    // 240125-這裡前面加一個fack的checkbox
                    var input_cb = '<input type="checkbox" class="form-check-input" checked disabled ><input type="hidden" name="cata_SN_amount['+cata['SN']+'][need]" id="'+cata['SN']+'" value="'+add_amount['need']+'" >';
                    var add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+'</td><td style="text-align: left;">'+cata['SN']+' / '+cata['pname']+'</td><td>'+cata['model']+'</td><td>'+cata['size']+'</td><td>'+add_amount['need']+' / '+cata['unit']+'</td>';
                    if(receive_collect_role){
                            var amount_need = add_amount['need'];               // 加工：取需求量
                            var amount_need_length = amount_need.length;        // 加工：取需求量的長度
                        add_cata_item += '<td><input type="number" name="cata_SN_amount['+cata['SN']+'][pay]" class="collect amount t-center" placeholder="數量" min="0" ';
                        add_cata_item += ' max="'+add_amount['need']+'" maxlength="'+amount_need_length+'" value="'+add_amount['pay']+'" oninput="if(value>'+amount_need+') value='+amount_need+'" >'+'</td></tr>';
                        // 有發放權，就可以編輯數量
                    }else{
                        add_cata_item += '<td>'+add_amount['pay']+'</td></tr>';
                    }
                    $('#shopping_cart_tbody').append(add_cata_item);
                    return;         // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了
                }
            })
            // 根據check_item_return來決定使用哪個swal型態；true = 有找到數值=更新、false = 沒找到數值=加入
            if(check_item_return){
                var swal_content = ' 更新成功';
                var swal_action = 'info';
            }else{
                var swal_content = ' 加入成功';
                var swal_action = 'success';
            }
            // swal_time>0才顯示swal，主要過濾edit時的渲染導入
            if(swal_time > 0){
                swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:swal_time});        // swal自動關閉
            }
            
        }
        check_shopping_count();
    }

    // 查找購物車清單已存在的項目，並予以清除
    function check_item(cata_SN, swal_time) {
        // swal_time = 是否啟動swal提示 ： 0 = 不啟動
        if(swal_time > 2){
            swal_time = 2;
        }
        var shopping_cart_list = document.querySelectorAll('#shopping_cart_tbody > tr');
        if (shopping_cart_list.length > 0) {
            // 使用for迴圈遍歷NodeList，而不是Object.keys()
            for (var i = 0; i < shopping_cart_list.length; i++) {
                var trElement = shopping_cart_list[i];
                if (trElement.id === 'item_' + cata_SN) {
                    // 從父節點中移除指定的<tr>元素
                    trElement.parentNode.removeChild(trElement);
                    if(swal_time != 0){
                        var swal_title = '移除購物車項目';
                        var swal_content = ' 移除成功';
                        var swal_action = 'warning';
                        swal_time = swal_time * 1000;
                        swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:swal_time});        // swal自動關閉
                    }
                    check_shopping_count();
                    return true; // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了  // true = 有找到數值
                }
            }
        }
        return false;       // false = 沒找到數值
    }
    
    // 清算購物車件數，顯示件數，切換申請單按鈕
    function check_shopping_count(){
        var shopping_cart_list = document.querySelectorAll('#shopping_cart_tbody > tr');
        $('#shopping_count').empty();
        if(shopping_cart_list.length > 0){
            $('#shopping_count').append(shopping_cart_list.length);
        }
    }

// // // 第三頁：searchUser function 
    // fun3-1：search Key_word
    function search_fun(tag_id, search){
        mloading("show");                       // 啟用mLoading

        var fun = tag_id+'_badge';
        $('#'+fun).empty();
        $('#'+fun+'Name').empty();

        search = search.trim();
        if(!search || (search.length < 8)){
            alert("查詢工號字數最少 8 個字以上!!");
            $("body").mLoading("hide");
            return false;
        } 

        $.ajax({
            // url:'http://tneship.cminl.oa/hrdb/api/index.php',        // 正式舊版
            url:'http://tneship.cminl.oa/api/hrdb/index.php',           // 正式2024新版
            method:'post',
            dataType:'json',
            data:{
                functionname: 'showStaff',                              // 操作功能
                uuid: '752382f7-207b-11ee-a45f-2cfda183ef4f',           // ppe
                emp_id: search                                          // 查詢對象key_word  // 使用開單人工號查詢
            },
            success: function(res){
                var res_r = res["result"];
                // 將結果進行渲染
                if (res_r !== '') {
                    var obj_val = res_r;                                         // 取Object物件0

                    if(obj_val){     
                        
                        if(fun == 'omager_badge'){     // 搜尋申請人上層主管emp_id
                            $('#'+fun).append('<div class="tag"> ' + obj_val.cname + '&nbsp</div>');

                        }else if(fun == 'assignSign_badge'){     // 搜尋申請人上層主管emp_id
                            $('#'+fun).append('<div class="tag"> ' + obj_val.cname + '<span class="remove">x</span></div>');
                            document.getElementById(tag_id+'Name').value = obj_val.cname;   // 帶入待簽人姓名

                        }else{
                            $('#'+fun).append('<div class="tag">' + obj_val.cname + '<span class="remove">x</span></div>');
                            document.getElementById('in_signName').value = obj_val.cname;             // 帶入待簽人姓名
                        }
                        
                    }else{
                        alert('查無工號：'+ search +' !!');
                    }
                }
            },
            error (err){
                console.log("search error", err);
            }
        })
        $("body").mLoading("hide");
    }

    // fun3-2：in_sign上層主管：移除單項模組
    $('#in_sign_badge').on('click', '.remove', function() {
        $(this).closest('.tag').remove();                         // 自畫面中移除
        document.getElementById('in_sign').value = '';            // 將欄位cname清除
        document.getElementById('in_signName').value = '';        // 將欄位in_signName清除
        $('#in_sign_badge').empty();
    });
    $('#assignSign_badge').on('click', '.remove', function() {
        console.log(this);
        $(this).closest('.tag').remove();                         // 自畫面中移除
        document.getElementById('assignSign').value = '';            // 將欄位cname清除
        document.getElementById('assignSignName').value = '';        // 將欄位assignSignName清除
        $('#assignSign_badge').empty();
    });

// // // 第三頁：searchUser function 

// // // Edit選染
    // 引入action資料
    // var action = '<?=$action;?>';
    function edit_item(){
        // 引入receive_row資料作為Edit
        // var receive_row = <?=json_encode($receive_row);?>;
        var receive_item = {
            "plant"          : "plant/申請單位", 
            "dept"           : "dept/部門名稱", 
            "sign_code"      : "sign_code/部門代號", 
            "emp_id"         : "emp_id/工號",
            "cname"          : "cname/申請人姓名",
            "extp"           : "extp/分機",
            "ppty"           : "** ppty/需求類別",
            "omager"         : "omager/上層主管工號",
            "receive_remark" : "receive_remark/用途說明",
            "uuid"           : "uuid",
            "cata_SN_amount" : "** cata_SN_amount"
            // "sin_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原陣列逐筆繞出來
        Object.keys(receive_item).forEach(function(receive_key){
            if(receive_key == 'ppty'){                      // ppty/需求類別
                document.querySelector('#'+receive_key+'_'+receive_row[receive_key]).checked = true;
                
            }else if(receive_key == 'cata_SN_amount'){      //cata_SN_amount 購物車
                var receive_row_cart = JSON.parse(receive_row[receive_key]);
                Object.keys(receive_row_cart).forEach(function(cart_key){
                    add_item(cart_key, receive_row_cart[cart_key], 'off');
                })
            }else{
                document.querySelector('#'+receive_key).value = receive_row[receive_key]; 
            }
        })

        // 鋪設logs紀錄
        // var json = JSON.parse('<?=json_encode($logs_arr)?>');
        // var uuid = '<=$receive_row["uuid"]?>';
        var forTable = document.querySelector('.logs tbody');
        for (var i = 0, len = json.length; i < len; i++) {
            json[i].remark = json[i].remark.replaceAll('_rn_', '<br>');   // *20231205 加入換行符號
            forTable.innerHTML += 
                '<tr><td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + 
                    '</td><td style="text-align: left; word-break: break-all;">' + json[i].remark + '</td></tr>';
        }
    }

    // 簽核類型渲染
    function submit_item(idty, idty_title){
        $('#idty, #idty_title, #action').empty();
        document.getElementById('idty').value = idty;
        // idty=99 => return the goods
        document.getElementById('action').value = (receive_row['idty'] == 10 && idty == 99 ) ? 'return' : 'sign';
        $('#idty_title').append(idty_title);
        var forwarded_div = document.getElementById('forwarded');
        if(forwarded_div && (idty == 5)){
            forwarded_div.classList.remove('unblock');           // 按下轉呈 = 解除 加簽
        }else{
            forwarded_div.classList.add('unblock');              // 按下其他 = 隱藏
        }
    }

    // tab_table的顯示關閉功能
    function op_tab(tab_value){
        $("#"+tab_value+"_btn .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
        var tab_table = document.getElementById(tab_value+"_table");
        if (tab_table.style.display === "none") {
            tab_table.style.display = "table";
        } else {
            tab_table.style.display = "none";
        }
    }
    
    // 2023/10/25 將領用申請單推送給按push的人~
    function push_mapp(emp_id){

        // emp_id = emp_id.trim();
        if(!emp_id || (emp_id.length < 8)){
            alert("工號字數有誤 !!");
            $("body").mLoading("hide");
            return false;
        } 

        receive_msg = sort_receive();       // 呼叫fun 取得整理的文字串

        $.ajax({
            // url:'http://10.53.248.167/SendNotify',                           // 20230505 正式修正要去掉port 801
            url:'http://tneship.cminl.oa/api/pushmapp/index.php',               // 正式2024新版
            method:'post',
            async: false,                                                       // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                uuid    : '752382f7-207b-11ee-a45f-2cfda183ef4f',               // ppe
                eid     : emp_id,                                               // 傳送對象
                message : receive_msg                                           // 傳送訊息
            },
            success: function(res){
                console.log("push_mapp -- success：",res);
                swal_content = '推送成功';
                swal_action = 'success';
            },
            error: function(res){
                console.log("push_mapp -- error：",res);
                swal_content = '推送失敗';
                swal_action = 'error';
            }
        });
        
        var swal_title = '領用申請單-發放訊息';
        swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:2000});        // swal自動關閉
        $("body").mLoading("hide");
        
        return;
    }
    
    // 2023/10/25 整理領用申請單內訊息給mapp用
    function sort_receive(){
        
        // get領用地點
            var getLocal_id = document.getElementById('local_id');
            if(getLocal_id){
                var collect_local = getLocal_id.value;
            }else{
                var collect_local = '(請查閱領用申請)';
            }
        // get購物車數量
            var getShopping_cart = document.getElementById('shopping_count');
            if(getShopping_cart){
                var shopping_count = getShopping_cart.innerText;
            }else{
                var shopping_count = '(請查閱領用申請)';
            }

        var receive_row_cart = JSON.parse(receive_row['cata_SN_amount']);   // get申請單品項數量
        var i_cunt = 1;                                                     // 各品項前的計數
        var add_cata_item = '[ PPE領用申請 - '+action+' ]';
        add_cata_item += '\n申請日期：'+receive_row['created_at'];
        add_cata_item += '\n申請單位：'+receive_row['plant'];
        add_cata_item += '\n申請人：'+receive_row['cname']+'  分機：'+receive_row['extp'];
        add_cata_item += '\n領用地點：'+collect_local;
        
        Object.keys(receive_row_cart).forEach(function(cart_key){
            Object(catalogs).forEach(function(cata){          
                if(cata['SN'] === cart_key){
                    add_cata_item += '\n'+i_cunt+'.SN:'+cata['SN']+' / '+cata['pname'];
                    add_cata_item += '\n'+i_cunt+'.型號:'+cata['model']+' / Size:'+cata['size']+' / 數量：'+receive_row_cart[cart_key]['need']+' '+cata['unit']+'\n';
                    i_cunt += 1;
                    return;         // 對應到一筆資料就可以結束迴圈了
                }
            })
        })

        add_cata_item += '\n以上共：'+shopping_count +' 品項';
        add_cata_item += '\n文件連結：'+receive_url;
        
        return add_cata_item;
    }

    // 20240429_return the goods
    function return_the_goods(){
        let receive_row_cart = JSON.parse(receive_row['cata_SN_amount']);
        Object.keys(receive_row_cart).forEach(function(cart_key){
            check_item(cart_key, 0);                        // call function 查找已存在的項目，並予以清除。form-control
            let add_amount = receive_row_cart[cart_key];
            Object(catalogs).forEach(function(cata){          
                if(cata['SN'] === cart_key){
                    let input_cb = '<input type="checkbox" class="form-check-input" checked disabled ><input type="hidden" name="cata_SN_amount['+cata['SN']+'][need]" id="'+cata['SN']+'" value="'+add_amount['need']+'" >';
                    let add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+'</td><td style="text-align: left;">'+cata['SN']+' / '+cata['pname']+'</td><td>'+cata['model']+'</td><td>'+cata['size']+'</td><td>'+add_amount['need']+' / '+cata['unit']+'</td>';
                    let amount_pay = add_amount['pay'];               // 加工：取實發量
                    let amount_pay_length = amount_pay.length;        // 加工：取實發量的長度
                    add_cata_item += '<td><input type="number" name="cata_SN_amount['+cata['SN']+'][pay]" class="collect amount t-center" placeholder="數量" min="0" ';
                    add_cata_item += ' max="'+amount_pay+'" maxlength="'+amount_pay_length+'" value="'+amount_pay+'" oninput="if(value>'+amount_pay+') value='+amount_pay+'" >'+'</td></tr>';
                    $('#shopping_cart_tbody').append(add_cata_item);
                    return;         // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了
                }
            })
        })
        // 鋪設按鈕
        let let_btn_s = '<button type="button" class="btn ';
        let let_btn_m = '" data-bs-toggle="modal" data-bs-target="#submitModal" value="';
        let let_btn_e = '" onclick="submit_item(this.value, this.innerHTML);">';
        $('#form_btn_div').empty().append(let_btn_s+"btn-success"+let_btn_m+"99"+let_btn_e+"同意退貨 (Approve)</button>");

        // 預設數值 //id="sign_comm_label"
        $("#sign_comm_label").append('<sup class="text-danger"> *</sup>');
        $("#sign_comm").prop('required', true);

        //swal 顯示
        let swal_title = '退貨編輯功能';
        let swal_content = '開啟成功';
        let swal_action = 'warning';
        swal_time = 5 * 1000;
        swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:swal_time});        // swal自動關閉
    }

    // 20240529 確認自己是否為彈出視窗 !! 只在完整url中可運行 = tw123456p.cminl.oa
    function checkPopup() {
        var urlParams = new URLSearchParams(window.location.search);
        if ((urlParams.has('popup') && urlParams.get('popup') === 'true') || (window.opener) || (sessionStorage.getItem('isPopup') === 'true')) {
            console.log('popup');
            sessionStorage.removeItem('isPopup');

            let nav = document.querySelector('nav');                // 獲取 <nav> 元素
                nav.classList.add('unblock');                           // 添加 'unblock' class

            let rtn_btns = document.querySelectorAll('.rtn_btn');   // 獲取所有帶有 'rtn_btn' class 的按鈕
                rtn_btns.forEach(function(btn) {                        // 遍歷這些按鈕，並設置 onclick 事件
                    btn.onclick = function() {
                        closeWindow();                                  // true=更新 / false=不更新
                    };
                });
        }else{
            console.log('main');
        }
    }

    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();

        // 20230817 禁用Enter鍵表單自動提交 
        document.onkeydown = function(event) { 
            var target, code, tag; 
            if (!event) { 
                event = window.event;       //針對ie瀏覽器 
                target = event.srcElement; 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "TEXTAREA") { return true; } 
                    else { return false; } 
                } 
            } else { 
                target = event.target;      //針對遵循w3c標準的瀏覽器，如Firefox 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "INPUT") { return false; } 
                    else { return true; } 
                } 
            } 
        };
    })
    

    $(document).ready(function () {
        
        checkPopup();

        edit_item();                                            // 1.啟動鋪設畫面

        omager_id = document.getElementById("omager").value     // 2.提取上層主管工號
        if(omager_id){
            search_fun('omager', omager_id)                     // 3.查詢工號並鋪設姓名
        }
        // 20240430 退貨按鈕消失
        if(receive_row['idty'] == '10'){                        // 4.if 10結案 then disabled & unblock this return_btn
            let now_time    = new Date();
            let flow_m      = new Date(receive_row['flow'].split(',')[0]);
            let difference  = now_time - flow_m;                // 計算日期差距（毫秒單位）
            let days        = Math.floor((difference % (30.44 * 24 * 60 * 60 * 1000)) / (24 * 60 * 60 * 1000));
            if (days >= 14) {                                   // over 14days then do it 
                $('#return_btn').prop('disabled', true) // .addClass("unblock");
            }
        }

        // p1-3. 增加指派簽核鈕的監聽動作...
        assignSignSubmit_btn.addEventListener('click', async function() {
            mloading();
            const getValue = id => document.querySelector(`#assignSignModal input[id="${id}"]`).value;              // 取得數值
            const setValue = newOmger_str => document.querySelector(`snap[id="newSign"]`).innerText = newOmger_str; // 指定顯示內容
            const newSignEmpID = getValue('assignSign');        // 取得新簽核工號
            if (newSignEmpID) {
                const newSignName = getValue('assignSignName'); // 取得新簽核姓名
                const newOmger_str = `指派簽核：${newSignName} (${newSignEmpID})`; // 組合字串
                setValue(newOmger_str);                         // 指定snap顯示新簽核訊息內容
                inside_toast(newOmger_str, 5000, 'warning');    // 調用toast顯示新簽核訊息內容
                assignSign_modal.hide();                      // 關閉modal

            } else {
                alert('沒有新簽核工號訊息 !!');
            }
            
            $('#assignSign_Badge').empty();                      // 清除modal畫面中Badge
            $('#assignSign, #assignSignName').val('');          // 清除modal中empId&cName
            // console.log('assignSign =>', staff_inf);
            $("body").mLoading("hide");
        })

    })