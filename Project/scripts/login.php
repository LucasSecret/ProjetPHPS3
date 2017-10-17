<?php

session_start();
// TODO : Handle Admin In $_SESSION

/***********/
/* Include */
/***********/

include('../includes/variables.inc.php');
include('../classes/SQLServices.php');

//Déclaration des variables
$dbHandler = new SQLServices($hostnameDB, $dbName, $userDB, $passwordDB);

if(isset($_POST['username']) && isset($_POST['password'])) {
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];

    if ($dbHandler->isRegistered($_SESSION['username'], $_SESSION['password']))
    {
        $_SESSION['connected'] = 1;
        header('Location:../user/user_index.php');
    }
    elseif ($dbHandler->isAdmin($_SESSION['username'], $_SESSION['password']))
    {
        header('Location:../admin/admin_panel.php');
    }
    else
    {
        header('Location:../login.html?error_connexion=noIdentified');
    }
}

header('Location: ../login.html');

?>
