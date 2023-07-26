<?php

    extract($_REQUEST);
    unlink($img);
    header("refresh:0;url=edit.php?id=$id&cover=1#cover");