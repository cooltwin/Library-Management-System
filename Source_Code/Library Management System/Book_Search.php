<?php
/**
 * Author : Twinkle Gupta
 * File Description : Implements the Book Search feature of Library Management System
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
            Book Id<input type="text" name="book_id" style="margin-left:35px; margin-bottom:21px;"><br><br><br>
            Book Title<input type="text" name="book_title" style="margin-left:25px; margin-bottom:15px;"><br><br><br>
            Book Author<input type="text" name="book_author" style="margin-left:15px; margin-bottom:9px;"><br><br><br>
            <input type="submit" value="Search Book"><br><br><br>
        </div>
    </form>

    <?php


    $book_id = $_POST["book_id"];
    $author = $_POST["book_author"];
    $title = $_POST["book_title"];

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


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //searching by book id alone
        if (!empty($_POST["book_id"]) && empty($_POST["book_title"]) && empty($_POST["book_author"])) {
            //execute the SQL query and return records
            $result = mysql_query("SELECT b.Book_id, b.Title, a.Author_name, c.Branch_id, c.No_of_copies,
                    (c.No_of_copies - (IFNULL((SELECT COUNT(*) FROM BOOK_LOANS AS l WHERE c.Branch_id = l.Branch_id AND
                    c.Book_id = l.Book_id GROUP BY Branch_id), 0))) AS No_of_copies_available
                    FROM BOOK_COPIES AS c, BOOK AS b, BOOK_AUTHORS AS a
                    WHERE c.Book_id like '%$book_id%' AND c.Book_id = b.Book_id AND c.Book_id = a.Book_id;");
        } //searching by book title alone
        else if (empty($_POST["book_id"]) && !empty($_POST["book_title"]) && empty($_POST["book_author"])) {
            $result = mysql_query("SELECT b.Book_id, b.Title, a.Author_name, c.Branch_id, c.No_of_copies,
                    (c.No_of_copies - (IFNULL((SELECT COUNT(*) FROM BOOK_LOANS AS l WHERE c.Branch_id = l.Branch_id AND
                    b.Book_id = l.Book_id GROUP BY Branch_id), 0))) AS No_of_copies_available
                    FROM BOOK_COPIES AS c, BOOK AS b, BOOK_AUTHORS AS a
                    WHERE b.Title like '%$title%' AND c.Book_id = b.Book_id AND a.Book_id = b.Book_id;");
        } //searching by author name alone
        else if (empty($_POST["book_id"]) && empty($_POST["book_title"]) && !empty($_POST["book_author"])) {
            $result = mysql_query("SELECT b.Book_id, b.Title, a.Author_name, c.Branch_id, c.No_of_copies,
                    (c.No_of_copies - (IFNULL((SELECT COUNT(*) FROM BOOK_LOANS AS l WHERE c.Branch_id = l.Branch_id AND
                    a.Book_id = l.Book_id GROUP BY Branch_id), 0))) AS No_of_copies_available
                    FROM BOOK_COPIES AS c, BOOK AS b, BOOK_AUTHORS AS a
                    WHERE a.Author_name like'%$author%' AND a.Book_id = b.Book_id AND c.Book_id = a.Book_id;");
        } //searching by book id and book title
        else if (!empty($_POST["book_id"]) && !empty($_POST["book_title"]) && empty($_POST["book_author"])) {
            $result = mysql_query("SELECT b.Book_id, b.Title, a.Author_name, c.Branch_id, c.No_of_copies,
                    (c.No_of_copies - (IFNULL((SELECT COUNT(*) FROM BOOK_LOANS AS l WHERE c.Branch_id = l.Branch_id AND
                    c.Book_id = l.Book_id GROUP BY Branch_id), 0))) AS No_of_copies_available
                    FROM BOOK_COPIES AS c, BOOK AS b, BOOK_AUTHORS AS a
                    WHERE c.Book_id like '%$book_id%' AND b.title like '%$title%' AND a.Book_id = c.Book_id
                    AND c.Book_id = b.Book_id;");
        } //searching by book id and book author name
        else if (!empty($_POST["book_id"]) && empty($_POST["book_title"]) && !empty($_POST["book_author"])) {
            $result = mysql_query("SELECT b.Book_id, b.Title, a.Author_name, c.Branch_id, c.No_of_copies,
                    (c.No_of_copies - (IFNULL((SELECT COUNT(*) FROM BOOK_LOANS AS l WHERE c.Branch_id = l.Branch_id AND
                    c.Book_id = l.Book_id GROUP BY Branch_id), 0))) AS No_of_copies_available
                    FROM BOOK_COPIES AS c, BOOK AS b, BOOK_AUTHORS AS a
                    WHERE c.Book_id like '%$book_id%' AND a.Author_name like'%$author%' AND c.Book_id = b.Book_id AND
                    c.Book_id = a.Book_id;");
        } //searching by title and author name
        else if (empty($_POST["book_id"]) && !empty($_POST["book_title"]) && !empty($_POST["book_author"])) {
            $result = mysql_query("SELECT b.Book_id, b.Title, a.Author_name, c.Branch_id, c.No_of_copies,
                    (c.No_of_copies - (IFNULL((SELECT COUNT(*) FROM BOOK_LOANS AS l WHERE c.Branch_id = l.Branch_id AND
                    a.Book_id = l.Book_id GROUP BY Branch_id), 0))) AS No_of_copies_available
                    FROM BOOK_COPIES AS c, BOOK AS b, BOOK_AUTHORS AS a
                    WHERE b.title like '%$title%' AND a.Author_name like'%$author%' AND a.Book_id = b.Book_id
                    AND c.Book_id = a.Book_id;");
        } //searching by book id, title and author name
        else if (!empty($_POST["book_id"]) && !empty($_POST["book_title"]) && !empty($_POST["book_author"])) {
            $result = mysql_query("SELECT b.Book_id, b.Title, a.Author_name, c.Branch_id, c.No_of_copies,
                    (c.No_of_copies - (IFNULL((SELECT COUNT(*) FROM BOOK_LOANS AS l WHERE c.Branch_id = l.Branch_id AND
                    a.Book_id = l.Book_id GROUP BY Branch_id), 0))) AS No_of_copies_available
                    FROM BOOK_COPIES AS c, BOOK AS b, BOOK_AUTHORS AS a
                    WHERE a.Author_name like'%$author%'AND b.Title like '%$title%' AND c.Book_id like '%$book_id%'
                    AND a.Book_id = b.Book_id AND c.Book_id = a.Book_id;");
        }
    }

    // wait till the result variable fetches the query i.e $result != ''
    if (!empty ($result)) {
        echo "<h3>Search Results : </h3>";
        echo '<table cellspacing = "20" class="table table-striped table-bordered table-hover">';
        echo "<tr><th> Book Id </th><th> Title </th><th> Author Name </th><th> Branch ID </th>
                        <th> Total No.of copies </th><th> No of Copies Available </th></tr>";

        // Checking query results in empty set, this means that either of the fields are wrongly entered
        if(!($row = mysql_fetch_array($result))){
            echo "Error!! Could not Search the book, No Such book exists";
        }
        //fetch tha data from the database
        while ($row = mysql_fetch_array($result)) {
            echo "<tr><td>" . $row{'Book_id'} . " </td><td>" . $row{'Title'} . " </td><td>" . $row{'Author_name'} . " </td><td>"
                . $row{'Branch_id'} . " </td><td>" . $row{'No_of_copies'} . " </td><td>" . $row{'No_of_copies_available'} .
                " </td></tr>";
            $flag =1;
        }
    }

    $dbhandle->close();
    ?>
</div>
</body>
</html>
