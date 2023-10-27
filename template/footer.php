<footer class="text-center text-white">
    <div class="mb-3">
        <!-- copyright &copy; 2022 Design by inx tnESH.cop　　　　owner:陳建良 -->
        copyright &copy; 2023 Design by inx tnESH.cop<br>
        <?php 
            echo "<br>您的IP為 : ".$_SERVER['REMOTE_ADDR']." => '".sha1(md5($_SERVER['REMOTE_ADDR']))."'</br>"; 
        ?>
    </div>
    <div class="mb-3 text-start">
        <?php 
            // if(isset($_SESSION["AUTH"]) && $_SESSION["AUTH"]["role"] <= 2 ){
            if(isset($_SESSION["AUTH"])){
                echo "session_id: ".session_id()."</br>";
                echo "<pre>";
                // print_r($_REQUEST);
                print_r($_SESSION); 
                echo "</pre>text-end";
            } 
        ?>
    </div>
</footer>

<script src="../../libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body><!-- 防止 ERR_CACHE_MISS -->
<script>window.history.replaceState(null, null, window.location.href);</script>
</html>