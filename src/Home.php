<?php
/*
 * Author Name : Twinkle Gupta
 * File Description : Home page for Library Management System
 */
?>
<html>
<body bgcolor="#FF9933">
<div align="center">
    <h1>Library Management System</h1>
    <img src="lib_pic.jpg" alt="library Icon" width="1100" height="300">
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

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div style="padding-top: 80px">
            Username <input type="text" name="username" style="margin:15px"><br><br><br>
            Password <input type="text" name="password" style="margin:15px"><br><br><br>
            <input type="submit" value="submit"><br><br><br>
        </div>
    </form>
    <?php
    If ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $hostname = "localhost";

        //connection to the database
        $dbhandle = mysql_connect($hostname, $username, $password)
        or die("Error!! Wrong Username or Password, Unable to connect to MySQL");

        //selecting library database to work on it
        $selected = mysql_select_db("Library_Management_System", $dbhandle)
        or die("Error!! Wrong Username or Password, Could not select the database");

        //passing the database credentials via session
        if (!empty($dbhandle)) {
            echo "You are authenticated successfully";
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
        } else {
            session_start();
            $_SESSION['username'] = "";
            $_SESSION['password'] = "";
        }

    }
    ?>
</div>
</body>
</html>

