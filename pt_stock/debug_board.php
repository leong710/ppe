<!-- 尾段：deBug訊息 -->
<hr>
<div class="row px-2 block" id="debug">
    <div class="col-12 mb-0 bg-white rounded">
        <div style="font-size: 8px;">
            <?php 
                // echo $step ? ">>> 表單身分：".$step."</br>" : "";
                // if(isset($trade_row['idty'])){
                //     echo ">>> idty:".$trade_row['idty']." ";
                //     switch($trade_row['idty']){
                //         case "0" : echo '<span class="badge rounded-pill bg-warning text-dark">待領</span>'; break;
                //         case "1" : echo '<span class="badge rounded-pill bg-danger">待簽</span>'; break;
                //         case "2" : echo "退件"; break;
                //         case "3" : echo "取消"; break;
                //         case "10": echo "結案"; break;
                //         case "11": echo "轉PR"; break;
                //         case "12": echo '<span class="badge rounded-pill bg-success">待收</span>'; break;
                //         default  : echo "na"; break; }
                //     echo !empty($trade_row['in_sign']) ? " / wait: ".$trade_row['in_sign']." " :"";
                //     echo !empty($trade_row['flow']) ? " / flow: ".$trade_row['flow']." " :"";
                //     echo "</br>";
                //     echo "<hr>";
                // }

                echo "<pre>";
                    if($_REQUEST){
                        echo ">>> _REQUEST：</br>";
                        print_r($_REQUEST);
                        echo "<hr>";
                    }
                    if($stocks){
                        echo ">>> stocks</br>";
                        print_r($stocks);
                    }
                    if($myReceives){
                        echo ">>> myReceives</br>";
                        print_r($myReceives);
                    }
                echo "</pre>text-end";
            ?>
        </div>
    </div>
</div>