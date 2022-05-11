<?php 
    session_start();

    $linkBD = mysqli_connect('localhost', 'root', 'root', 'userdata');

    if ($linkBD == false){
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    }
    //Кодировка
    mysqli_query($linkBD, "SET NAMES 'utf8'");

    if(isset($_GET['id'])) $id = $_GET['id'];
    else $id = 0;
    
    // Отправка данных о помещении
    $sql = "SELECT Watched FROM uploaddata WHERE id = $id";
    $result = mysqli_query($linkBD,$sql);
    $watch = mysqli_fetch_array($result);
    $watch = (int)$watch['Watched'];
    $watch++;
    $sql = "UPDATE uploaddata SET Watched = $watch WHERE id = $id";
    mysqli_query($linkBD,$sql);

    // Остальное
    $sql = "SELECT * FROM uploaddata WHERE id = $id";
    $result = mysqli_query($linkBD, $sql);
    $row = mysqli_fetch_array($result);

    // Комментарии от пользователей
    $sqlCom = "SELECT * FROM commentsuser WHERE idPage = '$id' ORDER BY id DESC";
    $resultCom = mysqli_query($linkBD, $sqlCom);
    $comRow = mysqli_fetch_all($resultCom, MYSQLI_ASSOC);

    // Для окошка комметария
    $l = $_SESSION['login'];
    $sql = "SELECT * FROM commentsuser WHERE loginUser = '$l' AND idPage = '$id'";
    $result = mysqli_query($linkBD, $sql);
    $rowCheck = mysqli_fetch_array($result);

    //$rows[$i]['pathImage'];
    //$rows[$i]['ExtensionFiles'];
    //$rows[$i]['ExtensionIMG'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/fonts.css" type="text/css">
    <script src="js/jquery-3.6.0.js"></script>
</head>
<body>
    <header>
        <div class="UpHeaderAU">
            <div class="LogoAU">
                <a class="LogoTextAU" href="index.php">Trinty 
                    <a class="LogoText SmallAU" href="index.php">3D МОДЕЛИ</a>
                </a>
            </div>
            <div class="SearchRequest">
                <form action="index.php" method="GET">
                    <?php if(isset($_GET['search'])) :?>
                        <input class="SearchPlace" type="text" name='search' value="<?php echo $_GET['search'];?>">
                    <?php else :?>
                        <input class="SearchPlace" type="text" name='search'>
                    <?php endif ?>
                    <input class="SearchButton" type="submit" value="Поиск">
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
                                <div class="buttonAccount">
                                    <a href="account.php">Аккаунт</a>
                                </div>
                                <div class="buttonLeaveAccount">
                                    <span class="leaveAccount">Выход</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </header>
    <content>
        <div class="wrapper-1">
            <div class='UserNamePage'>
                <a href="account.php?login=<?php echo $row['loginUser'];?>">
                    <span class="UserNameTextPage"> <?php echo $row['loginUser']; ?></span>
                </a>
            </div>
                <div class='nameOfModelPage'>
                    <span class="MainTextUser"><?php echo $row['MainText'];?></span>
                </div>
                <div class='uploadImages'>
                    <div class="mainImagePage">
                        <img src="uploads/<?php echo $row['MainImg'];?>" alt="">
                    </div>
                    <div class="uploadDopImages">
                        <div class="buttonUploadImages">
                            <div class="dopImagesUploadText"><a class="download" href='uploads/<?php echo $row['pathFile']; ?>'>Загрузить</a></div>
                        </div>
                        <div class="dopPhoto">
                            <div class='lineDopPhoto'>
                                <div class='ptrnPhoto' id='one'></div>
                                <div class='ptrnPhoto' id='two'></div>
                            </div>
                            <div class='lineDopPhoto'>
                                <div class='ptrnPhoto' id='three'></div>
                                <div class='ptrnPhoto' id='four'></div>
                            </div>
                            <div class='lineDopPhoto'>
                                <div class='ptrnPhoto' id='five'></div>
                                <div class='ptrnPhoto' id='six'></div>
                            </div>
                            <div class='lineDopPhoto'>
                                <div class='ptrnPhoto' id='seven'></div>
                                <div class='ptrnPhoto' id='eight'></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="placeRash">
                    <span class="textRash">Расширение: <?php echo $row['ExtensionFiles']; ?></span>
                </div>
                <div class="descriptonPage">
                    <span class="DescriptionTitle">Описание</span>
                </div>
                <div class="text">
                    <?php echo $row['DescriptionText'];?>
                </div>
                <?php if($rowCheck['loginUser'] == NULL) :?>
                <div class="otzivi">
                    <div class="titleO">
                        <div>
                            <span class="titleOtz">Отзывы <span class="titleOtzDiscr">Всего отзывов: <?php echo count($comRow); ?> (c/o: <?php echo $row['Reviews'];?>)</span></span>
                            <div class="descripTitleOtz">Вы можете оставить свой комментарий:</div>
                        </div>
                        <div class="lineStars">
                            <img class="stars" id="star1" src="images/logo/starNoActive.png" alt="Первая звезда оценки">
                            <img class="stars" id="star2" src="images/logo/starNoActive.png" alt="Вторая звезда оценки">
                            <img class="stars" id="star3" src="images/logo/starNoActive.png" alt="Третья звезда оценки">
                            <img class="stars" id="star4" src="images/logo/starNoActive.png" alt="Четвёртая звезда оценки">
                            <img class="stars" id="star5" src="images/logo/starNoActive.png" alt="Пятая звезда оценки">
                        </div>
                    </div>
                    <textarea class="reviewsPlace" maxlength="4000" rows="10" placeholder="Помните о человеческом отношении друг к другу!"></textarea>
                    <button onclick="pushReview()" class="pushReviws">Оставить отзыв</button>
                </div>
                <?php else : ?>
                    <div class="otzivi">
                    <div class="titleO">
                        <div>
                            <span class="titleOtz">Отзывы <span class="titleOtzDiscr">Всего отзывов: <?php echo count($comRow); ?> (c/o: <?php echo $row['Reviews'];?>)</span></span>
                            <div class="descripTitleOtz">Вы можете отредактировать свой комментарий:</div>
                        </div>
                        <?php switch($rowCheck['Reviews']) :
                        case"1": ?>
                            <div class="lineStars">
                                <img class="stars" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                <img class="stars" id="star2" src="images/logo/starNoActive.png" alt="Вторая звезда оценки">
                                <img class="stars" id="star3" src="images/logo/starNoActive.png" alt="Третья звезда оценки">
                                <img class="stars" id="star4" src="images/logo/starNoActive.png" alt="Четвёртая звезда оценки">
                                <img class="stars" id="star5" src="images/logo/starNoActive.png" alt="Пятая звезда оценки">
                            </div>
                            <?php break; ?>
                            <?php case"2": ?>
                            <div class="lineStars">
                                <img class="stars" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                <img class="stars" id="star2" src="images/logo/starActive.png" alt="Вторая звезда оценки">
                                <img class="stars" id="star3" src="images/logo/starNoActive.png" alt="Третья звезда оценки">
                                <img class="stars" id="star4" src="images/logo/starNoActive.png" alt="Четвёртая звезда оценки">
                                <img class="stars" id="star5" src="images/logo/starNoActive.png" alt="Пятая звезда оценки">
                            </div>
                            <?php break; ?>
                            <?php case"3": ?>
                            <div class="lineStars">
                                <img class="stars" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                <img class="stars" id="star2" src="images/logo/starActive.png" alt="Вторая звезда оценки">
                                <img class="stars" id="star3" src="images/logo/starActive.png" alt="Третья звезда оценки">
                                <img class="stars" id="star4" src="images/logo/starNoActive.png" alt="Четвёртая звезда оценки">
                                <img class="stars" id="star5" src="images/logo/starNoActive.png" alt="Пятая звезда оценки">
                            </div>
                            <?php break; ?>
                            <?php case"4": ?>
                            <div class="lineStars">
                                <img class="stars" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                <img class="stars" id="star2" src="images/logo/starActive.png" alt="Вторая звезда оценки">
                                <img class="stars" id="star3" src="images/logo/starActive.png" alt="Третья звезда оценки">
                                <img class="stars" id="star4" src="images/logo/starActive.png" alt="Четвёртая звезда оценки">
                                <img class="stars" id="star5" src="images/logo/starNoActive.png" alt="Пятая звезда оценки">
                            </div>
                            <?php break; ?>
                            <?php case"5": ?>
                            <div class="lineStars">
                                <img class="stars" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                <img class="stars" id="star2" src="images/logo/starActive.png" alt="Вторая звезда оценки">
                                <img class="stars" id="star3" src="images/logo/starActive.png" alt="Третья звезда оценки">
                                <img class="stars" id="star4" src="images/logo/starActive.png" alt="Четвёртая звезда оценки">
                                <img class="stars" id="star5" src="images/logo/starActive.png" alt="Пятая звезда оценки">
                            </div>
                            <?php break; ?>
                        <?php endswitch; ?>
                    </div>
                    <textarea class="reviewsPlace" maxlength="4000" rows="10"><?php echo $rowCheck['comment'];?></textarea>
                    <div class="choiceUserToRevie">
                        <button onclick="deleteReview()" class="EditReviws">Удалить отзыв</button>
                        <button onclick="editReview()" class="EditReviws">Редактировать отзыв</button>
                    </div>
                </div>
                <?php endif ?>
                <?php if(count($comRow) == 0) :?>
                    <div class="emptyRev">
                        Комментарии отсутсвуют
                    </div>
                <?php else :?>
                    <?php if($row['Comments'] != '0') :?>
                        <?php foreach($comRow as $crow) : ?>
                            <div class="coment">
                                <div class="titleUserComent">
                                    <div>
                                    <span></span><?php echo $crow['loginUser'];?></span>
                                    <span><?php echo $crow['DateP'];?></span>
                                    </div>
                                    <?php switch($crow['Reviews']) :
                                    case"1": ?>
                                    <div class="lineStarsCom">
                                        <img class="starsCom" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                        <img class="starsCom" id="star2" src="images/logo/starNoActive.png" alt="Вторая звезда оценки">
                                        <img class="starsCom" id="star3" src="images/logo/starNoActive.png" alt="Третья звезда оценки">
                                        <img class="starsCom" id="star4" src="images/logo/starNoActive.png" alt="Четвёртая звезда оценки">
                                        <img class="starsCom" id="star5" src="images/logo/starNoActive.png" alt="Пятая звезда оценки">
                                    </div>
                                    <?php break; ?>
                                    <?php case"2": ?>
                                    <div class="lineStarsCom">
                                        <img class="starsCom" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                        <img class="starsCom" id="star2" src="images/logo/starActive.png" alt="Вторая звезда оценки">
                                        <img class="starsCom" id="star3" src="images/logo/starNoActive.png" alt="Третья звезда оценки">
                                        <img class="starsCom" id="star4" src="images/logo/starNoActive.png" alt="Четвёртая звезда оценки">
                                        <img class="starsCom" id="star5" src="images/logo/starNoActive.png" alt="Пятая звезда оценки">
                                    </div>
                                    <?php break; ?>
                                    <?php case"3": ?>
                                    <div class="lineStarsCom">
                                        <img class="starsCom" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                        <img class="starsCom" id="star2" src="images/logo/starActive.png" alt="Вторая звезда оценки">
                                        <img class="starsCom" id="star3" src="images/logo/starActive.png" alt="Третья звезда оценки">
                                        <img class="starsCom" id="star4" src="images/logo/starNoActive.png" alt="Четвёртая звезда оценки">
                                        <img class="starsCom" id="star5" src="images/logo/starNoActive.png" alt="Пятая звезда оценки">
                                    </div>
                                    <?php break; ?>
                                    <?php case"4": ?>
                                    <div class="lineStarsCom">
                                        <img class="starsCom" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                        <img class="starsCom" id="star2" src="images/logo/starActive.png" alt="Вторая звезда оценки">
                                        <img class="starsCom" id="star3" src="images/logo/starActive.png" alt="Третья звезда оценки">
                                        <img class="starsCom" id="star4" src="images/logo/starActive.png" alt="Четвёртая звезда оценки">
                                        <img class="starsCom" id="star5" src="images/logo/starNoActive.png" alt="Пятая звезда оценки">
                                    </div>
                                    <?php break; ?>
                                    <?php case"5": ?>
                                    <div class="lineStarsCom">
                                        <img class="starsCom" id="star1" src="images/logo/starActive.png" alt="Первая звезда оценки">
                                        <img class="starsCom" id="star2" src="images/logo/starActive.png" alt="Вторая звезда оценки">
                                        <img class="starsCom" id="star3" src="images/logo/starActive.png" alt="Третья звезда оценки">
                                        <img class="starsCom" id="star4" src="images/logo/starActive.png" alt="Четвёртая звезда оценки">
                                        <img class="starsCom" id="star5" src="images/logo/starActive.png" alt="Пятая звезда оценки">
                                    </div>
                                    <?php break; ?>
                                    <?php endswitch; ?>
                                </div>
                                <div class="comentUserYes">
                                    <?php echo $crow['comment'];?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php else :?>
                        <div class="emptyRev">
                            Комментарии отсутсвуют
                        </div>
                    <?php endif ?>
                <?php endif ?>
        </div>
    </content>
    <script>
        function changeMainPicter(id){
            if($(id + ' img').length == 0){alert("You clicked on an empty cell");} // Проверка на пустую ячейку, где нет изображения
            else{
                $(".mainImagePage img").remove();
                $(".mainImagePage").append('<img src="/'+$(id+' img').attr('src') +'">');
            }
        }

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

            // Проверка на то, какое изображение выбрал пользователь в качестве главного
            $('#one').click(function(){
                changeMainPicter('#one');
            });
            $('#two').click(function(){
                changeMainPicter('#two');
            });
            $('#three').click(function(){
                changeMainPicter('#three');
            });
            $('#four').click(function(){
                changeMainPicter('#four');
            });
            $('#five').click(function(){
                changeMainPicter('#five');
            });
            $('#six').click(function(){
                changeMainPicter('#six');
            });
            $('#seven').click(function(){
                changeMainPicter('#seven');
            });
            $('#eight').click(function(){
                changeMainPicter('#eight');
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
    <script>
        function convertDataFromBD(data){
            var masData = [];
            var tempStr = "";
            var counter = 0;
            for(var i = 0; i < data.length; i++){
                if(i + 1 == data.length) {tempStr += data[i]; masData.push(tempStr);}
                else if(data[i] != ",") tempStr += data[i];
                else {masData.push(tempStr); tempStr = "";}
            }
            return masData;
        }

        var images = convertDataFromBD("<?php echo $row['pathImage'];?>");
        var tagCount = ['#one', '#two', '#three', '#four', '#five', '#six', '#seven', '#eight'];
        var counterImg = 0;
        for(var i = images.length-1; i > -1; i--){
            $(tagCount[counterImg]).append('<img src="uploads/'+images[i]+'">');
            counterImg++;
        }
    </script>
    <script>
        var passive = 'images/logo/starNoActive.png';
        var active = 'images/logo/starActive.png';
        <?php if($rowCheck['Reviews'] == NULL) :?>
            var counterStars = 0;
        <?php else :?>
            var counterStars = <?php echo $rowCheck['Reviews'];?>;
        <?php endif ?>
        // Код для ценки
        $('#star1').mouseenter(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', passive);
            $('#star3').attr('src', passive);
            $('#star4').attr('src', passive);
            $('#star5').attr('src', passive);
        });
        $('#star1').click(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', passive);
            $('#star3').attr('src', passive);
            $('#star4').attr('src', passive);
            $('#star5').attr('src', passive);
            counterStars = 1;
        });

        // 2
        $('#star2').mouseenter(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', active);
            $('#star3').attr('src', passive);
            $('#star4').attr('src', passive);
            $('#star5').attr('src', passive);
        });
        $('#star2').click(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', active);
            $('#star3').attr('src', passive);
            $('#star4').attr('src', passive);
            $('#star5').attr('src', passive);
            counterStars = 2;
        });
        // 3
        $('#star3').mouseenter(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', active);
            $('#star3').attr('src', active);
            $('#star4').attr('src', passive);
            $('#star5').attr('src', passive);
        });
        $('#star3').click(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', active);
            $('#star3').attr('src', active);
            $('#star4').attr('src', passive);
            $('#star5').attr('src', passive);
            counterStars = 3;
        });

        //4
        $('#star4').mouseenter(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', active);
            $('#star3').attr('src', active);
            $('#star4').attr('src', active);
            $('#star5').attr('src', passive);
        });
        $('#star4').click(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', active);
            $('#star3').attr('src', active);
            $('#star4').attr('src', active);
            $('#star5').attr('src', passive);
            counterStars = 4;
        });

        //5
        $('#star5').mouseenter(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', active);
            $('#star3').attr('src', active);
            $('#star4').attr('src', active);
            $('#star5').attr('src', active);
        });
        $('#star5').click(function(){
            $('#star1').attr('src', active);
            $('#star2').attr('src', active);
            $('#star3').attr('src', active);
            $('#star4').attr('src', active);
            $('#star5').attr('src', active);
            counterStars = 5;
        });
    </script>
    <script>
        function pushReview(){
            if(counterStars >= 1 || counterStars <=5){
                var rev = $('.reviewsPlace').val();
                if(rev != ""){
                    $.ajax({
                        url: '/api/API.php',
                        method: 'post',
                        dataType: 'json',
                        data: {method: 'review', Reviews: counterStars, Comments: rev, id: <?php echo $_GET['id']; ?>, loginUser: "<?php echo $_SESSION['login'];?>"},
                        success: function(data){
                            $(location).attr('href', 'page.php?id=<?php echo $_GET['id'];?>');
                        }
                    });
                }
                else alert("Вам необходимо что-то написать для отправки комментария");
            }
            else alert("Оцените работу, чтобы оставить комментарий");
        }

        function deleteReview(){
            if(counterStars >= 1 || counterStars <=5){
                var rev = $('.reviewsPlace').val();
                if(rev != ""){
                    $.ajax({
                        url: '/api/API.php',
                        method: 'post',
                        dataType: 'json',
                        data: {method: 'deleteCom', Reviews: counterStars, RevOld: "<?php echo $row['Reviews']; ?>" , Comments: rev, id: <?php echo $_GET['id']; ?>, loginUser: "<?php echo $_SESSION['login'];?>"},
                        success: function(data){
                            $(location).attr('href', 'page.php?id=<?php echo $_GET['id'];?>');
                        }
                    });
                }
                else alert("Вам необходимо что-то написать для отправки комментария");
            }
            else alert("Оцените работу, чтобы оставить комментарий");
        }

        function editReview(){
            if(counterStars >= 1 || counterStars <=5){
                var rev = $('.reviewsPlace').val();
                if(rev != ""){
                    $.ajax({
                        url: '/api/API.php',
                        method: 'post',
                        dataType: 'json',
                        data: {method: 'editCom', Reviews: counterStars, RevOld: "<?php echo $row['Reviews']; ?>", Comments: rev, id: <?php echo $_GET['id']; ?>, loginUser: "<?php echo $_SESSION['login'];?>"},
                        success: function(data){
                            $(location).attr('href', 'page.php?id=<?php echo $_GET['id'];?>');
                        }
                    });
                }
                else alert("Вам необходимо что-то написать для отправки комментария");
            }
            else alert("Оцените работу, чтобы оставить комментарий");
        }
    </script>
</body>
</html>