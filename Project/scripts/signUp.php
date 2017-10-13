<?php
/**
 * Created by PhpStorm.
 * User: sntri
 * Date: 09/10/2017
 * Time: 11:08
 */

include('../classes/SQLServices.php');
include('../includes/variables.inc.php');
$dbHandler = new SQLServices($hostnameDB, $dbName, $userDB, $passwordDB);

if(isset($_POST['username']) && isset($_POST['password']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(!($dbHandler->isRegistered($username, $password)))
    {
        $dbHandler->insertData('user', array(
            array(
                'username' => $username,
                'password' => md5($password),
                'admin' => 0
            )
        ));
        header('Location:../signUp.html?error_signUp=no_error');
    }

    else
    {
        session_destroy();
        header('Location:../signUp.html?error_signUp=existingUsername');

    }
}

else
{
    session_destroy();
    header('Location:../signUp.html?error_signUp=fieldEmpty');

}
?>