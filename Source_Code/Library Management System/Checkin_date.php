<?php
/**
 * Author : Twinkle Gupta
 * File Description : Continuation of Checking.php page, Mainly created to take the check in date from user
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
    <div style="padding-top: 200px">
        <?php

        session_start();
        $username = $_SESSION['username'];
        $password = $_SESSION['password'];
        $hostname = "localhost";

        //connection to the database
        $dbhandle = mysql_connect($hostname, $username, $password)
        or die("Unable to connect to MySQL");

        //selecting library database to work on it
        $selected = mysql_select_db("Library_Management_System", $dbhandle)
        or die("Could not select the database");

        session_start();
        $loan_id = $_SESSION['loan_id'];
        $bid = $_SESSION['book_id'];

        $result1 = mysql_query("UPDATE BOOK_LOANS SET Date_in = now() WHERE Loan_id = $loan_id;");
        $result2 = mysql_query("SELECT b.Title, a.Author_name FROM BOOK AS b, BOOK_AUTHORS AS a WHERE b.Book_id = $bid AND
                        a.Book_id = b.Book_id;");
        $result3 = mysql_query("SELECT Fine_amt FROM FINES WHERE Paid = 0 AND Loan_id = $loan_id;");
        if ($row = mysql_fetch_array($result2)) {
            echo "You have successfully checked in for loan id : " . $loan_id . "<br><br>";
            echo "Book Title :  '" . $row{'Title'} . "' by '" . $row{'Author_name'} . "'<br>";
            if ($row1 = mysql_fetch_array($result3)) {
                echo "Imp!!  Note : You have fine to be paid for this book";
            }
        }
        $dbhandle->close;
        ?>
    </div>
</div>
</body>
</html>