// // // 第一頁：info modal function
    // var catalog = <?=json_encode($catalogs);?>;                        // 引入catalogs資料
    var catalogs_item = {
        "SN"            : "SN/編號", 
        "cate_no"       : "category/分類", 
        "pname"         : "pname/品名", 
        "cata_remark"   : "cata_remark/敘述說明",
        "OBM"           : "OBM/品牌/製造商",
        "model"         : "model/型號",
        "size"          : "size/尺寸",
        "unit"          : "unit/單位",
        "SPEC"          : "SPEC/規格"
        // "scomp_no"      : "scomp_no/供應商"
    };    // 定義要抓的key=>value

    // fun-1.鋪info畫面
    function info_module(to_module, row_SN){
        // step1.將原排程陣列逐筆繞出來
        $('#info_append, #pic_append').empty();
        Object(window[to_module]).forEach(function(row){          
            if(row['SN'] === row_SN){
                // step2.鋪畫面到module
                Object.keys(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key === 'cate_no'){
                        item_value = row['cate_no']+'.'+row['cate_title'];
                    }else{
                        item_value = row[item_key]; 
                    }
                    $('#info_append').append('<b>'+window[to_module+'_item'][item_key]+'：</b> '+item_value+'</br>');
                })
                $('#pic_append').append('<img src="../catalog/images/'+row['PIC']+'" style="width: 100%;" class="img-thumbnail">');     // 圖片
                return; // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了
            }
        })
    }
// // // 第n頁：catalog_modal 篩選 function
    // 加入購物車清單
    function add_item(cata_SN, add_amount, flag){
        var swal_title = '加入購物車清單';
        // flag=off不顯示swal、其他是預設1秒
        if(flag == 'off'){
            var swal_time = 0;
        }else{
            var swal_time = 1 * 1000;
        }

        if(action != 'create'){                                // 確認action不是新表單，就進行Edit模式渲染，編輯狀態下參數需要分割

            var cata_SN_unity       = cata_SN;
            var add_amount_unity    = add_amount;

            var cata_SN_arr = cata_SN_unity.split(',');           // arr[0]=cata_SN, arr[1]=stk_id
                var cata_SN  = cata_SN_arr[0];
                var stk_id   = cata_SN_arr[1];
    
            var add_amount_arr = add_amount_unity.split(',');     // arr[0]=amount, arr[1]=po_no, arr[2]=lot_num
                var arr_amount  = add_amount_arr[0];
                var arr_po_no   = add_amount_arr[1];
                var arr_lot_num = add_amount_arr[2];
                var check_item_return = check_item(cata_SN_unity, 0);    // call function 查找已存在的項目，並予以清除。
                
            Object(catalogs).forEach(function(cata){          
                if(cata['SN'] === cata_SN){
                    var input_cb = '<input type="checkbox" name="item['+cata['SN']+',]" id="'+cata['SN']+'" class="select_item" value="'+add_amount_unity+',," checked onchange="check_item(this.id)">';
                    var add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+'</td><td>'+cata['SN']+'</td><td>'+cata['pname']+'</td><td>'+cata['model']+'</td><td>'+cata['size']+'</td><td>'+arr_amount+' / '+cata['unit']+'</td></tr>';
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

            if(add_amount <= 0 ){
                var swal_content = cata_SN+' 沒有填數量!'+' 加入失敗';
                var swal_action = 'error';
                swal(swal_title ,swal_content ,swal_action);      // swal需要按鈕確認

            }else{
                var check_item_return = check_item(cata_SN, 0);    // call function 查找已存在的項目，並予以清除。
                Object(catalogs).forEach(function(cata){          
                    if(cata['SN'] === cata_SN){
                        var input_cb = '<input type="checkbox" name="item['+cata['SN']+',]" id="'+cata['SN']+'" class="select_item" value="'+add_amount+',," checked onchange="check_item(this.id)">';
                        var add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+'</td><td>'+cata['SN']+'</td><td>'+cata['pname']+'</td><td>'+cata['model']+'</td><td>'+cata['size']+'</td><td>'+add_amount+' / '+cata['unit']+'</td></tr>';
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
        var nav_review_btn = document.getElementById('nav-review-tab'); 
        $('#shopping_count').empty();
        if(shopping_cart_list.length > 0){
            $('#shopping_count').append(shopping_cart_list.length);
            nav_review_btn.classList.remove('disabled');        // 購物車大於0，取消disabled
        }else{
            nav_review_btn.classList.add('disabled');           // 購物車等於0，disabled
        }
    }

// // // 第三頁：searchUser function 
    // fun3-1：search Key_word
    function search_fun(search){
        mloading("show");                       // 啟用mLoading
        // 使用開單人工號查詢
        if(search == 'emp_id'){
            var fun = search;
            search = $('#emp_id').val().trim();
            $('#cname').empty();

        // 查詢上層主管工號
        }else{
            var fun = 'omager_badge';
            search = search.trim();
            $('#omager_badge').empty();
        }

        if(!search || (search.length < 8)){
            alert("查詢工號字數最少 8 個字以上!!");
            $("body").mLoading("hide");
            return false;
        } 

        $.ajax({
            url:'http://tneship.cminl.oa/api/hrdb/index.php',           // 正式2024新版
            method:'post',
            dataType:'json',
            data:{
                functionname: 'showStaff',                              // 操作功能
                uuid: '06d4e304-a8bd-11f0-8ffe-1c697a98a75f',           // carux
                emp_id: search                                          // 查詢對象key_word  // 使用開單人工號查詢
            },
            success: function(res){
                var obj_val = res["result"];
                // 將結果進行渲染
                if (obj_val !== '') {
                    // 搜尋申請人emp_id
                    if(fun == 'emp_id'){     
                        var input_cname = document.getElementById('cname');             // 申請人姓名
                        if(obj_val){
                            input_cname.value = obj_val.cname;                          // 將欄位帶入數值 = cname
                            $("#cname").addClass("autoinput");
                            var sinn = '以工號&nbsp<b>'+obj_val.emp_id+'/'+obj_val.cname+'</b>&nbsp帶入資訊...完成!!';
                            inside_toast(sinn);

                        }else{
                            // alert("查無工號["+search+"]!!");
                            input_cname.value = '';                         // 將欄位cname清除
                            var sinn = '查無工號&nbsp<b>'+ search +'</b>&nbsp!!';
                            inside_toast(sinn);
                        }
                    
                    }
                }
            },
            error(err){
                console.log("search error:", err);
            }
        })
        $("body").mLoading("hide");
    }

    // fun3-3：吐司顯示字條 // init toast
    function inside_toast(sinn){
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }

// // // 第三頁：searchUser function 

// // // Edit選染
    function edit_item(){
        var trade_item = {
            "out_local"      : "out_local/發貨廠區", 
            "in_local"       : "in_local/收貨廠區", 
            "out_user_id"    : "out_user_id/發貨人emp_id", 
            "form_type"      : "form_type/工號",
            "item"           : "item",
            "id"             : "id",
        };    // 定義要抓的key=>value
        // step1.將原排程陣列逐筆繞出來
        Object.keys(trade_item).forEach(function(trade_key){
            if(trade_key == 'item'){      //item 購物車
                var trade_row_cart = JSON.parse(trade_row[trade_key]);
                Object.keys(trade_row_cart).forEach(function(cart_key){
                    var cart_key_arr = cart_key.split(',');           // arr[0]=cata_SN, arr[1]=stk_id
                        var cata_SN  = cart_key_arr[0];                   
                        var stk_id   = cart_key_arr[1];
                    var add_amount_unity =  trade_row_cart[cart_key];
                    var add_amount_arr = add_amount_unity.split(',');     // arr[0]=amount, arr[1]=po_no, arr[2]=lot_num
                        var arr_amount  = add_amount_arr[0];
                        var arr_po_no   = add_amount_arr[1];
                        var arr_lot_num = add_amount_arr[2];
                    var check_item_return = check_item(cata_SN, 0);    // call function 查找已存在的項目，並予以清除。
                    add_item(cata_SN, arr_amount, 'off');
                })

            }else if(trade_key == 'out_local'){             //out_local 發貨廠區
                document.getElementById('po_no').value = trade_row[trade_key];

            }else{
                document.querySelector('#'+trade_key).value = trade_row[trade_key]; 

            }
        })

        // 鋪設logs紀錄
        var forTable = document.querySelector('.logs tbody');
        for (var i = 0, len = json.length; i < len; i++) {
            json[i].remark = json[i].remark.replaceAll('_rn_', '<br>');   // *20231205 加入換行符號
            forTable.innerHTML += 
                '<tr>' + '<td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + 
                    '</td><td style="text-align: left; word-break: break-all;">' + json[i].remark + '</td>' +
                '</tr>';
        }
        document.getElementById('logs_div').classList.remove('unblock');           // 購物車等於0，disabled
    }

// 20231128 以下為上傳後"iframe"的部分
    // 阻止檔案未上傳導致的錯誤。
    // 請注意設置時的"onsubmit"與"onclick"。
    function restockExcelForm() {
        // 如果檔案長度等於"0"。
        if (excelFile.files.length === 0) {
            // 如果沒有選擇文件，顯示警告訊息並阻止表單提交
            warningText.style.display = "block";
            return false;
        }
        // 如果已選擇文件，允許表單提交
        iframe.style.display = 'block'; 
        // 以下為編輯特有
        // showTrainList.style.display = 'none';
        return true;
    }

    function iframeLoadAction() {
        iframe.style.height = '0px';
        var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
        var iframeContent = iframeDocument.documentElement;
        var newHeight = iframeContent.scrollHeight + 'px';
        iframe.style.height = newHeight;
        var excel_json = iframeDocument.getElementById('excel_json');
        var stopUpload = iframeDocument.getElementById('stopUpload');
        // 在此處對找到的 <textarea> 元素進行相應的操作
        if (excel_json) {
            sn_list.style.display = "none";
            // 手动触发input事件
            var inputEvent = new Event('input', { bubbles: true });
            import_excel_btn.style.display = "block";       // 載入按鈕--顯示
            warningText.style.display = "none";             // 警告文字--隱藏
            
        } else if(stopUpload) {
            // 沒有找到 <textarea> 元素
            console.log('請確認資料是否正確');
            sn_list.style.display = "block";
            import_excel_btn.style.display = "none";        // 載入按鈕--隱藏
            warningText.style.display = "block";            // 警告文字--顯示

        }else{
            // console.log('找不到 < ? > 元素');
        }
    };
    // Excel載入購物車
    function uploadExcel_toCart(row_cart){
        var trade_row_cart = JSON.parse(row_cart);
        Object(trade_row_cart).forEach(function(cart_row){
            Object.keys(cart_row).forEach(function(cart_row_key){
                var cata_SN    = cart_row_key;                   
                var arr_amount = cart_row[cart_row_key];
                check_item(cata_SN, 0);                     // call function 查找已存在的項目，並予以清除。
                add_item(cata_SN, arr_amount, 'off');
            })
        })
        $('.nav-tabs button:eq(1)').tab('show');            // 切換頁面到購物車

    }

    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();

        // 監聽表單內 input 變更事件
        $('#cname, #plant, #dept, #sign_code, #omager, #extp').change(function() {
            // 當有變更時，對該input加上指定的class
            $(this).removeClass('autoinput');
        });

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

        // 20231128 以下為上傳後"iframe"的部分
            // 監控按下送出鍵後，打開"iframe"
            excelUpload.addEventListener('click', function() {
                iframeLoadAction();
                restockExcelForm();
            });
            // 監控按下送出鍵後，打開"iframe"，"load"後，執行抓取資料
            iframe.addEventListener('load', function(){
                iframeLoadAction();
            });
            // 監控按下[載入]鍵後----呼叫Excel載入購物車
            import_excel_btn.addEventListener('click', function() {
                var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
                var excel_json = iframeDocument.getElementById('excel_json');
                var stopUpload = iframeDocument.getElementById('stopUpload');

                if (excel_json) {
                    uploadExcel_toCart(excel_json.value);

                } else if(stopUpload) {
                    console.log('請確認資料是否正確');
                }else{
                    console.log('找不到 ? 元素');
                }
            });
    })

    $(document).ready(function () {
        
        $('#catalog_list').DataTable({
            "autoWidth": false,
            // 顯示長度
            "pageLength": 25,
            // 中文化
            "language":{
                url: "../../libs/dataTables/dataTable_zh.json"
            }
        });

        // <!-- 有填數量自動帶入+號按鈕，沒數量自動清除+號按鈕的value -->
        let amounts = [...document.querySelectorAll('.amount')];
        for(let amount of amounts){
            amount.onchange = e => {
                let amount_id = e.target.id;
                if(amount.value == ''){
                    // document.getElementById('catalog_SN_'+ amount_id).checked=false;     // 取消選取 = 停用
                    document.getElementById('add_'+ amount_id).value = '';
                } else {
                    // document.getElementById('catalog_SN_'+ amount_id).checked=true;      // 增加選取 = 停用
                    document.getElementById('add_'+ amount_id).value = amount.value;
                }
            }
        }

        // 確認action不是新表單，就進行Edit模式渲染
        if(action != 'create'){                                // 確認action不是新表單，就進行Edit模式渲染
            edit_item();
            $('.nav-tabs button:eq(1)').tab('show');        // 切換頁面到購物車
        }

    })