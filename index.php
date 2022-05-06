<?php
    session_start();
    require 'functions.php';
    //Подключение к БД
    $linkBD = mysqli_connect("localhost", "root", "root", "userdata");

    if ($linkBD == false){
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    }

    //Кодировка
    mysqli_query($linkBD, "SET NAMES 'utf8'");

    if(isset($_GET['search'])){$searchRequest = $_GET['search'];}

    if(empty($_GET['id'])){$id = 1;}
    else $id = (int)$_GET['id'];

    // Узнаю последний айди, чтобы не выгружать всю бд, а только нужную часть
    $maxID = "SELECT max(id) FROM uploaddata";
    $resultMaxNum = mysqli_query($linkBD, $maxID);
    $rowsMax = mysqli_fetch_all($resultMaxNum, MYSQLI_ASSOC);
    $maxID = (int)$rowsMax[0]['max(id)'] - (28 * ($id-1)) + 1;

    $pages; // количество страниц учитывая число публикаций в бд
    if(((int)$rowsMax[0]['max(id)'] % 30) != 0) $pages = intdiv((int)$rowsMax[0]['max(id)'], 28) + 1;
    else $pages = (int)$rowsMax[0]['max(id)'] / 28;
    if($pages == 0) $pages = 1;

    //получение последние 12 побликаций
    if(isset($_GET['search'])) {
        $sql = "SELECT * FROM uploaddata WHERE `id` < $maxID AND `MainText` LIKE '%$searchRequest%' ORDER BY id DESC LIMIT 28";
        $result = mysqli_query($linkBD, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    else {
        $sql = "SELECT * FROM uploaddata WHERE id < $maxID ORDER BY id DESC LIMIT 30";
        $result = mysqli_query($linkBD, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Преобразование данных из бд
    for($i = 0; $i < count($rows); $i++){
        $rows[$i]['pathImage'] = convertDataFromBD($rows[$i]['pathImage']);
        $rows[$i]['ExtensionFiles'] = convertDataFromBD($rows[$i]['ExtensionFiles']);
        $rows[$i]['ExtensionIMG'] = convertDataFromBD($rows[$i]['ExtensionIMG']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <title>Trinty</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/fonts.css" type="text/css">
    <script src="js/jquery-3.6.0.js"></script>
</head>
<body>
    <header>
        <div class="UpHeader">
            <div class="Logo">
                <a class="LogoText" href="index.php">Trinty 
                    <a class="LogoText Small" href="index.php">3D MODELS</a>
                </a>
            </div>
            <div class="SearchRequest">
                <form action="index.php" method="GET">
                    <?php if(isset($_GET['search'])) :?>
                        <input class="SearchPlace" type="text" name='search' value="<?php echo $_GET['search'];?>">
                    <?php else :?>
                        <input class="SearchPlace" type="text" name='search'>
                    <?php endif ?>
                    <input class="SearchButton" type="submit" value="Search">
                </form>
            </div>
            <div class="UserActions">
                <?php if(!isset($_SESSION['login'])) :?>
                    <div><a href="authorization.php?autho=Authorizaton">+Upload</a></div>
                <?php else : ?>
                    <div><a href="uploadPage.php">+Upload</a></div>
                <?php endif ?>
                <?php if(!isset($_SESSION['login'])) :?>
                    <div class="ImageSI"><img src="images/logo/Sign in.png" alt="Картинка авторизации"></div>
                    <div class="Signin">
                        <span class="autho">+Sign in</span>    
                        <div class="Authorization">
                            <div class="choiceUserAutho">
                                <a class="Autho" href="authorization.php?autho=Authorizaton">Sign in</a>
                                <a class="Registr" href="authorization.php?autho=Registration">Registration</a>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="ImageUser"><img src="images/logo/Sign in.png" alt="Картинка авторизации"></div>
                    <div class="MenuUser">
                        <span class="UserName"><?php echo $_SESSION['login']; ?> </span>    
                        <div class="SetMenu">
                            <div class="choiceUserMenu">
                                <div class="buttonAccount">
                                    <a href="account.php">Account</a>
                                </div>
                                <div class="buttonLeaveAccount">
                                    <span class="leaveAccount">Sign out</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <div class="Filters">
            <ul class="Filters Cells">
                <li>Date<span class="ChangePlus">+</span></li>
                <li>Extension<span class="ChangePlus">+</span></li>
                <li>Cost<span class="ChangePlus">+</span></li>
            </ul>
        </div>
    </header>
    <content>
        <?php if(count($rows) != 0) :?>
        <div class="wrapper-c">
                <div class="items">
                <?php foreach($rows as $row) : ?>
                    <div class="Cell">

                            <div class="mainText">
                                <a class="link" href="page.php?id=<?php echo $row['id']; ?>">
                                    <h3><?php echo $row['MainText']; ?></h3>
                                </a>
                            </div>
                            <div class="MainImage">
                                <a class="link" href="page.php?id=<?php echo $row['id']; ?>">
                                    <img src="uploads/<?php echo $row['MainImg']; ?>">
                                </a>
                            </div>
                            <div class="UserInfo">
                                <a class="link" href="account.php?login=<?php echo $row['loginUser'] ?>">
                                    <span><?php echo $row['loginUser']; ?></span>
                                </a>
                                <div class="Statistics">
                                    <img src="images/logo/" alt="">
                                    <span><?php echo $row['Comments']; ?></span>
                                    <img src="images/logo/" alt="">
                                    <span><?php echo $row['Reviews']; ?></span>
                                    <img src="images/logo/" alt="">
                                    <span><?php echo $row['Watched']; ?></span>
                                </div>
                            </div>
                        
                    </div>
                <?php endforeach ?>
            </div>
        </div>
        <?php else :?>
        <div class="notFound">
            <span class="textNotFound"><?php echo $searchRequest; ?> not found</span>
        </div>
        <?php endif ?>
    </content>
    <footer>
        <?php if(count($rows) != 0) : ?>
        <div class="NumberPages">
            <?php for($i = 0; $i < $pages; $i++) : ?>
                <a href="index.php?<?php 
                if(isset($_GET['search'])){
                    $tempID = $i+1;
                    $tempID = (String)$tempID;
                    echo "id=".$tempID."&search=$searchRequest";
                }
                else{
                    $tempID = $i+1;
                    $tempID = (String)$tempID;
                    echo "id=".$tempID;
                }
                ?>">
                    <?php echo $i+1; ?>
                </a>
            <?php endfor ?>
        </div>
        <?php else : ?>
        <?php endif ?>
    </footer>
    <script>
        $('.SetMenu').hide();
        $('.Authorization').hide();
        $(document).ready(function(){
            // Переключатели видимости с авторизацией
            $(".autho").mouseenter(function(){
                $('.Authorization').fadeIn(100);
            });
            $(".ImageSI").mouseenter(function(){
                $('.Authorization').fadeIn(100);
            });
            $('.Authorization').mouseleave(function(){
                $('.Authorization').fadeOut(200);
            });
            $('content').click(function(){
                $('.Authorization').fadeOut(200);
            });
            $('.Filters').click(function(){
                $('.Authorization').fadeOut(200);
            });

            //Переключение видимости панели зарегистрированного пользователя
            $('.UserName').mouseenter(function(){
                $('.SetMenu').fadeIn(100);
            });
            $('.ImageUser').mouseenter(function(){
                $('.SetMenu').fadeIn(100);
            });
            $('.SetMenu').mouseleave(function(){
                $('.SetMenu').fadeOut(200);
            });
            $('content').click(function(){
                $('.SetMenu').fadeOut(200);
            });
            $('.Filters').click(function(){
                $('.SetMenu').fadeOut(200);
            });
            $('.Cell').mouseenter(function(){
                $(this).css('box-shadow', '0 0 10px rgba(0,0,0,0.5)');
            });
            $('.Cell').mouseleave(function(){
                $(this).css('box-shadow', '0 0 10px rgba(0,0,0,0.3)');
            });

            //Выход из аккаунта
            $(".leaveAccount").click(function(){
                $.ajax({
                // оптравка данных на сервер
                url: '/api/API.php',       /* Куда пойдет запрос */
                method: 'post',            /* Метод передачи (post или get) */
                dataType: 'json',          /* Тип данных в ответе (xml, json, script, html). */
                data: {method: 'leaveAccount'},     /* Параметры передаваемые в запросе. */
                success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
                    if(data['leave'] == true){
                        $(location).attr('href', 'index.php');
                    }
                }
                });
            });
        }); 
    </script>
</body>
</html>