<?php    
    //Подключение к БД
    $linkBD = mysqli_connect("localhost", "root", "root", "userdata");

    if ($linkBD == false){
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    }
        
    //Кодировка
    mysqli_query($linkBD, "SET NAMES 'utf8'");

    $sql = "SELECT * FROM uploaddata ORDER BY id DESC";
    $result = mysqli_query($linkBD, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $test1 = 20/(1/4);
    $test2 = (2/4);
    $test3 = (3/4);
    echo $test1."<br>".$test2."<br>".$test3."<br>";
?>