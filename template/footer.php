<footer class="text-center text-white">
    <div class="mb-3">
        copyright &copy; 2023 Design by INX tnESH.cop<br>
        <p>Revision：2025/10</p>
    </div>
    <div class="d-custom-none">( 本系統螢幕解析度建議：1920 x 1080 dpi，低於此解析度將會影響操作體驗 )</div>
    <div id="debug" class="mb-5">
        <?php 
            if(isset($sys_auth) || (isset($_REQUEST["debug"]) || ($_SERVER['REMOTE_ADDR'] == '10.53.230.106'))){
                $tsr = "../template/zz/TSR.php";
                if(file_exists($tsr)){
                    include($tsr); 
                }else{
                    echo "{$tsr} is loss...";
                }
                echo "<div class='text-start text-white'><pre>";
                    echo "您的IP為 : ".$_SERVER['REMOTE_ADDR']." => '".sha1(md5($_SERVER['REMOTE_ADDR']))."'</br>"; 
                    echo "... session_id: ".session_id()."</br>";
                    if(isset($_SESSION["AUTH"])){
                        echo "... AUTH：";
                        print_r($_SESSION["AUTH"]);
                        echo "<hr>";
                    }
                    if(isset($_SESSION[$sys_id])){
                        echo "... sys_id({$sys_id})：";
                        print_r($_SESSION[$sys_id]);
                        echo "<hr>";
                    }
                echo "</pre></div>";
            } 
        ?>
    </div>
</footer>

</body><!-- 防止 ERR_CACHE_MISS -->
<script>window.history.replaceState(null, null, window.location.href);</script>
</html>