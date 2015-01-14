<? php
/**
 * Author : Twinkle Gupta
 * File Description : Implements the Book Check out feature of Library Management System
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

    <!creating the form to input the book id, title and or author name>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div style="padding-top: 150px">
            Book Id <input type="text" name="book_id" style="margin-left:25px; margin-bottom:20px;"><br><br><br>
            Branch Id <input type="text" name="branch_id" style="margin-left:15px; margin-bottom:20px;"><br><br><br>
            Card No <input type="text" name="card_no" style="margin-left:25px; margin-bottom:20px;"><br><br><br>
            <input type="submit" value="Checkout Book"><br><br><br>
        </div>
    </form>

    <?php
    $book_id = $_POST["book_id"];
    $branch_id = $_POST["branch_id"];
    $card_no = $_POST["card_no"];

    session_start();
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    $hostname = "localhost";

    $no_of_books_taken = 0;

    //connection to the database
    $dbhandle = mysql_connect($hostname, $username, $password)
    or die("Unable to connect to MySQL");

    //selecting library database to work on it
    $selected = mysql_select_db("Library_Management_System", $dbhandle)
    or die("Could not select the database");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // checking if the fields are empty, if so abort the checkout
        if($book_id == '' || $branch_id == '' || $card_no == ''){
            echo "Checkout failed !! No field can be left empty, Please enter a valid value for each of the fields";
            goto end;
        }
        // checking if book_id is a valid
        $result = mysql_query("SELECT Book_id
                            FROM Book
                            WHERE Book_id = $book_id;");
        if (!($row = mysql_fetch_array($result))) {
            echo "Error!! Checkout failed, Invalid Book id !! No such book id exists <br>";
            goto end;
        }

        //checking if branch id is valid
        if ($branch_id < 1 || $branch_id > 5) {
            echo "Error!! Checkout failed, Invalid Branch id !! please enter a valid branch id <br>";
            goto end;
        }

        // checking if card no is valid
        $result1 = mysql_query("SELECT Fname
                                FROM BORROWER
                                WHERE Card_no = $card_no;");
        if ($row = mysql_fetch_array($result1)) {
            echo "Hello " . $row{'Fname'} . "<br>";
        } else {
            echo "Error!! Checkout failed, Invalid Card Holder !! No such card no exists <br>";
            goto end;
        }


        // checking if the card holder has taken 3 books
        $result2 = mysql_query("SELECT COUNT(*) AS No_of_copies_taken
                                            FROM  BOOK_LOANS
                                            WHERE Card_no = $card_no
                                            GROUP BY Card_no;");
        $loan_id = array();
        $i = 0;
        while ($row2 = mysql_fetch_array($result2)) {
            if ($row2{No_of_copies_taken} == 3) {
                echo "Error!! Checkout failed, You have already taken 3 book loans<br>";
                echo "The 3 loans you have taken are :<br>";
                // printing the books the card holder has taken
                $result3 = mysql_query("SELECT *
                                            FROM  BOOK_LOANS
                                            WHERE Card_no = $card_no;");
                echo '<table cellspacing = "20" class="table table-striped table-bordered table-hover">';
                echo "<tr><th> Loan Id</th><th>Book Id </th><th> Branch Id </th><th> Card No </th><th>
                                    Date out </th><th> Due Date </th><th> Date in </th></tr>";
                while ($row3 = mysql_fetch_array($result3)) {
                    echo "<tr><td>" . $row3{'Loan_id'} . "</td><td>" . $row3{'Book_id'} . " </td><td>" .
                        $row3{'Branch_id'} . " </td><td>" . $row3{'Card_no'} . " </td><td>"
                        . $row3{'Date_out'} . " </td><td>" . $row3{'Due_date'} . " </td><td>" . $row3{'Date_in'} .
                        " </td></tr>";
                    $loan_id[$i] = $row3{'Loan_id'};
                    $i++;
                }
                goto end; // if card holder already has taken 3 book loans, then stop the query
            }
        }


        //checking if borrower has any unpaid fines
        $result4 = mysql_query("SELECT SUM(Fine_amt) AS Total_Fine
                            FROM FINES AS f, BOOK_LOANS AS l
                            WHERE l.Card_no = $card_no AND l.Loan_id = f.loan_id AND Paid = 0;");
        if ($row4 = mysql_fetch_array($result4)) {
            if ($row4{'Total_Fine'} != NULL) {
                echo "Error !! Checkout failed, You have fine due of $" . $row4{'Total_Fine'} . "<br>";
                echo "Please first pay the fine and then try again to checkout the book <br>";
                goto end;
            }
        }

        //checking if the branch has sufficient copies of the book to given to the card holder
        $result5 = mysql_query("SELECT (c.No_of_copies - (IFNULL((SELECT COUNT(*) FROM BOOK_LOANS AS l
                        WHERE c.Branch_id = l.Branch_id AND c.Book_id = l.Book_id GROUP BY Branch_id), 0))) AS
                        No_of_copies_available
                        FROM BOOK_COPIES AS c
                        WHERE c.Book_id = $book_id AND c.Branch_id = $branch_id;");
        //fetch tha data from the database
        while ($row5 = mysql_fetch_array($result5)) {
            if ($row5{'No_of_copies_available'} == 0) {
                echo "Error!! Checkout failed, Sorry !! No Copies of this book is left in the library
                                branch<br>";
                echo "Please check some other library branch<br>";
                goto end;
            }
        }


        // query satisfies all restrictions, so now creating a new tuple in database
        // Due_date is 14 days from date_out so its the 14th day so interval of 15 days from date_out
        $result6 = mysql_query("INSERT INTO BOOK_LOANS (Book_id, Branch_id, Card_no, Date_out, Due_date,
                        Date_in) VALUES ('$book_id', $branch_id, $card_no,now(),DATE_ADD(now(),interval 15 day),
                        '0000-00-00');");
        // Insertion will be successful if none of above happens
        echo "Checkout Successful !! Your book loan is successfully added to the database <br>";
    }
    end:
    $dbhandle->close();
    ?>
</div>
</body>
</html>