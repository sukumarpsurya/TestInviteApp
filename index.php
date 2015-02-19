<html>
<head>
<Title>Example Web Form</Title>
</head>
<body>
<form method="post" action="?action=add" enctype="multipart/form-data" >
    Last name <input type="text" name="lastName" id="lastName"/><br />
    First name <input type="text" name="firstName" id="firstName"/><br />
    E-mail address <input type="text" name="emailAddress" id="emailAddress"/><br />
    <input type="submit" name="submit" value="Submit" />
</form>
 
<?php
/* Connect to SQL Azure */
$server = "tcp:tcp:wbqa55dhmb.database.windows.net,1433"; 
$user = "suku@wbqa55dhmb";
$pass = "Stoked88!";
$database = "testinvapp";
 
$connectionoptions = array("Database" => $database, 
                           "UID" => $user, 
                           "PWD" => $pass);
 
$conn = sqlsrv_connect($server, $connectionoptions);
if($conn === false)
{
    die(print_r(sqlsrv_errors(), true));
}
 
if(isset($_GET['action']))
{
    if($_GET['action'] == 'add')
    {
        /*Insert data.*/
        $insertSql = "INSERT INTO RegistrationTbl (LastName, FirstName, Email, RegDate) VALUES (?,?,?,?)";
        $params = array(&$_POST['lastName'], 
                        &$_POST['firstName'], 
                        &$_POST['emailAddress'], 
                        date("Y-m-d"));
        $stmt = sqlsrv_query($conn, $insertSql, $params);
        if($stmt === false)
        {
            /*Handle the case of a duplicte e-mail address.*/
            $errors = sqlsrv_errors();
            if($errors[0]['code'] == 2601)
            {
                echo "The e-mail address you entered has already been used.<br />";
            }/*Die if other errors occurred.*/
            else
            {
                die(print_r($errors, true));
            }
        }
        else
        {
            echo "Registration complete.</br>";
        }
    }
}
 
/*Display registered people.*/
$sql = "SELECT * FROM RegistrationTbl ORDER BY LastName";
$stmt3 = sqlsrv_query($conn, $sql);
if($stmt3 === false)
{
    die(print_r(sqlsrv_errors(), true));
}
 
if(sqlsrv_has_rows($stmt3))
{
    print("<table border='1px'>");
    print("<tr><td>Last Name</td>");
    print("<td>First Name</td>");
    print("<td>E-mail Address</td>");
    print("<td>Registration Date</td></tr>");
    while($row = sqlsrv_fetch_array($stmt3))
    {
        $regDate = date_format($row['RegDate'], 'Y-m-d');
        print("<tr><td>".$row['LastName']."</td>");
        print("<td>".$row['FirstName']."</td>");
        print("<td>".$row['Email']."</td>");
        print("<td>".$regDate."</td></tr>");
    }
    print("</table>");
}
?>
</body>
</html>