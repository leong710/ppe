// // // 第一頁：info modal function
    var catalog_item = {
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
    
// // // catalog_modal 篩選 function
    // // <!-- 有填數量自動帶入+號按鈕，沒數量自動清除+號按鈕的value --> 20230816 修正手動輸入超過限購，add_btn的value會只吃最大值
    function add_cart_btn(cata_SN, add_amount){
        let add_btn = document.getElementById('add_'+ cata_SN);
        if(add_btn){
            if(add_amount == ''){
                add_btn.value = '';
            } else {
                add_btn.value = add_amount;
            }
        }
    }

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
            Object(catalog).forEach(function(cata){          
                if(cata['SN'] === cata_SN){
                    var input_cb = '<input type="checkbox" name="item['+cata['SN']+'][need]" id="'+cata['SN']+'" class="select_item" value="'+add_amount+'" checked onchange="check_item(this.id)">';
                    var collect_cb = '<input name="item['+cata['SN']+'][pay]" class="unblock" value="'+add_amount+'">';
                    var add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+collect_cb+'</td><td>'+cata['SN']+'</td><td>'+cata['pname']+'</td><td>'+cata['model']+'</td><td>'+cata['size']+'</td><td>'+add_amount+' / '+cata['unit']+'</td></tr>';
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
                
                if(action == 'create'){
                    inside_toast(sinn);
                };
        
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
        // check_shopping_count();
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

// // // searchUser function 
    // 第一-階段：search Key_word
    function search_fun(search){
        mloading("show");                       // 啟用mLoading
        // 使用開單人工號查詢
        if(search == 'emp_id'){
            var fun = search;
            search = $('#emp_id').val().trim();
            $('#plant, #dept, #sign_code, #cname, #extp').empty();

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
            url:'http://tneship.cminl.oa/hrdb/api/index.php',       // 正式
            method:'get',
            async: false,                                                       // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                functionname: 'showStaff',                          // 操作功能
                uuid: '39aad298-a041-11ed-8ed4-2cfda183ef4f',
                search: search                                      // 查詢對象key_word
            },
            success: function(res){
                var obj_val = res["result"];

                // 將結果進行渲染
                if (obj_val !== '') {
                    // 搜尋申請人emp_id
                    if(fun == 'emp_id'){     
                        var input_cname = document.getElementById('cname');             // 申請人姓名
                        var input_plant = document.getElementById('plant');             // 申請單位
                        var input_dept = document.getElementById('dept');               // 部門名稱
                        var input_sign_code = document.getElementById('sign_code');     // 部門代號
                        var input_extp = document.getElementById('extp');               // 分機
                        var input_omager = document.getElementById('omager');           // 上層主管
                        var input_in_signName = document.getElementById('in_signName'); // 待簽姓名

                        if(obj_val){

                            input_cname.value = obj_val.cname;                          // 將欄位帶入數值 = cname

                            if(obj_val.comid3 != ' '){
                                input_extp.value = obj_val.comid3;                      // 將欄位帶入數值 = extp/comid3分機
                                $("#extp").addClass("autoinput");
                            }else{
                                input_extp.value = '';
                                $("#extp").removeClass("autoinput");
                            }

                            if(obj_val.emp_dept == obj_val.dept_d){
                                input_plant.value = obj_val.dept_b + '/' + obj_val.dept_c;  // 申請單位 = dept_b處30 + dept_c部20
                            }else{
                                input_plant.value = obj_val.dept_b;                         // 申請單位 = dept_b 處30
                            }
                            
                            input_dept.value = obj_val.emp_dept;                        // 部門名稱 = 所屬單位
                            input_sign_code.value = obj_val.dept_no;                    // 部門代號 = dept_no 部門代號

                            if(obj_val.omager){
                                $('#omager_badge').closest('.tag').remove();           // 泡泡自畫面中移除
                                $('#omager_badge').empty();
                                input_omager.value = obj_val.omager;                   // 將欄位帶入數值 = omager/omager 上層主管
                                $('#omager_badge').append('<div class="tag">' + obj_val.s2_cname + '<span class="remove">x</span></div>');
                                input_in_signName.value = obj_val.s2_cname;             // 帶入待簽人姓名

                                showDelegation(obj_val.omager);                         // 呼叫查詢代理簽核程式，有結果就置換，沒有就保持!!

                            }else{
                                input_omager.value = '';
                                input_in_signName.value = '';
                            }
                            $("#cname, #plant, #dept, #sign_code, #omager").addClass("autoinput");

                            var sinn = '以工號&nbsp<b>'+obj_val.emp_id+'/'+obj_val.cname+'</b>&nbsp帶入資訊...完成!!';
                            inside_toast(sinn);

                        }else{
                            // alert("查無工號["+search+"]!!");
                            input_cname.value = '';                         // 將欄位cname清除
                            input_plant.value = '';
                            input_dept.value = '';
                            input_sign_code.value = '';
                            input_extp.value = '';
                            var sinn = '查無工號&nbsp<b>'+ search +'</b>&nbsp!!';
                            inside_toast(sinn);
                        }
                        
                    // 搜尋申請人上層主管emp_id    
                    }else{                    
                        if(obj_val){ 
                            console.log(fun,'obj_val:', obj_val);                           
                            $('#omager_badge').append('<div class="tag">' + obj_val.cname + '<span class="remove">x</span></div>');
                            $("#omager").addClass("autoinput");
                            document.getElementById('in_signName').value = obj_val.cname;             // 帶入待簽人姓名
                            var sinn = '以工號&nbsp<b>'+obj_val.emp_id+'/'+obj_val.cname+'</b>&nbsp帶入上層主管資訊...完成!!';
                            inside_toast(sinn);

                        }else{
                            document.getElementById('omager').value = '';                         // 將欄位cname清除
                            document.getElementById('in_signName').value = '';                    // 將欄位cname清除
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

    // fun3-1A 用上層主管工號查詢簽核代理人
    function showDelegation(search){
        $.ajax({
            url:'http://tneship.cminl.oa/hrdb/api/index.php',       // 正式
            method:'get',
            async: false,                                                       // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                functionname: 'showDelegation',                     // 操作功能
                uuid: '39aad298-a041-11ed-8ed4-2cfda183ef4f',
                search: search                                      // 查詢對象key_word
            },
            success: function(res){
                var obj_val = res["result"];
                // 搜尋申請人上層主管emp_id其簽核代理人 將結果進行渲染
                if(obj_val){  
                    $('#omager_badge').closest('.tag').remove();           // 泡泡自畫面中移除
                    $('#omager_badge').empty();
                    document.getElementById('omager').value = obj_val.DEPUTYEMPID;                   // 將欄位帶入數值 = omager/omager 上層主管                          
                    $('#omager_badge').append('<div class="tag">代理人：' + obj_val.DEPUTYCNAME + '<span class="remove">x</span></div>');
                    $("#omager").addClass("autoinput");

                    document.getElementById('in_signName').value = obj_val.DEPUTYCNAME;             // 將欄位帶入待簽人姓名                          

                }
            },
            error(err){
                console.log("showDelegation search error:", err);
            }
        })
    }

    // fun3-2：omager上層主管：移除單項模組
    $('#omager_badge').on('click', '.remove', function() {
        $(this).closest('.tag').remove();                       // 泡泡自畫面中移除
        document.getElementById('omager').value = '';          // 將欄位cname清除
        document.getElementById('in_signName').value = '';     // 將欄位in_signName清除
        $('#omager').removeClass('autoinput');                 // 移除外框提示
        // $('#omager_badge').empty();
    });

    function inside_toast(sinn){
        // init toast
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();

    }
// // // searchUser function 

// // // Edit選染
    function edit_item(){
        // var issue_row = <?=json_encode($issue_row);?>;                        // 引入issue_row資料作為Edit
        var issue_item = {
            "plant"          : "plant/申請單位", 
            "dept"           : "dept/部門名稱", 
            "sign_code"      : "sign_code/部門代號", 
            "in_user_id"     : "in_user_id/工號",
            "cname_i"        : "cname_i/申請人姓名",
            "extp"           : "extp/分機",
            "in_local"       : "in_local/領用站點",
            "ppty"           : "** ppty/需求類別",
            "omager"         : "** omager/上層主管工號",
            "receive_remark" : "receive_remark/用途說明",
            "created_emp_id" : "created_emp_id/開單人工號",
            "created_cname"  : "created_cname/開單人姓名",
            "id"             : "id",
            "item"           : "** item"
            // "sign_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原排程陣列逐筆繞出來
        Object.keys(issue_item).forEach(function(issue_key){
            if(issue_key == 'ppty'){      // ppty/需求類別
                var ppty = document.querySelector('#'+issue_key+'_'+issue_row[issue_key]);
                if(ppty){
                    ppty.checked = true;
                }
                
            }else if(issue_key == 'item'){      //item 購物車
                var issue_row_cart = JSON.parse(issue_row[issue_key]);
                Object.keys(issue_row_cart).forEach(function(cart_key){
                    add_item(cart_key, issue_row_cart[cart_key]['need'], 'off');
                })
                
            }else if(receive_key == 'omager'){             //omager 上層主管工號
                var id_omager = document.getElementById('omager');
                if(id_omager){
                    id_omager.value = receive_row[receive_key];
                }
                search_fun(receive_row[receive_key]);

            }else{
                var row_key = document.querySelector('#'+issue_key);
                if(row_key){
                    row_key.value = issue_row[issue_key]; 
                }
            }
        })

        // 鋪設logs紀錄
        // var json = JSON.parse('<?=json_encode($logs_arr)?>');
        // var id = '<?=$issue_row["id"]?>';
        var forTable = document.querySelector('.logs tbody');
        for (var i = 0, len = json.length; i < len; i++) {
            json[i].remark = json[i].remark.replaceAll('_rn_', '<br>');   // *20231205 加入換行符號
            forTable.innerHTML += 
                '<tr><td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + 
                    '</td><td style="text-align: left; word-break: break-all;">' + json[i].remark + '</td>' +
                    // '<?php if($_SESSION[$sys_id]["role"] == 0){ ?><td>' + '<form action="" method="post">'+
                    //     `<input type="hidden" name="log_id" value="` + [i] + `";>` +
                    //     `<input type="hidden" name="id" value="` + id + `";>` +
                    //     `<input type="submit" name="delete_log" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">` +
                    // '</form>' + '</td><?php } ?>' +
                '</tr>';
        }
        document.getElementById('logs_div').classList.remove('unblock');           // 購物車等於0，disabled

        action = 'create';  // edit表單鋪設完畢後，恢復成create狀態

    }

// // // show 年領用量與建議值
    function show_myReceives(){
        // 彙整出SN年領用量
        Object(myReceives).forEach(function(row){
            let csa = JSON.parse(row['cata_SN_amount']);
            Object.keys(csa).forEach(key =>{                                // 這裡的key = SN
                let pay = Number(csa[key]['pay']);
                let l_key = row['local_id'] +'_'+ key;
                if(receiveAmount[l_key]){
                    receiveAmount[l_key]['pay'] += pay;
                }else{
                    receiveAmount[l_key] = {
                        'pay' : pay
                    }
                }
                Object(catalog).forEach(function(cata){
                    if(cata['SN'] == key){
                        receiveAmount[l_key]['buy_dm'] = cata['buy_'+buy_ty];
                        receiveAmount[l_key]['unit'] = cata['unit'];
                        return;
                    }
                })
            })
        });

        // 選染到Table上指定欄位
        Object.keys(receiveAmount).forEach(key => {
            let pay = receiveAmount[key]['pay'];                // 領用總數
            let buy_dm = receiveAmount[key]['buy_dm'];          // 安量倍數
            let unit = receiveAmount[key]['unit'];              // SN-unit單位
            let value = Math.ceil(pay * buy_dm);                // 算出建議值
            $('#receive_'+key).empty();                                             // 清除領用格+建議值
            $('#receive_'+key).append(pay+' x '+ buy_dm +'</br>= '+ value +' / '+ unit );  // 貼上領用總量+建議值計算公式
            // document.getElementById('buy_qt_'+key).classList.add('alert_it');       // 將建議值套用css:alert_it
            // document.getElementById(key).value = value;                             // input.value套用
        })

        let sinn = '<b>** 自動帶入 年領用累計 與 建議值 ... 完成</b>~';
        inside_toast(sinn);
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
                check_item(cata_SN, 0);                 // call function 查找已存在的項目，並予以清除。
                add_item(cata_SN, arr_amount, 'off');
            })
        })
        $('.nav-tabs button:eq(1)').tab('show');        // 切換頁面到購物車

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
        
        // dataTable 2 https://ithelp.ithome.com.tw/articles/10272439
        $('#catalog_list').DataTable({
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

        // <!-- 有填數量自動帶入+號按鈕，沒數量自動清除+號按鈕的value -->
        let amounts = [...document.querySelectorAll('.amount')];
        for(let amount of amounts){
            amount.onchange = e => {
            // amount.onblur = e => {
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
        
        // call fun show 年領用量與建議值
        if(catalog.length >= 1){
            show_myReceives();
        }

        // 確認action不是新表單，就進行Edit模式渲染
        if(action != 'create'){                                // 確認action不是新表單，就進行Edit模式渲染
            edit_item();
            $('.nav-tabs button:eq(1)').tab('show');        // 切換頁面到購物車
        }

    })


    