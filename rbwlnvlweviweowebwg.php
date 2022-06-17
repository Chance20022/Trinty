<?php 
    session_start();

    $linkBD = mysqli_connect('localhost', 'root', 'root', 'userdata');

    // Отдел по связь с нами
    if(isset($_GET['search'])){
        $log = $_GET['search'];
        $sqlReport = "SELECT * FROM report WHERE loginUser = '$log' ORDER BY id DESC";
        $resultReport = mysqli_query($linkBD, $sqlReport);
        $rowsReport = mysqli_fetch_all($resultReport, MYSQLI_ASSOC);
    }
    else{
        $sqlReport = "SELECT * FROM report ORDER BY id DESC";
        $resultReport = mysqli_query($linkBD, $sqlReport);
        $rowsReport = mysqli_fetch_all($resultReport, MYSQLI_ASSOC);
    }



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery-3.6.0.js"></script>
</head>
<body>
    <?php if($_SESSION['srbwqabqeb'] == 'wvhgl2nvlvnlirnl438hvnlsuhrivr') :?>
        <div class='wrapper-admin'>
            <div class="TitleAdminPanel"><div>Админ-панель</div><div style="margin: auto 0;" ><a style="cursor:pointer; font-size:24px; color: black;" href="index.php">Выход</a></div></div>
            <div class="buttonAbility">
                <div id="pisma">Письма</div>
                <div id="dannie">Данные</div>
                <div id='public'>Публикации</div>
                <div id='com'>Комментарии</div>
                <script>
                    $('#pisma').click(function(){

                    });
                    $('#dannie').click(function(){
                        $('.pismaHide').hide();

                    });
                    $('#public').click(function(){
                        $('.pismaHide').hide();

                    });
                    $('#com').click(function(){
                        $('.pismaHide').hide();

                    });
                </script>
            </div>
            <div class="pismaHide">
                <div class="searchAdmin">
                    <a style="margin: auto 10px auto 0; color: #4c4cb3; font-size: 18px;" href="rbwlnvlweviweowebwg.php">Очистить</a>
                    <input class="inputSearchAdmin" type="text">
                    <button onclick="searchRequest()" class="searchButtonAdmin">Поиск</button>
                </div>
                <?php foreach($rowsReport as $row) :?>
                <div class="lineRowAdmin">
                    <div class="rowR1"><?php echo $row['id']; ?></div>
                    <div class="rowR2"><?php echo $row['loginUser']; ?></div>
                    <div class="rowR3"><?php echo $row['title']; ?></div>
                    <div class="rowR4"><?php echo $row['textR']; ?></div>
                    <div onclick="deleteLine(<?php echo $row['id'];?>)" class="rowR5"><img src="images/logo/galka.png" alt=""></div>
                </div>
                <?php endforeach ?>
                <script>
                    function deleteLine(id){
                        $.ajax({
                            url: 'API/API.php',
                            method: 'post',
                            dataType: 'json',
                            data: {method: 'deleteLine', id: id},
                            success: function(data){
                                if(data['access'] == true){
                                    $(location).attr('href', 'rbwlnvlweviweowebwg.php');
                                }
                            }
                        });
                    }

                    function searchRequest(){
                        $(location).attr('href', 'rbwlnvlweviweowebwg.php?search=' + $('.inputSearchAdmin').val());
                    }
                </script>
            </div>
        </div>
    <?php else :?>
        Неизвестная страница
    <?php endif ?>
</body>
</html>