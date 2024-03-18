function make_sw(){
    
    var sw = {};

    fab.forEach(el => {
        sw[el.fab_title] = [];
        var pm_emp_id_str = el.pm_emp_id;
        
        if(pm_emp_id_str != null && pm_emp_id_str.length > 0){    // 預防null或異常
            var pm_emp_id_arr = pm_emp_id_str.split(",");
            pm_emp_id_arr.forEach(pm_s =>{
                if(!pm_s || (pm_s.length < 8)){
                    return false;
                }
                var pm_s_arr = search_sw_fun(pm_s);
                sw[el.fab_title].push(pm_s_arr);
            })
        }
    });
    
    // let json_str = JSON.stringify(sw);
    // output_json(json_str)
    // console.log(sw);
    paint_service_window(sw);
}

// // fun3-1：search Key_word
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
            uuid: '752382f7-207b-11ee-a45f-2cfda183ef4f',           // ppe
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

function output_json(json_str){
    // 创建一个 Blob 对象
    const blob = new Blob([json_str], { type: 'application/json' });

    // 创建一个链接
    const url = window.URL.createObjectURL(blob);

    // 创建一个 <a> 元素
    const a = document.createElement('a');
    a.href = url;
    a.download = 'make_service_window.json';

    // 模拟点击链接以下载文件
    a.click();

    // 释放 URL 对象
    window.URL.revokeObjectURL(url);

}

function paint_service_window(sw){
    // console.log(sw);
    for (const [fab, value] of Object.entries(sw)) {
        console.log(fab, value);
        $('#service_window tbody').append()
    }

    
    // Object(sw).forEach(function(row){
    //     console.log(row);
    // })
}

$(document).ready(function () {

    make_sw();


})