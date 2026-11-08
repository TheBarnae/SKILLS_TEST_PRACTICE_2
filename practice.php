<?php
    $no_books = 3;
    $price = 7.20;
    $totalPrice = $no_books * $price;

    echo "The total is: &#8369;{$totalPrice} &#x20B1;";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Borrowing</title>
</head>
<body>
    <p>--this is html part--</p>
    <!-- Example Usage -->
<p>The total price is &#8369;1,500.00</p>
<p>The total price is &#x20B1;1,500.00</p>

</body>
</html>

/*Arithmetic operators in PHP:
    +  Addition
    -  Subtraction
    *  Multiplication
    /  Division
    %  Modulus - remainder of division (example: 5 % 2 = 1 - because 5 divided by 2 is 2 with a remainder of 1)
    ** Exponentiation
*/