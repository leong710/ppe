// // // utility fun
    // Bootstrap Alarm function
    function alert(message, type) {
        var alertPlaceholder = document.getElementById("liveAlertPlaceholder");      // Bootstrap Alarm
        var wrapper = document.createElement('div');
        wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message 
                            + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        alertPlaceholder.append(wrapper);
    }
    // fun3-3：吐司顯示字條 // init toast
    function inside_toast(sinn){
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }

    // fun-1.鋪編輯畫面
    function edit_module(row_id){
        $('#modal_action, #checked_tbody, #checked_remark, #checked_delete_id').empty();        // 清除model 大部功能
        $('#aid_id, #created_at, #fab_title, #updated_user ,#form_type ,#checked_year ,#half').empty();     // 清除model 文件表頭

        // step1.Lists中帶出檢視對象
        checked_lists.forEach(function(row){
            if(row['id'] == row_id){
                Object(checked_item).forEach(function(item_key){
                    if(item_key == 'id'){
                        document.querySelector('#checked_delete_id').value = row['id'];         // 鋪上delete_id = this id.no for delete form
                        $('#aid_id').append('aid_'+row['id']);                                  // aid_id
                        
                    }else if(item_key == 'fab_title'){
                        $('#fab_title').append(row['fab_title']+'('+row['fab_remark']+')');     // fab_title

                    }else if(item_key == 'stocks_log'){                                         // stocks_log
                        var json = [];                                                          // 清除json陣列
                        // 第0階段：取出stocks_log紀錄
                        var stocks_log_arr = row['stocks_log'].split('_,');     // 切割成陣列
                        for( i=0; i<stocks_log_arr.length; i++ ){
                            json[i] = JSON.parse(stocks_log_arr[i]);  // 文字串轉成JSON物件
                        }
                        var forTable = document.querySelector('.for-table tbody');
                        for (var i = 0, len = json.length; i < len; i++) {
                            forTable.innerHTML += 
                                '<tr>' +
                                    '<td class="word_bk">' + json[i].fab_title + '</br>' + json[i].local_title + '</td>' +
                                    '<td style="font-size: 12px;">' + json[i].cate_no + '.' + json[i].cate_title + '</td>' +
                                    '<td class="word_bk">' + json[i].cata_SN + '</br>' + json[i].pname + '</td>' +
                                    '<td>' + json[i].size + '</td>' +
                                    '<td>' + json[i].standard_lv + '</td>' +
                                    '<td>' + json[i].amount + '</td>' +
                                    '<td class="word_bk">' + json[i].stock_remark + '</td>' +
                                    '<td style="font-size: 12px;">' + json[i].lot_num + '</br>' + json[i].po_no + '</td>' +
                                    '<td style="font-size: 12px;">' + json[i].updated_at + '</br>by：' + json[i].updated_user + '</td>' +
                                    // 這裡的updated_user指的是儲存物編輯人
                                '</tr>';
                        }

                    }else if(item_key == 'checked_remark'){                          // checked_remark
                        document.getElementById('checked_remark').value = row['checked_remark'];

                    }else{
                        $('#'+item_key).append(row[item_key]);                  // anoth
                    }

                })
                return;
            }
        })
    }

    function put_in(){
        checked_lists.forEach(function(row){
            let row_key = row['checked_year']+'_'+row['half']+'_'+row['form_type']+'_'+row['fab_id'];
            let row_btn = '<button type="button" class="op_tab_btn reviewBtn " value="'+row['id']+'" onclick="edit_module(this.value)" title="'+row['created_at']+' / '+row['updated_user']+' " '; 
            row_btn += 'data-bs-toggle="modal" data-bs-target="#review_checked"><h4 class="mb-0"><i class="fa-regular fa-file-lines mb-0"></i></h4><h7>'+row['created_at'].substr(0, 10).replace(/-/g,"")+'</h7></button>';
            $('#'+row_key).append(row_btn);                  // anoth
        })
    }

    $(function(){
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();

        // 定義檢視按鈕與對應功能
        let reviewBtns = [...document.querySelectorAll('.reviewBtn')];
        for(let reviewBtn of reviewBtns){
            reviewBtn.onclick = e => {
                edit_module(e.target.value);
            }
        }

    })

    $(document).ready(function () {

        // 假如index找不到當下存在已完成的表單，就alarm它!
        if (check_yh_list_num == '0') {
            let message  = '*** '+ thisYear +' '+ half +'年度 PPE儲存量確認開始了! 請務必在指定時間前完成確認 ~ ';
            alert( message, 'danger')
        }

        put_in();
    })


        