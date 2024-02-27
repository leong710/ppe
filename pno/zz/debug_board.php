<!-- 尾段：deBug訊息 -->
<hr>
<div class="row px-2 block" id="debug">
    <div class="col-12 mb-0 bg-white rounded">
        <div style="font-size: 8px;">
            <?php 
                echo "<pre>";
                    if($_REQUEST){
                        echo ">>> _REQUEST：</br>";
                        print_r($_REQUEST);
                        echo "<hr>";
                    }
                    if($pnos){
                        echo ">>> pnos</br>";
                        print_r($pnos);
                    }
                echo "</pre>text-end";
            ?>
        </div>
    </div>
</div>