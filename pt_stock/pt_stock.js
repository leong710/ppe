
// // // utility fun
    // Bootstrap Alarm function
    function alert(message, type) {
        var alertPlaceholder = document.getElementById("liveAlertPlaceholder")      // Bootstrap Alarm
        var wrapper = document.createElement('div')
        wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message 
                            + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        alertPlaceholder.append(wrapper)
    }
    // fun3-3：吐司顯示字條 // init toast
    function inside_toast(sinn){
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }
    // 選擇local時，取得該local的low_level
    function select_local(local_id){
        Object(allLocals).forEach(function(aLocal){
            if(aLocal['id'] == local_id){
                low_level = JSON.parse(aLocal['low_level']);            // 引入所選local的low_level值
            }
        })
        // 預防已經先選了器材，進行防錯
        var select_cata_SN = document.getElementById('edit_cata_SN').value;  // 取得器材的選項值
        if(select_cata_SN != null){                                     // 假如器材已經選擇了
            update_standard_lv(select_cata_SN);                         // 就執行取得low_level對應值
        }
    }
    // 選擇器材，並更新low_level值
    function update_standard_lv(catalog_SN){
        var standard_lv = document.getElementById('edit_standard_lv');       // 定義standard_lv主體
        // var low_level = JSON.parse('<=json_encode($low_level);?>');    // 引入所local的low_level值
        if(low_level[catalog_SN] == null){
            standard_lv.value = 0;                                      // 預防low_level對應值是null
        }else{
            standard_lv.value = low_level[catalog_SN];                  // 套用對應cata_SN的low_level值
        }
    }
    // 變更按鈕樣態
    function change_btn(target){
        var toggle_btn = document.getElementById(target+'_toggle_btn');
        var lot_num = document.getElementById(target+'_lot_num');

        if (lot_num.value == '') {
            // 输入字段为空
            toggle_btn.innerText = '永久';
            toggle_btn.classList.remove('btn-secondary');
            toggle_btn.classList.add('btn-warning', 'text-dark');
        } else {
            // 输入字段有值
            toggle_btn.innerText = '清除';
            toggle_btn.classList.remove('btn-warning', 'text-dark');
            toggle_btn.classList.add('btn-secondary');
        }
    }
    // 變更lot_num數值
    function chenge_lot_num(target){
        var lot_num = document.getElementById(target+'_lot_num');
        if(lot_num.value =='') {
            lot_num.value = '9999-12-31';
        }else{
            lot_num.value = '';
        }
        change_btn(target);
    };

    $(function(){
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
        // swl function    
        if(swal_json.length != 0){
            if(swal_json['action'] == 'error'){
                swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});
            }else{
                var sinn = 'submit - ( '+swal_json['fun']+' : '+swal_json['content']+' ) <b>'+ swal_json['action'] +'</b>&nbsp!!';
                inside_toast(sinn);
            }

        }
        // 20230131 新增保存日期為'永久'    20230714 升級合併'永久'、'清除'
        // 監聽lot_num是否有輸入值，跟著改變樣態
        $('#edit_lot_num').on('input', function() {
            change_btn('edit');
        });

    });
    
// // // add mode function
    function add_module(to_module){     // 啟用新增模式
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_ptstock_info').empty();     // 清除model功能
        $('#reset_btn').click();                                                            // reset清除表單
        var add_btn = '<input type="submit" name="add_ptstock_submit" class="btn btn-primary" value="新增">';
        $('#modal_action').append('新增');                      // model標題
        $('#modal_button').append(add_btn);                     // 儲存鈕
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.remove('unblock');                  // 新增模式 = 解除
        document.querySelector('#edit_'+to_module+' .modal-header').classList.remove('edit_mode_bgc');
        document.querySelector('#edit_'+to_module+' .modal-header').classList.add('add_mode_bgc');
    }
// // // edit mode function
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_ptreceive_info, #shopping_cart_tbody').empty();     // 清除model功能
        $('#reset_btn').click();                                                            // reset清除表單
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.add('unblock');                     // 編輯模式 = 隱藏
        document.querySelector('#edit_'+to_module+' .modal-header').classList.remove('add_mode_bgc');
        document.querySelector('#edit_'+to_module+' .modal-header').classList.add('edit_mode_bgc');
        // remark: to_module = 來源與目的 site、fab、local
        // step1.將原排程陣列逐筆繞出來
        Object(window[to_module]).forEach(function(row){          
            if(row['id'] == row_id){
                // step2.鋪畫面到module
                Object(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key == 'id'){
                        document.querySelector('#'+to_module+'_delete_id').value = row['id'];       // 鋪上delete_id = this id.no for delete form
                        document.querySelector('#'+to_module+'_edit_id').value = row['id'];         // 鋪上edit_id = this id.no for edit form
                    }else if(item_key == 'ppty'){
                        document.querySelector('#edit_'+to_module+' #'+item_key+'_'+row[item_key]).checked = true;
                    }else if(item_key == 'item'){
                        var item_arr = JSON.parse(row['item']);
                        Object.keys(item_arr).forEach(ikey => {
                            var item_amount = item_arr[ikey];
                            add_item(ikey, item_amount, 'off');
                        })
                    }else{
                        // console.log(item_key, row[item_key]);
                        document.querySelector('#edit_'+to_module+' #'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_cname'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

                var add_btn = '<input type="submit" name="'+to_module+'_update" value="送出" class="btn btn-primary">';
                var del_btn = '<input type="submit" name="'+to_module+'_delete" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#modal_action').append('編輯');          // model標題
                $('#modal_delect_btn').append(del_btn);     // 刪除鈕
                $('#modal_button').append(add_btn);         // 儲存鈕
                return;
            }
        })
    }
// // // 20231114_綁定編輯完成事件    // contenteditable="true" table可編輯、儲存功能
    var rows = document.getElementsByTagName("td");
        Array.from(rows).forEach(function(row) {
            row.addEventListener("blur", handleBlur);               // 監聽進入編輯狀態 或失焦
            row.addEventListener("keydown", handleKeyDown);         // 監聽Enter => run失焦
            row.addEventListener("click", handleCellClick);         // 監聽點擊時取得原始值
        });
    // tableFun_1.綁定失焦事件
    function handleBlur(e) {                                    // 綁定失焦事件
       if(e.sourceCapabilities){
           var originalValue = parseFloat(this.getAttribute("data-original-value").replace(/[^\d.-]/g, ""));
           this.innerHTML = originalValue;
       } 
    }
    // tableFun_2.綁定按鍵事件
    function handleKeyDown(e) {                                 // 綁定按鍵事件
        if (e.keyCode == 13) {                                  // 如果按下的是 Enter 键
            e.preventDefault();

            var rowId = parseFloat(this.id);
            var rowName = this.getAttribute("name");
            var newValue = parseFloat(this.innerHTML.replace(/[^\d.-]/g, ""));
            var originalValue = parseFloat(this.getAttribute("data-original-value").replace(/[^\d.-]/g, ""));
 
            newValue = isNaN(newValue) ? 0 : newValue;
            originalValue = isNaN(originalValue) ? 0 : originalValue;
            
            if (newValue !== originalValue) {
                var request = {
                    "rowId"     : rowId,
                    "rowName"   : rowName,
                    "newValue"  : newValue
                }
                updateCellValue(this, newValue, request);           // 呼叫 tableFun_4.API更新
                this.blur();
            }
        } else if (e.keyCode == 27) {                           // 如果按下的是 Esc 键
            var originalValue = parseFloat(this.getAttribute("data-original-value").replace(/[^\d.-]/g, ""));
            this.innerHTML = originalValue;
            this.blur();
        }
    }
    // tableFun_3.綁定單元格編輯開始事件
    function handleCellClick(e) {                               // 綁定單元格編輯開始事件
        this.setAttribute("data-original-value", this.textContent.trim());      // 獲取當前單元格的原始值並設置到屬性中
    }
    // tableFun_4.API更新現量
    function updateCellValue(cell, newValue, _request) {
        cell.innerHTML = newValue;
        $.ajax({
            url:'api.php',
            method:'post',
            async: false,                                           // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                function: 'update_amount',           // 操作功能
                _id     : _request['rowId'],
                _rowName: _request['rowName'],
                _amount : _request['newValue']
            },
            success: function(res){
                // swal_content += res_r_flag+' 套用成功';
                swal_action = 'success';
                update_catchValue(_request);                        // 呼叫 tableFun_5.更新pno_Catch中的數值
            },
            error: function(e){
                // swal_content += res_r_flag+' 套用失敗';
                swal_action = 'error';
                console.log("error");
            }
        });
        
        var sinn = '寫入 - ( '+_request['rowName']+' : '+_request['newValue']+' ) <b>'+ swal_action +'</b>&nbsp!!';
        inside_toast(sinn);

    }
    // tableFun_5.更新Catch中的數值
    function update_catchValue(_request){
        for(var i=0; i < ptstock.length ; i++){                             // stock array 採用迴圈繞出來
            if(ptstock[i]['id'] == _request['rowId']){                      // 找到id = rowId
                ptstock[i][_request['rowName']] = _request['newValue'];     // 覆蓋進stock中
                return;                                                   // 找到+完成後=返回
            }
        }
    }
// 20231128_下載Excel
    function submitDownloadExcel() {
        // 定義要抓的key=>value
            var stocks_item_keys = {
                "id"            : "aid", 
                "fab_title"     : "儲存點", 
                "local_title"   : "儲存位置", 
                "cate_no"       : "分類", 
                "cate_title"    : "分類名稱", 
                "SN"            : "SN", 
                "pname"         : "名稱", 
                "standard_lv"   : "安全存量", 
                "amount"        : "現場存量", 
                "stock_remark"  : "備註說明",
                "lot_num"       : "批號/期限",
                "po_no"         : "po_no",
                "updated_at"    : "最後更新",
                "updated_user"  : "最後編輯"
            };
        var sort_listData = [];         // 建立陣列
        for(var i=0; i < listData.length; i++){
            sort_listData[i] = {};      // 建立物件
            Object.keys(stocks_item_keys).forEach(function(item_key){
                // console.log(stocks_item_keys[item_key]+"：" ,listData[i][item_key]);
                sort_listData[i][stocks_item_keys[item_key]] = listData[i][item_key];
            })
        }
        console.log('sort_listData:', sort_listData);
        var htmlTableValue = JSON.stringify(sort_listData);
        document.getElementById('htmlTable').value = htmlTableValue;
        // console.log(listData);
    }

// // // show 年領用量與建議值
    function show_myReceives(){
        // 彙整出SN年領用量
        Object(myReceives).forEach(function(row){
            let csa = JSON.parse(row['cata_SN_amount']);
            Object.keys(csa).forEach(key =>{
                let pay = Number(csa[key]['pay']);
                let l_key = row['local_id'] +'_'+ key;
                if(receiveAmount[l_key]){
                    receiveAmount[l_key] += pay;
                }else{
                    receiveAmount[l_key] = pay;
                }
                // console.log(l_key, pay)
            })
        });
        // 選染到Table上指定欄位
        Object.keys(receiveAmount).forEach(key => {
            let value = receiveAmount[key];
            $('#receive_'+key).empty();
            $('#receive_'+key).append(value);
        })

        let sinn = '<b>** 自動帶入 年領用累計 ... 完成</b>~';
        inside_toast(sinn);
    }

// // // shopping_cart

    // 加入購物車清單
    function add_item(cata_SN, add_amount, swal_flag){

        var swal_title = '加入購物車清單';
        // swal_flag=off不顯示swal、其他是預設1秒
        if(swal_flag == 'off'){
            var swal_time = 0;
        }else{
            var swal_time = 1 * 1000;
        }

        var add_amount_length = add_amount.length;

        if(action != 'create'){                                // 確認action不是新表單，就進行Edit模式渲染，編輯狀態下參數需要分割

            var cata_SN_unity       = cata_SN;
            // var add_amount_unity    = add_amount;
            
            var cata_SN_arr = cata_SN_unity.split(',');           // arr[0]=cata_SN, arr[1]=stk_id
                var cata_SN  = cata_SN_arr[0];
                var stk_id   = cata_SN_arr[1];
    
            var add_pay  = add_amount['pay'];
            var add_need_arr = add_amount['need'].split(',');     // arr[0]=amount, arr[1]=po_no, arr[2]=lot_num
                // var add_po_no   = add_need_arr[0];
                var add_lot_num = add_need_arr[1];
                var check_item_return = check_item(cata_SN_unity, 0);    // call function 查找已存在的項目，並予以清除。
                
            Object(ptstock).forEach(function(cata){   
                if(cata['cata_SN']+','+cata['stk_id'] === cata_SN_unity){
                    var input_cb = '<input type="checkbox" checked disabled >';
                    input_cb += '<input type="hidden" name="item['+cata_SN+','+stk_id+'][need]" id="'+cata_SN+'_'+stk_id+'" class="select_item" value="'+add_amount['need']+'" >';
                    var add_cata_item = '<tr id="item_'+cata_SN+'_'+stk_id+'"><td>'+input_cb+'</td><td class="word_bk">'+cata['fab_title']+'_'+cata['local_title']+'</td><td class="word_bk">'+cata['SN']+'_'+cata['pname']+'</td>';
                    add_cata_item += '<td><input type="number" name="item['+cata_SN+','+stk_id+'][pay]" class="collect amount t-center" placeholder="數量" min="1" readonly ';
                    add_cata_item += ' max="'+add_pay+'" maxlength="'+add_amount_length+'" value="'+add_pay+'" oninput="if(value>'+add_pay+') value='+add_pay+'" >'+'</td><td>'+add_lot_num+'</td></tr>';
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

        }else{                  // 非編輯狀態下，參數可直接引用

            // 加入購物車清單input checkBox：On、Off
            var checkbox = document.getElementById("add_"+cata_SN);
            var flag = checkbox.checked ? "On" : "Off";
            if(flag == 'Off'){
                check_item(cata_SN, 0);
                return;
            }

            if(add_amount <= 0 ){
                checkbox.checked = false;
                var swal_content = cata_SN+' 沒有數量!'+' 加入失敗';
                var swal_action = 'error';
                swal(swal_title ,swal_content ,swal_action);      // swal需要按鈕確認
    
            }else{
                var check_item_return = check_item(cata_SN, 0);    // call function 查找已存在的項目，並予以清除。
                Object(ptstock).forEach(function(cata){          
                    if(cata['SN']+'_'+cata['stk_id'] === cata_SN){
                        var input_cb = '<input type="checkbox" name="item['+cata['SN']+','+cata['stk_id']+'][need]" id="'+cata['SN']+'_'+cata['stk_id']+'" class="select_item" value="'+cata['po_no']+','+cata['lot_num']+'" checked onchange="check_item(this.id)">';
                        var add_cata_item = '<tr id="item_'+cata['SN']+'_'+cata['stk_id']+'"><td>'+input_cb+'</td><td class="word_bk">'+cata['fab_title']+'_'+cata['local_title']+'</td><td class="word_bk">'+cata['SN']+'_'+cata['pname']+'</td>';
                        // add_cata_item += '<td>'+add_amount+'</td><td>'+cata['lot_num']+'</td></tr>';
                        add_cata_item += '<td><input type="number" name="item['+cata['SN']+','+cata['stk_id']+'][pay]" class="collect amount t-center" placeholder="數量" min="1" ';
                        add_cata_item += ' max="'+add_amount+'" maxlength="'+add_amount_length+'" value="'+add_amount+'" oninput="if(value>'+add_amount+') value='+add_amount+'" >'+'</td><td>'+cata['lot_num']+'</td></tr>';
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
                }else{
                    var sinn = cata_SN+' 數量:&nbsp<b>'+ add_amount +'</b>&nbsp加入購物車清單~';
                    inside_toast(sinn);
                }
                
            }

        }
        check_shopping_count();        // 清算購物車件數
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
                        // swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:swal_time});         // swal自動關閉
                        var sinn = '<b>'+swal_action+'</b>：&nbsp'+ swal_title + swal_content +'&nbsp~';
                        inside_toast(sinn);
                    }
                    document.getElementById("add_"+cata_SN).checked = false;
                    check_shopping_count();

                    return true;                    // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了  // true = 有找到數值
                }
            }
        }
        // check_shopping_count();
        return false;       // false = 沒找到數值
    }

    // 清算購物車件數，顯示件數，切換申請單按鈕
    function check_shopping_count(){
        var shopping_cart_list = document.querySelectorAll('#shopping_cart_tbody > tr');
        var review_btn = document.getElementById('receive_btn');                // 領用鈕
        var receive_submit_btn = document.getElementById('receive_submit');     // 領用送出鈕
        $('#shopping_count').empty();

        if(action == 'create'){                                         // 確認action是新表單，就進行模式渲染
            if(shopping_cart_list.length > 0){
                $('#shopping_count').append(shopping_cart_list.length);
                review_btn.classList.remove('disabled');                // 購物車大於0，領用鈕--取消disabled
                receive_submit_btn.classList.remove('disabled');        // 購物車大於0，領用送出鈕--取消disabled
            }else{
                review_btn.classList.add('disabled');                   // 購物車等於0，領用鈕--disabled
                receive_submit_btn.classList.add('disabled');           // 購物車等於0，領用送出鈕--disabled
            }
        }
    }

    $(document).ready(function () {
        
        // dataTable 2 https://ithelp.ithome.com.tw/articles/10272439
        $('#stock_list').DataTable({
            "autoWidth": false,
            // 排序
            // "order": [[ 4, "asc" ]],
            // 顯示長度
            "pageLength": 25,
            // 中文化
            "language":{
                url: "../../libs/dataTables/dataTable_zh.json"
            }
        });

        // call fun show 年領用量與建議值
        // if(stock.length >= 1){
        //     show_myReceives();
        // }

        // 假如index找不到當下存在已完成的表單，就alarm它!
        // if (check_yh_list_num == '0') {
        //     let message  = '*** '+ thisYear +' '+ half +'年度 PPE儲存量確認開始了! 請務必在指定時間前完成確認 ~ <i class="fa-solid fa-right-long"></i>&nbsp&nbsp&nbsp';
        //         message += '<button type="button" style="background-color: transparent;" data-bs-toggle="modal" data-bs-target="#checkList">'
        //                     +'<b><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>&nbsp打開點檢表</button></b>';

        //     alert( message, 'danger')
        // }


    })