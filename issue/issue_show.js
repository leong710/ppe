

    // 加入購物車清單
    function add_item(cata_SN, add_amount, swal_flag){
        var swal_title = '加入購物車清單';
        // swal_flag=off不顯示swal、其他是預設1秒
        if(swal_flag == 'off'){
            var swal_time = 0;
        }else{
            var swal_time = 1 * 1000;
        }

        if(add_amount <= 0 ){
            var swal_content = cata_SN+' 沒有填數量!'+' 加入失敗';
            var swal_action = 'error';
            swal(swal_title ,swal_content ,swal_action);      // swal需要按鈕確認

        }else{
            var check_item_return = check_item(cata_SN, 0);    // call function 查找已存在的項目，並予以清除。
            Object(catalogs).forEach(function(cata){          
                if(cata['SN'] === cata_SN){
                    // 240125-這裡前面加一個fack的checkbox
                    var input_cb = '<input type="checkbox" class="select_item" checked disabled ><input type="hidden" name="item['+cata['SN']+'][need]" id="'+cata['SN']+'" value="'+add_amount['need']+'" >';
                    var add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+'</td><td style="text-align: left;">'+cata['SN']+' / '+cata['pname']+'</td><td>'+cata['model']+'</td><td>'+cata['size']+'</td><td>'+add_amount['need']+' / '+cata['unit']+'</td>';
                    if(issue_collect_role){
                            var amount_need = add_amount['need'];               // 加工：取需求量
                            var amount_need_length = amount_need.length;        // 加工：取需求量的長度
                        add_cata_item += '<td><input type="number" name="item['+cata['SN']+'][pay]" class="collect amount t-center" placeholder="數量" min="0" ';
                        add_cata_item += ' value="'+add_amount['pay']+'" oninput="if(value>'+amount_need+') value='+amount_need+'" >'+'</td></tr>';
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
        if(!swal_time){
            swal_time = 1;
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
            url:'http://tneship.cminl.oa/hrdb/api/index.php',       // 正式
            method:'get',
            dataType:'json',
            data:{
                functionname: 'showStaff',                          // 操作功能
                uuid: '752382f7-207b-11ee-a45f-2cfda183ef4f',       // ppe
                search: search                                      // 查詢對象key_word
            },
            success: function(res){
                var res_r = res["result"];

                // 將結果進行渲染
                if (res_r !== '') {
                    var obj_val = res_r;                                         // 取Object物件0

                    if(obj_val){     
                        
                        if(fun == 'omager_badge'){     // 搜尋申請人上層主管emp_id
                            $('#'+fun).append('<div class="tag"> ' + obj_val.cname + '&nbsp</div>');
                        }else{
                            $('#'+fun).append('<div class="tag">' + obj_val.cname + '<span class="remove">x</span></div>');
                            document.getElementById('in_signName').value = obj_val.cname;             // 帶入待簽人姓名
                        }
                        
                    }else{
                        alert('查無工號：'+ search +' !!');
                    }
                }
            },
            error (){
                console.log("search error");
            }
        })
        $("body").mLoading("hide");
    }

    // fun3-2：in_sign上層主管：移除單項模組
    $('#in_sign_badge').on('click', '.remove', function() {
        $(this).closest('.tag').remove();   // 自畫面中移除
        document.getElementById('in_sign').value = '';            // 將欄位cname清除
        document.getElementById('in_signName').value = '';        // 將欄位in_signName清除
        $('#in_sign_badge').empty();
    });

// // // 第三頁：searchUser function 


// // // Edit選染
    function edit_item(){
        // 引入issue_row資料作為Edit
        // var issue_row = <?=json_encode($issue_row);?>;
        var issue_item = {
            "in_user_id"     : "in_user_id/工號",
            "cname_i"        : "cname_i/申請人姓名",
            "extp"           : "extp/分機",
            "plant"          : "plant/申請單位", 
            "dept"           : "dept/部門名稱", 
            "sign_code"      : "sign_code/部門代號", 
            "in_local"       : "in_local/領用站點",
            "ppty"           : "** ppty/需求類別",
            "omager"         : "omager/上層主管工號",
            "issue_remark"   : "issue_remark/用途說明",
            "id"             : "id",
            "item"           : "** item"
            // "sign_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原陣列逐筆繞出來
        Object.keys(issue_item).forEach(function(issue_key){
            if(issue_key == 'ppty'){                      // ppty/需求類別
                var ppty = document.querySelector('#'+issue_key+'_'+issue_row[issue_key]);
                if(ppty){
                    ppty.checked = true;
                }
                
            }else if(issue_key == 'item' && issue_row[issue_key]){      //item 購物車
                var issue_row_cart = JSON.parse(issue_row[issue_key]);
                Object.keys(issue_row_cart).forEach(function(cart_key){
                    add_item(cart_key, issue_row_cart[cart_key], 'off');
                })
            }else if(issue_row[issue_key]){
                var row_key = document.querySelector('#'+issue_key);
                if(row_key){
                    row_key.value = issue_row[issue_key]; 
                }
            }
        })

        // 鋪設logs紀錄
        // var id = '<=$issue_row["id"]?>';
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
        $('#idty, #idty_title, #action, #po_no_form, #sign_comm').empty();
        document.getElementById('action').value = 'sign';
        document.getElementById('idty').value = idty;
        $('#idty_title').append(idty_title);
        var forwarded_div = document.getElementById('forwarded');
        if(forwarded_div && (idty == 5)){
            forwarded_div.classList.remove('unblock');           // 按下轉呈 = 解除 加簽
        }else{
            forwarded_div.classList.add('unblock');              // 按下其他 = 隱藏
        }

        var po_no_form = document.getElementById('po_no_form');
        var po_no_input = '<label for="po_no" class="form-label">PO編號：<sup class="text-danger"> *</sup></label>';
            po_no_input += '<input type="text" name="po_no" id="po_no" class="form-control t-center" placeholder="請填PO編號" maxlength="12" required>';
        var sign_comm = document.getElementById('sign_comm');

        if(po_no_form && (idty == 13)){
            $('#po_no_form').append(po_no_input);
            po_no_form.classList.remove('unblock');           // 按下轉呈 = 解除 加簽
            sign_comm.value = '(請購入庫)';
        }else{
            po_no_form.classList.add('unblock');              // 按下其他 = 隱藏
            sign_comm.value = '';
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
    
    // 2023/10/25 將請購需求單推送給按push的人~
    function push_mapp(emp_id){

        emp_id = emp_id.trim();
        if(!emp_id || (emp_id.length < 8)){
            alert("工號字數有誤 !!");
            $("body").mLoading("hide");
            return false;
        } 

        issue_msg = sort_issue();       // 呼叫fun 取得整理的文字串

        $.ajax({
            url:'http://10.53.248.167/SendNotify',                              // 20230505 正式修正要去掉port 801
            method:'post',
            async: false,                                                       // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                eid : emp_id,                                                   // 傳送對象
                message : issue_msg                                           // 傳送訊息
            },
            success: function(res){
                console.log("push_mapp -- success：",res);
                // swal_content = '推送成功';
                // swal_action = 'success';
            },
            error: function(res){
                console.log("push_mapp -- error：",res);
                // swal_content = '推送失敗';
                // swal_action = 'error';
            }
        });
        
        var swal_title = '請購需求單-發放訊息';
        var swal_content = '推送成功';
        var swal_action = 'success';
        swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:2000});        // swal自動關閉
        $("body").mLoading("hide");
        
        return;
    }
    
    // 2023/10/25 整理請購需求單內訊息給mapp用
    function sort_issue(){
        
        // get領用地點
            var getLocal_id = document.getElementById('local_id');
            if(getLocal_id){
                var collect_local = getLocal_id.value;
            }else{
                var collect_local = '(請查閱請購需求)';
            }
        // get購物車數量
            var getShopping_cart = document.getElementById('shopping_count');
            if(getShopping_cart){
                var shopping_count = getShopping_cart.innerText;
            }else{
                var shopping_count = '(請查閱請購需求';
            }

        var issue_row_cart = JSON.parse(issue_row['item']);                 // get申請單品項數量
        var i_cunt = 1;                                                     // 各品項前的計數
        var add_cata_item = '[ PPE請購需求 - '+action+' ]';
            add_cata_item += '\n申請日期：'+issue_row['create_date'];
            add_cata_item += '\n申請人：'+issue_row['cname_i'];
            add_cata_item += '\n需求廠區：'+collect_local;
        
        Object.keys(issue_row_cart).forEach(function(cart_key){
            Object(catalogs).forEach(function(cata){          
                if(cata['SN'] === cart_key){
                    add_cata_item += '\n'+i_cunt+'.SN:'+cata['SN']+' / '+cata['pname'];
                    add_cata_item += '\n'+i_cunt+'.型號:'+cata['model']+' / Size:'+cata['size']+' / 數量：'+issue_row_cart[cart_key]['need']+' '+cata['unit']+'\n';
                    i_cunt += 1;
                    return;         // 對應到一筆資料就可以結束迴圈了
                }
            })
        })
        add_cata_item += '\n以上共：'+shopping_count +' 品項';
        add_cata_item += '\n文件連結：'+issue_url;
        
        return add_cata_item;
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

        edit_item();        // 啟動鋪設畫面

        omager_id = document.getElementById("omager").value     // 2.提取上層主管工號
        if(omager_id){
            search_fun('omager', omager_id)                     // 3.查詢工號並鋪設姓名
        }

    })


