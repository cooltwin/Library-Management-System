<?php
/**
 * Author : Twinkle Gupta
 * File Description : Implements the Book Check in feature of Library Management System
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
        label {
            width: 500px;
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
    <!creating the form to input the book id, title and or author name>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div style="padding-top: 120px">
            <label>Book Id</label><input type="text" name="book_id" style="margin-left:50px; margin-bottom:20px;">
            <br><br><br>
            <label>Card No</label><input type="text" name="card_no" style="margin-left:50px; margin-bottom:20px;">
            <br><br><br>
            <label>Borrower Name</label><input type="text" name="borrower_name" style="margin-left:10px;">
            <br><br><br>
            <input type="submit" value="Search Book Loan"><br><br><br>
        </div>
    </form>

    <?php
    $book_id = $_POST["book_id"];
    $card_no = $_POST["card_no"];
    $borrower_name = $_POST["borrower_name"];

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

    // Exploring all possible ways to search the book loan tuple using book_id, card_no and or borrower_name
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //searching by book id alone
        if (!empty($_POST["book_id"]) && empty($_POST["card_no"]) && empty($_POST["borrower_name"])) {
            $result = mysql_query("SELECT *
                    FROM BOOK_LOANS
                    WHERE Book_id like '%$book_id%';");
        }
        // searching by card_no alone
        if (empty($_POST["book_id"]) && !empty($_POST["card_no"]) && empty($_POST["borrower_name"])) {
            $result = mysql_query("SELECT *
                    FROM BOOK_LOANS
                    WHERE Card_no like '%$card_no%';");
        }
        // searching by borrower_name alone
        if (empty($_POST["book_id"]) && empty($_POST["card_no"]) && !empty($_POST["borrower_name"])) {
            $result = mysql_query("SELECT *
                    FROM BOOK_LOANS NATURAL JOIN BORROWER
                    WHERE Fname like '%$borrower_name%' OR  Lname like '%$borrower_name%';");
        }
        //searching by book id and card_no
        if (!empty($_POST["book_id"]) && !empty($_POST["card_no"]) && empty($_POST["borrower_name"])) {
            $result = mysql_query("SELECT *
                    FROM BOOK_LOANS
                    WHERE Book_id like '%$book_id%' AND Card_no like '%Card_no%';");
        }
        //searching by book id and borrower_no
        if (!empty($_POST["book_id"]) && empty($_POST["card_no"]) && !empty($_POST["borrower_name"])) {
            $result = mysql_query("SELECT *
                    FROM BOOK_LOANS NATURAL JOIN BORROWER
                    WHERE Book_id like '%$book_id%' AND (Fname like '%$borrower_name%' OR Lname like '%$borrower_name%');");
        }
        //searching by card no and borrower_no
        if (empty($_POST["book_id"]) && !empty($_POST["card_no"]) && !empty($_POST["borrower_name"])) {
            $result = mysql_query("SELECT *
                    FROM BOOK_LOANS NATURAL JOIN BORROWER
                    WHERE Card_no like '%Card_no%' AND (Fname like '%$borrower_name%' OR Lname like '%$borrower_name%');");
        }
        //searching by book id, card no and borrower_no
        if (!empty($_POST["book_id"]) && !empty($_POST["card_no"]) && !empty($_POST["borrower_name"])) {
            $result = mysql_query("SELECT *
                    FROM BOOK_LOANS NATURAL JOIN BORROWER
                    WHERE Book_id like '%$book_id%' AND Card_no like '%Card_no%' AND (Fname like '%$borrower_name%'
                    OR Lname like '%$borrower_name%');");
        }
    }
    $count_book_loans = 0;
    $count_books_for_check_in = 0;
    if (!empty($result)) {
        echo "<h3>Search Results : </h3>";
        echo '<table cellspacing = "20" class="table table-striped table-bordered table-hover">';
        echo "<tr><th>Loan Id</th><th>Book Id </th><th> Branch Id </th><th> Card No </th><th>
                                    Date out </th><th> Due Date </th><th>     </th></tr>";
        while ($row = mysql_fetch_array($result)) {
            $loan_id = $row{'Loan_id'};
            $bid = $row{'Book_id'};
            $count_book_loans++;
            if ($row{'Date_in'} == '0000-00-00') {
                // Printing only those books which are not checked in
                echo "<tr><td>" . $row{'Loan_id'} . "</td><td>" . $row{'Book_id'} . " </td><td>" .
                    $row{'Branch_id'} . " </td><td>" . $row{'Card_no'} . " </td><td>"
                    . $row{'Date_out'} . " </td><td>" . $row{'Due_date'} . ' </td ><td ><form method = "get"
            action = "Checkin_date.php" ><input type = "submit" name = "check_in" value = "Check in" />
            </form ></td ></tr > ';
                session_start();
                $_SESSION['loan_id'] = $loan_id;
                $_SESSION['book_id'] = $bid;
                $count_books_for_check_in++;
            } else {
                // If the book is already checked in then do nothing
            }
        }
        if ($count_book_loans > 0 && $count_books_for_check_in == 0) {
            echo "Error!! You have checked in all the books you had taken";
        }

        if ($count_book_loans == 0) {
            echo "Error!! You don't have any book loan or You have entered wrong values";
        }

    }


    $dbhandle->close();
    ?>
</div>
</body>
</html>
