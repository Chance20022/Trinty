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

    $sql = "SELECT * FROM uploaddata WHERE";

    if(empty($_GET['id'])){$id = 1;}
    else $id = (int)$_GET['id'];

    // Узнаю последний айди, чтобы не выгружать всю бд, а только нужную часть
    $maxID = "SELECT max(id) FROM uploaddata";
    $resultMaxNum = mysqli_query($linkBD, $maxID);
    $rowsMax = mysqli_fetch_all($resultMaxNum, MYSQLI_ASSOC);
    $maxID = (int)$rowsMax[0]['max(id)'] - (28 * ($id-1)) + 1;
    $sql .= " id < $maxID";

    $pages; // количество страниц учитывая число публикаций в бд
    $countPagesBD = "SELECT COUNT(id) FROM uploaddata";
    $res = mysqli_query($linkBD, $countPagesBD);
    $countPages = mysqli_fetch_array($res);
    if(((int)$countPages[0] % 28) != 0) $pages = intdiv((int)$countPages[0], 28) + 1;
    else $pages = (int)$countPages[0] / 28;
    if($pages == 0) $pages = 1;

    //получение последние 28 побликаций
    if(isset($_GET['search'])) {
        $searchRequest = $_GET['search'];
        $sql .= " AND `MainText` LIKE '%$searchRequest%' ";
    }

    if(isset($_GET['filters'])){
        // ExtensionFiles LIKE '%.stl%' OR ExtensionFiles LIKE '%.blend%'
        $filters = $_GET['filters'];
        $filtersMas = [];
        $filtersMasCounter = 0;
        $t = "";
        for($i = 0; $i < strlen($filters); $i++){
            if($filters[$i] != ',') $t .= $filters[$i];
            else {$filtersMas[$filtersMasCounter] = $t; $t = ""; $filtersMasCounter++;}
        }
        // вставка в sql зарос филтров
        if(count($filtersMas) != 1){
            $sql .= " AND (ExtensionFiles LIKE '%$filtersMas[0]%'";
            for($i = 1; $i < count($filtersMas); $i++){
                $sql.= " OR ExtensionFiles LIKE '%$filtersMas[$i]%'";
            }
            $sql .= ")";
        }
        else{
            $sql .= " AND (ExtensionFiles LIKE '%$filtersMas[0]%')";
        }
    }

    if(isset($_GET['DESC']) && empty($_GET['pop'])) $sql .= ' LIMIT 28';
    else if(empty($_GET['DESC']) && empty($_GET['pop'])) $sql .= ' ORDER BY id DESC LIMIT 28';
    else if(empty($_GET['DESC']) && isset($_GET['pop'])) $sql .= ' ORDER BY Watched DESC LIMIT 28';
    else if(isset($_GET['DESC']) && isset($_GET['pop'])) $sql .= ' ORDER BY id DESC LIMIT 28';

    //echo $sql;
    $result = mysqli_query($linkBD, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
    <?php if(isset($_SESSION['login'])) : ?>
    <div class="reporting">
        Связь с нами
    </div>
    <div class="reportDivPush">
        <div class="textReporting">
            Цель (коротко)
        </div>
        <div>
            <input class="korotko" type="text" id='inputReport'>
        </div>
        <div class="textReporting">
            Напишите развёрнуто
        </div>
        <div><textarea class="textareaReportClass" id='textareaReport' rows="10"></textarea></div>
        <div class="reportButton">
            <button onclick="closeReport()">Отмена</button>
            <button onclick="pushReport()">Отправить</button>
        </div>
    </div>
    <script>
        $('.reportDivPush').hide();
        $('.reporting').click(function(){
            $('.reportDivPush').show();
        });

        function pushReport(){
            var title = $('#inputReport').val();
            var text = $('#textareaReport').val();
            if(title!=""){
                if(text!=""){
                    $.ajax({
                        url: 'API/API.php',
                        method: 'post',
                        dataType: 'json',
                        data: {method: "pushReport", title: title, text: text, login: "<?php echo $_SESSION['login']; ?>"},
                        success: function(data){
                            if(data['access'] == true) {alert("Отправлено"); $('.reportDivPush').hide();}
                        }
                    });
                }
                else alert("Напишите развёрнуто вашу цель ниже");
            }
            else alert("Напишите коротко в чём цель");
        }

        function closeReport(){
            $('.reportDivPush').hide();
        }
    </script>
    <?php endif ?>
    <header>
        <div class="UpHeader">
            <div class="Logo">
                <a class="LogoText" href="index.php">Trinty 
                    <a class="LogoText Small" href="index.php">3D МОДЕЛИ</a>
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
                                <a class="Registr" href="authorization.php?autho=Registration">Регистрации</a>
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
        <div class="Filters">
            <ul class="Filters Cells">
                <li onclick="newOldF()" class='mainPos' id='date'>
                    Дата
                    <?php if(isset($_GET['DESC'])) :?><span class="ChangePlus">-</span><?php else : ?><span class="ChangePlus">+</span><?php endif ?>
                    <div class='filterChangerDate'>
                        <span>+ новые / - старые</span>
                    </div>
                    <script>
                        $('.filterChangerDate').hide();
                        $('#date').mouseenter(function(){
                            $('.filterChangerDate').show();
                        });
                        $('#date').mouseleave(function(){
                            $('.filterChangerDate').hide();
                        });

                        function newOldF(){
                            <?php if(isset($_GET['DESC'])) : ?>
                                var url = "index.php?id=1";
                                <?php if(isset($_GET['search'])) :?>
                                    url += "&search=<?php echo $searchRequest; ?>";
                                <?php endif ?>
                                <?php if(isset($_GET['filters'])) :?>
                                    url += '&filters=' + "<?php echo $_GET['filters']; ?>";
                                <?php endif ?>
                            
                                $(location).attr('href', url);
                            <?php else : ?>
                                var url = "index.php?id=1";
                                <?php if(isset($_GET['search'])) :?>
                                    url += "&search=<?php echo $searchRequest; ?>";
                                <?php endif ?>
                                <?php if(isset($_GET['filters'])) :?>
                                    url += '&filters=' + "<?php echo $_GET['filters']; ?>";
                                <?php endif ?>
                                url += '&DESC=' + 'true';
                            
                                $(location).attr('href', url);
                            <?php endif ?>
                        }
                    </script>
                </li>
                <li class='mainPos' id='rashi'>
                    Расширения <span class="ChangePlus">+</span>
                    <div class='filterChangerRashi'>
                        <div><input class="test" type="checkbox" value="stl"> stl</div>
                        <div><input class="test" type="checkbox" value="ply">ply</div>
                        <div><input class="test" type="checkbox" value="blend">blend</div>
                        <div><input class="test" type="checkbox" value="obj">obj</div>
                        <div><input class="test" type="checkbox" value="bhv">bhv</div>
                        <div><input class="test" type="checkbox" value="x3d">x3d</div>
                        <div><input class="test" type="checkbox" value="dae">dae</div>
                        <div><input class="test" type="checkbox" value="fbx">fbx</div>
                        <div><input class="test" type="checkbox" value="abc">abc</div>
                        <div><input class="test" type="checkbox" value="wrl">wrl</div>
                        <div><input class="test" type="checkbox" value="pdf">pdf</div>
                        <div><input class="test" type="checkbox" value="gl">gl</div>
                        <div><input class="test" type="checkbox" value="usd">usd</div>
                        <div><input class="test" type="checkbox" value="svg">svg</div>
                        <button onclick="getFilterSettingsCheckBox()" class="acceptButtonFilters">Применить</button>
                    </div>
                    <script>
                        var checkerBox = [];
                        for(var i = 0; i < 14; i++) checkerBox.push(false);
                        $('.filterChangerRashi').hide();
                        $('#rashi').mouseenter(function(){
                            $('.filterChangerRashi').show();
                        });
                        $('.filterChangerRashi').mouseleave(function(){
                            $('.filterChangerRashi').hide();
                        });

                        function getFilterSettingsCheckBox(){
                            var checkBox = ['stl', 'ply', 'blend', 'obj', 'bhv', 'x3d', 'dae', 'fbx', 'abc', 'wrl', 'pdf', 'gl', 'usd', 'svg'];
                            var checkBoxTF = [];
                            $('.test').each(function(){
                                if($(this).prop('checked') == true) checkBoxTF.push(true);
                                else checkBoxTF.push(false);
                            });
                            var push = "";
                            for(var i = 0; i < checkBoxTF.length; i++){
                                if(checkBoxTF[i] == true) push += '.' + checkBox[i] + ',';
                            }
                            var url = "index.php?id=1";
                            <?php if(isset($_GET['search'])) :?>
                                url += "&search=<?php echo $searchRequest; ?>";
                            <?php endif ?>
                            url += '&filters=' + push;
                        
                            $(location).attr('href', url);
                            
                        }
                    </script>
                </li>
                <li onclick="popular()" class='mainPos' id='popular'>
                    Популярные <?php if(empty($_GET['pop'])) :?><span class="ChangePlus">+</span><?php else : ?><span class="ChangePlus">-</span><?php endif ?>
                    <script>
                        function popular(){
                            <?php if(isset($_GET['pop'])) :?>
                                var url = "index.php?id=1";
                                <?php if(isset($_GET['search'])) :?>
                                    url += "&search=<?php echo $searchRequest; ?>";
                                <?php endif ?>
                                <?php if(isset($_GET['filters'])) :?>
                                    url += '&filters' + "<?php echo $_GET['filters']; ?>";
                                <?php endif ?>
                                <?php if(isset($_GET['DESC'])) :?>
                                    url += '&pop=' + '<?php echo $_GET['DESC']; ?>';
                                <?php endif ?>
                            
                                $(location).attr('href', url);
                            <?php else : ?>
                                var url = "index.php?id=1";
                                <?php if(isset($_GET['search'])) :?>
                                    url += "&search=<?php echo $searchRequest; ?>";
                                <?php endif ?>
                                <?php if(isset($_GET['filters'])) :?>
                                    url += '&filters' + "<?php echo $_GET['filters']; ?>";
                                <?php endif ?>
                                <?php if(isset($_GET['DESC'])) :?>
                                    url += '&pop=' + '<?php echo $_GET['DESC']; ?>';
                                <?php endif ?>
                                url += '&pop=' + 'true';
                            
                                $(location).attr('href', url);
                            <?php endif ?>
                        }
                    </script>
                </li>
            </ul>
        </div>
        <script>
        </script>
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
        <?php else :?>
        <div class="notFound">
            <span class="textNotFound"><?php echo $searchRequest; ?> не найдено</span>
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
                    <div class="cellNumberPages">
                        <?php echo $i+1; ?>
                    </div>
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