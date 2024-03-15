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

    $login_AUTH = (isset($_SESSION[$sys_id])) ? true : false ;

    // 讓一般nobody用戶帶到 我的申請文件
    if($login_AUTH && $_SESSION[$sys_id]["role"] >= 3 && empty($_SESSION["AUTH"]["dept"])){
        header("refresh:0;url=../receive/");
        exit;
    }

    $stock_percentages = show_fab_percentage();     // table-1 驗證用：各廠器材存量百分比(缺點：截長補短的問題) 
    $catalog_stocks = show_stock_byCatalog();       // table-2：現場存量總清單 
    $stock_losts = show_stock_lost();               // table-3：安全存量警示清單 

    $today_year = date('Y');                        // 今年年份
    $half = (date('m') <= 6 ) ? "H1" : "H2";        // 半年分界線

    // 建立查詢陣列for顯示今年點檢表
    $check_yh = array(
        'checked_year' => $today_year,
        'half'         => $half
    );
    // dashBoard-表頭數據用，秀出-site短缺/器材短缺 table-0 左 1、2
    $stock_db1 = show_stock_db1();
    // dashBoard-表頭數據用，秀出-點檢達成率/未完成site數 table-0 右 3、4
    $stock_db2 = show_stock_db2($check_yh);
    if(empty($stock_db2)){
        $stock_db2 = [];
    }

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <script src="../../libs/jquery/jquery.min.js" ></script>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script src="../../libs/jquery/mloading_init.js"></script>
    <style>
        .TOP {
            background-image: 
                repeating-linear-gradient(black 0px, black 1px, transparent 1px, transparent 2px),
                URL('../images/stock2.jpg');
            width: auto;
            height: auto;
            position: relative;
            overflow: hidden;
            /* overflow: hidden; 會影響表頭黏貼功能*/
            background-attachment: fixed;
            background-position: center top;
            background-repeat: no-repeat;
            background-size: cover;
            background-size: contain;
            /* padding-top: 300px; */
        }
        .ul , li {
            color: #FFFFFF;
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
        #toggle .ball:hover {
            /* width: 90px; */
            /* height: 90px; */
            /* 字體變大1.3倍 */
            font-size: 1.3em;
            /* 向上抬升 */
            transform: translateY(-30%);
        }
        .nav-link {
            color: white;
        }   
        .nav-link:hover {
            color: white;
            font-weight: bold;
        }  
        .page_title, .op_tab_btn {
            color: white;
            /* text-shadow:3px 3px 9px gray; */
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
        } 
    </style>
</head>

<body>
    <!-- 表頭 -->
    <div class="TOP">
        <div class="col-12">
            <div class="row justify-content-center">
                <!-- 抬頭h1的位置 -->
                <div class="col_xl_10 col-10 rounded p-3 py-2" style="background-color: rgba(112, 123, 124, .7);">
                    <div class="col-12 page_title">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <h3><b>tnESH PPE防護具管理</b></h3>
                            </div>

                            <div class="col-12 col-md-6 py-0 text-end">
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

                <?php if($login_AUTH){ ?>
                    <!-- 0.實驗性綜合統計數字看板 -->
                    <div class="col_xl_10 col-10 rounded p-3 my-2" style="background-color: rgba(255, 255, 255, .7);">
                        <div class="row center">
                            <div class="col-6 col-md-3">
                                <a href="#site_loss" title="連結下方：安全存量警示清單">
                                    <div class="w-100 p-1 box_a">
                                        <div class="title">存量警示</div>
                                        <div class="inside my-0"><?php ECHO $stock_db1["fab_num"];?></div>
                                        <div class="title_end">廠區</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="#site_loss" title="連結下方：安全存量警示清單">
                                    <div class="w-100 p-1 box_a">
                                        <div class="title">器材需求</div>
                                        <div class="inside my-0"><?php ECHO $stock_db1["cata_num"];?></div>
                                        <div class="title_end">品項</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="../checked/" title="連結：半年檢紀錄總表">
                                    <div class="w-100 p-1 box_a">
                                        <div class="title"><?php echo $half;?>未完成點檢</div>
                                        <div class="inside my-0">
                                            <?php if(isset($stock_db2["fab_num"])){
                                                    ECHO ($stock_db2["fab_num"] - $stock_db2["check_num"]);
                                                }else{
                                                    ECHO "0";
                                                }?>
                                        </div>
                                        <div class="title_end">廠區</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="../checked/" title="連結：半年檢紀錄總表">
                                    <div class="w-100 p-1 box_a">
                                        <div class="title"><?php echo $half;?>點檢達成率</div>
                                        <div class="inside my-0">
                                            <?php if(isset($stock_db2["percentage"])){
                                                    ECHO ($stock_db2["percentage"] == '100.00') ? '100' : $stock_db2["percentage"];
                                                }else{
                                                    ECHO "0";
                                                }?>
                                        </div>
                                        <div class="title_end">%百分比</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-12 my-5"></div>
                    <div class="col-12 my-5"></div>
                    <div class="col-12 my-4"></div>
                <?php } ?>

                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder"></div>  
            </div>
        </div>
    </div>
    
    <!-- 內容 -->
    <?php if($login_AUTH){ ?>
        <div class="col-12 pt-0">
            <div class="row justify-content-center">
                <div class="col_xl_10 col-10">
                    <!-- NAV 分頁標籤 -->
                    <div class="row py-0">
                        <nav>
                            <div class="nav nav-tabs pt-2 pb-0" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-tab_1" data-bs-toggle="tab" data-bs-target="#tab_1" type="button" role="tab" aria-controls="tab_1" aria-selected="true" >1.各廠存量燈號</button>
                                <button class="nav-link"        id="nav-tab_2" data-bs-toggle="tab" data-bs-target="#tab_2" type="button" role="tab" aria-controls="tab_2" aria-selected="false">2.現存總清單</button>
                                <button class="nav-link"        id="nav-tab_3" data-bs-toggle="tab" data-bs-target="#tab_3" type="button" role="tab" aria-controls="tab_3" aria-selected="false">3.安量警示清單</button>
                            </div>
                        </nav>
                    </div>
           
                    <!-- 內頁 -->
                    <div class="tab-content" id="nav-tabContent">
                        <!-- 1.各廠器材量存量燈號 -->
                        <div class="tab-pane bg-white fade p-2 show active" id="tab_1" role="tabpanel" aria-labelledby="nav-tab_1">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12 col-md-6 py-0">
                                        <h4>1.各廠存量燈號：</h4>
                                    </div>
                                    <div class="col-12 col-md-6 py-0 text-end">
                                        <a href="#base_stock" id="checkItem" onclick="open_div(this.id)"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-12 py-0">
                                        <span>ps-1.計算公式：(低於安量數) / (品項總數) * 100% = 占比</span></br>
                                        <span>ps-2.紅燈(占比>=30%)、橘燈(30%>占比>0%)、綠燈(占比=0%)</span>
                                    </div>
                                </div>
                                <!--1.各廠器材存量百分比-數據驗證用 -->
                                <div class="row rounded cs_a unblock" id="checkItem_div">
                                    <div class="col-12">
                                        <h5>(數據驗證)</h5>
                                        <table class="w-100">
                                            <thead>
                                                <tr>
                                                    <th>fab / local</th>
                                                    <th>品項數</th>
                                                    <th>低於安量數</th>
                                                    <th>占比</th>
                                                    <th>燈號</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- 定義陣列 -->
                                                <?php
                                                    $fab_balls = [];                                        // 燈號
                                                    $caseNum = 0;
                                                    foreach($stock_percentages as $stock_percentage){ 
                                                        $stock_perc_low_level = $stock_percentage["low_level"];
                                                        if($stock_perc_low_level > 0 ){ 

                                                            if($stock_percentage["percentage"] >= 30 ){ 
                                                                $stock_perc_color = "red";
                                                                $bar_color = 'rgba(255, 99, 132, 1)';       // 紅色red

                                                            } else {
                                                                $stock_perc_color = "orange";
                                                                $bar_color = 'rgba(255, 204, 0, 1)';        // 黃色yellow 
                                                            }

                                                        } else if($stock_perc_low_level == 0){ 
                                                                $stock_perc_color = "green";
                                                                $bar_color = 'rgba(0, 255, 72, 1)';         // 綠色 green

                                                        } else {
                                                            $stock_perc_color = "blue";
                                                            $bar_color = 'rgba(54, 162, 235, 1)';           // 藍色 blue 
                                 
                                                        }
                                                        array_push($fab_balls, array('fab_id' => $stock_percentage["fab_id"] ,'fab_title' => $stock_percentage["fab_title"], 'bgc' => $bar_color ));
                                                    ?>
                                                    <tr>
                                                        <td style="text-align: left;" title="l_aid:<?php echo $stock_percentage["local_id"];?>">
                                                            <?php echo $stock_percentage["fab_title"]." / ".$stock_percentage["local_title"]." (".$stock_percentage["local_remark"].")";?></td>
    
                                                        <td><?php echo $stock_percentage["count_SN"];?></td>
                                                        <td><?php echo $stock_percentage["low_level"];?></td>
                                                        
                                                        <td><?php echo $stock_percentage["percentage"]."%";?></td>
                                                        <td style="font-size: 1.2em; color:<?php echo $stock_perc_color;?>;"> ● </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--1.各廠器材存量膯號顯示 -->
                                <div class="col-12 rounded bg-white text-center" id="toggle">
                                    <?php foreach($fab_balls as $fab_ball){?>
                                        <div class="ball p-1 m-1" style="display:inline-block;background-color:<?php echo $fab_ball['bgc'];?>">
                                            <a href="../stock/index.php?fab_id=<?php echo $fab_ball['fab_id'];?>">
                                                <div class="ball2 p-0 m-1">
                                                    <?php echo $fab_ball['fab_title'];?>
                                                </div>
                                            </a>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
    
                        <!-- 2.器材基存清單 -->
                        <div class="tab-pane bg-white fade p-2" id="tab_2" role="tabpanel" aria-labelledby="nav-tab_2">
                            <div class="col-12 mb-1 bg-light">
                                <div class="row">
                                    <div class="col-12 col-md-6 py-0">
                                        <h4>2.現存總清單：</h4>
                                    </div>
                                    <div class="col-12 col-md-6 py-0 text-end">
                                        <a href="#base_stock" id="base_stock" onclick="open_div(this.id)"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-12 py-0">
                                        <span>p.s.安全存量：單一Local算一筆進行合併計算；&nbsp>>&nbsp現場存量：全南廠所有存量(不論效期)。&nbsp>>&nbsp存量成數：(現場存量/安全存量)*100%</span>
                                        </br><span>存量燈號：藍燈(現量>=100%)、綠燈(100%>現量>=80%)、黃燈(80%>現量>=60%)、紅燈(60%>現量)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 rounded border bg-white " id="base_stock_div">
                                <table class="w-100 table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>SN_器材名稱</th>
                                            <th>安全存量</th>
                                            <th>現場存量</th>
                                            <th>差額</th>
                                            <th data-toggle="tooltip" data-placement="bottom" title="(現場存量/安全存量)*100%">存量成數 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                            <th>存量燈號</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($catalog_stocks as $catalog){ 
                                            // $cata_pc = round($catalog["stock_amount"]/($catalog["stock_stand"])*100,2);   // 預防出錯
                                            $cata_stock_amount = ( $catalog["stock_amount"] == 0 ? 1 : $catalog["stock_amount"] );
                                            $cata_stock_stand  = ( $catalog["stock_stand"]  == 0 ? 1 : $catalog["stock_stand"] );
                                            $cata_pc = round(($cata_stock_amount / $cata_stock_stand)*100,2);
                                            if($cata_pc >= 100 ){ 
                                                $cata_color = "blue";
                                            } else if($cata_pc >= 80){ 
                                                $cata_color = "green";
                                            } else if($cata_pc >= 60){ 
                                                $cata_color = "orange";
                                            } else {
                                                $cata_color = "red";
                                            } ?>
                                            <tr>
                                                <td style="text-align: left;"><a href="../catalog/repo.php?sn=<?php echo $catalog["cata_SN"];?>"><?php echo $catalog["cata_SN"]."_".$catalog["cata_pname"];?></a></td>
                                                <td><?php echo $catalog["stock_stand"];?></td>
                                                <td <?php if($cata_pc < 60){ ?> style="background-color:pink; color:red; font-size:1.2em;"<?php }?>><?php echo $catalog["stock_amount"];?></td>
                                                <td style="color: <?php echo $cata_color;?>;"><?php echo $catalog["qty"];?></td>
                                                <td style="color: <?php echo $cata_color;?>;"><?php echo $cata_pc."%";?></td>
                                                <td style="font-size: 1.2em; color: <?php echo $cata_color;?>;"> ●</td>
                                            </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!--3.安全存量警示清單 -->
                        <div class="tab-pane bg-white fade p-2" id="tab_3" role="tabpanel" aria-labelledby="nav-tab_3">
                            <div class="col-12 mb-1 bg-light">
                                <div class="row">
                                    <div class="col-12 col-md-6 py-0">
                                        <h4>3.安全存量警示清單：</h4>
                                    </div>
                                    <div class="col-12 col-md-6 py-0 text-end">
                                        <a href="#fab_loss" id="fab_loss" onclick="open_div(this.id)"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-12 py-0">
                                        <span>p.s.單一品項所有存量(不論效期)等於或低於安全存量時才顯示!</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 rounded border bg-white " id="fab_loss_div">
                                <table class="w-100 table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>fab_local</th>
                                            <th>SN_器材名稱</th>
                                            <th>安全存量</th>
                                            <th>現場存量</th>
                                            <th>差額</th>
                                            <th data-toggle="tooltip" data-placement="bottom" title="(現場存量/安全存量)*100%">存量成數 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $check_item ="";
                                            foreach($stock_losts as $stock_lost){ 
                                                // $stock_pc = round($stock_lost["stock_amount"]/($stock_lost["stock_stand"])*100,2);   // 預防出錯
                                                $stock_lost_amount = ( $stock_lost["stock_amount"] == 0 ? 1 : $stock_lost["stock_amount"] );
                                                $stock_lost_stand  = ( $stock_lost["stock_stand"]  == 0 ? 1 : $stock_lost["stock_stand"] );
                                                $stock_pc = round(($stock_lost_amount / $stock_lost_stand)*100,2);
                                                if($stock_pc >= 100 ){ 
                                                    $stock_color = "blue";
                                                } else if($stock_pc >= 80){ 
                                                    $stock_color = "green";
                                                } else if($stock_pc >= 60){ 
                                                    $stock_color = "orange";
                                                } else {
                                                    $stock_color = "red";
                                                } ?>
                                            <tr <?php if($check_item != $stock_lost['fab_title']){?>style="border-top:3px #FFD382 solid;"<?php } ?>>
                                                <td style="text-align: left;"><?php echo $stock_lost["fab_title"]."_".$stock_lost["local_title"];?></td>
                                                <td style="text-align: left;"><a href="../catalog/repo.php?sn=<?php echo $stock_lost["cata_SN"];?>"><?php echo $stock_lost["cata_SN"]."_".$stock_lost["cata_pname"];?></a></td>
                                                <td><?php echo $stock_lost["stock_stand"];?></td>
                                                <td style="<?php echo ($stock_pc < 100 && $stock_pc >= 80) ? 'background-color:yellow; color:red;':'';
                                                                 echo ($stock_pc < 80  && $stock_pc >= 60) ? 'background-color:orange; color:red;':'';
                                                                 echo ($stock_pc < 60) ? 'background-color:pink; color:red;':'';
                                                            ?>"><?php echo $stock_lost["stock_amount"];?></td>
                                                <td style="color: <?php echo $stock_color;?>;"><?php echo $stock_lost["qty"];?></td>
                                                <td style="color: <?php echo $stock_color;?>;"><?php echo $stock_pc."%";?></td>
                                            </tr>
                                        <?php $check_item = $stock_lost["fab_title"];
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
    
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="col-12"></div>
    <?php } ?>

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>
<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>

<script>

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

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    // <php echo "var check_yh_list_num ='$numChecked';";?>       // 年度檢查筆數
    // <php echo "var login_AUTH ='$login_AUTH';";?>              // 是否已經登入

    // Bootstrap Alarm function
    function alert(message, type) {
        var wrapper = document.createElement('div');
        wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message + '<a href="#"  id="checkItem" class="alert-link">[打開點檢表]</a>' + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

        alertPlaceholder.append(wrapper);
    }

</script>

<?php include("../template/footer.php"); ?>

