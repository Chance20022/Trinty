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
                    <a class="LogoText SmallAU" href="index.php">Аккаунт</a>
                </a>
            </div>
            <div class="UserActionsAU">
                <div class="ImageSIAU"><img src="images/logo/Sign in.png" alt="Картинка авторизации"></div>
                <div class="SigninAU">
                    <span class="authoAU">Вход</span>
                </div>
            </div>
        </div>
    </header>
    <content>
        <div class="WindowAutho">
            <div class="MainText">
                <?php if($_GET['autho'] == "Authorizaton") : ?>
                    <span class="MainTextSetting">Авторизация</span>
                <?php else : ?>
                    <span class="MainTextSetting">Регистрация</span>
                <?php endif ?>
            </div>
            <div class="getDataUser">
                <?php if($_GET['autho'] == "Authorizaton") : ?>
                        <div class="LoginData">
                            <div class="LoginUser">
                                <span>Логин/Почта</span>
                            </div>
                            <div class="LoginText">
                                <input id="LoginAutho" type="text" name="Login">
                            </div>
                        </div>
                        <div class="PasswordnData">
                            <div class="LoginUser">
                                <span>Пароль</span>
                            </div>
                            <div class="PasswordText">
                                <input id="PasswordAutho" type="password" name="Password">
                            </div>
                            <div class="forgetPassword">
                                <a href="#" class="forgotA">Забыли пароль?</a>
                            </div>
                        </div>
                        <div class="ButtonPushData">
                            <input onclick="ajaxAU()" type="button" value="Войти">
                        </div>
                <?php else : ?>
                        <div class="EmailData">
                            <div class="EmailUser">
                                <span>Почта</span>
                            </div>
                            <div class="EmailText">
                                <input id="EmailT" type="text" name="Email">
                            </div>
                        </div>
                        <div class="LoginDataReg">
                            <div class="LoginUser">
                                <span>Логин</span>
                            </div>
                            <div class="LoginText">
                                <input id="LoginT" type="text" name="Login">
                            </div>
                        </div>
                        <div class="PasswordnData">
                            <div class="LoginUser">
                                <span>Пароль</span>
                            </div>
                            <div class="PasswordText">
                                <input id="PasswordT" type="password" name="Password">
                            </div>
                        </div>
                        <div class="ButtonPushData">
                            <input onclick="ajaxREG()" type="button" value="Далее">
                        </div>
                <?php endif ?>
            </div>
        </div>
        <div class="accessEmail">
            <div class="InfoTextEmail">
                <?php if($_GET['autho'] == 'Authorizaton') : ?>
                Вам на почту выслано письмо с кодом для предотвращения взлома. Для продолжения дальнейшей авторизации введите код.
                <?php else :?>
                Вам на почту выслано письмо с кодом для подтверждения вашей почты. Для продолжения дальнейшей регистрации введите код.
                <?php endif ?>
            </div>
            <div class="placeCode">
                <input type="text" id="codeUser">
            </div>
            <button onclick="checkCode()" class="buttonCode">Подтвердить</button>
        </div>
    </content>
</body>

<script>
    $('.accessEmail').hide();
    function checkCode(){
        var code = document.querySelector("#codeUser").value;
        if('<?php echo $_GET['autho']; ?>' == 'Authorizaton') var email = document.querySelector("#LoginAutho").value;
        else var email = document.querySelector("#EmailT").value;

        if(code != ""){
            $.ajax({
                url: "/api/API.php",
                method: 'post',
                dataType: 'json',
                data: {method: 'regCode', code: code, email: email},
                success: function(data){
                    if(data['access'] == true){
                        $(location).attr('href', 'index.php');
                    }
                    else alert("Не верный код");
                }
            });
        }
        else alert("Вы ничего не ввели");
    }

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
                        $('.accessEmail').show();
                    }
                    else if(data['ffsd'] == true){
                        $(location).attr('href', 'rbwlnvlweviweowebwg.php');
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
                        $('.accessEmail').show();
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