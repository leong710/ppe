<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function_sum.php");
    accessDenied($sys_id);

    // // *** 篩選組合項目~~
        $form = (isset($_REQUEST["form"])) ? $_REQUEST["form"] : 'stock';       // report表單類別 // 預設存量
        if(!in_array($form, ["stock", "receive", "issue", "trade"])){           // report表單類別--防呆
            $form = 'stock';
        }
        $report_yy = (isset($_REQUEST["report_yy"])) ? $_REQUEST["report_yy"] : date('Y'); // 今年
        $report_mm = (isset($_REQUEST["report_mm"])) ? $_REQUEST["report_mm"] : "All"; // 今月
        // $report_mm = date('m');                      // 今月

        $query_arr = array(                                 // 組合查詢陣列 -- 建立查詢陣列for顯示今年領用單
            'report_yy' => $report_yy,
            'report_mm' => $report_mm
        );

    switch($form){
        case "stock" :
            $report_title = "存量(現況)";
            $report_lists = show_stock();                      // 調閱stock
            // $allReport_yys = show_allTrade_yy();                 // 取出issues年份清單 => 供issues頁面篩選
            // $allReport_ymms = show_allTrade_ymm($query_arr);     // 取出issues年份裡的月清單 => 供issues頁面渲染
         break;
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
            $report_title = "進貨(不含調撥)";
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

    $fabs       = show_fab();                                 // 標題用：區域名稱
    $locals     = show_local();                               // 二層標題用：區域名稱
    $catalogs   = show_catalogs();                            // 標題用：器材名稱
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script src="../../libs/jquery/mloading_init.js"></script>
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
            /* writing-mode: vertical-lr; */
            /* text-orientation: upright; */
            /* 中文也轉向 */
            text-orientation: sideways-right;
            text-align: right;
        }
        .sum {
            font-weight: bold;
            /* text-align: right; */
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
        .bbs {
            border-bottom-style: dotted;
            /* border-bottom-style: dashed; */
            border-width: 1.5px;
            /* border-color: #FFAC55; */
            /* padding:5px; */
        }
        /* 防止該DIV元素塌陷 */
        .empty-div {
            height: 22px; /* 設置一個固定的高度，可以根據需求調整 */
        }
        .empty-div::before {
            /* 方法一 */
            /* 使用空白字符作為內容 */
            /* content: '\00a0';  */
            /* 讓伪元素可见并占位 */
            /* display: inline-block;  */
            /* 方法二 */
            /* 使用元素的 placeholder 属性作为内容 */
            content: attr(placeholder); 
            /* 使内容透明，但仍然占位 */
            color: transparent; 
        }

    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 px-3 py-3 rounded" style="background-color: rgba(255, 255, 255, .8);" >
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-md-6 py-1">
                        <div>
                            <h3><b>PPE器材管控清單：<?php echo $form.$report_title;?></b></h3>
                        </div>
                    </div>
                    <div class="col-md-6 py-1">

                    </div>
                </div>

                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder" class="col-11 mb-0 p-0"></div>

                <!-- NAV 分頁標籤 -->
                <div class="row pb-0">
                    <nav>
                        <div class="nav nav-tabs pt-2 pb-0" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-tab_0" data-bs-toggle="tab" data-bs-target="#tab_0" type="button" role="tab" aria-controls="tab_0" aria-selected="true" >現況存量總表</button>
                            <?php foreach($fabs as $fab){
                                $a_btn  = "<button type='button' class='nav-link' data-bs-toggle='tab' role='tab' aria-selected='false' "; 
                                $a_btn .= "id='nav-tab_{$fab["fab_id"]}' data-bs-target='#tab_{$fab["fab_id"]}' aria-controls='tab_{$fab["fab_id"]}'>{$fab["fab_title"]}</button>";
                                echo $a_btn;
                            } ?>
                        </div>
                    </nav>
                </div>

                <!-- 內頁 -->
                <div class="tab-content" id="nav-tabContent">
                    <!-- 1.領用彙總表單table -->
                    <div class="tab-pane bg-white fade p-2 show active" id="tab_0" role="tabpanel" aria-labelledby="nav-tab_0">
                        <div class="col-12 bg-white">
                            <!-- Banner -->
                            <div class="row">
                                <div class="col-12 col-md-6 py-0"></div>
                                <div class="col-12 col-md-6 py-0 text-end">
                                    <div style="display: inline-block;" class="px-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="On" id="tab_0_flag_Switch" name="tab_0" onchange="groupBy_flag(this.name);">
                                            <label class="form-check-label" for="tab_0_flag_Switch">遮蔽sum空值</label>
                                        </div>
                                    </div>
                                    <div style="display: inline-block;">
                                        <!-- 20240109 下載Excel -->
                                        <form id="myForm" method="post" action="../_Format/download_excel.php" style="display:inline-block;">
                                            <input type="hidden" name="htmlTable" id="tab_0_htmlTable" value="">
                                            <input type="hidden" name="submit" value="sum_ptreport">
                                            <input type="hidden" name="tab_name" value="現況存量總表">
                                            <input type="hidden" name="form_type" value="<?php echo $form;?>">
                                            <button type="submit" name="tab_0" class="btn btn-success" onclick="downloadExcel(this.name)" >
                                                <i class="fa fa-download" aria-hidden="true"></i> 匯出&nbspExcel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <table class="w-100 table table-stripedd table-hoverr tab_0 conta" id="tab_0_table">
                                <thead class="vlr">
                                    <tr>
                                        <th style="writing-mode: horizontal-tb; text-align: start; vertical-align: bottom;">cata_SN / pname：</th>
                                        <th style="writing-mode: horizontal-tb; text-align: center; vertical-align: bottom;">類型</th>
                                        <?php foreach($fabs as $fab){
                                            echo "<th id='fab_{$fab["fab_id"]}'>".$fab["fab_title"]."</th>";
                                        } ?>
                                        <th style="writing-mode: horizontal-tb; text-align: center; vertical-align: bottom; ">sum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($catalogs as $catalog){
                                        echo "<tr>";
                                            echo "<td id='cata_{$catalog["SN"]}' class='text-start' >{$catalog["SN"]}</br>{$catalog["pname"]}</td>";
                                            echo "<td>
                                                    <div class='bg-primary text-white'>總量</div>
                                                    </td>";
                                            foreach($fabs as $fab){
                                                echo "<td>
                                                        <div id='{$fab["fab_id"]}_{$catalog["SN"]}_all'></div>
                                                        </td>";
                                            };
                                            echo "<td class='sum'>
                                                    <div id='{$catalog["SN"]}_sum_all'></div>
                                                    </td>";
                                        echo "</tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 3.各廠器材量統計 table -->
                    <?php 
                        foreach($fabs as $fab){
                            // thead
                            $b_tab  = "<div class='tab-pane bg-white fade p-2' id='tab_{$fab["fab_id"]}' role='tabpanel' aria-labelledby='nav-tab_{$fab["fab_id"]}'>";
                            $b_tab .= "<div class='col-12 bg-white'>";
                            $b_tab .= "<div class='row'>
                                            <div class='col-12 col-md-6 py-0'></div>
                                            <div class='col-12 col-md-6 py-0 text-end'>
                                                <div style='display: inline-block;' class='px-3'>
                                                    <div class='form-check'>
                                                        <input class='form-check-input' type='checkbox' value='On' id='tab_{$fab["fab_id"]}_flag_Switch' name='tab_{$fab["fab_id"]}' onchange='groupBy_flag(this.name);'>
                                                        <label class='form-check-label' for='tab_{$fab["fab_id"]}_flag_Switch' >遮蔽sum空值</label>
                                                    </div>
                                                </div>
                                                <div style='display: inline-block;'>
                                                    <form id='myForm' method='post' action='../_Format/download_excel.php' style='display:inline-block;'>
                                                        <input type='hidden' name='htmlTable' id='tab_{$fab["fab_id"]}_htmlTable' value=''>
                                                        <input type='hidden' name='submit' value='sum_ptreport'>
                                                        <input type='hidden' name='form_type' value='{$form}'>
                                                        <input type='hidden' name='tab_name' value='{$fab["fab_title"]}({$fab["fab_remark"]})'>
                                                        <button type='submit' name='tab_{$fab["fab_id"]}' class='btn btn-success' onclick='downloadExcel(this.name)' >
                                                            <i class='fa fa-download' aria-hidden='true'></i> 匯出&nbspExcel</button>
                                                    </form>
                                                </div>
                                            </div></div>";
                            $b_tab .= "<table class='w-100 table table-stripedd table-hoverr tab_{$fab["fab_id"]}' id='tab_{$fab["fab_id"]}_table'>";
                            $b_tab .= "<thead class='vlr'><tr>";
                            $b_tab .= "<th style='writing-mode: horizontal-tb; text-align: start; vertical-align: bottom;'>cata_SN / pname</th>";
                            $b_tab .= '<th style="writing-mode: horizontal-tb; text-align: center; vertical-align: bottom; ">類型</th>';
                            echo $b_tab;
                                foreach ($locals as $local) {
                                    if($local["fab_id"] == $fab["fab_id"]){
                                        echo "<th>{$local["local_title"]}</br>{$local["local_remark"]}</th>";
                                    }
                                }
                                $b_tab = "<th class='sum_title' style='writing-mode: horizontal-tb; text-align: center; vertical-align: bottom;'>sum</th>";
                                $b_tab .= "</tr></thead><tbady>";
                            echo $b_tab;
                            // tbody
                            foreach($catalogs as $catalog){
                                echo "<tr>";
                                    echo "<td id='{$local["id"]}_{$catalog["SN"]}' class='text-start'>".$catalog["SN"]."</br>".$catalog["pname"]."</td>";
                                    echo "<td>
                                            <div class='bg-primary text-white'>總量</div>
                                            </td>";
                                    foreach ($locals as $local) {
                                        if($local["fab_id"] == $fab["fab_id"]){
                                            echo "<td>
                                                    <div id='{$fab["fab_id"]}_{$local["id"]}_{$catalog["SN"]}_all'></div>
                                                    </td>";
                                        }
                                    }
                                    echo "<td class='sum'>
                                            <div id='f_{$fab["fab_id"]}_{$catalog["SN"]}_sum_all'></div>
                                            </td>";
                                echo "</tr>";
                            }
                            echo "</tbady></table></div></div>";
                        } 
                    ?>

                    <!-- 20231108-資料更新時間 -->
                    <div class="col-12 pb-0 px-3 text-end">
                        <i class="fa-solid fa-rotate"></i> Reload time：<span id="reload_time1"></span>
                        <script> reload_time1.innerHTML=new Date().toLocaleString()+' 星期'+'日一二三四五六'.charAt(new Date().getDay()); </script>
                    </div>
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

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/sweetalert/sweetalert.min.js"></script>
<!-- 引入moment.js 參考資料 https://www.freecodecamp.org/chinese/news/javascript-date-format-how-to-format-a-date-in-js/ -->
<!-- <script src="../../libs/moment/moment.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script> -->
<script>
    var report_lists = <?=json_encode($report_lists)?>;           // 引入report_lists資料
    var catalogs     = <?=json_encode($catalogs)?>;               // 引入catalogs資料
    var reportAmount = [];                                        // 宣告變數陣列，承裝Receives年領用量
    var cata_price   = [];                                        // 宣告變數陣列，承裝pno年報價
    var Today        = new Date().getTime();                      // DAY1 -- 獲取今天日期並轉化為時間戳
    var dueDay       = Number(90);                                // 到期天數

    // cata目錄、報價、領用量、渲染
    function show_ptreports(){    
        // step-2.彙整出各廠存量
        report_lists.forEach(function(row){
            var fab_id   = row['fab_id'];
            var local_id = row['local_id'];
            var cata_SN  = row['cata_SN'];
            var fid_sn   = fab_id+'_'+cata_SN;
            var flid_sn   = fab_id+'_'+local_id+'_'+cata_SN;
            var amount   = Number(row['amount']);
            var lot_num  = new Date(row['lot_num']).getTime();   // DAY2 -- 獲取比較日期並轉化為時間戳
    
            // 我想用JS進行日期的比較，格式是YYYY-MM-DD，當aDay - today >= 60 天，返回true，反之false
                // var today = new Date();
                // var aDay = new Date("2022-01-01"); // 改成你要比較的日期，格式是YYYY-MM-DD
                // var timeDiff = aDay.getTime() - today.getTime(); // 取得兩日期之間的毫秒數差
                // var daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24)); // 將毫秒數差轉換成天數差
                // if (daysDiff >= 60) { console.log("true"); }else{ console.log("false"); }
            
            var timeDiff = lot_num - Today;                              // 取得兩日期之間的毫秒數差
            var daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24)); // 將毫秒數差轉換成天數差
    
            if(amount >0){
                // 第1頁 每個廠的總數值_all
                    // 每個廠的總數值_all              
                        if(reportAmount[fid_sn+'_all'])     { reportAmount[fid_sn+'_all'] += amount;      }else{ reportAmount[fid_sn+'_all'] = amount; }
                        if(reportAmount[cata_SN+'_sum_all']){ reportAmount[cata_SN+'_sum_all'] += amount; }else{ reportAmount[cata_SN+'_sum_all'] = amount; }
                        // fab_all // 廠每個local的總數值_all              
                        if(reportAmount[flid_sn+'_all'])        { reportAmount[flid_sn+'_all'] += amount;         }else{ reportAmount[flid_sn+'_all'] = amount; }
                        if(reportAmount['f_'+fid_sn+'_sum_all']){ reportAmount['f_'+fid_sn+'_sum_all'] += amount; }else{ reportAmount['f_'+fid_sn+'_sum_all'] = amount; }
        
                    // // 未過期_saf
                    // if(daysDiff >= dueDay) {
                    //     if(reportAmount[fid_sn+'_saf'])     { reportAmount[fid_sn+'_saf'] += amount;      }else{ reportAmount[fid_sn+'_saf'] = amount; }
                    //     if(reportAmount[cata_SN+'_sum_saf']){ reportAmount[cata_SN+'_sum_saf'] += amount; }else{ reportAmount[cata_SN+'_sum_saf'] = amount; }
                    //     // fab_saf // 廠每個local的總數值_saf              
                    //     if(reportAmount[flid_sn+'_saf'])    { reportAmount[flid_sn+'_saf'] += amount;             }else{ reportAmount[flid_sn+'_saf'] = amount; }
                    //     if(reportAmount['f_'+fid_sn+'_sum_saf']){ reportAmount['f_'+fid_sn+'_sum_saf'] += amount; }else{ reportAmount['f_'+fid_sn+'_sum_saf'] = amount; }
        
                    // // 快到期_war
                    // }else if(daysDiff < dueDay && daysDiff > 1){            
                    //     if(reportAmount[fid_sn+'_war'])     { reportAmount[fid_sn+'_war'] += amount;      }else{ reportAmount[fid_sn+'_war'] = amount; }
                    //     if(reportAmount[cata_SN+'_sum_war']){ reportAmount[cata_SN+'_sum_war'] += amount; }else{ reportAmount[cata_SN+'_sum_war'] = amount; }
                    //     // fab_war // 廠每個local的總數值_war              
                    //     if(reportAmount[flid_sn+'_war'])        { reportAmount[flid_sn+'_war'] += amount;         }else{ reportAmount[flid_sn+'_war'] = amount; }
                    //     if(reportAmount['f_'+fid_sn+'_sum_war']){ reportAmount['f_'+fid_sn+'_sum_war'] += amount; }else{ reportAmount['f_'+fid_sn+'_sum_war'] = amount; }
        
                    // // 已過期_dan
                    // }else{
                    //     if(reportAmount[fid_sn+'_dan'])     { reportAmount[fid_sn+'_dan'] += amount;      }else{ reportAmount[fid_sn+'_dan'] = amount; }
                    //     if(reportAmount[cata_SN+'_sum_dan']){ reportAmount[cata_SN+'_sum_dan'] += amount; }else{ reportAmount[cata_SN+'_sum_dan'] = amount; }
                    //     // fab_dan // 廠每個local的總數值_dan 
                    //     if(reportAmount[flid_sn+'_dan'])        { reportAmount[flid_sn+'_dan'] += amount;         }else{ reportAmount[flid_sn+'_dan'] = amount; }
                    //     if(reportAmount['f_'+fid_sn+'_sum_dan']){ reportAmount['f_'+fid_sn+'_sum_dan'] += amount; }else{ reportAmount['f_'+fid_sn+'_sum_dan'] = amount; }
                    // }
            }
        })

        // step-3.選染到Table上指定欄位
        Object.keys(reportAmount).forEach(key => {
            var value = reportAmount[key];
            $('#'+key).empty();
            if(key.includes("cost")){
                $('#'+key).append('$'+value);
            }else{
                $('#'+key).append(value);
            }
        })
    }
    // 空值遮蔽：On、Off
    function groupBy_flag(name){
        var checkbox = document.getElementById(name+"_flag_Switch");
        var flag = checkbox.checked ? "On" : "Off";
        var table_tr = document.querySelectorAll('.'+name+' > tbody > tr');
        if(flag=='Off'){
            table_tr.forEach(function(row){
                row.classList.remove('unblock');
            })
        }else{
            table_tr.forEach(function(row){
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
        document.getElementById(name+'_htmlTable').value = htmlTableValue;
    }

    $(document).ready(function () {
        show_ptreports();
    })
        // var sinn = '<b>** 自動帶入 年領用累計 ... 完成</b>~';
        // inside_toast(sinn);
    // }


</script>

<?php include("../template/footer.php"); ?>

