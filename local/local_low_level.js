
    // fun3-3：吐司顯示字條 // init toast
    function inside_toast(sinn){
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }
    // // // show 年領用量與建議值
    function show_myReceives(){
        // 彙整出SN年領用量
        Object(myReceives).forEach(function(row){
            let csa = JSON.parse(row['cata_SN_amount']);
            Object.keys(csa).forEach(key =>{                                // 這裡的key = SN
                let pay = Number(csa[key]['pay']);
                let l_key = row['local_id'] +'_'+ key;
                if(receiveAmount[l_key]){
                    receiveAmount[l_key]['pay'] += pay;
                }else{
                    receiveAmount[l_key] = {
                        'pay' : pay
                    }
                }
                Object(catalogs).forEach(function(cata){
                    if(cata['SN'] == key){
                        receiveAmount[l_key]['buy_dm'] = cata['buy_'+buy_ty];
                        receiveAmount[l_key]['unit'] = cata['unit'];
                        return;
                    }
                })
            })
        });

        // 選染到Table上指定欄位
        Object.keys(receiveAmount).forEach(key => {
            let pay = receiveAmount[key]['pay'];                // 領用總數
            let buy_dm = receiveAmount[key]['buy_dm'];          // 安量倍數
            let unit = receiveAmount[key]['unit'];              // SN-unit單位
            let value = Math.ceil(pay * buy_dm);                // 算出建議值
            $('#receive_'+key).empty();                                             // 清除領用格
            $('#receive_'+key).append(pay);                                         // 貼上領用總量
            $('#buy_qt_'+key).empty();                                              // 清除建議值
            $('#buy_qt_'+key).append('x '+ buy_dm +' =</br>'+ value +' / '+ unit );      // 貼上建議值計算公式
            document.getElementById('buy_qt_'+key).classList.add('alert_it');       // 將建議值套用css:alert_it
            document.getElementById(key).value = value;                             // input.value套用
        })
        let sinn = '<b>** 自動帶入 年領用累計 ... 完成</b>~';
        inside_toast(sinn);
    }

    $(document).ready(function () {
        // 停用 dataTable => 原因是它換頁後，前面的數值無法送出...
            // $('#catalog_list').DataTable({
            //     "autoWidth": false,
            //     // 排序
            //     // "order": [[ 4, "asc" ]],
            //     // 顯示長度
            //     "pageLength": 25,
            //     // 中文化
            //     "language":{
            //         url: "../../libs/dataTables/dataTable_zh.json"
            //     }
            // });

        // call fun show 年領用量與建議值
        if(myReceives.length >= 1){
            show_myReceives();
        }

    })