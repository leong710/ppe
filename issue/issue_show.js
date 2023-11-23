
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

    // // // Edit選染
    function edit_item(){
        // 引入issue_row資料作為Edit
        // var issue_row = <?=json_encode($issue_row);?>;
        var issue_item = {
            "in_user_id"     : "in_user_id/工號",
            "cname_i"        : "cname_i/申請人姓名",
            "in_local"       : "in_local/領用站點",
            "ppty"           : "** ppty/需求類別",
            "id"             : "id",
            "item"           : "** item"
            // "sin_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原陣列逐筆繞出來
        Object.keys(issue_item).forEach(function(issue_key){
            if(issue_key == 'ppty' && issue_row[issue_key]){                      // ppty/需求類別
                var ppty = document.querySelector('#'+issue_key+'_'+issue_row[issue_key]);
                if(ppty){
                    document.querySelector('#'+issue_key+'_'+issue_row[issue_key]).checked = true;
                }
                
            }else if(issue_key == 'item' && issue_row[issue_key]){      //item 購物車
                var issue_row_cart = JSON.parse(issue_row[issue_key]);
                Object.keys(issue_row_cart).forEach(function(cart_key){
                    add_item(cart_key, issue_row_cart[cart_key], 'off');
                })
            }else if(issue_row[issue_key]){
                var row_key = document.querySelector('#'+issue_key);
                if(row_key){
                    document.querySelector('#'+issue_key).value = issue_row[issue_key]; 
                }
            }
        })


        // var id = '<=$issue_row["id"]?>';
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
    }

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
    
    $(document).ready(function () {

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

        edit_item();        // 啟動鋪設畫面
        $('.nav-tabs button:eq(1)').tab('show');        // 切換頁面到購物車

    })


