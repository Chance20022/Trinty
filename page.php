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

    $sql = "SELECT * FROM uploaddata WHERE id = $id";
    $result = mysqli_query($linkBD, $sql);
    $row = mysqli_fetch_array($result);

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
                    <a class="LogoText SmallAU" href="index.php">3D MODELS</a>
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
                            <div class="dopImagesUploadText"><a class="download" href='uploads/<?php echo $row['pathFile']; ?>'>Download</a></div>
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
                    <span class="textRash">Model Extension: <?php echo $row['ExtensionFiles']; ?></span>
                </div>
                <div class="descriptonPage">
                    <span class="DescriptionTitle">Short description</span>
                </div>
                <div class="text">
                    <?php echo $row['DescriptionText'];?>
                </div>
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
</body>
</html>