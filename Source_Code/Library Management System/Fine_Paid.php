<?php
/**
 * Author : Twinkle Gupta
 * File Description : This file allows the user to pay the fine and updates the same in the library management database.
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

        $loan_id1 = $_SESSION['loan_id1'];
        $loan_id2 = $_SESSION['loan_id2'];
        $loan_id3 = $_SESSION['loan_id3'];


        //connecting to database
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

        if (empty($_SESSION['loan_id3'])) {
            $result4 = mysql_query("UPDATE FINES SET Paid = 1 WHERE Loan_id = $loan_id1 OR Loan_id = $loan_id2 OR
                        Loan_id = '';");
        }

        if (empty($_SESSION['loan_id2']) && empty($_SESSION['loan_id3'])) {
            $result4 = mysql_query("UPDATE FINES SET Paid = 1 WHERE Loan_id = $loan_id1 OR Loan_id = '' OR
                        Loan_id = '';");
        }
        echo "Success!! You have paid all your fines <br>";

        $dbhandle->close();

        ?>
    </div>
</div>
</body>
</html>
