<?php
/**
 * Created by PhpStorm.
 * User: sntri
 * Date: 09/10/2017
 * Time: 10:57
 */
    session_start();

    if($_SESSION['connected'] != 1)
    {
        session_destroy();
        header('Location:../index.php?error_connexion=NoConnected');
    }

    echo 'Vous etes un utilisateur enregistré c\'est cool';
?>
    
<!DOCTYPE html>
<html>
<head>
</head>
<body>

</body>
</html>
