
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

            // swal('套用資料' ,swal_content ,swal_action, {buttons: false, timer:2000}).then(()=>{location.href = url;});     // deley3秒，then自動跳轉畫面
            swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:1000});

        }
    }

    // // // 第三頁：searchUser function 
    // // fun3-1：search Key_word
    function search_fun(){
        mloading("show");                       // 啟用mLoading
        let search = $('.search > input').val().trim();
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
                        var com_val = obj_val.emp_id+','+obj_val.cname;
                        tagsInput_me(com_val);

                    }else{
                        alert('查無工號：'+ search +' !!');
                    }
                }
            },
            error (){
                console.log("search error");
            }
        })
        document.querySelector('#key_word').value = '';
        $("body").mLoading("hide");
    }
    // // fun3-2：移除單項模組
    $('#selectUserItem').on('click', '.remove', function() {
        var tagIndex = $(this).closest('.tag').index();
        let tagg = tags[tagIndex];                       // 取得目標數值 emp_id,cname
        let emp_id = tagg.substr(0, tagg.search(','));   // 指定 emp_id
        let tag_user = document.getElementById(emp_id);
        if(tag_user){
            tag_user.value = tagg;
        }
        tags.splice(tagIndex, 1);           // 自陣列中移除
        $(this).closest('.tag').remove();   // 自畫面中移除
        let edit_pm_emp_id = document.getElementById('edit_pm_emp_id');
        if(edit_pm_emp_id){
            edit_pm_emp_id.value = tags;
        }
    });
    // // fun3-3：清除search keyWord
    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }
    // // fun3-3：選染功能
    function tagsInput_me(val) {
        let emp_id = val.substr(0, val.search(','));    // 取第1位 指定emp_id
        let cname = val.substr(val.search(',',)+1);     // 取第2位 指定cname
        if (val !== '') {
            tags.push(val);
            $('#selectUserItem').append('<div class="tag">' + cname + '<span class="remove">x</span></div>');
            let tag_user = document.getElementById(emp_id);
            if(tag_user){
                tag_user.value = '';
            }
            let edit_pm_emp_id = document.getElementById('edit_pm_emp_id');
            if(edit_pm_emp_id){
                edit_pm_emp_id.value = tags;
            }
        }
    }
    // // // 第三頁：searchUser function 

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
        // 切換指定NAV分頁
        //激活选项卡
        $('.nav-tabs button:eq(' + activeTab + ')').tab('show');
    });