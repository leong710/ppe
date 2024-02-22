<?php
    // 定義一個名為 "Person" 的類別
    class Person {
        // 屬性（成員變數）
        public $name;
        public $age;

        // 方法（成員函數）
        public function sayHello() {
            echo "Hello, my name is " . $this->name . " and I am " . $this->age . " years old.";
        }
    }

    // 創建一個 "Person" 類別的實例（對象）
    $person1 = new Person();

    // 設定屬性的值
    $person1->name = "John";
    $person1->age = 30;

    // 調用方法
    $person1->sayHello();

