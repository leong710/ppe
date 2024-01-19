<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("sum_function.php");
    accessDenied($sys_id);

    // // *** 篩選組合項目~~
        if(isset($_REQUEST["form"])){                       // report表單類別
            $form = $_REQUEST["form"];
        }else{
            $form = 'receive';                              
        }
        if(!in_array($form, ["receive", "issue", "trade"])){          // report表單類別--防呆
            $form = 'receive';
        }

        if(isset($_REQUEST["report_yy"])){
            $report_yy = $_REQUEST["report_yy"];
        }else{
            $report_yy = date('Y');                         // 今年
        }
        if(isset($_REQUEST["report_mm"])){
            $report_mm = $_REQUEST["report_mm"];
        }else{
            // $report_mm = date('m');                      // 今月
            $report_mm = "All";                             // 今月
        }
        $query_arr = array(                                 // 組合查詢陣列 -- 建立查詢陣列for顯示今年領用單
            'report_yy' => $report_yy,
            'report_mm' => $report_mm
        );

    switch($form){
        case "receive" :
            $report_title = "領用";
            $report_lists = show_receives($query_arr);           // 調閱點檢表
            $allReport_yys = show_allReceive_yy();               // 取出receives年份清單 => 供receives頁面篩選
            $allReport_ymms = show_allReceive_ymm($query_arr);   // 取出receives年份裡的月清單 => 供receives頁面渲染
            break;
        case "issue" :
            $report_title = "需求";
            $report_lists = show_issues($query_arr);             // 調閱點檢表
            $allReport_yys = show_allIssue_yy();                 // 取出issues年份清單 => 供issues頁面篩選
            $allReport_ymms = show_allIssue_ymm($query_arr);     // 取出issues年份裡的月清單 => 供issues頁面渲染
         break;
        case "trade" :
            $report_title = "進貨";
            $report_lists = show_trades($query_arr);             // 調閱點檢表
            $allReport_yys = show_allTrade_yy();                 // 取出issues年份清單 => 供issues頁面篩選
            $allReport_ymms = show_allTrade_ymm($query_arr);     // 取出issues年份裡的月清單 => 供issues頁面渲染
         break;
        default:    // $act = '錯誤 (Error)';  以receive替代錯誤 
            $report_title = "領用(form_error)";
            $report_lists = show_receives($query_arr);           // 調閱點檢表
            $allReport_yys = show_allReceive_yy();               // 取出receives年份清單 => 供receives頁面篩選
            $allReport_ymms = show_allReceive_ymm($query_arr);   // 取出receives年份裡的月清單 => 供receives頁面渲染      
        break;
    }

        if($report_mm != "All"){                            // 確認月選項是否為All
            $allReport_ymms = array(                        // 包一個月選項陣列給表頭用 => 供receives頁面渲染
                "0" => array( "mm" => $report_mm )
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
        .form-check-input[type="checkbox"] {
            margin-right: 10px;
        }
        .form-check-label {
            padding-top: 2px;
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
                    <div class="col-md-6 py-1">
                        <div>
                            <h3><b>進出量與成本匯總：<?php echo $form.$report_title;?></b></h3>
                        </div>
                    </div>
                    <div class="col-md-6 py-1">
                        <form action="" method="post">
                            <div class="input-group">
                                <span class="input-group-text">篩選 表單/年/月</span>
                                <select name="form" id="groupBy_form" class="form-select">
                                    <option value="receive" <?php echo ($form == "receive") ? "selected":"";?> >receive領用</option>
                                    <option value="issue"   <?php echo ($form == "issue")   ? "selected":"";?> >issue需求</option>
                                    <option value="trade"   <?php echo ($form == "trade")   ? "selected":"";?> >trade進貨</option>
                                </select>
                                <select name="report_yy" id="groupBy_report_yy" class="form-select">
                                    <?php foreach($allReport_yys as $aR_yy){ ?>
                                        <option value="<?php echo $aR_yy["yy"];?>" <?php echo ($aR_yy["yy"] == $report_yy) ? "selected":"";?>>
                                            <?php echo $aR_yy["yy"]."y";?></option>
                                    <?php } ?>
                                </select>
                                <select name="report_mm" id="groupBy_report_mm" class="form-select">
                                    <option value="All" <?php echo ($report_mm == "All") ? "selected":"";?> >-- 全年度 / All --</option>
                                    <?php foreach (range(1, 12) as $item) {
                                            $item_str = str_pad($item, 2, '0', STR_PAD_LEFT);
                                            echo "<option value='{$item_str}'";
                                            echo ($item_str == $report_mm ) ? "selected":"";
                                            echo " >{$item_str}m</option>";
                                        } ?>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary">查詢</button>
                            </div>
                        </form>
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
                            <div class="row">
                                <div class="col-12 col-md-6 py-0"></div>
                                <div class="col-12 col-md-6 py-0 text-end">
                                    <div style="display: inline-block;" class="px-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="On" id="tab_0_flag_Switch" name="tab_0" onchange="groupBy_flag(this.name);">
                                            <label class="form-check-label" for="tab_0_flag_Switch" >遮蔽sum空值</label>
                                        </div>
                                    </div>
                                    <div style="display: inline-block;">
                                        <!-- 20240109 下載Excel -->
                                        <form id="myForm" method="post" action="../_Format/download_excel.php" style="display:inline-block;">
                                            <input type="hidden" name="htmlTable" id="tab_0_htmlTable" value="">
                                            <input type="hidden" name="submit" value="sum_report">
                                            <input type="hidden" name="tab_name" value="sum_report">
                                            <input type="hidden" name="form_type" value="<?php echo $form;?>">
                                            <input type="hidden" name="report_yy" value="<?php echo $report_yy;?>">
                                            <input type="hidden" name="report_mm" value="<?php echo $report_mm;?>">
                                            <button type="submit" name="tab_0" class="btn btn-success" onclick="downloadExcel(this.name)" >
                                                <i class="fa fa-download" aria-hidden="true"></i> 匯出&nbspExcel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <table class="w-100 table table-striped table-hover tab_0" id="tab_0_table">
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
                                        if(isset($this_price_arr[$report_yy])){
                                            $this_price = $this_price_arr[$report_yy];
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
                            $b_tab .= "<div class='col-12 bg-white'>";
                            $b_tab .= "<div class='row'>
                                            <div class='col-12 col-md-6 py-0'></div>
                                            <div class='col-12 col-md-6 py-0 text-end'>
                                                <div style='display: inline-block;' class='px-3'>
                                                    <div class='form-check'>
                                                        <input class='form-check-input' type='checkbox' value='On' id='tab_{$local["id"]}_flag_Switch' name='tab_{$local["id"]}' onchange='groupBy_flag(this.name);'>
                                                        <label class='form-check-label' for='tab_{$local["id"]}_flag_Switch' >遮蔽sum空值</label>
                                                    </div>
                                                </div>
                                                <div style='display: inline-block;'>
                                                    <form id='myForm' method='post' action='../_Format/download_excel.php' style='display:inline-block;'>
                                                        <input type='hidden' name='htmlTable' id='tab_{$local["id"]}_htmlTable' value=''>
                                                        <input type='hidden' name='submit' value='sum_report'>
                                                        <input type='hidden' name='form_type' value='{$form}'>
                                                        <input type='hidden' name='report_yy' value='{$report_yy}'>
                                                        <input type='hidden' name='report_mm' value='{$report_mm}'>
                                                        <input type='hidden' name='tab_name' value='{$local["fab_title"]}({$local["local_title"]}_{$local["local_remark"]})'>
                                                        <button type='submit' name='tab_{$local["id"]}' class='btn btn-success' onclick='downloadExcel(this.name)' >
                                                            <i class='fa fa-download' aria-hidden='true'></i> 匯出&nbspExcel</button>
                                                    </form>
                                                </div>
                                            </div></div>";
                            $b_tab .= "<table class='w-100 table table-striped table-hover tab_{$local["id"]}' id='tab_{$local["id"]}_table'>";
                            $b_tab .= "<thead><tr>";
                            $b_tab .= "<th style='writing-mode: horizontal-tb; text-align: start; vertical-align: bottom;'>cata_SN / pname / {$local["fab_title"]}({$local["local_title"]}_{$local["local_remark"]}) -- Total：<span id='{$local["id"]}_fab_total_cost'></span></th>";
                            echo $b_tab;
                                foreach ($allReport_ymms as $ymm) {
                                    echo "<th>{$ymm["mm"]}月</th>";
                                }
                                $b_tab = "<th class='sum_title'>sum</th>";
                                $b_tab .= "</tr></thead><tbady>";
                            echo $b_tab;
                            // tbody
                            foreach($catalogs as $catalog){
                                echo "<tr>";
                                    echo "<td id='{$local["id"]}_{$catalog["SN"]}' class='text-start'>".$catalog["SN"]."</br>".$catalog["pname"]."</td>";
                                    foreach ($allReport_ymms as $ymm) {
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
    var report_lists = <?=json_encode($report_lists)?>;           // 引入report_lists資料
    var catalogs     = <?=json_encode($catalogs)?>;               // 引入catalogs資料
    var form_type    = '<?=$form?>';                              // 引入catalogs資料
    var report_yy    = '<?=$report_yy?>';                         // 引用年分
    var reportAmount = [];                                        // 宣告變數陣列，承裝Receives年領用量
    var cata_price   = [];                                        // 宣告變數陣列，承裝pno年報價
    
    console.log('report_lists:', report_lists);

    // cata目錄、報價、領用量、渲染
    function show_reports(){
        // step-1.把 catalog對應的p_no報價繞出來
        Object(catalogs).forEach(function(cata){
            // var cata_yy_p = JSON.parse(cata['price'])[report_yy];
            var cata_p = JSON.parse(String(cata['price']));
            if((cata_p === 0) || (cata_p === null)){
                cata_p = {};
            }
            cata_yy_p = cata_p[report_yy];
            if(cata_yy_p === undefined || cata_yy_p === 0){
                cata_yy_p = 1;
            }
            cata_price[cata['SN']] = Number(cata_yy_p);
        })
        // console.log('cata_price:', cata_price)

        // step-2.彙整出SN年領用量
        Object(report_lists).forEach(function(row){
            var csa = JSON.parse(row['cata_SN_amount']);
            Object.keys(csa).forEach(key =>{                    // key = cats_SN
                var pay = Number(csa[key]['pay']);
                var l_key = row['local_id'] +'_'+ key;          // 第1頁
                var key_TT = key +'_TT';                        // 第1頁
                var l_key_mm = l_key +'_'+ row['mm'];           // 第2頁 mm = 月份
                var l_key_fabTT = l_key +'_fabTT';              // 第2頁 
                var local_id = row['local_id'];
                // 第1頁
                    if(reportAmount[l_key]){                       // 第1頁 每個廠的實付數量
                        reportAmount[l_key] += pay;
                    }else{
                        reportAmount[l_key] = pay;
                    }
                    if(reportAmount[l_key+'_cost']){               // 第1頁 每個廠的實付數量金額
                        reportAmount[l_key+'_cost'] += pay * cata_price[key];
                    }else{
                        reportAmount[l_key+'_cost'] = pay * cata_price[key];
                    }
                        if(reportAmount[key_TT]){                  // 第1頁 末端總計實付數量sum
                            reportAmount[key_TT] += pay;
                        }else{
                            reportAmount[key_TT] = pay;
                        }
                        if(reportAmount[key_TT+'_cost']){          // 第1頁 末端總計實付數量金額sum
                            reportAmount[key_TT+'_cost'] += pay * cata_price[key];
                        }else{
                            reportAmount[key_TT+'_cost'] = pay * cata_price[key];
                        }
                            if(reportAmount['all_total_cost']){      // 第1頁 TITLE 總計實付數量金額Total
                                reportAmount['all_total_cost'] += pay * cata_price[key];
                            }else{
                                reportAmount['all_total_cost'] = pay * cata_price[key];
                            }
                            
                // 第n頁byFab/mm
                if(reportAmount[l_key_mm]){                        // 第n頁byFab/mm 每個廠的實付數量
                    reportAmount[l_key_mm] += pay;
                }else{
                    reportAmount[l_key_mm] = pay;
                }
                if(reportAmount[l_key_mm+'_cost']){                // 第n頁byFab/mm 每個廠的實付數量金額
                    reportAmount[l_key_mm+'_cost'] += pay * cata_price[key];
                }else{
                    reportAmount[l_key_mm+'_cost'] = pay * cata_price[key];
                }
                    if(reportAmount[l_key_fabTT]){                 // 第n頁 末端總計實付數量sum
                        reportAmount[l_key_fabTT] += pay;
                    }else{
                        reportAmount[l_key_fabTT] = pay;
                    }
                    if(reportAmount[l_key_fabTT+'_cost']){         // 第n頁 末端總計實付數量金額sum
                        reportAmount[l_key_fabTT+'_cost'] += pay * cata_price[key];
                    }else{
                        reportAmount[l_key_fabTT+'_cost'] = pay * cata_price[key];
                    }
                        if(reportAmount[local_id+'_fab_total_cost']){       // 第n頁 TITLE 總計實付數量金額Total
                            reportAmount[local_id+'_fab_total_cost'] += pay * cata_price[key];
                        }else{
                            reportAmount[local_id+'_fab_total_cost'] = pay * cata_price[key];
                        }
            })
        });
        // console.log('reportAmount:', reportAmount)

        // step-3.選染到Table上指定欄位
        Object.keys(reportAmount).forEach(key => {
            var value = reportAmount[key];
            $('#'+key).empty();
            if(key.includes("cost")){
                $('#'+key).append('$'+value);
            }else{
                // console.log('key:', key);
                $('#'+key).append(value);
            }
        })
    }
    // 空值遮蔽：On、Off
    function groupBy_flag(name){
        var checkbox = document.getElementById(name+"_flag_Switch");
        var flag = checkbox.checked ? "On" : "Off";
        // console.log(name, flag);
        var table_tr = document.querySelectorAll('.'+name+' > tbody > tr');
        if(flag=='Off'){
            table_tr.forEach(function(row){
                row.classList.remove('unblock');
            })
        }else{
            table_tr.forEach(function(row){
                // console.log(row.children);
                // 因為外層又包了一個Button導致目標下移
                if(row.children[row.children.length-1].innerText != ""){
                    row.classList.remove('unblock');
                }else{
                    row.classList.add('unblock');
                }
            })  
        }
    }
    // 20240109_下載Excel
    function downloadExcel(name) {
        // 定義畫面上Table範圍
        var ia_table = document.getElementById(name+"_table");
        var rows = ia_table.getElementsByTagName("tr");
        var rowData = [];
        // 获取表格的标题行数据
        var headerRow = ia_table.getElementsByTagName("thead")[0].getElementsByTagName("tr")[0];
        var headerCells = headerRow.getElementsByTagName("th");
        // 逐列導出
        for (var i = 1; i < rows.length; i++) {
            var cells = rows[i].getElementsByTagName("td");
            rowData[i-1] = {};
            // 逐欄導出：thead-th = tbod-td
            for (var j = 0; j < cells.length; j++) {
                rowData[i-1][headerCells[j].innerText] = cells[j].innerText.replace(/<br\s*\/?>/gi, "\r\n");
            }
        }
        var htmlTableValue = JSON.stringify(rowData);
        // console.log('htmlTableValue:', htmlTableValue);
        document.getElementById(name+'_htmlTable').value = htmlTableValue;
    }

    $(document).ready(function () {
        show_reports();

    })

        // var sinn = '<b>** 自動帶入 年領用累計 ... 完成</b>~';
        // inside_toast(sinn);
    // }


</script>

<?php include("../template/footer.php"); ?>

