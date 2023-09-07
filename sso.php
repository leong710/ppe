<?php
// // // *** none function group
    function accessDenied($sys_id){
        $url='http://'.$_SERVER['HTTP_HOST'].'/';   // 複製本頁網址藥用
        session_start();
        if(!isset($_SESSION["AUTH"]) || !isset($_SESSION[$sys_id])){
            header("refresh:0;url={$url}tnESH_SSO/login.php?sys_id={$sys_id}");
            exit;
        }
        return;
    }
    // reload sys_id & local user role
    function accessDenied_sys($sys_id){
        $url='http://'.$_SERVER['HTTP_HOST'].'/';                           // 複製本頁網址藥用
        if(!isset($_SESSION)){                                              // 確認session是否啟動
            session_start();
        }
        if(isset($_SESSION["AUTH"]) && !empty($_SESSION["AUTH"]["pass"])){  // 確認是否完成AUTH/pass => True
            if(isset($_SESSION[$sys_id])){                                  // 如果sys_id已經建立，就返回
                return;
                
            }else{                                                          // sys_id還沒建立，就導入
                $user = $_SESSION["AUTH"]["user"];
                $pdo = pdo();
                $sql = "SELECT u.*, _fab.fab_title, _fab.fab_remark
                        FROM _users u 
                        LEFT JOIN _fab ON u.fab_id = _fab.id
                        WHERE u.user = ? ";
                $stmt = $pdo -> prepare($sql);
                $stmt -> execute([$user]);
                $sys_local_row = $stmt -> fetch();
    
                if($sys_local_row){                                         // 有sys_id的人員權限資料
                    if($sys_local_row["role"] != ""){                       // 權限沒被禁用
                        $sys_local_row["sfab_id"] = explode(",",$sys_local_row["sfab_id"]);       //資料表是字串，要炸成陣列
                        $_SESSION[$sys_id] = $sys_local_row;                // 導入$sys_id指定權限

                    }else{                                                  // 權限被禁用
                        echo "<script>alert('{$sys_local_row["cname"]} Local帳號停用，請洽管理員')</script>";
                    }
                }else{                                                      // 沒有sys_id的人員權限資料
                    echo "<script>alert('{$user} local無資料，請洽管理員')</script>";
                    header("location:../auth/register.php?user=$user");     // 沒有local資料，帶入註冊頁面
                    exit;
                }
                
                // 20230906_set this point to log logs
                header("refresh:0;url={$url}tnESH_SSO/autoLog.php?sys_id={$sys_id}");
                exit;
            }
        }else{                                                              // 確認AUTH/pass => false
            header("refresh:0;url={$url}tnESH_SSO/login.php?sys_id={$sys_id}");
            exit;
        }
        return;
    }
    
    function accessDeniedAdmin($sys_id){
        session_start();
        if(!isset($_SESSION["AUTH"]) || !isset($_SESSION[$sys_id]) || $_SESSION[$sys_id]["role"] == "" || $_SESSION[$sys_id]["role"] >= "2"){
            header('location:../');
            exit;
        }
        return;
    }

?>