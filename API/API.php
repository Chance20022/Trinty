<?php
   session_start();

    //Подключение к БД
    $linkBD = mysqli_connect("localhost", "root", "root", "userdata");

    if ($linkBD == false){
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    }

    //Кодировка
    mysqli_query($linkBD, "SET NAMES 'utf8'");

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if($_POST['method'] == 'authorization'){
        $login = $_POST['login'];
        $password = $_POST['password'];
        $error = "";
        $rowSQL;

        $hash = md5($password);

        $sql = "SELECT * FROM `authouser`";
        $result = mysqli_query($linkBD, $sql);

        // поиск пользователя по логину или паролю
        while($row = mysqli_fetch_array($result)){
            if($row['loginUser'] == $login) {
                $error = "";
                $rowSQL = $row;
                break;
            }
            else $error = "This user does not exist";
        }

        if($error == ""){
            if($rowSQL['passwordUser'] == $hash){
                if(!isset($_SESSION['login'])){
                    $_SESSION['login'] = $login;
                    $_SESSION['password'] = $hash; // Да, а нужно что-то ещё?
                }
                $arr = ['autho'=>true, 'result'=>"All ok"];
                $json = json_encode($arr);
                echo $json;
            }
            else{
                $arr = ['autho'=>false, 'result'=>"Wrong password"];
                $json = json_encode($arr);
                echo $json;
            }
        }
        else{
            $arr = ['autho'=>false, 'result'=>"$error"];
            $json = json_encode($arr);
            echo $json;
        }
    }

    if($_POST['method'] == 'registration'){
        $login = $_POST['login'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $error = "";

        $hash = md5($password); // Хеширование пароля

        // проверка на доступность регистрации данных введённых пользователем
        $sqlLogin = "SELECT loginUser FROM `authouser` WHERE loginUser = '$login'";
        $sqlEmail = "SELECT email FROM `authouser` WHERE email = '$email'";
        $resultLogin = mysqli_query($linkBD, $sqlLogin);
        $resultEmail = mysqli_query($linkBD, $sqlEmail);

        while($rowLogin = mysqli_fetch_array($resultLogin)){
            if($rowLogin['loginUser'] == $login){
                $error = 'This login is being used by another user';
                break;
            }
        }

        if($error == ""){
            while($rowEmail = mysqli_fetch_array($resultEmail)){
                if($rowEmail['email'] == $email){
                    $error = 'This email is being used by another user';
                    break;
                }
            }
        }

        if($error == ""){
            $sql = "INSERT INTO authouser (id, email, loginUser, passwordUser) VALUES (NULL, '$email', '$login', '$hash');";

            $result = mysqli_query($linkBD, $sql);

            if ($result == false) {
                $arr = ['reg'=>false, 'result'=>'Error while trying to write to the database'];
                $json = json_encode($arr);
                echo $json;
            }
            else{
                $arr = ['reg'=>true, 'result'=>'All good'];
                $json = json_encode($arr);
                echo $json;
            }
        }
        else{
            $arr = ['reg'=>false, 'result'=>"$error"];
            $json = json_encode($arr);
            echo $json;
        }
    }

    if($_POST['method'] == 'leaveAccount'){
        unset($_SESSION['login']);
        unset($_SESSION['password']);

        $arr = ['leave'=>true];
        $json = json_encode($arr);
        echo $json;
    }

    function newName(){
        // 97 - 122 // 48 - 57

        // определение сколько будет символов в названии
        $length = rand(6,16);
        $name = "";

        for($i = 0; $i < $length; $i++){
            $r = rand(0,1);
            if($r == 0){ // буква
                $letter = chr(rand(97,122));
                $name .= $letter;
            }
            else{ // цифра
                $num = chr(rand(48,57));
                $name .= $num;
            }
        }

        return $name;
    }

    function convertingData($str){
        $masData = [];
        $masCount = 0;
        for($i = 0; $i < strlen($str); $i++){
            if($str[$i] != ":"){
                $masData[$masCount] .= $str[$i];
            }
            else $masCount++;
        }

        return $masData;
    }

    function convertingForBD($data){
        $fileBD = "";
        for($i = 0; $i < count($data); $i++){
            if($i == 0){$fileBD .= $data[$i];}
            else{$fileBD .= ",".$data[$i];}
        }
        return $fileBD;
    }

    if($_POST['method'] == 'pushData'){
        $mainText = $_POST['mainText']; // Главный текст
        $textArea = $_POST['textArea']; // текст описание
        $extensionFiles = $_POST['extensionFiles']; // расширение
        $extensionIMG = $_POST['extensionIMG'];
        $mainImg = $_POST['mainImg']; // Главная картинка, превью
        $login = $_POST['login'];
        $img = $_POST['img']; // пути ко временным изображениям для переноса
        $masFiles = $_POST['masFiles']; // название файла во временном хранилище

        // Преобразование данных 

        $extensionIMG = convertingData($extensionIMG);
        $extensionFiles = convertingData($extensionFiles);
        $img = convertingData($img);
        $masFiles = convertingData($masFiles);

        for($i = 0; $i < count($masFiles); $i++){
            $tempStr = "";
            for($z = 0; $z < strlen($masFiles[$i]); $z++){
                if($masFiles[$i][$z] == " ") $tempStr .= '-';
                else $tempStr .= $masFiles[$i][$z];
            }
            $masFiles[$i] = $tempStr;
            $tempStr = "";
        }

        for($i = 0; $i < count($img); $i++){
            $tempStr = "";
            for($z = 0; $z < strlen($img[$i]); $z++){
                if($img[$i][$z] == " ") $tempStr .= '-';
                else $tempStr .= $img[$i][$z];
            }
            $img[$i] = $tempStr;
            $tempStr = "";
        }

        // Перемещение изображений из временного хранилища в постоянное
        for($i = 0; $i < count($img); $i++){
            while(1){
                $newname = newName();
                $sql = "SELECT pathImage FROM uploaddata WHERE pathImage = '$newname'"; // Если такое есть, то нужно искать новое имя
                $result = mysqli_query($linkBD, $sql);
                $row = mysqli_fetch_array($result);
                if($row['pathImage'] != "../uploads/$newname".$extensionIMG[$i]){
                    if("/".$img[$i] == $mainImg){
                        rename("../".$img[$i],"../uploads/$newname".$extensionIMG[$i]);
                        $mainImg = $newname.$extensionIMG[$i];
                        $img[$i] = "$newname".$extensionIMG[$i];
                        break;
                    }
                    else{
                        rename("../".$img[$i],"../uploads/$newname".$extensionIMG[$i]);
                        $img[$i] = "$newname".$extensionIMG[$i];
                        break;
                    }
                }
                else continue;
            }
        }

        // перемещение файлов из временного хранилища
        for($i = 0; $i < count($masFiles); $i++){
            while(1){
                $newname = newName();
                $sql = "SELECT pathFile FROM uploaddata WHERE pathFile = '$newname'"; // Если такое есть, то нужно искать новое имя
                $result = mysqli_query($linkBD, $sql);
                $row = mysqli_fetch_array($result);
                if($row['pathFile'] != "../uploads/$newname".$extensionFiles[$i]){
                    rename("../temp/files/".$masFiles[$i], "../uploads/$newname".$extensionFiles[$i]);
                    $masFiles[$i] = "$newname".$extensionFiles[$i];
                    break;
                }
                else continue;
            }
        }

        // новое имя для архива файлов загруженные пользователем
        while(1){
            $newname = newName();
            $sql = "SELECT pathFile FROM uploaddata WHERE pathFile = '$newname'"; // Если такое есть, то нужно искать новое имя
            $result = mysqli_query($linkBD, $sql);
            $row = mysqli_fetch_array($result);
            if($row['pathFile'] != $newname.".zip") break;
            else continue;
        }
        // добавление документов в архив
        $zip = new ZipArchive();
        $zip->open("../uploads/$newname.zip", ZIPARCHIVE::CREATE);
        for($i = 0; $i < count($masFiles); $i++){
            $zip->addFile("../uploads/".$masFiles[$i]);
        }
        $zip->close();

        //Удаление файлов которые были добавлены в архив
        for($i = 0; $i < count($masFiles); $i++){
            unlink("../uploads/$masFiles[$i]");
        }

        $imgBD = convertingForBD($img);
        //$fileBD = convertingForBD($masFiles);
        $ExtensionFiles = convertingForBD($extensionFiles);
        $ExtensionIMG = convertingForBD($extensionIMG);

        $today = date("Y-m-d H:i:s");

        //echo $login."<br>".$imgBD."<br>".$fileBD."<br>".$today."<br>".$mainText."<br>".$ExtensionFiles."<br>".$ExtensionIMG."<br>".$mainImg."<br>";

        $sql = "INSERT INTO uploaddata (id, loginUser, pathImage, pathFile, timeUploading, DescriptionText, MainText, MainImg, ExtensionFiles, ExtensionIMG, Watched, Comments, Reviews, pay) VALUES (NULL, '$login', '$imgBD', '$newname.zip', '$today', '$textArea', '$mainText', '$mainImg', '$ExtensionFiles', '$ExtensionIMG', '0', '0', '0', '0');";
        $result = mysqli_query($linkBD, $sql);
        if(!$result){
            echo "Ошибка в записи в бд";
        }

        $arr = ['access'=>true];
        $json = json_encode($arr);
        echo $json;
    }

?>