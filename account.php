<?php 
    session_start();
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
        <div class="UpHeader">
            <div class="Logo">
                <a class="LogoText" href="index.php">Trinty 
                    <a class="LogoText Small" href="index.php">ACCOUNT</a>
                </a>
            </div>
            <div class="SearchRequest">
                <form action="index.php" method="get">
                    <input class="SearchPlace" type="text">
                    <input class="SearchButton" type="button" value="Search">
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
        <div class='wrapperAcc'>
            <div class="userDataAccount">

            </div>
            <div class="publicationUserAcc">
                
            </div>
        </div>
    </content>
    <Script>
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