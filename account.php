<?php 
    session_start();

    $linkBD = mysqli_connect('localhost', 'root', 'root', 'userdata');
    if(!$linkBD) print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    mysqli_query($linkBD, "SET NAMES 'utf8'");

    $checker = false;
    if(isset($_GET['login'])){
        if($_GET['login'] == $_SESSION['login']) {$login = $_GET['login'];}
        else{
            $login = $_GET['login'];
            $checker = true;
        }
    }
    else {$login = $_SESSION['login'];}

    // Получение публикаций пользователя
    $sql = "SELECT * FROM uploaddata WHERE loginUser = '$login' ORDER BY id DESC";
    $result = mysqli_query($linkBD, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $result = mysqli_query($linkBD, $sql);
    // Выявление статистики пользователя
    $watch = 0; // Колличество просмотренных со всех работ
    $rating = 0; // Средний рейтинг по работам
    $countPublication = 0;
    while($row = mysqli_fetch_array($result)){
        $watch += (int)$row['Watched'];

        if((int)$row['Reviews'] != 0){
            if($rating == 0) $rating = (int)$row['Reviews'];
            else $rating = ($rating + (int)$row['Reviews'])/2;
        }
        $countPublication++;
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
    <title>Account</title>
</head>
<body>
    <header>
        <div id="toTop"> ^ Наверх </div>
        <div class="UpHeader">
            <div class="Logo">
                <a class="LogoText" href="index.php">Trinty 
                    <a class="LogoText Small" href="index.php">АККАУНТ</a>
                </a>
            </div>
            <div class="SearchRequest">
                <form action="index.php" method="get">
                    <input class="SearchPlace" type="text">
                    <input class="SearchButton" type="button" value="Поиск">
                </form>
            </div>
            <div class="UserActions">
            <?php if(!isset($_SESSION['login'])) :?>
                    <div><a href="authorization.php?autho=Authorizaton">+Загрузить</a></div>
                <?php else : ?>
                    <div><a href="uploadPage.php">+Загрузить</a></div>
                <?php endif ?>
                <?php if(!isset($_SESSION['login'])) :?>
                    <div class="ImageSI"><img src="images/logo/Sign in.png" alt="Картинка авторизации"></div>
                    <div class="Signin">
                        <span class="autho">+Вход</span>
                        <div class="Authorization">
                            <div class="choiceUserAutho">
                                <a class="Autho" href="authorization.php?autho=Authorizaton">Вход</a>
                                <a class="Registr" href="authorization.php?autho=Registration">Регистрация</a>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="ImageUser"><img src="images/logo/Sign in.png" alt="Картинка авторизации"></div>
                    <div class="MenuUser">
                        <span class="UserName"><?php echo $_SESSION['login']; ?> </span>    
                        <div class="SetMenu">
                            <div class="choiceUserMenu">
                                <span class="leaveAccount">Выход</span>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </header>
    <content>
        <div class='wrapperAcc'>
            <div class="userDataAccount">
                <?php if($checker) : ?>
                <div class="InfoAboutStatisticks">
                    <h1><?php echo $login;?></h1>
                </div>
                <?php else : ?>
                <div class="InfoAboutStatisticks">
                    <h1><?php echo $_SESSION['login'];?></h1>
                    <h3>Доброго времени суток!</h3>
                </div>
                <?php endif ?>
                <div class="statisticsACC">
                    <div class="statsText">
                        <div class="statsT firststatsT">
                            Публикаций <br>
                        </div>
                        <div class="statsT secondstatsT">
                            Средняя оценка <br>
                        </div>
                        <div class="statsT thirdstatsT">
                            Просмотров <br>
                        </div>
                    </div>
                    <div class="statsInfo">
                        <div class="statsI firststatsT">
                            <?php echo $countPublication; ?>
                        </div>
                        <div class="statsI secondstatsT">
                            <?php echo $rating; ?>
                        </div>
                        <div class="statsI thirdstatsT">
                            <?php echo $watch ?>
                        </div>
                    </div>
                </div>
                <?php if($checker) :?>
                <?php else : ?>
                <button class="buttonLichDan">Личные данные</button>
                <?php endif ?>
            </div>
            <div class="publicationUserAcc">
                <?php if($checker) : ?>
                <div class="titleTextAcc">
                    <h1>Публикации пользователя:</h1>
                </div>
                <?php else : ?>
                    <div class="titleTextAcc">
                        <h1>Ваши публикации:</h1>
                    </div>
                <?php endif ?>
                <div class="wrapper-c">
                    <div class="items">
                        <?php foreach($rows as $row) : ?>
                            <a href="page.php?id=<?php echo $row['id'];?>"></a>
                            <div class='CellAcc'>
                                <div class="mainImageAcc">
                                    <a href="page.php?id=<?php echo $row['id'];?>">
                                        <img src="uploads/<?php echo $row['MainImg'];?>" alt="">
                                    </a>
                                </div>
                                <div class="InformationAcc">    
                                    <div class="titleAccCell">
                                        <a class="mainTitleTextAcc" href="page.php?id=<?php echo $row['id'];?>">
                                            <h2><?php echo $row['MainText'];?></h2>
                                        </a>
                                        <?php if(!$checker) :?>
                                        <div class="optionPubAcc">
                                            <a onclick="editPub(<?php echo $row['id']; ?>)">Редактировать</a>
                                            <a onclick="deletePub(<?php echo $row['id']; ?>)">Удалить</a>
                                        </div>
                                        <?php endif ?>
                                    </div>
                                    <div class="StatisticsAcc">
                                        <img class="statisticsImgMainPage" src="images/logo/coments.png" alt="">
                                        <span><?php echo $row['Comments']; ?></span>
                                        <img class="statisticsImgMainPageStar" src="images/logo/starActive.png" alt="">
                                        <span><?php echo $row['Reviews']; ?></span>
                                        <img class="statisticsImgMainPageWatch" src="images/logo/watch.png" alt="">
                                        <span><?php echo $row['Watched']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </content>
    <script>
        $('.SetMenu').hide();
        $(document).ready(function(){
            $('.CellAcc').mouseenter(function(){
                $(this).css('box-shadow', '0 0 10px rgba(0,0,0,0.5)');
            });
            $('.CellAcc').mouseleave(function(){
                $(this).css('box-shadow', '0 0 10px rgba(0,0,0,0.3)');
            });
            // Переключатели видимости с авторизацией
            $(".MenuUser").mouseenter(function(){
                $('.SetMenu').fadeIn(100);
            });
            $(".ImageUser").mouseenter(function(){
                $('.SetMenu').fadeIn(100);
            });
            $('.SetMenu').mouseleave(function(){
                $('.SetMenu').fadeOut(200);
            });
            $('content').click(function(){
                $('.SetMenu').fadeOut(200);
            });
            $('.buttonLichDan').mouseenter(function(){
                $('.buttonLichDan').css('background-color', '#7474fb');
            });
            $('.buttonLichDan').mouseleave(function(){
                $('.buttonLichDan').css('background-color', '#4c4cb3');
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
        $(function(){
            $(window).scroll(function() {
                if($(this).scrollTop() != 0) {$('#toTop').fadeIn();} 
                else {$('#toTop').fadeOut();}
            });
            $('#toTop').click(function() {$('body,html').animate({scrollTop:0},800);});
        });
    </script>
    <script>
        function deletePub(id){
            $.ajax({
                url: '/api/API.php',
                method: 'post',
                dataType: 'json',
                data: {method: 'deletePublicationUser', id: id},
                success: function(data){
                    if(data['access'] == true){
                        $(location).attr('href', 'account.php');
                    }
                }
            });
        }

        function editPub(id){
            $(location).attr('href', "edit.php?id="+id);
        }
    </script>
</body>
</html>