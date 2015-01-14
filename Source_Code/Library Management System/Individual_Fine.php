<?php
/**
 * Author : Twinkle Gupta
 * File Description : This file takes user details inorder to compute his/her fine amount.
 */
?>
<html>
<body bgcolor="#FF9933">
<div align="center">
    <h1>Library Management System</h1>
    <style>
        ul {
            list-style-type: none;
        }

        li {
            color: #480000;
            font-size: large;
            display: inline;
            margin-left: 10px;
        }
    </style>
    <ul>
        <li><button type="button"><a href="Home.php">Home</a></button></li>
        <li><button type="button"><a href="Book_Search.php">Search Book</a></button></li>
        <li><button type="button"><a href="Checkout_Books.php">Checkout Book</a></button></li>
        <li><button type="button"><a href="Checkin_Books.php">Checkin Book</a></button></li>
        <li><button type="button"><a href="Create_New_Borrower.php">Add New Borrower</a></button></li>
        <li><button type="button"><a href="Fine_Management.php">Manage Fine</a></button></li>
    </ul>

    <form method="post" action="Pay_Fine.php">
        <div style="padding-top: 100px">
            Card No <input type="text" name="card_no" style="margin-left:90px; margin-bottom:20px;"><br><br><br>
            Card Holder Fname <input type="text" name="fname" style="margin-left:20px; margin-bottom:20px;"><br><br><br>
            Card Holder Lname <input type="text" name="lname" style="margin-left:20px; margin-bottom:6px;"><br><br><br>
            <input type="submit" value="Get Fine Amount"><br><br><br>
        </div>
    </form>

</div>
</body>
</html>