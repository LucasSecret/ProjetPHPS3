<?php
/*** Script de Connexion ***/

//Initialisation de la session
session_start();
$_SESSION['connected'] = 1 ; //Cette variable nous servira a éviter les accès via URL aux pages réservées

//Includes
include('../includes/variables.inc.php');
include('../classes/SQLServices.php');

//Déclaration des variables
$dbHandler = new SQLServices($hostnameDB, $dbName, $userDB, $passwordDB);

if(isset($_POST['username']) && isset($_POST['password']))
{
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];

    if($dbHandler->isRegistered($_SESSION['username'], $_SESSION['password']))
    {
        session_destroy();
        header('Location:../user/userIndex.php');
    }

    elseif($dbHandler->isAdmin($_SESSION['username'], $_SESSION['password']))
        header('Location:../admin/adminIndex.php');

    else
    {
        session_destroy();
        header('Location:../index.php?error_connexion=noIdentified');
    }

}
?>
