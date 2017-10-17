<?php
	//Includes
	include('includes/variables.inc.php');
?>


<!DOCTYPE html>
<html>
<head>
    <title>Projet PHP</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css">
</head>
<body>

<!-- Formulaire de Connexion -->
<div id="coForm_container">
    <form method="post" action="scripts/login.php">
        <legend>Log-In</legend>
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <input type="submit" name="submit" class="button" value="Subscribe">
    </form>
    <a href="signUp.html" id="signInLink">Not registered yet ?</a>

</div>

</body>
</html>