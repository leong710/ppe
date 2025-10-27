    // 產出service window
    function update_sw(){
        var sw = {};
        fab.forEach(el => {
            if(el.flag == 'Off'){                           // 排除flag已關閉
                return;
            }
            sw[el.fab_title] = [];
            var pm_emp_id_str = el.pm_emp_id;
            
            if(pm_emp_id_str != null && pm_emp_id_str.length > 0){      // 預防null或異常
                var pm_emp_id_arr = pm_emp_id_str.split(",");
                pm_emp_id_arr.forEach(pm_s =>{
                    if(!pm_s || (pm_s.length < 8)){
                        return false;
                    }
                    let pm_s_arr = search_sw_fun(pm_s);                 // call:查詢email
                    console.log(pm_s_arr)
                    // if(pm_s_arr.length > 0){
                        sw[el.fab_title].push(pm_s_arr);
                    // }
                })
            }
        });
        
        let json_str = JSON.stringify(sw);
        output_json(json_str)                               // call:輸出json文檔
    }

    // search emp_id
    function search_sw_fun(search){
        search = search.trim();
        var result = {};
        $.ajax({
            url:'http://tneship.cminl.oa/api/hrdb/index.php',           // 正式2024新版
            method:'post',
            async: false,                                               // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                functionname: 'showStaff',                              // 操作功能
                uuid: '06d4e304-a8bd-11f0-8ffe-1c697a98a75f',           // carux
                emp_id: search                                          // 查詢對象key_word  // 使用開單人工號查詢
            },
            success: function(res){
                var res_r = res["result"];
                if (res_r !== '') {
                    result.cname = res_r.cname;
                    result.email = res_r.comid2;
                    result.tel_no = res_r.comid3;
                }
            },
            error: function(err){
                console.log("search error:", err);
            }
        });
        return result;
    }
    // 輸出json文檔
    function output_json(json_str){
        $.ajax({
            url:'service_window_api.php',
            method:'post',
            async: false,                                   // ajax取得數據包後，可以return的重要參數
            dataType:'json',
            data:{
                function: 'update_service_window',          // 操作功能update_service_window
                sw_json: json_str
            },
            success: function(res){
                swal_action = 'success';
                swal_content = '套用成功';
                rework_sw(json_str);                        // call:重新操作與更新sw畫面
            },
            error: function(e){
                console.log("error",e);
                swal_action = 'error';
                swal_content = '套用失敗';
            }
        });
        swal('update_service_window' ,swal_content ,swal_action, {buttons: false, timer:1000});
    }
    // 重新操作與更新sw畫面
    function rework_sw(json_str){
        json_str = JSON.parse(json_str);                    // 字串轉json
        paint_service_window(json_str);                     // call:sw更新畫面
    }

    // 輸出畫面渲染
    function paint_service_window(sw){
        $('#service_window tbody').empty();
        for (const [fab, value] of Object.entries(sw)) {
            if(value.length < 1){
                var append_str = '<tr><td>'+fab+'</td><td>null</td><td></td><td></td></tr>';
            }else{
                let td_key = '<td rowspan="'+value.length+'">'+fab+'</td>';
                var append_str = "";
                for(let i=0; i < value.length; i++ ){
                    if(value[i]["cname"]){
                        var td_value = value[i]["cname"]+'</td><td>'+value[i]["tel_no"]+'</td><td>'+(value[i]["email"].toLowerCase())+'</td></tr>';
                    }else{
                        var td_value = '</td><td>'+'</td><td>'+'</td></tr>';
                    }
                    if(i === 0){
                        append_str += '<tr>' + td_key + '<td>' + td_value;
                    }else{
                        append_str += '<tr><td>' + td_value;
                    }
                }
            }
            $('#service_window tbody').append(append_str);
        }
    }

    $(document).ready(function () {
        let sw = JSON.parse(sw_json);                       // 空中取餐
        paint_service_window(sw);                           // call:sw更新畫面
    })