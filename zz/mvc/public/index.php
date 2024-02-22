<?php
    // public/index.php

    // Autoload classes using Composer or your own autoloader

    // Initialize your application

        // 設定應用程式目錄為當前目錄
        define('APP_PATH', __DIR__.'/../');

        // 開啟除錯模式
        define('APP_DEBUG', true);

        // 載入框架
        require(APP_PATH.'app/App.php');

    $app = new App();

    // Dispatch the request
    $app->handleRequest();
