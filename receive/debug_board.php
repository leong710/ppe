<!-- 尾段：deBug訊息 -->
<hr>
<div class="row px-2 block" id="debug">
    <div class="col-12 mb-0 bg-white rounded">
        <div style="font-size: 8px;">
            <?php 
                echo $step ? ">>> 表單身分：".$step."</br>" : "";
                if(isset($receive_row['idty'])){
                    echo ">>> idty:".$receive_row['idty']." ";
                    switch($receive_row['idty']){
                        case "0" : echo '<span class="badge rounded-pill bg-warning text-dark">待領</span>'; break;
                        case "1" : echo '<span class="badge rounded-pill bg-danger">待簽</span>'; break;
                        case "2" : echo "退件"; break;
                        case "3" : echo "取消"; break;
                        case "10": echo "結案"; break;
                        case "11": echo "轉PR"; break;
                        case "12": echo '<span class="badge rounded-pill bg-success">待收</span>'; break;
                        default  : echo "na"; break; }
                    echo !empty($receive_row['in_sign']) ? " / wait: ".$receive_row['in_sign']." " :"";
                    echo !empty($receive_row['flow']) ? " / flow: ".$receive_row['flow']." " :"";
                    echo "</br>";
                    echo "<hr>";
                }

                echo "<pre>";
                    if($_REQUEST){
                        echo ">>> _REQUEST：</br>";
                        print_r($_REQUEST);
                        echo "<hr>";
                    }
                    if($receive_row){
                        echo ">>> receive_row</br>";
                        print_r($receive_row);
                    }
                echo "</pre>text-end";
            ?>
        </div>
    </div>
</div>