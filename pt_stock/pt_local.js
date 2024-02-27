
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

    function add_module(to_module){     // 啟用新增模式
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_button, #'+to_module+'_modal_delect_btn, #edit_'+to_module+'_info').empty();   // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                                        // reset清除表單
        var add_btn = '<input type="submit" name="'+to_module+'_submit" class="btn btn-primary" value="新增">';
        $('#'+to_module+'_modal_action').append('新增');                      // model標題
        $('#'+to_module+'_modal_button').append(add_btn);                     // 儲存鈕
        var reset_btn = document.getElementById(to_module+'_reset_btn');   // 指定清除按鈕
        reset_btn.classList.remove('unblock');                  // 新增模式 = 解除
        document.querySelector("#edit_"+to_module+" .modal-header").classList.remove('edit_mode_bgc');
        document.querySelector("#edit_"+to_module+" .modal-header").classList.add('add_mode_bgc');
    }
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_button, #'+to_module+'_modal_delect_btn, #edit_'+to_module+'_info').empty();   // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                                        // reset清除表單
        var reset_btn = document.getElementById(to_module+'_reset_btn');   // 指定清除按鈕
        reset_btn.classList.add('unblock');                     // 編輯模式 = 隱藏
        document.querySelector("#edit_"+to_module+" .modal-header").classList.remove('add_mode_bgc');
        document.querySelector("#edit_"+to_module+" .modal-header").classList.add('edit_mode_bgc');
        // 參數說明: to_module = 來源與目的 site、fab、local
        $('#edit_pm_emp_id').value = '';
        $('#selectUserItem').empty();
        tags = [];                                                      // 清除tag名單陣列
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
                    }else if(item_key == 'pm_emp_id'){                          // 20231108_pm_emp_id多名單
                        // 第0階段：套用既有數據
                        var intt_val_str = row['pm_emp_id'];                    // 引入PM資料
                        var intt_val = [];
                        // if(intt_val_str.length !== 0){                          // 過濾原本pm字串不能為空
                        if(intt_val_str){                                       // 過濾原本pm字串不能為空
                            intt_val = intt_val_str.split(',');                 // 直接使用 split 方法得到陣列
                            for(let i=0; i < intt_val.length; i=i+2){   
                                tagsInput_me(intt_val[i]+','+intt_val[i+1]);    // 利用合併帶入
                            }
                        }

                    }else if(item_key == 'buy_ty'){
                        document.querySelector('#edit_'+to_module+' #edit_buy_'+row[item_key]).checked = true;
                    }else{
                        document.querySelector('#edit_'+to_module+' #edit_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

                // step3-3.開啟 彈出畫面模組 for user編輯
                // edit_myTodo_btn.click();
                var add_btn = '<input type="submit" name="edit_'+to_module+'_submit" class="btn btn-primary" value="儲存">';
                var del_btn = '<input type="submit" name="delete_'+to_module+'" value="刪除'+to_module+'" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#'+to_module+'_modal_action').append('編輯');          // model標題
                $('#'+to_module+'_modal_delect_btn').append(del_btn);     // 刪除鈕
                $('#'+to_module+'_modal_button').append(add_btn);         // 儲存鈕
            }
        })
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
                    id   : e.target.id,
                    flag : e.target.value
                },
                success: function(res){
                    var res_r = res["result"];
                    var res_r_flag = res_r["flag"];
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
            
            if(swal_action == 'error'){
                swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:3000});
            }else{
                var sinn = 'change_flag - ( '+swal_content+' ) <b>'+ swal_action +'</b>&nbsp!!';
                inside_toast(sinn);
            }

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

    $(document).ready(function(){
        // dataTable 2 https://ithelp.ithome.com.tw/articles/10272439
        $('#local_list').DataTable({
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
        // 切換指定NAV分頁

            //激活选项卡
            // $('.nav-tabs button:eq(' + activeTab + ')').tab('show');
    });