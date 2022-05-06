<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/fonts.css" type="text/css">
    <script src="js/jquery-3.6.0.js"></script>
    <title>Trinty</title>
</head>

<body>
    <header>
        <div class="UpHeaderAU">
            <div class="LogoAU">
                <a class="LogoTextAU" href="index.php">Trinty 
                    <a class="LogoText SmallAU" href="index.php">ACCOUNT</a>
                </a>
            </div>
            <div class="UserActionsAU">
                <div class="ImageSIAU"><img src="images/logo/Sign in.png" alt="Картинка авторизации"></div>
                <div class="SigninAU">
                    <span class="authoAU">+Sign in</span>
                </div>
            </div>
        </div>
    </header>
    <content>
        <div class="WindowAutho">
            <div class="MainText">
                <?php if($_GET['autho'] == "Authorizaton") : ?>
                    <span class="MainTextSetting">Authorization</span>
                <?php else : ?>
                    <span class="MainTextSetting">Registration</span>
                <?php endif ?>
            </div>
            <div class="getDataUser">
                <?php if($_GET['autho'] == "Authorizaton") : ?>
                    <form action="authorization.php" method="post">
                        <div class="LoginData">
                            <div class="LoginUser">
                                <span>Login/Email</span>
                            </div>
                            <div class="LoginText">
                                <input id="LoginAutho" type="text" name="Login">
                            </div>
                        </div>
                        <div class="PasswordnData">
                            <div class="LoginUser">
                                <span>Password</span>
                            </div>
                            <div class="PasswordText">
                                <input id="PasswordAutho" type="password" name="Password">
                            </div>
                        </div>
                        <div class="ButtonPushData">
                            <input onclick="ajaxAU()" type="button" value="Enter">
                        </div>
                    </form>
                <?php else : ?>
                    <form action="authorization.php" method="post">
                        <div class="EmailData">
                            <div class="EmailUser">
                                <span>Email</span>
                            </div>
                            <div class="EmailText">
                                <input id="EmailT" type="text" name="Email">
                            </div>
                        </div>
                        <div class="LoginDataReg">
                            <div class="LoginUser">
                                <span>Login</span>
                            </div>
                            <div class="LoginText">
                                <input id="LoginT" type="text" name="Login">
                            </div>
                        </div>
                        <div class="PasswordnData">
                            <div class="LoginUser">
                                <span>Password</span>
                            </div>
                            <div class="PasswordText">
                                <input id="PasswordT" type="password" name="Password">
                            </div>
                        </div>
                        <div class="ButtonPushData">
                            <input onclick="ajaxREG()" type="button" value="Enter">
                        </div>
                    </form>
                <?php endif ?>
            </div>
        </div>

        <?php if($_GET['autho'] == "Authorizaton") : ?>

        <?php else : ?>

        <?php endif ?>
    </content>
</body>

<script>
    function ajaxAU(){
        var login = document.querySelector('#LoginAutho').value;
        var password = document.querySelector('#PasswordAutho').value;
        var error = "";

        if(login == ''){
            error = 'Please, enter your login';
        }
        else if(login.length < 3){
            error = 'Please, enter more then 2 letters';
        }

        if(password == ''){
            error = "Please, enter password";
        }

        if(error == ""){
            $.ajax({
                // оптравка данных на сервер
                url: '/api/API.php',       /* Куда пойдет запрос */
                method: 'post',            /* Метод передачи (post или get) */
                dataType: 'json',          /* Тип данных в ответе (xml, json, script, html). */
                data: {method: 'authorization', login: login, password: password},     /* Параметры передаваемые в запросе. */
                success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
                    if(data['autho'] == true){
                        $(location).attr('href', 'index.php');
                    }
                    else{
                        alert(data['result']);
                    }
                }
            });
        }
        else alert(error);
    }

    function ajaxREG(){
        var login = document.querySelector('#LoginT').value;
        var password = document.querySelector('#PasswordT').value;
        var email = document.querySelector('#EmailT').value;
        var error = "";

        if(login == ''){
            error = 'Please, enter your login';
        }
        else if(login.length < 3){
            error = 'Please, enter more then 2 letters';
        }

        if(password == ''){
            error = "Please, enter your password";
        }
        else if(password.length < 6){
            error = "Your password must contain more than 6 characters";
        }

        if(email == ''){
            error = "Please, enter your email";
        }

        if(error == ""){
            $.ajax({
                // оптравка данных на сервер
                url: '/api/API.php',       /* Куда пойдет запрос */
                method: 'post',            /* Метод передачи (post или get) */
                dataType: 'json',          /* Тип данных в ответе (xml, json, script, html). */
                data: {method: 'registration', login: login, password: password, email: email},     /* Параметры передаваемые в запросе. */
                success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
                    if(data['reg'] == true){
                        $(location).attr('href', 'authorization.php?autho=Authorizaton');
                    }
                    else{
                        alert(data['result']);
                    }
                }
            });
        }
        else alert(error);
    }
</script>

</html>