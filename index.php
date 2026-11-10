<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="index.php" method="post">
        <label>Usernames: </label><br>
        <input type= "text" name="username"><br>
        <label>Password: </label><br>
        <input type= "password" name="password"><br>
        <input type= "submit" value="Log in">
        <form action="register.php" method="post">
        <input type= "submit" value="Register"><br>
    </form>
</body>
</html>

<?php
    if ($_POST) {
        echo "username: " . $_POST["username"] . "<br>";
        echo "password: " . $_POST["password"] . "<br>";
    }
?>