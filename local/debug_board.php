<!-- 尾段：deBug訊息 -->
<hr>
<div class="row px-2 block" id="debug">
    <div class="col-12 mb-0 bg-white rounded">
        <div style="font-size: 8px;">
            <?php 

                // if(!empty($stock_cata_SN) && isset($_POST["local_id"])){
                //     foreach($stock_cata_SN AS $row){
                //         echo "</br>".$_POST["local_id"]." = ".$row["cata_SN"];
                //     }
                // }

                echo "<pre>";
                    // if($_REQUEST){
                    //     echo ">>> _REQUEST：</br>";
                    //     print_r($_REQUEST);
                    //     echo "<hr>";
                    // }
                    // if($stock_cata_SN){
                    //     echo ">>> stock_cata_SN</br>";
                    //     print_r($stock_cata_SN);
                    // }
                    if($myReceives){
                        echo ">>> myReceives</br>";
                        // print_r($myReceives);

                        foreach($myReceives AS $row){
                            $csa = (array)json_decode($row['cata_SN_amount']);
                            // $csa_keys = array_keys($csa);   
                            // foreach($csa_keys AS $key){
                            //     echo "<br>key: ".$key."<br>";       
                            // }
                            echo "<pre>";
                            print_r($csa);
                            echo "</pre>";
                            
                            // $key = key($csa);
                            // $value = current($csa);
                            // echo "<br>key: ".$key."<br>";       
                            // print_r($value);       
                         
                        }

                    }
                        
                echo "</pre>text-end";
                        

            ?>
        </div>
    </div>
</div>