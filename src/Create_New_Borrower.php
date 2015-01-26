<?php
/**
 * Author : Twinkle Gupta
 * File Description : This page is used to create a new borrower in library management system database
 */
?>

<!DOCTYPE HTML>
<html>
<head>
    <style>
        .error {
            color: #FF0000;
        }
    </style>
</head>

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
            display: table;
        }

        p {
            display: table-row;
        }

        label {
            display: table-cell;
        }

        input {
            display: table-cell;
            text-align: right;
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


    <?php
    // defining variables and set to empty values
    $fnameErr = $lnameErr = $addrErr = $phoneNoErr = "";
    $fname = $lname = $addr = $phoneNo = "";
    $flag = 0;
    //database part :
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

    //validating all the fields
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["fname"])) {
            $fnameErr = "First Name is required";
            $flag = 1;
        } else {
            $fname = $_POST["fname"];
            // check if fname only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z ]*$/", $fname)) {
                $fnameErr = "Only letters and white space allowed";
                $flag = 1;
            }
        }

        if (empty($_POST["lname"])) {
            $lnameErr = "Last Name is required";
            $flag = 1;
        } else {
            $lname = $_POST["lname"];
            // check if lname only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z ]*$/", $lname)) {
                $lnameErr = "Only letters and white space allowed";
                $flag = 1;
            }
        }

        if (empty($_POST["address"])) {
            $addrErr = "Address is required";
            $flag = 1;
        } else {
            $addr = $_POST["address"];
            // check if Address only contains letters, numbers and whitespace
            if (!preg_match("/^[0-9]+\ +[a-zA-Z. ]*$/", $addr)) {
                $addrErr = "Invalid Address, desired format is : 123 road city state";
                $flag = 1;
            }
        }

        if (empty($_POST["phoneNo"])) {
            $phoneNo = "";
        } else {
            $phoneNo = $_POST["phoneNo"];
            // check if Phone only has numbers, dash , opening closing braces and whitespaces
            if (!preg_match("/^\(+[0-9]{3}+\)+\ +[0-9]{3}+\-+[0-9]{4}+$/", $phoneNo)) {
                $phoneNoErr = "Invalid Phone no, desired format is :(123) 456-7891";
                $flag = 1;
            }
        }
    }

    ?>
    <! Creating form >
    <p><span class="error"></span></p>

    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <div style="padding-top: 70px">
            <p>
                <label>Fname</label><input type="text" name="fname" style="margin:15px">
                <span class="error">* <?php echo "<br>" . $fnameErr; ?></span>
                <br><br>
            </p>

            <p>
                <label>Lname</label><input type="text" name="lname" style="margin:15px">
                <span class="error">* <?php echo "<br>" . $lnameErr; ?></span>
                <br><br>
            </p>

            <p>
                <label>Address</label><input type="text" name="address" style="margin:10px">
                <span class="error">* <?php echo "<br>" . $addrErr; ?></span>
                <br><br>
            </p>

            <p>
                <label>Phone No</label><input type="text" name="phoneNo" style="margin:12px">
                <span class="error"><?php echo "<br>" . $phoneNoErr; ?></span>
                <br><br>
            </p>
            <input type="submit" name="submit" value="Submit"><br><br><br><br>
    </form>
</div>

<?php

// checking if the borrower details already exists in borrower table
$result11 = mysql_query("SELECT *
                    FROM BORROWER
                    WHERE Fname = '$fname' AND Lname = '$lname' AND Address = '$addr';");

if (!empty($result11)) {
    // checking if any such borrower exist then through error and stop
    if ($row11 = mysql_fetch_array($result11)) {
        if ($row11{'Fname'} != NULL) {
            echo "Error!! Borrower Details already exists";
            goto end;
        }
    } else {
        // Insert new borrowers detail in the database
        if ($flag == 1) {
            //flag variable is like a check to ensure no erroneous value is inserted in the database
            // if there is any error in input of any field, then don't allow database insertion
            $result1 = '';
            $flag = 0;
        } else {
            // else all the input field values are correct so insert it to the database.
            $result1 = mysql_query("INSERT INTO BORROWER (Fname, Lname, Address,Phone)
                        VALUES ('$fname', '$lname', '$addr', '$phoneNo');");
        }
        $result2 = mysql_query("SELECT Card_no
                            FROM BORROWER
                            WHERE Fname = '$fname' AND Lname = '$lname' AND Address = '$addr';");
        if ($row = mysql_fetch_array($result2)) {
            $generated_card_no = $row{'Card_no'};
            echo 'Borrower added Successfully !! Your card no is :' . $generated_card_no;
        } else {
            echo "Error!! Could not insert the new borrower entry in the table";
        }
    }
}
end :
$dbhandle->close;
?>
</div>
</body>
</html>