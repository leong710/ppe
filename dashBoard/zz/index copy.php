<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    require_once("count_visitor.php");              // 網站計數器
    if(!isset($_SESSION)){                          // 確認session是否啟動
		session_start();
	}
    if(!empty($_SESSION["AUTH"]["pass"]) && !isset($_SESSION[$sys_id])){
		accessDenied_sys($sys_id);                  // 套用sys_id權限
    }

    $stock_percentages = show_site_percentage();    // 各廠器材存量百分比(缺點：截長補短的問題) table-1 驗證用
    $catalog_stocks = show_stock_byCatalog();       // 器材存量的清單 table-2
    $stock_losts = show_stock_lost();               // 各廠有缺少的清單 table-3

    // 今年年份
    $today_year = date('Y');
    // 半年分界線
    if(date('m') <= 6 ){
        $half = "H1";
    }else{
        $half = "H2";
    }
    // 建立查詢陣列for顯示今年點檢表
    $check_yh = array(
        'checked_year' => $today_year,
        'half' => $half
    );
    // dashBoard-表頭數據用，秀出-site短缺/器材短缺 table-0 左
    $stock_db1 = show_stock_db1();
    // dashBoard-表頭數據用，秀出-點檢達成率/未完成site數 table-0 右
    $stock_db2 = show_stock_db2($check_yh);

    if(isset($_SESSION[$sys_id])){
        $login_AUTH = TRUE;
    }else{
        $login_AUTH = FALSE;
    }
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .TOP {
            background-image: URL('../images/First-Aid-Box-and-Contents-Banner1.jpg');
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            /* overflow: hidden; 會影響表頭黏貼功能*/
            background-attachment: fixed;
            background-position: center top;
            /* background-position: left top; */
            background-repeat: no-repeat;
            /* background-size: cover; */
            background-size: contain;
            padding-top: 150px;
        }
        .ul , li {
            color: #FFFFFF;
        }
        .unblock{
            display: none;
        }
        .dbShow {
            text-align: center;
        }
        .cs_a{
            /* background-image: linear-gradient(to bottom, #548C00, #CCFF80); */
            background-color: #CCFF80;
        }
        .box_a{
            background-color: gray;
            border-radius: 10px;
        }
        .title {
            color: #FFFFFF;
            font-size: 20px;
        }
        .title_end {
            color: #FFFFFF;
            /* text-align: right; */
            font-size: 15px;
        }
        .inside {
            color: #FFD306;
            line-height: 85px;
            font-size: 70px;
            font-weight: bolder;
        }
        #toggle .ball {
            width: 90px;
            height: 90px;
            background-color: greenyellow;
            border-radius: 50%;
            line-height: 90px;
            /* font-weight:bold; */
            vertical-align: middle;
            margin: auto;               /*置中對齊*/
            display: flex;
            /* text-shadow: 3px 3px 5px rgba(0,0,0,.5); */
            box-shadow:3px 3px 9px gray;
            /* text-align: center; */
            /* align-items: center; */
            /* position: absolute; */
            /* top: 50%; */
            /* left: 50%; */
            /* transform: scale(0.9); */
            /* transform: translateY(-50%); */
            /* box-shadow: inset -7px -7px 7px 1px lightcyan; */
            transition: 1s;
        }
        #toggle .ball2 {
            width: 75px;
            height: 75px;
            background-color: white;
            border-radius: 50%;
            line-height: 75px;
            font-weight: bold;
            vertical-align: middle;
            margin: auto;               /*置中對齊*/
            /* display: flex; */
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
            box-shadow: inset 3px 3px 9px gray;
            /* position: absolute; */
        }
        #toggle .ball:hover{
            /* width: 90px; */
            /* height: 90px; */
            /* 字體變大1.3倍 */
            font-size: 1.3em;
            /* 向上抬升 */
            transform: translateY(-30%);
        }
    </style>
</head>

<body>
    <div class="TOP">
        <div class="col-12">
            <div class="row justify-content-center">
                <div class="col_xl_10 col-10 rounded p-3 py-2" style="background-color: rgba(255, 255, 255, .8);">
                    <div class="col-12 py-0">
                        <!-- 抬頭h1的位置 -->
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h3>tnESH Nurse 衛材管理</h3>
                            </div>
                            <div class="col-12 col-md-6 text-end">
                                <div id="show_time1" class="text-end" style="display:inline-block;">
                                    <script>
                                        setInterval("show_time1.innerHTML=new Date().toLocaleString()+' 星期'+'日一二三四五六'.charAt(new Date().getDay());",1000);  
                                    </script>
                                </div>
                                <!-- 網站人數 -->
                                <div class="my-2 t-center" style="display:inline-block;">
                                    <span>您是第 <?php echo $counter; //輸出計數器 ?> 位訪客！</span>
                                </div>
                            </div>
                        </div>

                     </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="row justify-content-center">

            <div class="col_xl_10 col-10">
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder"></div>
                
                <!-- 0.實驗性綜合統計數字看板 -->
                <div class="col-12 justify-content-center rounded bg-light py-0">
                    <div class="row dbShow">
                        <div class="col-12 col-md-3">
                            <div class="w-100 p-1 box_a"><a href="#site_loss" title="連結下方：安全存量警示清單">
                                <div class="title">存量警示</div>
                                <div class="inside my-0"><?php ECHO $stock_db1["site_num"];?></div>
                                <div class="title_end">廠區</div>
                            </div></a>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="w-100 p-1 box_a"><a href="#site_loss" title="連結下方：安全存量警示清單">
                                <div class="title">衛材需求</div>
                                <div class="inside my-0"><?php ECHO $stock_db1["catalog_num"];?></div>
                                <div class="title_end">品項</div>
                            </div></a>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="w-100 p-1 box_a"><a href="../checked/siteList.php" title="連結：半年檢紀錄總表">
                                <div class="title"><?php echo $half;?>未完成點檢</div>
                                <div class="inside my-0"><?php ECHO ($stock_db2["site_num"] - $stock_db2["check_num"]);?></div>
                                <div class="title_end">廠區</div>
                            </div></a>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="w-100 p-1 box_a"><a href="../checked/siteList.php" title="連結：半年檢紀錄總表">
                                <div class="title"><?php echo $half;?>點檢達成率</div>
                                <div class="inside my-0">
                                    <?php if($stock_db2["percentage"] == '100.00'){ $stock_db2["percentage"] = '100'; }
                                        ECHO $stock_db2["percentage"];?>
                                </div>
                                <div class="title_end">%百分比</div>
                            </div></a>
                        </div>
                    </div>
                </div>
                <!-- 1.各廠衛材量存量燈號 -->
                <div class="col-12 justify-content-center rounded my-2 bg-light">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <h4>1.各廠衛材量存量燈號：</h4>
                            </div>
                            <div class="col-12 col-md-6 py-0 text-end">
                                <button type="submit" id="checkItem" class="btn btn-sm btn-xs btn-success" onclick="open_div(this.id)">openSource</button>
                            </div>
                        </div>
                        <span>p.s.安全存量：單一Local算一筆進行合併計算；現場存量：單一品項全南廠所有存量(不論效期)。</span>
                    </div>
                    <!--1.各廠衛材存量百分比-數據驗證用 -->
                    <div class="col-12 justify-content-center rounded my-2 cs_a unblock" id="checkItem_div">
                        <div class="col-12">
                            <h4>1.各廠衛材存量百分比：(數據驗證用-正式版會移除)</h4>
                        </div>
                        <div class="col-12">
                            <table class="w-100">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>site</th>
                                        <th>安全存量</th>
                                        <th>現場存量</th>
                                        <th>正負差額</th>
                                        <th>存量成數</th>
                                        <th>存量燈號</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- 定義陣列 -->
                                    <?php
                                        $fab_balls = [];     // 燈號
                                        $caseNum = 0;
                                    ?>
                                    <?php foreach($stock_percentages as $stock_percentage){ ?>
                                        <tr>
                                            <td><?php echo $stock_percentage["site_id"];?></td>
                                            <td><?php echo $stock_percentage["site_title"];?></td>
                                            <td><?php echo $stock_percentage["stock_stand"];?></td>
                                            <td><?php echo $stock_percentage["stock_stand"]+$stock_percentage["sqty"];?></td>
                                            <td><?php echo $stock_percentage["sqty"];?></td>
                                            <td><?php echo $stock_percentage["percentage"]."%";?></td>
                                            <td style="font-size: 1.2em; color: 
                                                <?php if($stock_percentage["sqty"] > 0 ){ 
                                                    if($stock_percentage["sqty"] >= $stock_percentage["stock_stand"]){ ?>
                                                        blue 
                                                    <?php } else { ?>
                                                        green
                                                    <?php } }?>
                                                <?php if($stock_percentage["sqty"] == 0 ){ ?> yellow<?php } ?>
                                                <?php if($stock_percentage["sqty"] < 0 ){ ?> red<?php } ?> ;"> 
                                                ●
                                            </td>
                                        </tr>
                                    <?php 
                                        if($stock_percentage["sqty"] > 0 ){ 
                                            if($stock_percentage["sqty"] >= $stock_percentage["stock_stand"]){ 
                                                $bar_color = 'rgba(54, 162, 235, 1)';   // 藍色 blue 
                                            } else { 
                                                $bar_color = 'rgba(0, 255, 72, 1)';  // 綠色 green
                                            } }
                                        if($stock_percentage["sqty"] == 0 ){ 
                                            $bar_color = 'rgba(255, 204, 0, 1)';   // 黃色yellow 
                                        } 
                                        if($stock_percentage["sqty"] < 0 ){  
                                            $bar_color = 'rgba(255, 99, 132, 1)';   // 紅色red
                                        } 

                                        array_push($fab_balls, array('site_id' => $stock_percentage["site_id"] ,'site_title' => $stock_percentage["site_title"], 'bgc' => $bar_color ));
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--1.各廠衛材存量膯號顯示 -->
                    <div class="col-12 rounded bg-white text-center" id="toggle">
                        <?php foreach($fab_balls as $fab_ball){?>
                            <div class="ball p-1 m-1" style="display:inline-block;background-color:<?php echo $fab_ball['bgc'];?>">
                                <a href="../stock/index.php?site_id=<?php echo $fab_ball['site_id'];?>">
                                    <div class="ball2 p-0 m-1">
                                        <?php echo $fab_ball['site_title'];?>
                                    </div>
                                </a>
                            </div>
                        <?php }?>
                    </div>
                </div>
                <!-- 2.衛材基存清單 -->
                <div class="col-12 justify-content-center rounded my-2 bg-light">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <h4>2.衛材基存清單：</h4>
                            </div>
                            <div class="col-12 col-md-6 py-0 text-end">
                                <a href="#base_stock" id="base_stock" onclick="open_div(this.id)"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <span>p.s.安全存量：單一Local算一筆進行合併計算；現場存量：單一品項全南廠所有存量(不論效期)。</span>
                    </div>
                    <div class="col-12 rounded bg-white unblock" id="base_stock_div">
                        <table class="w-100">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>衛材名稱</th>
                                    <th>安全存量</th>
                                    <th>現場存量</th>
                                    <th>正負差額</th>
                                    <th data-toggle="tooltip" data-placement="bottom" title="(現場存量/2倍安全存量)*100%">存量成數 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                    <th>存量燈號</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($catalog_stocks as $catalog){ ?>
                                    <tr>
                                        <td><?php echo $catalog["catalog_id"];?></td>
                                        <td style="text-align: left;"><a href="../catalog/repo.php?id=<?php echo $catalog["catalog_id"];?>"><?php echo $catalog["catalog_title"];?></a></td>
                                        <td><?php echo $catalog["stock_stand"];?></td>
                                        <td <?php if($catalog["stock_amount"] < $catalog['stock_stand']){ ?> style="background-color:#FFBFFF;color:red;font-size:1.2em;"  <?php } ?>>
                                        <?php echo $catalog["stock_amount"];?></td>
                                        <td style="color: 
                                            <?php if($catalog["qty"] > 0 ){ 
                                                if($catalog["qty"]>=$catalog["stock_stand"]){ ?>
                                                    blue
                                                <?php } else { ?>
                                                    green 
                                                <?php } } ?>
                                            <?php if($catalog["qty"] < 0 ){ ?> red<?php } ?> ;">
                                            <?php echo $catalog["qty"];?></td>
                                        <td style="color: 
                                                <?php if($catalog["qty"] > 0 ){ 
                                                if($catalog["qty"]>=$catalog["stock_stand"]){ ?>
                                                    blue
                                                <?php } else { ?>
                                                    green 
                                                <?php } } ?>
                                            <?php if($catalog["qty"] < 0 ){ ?> red<?php } ?> ;">
                                            <!-- <php echo round($catalog["stock_amount"]/($catalog["stock_stand"]*2)*100,2)."%";?> -->
                                        </td>
                                        <td style="font-size: 1.2em; color: 
                                            <?php if($catalog["qty"] > 0 ){ 
                                                if($catalog["qty"]>=$catalog["stock_stand"]){ ?>
                                                    blue
                                                <?php } else { ?>
                                                    green 
                                                <?php } } ?>
                                            <?php if($catalog["qty"] == 0 ){ ?> yellow<?php } ?> 
                                            <?php if($catalog["qty"] < 0 ){ ?> red<?php } ?> ;"> 
                                            ●
                                        </td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--3.安全存量警示清單 -->
                <div class="col-12 justify-content-center rounded my-2 bg-light">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <h4>3.安全存量警示清單：</h4>
                            </div>
                            <div class="col-12 col-md-6 py-0 text-end">
                                <a href="#site_loss" id="site_loss" onclick="open_div(this.id)"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <span>p.s.單一品項所有存量(不論效期)等於或低於安全存量時才顯示!</span>
                    </div>
                    <div class="col-12 rounded bg-white unblock" id="site_loss_div">
                        <table class="w-100">
                            <thead>
                                <tr>
                                    <th>site_local</th>
                                    <th>衛材名稱</th>
                                    <th>安全存量</th>
                                    <th>現場存量</th>
                                    <th>正負差額</th>
                                    <th data-toggle="tooltip" data-placement="bottom" title="(現場存量/2倍安全存量)*100%">存量成數 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $check_item ="";?>
                                <?php foreach($stock_losts as $stock_lost){ ?>
                                    <tr <?php if($check_item != $stock_lost['site_title']){?>style="border-top:3px #FFD382 solid;"<?php } ?>>
                                        <td style="text-align: left;"><?php echo $stock_lost["site_title"]."_".$stock_lost["local_title"];?></td>
                                        <td style="text-align: left;"><a href="../catalog/repo.php?id=<?php echo $stock_lost["catalog_id"];?>"><?php echo $stock_lost["catalog_title"];?></a></td>
                                        <td><?php echo $stock_lost["stock_stand"];?></td>
                                        <td <?php if($stock_lost["stock_amount"] < $stock_lost['stock_stand']){ ?> style="color:red;" <?php } ?>>
                                            <?php echo $stock_lost["stock_amount"];?></td>
                                        <td <?php if($stock_lost["qty"] > 0 ){ ?> style="color: blue;" <?php } ?> 
                                            <?php if($stock_lost["qty"] < 0 ){ ?> style="color: red;" <?php } ?> >
                                            <?php echo $stock_lost["qty"];?></td>
                                        <td><?php echo round($stock_lost["stock_amount"]/($stock_lost["stock_stand"]*2)*100,2)."%";?></td>
                                    </tr>
                                    <?php $check_item = $stock_lost["site_title"];?>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- goTop滾動畫面DIV 2/4-->
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>

<script>
    // // <!-- 摺疊開關 -->
    // var checkItem = document.getElementById("checkItem");   // 驗證用表單的按鈕
    // // 在清單上建立按鈕
    // checkItem.addEventListener('click',function(){
    //     if($("#checkItem_div").css("display")=="none"){
    //         $("#checkItem_div").css("display","block")
    //         return;
    //     }
    //     if($("#checkItem_div").css("display")=="block"){
    //         $("#checkItem_div").css("display","none")
    //         return;
    //     }
    // })

    function open_div(obj){
        $("#"+obj+" > .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
        if($("#"+obj+"_div").css("display")=="none"){
            $("#"+obj+"_div").css("display","block")
            return;
        }
        if($("#"+obj+"_div").css("display")=="block"){
            $("#"+obj+"_div").css("display","none")
            return;
        }
    }

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>
<!-- 警告視窗 -->
<script>
    var checkList = document.getElementById("checkList");   // 檢點表單
    var alertPlaceholder = document.getElementById("liveAlertPlaceholder")      // Bootstrap Alarm

    // 神奇PHP變數帶入js方法
    <?php echo "var check_yh_list_num ='$numChecked';";?>       // 年度檢查筆數
    <?php echo "var login_AUTH ='$login_AUTH';";?>              // 是否已經登入

    // Bootstrap Alarm function
    function alert(message, type) {
        var wrapper = document.createElement('div')
        wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message + '<a href="#"  id="checkItem" class="alert-link">[打開點檢表]</a>' + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'

        alertPlaceholder.append(wrapper)
    }
    // 假如index找不到當下存在已完成的表單，而且user已經登入就alarm它!
    if (login_AUTH && check_yh_list_num == '0') {
        alert('*** <?php echo $today_year;?> 年 <?php echo $half;?> 年度 衛材儲存量確認開始了! 請務必在指定時間前完成確認~ ', 'danger')
    }

</script>

<?php include("../template/footer.php"); ?>

