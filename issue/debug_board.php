<!-- 尾段：deBug訊息 -->
<hr>
<div class="row px-2 block" id="debug">
    <div class="col-12 mb-0 bg-white rounded">
        <div style="font-size: 8px;">
            <?php 
                if(isset($step)){
                    echo $step ? ">>> 表單身分：".$step."</br>" : "";
                }
                if(isset($issue_row['idty'])){
                    echo ">>> idty:".$issue_row['idty']." ";
                    switch($issue_row['idty']){
                        case "0" : echo '<span class="badge rounded-pill bg-warning text-dark">待領</span>'; break;
                        case "1" : echo '<span class="badge rounded-pill bg-danger">待簽</span>'; break;
                        case "2" : echo "退件"; break;
                        case "3" : echo "取消"; break;
                        case "10": echo "結案"; break;
                        case "11": echo "轉PR"; break;
                        case "12": echo '<span class="badge rounded-pill bg-success">待收</span>'; break;
                        default  : echo "na"; break; }
                    echo !empty($issue_row['in_sign']) ? " / wait: ".$issue_row['in_sign']." " :"";
                    echo !empty($issue_row['flow']) ? " / flow: ".$issue_row['flow']." " :"";
                    echo "</br>";
                    echo "<hr>";
                }

                echo "<pre>";
                    if($_REQUEST){
                        echo ">>> _REQUEST：</br>";
                        print_r($_REQUEST);
                        echo "<hr>";
                    }
                    if(isset($issue_row)){
                        echo ">>> issue_row</br>";
                        print_r($issue_row);
                    }
                    if(isset($issues)){
                        echo ">>> issues</br>";
                        print_r($issues);
                    }
                echo "</pre>text-end";
            ?>
        </div>
    </div>
</div>