<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("sum_function.php");
    accessDenied($sys_id);

    // // *** 篩選組合項目~~
        if(isset($_REQUEST["receive_yy"])){
            $receive_yy = $_REQUEST["receive_yy"];
        }else{
            $receive_yy = date('Y');                       // 今年月
        }
        if(isset($_REQUEST["receive_mm"])){
            $receive_mm = $_REQUEST["receive_mm"];
        }else{
            $receive_mm = date('m');                       // 今年月
        }
        $list_setting = array(                             // 組合查詢陣列 -- 建立查詢陣列for顯示今年領用單
            'receive_yy' => $receive_yy,
            'receive_mm' => $receive_mm
        );

    $receive_lists = show_receives($list_setting);          // 調閱點檢表
    $allReceive_yys = show_allReceive_yy();                 // 取出receives年份清單 => 供receives頁面篩選
    if($receive_mm == "All"){                               // 確認月選項是否為All
        $allReceive_ymms = show_allReceive_ymm($list_setting);  // 取出receives年份裡的月清單 => 供receives頁面渲染
    }else{
        $allReceive_ymms = array(                           // 包一個月選項陣列給表頭用 => 供receives頁面渲染
            "0" => array( "mm" => $receive_mm )
        );
    }

    $locals = show_local();                                 // 標題用：區域名稱
    $catalogs = show_catalogs();                            // 標題用：器材名稱
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <style>
        #fix_local tr > th {
            color: blue;
            text-align: center;
            vertical-align: top; 
            word-break: break-all; 
            /* background-color: white; */
            font-size: 16px;
            -webkit-writing-mode: vertical-lr;
            writing-mode: vertical-lr; 
        }
        #fix_price tr > td {
            vertical-align: middle; 
        }
        #fix_price input{
            text-align: center;
        }
        .fix_quote:hover {
            /* font-size: 1.05rem; */
            font-weight: bold;
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
        }
        .vlr tr > th {
            vertical-align: middle; 
            writing-mode: vertical-lr;
            /* text-orientation: upright; */
            text-align: right;
        }
        .sum {
            font-weight: bold;
            text-align: right;
            color: blue;
        }
        .sum_title {
            writing-mode: horizontal-tb;
            text-align: right;
            vertical-align: bottom;
        }

    </style>
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                // icon: "../../libs/jquery/Wedges-3s-120px.gif",
                icon: "../../libs/jquery/loading.gif",
            }); 
        }
        // All resources finished loading! // 關閉mLoading提示
        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
        });
        mloading();    // 畫面載入時開啟loading
    </script>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 px-3 py-3 rounded" style="background-color: rgba(255, 255, 255, .8);" >
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-md-4 py-1">
                        <div>
                            <h3><i class="fa-solid fa-3"></i>&nbsp<b>領用匯總表</b></h3>
                        </div>
                    </div>
                    <div class="col-md-6 py-1">
                        <form action="" method="post">
                            <div class="input-group">
                                <span class="input-group-text">篩選年度</span>
                                <select name="receive_yy" id="groupBy_receive_yy" class="form-select">
                                    <?php foreach($allReceive_yys as $aR_yy){ ?>
                                        <option value="<?php echo $aR_yy["yy"];?>" <?php echo ($aR_yy["yy"] == $receive_yy) ? "selected":"";?>>
                                            <?php echo $aR_yy["yy"]."y";?></option>
                                    <?php } ?>
                                </select>
                                <select name="receive_mm" id="groupBy_receive_mm" class="form-select">
                                    <option value="All" <?php echo ($receive_mm == "All") ? "selected":"";?> >-- 全年度 / All --</option>
                                    <?php foreach (range(1, 12) as $item) {
                                            $item_str = str_pad($item, 2, '0', STR_PAD_LEFT);
                                            echo "<option value='{$item_str}'";
                                            echo ($item_str == $receive_mm ) ? "selected":"";
                                            echo " >{$item_str}m</option>";
                                        } ?>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary">查詢</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-2 py-1 text-end">

                    </div>
                </div>
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder" class="col-11 mb-0 p-0"></div>

                <!-- NAV 分頁標籤 -->
                <div class="row pb-0">
                    <nav>
                        <div class="nav nav-tabs pt-2 pb-0" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-tab_0" data-bs-toggle="tab" data-bs-target="#tab_0" type="button" role="tab" aria-controls="tab_0" aria-selected="true" >領用匯總表</button>
                            <?php foreach($locals as $local){
                                $a_btn  = "<button type='button' class='nav-link' data-bs-toggle='tab' role='tab' aria-selected='false' "; 
                                $a_btn .= "id='nav-tab_{$local["id"]}' data-bs-target='#tab_{$local["id"]}' aria-controls='tab_{$local["id"]}' >{$local["fab_title"]}</button>";
                                echo $a_btn;
                            } ?>
                        </div>
                    </nav>
                </div>

                <!-- 內頁 -->
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane bg-white fade p-2 show active" id="tab_0" role="tabpanel" aria-labelledby="nav-tab_0">
                        <!-- 1.領用彙總表單table -->
                        <div class="col-12 bg-white">
                            <table class="w-100 table table-striped table-hover">
                                <thead class="vlr">
                                    <tr>
                                        <th style="writing-mode: horizontal-tb; text-align: start; vertical-align: bottom; ">cata_SN / pname / Total：<span id="all_total_cost"></span></th>
                                        <?php foreach($locals as $local){
                                            echo "<th id='local_{$local["id"]}'>".$local["fab_title"]."</br>（".$local["local_title"]."）"."</th>";
                                        } ?>
                                        <th style="writing-mode: horizontal-tb; vertical-align: bottom; ">sum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($catalogs as $catalog){
                                        // 把年度報價帶出來
                                        $this_price_arr = (array)(json_decode($catalog["price"]));
                                        if(isset($this_price_arr[$receive_yy])){
                                            $this_price = $this_price_arr[$receive_yy];
                                        }else{
                                            $this_price = 0;
                                        }
                                        echo "<tr>";
                                            echo "<td id='cata_{$catalog["SN"]}' class='text-start'>{$catalog["SN"]}</br>{$catalog["pname"]} (\${$this_price})</td>";
                                            foreach($locals as $local){
                                                echo "<td><div id='{$local["id"]}_{$catalog["SN"]}'></div><div id='{$local["id"]}_{$catalog["SN"]}_cost'></div></td>";
                                            };
                                            echo "<td class='sum'><div id='{$catalog["SN"]}_TT'></div><div id='{$catalog["SN"]}_TT_cost'></td>";
                                        echo "</tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- 2.各廠器材量統計 table -->
                    <?php 
                        foreach($locals as $local){
                            $b_tab  = "<div class='tab-pane bg-white fade p-2' id='tab_{$local["id"]}' role='tabpanel' aria-labelledby='nav-tab_{$local["id"]}'>";
                            $b_tab .= "<div class='col-12 bg-white'><table class='w-100 table table-striped table-hover'>";
                            $b_tab .= "<thead><tr>";
                            $b_tab .= "<th style='writing-mode: horizontal-tb; text-align: start; vertical-align: bottom;'>cata_SN / pname / {$local["fab_title"]} -- Total：<span id='{$local["id"]}_fab_total_cost'></span></th>";
                            echo $b_tab;
                                foreach ($allReceive_ymms as $ymm) {
                                    echo "<th>{$ymm["mm"]}月</th>";
                                }
                                $b_tab = "<th class='sum_title'>sum</th>";
                                $b_tab .= "</tr></thead><tbady>";
                            echo $b_tab;
                            // tbody
                            foreach($catalogs as $catalog){
                                echo "<tr>";
                                    echo "<td id='{$local["id"]}_{$catalog["SN"]}' class='text-start'>".$catalog["SN"]."</br>".$catalog["pname"]."</td>";
                                    foreach ($allReceive_ymms as $ymm) {
                                        echo "<td><div id='{$local["id"]}_{$catalog["SN"]}_{$ymm["mm"]}'></div><div id='{$local["id"]}_{$catalog["SN"]}_{$ymm["mm"]}_cost'></div></td>";
                                    }
                                    echo "<td class='sum'><div id='{$local["id"]}_{$catalog["SN"]}_fabTT'></div><div id='{$local["id"]}_{$catalog["SN"]}_fabTT_cost'></div></td>";

                                echo "</tr>";
                            }
                            echo "</tbady></table></div></div>";
                        } 
                    ?>
                </div>
            </div>
        </div>
    </div>
<!-- toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="1000">
            <div class="d-flex">
                <div class="toast-body" id="toast-body"></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
<!-- goTop滾動畫面DIV 2/4-->
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>
<script>
    var receive_lists = <?=json_encode($receive_lists);?>;          // 引入receive_lists資料
    var catalogs      = <?=json_encode($catalogs);?>;               // 引入catalogs資料
    var receive_yy    = '<?=$receive_yy;?>';                        // 引用年分
    var receiveAmount = [];                                         // 宣告變數陣列，承裝Receives年領用量
    var cata_price    = [];                                         // 宣告變數陣列，承裝pno年報價
    
    // 把 catalog對應的p_no報價繞出來
    Object(catalogs).forEach(function(cata){
         let cata_yy_p = JSON.parse(cata['price'])[receive_yy];
         if(cata_yy_p == undefined){
            cata_yy_p = 1;
         }
         cata_price[cata['SN']] = Number(cata_yy_p);
    })
    // console.log('cata_price:', cata_price)

    // function show_receives(){
        // 彙整出SN年領用量
        Object(receive_lists).forEach(function(row){
            let csa = JSON.parse(row['cata_SN_amount']);
            Object.keys(csa).forEach(key =>{                    // key = cats_SN
                let pay = Number(csa[key]['pay']);
                let l_key = row['local_id'] +'_'+ key;          // 第1頁
                let key_TT = key +'_TT';                        // 第1頁
                let l_key_mm = l_key +'_'+ row['mm'];           // 第2頁 mm = 月份
                let l_key_fabTT = l_key +'_fabTT';              // 第2頁 
                let local_id = row['local_id'];
                // 第1頁
                    if(receiveAmount[l_key]){                       // 第1頁 每個廠的實付數量
                        receiveAmount[l_key] += pay;
                    }else{
                        receiveAmount[l_key] = pay;
                    }
                    if(receiveAmount[l_key+'_cost']){               // 第1頁 每個廠的實付數量金額
                        receiveAmount[l_key+'_cost'] += pay * cata_price[key];
                    }else{
                        receiveAmount[l_key+'_cost'] = pay * cata_price[key];
                    }
                        if(receiveAmount[key_TT]){                  // 第1頁 末端總計實付數量sum
                            receiveAmount[key_TT] += pay;
                        }else{
                            receiveAmount[key_TT] = pay;
                        }
                        if(receiveAmount[key_TT+'_cost']){          // 第1頁 末端總計實付數量金額sum
                            receiveAmount[key_TT+'_cost'] += pay * cata_price[key];
                        }else{
                            receiveAmount[key_TT+'_cost'] = pay * cata_price[key];
                        }
                            if(receiveAmount['all_total_cost']){      // 第1頁 TITLE 總計實付數量金額Total
                                receiveAmount['all_total_cost'] += pay * cata_price[key];
                            }else{
                                receiveAmount['all_total_cost'] = pay * cata_price[key];
                            }
                            
                // 第n頁byFab/mm
                if(receiveAmount[l_key_mm]){                        // 第n頁byFab/mm 每個廠的實付數量
                    receiveAmount[l_key_mm] += pay;
                }else{
                    receiveAmount[l_key_mm] = pay;
                }
                if(receiveAmount[l_key_mm+'_cost']){                // 第n頁byFab/mm 每個廠的實付數量金額
                    receiveAmount[l_key_mm+'_cost'] += pay * cata_price[key];
                }else{
                    receiveAmount[l_key_mm+'_cost'] = pay * cata_price[key];
                }
                    if(receiveAmount[l_key_fabTT]){                 // 第n頁 末端總計實付數量sum
                        receiveAmount[l_key_fabTT] += pay;
                    }else{
                        receiveAmount[l_key_fabTT] = pay;
                    }
                    if(receiveAmount[l_key_fabTT+'_cost']){         // 第n頁 末端總計實付數量金額sum
                        receiveAmount[l_key_fabTT+'_cost'] += pay * cata_price[key];
                    }else{
                        receiveAmount[l_key_fabTT+'_cost'] = pay * cata_price[key];
                    }
                        if(receiveAmount[local_id+'_fab_total_cost']){       // 第n頁 TITLE 總計實付數量金額Total
                            receiveAmount[local_id+'_fab_total_cost'] += pay * cata_price[key];
                        }else{
                            receiveAmount[local_id+'_fab_total_cost'] = pay * cata_price[key];
                        }
            })
        });

        // console.log('receiveAmount:', receiveAmount)
        // 選染到Table上指定欄位
        Object.keys(receiveAmount).forEach(key => {
            let value = receiveAmount[key];
            $('#'+key).empty();
            if(key.includes("cost")){
                $('#'+key).append('$'+value);
            }else{
                $('#'+key).append(value);
            }
        })

        // let sinn = '<b>** 自動帶入 年領用累計 ... 完成</b>~';
        // inside_toast(sinn);
    // }


</script>

<?php include("../template/footer.php"); ?>

