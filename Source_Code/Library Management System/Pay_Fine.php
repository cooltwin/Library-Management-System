<?php
/*
 * Author : Twinkle Gupta
 * File Description : This page checks if user has fulfilled all criteria's to a pay a fine.
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

        form {
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

        $card_no = $_POST["card_no"];
        $fname = $_POST["fname"];
        $lname = $_POST["lname"];


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //searching by card_no
            if (!empty($_POST["card_no"]) && empty($_POST["fname"]) && empty($_POST["lname"])) {
                //execute the SQL query and return records
                $result1 = mysql_query("SELECT SUM(Fine_amt) AS Total_Fine_Amt, l.Card_no
                                FROM Fines AS f, Book_loans AS l
                                WHERE l.Card_no = $card_no AND l.loan_id = f.loan_id AND f.Paid = 0
                                GROUP BY l.Card_no;");
            } //searching by borrower's first name
            else if (empty($_POST["card_no"]) && !empty($_POST["fname"]) && empty($_POST["lname"])) {
                $result1 = mysql_query("SELECT SUM(Fine_amt) AS Total_Fine_Amt, l.Card_no
                                FROM Fines AS f, Book_loans AS l, BORROWER AS b
                                WHERE b.Fname = '$fname' AND l.Card_no = b.Card_no AND l.loan_id = f.loan_id AND f.Paid = 0
                                GROUP BY l.card_no ;");
            } //searching by borrower's last name
            else if (empty($_POST["card_no"]) && empty($_POST["fname"]) && !empty($_POST["lname"])) {
                $result1 = mysql_query("SELECT SUM(Fine_amt) AS Total_Fine_Amt, l.Card_no
                                FROM Fines AS f, Book_loans AS l, BORROWER AS b
                                WHERE b.Lname = '$lname' AND l.Card_no = b.Card_no AND l.loan_id = f.loan_id AND f.Paid = 0
                                GROUP BY l.card_no;");
            } //searching by card_no and borrower's first name
            else if (!empty($_POST["card_no"]) && !empty($_POST["fname"]) && empty($_POST["lname"])) {
                $result1 = mysql_query("SELECT SUM(Fine_amt) AS Total_Fine_Amt, l.Card_no
                                FROM Fines AS f, Book_loans AS l, BORROWER AS b
                                WHERE b.Fname = '$fname' AND l.Card_no = $card_no AND l.loan_id = f.loan_id AND f.Paid = 0
                                GROUP BY l.card_no;");
            } //searching by card_no and borrower's last name
            else if (!empty($_POST["card_no"]) && empty($_POST["fname"]) && !empty($_POST["lname"])) {
                $result1 = mysql_query("SELECT SUM(Fine_amt) AS Total_Fine_Amt, l.Card_no
                                FROM Fines AS f, Book_loans AS l, BORROWER AS b
                                WHERE b.Lname = '$lname' AND l.Card_no = $card_no AND l.loan_id = f.loan_id AND f.Paid = 0
                                GROUP BY l.card_no;");
            } //searching by borrower's first and last name
            else if (empty($_POST["card_no"]) && !empty($_POST["fname"]) && !empty($_POST["lname"])) {
                $result1 = mysql_query("SELECT SUM(Fine_amt) AS Total_Fine_Amt, l.Card_no
                                FROM Fines AS f, Book_loans AS l, BORROWER AS b
                                WHERE b.Fname = '$fname' AND b.Lname = '$lname' AND l.card_no = b.card_no AND f.Paid = 0
                                 AND l.loan_id = f.loan_id
                                GROUP BY l.card_no;");
            } //searching by card_no and borrower's first and last name
            else if (!empty($_POST["card_no"]) && !empty($_POST["fname"]) && !empty($_POST["lname"])) {
                $result1 = mysql_query("SELECT SUM(Fine_amt) AS Total_Fine_Amt, l.Card_no
                                FROM Fines AS f, Book_loans AS l, BORROWER AS b
                                WHERE b.Fname = '$fname' AND b.Lname = '$lname' AND l.Card_no = $card_no AND f.Paid = 0
                                AND l.loan_id = f.loan_id
                                GROUP BY l.card_no;");
            }
        }
        if ($row1 = mysql_fetch_array($result1)) {
        $fine_amt = $row1{'Total_Fine_Amt'};
        $borrower_card_no = $row1{'Card_no'};
        $flag = 0;
        $i = 0;
        $loan_id = array();
        $result2 = mysql_query("SELECT Date_in, Loan_id FROM BOOK_LOANS WHERE Card_no = $borrower_card_no;");
        while ($row2 = mysql_fetch_array($result2)) {
            $loan_id[$i] = $row2{'Loan_id'};
            $i++;
            if ($row2{'Date_in'} == "0000-00-00") {
                $flag++;
            }
        }
        // Borrower has not returned the book(s), Ask him to return and then try to pay the fine
        if ($flag > 0){
            echo "Error!! You have not returned the book(s), You can't pay the fine now <br>";
            echo "Please return the book(s) and then try again, book(s) :<br>";
            $result3 = mysql_query("SELECT b.Title
                                FROM BOOK AS b,BOOK_LOANS AS l
                                WHERE l.Card_no = $borrower_card_no AND l.book_id = b.book_id;");
            while ($row3 = mysql_fetch_array($result3)) {
                echo "-- " . $row3{'Title'} . "<br>";
            }
        }
        else {
        //Borrower has returned all the books, he/she can be allowed to pay the fine
        echo "Good!! You have returned all the book(s) <br><br>";
        echo "You can now pay your fine <br><br>";
        echo "Your fine amt is $" . $fine_amt . "<br><br>";
        echo "You can either pay full or none <br><br>";
        $loan_id1 = $loan_id[0];
        $loan_id2 = $loan_id[1];
        $loan_id3 = $loan_id[2];

        session_start();
        $_SESSION['loan_id1'] = $loan_id1;
        $_SESSION['loan_id2'] = $loan_id2;
        $_SESSION['loan_id3'] = $loan_id3;

        ?>
        <form method="get" action="Fine_Paid.php">
            <div style="padding-top: 40px">
                <input type="submit" value="Pay Fine"/>
        </form>
        <form method="get" action="Fine_Management.php">
            <input type="submit" value="Cancel"/><br><br><br>
            </div>
        </form>
    <?php

    }
    }
    else {
        echo "Error !! Invalid value, Either of the possibilities :- <br>";
        echo "1. You have not taken any book loan <br>";
        echo "2. You have entered wrong lname/fname/card_no <br>";
        echo "3. You have returned the book on time, i.e  Good News !! You don't have any fine <br>";
    }


    $dbhandle->close();
    ?>
</div>
</div>
</body>
</html>
