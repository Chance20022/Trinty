<?php 
    session_start();
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
                    <a class="LogoText SmallAU" href="index.php">UPLOAD</a>
                </a>
            </div>
            <div class="UserActionsAU">
                <div class="ImageSIAU"><img src="images/logo/Sign in.png" alt="Картинка авторизации"></div>
                <div class="SigninAU">
                    <span class="authoAU"> <?php echo $_SESSION['login']; ?> </span>
                </div>
            </div>
        </div>
    </header>
    <content>
        <div class="wrapper-1">
            <div class='UserNameUpload'>
                <span class="UserNameTextUoload"> <?php echo $_SESSION['login']; ?></span>
            </div>
            <form enctype="multipart/form-data" action="uploadPage.php" method="POST" id='mainForm'>
                <div class='nameOfModel'>
                    <input type="text" name="mainText" id="mainText">
                    <span class="HintHeader">(Main title)</span>
                </div>
                <div class='uploadImages'>
                    <div class="mainImageUpload">
                        <div class="field__file-button">Main image</div>
                    </div>
                    <div class="uploadDopImages">
                        <div class="buttonUploadImages">
                            <input onchange="loadingIMG(this.files)" type="file" name='dopImages' id='file2' class='dopImagesUpload' multiple>
                            <label for="file2" class='dopImagesUploadWrapper'>
                                <div class="dopImagesUploadText">Upload images</div>
                            </label>
                            <span class="descripUploadTx">(No more than 8 pic)</span>
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
                    <span class="textRash">Model Extension:</span>
                    <span class="descriptinDesctiprion">(.stl, .txt, .ply, .blend, .obj, .bhv, .x3d, .dae, .fbx, .abc, .wrl, .pdf, .gl, .usd, .svg, .psd)</span>
                    <br>
                    <input onchange="loadingFiles(this.files)" type="file" name='files' id='file3' class='fileUpload' multiple>
                    <label for="file3" class='fileUploadWrapper'>
                        <div class="fileUploadText">Upload files</div>
                    </label>
                    <span class="descripFileUploadTx">(No more 200M)</span>
                </div>
                <div class="descripton">
                    <span class="ShortDescriptionTitle">Short description</span><br>
                    <textarea id="textArea" name="descriptionText" maxlength="1500" cols="30" rows="10" class="tired"></textarea>
                </div>
            </form>
            <button class="UploadButton">Upload</button>
        </div>
    </content>
    <script>
        function loadingFiles(files){
            var extension = [".stl", ".txt", ".ply",".obj",".bhv",".x3d",".blend",".dae",".fbx",".abc",".wrl",".pdf",".gl",".usd",".svg",".psd"];
            var masSize = [];
            var typeFiles = [];
            var error = "";
            var switcher = true; // Для выявления ошибок

            if(files.length > 10){
                error="You uploaded more than 10 files";
                alert(error);
            }
            else{
                for(var i = 0; i < files.length; i++){

                    // Проверка на расширение
                    // Так как type не показывает расширения нужные, придётся этим заняться самому
                    let typeFile = files[i].name;
                    typeFile = typeFile.match(/\.\w+/gi);

                    for(let z = 0; z < extension.length; z++){
                        if(typeFile[typeFile.length-1] == extension[z]){switcher = true; typeFiles[i] = typeFile[typeFile.length-1]; break;}
                        else { switcher = false; error = "The extension is not supported. Available extensions: stl, ply, obj, bhv, x3d, blend, dae, fbx, abc, wrl, pdf, gl, usd, svg, psd";}
                    }

                    // Запись размера каждого файла
                    if(switcher){
                        masSize.push(files[i].size);
                    }
                    else {alert(error); break;}
                }
                // проверка на файлы. Сумма всех файлов не должна превышать 200мб
                let sumSize = 0;
                for(var i = 0; i < masSize.length; i++){sumSize += masSize[i];}
                if(sumSize > 200000000){error = "Uploaded files must not exceed 200mb"; switcher = false;}


                // Проверка на пробелы. rename в php не работает с пробелами
                
                var nameFile = [];
                var counter = 0;
                for(var i = 0; i < files.length; i++){
                    nameFile[counter] = "";
                    var tempName = files[i].name;
                    for(var z = 0; z < tempName.length; z++){
                        if(tempName[z] == " ") nameFile[counter] += "-";
                        else nameFile[counter] += tempName[z];
                    }
                    counter++;
                }

                if(switcher){
                        if (window.FormData === undefined){
                            alert('В вашем браузере FormData не поддерживается');
                        } else {
                            var formData = new FormData();
                            $.each($("#file3")[0].files,function(key, input){
                                formData.append('file[]', input, nameFile[counter]);
                                counter++;
                            });
                            
                            $.ajax({
                                type: "POST",
                                url: '/api/tempFiles.php',
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: formData,
                                dataType : 'json',
                                success: function(data){

                                }
                            });
                        }
                    }
                else alert(error);
            }
        }

        function loadingIMG(files){
            var extension = ["image/jpeg","image/jpg","image/png","image/bmp"];
            var error = "";

            if(files.length > 8){
                error="You uploaded more than 8 image";
                alert(error);
            }
            else{
                for(var i = 0; i < files.length; i++){
                var switcher = true; // Для выявления ошибок
                // Проверка на расширение
                for(let z = 0; z < extension.length; z++){
                    if(files[i].type == extension[z]){switcher = true; break;}
                    else { switcher = false; error = "The extension is not supported. Available extensions: jpeg, png, bmp"; }
                }

                // Проверка на размер
                if(switcher){
                    if(files[i].size > 15000000){
                        error = "The maximum size of one image is no more than 15mb";
                        switcher = false;
                    }
                }
                else {alert(error); break;}
                if(!switcher) {alert(error); break;}
                }

                var nameFile = [];
                var counter = 0;
                for(var i = 0; i < files.length; i++){
                    nameFile[counter] = "";
                    var tempName = files[i].name;
                    for(var z = 0; z < tempName.length; z++){
                        if(tempName[z] == " ") nameFile[counter] += "-";
                        else nameFile[counter] += tempName[z];
                    }
                    counter++;
                }
                counter = 0;

                if(switcher){
                    if (window.FormData === undefined){
                        alert('В вашем браузере FormData не поддерживается')
                    } else {
                        var formData = new FormData();
                        $.each($("#file2")[0].files,function(key, input){
                            formData.append('file[]', input, nameFile[counter]);
                            counter++;
                        });
                    
                        var tagCount = ['#one', '#two', '#three', '#four', '#five', '#six', '#seven', '#eight'];

                        $.ajax({
                            type: "POST",
                            url: '/api/tempImage.php',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            dataType : 'json',
                            success: function(data){
                                let maxLen = files.length;
                                let count = 0;
                                for(var i = 0; i < maxLen; i++){
                                    if($(tagCount[i] + ' img').length > 0) { maxLen++; continue; }
                                    $(tagCount[i]).append('<img src="temp/images/'+nameFile[count]+'">');
                                    count++;
                                }
                            }
                        });
                    }
                }
                else alert(error);
            }
            
        }

        function changeMainPicter(id){
            if($(id + ' img').length == 0){alert("You clicked on an empty cell");} // Проверка на пустую ячейку, где нет изображения
                else{
                    if($('.mainImageUpload img').length == 0){ // Проверка на отсутсвие изображения в главном окошке
                        $('.field__file-button').remove();
                        $(".mainImageUpload").append('<img src="/'+$(id+' img').attr('src') +'">');
                    }
                    else{
                        $(".mainImageUpload img").remove();
                        $(".mainImageUpload").append('<img src="/'+$(id+' img').attr('src') +'">');
                    }
                }
        }

        $(document).ready(function(e){ 
            $('.field__file-button').on({
                mouseenter: function() {
                    $('.field__file-button').css("background-color","#5a5acc");
                    $('.field__file-button').contents()[0].nodeValue = 'Select the main image in the uploaded images';
                },
                mouseleave: function() {
                    $('.field__file-button').css('background-color',"#4c4cb3");
                    $('.field__file-button').contents()[0].nodeValue = 'Main image';
                },
            });
            $('.dopImagesUploadText').on({
                mouseenter: function() {
                    $('.dopImagesUploadText').css("background-color","#5a5acc");
                },
                mouseleave: function() {
                    $('.dopImagesUploadText').css('background-color',"#4c4cb3");
                }
            });


            $('.fileUploadText').on({
                mouseenter: function() {
                    $('.fileUploadText').css("background-color","#5a5acc");
                },
                mouseleave: function() {
                    $('.fileUploadText').css('background-color',"#4c4cb3");
                }
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

            /////////////////////////////////////////////////////////////////
            // Отправка на сервер всех данных

            $('.UploadButton').on({
                mouseenter: function() {
                    $('.UploadButton').css("background-color","#5a5acc");
                },
                mouseleave: function() {
                    $('.UploadButton').css('background-color',"#4c4cb3");
                },
                click: function(){
                    pushDataInBD();
                }
            });
            
        });

        function pushDataInBD(){
            var mainText = $('#mainText').val();
            var extensionFiles = [];
            var extensionIMG = [];
            var textArea = $('#textArea').val();

            var img = []; // Все загруженные изображения
            var masFiles = [];
            var files = $("#file3")[0].files; // Все загруженные файлы
            var mainImg = $('.mainImageUpload img').attr('src'); // Фото, которое было выбрано в качестве главного 

            // выявление расширения загруженных файлов
            for(var i = 0; i < files.length; i++){
                var extension = [".stl", ".txt", ".ply",".obj",".bhv",".x3d",".blend",".dae",".fbx",".abc",".wrl",".pdf",".gl",".usd",".svg",".psd"];
                let typeFile = files[i].name;
                    typeFile = typeFile.match(/\.\w+/gi);

                for(let z = 0; z < extension.length; z++){
                    if(typeFile[typeFile.length-1] == extension[z]) {extensionFiles[i] = extension[z];}
                }
            }
            // выявление расширения загруженных фотографий

            if(mainText == ""){ // Проверка на главный текст
                alert("Enter main title");
                $('#mainText').css('border-color', "red");
            }
            else{ 

                if($('#one img').length == 0){ // проверка на наличия изображения
                    alert("Upload image");
                }
                else{
                    if(!!!mainImg){ // проверка на выбрал ли пользователь главную фотку
                        alert("Choice main image");
                    }
                    else{
                        if(files.length == 0){ // проверка на наличие файлов
                            alert("Upload files");
                        }
                        else{
                            var tagCount = ['#one', '#two', '#three', '#four', '#five', '#six', '#seven', '#eight'];

                            var imgSRC = [];
                            var fileSRC = [];

                            for(var i = 0; i < 8.; i++){
                                if(!!$(tagCount[i] + ' img').attr('src')){
                                    img.push($(tagCount[i] + ' img').attr('src'));
                                }
                                else break;
                            }

                            // выявление расширения загруженных изображений
                            for(var i = 0; i < img.length; i++){
                                var extension = [".jpeg",".jpg",".png",".bmp"];
                                let typeFile = img[i];
                                    typeFile = typeFile.match(/\.\w+/gi);

                                for(let z = 0; z < extension.length; z++){
                                    if(typeFile[typeFile.length-1] == extension[z]) {extensionIMG[i] = extension[z];}
                                }
                            }

                            for(var i = 0; i < files.length; i++){
                                masFiles.push(files[i].name);
                            }

                            // Преобразование массивов в строку для передачи в ajax.
                            var ExtensionIMG = "";
                            var ExtensionFiles = "";
                            var Img = "";
                            var MasFiles = "";

                            for(var i = 0; i < extensionIMG.length; i++){
                                ExtensionIMG += extensionIMG[i] + ":";
                            }
                            for(var i = 0; i < extensionFiles.length; i++){
                                ExtensionFiles += extensionFiles[i] + ":";
                            }
                            for(var i = 0; i < img.length; i++){
                                Img += img[i] + ":";
                            }
                            for(var i = 0; i < masFiles.length; i++){
                                MasFiles += masFiles[i] + ":";
                            }

                            $.ajax({
                                type: "POST",
                                url: '/api/API.php',
                                data: {method: 'pushData', login: "<?php echo $_SESSION['login'] ?>", mainText: mainText, extensionIMG: ExtensionIMG, extensionFiles: ExtensionFiles, textArea: textArea, mainImg: mainImg, img: Img, masFiles: MasFiles},
                                dataType : 'json',
                                success: function(data){
                                    if(data['access'] == true){
                                        $(location).attr('href', 'account.php');
                                    }
                                }
                            });
                        }
                    }
                }
                
            }
        }
    </script>
</body>
</html>