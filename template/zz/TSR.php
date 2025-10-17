    <!-- 測試權限 -->
    <hr>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-3 bg-light border rounded p-3 bs-b text-center">
                <form action="../template/zz/sys_role.php" method="get">
                    <div class="input-group">
                        <span class="input-group-text">測試</span>
                        <select name="role" id="role" class="form-select" onchange="submit()">
                            <option for="role" hidden selected>-- 調整role權限 --</option>
                            <?php 
                                $vr_role = [
                                    "0"   => "管理員",
                                    "1"   => "大PM/總窗護理師",
                                    "2"   => "廠區窗口",
                                    "2.2" => "廠-業務工安",
                                    "2.5" => "一般ESH工安",
                                    "3"   => "現場窗口",
                                    "3.5" => "unknow"
                                ];
                                foreach($vr_role as $val => $rem) {
                                    echo "<option for='role' value='{$val}'>{$val} {$rem}</option>";
                                } 
                            ?>
                        </select>
                        <input type="hidden" name="sys_id" value="<?=$sys_id?>">
                    </div>
                </form>  
                <hr>
                <button type="button" class="btn btn-sm btn-xs btn-success" onclick="clrSession()">clrSession</button>
            </div>
        </div>
    </div>