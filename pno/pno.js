
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    // All resources finished loading! // 關閉mLoading提示
    window.addEventListener("load", function(event) {
        $("body").mLoading("hide");
    });

    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }

    // fun3-3：吐司顯示字條 // init toast
    function inside_toast(sinn){
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }


    function add_module(to_module){     // 啟用新增模式
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_pno_info').empty();   // 清除model功能
        $('#reset_btn').click();                                                        // reset清除表單
        var add_btn = '<input type="submit" name="pno_submit" class="btn btn-primary" value="新增">';
        $('#modal_action').append('新增');                      // model標題
        $('#modal_button').append(add_btn);                     // 儲存鈕
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.remove('unblock');                  // 新增模式 = 解除
        document.querySelector("#edit_pno .modal-header").classList.remove('edit_mode_bgc');
        document.querySelector("#edit_pno .modal-header").classList.add('add_mode_bgc');
    }
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_pno_info').empty();   // 清除model功能
        $('#reset_btn').click();                                                        // reset清除表單
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.add('unblock');                     // 編輯模式 = 隱藏
        document.querySelector("#edit_pno .modal-header").classList.remove('add_mode_bgc');
        document.querySelector("#edit_pno .modal-header").classList.add('edit_mode_bgc');
        // remark: to_module = 來源與目的 site、fab、local
        // step1.將原排程陣列逐筆繞出來
        Object(window[to_module]).forEach(function(row){          
            if(row['id'] == row_id){
                // step2.鋪畫面到module
                Object(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key == 'id'){
                        document.querySelector('#'+to_module+'_delete_id').value = row['id'];       // 鋪上delete_id = this id.no for delete form
                        document.querySelector('#'+to_module+'_edit_id').value = row['id'];         // 鋪上edit_id = this id.no for edit form
                    }else if(item_key == 'flag'){
                        document.querySelector('#edit_'+to_module+' #edit_'+to_module+'_'+row[item_key]).checked = true;
                    }else if(item_key == 'price'){
                        var price_json = row[item_key];
                        if(!price_json || price_json == 0){
                            var price_json_parse = {};
                        }else{
                            var price_json_parse = JSON.parse(price_json);
                        }
                        if(!price_json_parse[thisYear_num]){
                            price_json_parse[thisYear_num] = 0;
                        }
                        document.querySelector('#edit_'+to_module+' #edit_quoteYear').value = thisYear_num; 
                        document.querySelector('#edit_'+to_module+' #edit_price').value = price_json_parse[thisYear_num]; 
                    }else{
                        document.querySelector('#edit_'+to_module+' #edit_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

                // step3-3.開啟 彈出畫面模組 for user編輯
                // edit_myTodo_btn.click();
                var add_btn = '<input type="submit" name="edit_pno_submit" class="btn btn-primary" value="儲存">';
                var del_btn = '<input type="submit" name="delete_pno" value="刪除pno料號" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#modal_action').append('編輯');          // model標題
                $('#modal_delect_btn').append(del_btn);     // 刪除鈕
                $('#modal_button').append(add_btn);         // 儲存鈕
                return;
            }
        })
    }

    // 切換上架/下架開關
    let flagBtns = [...document.querySelectorAll('.flagBtn')];
    for(let flagBtn of flagBtns){
        flagBtn.onclick = e => {
            let swal_content = e.target.name+'_id:'+e.target.id+'=';
            // console.log('e:',e.target.name,e.target.id,e.target.value);
            $.ajax({
                url:'api.php',
                method:'post',
                async: false,                                           // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    function: 'cheng_flag',           // 操作功能
                    table: e.target.name,
                    id: e.target.id,
                    flag: e.target.value
                },
                success: function(res){
                    let res_r = res["result"];
                    let res_r_flag = res_r["flag"];
                    // console.log(res_r_flag);
                    if(res_r_flag == 'Off'){
                        e.target.classList.remove('btn-success');
                        e.target.classList.add('btn-warning');
                        e.target.value = 'Off';
                        e.target.innerText = 'Off';
                    }else{
                        e.target.classList.remove('btn-warning');
                        e.target.classList.add('btn-success');
                        e.target.value = 'On';
                        e.target.innerText = 'On';
                    }
                    swal_action = 'success';
                    swal_content += res_r_flag+' 套用成功';
                },
                error: function(e){
                    swal_action = 'error';
                    swal_content += res_r_flag+' 套用失敗';
                    console.log("error");
                }
            });

            // swal('套用人事資料' ,swal_content ,swal_action, {buttons: false, timer:2000}).then(()=>{location.href = url;});     // deley3秒，then自動跳轉畫面
            swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:1000});

        }
    }
    
    // 20231114_綁定編輯完成事件    // contenteditable="true" table可編輯、儲存功能
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
            var rowName = parseFloat(this.getAttribute("name"));
            var newValue = parseFloat(this.innerHTML.replace(/[^\d.-]/g, ""));
            var originalValue = parseFloat(this.getAttribute("data-original-value").replace(/[^\d.-]/g, ""));
 
            // if (isNaN(newValue)) { newValue = 0; }
            // if (isNaN(originalValue)) { originalValue = 0; }
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
    // tableFun_4.API更新
    function updateCellValue(cell, newValue, _request) {
        cell.innerHTML = newValue;
        // console.log("送API", _request);
        $.ajax({
                url:'api.php',
                method:'post',
                async: false,                                           // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    function: 'update_price',           // 操作功能
                    _id: _request['rowId'],
                    _quoteYear: _request['rowName'],
                    _price: _request['newValue'],
                    // json_request: JSON.stringify({_request})
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
            var sinn = 'mySQL寫入 - ( '+_request['rowName']+' : '+_request['newValue']+' ) <b>'+ swal_action +'</b>&nbsp!!';
            inside_toast(sinn);

    }
    // tableFun_5.更新Catch中的數值
    function update_catchValue(_request){
        for(var i=0; i < pno.length ; i++){                             // pno array 採用迴圈繞出來
            if(pno[i]['id'] == _request['rowId']){                        // 找到id = rowId
                if(!pno[i]['price'] || pno[i]['price'] == 0){               // 沒有price或等於0
                    var price_json_parse = {};                                  // 建一個空物件
                }else{                                                      // 有price
                    var price_json_parse = JSON.parse(pno[i]['price']);         // 1.取pno中的price 2.解碼 3.放到price_json_parse
                }

                var _quoteYear = _request['rowName'];                   // 取參數 _quoteYear = rowName
                var _price = _request['newValue'];                      // 取參數 _price = newValue
                price_json_parse[_quoteYear] = _price;                  // 將參數覆蓋進去price_json_parse陣列/物件中
                pno[i]['price'] = JSON.stringify(price_json_parse);     // 1.編碼price_json_parse 2.覆蓋進pno中

                return;                                                 // 找到+完成後=返回
            }
        }
    }

    
    // 20231129 合併：excel mode function
    function excel_module(to_module){     // 上傳Excel模式
        $('#excel_modal_action, #excel_example').empty();     // 清除model功能

        $('#excel_modal_action').append(to_module);                                                    // model標題文字

        var example_btn = '&nbsp<a href="../_Format/'+to_module+'_example.xlsx" target="_blank">上傳格式範例</a>';
        $('#excel_example').append(example_btn);                                               // 添加 上傳格式範例 鈕

        document.getElementById('upload_excel_btn').value = to_module;
        document.getElementById('import_excel_btn').value = to_module;
    }

// 20231128 以下為上傳後"iframe"的部分
    // 阻止檔案未上傳導致的錯誤。
    // 請注意設置時的"onsubmit"與"onclick"。
    function checkExcelForm() {
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
            warningData.style.display = "none";
            // 手动触发input事件
            var inputEvent = new Event('input', { bubbles: true });
            import_excel_btn.style.display = "block";       // 載入按鈕--顯示
            warningText.style.display = "none";             // 警告文字--隱藏
            
        } else if(stopUpload) {
            // 沒有找到 <textarea> 元素
            console.log('請確認資料是否正確');
            warningData.style.display = "block";
            import_excel_btn.style.display = "none";        // 載入按鈕--隱藏
            warningText.style.display = "block";            // 警告文字--顯示

        }else{
            // console.log('找不到 < ? > 元素');
        }
    };

    // 20231128_下載Excel
    function submitDownloadExcel(to_module) {
        // 定義要抓的key=>value
            if(to_module == "pno"){
                var item_keys = {
                    "cate_no"       : "分類編號",
                    "cate_remark"   : "分類名稱",
                    "cata_SN"       : "器材編號", 
                    "pname"         : "器材名稱", 
                    "size"          : "尺寸", 
                    "_year"         : "年度", 
                    "part_no"       : "料號", 
                    // "price"         : "年度/單價NT$", 
                    "pno_remark"    : "料號註解", 
                    "flag"          : "開關",
                    "updated_at"    : "最後更新",
                    "updated_user"  : "最後編輯"
                };
            }else{
                var item_keys = {};
            }
            // Object(window[to_module]).forEach(function(row){          

        var sort_listData = [];         // 建立整理陣列
        for(var i=0; i < window[to_module].length; i++){
            sort_listData[i] = {};      // 建立物件
            Object.keys(item_keys).forEach(function(i_key){
                // console.log(item_keys[i_key]+"：" ,listData[i][item_key]);
                sort_listData[i][item_keys[i_key]] = window[to_module][i][i_key];
            })
        }
        // console.log('sort_listData:', sort_listData);
        var htmlTableValue = JSON.stringify(sort_listData);
        document.getElementById(to_module+'_htmlTable').value = htmlTableValue;
        // console.log(listData);
    }

    // 精簡前語法
    // var rows = document.getElementsByTagName("td");
        // Array.from(rows).forEach(function(row) {
        //     // 綁定失去焦點事件
        //     row.addEventListener("blur", function(e) {
        //         // 取得編輯後的值及目前行的編號
        //             var newValue = (this.innerHTML).replace(/[^\d.-]/g, "");
        //                 newValue = Number(newValue);
        //             var rowId = this.id;
        //             var rowName = this.getAttribute("name");

        //             // 取得原始資料
        //             var originalValue = this.getAttribute("data-original-value").replace(/[^\d.-]/g, "");
        //                 originalValue = Number(originalValue);

        //         // 如果編輯後的值與原始資料不同，才更新至資料庫
        //         if (newValue !== originalValue) {
        //             this.innerHTML = newValue;
        //             // 傳送 Ajax 請求至伺服器端，以下省略...
        //             console.log("失去焦點事件觸發", rowId, rowName, originalValue, newValue);
        //         }
        //     });

        //     // 綁定按下 enter 事件
        //     row.addEventListener("keydown", function(e) {
        //         // 如果按下的是 Enter 鍵
        //         if (e.keyCode == 13) {
        //             // 阻止預設行為 (移動到下一行)
        //             e.preventDefault();

        //             // 取得編輯後的值及目前行的編號
        //                 var newValue = (this.innerHTML).replace(/[^\d.-]/g, "");
        //                     newValue = Number(newValue);
        //                 var rowId = this.id;
        //                 var rowName = this.getAttribute("name");

        //                 // 取得原始資料
        //                 var originalValue = this.getAttribute("data-original-value").replace(/[^\d.-]/g, "");
        //                     originalValue = Number(originalValue);

        //             // 如果編輯後的值與原始資料不同，才更新至資料庫
        //             if (newValue !== originalValue) {
        //                 this.innerHTML = newValue;
        //                 // 傳送 Ajax 請求至伺服器端，以下省略...
        //                 console.log("按下 Enter 鍵事件觸發", rowId, rowName, originalValue, newValue);
        //             }

        //             // 取消表格單元格的 Focus 狀態
        //             this.blur();
        //         }
        //     });

        //     // 綁定單元格編輯開始事件
        //     row.addEventListener("click", function(e) {
        //         // 獲取當前單元格的原始值並設置到屬性中
        //         this.setAttribute("data-original-value", this.textContent.trim());
        //     });
        // });



//     // 呼叫編輯Price歷年報價
    //     let fix_quotes = [...document.querySelectorAll('.fix_quote')];
    //     for(let fix_quote of fix_quotes){
    //         fix_quote.onclick = e => {
    //             let swal_content = e.target.name+'_id:'+e.target.id+'=';
    //             // console.log('e:',e.target.id);
    //             var target_id = e.target.id;
    //             $('#fix_part_no, #fix_part_table').empty();
    //                 // step1.將原陣列逐筆繞出來
    //                 Object(pno).forEach(function(row){          
    //                     if(row['id'] == target_id){
    //                         // step2.鋪畫面到module
    //                         $('#fix_part_no').append(row['part_no']);
    //                         var lastYear_num = thisYear_num-1;     
    //                         var row_price = row['price'];
    //                         if(row_price != 0 || row_price != ''){
    //                             row_price = JSON.parse(row_price);
    //                         }else{
    //                             row_price = {};
    //                         }

    //                         if(row_price[lastYear_num] != undefined) {
    //                             var lastYear_price = row_price[lastYear_num];
    //                         }else{
    //                             var lastYear_price = 0;
    //                         }
    //                         if(row_price[thisYear_num] != undefined) {
    //                             var thisYser_price = row_price[thisYear_num];
    //                         }else{
    //                             var thisYser_price = 0;
    //                         }
    //                         // Object.keys(row_price).forEach(function(price_key){  
    //                             //     let edit_quoteYear = '<tr><td><input type="number" name="_quoteYear" id="_quoteYear" required placeholder="_報價年度" value="'+price_key+'"></td>';
    //                             //     edit_quoteYear += '<td><input type="number" name="_price" id="_price" required placeholder="_單價" value="'+row_price[price_key]+'"></td></tr>';
    //                             //     $('#fix_part_table').append(edit_quoteYear);
    //                             // })  
    //                         var edit_quoteYear  = '<tr><td>'+lastYear_num+'</td>';
    //                             // edit_quoteYear += '<td><input type="number" name="price['+lastYear_num+']" required placeholder="_去年報價" value="'+lastYear_price+'"></td></tr>';
    //                             edit_quoteYear += '<td>'+lastYear_price+'</td></tr>';
    //                             edit_quoteYear += '<tr><td>'+thisYear_num+'</td>';
    //                             edit_quoteYear += '<td><input type="number" name="price['+thisYear_num+']" required placeholder="_今年報價" value="'+thisYser_price+'"></td></tr>';
    //                         $('#fix_part_table').append(edit_quoteYear);
    //                         document.querySelector('#pno_price_id').value = target_id; 

    //                         return; // 找到後就可以結束迴圈了
    //                     }
    //                 })


    //             var fix_price = new bootstrap.Modal(document.getElementById('fix_price'));
    //             fix_price.show();

    //             // $.ajax({
    //             //     url:'api.php',
    //             //     method:'post',
    //             //     async: false,                                           // ajax取得數據包後，可以return的重要參數
    //             //     dataType:'json',
    //             //     data:{
    //             //         function: 'cheng_flag',           // 操作功能
    //             //         table: e.target.name,
    //             //         id: e.target.id,
    //             //         flag: e.target.value
    //             //     },
    //             //     success: function(res){
    //             //         let res_r = res["result"];
    //             //         let res_r_flag = res_r["flag"];
    //             //         // console.log(res_r_flag);
    //             //         if(res_r_flag == 'Off'){
    //             //             e.target.classList.remove('btn-success');
    //             //             e.target.classList.add('btn-warning');
    //             //             e.target.value = 'Off';
    //             //             e.target.innerText = 'Off';
    //             //         }else{
    //             //             e.target.classList.remove('btn-warning');
    //             //             e.target.classList.add('btn-success');
    //             //             e.target.value = 'On';
    //             //             e.target.innerText = 'On';
    //             //         }
    //             //         swal_action = 'success';
    //             //         swal_content += res_r_flag+' 套用成功';
    //             //     },
    //             //     error: function(e){
    //             //         swal_action = 'error';
    //             //         swal_content += res_r_flag+' 套用失敗';
    //             //         console.log("error");
    //             //     }
    //             // });

    //             // swal('套用人事資料' ,swal_content ,swal_action, {buttons: false, timer:2000}).then(()=>{location.href = url;});     // deley3秒，then自動跳轉畫面
    //             // swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:1000});

    //         }
    //     }
//    // fun-1.鋪info畫面
    //     function fix_price_form(){
    //         var fix_price_form = document.getElementById('fix_price_form');
    //         // var fix_price_formDatd = new FormData(fix_price_form);
    //         // var username = form.elements["username"].value;
    //         // console.log('fix_price_form:', fix_price_form.elements["price["+thisYear_num+"]"].value);
    //         // console.log('id:', fix_price_form.elements["id"].value);
    //         var pno_id = fix_price_form.elements["id"].value;
    //         var thisYear_price = {};
    //         thisYear_price[thisYear_num] = Number(fix_price_form.elements["price["+thisYear_num+"]"].value);

    //         $.ajax({
    //             url:'api.php',
    //             method:'post',
    //             async: false,                                           // ajax取得數據包後，可以return的重要參數
    //             dataType:'json',
    //             data:{
    //                 function: 'update_price',           // 操作功能
    //                 id: pno_id,
    //                 price: JSON.stringify(thisYear_price)
    //             },
    //             success: function(res){
    //                 let res_r = res["result"];
    //                 let res_r_flag = res_r["flag"];
    //                 // console.log(res_r_flag);
    //                 if(res_r_flag == 'Off'){
    //                     e.target.classList.remove('btn-success');
    //                     e.target.classList.add('btn-warning');
    //                     e.target.value = 'Off';
    //                     e.target.innerText = 'Off';
    //                 }else{
    //                     e.target.classList.remove('btn-warning');
    //                     e.target.classList.add('btn-success');
    //                     e.target.value = 'On';
    //                     e.target.innerText = 'On';
    //                 }
    //                 swal_action = 'success';
    //                 swal_content += res_r_flag+' 套用成功';
    //             },
    //             error: function(e){
    //                 swal_action = 'error';
    //                 swal_content += res_r_flag+' 套用失敗';
    //                 console.log("error");
    //             }
    //         });
    //     }