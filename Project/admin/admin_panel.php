<?php

session_start();

if($_SESSION['connected'] != 1)
{
    session_destroy();
    header('Location:../index.php?error_connexion=NoConnected');
}

echo 'Bonjour '.$_SESSION['username'].', vous etes admin c\'est cool';
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>

</body>
</html>
