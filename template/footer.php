<footer class="text-center text-white">
    <div class="mb-3">
        copyright &copy; 2023 Design by INX tnESH.cop<br>
        <!-- <br>system owner：陳建良 (42117)  -->
    </div>
    <?php if(isset($_REQUEST["debug"])){
        echo "<div class='text-start text-white'><pre>";
        print_r($_SESSION[$sys_id]);
        echo "</pre></div>";
    }?>
</footer>

<script src="../../libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body><!-- 防止 ERR_CACHE_MISS -->
<script>window.history.replaceState(null, null, window.location.href);</script>
</html>