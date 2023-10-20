
// // // 第n頁：catalog_modal 篩選 function
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
                    var input_cb = '<input type="checkbox" name="cata_SN_amount['+cata['SN']+']" id="'+cata['SN']+'" class="select_item" value="'+add_amount+'" checked onchange="check_item(this.id)" disabled>';
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
    function search_fun(search){
        mloading("show");                       // 啟用mLoading

        var fun = 'in_sign_badge';
        search = search.trim();
        $('#in_sign_badge').empty();

        if(!search || (search.length < 8)){
            alert("查詢工號字數最少 8 個字以上!!");
            $("body").mLoading("hide");
            return false;
        } 

        $.ajax({
            // url:'http://tneship.cminl.oa/hrdb/api/index.php',
            url:'http://localhost/hrdb/api/index.php',
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
                    var obj_val = res_r[0];                                         // 取Object物件0

                    if(fun == 'in_sign_badge'){     // 搜尋申請人上層主管emp_id
                        if(obj_val){                            
                            $('#in_sign_badge').append('<div class="tag">' + obj_val.cname + '<span class="remove">x</span></div>');
                        }else{
                            // alert("查無工號["+search+"]!!");
                            document.getElementById('key_word').value = '';                         // 將欄位cname清除
                            alert('查無工號：'+ search +' !!');
                        }
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
        document.getElementById('key_word').value = '';                         // 將欄位cname清除
        // $('#in_sign_badge').empty();
    });

// // // 第三頁：searchUser function 

// // // Edit選染
    // 引入action資料
    // var action = '<?=$action;?>';
    function edit_item(){
        // 引入receive_row資料作為Edit
        // var receive_row = <?=json_encode($receive_row);?>;
        var receive_item = {
            "plant"          : "plant/申請單位", 
            "dept"           : "dept/部門名稱", 
            "sign_code"      : "sign_code/部門代號", 
            "emp_id"         : "emp_id/工號",
            "cname"          : "cname/申請人姓名",
            "extp"           : "extp/分機",
            // "local_id"       : "local_id/領用站點",              // 改由php echo產生
            "ppty"           : "** ppty/需求類別",
            "omager"         : "omager/上層主管工號",
            "receive_remark" : "receive_remark/用途說明",
            // "created_emp_id" : "created_emp_id/開單人工號",
            // "created_cname"  : "created_cname/開單人姓名",
            // "idty"           : "idty",
            "uuid"           : "uuid",
            "cata_SN_amount" : "** cata_SN_amount"
            // "sin_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原陣列逐筆繞出來
        Object.keys(receive_item).forEach(function(receive_key){
            if(receive_key == 'ppty'){                      // ppty/需求類別
                document.querySelector('#'+receive_key+'_'+receive_row[receive_key]).checked = true;
                
            }else if(receive_key == 'cata_SN_amount'){      //cata_SN_amount 購物車
                var receive_row_cart = JSON.parse(receive_row[receive_key]);
                Object.keys(receive_row_cart).forEach(function(cart_key){
                    add_item(cart_key, receive_row_cart[cart_key], 'off');
                })
            }else{
                document.querySelector('#'+receive_key).value = receive_row[receive_key]; 
            }
        })

        // 鋪設logs紀錄
        // var json = JSON.parse('<?=json_encode($logs_arr)?>');
        // var uuid = '<=$receive_row["uuid"]?>';
        var forTable = document.querySelector('.logs tbody');
        for (var i = 0, len = json.length; i < len; i++) {
            forTable.innerHTML += 
                '<tr><td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + '</td><td>' + json[i].remark + '</td></tr>';
        }
    }

    // 簽核類型渲染
    function submit_item(idty, idty_title){
        $('#idty, #idty_title, #action').empty();
        document.getElementById('action').value = 'sign';
        document.getElementById('idty').value = idty;
        $('#idty_title').append(idty_title);
        var forwarded_div = document.getElementById('forwarded');
        if(forwarded_div && (idty == 5)){
            forwarded_div.classList.remove('unblock');           // 按下轉呈 = 解除 加簽
        }else{
            forwarded_div.classList.add('unblock');              // 按下其他 = 隱藏
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

    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();

        // All resources finished loading! // 關閉mLoading提示
        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
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

        edit_item();        // 啟動鋪設畫面

    })