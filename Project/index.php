<?php
include('classes/SQLServices.php');
include('classes/ImageHandler.php');
include('includes/variables.inc.php');

$keywords = null;

if(isset($_GET['keywords']))
    $keywords = $_GET['keywords'];

$sqlService = new SQLServices($hostnameDB, $dbName, $userDB, $passwordDB);
$imageHandler = new ImageHandler($sqlService);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue</title>

    <link type="text/css" href="./css/bootstrap.css" rel="stylesheet">
    <link type="text/css" href="./css/project-style.css" rel="stylesheet">

</head>
<body>
<div id="main-content">

    <header class="sticky-top">
        <nav class="navbar navbar-dark bg-dark">
            <div class="container d-flex justify-content-between">

                <div class="navbar-brand d-flex">
                    <img src="./images/logo.png" id="logo">
                    <h1 class="text-white align-self-center">Catalogue</h1>
                </div>


                <ul class="navbar-nav d-flex">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Subscribe</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="bg-dark collapse" id="advanced-menu">
            <form action="scripts/returnKeywordSelected.php" method="post" class="container d-flex justify-content-between">
                    <?php $imageHandler->displayCheckbox(); ?>
                <input type="submit" class="btn" value="Display">
            </form>
        </div>

        <a data-toggle="collapse" href="#advanced-menu" aria-expanded="false" aria-controls="collapseExample">
            <img src="images/advanced_menu.png" class="center-horizontaly" id="advanced-menu-button" >
        </a>
    </header>

    <div id="photos" class="container bg-secondary" style="height: 1000px">
        <?php $imageHandler->displayImageWithKeyword($keywords, 'index') ?>
    </div>

    <footer>
        <div class="bg-dark">
            <div class="container d-flex justify-content-between text-white">
                mentions légales
                <!-- TODO: content imp -->
            </div>
        </div>
    </footer>
</div>


<!-- JavaScript -->
<script type="text/javascript" src="./js/jquery.min.3.1.2.js"></script>
<script type="text/javascript" src="./js/bootstrap.min.js"></script>

</body>
</html>