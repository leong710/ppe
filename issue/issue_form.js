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
                    var input_cb = '<input type="checkbox" name="item['+cata['SN']+']" id="'+cata['SN']+'" class="select_item" value="'+add_amount+'" checked onchange="check_item(this.id)">';
                    var add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+'</td><td>'+cata['SN']+'</td><td>'+cata['pname']+'</td><td>'+cata['model']+'</td><td>'+cata['size']+'</td><td>'+add_amount+'</td><td>'+cata['unit']+'</td></tr>';
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
    function search_fun(){
        mloading("show");                       // 啟用mLoading
        let search = $('#emp_id').val().trim();
        $('#cname').empty();

        if(!search || (search.length < 8)){
            alert("查詢工號字數最少 8 個字以上!!");
            $("body").mLoading("hide");
            return false;
        } 
        $.ajax({
            url:'http://tneship.cminl.oa/hrdb/api/index.php',
            method:'get',
            dataType:'json',
            data:{
                functionname: 'search',                     // 操作功能
                uuid: '39aad298-a041-11ed-8ed4-2cfda183ef4f',
                search: search                              // 查詢對象key_word
            },
            success: function(res){
                var res_r = res["result"];
                // 將結果進行渲染
                if (res_r !== '') {
                    let obj_val = res_r[0];                                         // 取Object物件0
                    var input_cname = document.getElementById('cname');
                    if(obj_val && input_cname){
                        input_cname.value = obj_val.cname;              // 將欄位帶入數值 = cname
                        var sinn = '以工號&nbsp<b>'+obj_val.emp_id+'/'+obj_val.cname+'</b>&nbsp帶入資訊...完成!!';
                        inside_toast(sinn);
                    }else{
                        // alert("查無工號["+search+"]!!");
                        input_cname.value = '';                         // 將欄位cname清除
                        var sinn = '查無工號&nbsp<b>'+ search +'</b>&nbsp!!';
                        inside_toast(sinn);
                    }
                }

            },
            error (){
                console.log("search error");
            }
        })
        $("body").mLoading("hide");
    }

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
            "in_user_id"     : "in_user_id/工號",
            "cname_i"        : "cname_i/申請人姓名",
            "in_local"       : "in_local/領用站點",
            "ppty"           : "** ppty/需求類別",
            "id"             : "id",
            "item"           : "** item"
            // "sin_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原排程陣列逐筆繞出來
        Object.keys(issue_item).forEach(function(issue_key){
            if(issue_key == 'ppty'){      // ppty/需求類別
                var ppty = document.querySelector('#'+issue_key+'_'+issue_row[issue_key]);
                if(ppty){
                    document.querySelector('#'+issue_key+'_'+issue_row[issue_key]).checked = true;
                }
                
            }else if(issue_key == 'item'){      //item 購物車
                var issue_row_cart = JSON.parse(issue_row[issue_key]);
                Object.keys(issue_row_cart).forEach(function(cart_key){
                    add_item(cart_key, issue_row_cart[cart_key], 'off');
                })
            }else{
                var row_key = document.querySelector('#'+issue_key);
                if(row_key){
                    document.querySelector('#'+issue_key).value = issue_row[issue_key]; 
                }
            }
        })

        // 鋪設logs紀錄
        // var json = JSON.parse('<?=json_encode($logs_arr)?>');
        // var id = '<?=$issue_row["id"]?>';
        var forTable = document.querySelector('.logs tbody');
        for (var i = 0, len = json.length; i < len; i++) {
            forTable.innerHTML += 
                '<tr>' + '<td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + '</td><td>' + json[i].remark + '</td>' +
                    '<?php if($_SESSION[$sys_id]["role"] <= 1){ ?><td>' + '<form action="" method="post">'+
                        `<input type="hidden" name="log_id" value="` + [i] + `";>` +
                        `<input type="hidden" name="id" value="` + id + `";>` +
                        `<input type="submit" name="delete_log" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">` +
                    '</form>' + '</td><?php } ?>' +
                '</tr>';
        }
        document.getElementById('logs_div').classList.remove('unblock');           // 購物車等於0，disabled

    }

    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
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

        // <!-- 有填數量自動帶入+號按鈕，沒數量自動清除+號按鈕的value -->
        // let amounts = [...document.querySelectorAll('.amount')];
        // for(let amount of amounts){
        //     amount.onchange = e => {
        //     // amount.onblur = e => {
        //         let amount_id = e.target.id;
        //         if(amount.value == ''){
        //             // document.getElementById('catalog_SN_'+ amount_id).checked=false;     // 取消選取 = 停用
        //             document.getElementById('add_'+ amount_id).value = '';
        //         } else {
        //             // document.getElementById('catalog_SN_'+ amount_id).checked=true;      // 增加選取 = 停用
        //             document.getElementById('add_'+ amount_id).value = amount.value;
        //         }
        //     }
        // }
        
        // 確認action不是新表單，就進行Edit模式渲染
        if(action != 'create'){                                // 確認action不是新表單，就進行Edit模式渲染
            edit_item();
            $('.nav-tabs button:eq(1)').tab('show');        // 切換頁面到購物車
        }

    })


    