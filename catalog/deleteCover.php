<?php

    extract($_REQUEST);
    unlink($img);
    header("refresh:0;url=create.php?cover=1#cover");