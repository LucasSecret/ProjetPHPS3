<?php
    session_start();

    if($_SESSION['connected'] != 1)
    {
        session_destroy();
        header('Location:../login.html?error_connexion=NoConnected');
    }

    echo 'Vous etes un utilisateur enregistré c\'est cool';


?>