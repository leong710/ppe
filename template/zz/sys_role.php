<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>鹹魚改運系統</title>
    <link rel="stylesheet" href="../../../libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/grid.css">
    <!-- <link rel="stylesheet" href="../../css/style.css?v=<=time()?>"> -->
    <script src="../../../libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            /* background-color: rgb(#00273b); */
            /* background-image: linear-gradient(to top left,#4A0080,#000000); */
                /* 背景全部推開=螢幕高度滿版 */
            /* height: 80vh; */
            /* 背景顏色-原本的漸層 */
            /* background-image: linear-gradient(to top left,#4A0080,#000000); */
            background-image: linear-gradient(to top left,#00273b,#00273b);
            /* 背景固定不移動 */
            background-attachment: fixed;
        }
        .add_btn:hover {
            max-height: 100%;
            margin-bottom: 0px;
            vertical-align: middle; 
            color: goldenrod;
            /* padding: 0px; */
        }
    </style>
    <script>
        function goBackAfterDelay() {
            setTimeout(function() {
                window.history.back(); // 返回上一頁
                window.location.reload();
            }, 2000); // 延遲 2000 毫秒（即 2 秒）
        }

        // fun_2 倒數 n秒自動關閉視窗功能
        function CountDown(seconds) {
            const btnMsg = document.getElementById("btnMsg");
            let i = seconds;                        // 15次==15秒
            const loop = () => {
                if (i >= 0) {
                    let txt = '';
                    for (let j = 0; j < i; j++) {
                        txt += '>';  // 每次迴圈增加一個星號
                    }
                    btnMsg.innerHTML = `視窗倒數 ${i} 秒 ${txt} `;
                    setTimeout(loop, 1000);
                } else {
                    // callback();                  // 要執行的程式
                    btnMsg.innerHTML = "GoGoGo ";
                    // window.open('', '_self', '');
                    // window.close();
                    window.history.back(); // 返回上一頁
                }
                i--;
            };
            loop();
        }
    </script>
</head>
<!-- <body onload="goBackAfterDelay()"> -->
<body onload="CountDown(3)">
    <div class="col-12">
        <div class="row justify-content-center">
            <div class=" col-8 rounded p-3 " style="background-color: rgba(255, 255, 255, .8);">
                <h5><b>
                    <?php
                        if(!isset($_SESSION)){                                              // 確認session是否啟動
                            session_start();
                            if(empty($_SESSION["AUTH"])){
                                $_SESSION["AUTH"] = [
                                    "pass"       => "ldap",
                                    "user"       => "LEONG.CHEN",
                                    "emp_id"     => "10008048",
                                    "cname"      => "陳建良",
                                    "sign_code"  => "9T041500",
                                    "role"       => 2,
                                    "idty"       => "E",
                                    "BTRTL"      => "0007",
                                    "emp_scope"  => "南科",
                                ];
                                
                                $sys_id = $sys_id ?? "lcsdb";
                                $_SESSION[$sys_id] = [
                                    "id"        => "1",
                                    "emp_id"    => "10008048",
                                    "user"      => "LEONG.CHEN",
                                    "cname"     => "陳建良",
                                    "ufab_id"   => "0",
                                    "sfab_id"   => ["2"],
                                    "role"      => "0",
                                    "idty"      => 1,
                                    "fab_scope" => ["9","11","2","0"],
                                    "BTRTL"     => "南科_0012,南科_0012",
                                    "autolog"   => 1,
                                ];
                            }
                        }
                
                        if(isset($_REQUEST)){
                            $sys_id = $_REQUEST["sys_id"] ?? '';
                            $role   = $_REQUEST["role"]   ?? '';
                        }
                
                        if(empty($sys_id) || !isset($role)){
                            echo "error.1：兄弟...沒有任何參數...請確認!!";
                
                        } else {
                            if(isset($_SESSION[$sys_id])){
                                if($_SESSION[$sys_id]["role"] == $role ){
                                    echo "error.2：兄弟...你的session[{$sys_id}][role]都是 {$role} ... 沒有變動...請確認!!";
                                }else{
                                    $pre_role = $_SESSION[$sys_id]["role"];
                                    $_SESSION[$sys_id]["role"] = $role;
                                    echo "success.1：兄弟...你的session[{$sys_id}][role]由 {$pre_role} 變更成 {$role} ... 變動完成!!";
                                }
                                echo "<hr><pre>";
                                print_r($_SESSION[$sys_id]);
                                echo "</pre>";
                
                            }else{
                                echo "error.3：兄弟...你的session沒有 {$sys_id}...請確認!!";
                            }
                        }
                    ?>
                </b></h5>
                <hr>
                <div class="col-12 py-2 text-end">
                    241108 $sys_role 鹹魚改運系統：<button type="button" class="btn btn-primary add_btn" onclick="history.back()"><span id="btnMsg"></span>&nbsp;回上頁</button>
                </div>
            </div>
        </div>
    </div>
</body>
