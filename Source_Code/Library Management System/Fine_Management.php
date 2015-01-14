<?php
/**
 * Author : Twinkle Gupta
 * File Description : This file computes the fine for all borrowers of the library management system
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
            margin-left: 20px;
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


<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div style="padding-top: 150px">
        <input type="submit" name='update_all' value="Update all Fines" style="padding: 10px"/>
</form>
    <form method="post" action="Individual_Fine.php">
        <input type="submit" name='get_one' value="Find Individual Fine Amt" style="padding: 10px"/><br><br>
</div>
</form>

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

if ($_GET) {
    if (isset($_GET['update_all'])) {
        update_all_fines();
    }
}

function update_all_fines()
{
    $result1 = mysql_query("SELECT * FROM BOOK_LOANS;");
    // for each loan id in book loans table update the fines table
    while ($row1 = mysql_fetch_array($result1)) {
        $do_not_take_fine = 0;
        $loan_id = $row1{'Loan_id'};
        $date_in = strtotime($row1{'Date_in'});
        $due_date = strtotime($row1{'Due_date'});

        $days_diff = $date_in - $due_date;
        $days_past_due_date = floor($days_diff / (60 * 60 * 24));

        if ($days_past_due_date > 0 || $row1{'Date_in'} == '0000-00-00') {
            //book is returned after due date, charge fine
            //Fine Computation :-

            $current_date = time();
            $future_due_diff = $current_date - $due_date;
            $future_due = floor($future_due_diff / (60 * 60 * 24));

            if ($row1{'Date_in'} == '0000-00-00' && $future_due > 0) {
                // if book is not returned till today's date, and due date has passed
                $diff = $current_date - $due_date;
            } elseif ($row1{'Date_in'} != '0000-00-00') {
                // if book is returned but delayed from its due date
                $diff = $date_in - $due_date;
            } else {
                //if book is not returned till today, but due date has still not passed
                //do nothing
                $do_not_take_fine++;
            }
            $paid = 0;
            $date_diff = floor($diff / (60 * 60 * 24));
            $fine_amt = $date_diff * 0.25;

            $result2 = mysql_query("SELECT * FROM FINES WHERE Loan_id = $loan_id");
            // checking if this loan id is already there in fines table
            if ($row2 = mysql_fetch_array($result2)) {
                // Already paid the fine. do nothing
                // if not paid fine then update the fine table with new fine_amt
                if ($row2{'Paid'} == 0 && $do_not_take_fine == 0) {
                    $result3 = mysql_query("UPDATE FINES SET Fine_amt = $fine_amt WHERE Loan_id = $loan_id");
                }

            } else {
                // this loan id is not present in fines table, it's a new entry, so use insert command
                if ($do_not_take_fine == 0)
                    $result3 = mysql_query("INSERT INTO FINES VALUES($loan_id, $fine_amt, $paid);");
            }
        }
        // else Borrower has returned by the due date so no charge is to be fined on the borrower.
    }
    echo "<br><br><br><br> Fines table is updated successfully !!!";
}

$dbhandle->close();
?>
</div>
</body>
</html>
