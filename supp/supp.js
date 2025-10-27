
    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();

        // 20231128 以下為上傳後"iframe"的部分
            // 監控按下送出鍵後，打開"iframe"
            upload_excel_btn.addEventListener('click', function() {
                iframeLoadAction();
                checkExcelForm();
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
                    document.getElementById('excelTable').value = excel_json.value;

                } else if(stopUpload) {
                    console.log('請確認資料是否正確');
                }else{
                    console.log('找不到 ? 元素');
                }
            });
    })

    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }

    // 20231129 合併：add mode function
    function add_module(to_module){     // 啟用新增模式
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_delect_btn, #'+to_module+'_modal_submit_btn, #edit_'+to_module+'_info').empty();     // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                                              // reset清除表單

        $('#'+to_module+'_modal_action').append('新增');                                                    // model標題文字
        document.querySelector('#edit_'+to_module+' .modal-header').classList.remove('edit_mode_bgc');      // model標題底色--去除編輯底色
        document.querySelector('#edit_'+to_module+' .modal-header').classList.add('add_mode_bgc');          // model標題底色--增加新增底色

        var add_btn = '<input type="submit" name="add_'+to_module+'_submit" class="btn btn-primary" value="新增">';
        $('#'+to_module+'_modal_submit_btn').append(add_btn);                                               // 添加儲存鈕

        var reset_btn = document.getElementById(to_module+'_reset_btn');                                    // 指定清除按鈕
        reset_btn.classList.remove('unblock');                                                              // 新增模式 = 解除

            if(to_module == 'supp'){
                var activeTab = 0;
            }else if(to_module == 'contact'){
                var activeTab = 1;
            }else{
                var activeTab = 0;
            }
            document.getElementById(to_module+'_activeTab').value = activeTab;
    }

    // 20231129 合併：fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_delect_btn, #'+to_module+'_modal_submit_btn, #edit_'+to_module+'_info').empty();     // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                                              // reset清除表單

        $('#'+to_module+'_modal_action').append('編輯');                                                    // model標題
        document.querySelector('#edit_'+to_module+' .modal-header').classList.remove('add_mode_bgc');       // model標題底色--去除新增底色
        document.querySelector('#edit_'+to_module+' .modal-header').classList.add('edit_mode_bgc');         // model標題底色--新增編輯底色
        
        var add_btn = '<input type="submit" name="add_'+to_module+'_submit" class="btn btn-primary" value="儲存">';
        $('#'+to_module+'_modal_submit_btn').append(add_btn);                                               // 添加儲存鈕

        var reset_btn = document.getElementById(to_module+'_reset_btn');                                    // 指定清除按鈕
        reset_btn.classList.add('unblock');                                                                 // 編輯模式 = 隱藏

        var del_btn = '&nbsp&nbsp&nbsp&nbsp<input type="submit" name="delete_'+to_module+'" value="刪除'+to_module+'" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
        $('#'+to_module+'_modal_delect_btn').append(del_btn);     // 刪除鈕

            if(to_module == 'supp'){
                var activeTab = 0;
            }else if(to_module == 'contact'){
                var activeTab = 1;
            }else{
                var activeTab = 0;
            }
            document.getElementById(to_module+'_delete_activeTab').value = activeTab;
            document.getElementById(to_module+'_activeTab').value = activeTab;

        // remark: to_module = 來源與目的 supp、contact
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
                    }else{
                        document.querySelector('#edit_'+to_module+' #edit_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

                return;
            }
        })
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

    // 切換上架/下架開關
    let flagBtns = [...document.querySelectorAll('.flagBtn')];
    for(let flagBtn of flagBtns){
        flagBtn.onclick = e => {
            let swal_content = e.target.name+'_id:'+e.target.id+'=';
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
            if(to_module == "supp"){
                var item_keys = {
                    "scname"        : "供應商中文名稱", 
                    "sname"         : "供應商英文名稱", 
                    "inv_title"     : "發票抬頭", 
                    "cate_no"       : "分類", 
                    "comp_no"       : "統編", 
                    "_address"      : "發票地址", 
                    "contact"       : "聯絡人", 
                    "phone"         : "連絡電話", 
                    "email"         : "電子信箱", 
                    "fax"           : "傳真",
                    "supp_remark"   : "備註說明",
                    "flag"          : "開關",
                    "updated_at"    : "最後更新",
                    "updated_user"  : "最後編輯"
                };
            }else if(to_module == "contact"){
                var item_keys = {
                    "cname"         : "聯絡人姓名", 
                    "phone"         : "連絡電話", 
                    "email"         : "電子信箱", 
                    "fax"           : "傳真",
                    "comp_no"       : "統編", 
                    "contact_remark": "備註說明",
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
                sort_listData[i][item_keys[i_key]] = window[to_module][i][i_key];
            })
        }
        var htmlTableValue = JSON.stringify(sort_listData);
        document.getElementById(to_module+'_htmlTable').value = htmlTableValue;
    }

