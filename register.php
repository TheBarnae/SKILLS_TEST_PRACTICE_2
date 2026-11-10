<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <form action="register.php" method="post">
        <label>Username:</label><br>
        <input type ="text" name = "username"><br>
        <label>Password:</label><br>
        <input type ="password" name = "password"><br>
        <input type = "radio" name = "course" value = "BSIT">BSIT<br>
        <input type = "radio" name = "course" value = "BSCS">BSCS<br>
        <input type = "radio" name = "course" value = "BSAI">BSAI<br>
        <input type ="submit" value = "Register">
    </form>
    
</body>
</html>
<?php
    
?>