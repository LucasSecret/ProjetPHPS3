<?php
/**
 * Created by PhpStorm.
 * User: sntri
 * Date: 09/10/2017
 * Time: 10:57
 */
    session_start();
    include('../includes/variables.inc.php');
    include('../classes/SQLServices.php');

    if($_SESSION['connected'] != 1)
    {
        session_destroy();
        header('Location:../index.php?error_connexion=NoConnected');
    }

    echo 'Vous etes un utilisateur enregistré c\'est cool';

    $sqlService = new SQLServices($hostnameDB, $dbName, $userDB, $passwordDB);
?>
    
<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <?php
        $sqlService->displayAllImage();
    ?>
</body>
</html>
