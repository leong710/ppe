// // // 第一頁：info modal function
    // var catalog = <?=json_encode($catalogs);?>;                        // 引入catalogs資料
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

        if(add_amount <= 0 ){
            var swal_content = cata_SN+' 沒有填數量!'+' 加入失敗';
            var swal_action = 'error';
            swal(swal_title ,swal_content ,swal_action);      // swal需要按鈕確認

        }else{
            var check_item_return = check_item(cata_SN, 0);    // call function 查找已存在的項目，並予以清除。
            Object(catalog).forEach(function(cata){          
                if(cata['SN'] === cata_SN){
                    var input_cb = '<input type="checkbox" name="cata_SN_amount['+cata['SN']+'][need]" id="'+cata['SN']+'" class="select_item" value="'+add_amount+'" checked onchange="check_item(this.id)">';
                    var collect_cb = '<input name="cata_SN_amount['+cata['SN']+'][pay]" class="unblock" value="'+add_amount+'">';
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
                inside_toast(sinn);
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
            // url:'http://tneship.cminl.oa/hrdb/api/index.php',    // 正式
            url:'http://tw059332n.cminl.oa/hrdb/api/index.php',     // 開發
            method:'get',
            dataType:'json',
            data:{
                // functionname: 'search',                          // 操作功能
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
    // var action = '<?=$action;?>';                       // 引入action資料
    function edit_item(){
        // var receive_row = <?=json_encode($receive_row);?>;                        // 引入receive_row資料作為Edit
        var receive_item = {
            "plant"          : "plant/申請單位", 
            "dept"           : "dept/部門名稱", 
            "sign_code"      : "sign_code/部門代號", 
            "emp_id"         : "emp_id/工號",
            "cname"          : "cname/申請人姓名",
            "extp"           : "extp/分機",
            "local_id"       : "local_id/領用站點",
            "ppty"           : "** ppty/需求類別",
            "omager"         : "** omager/上層主管工號",
            "receive_remark" : "receive_remark/用途說明",
            "created_emp_id" : "created_emp_id/開單人工號",
            "created_cname"  : "created_cname/開單人姓名",
            // "idty"           : "idty",
            "uuid"           : "uuid",
            "cata_SN_amount" : "** cata_SN_amount"
            // "sign_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原排程陣列逐筆繞出來
        Object.keys(receive_item).forEach(function(receive_key){
            if(receive_key == 'ppty'){      // ppty/需求類別
                document.querySelector('#'+receive_key+'_'+receive_row[receive_key]).checked = true;
                
            }else if(receive_key == 'cata_SN_amount'){      //cata_SN_amount 購物車
                var receive_row_cart = JSON.parse(receive_row[receive_key]);
                Object.keys(receive_row_cart).forEach(function(cart_key){
                    add_item(cart_key, receive_row_cart[cart_key]['need'], 'off');
                })

            }else if(receive_key == 'omager'){             //omager 上層主管工號
                document.getElementById('omager').value = receive_row[receive_key];
                search_fun(receive_row[receive_key]);

            }else{
                document.querySelector('#'+receive_key).value = receive_row[receive_key]; 

            }
        })

        // 鋪設logs紀錄
        // var json = JSON.parse('<?=json_encode($logs_arr)?>');
        // var uuid = '<?=$receive_row["uuid"]?>';
        var forTable = document.querySelector('.logs tbody');
        for (var i = 0, len = json.length; i < len; i++) {
            forTable.innerHTML += 
                '<tr>' + '<td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + '</td><td>' + json[i].remark + 
                    '<?php if($_SESSION[$sys_id]["role"] <= 1){ ?>' + '<form action="" method="post">'+
                        `<input type="hidden" name="log_id" value="` + [i] + `";>` +
                        `<input type="hidden" name="uuid" value="` + uuid + `";>` +
                        `<input type="submit" name="delete_log" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">` +
                    '</form>' + '<?php } ?>' +
                '</td>' +'</tr>';
        }
        document.getElementById('logs_div').classList.remove('unblock');           // 購物車等於0，disabled
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