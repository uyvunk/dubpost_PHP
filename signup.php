<?php
//signup.php
include 'connect.php';
include 'header.php';
?>
<h3>Sign up</h3>
<p>Please fill the following fields and click Sign Up to create your account</p>
<?php
 
if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    /*the form hasn't been posted yet, display it
      note that the action="" will cause the form to post to the same page it is on */
?>
    <form method="post" action="">
        First Name: <input type="text" name="user_fname" /><br>
        Last Name: <input type="text" name="user_lname" /><br>
        Year of Birth: <input type="number" name="user_yob"><br>
        Email: <input type="text" name="user_email" /><br>
        Password: <input type="password" name="user_pass"><br>
        Confim Password: <input type="password" name="user_pass_check"><br>
        <input type="submit" value="Sign Up" />
     </form>
<?php
}
else
{
    /* so, the form has been posted, we'll process the data in three steps:
        1.  Check the data
        2.  Let the user refill the wrong fields (if necessary)
        3.  Save the data 
    */
    $errors = array(); /* declare the array for later use */

    # Check for validility of First Name and Last Name
    if (isset($_POST['user_fname']) && isset($_POST['user_lname'])) {
        if(!ctype_alnum ($_POST['user_fname']) || !ctype_alnum ($_POST['user_lname'])) {
            $errors[] = 'Invalid characters for First Name and/or Last Name';
        }
    } else {
        $errors[] = 'The First Name and Last Name fields cannot be empty.';
    }
    
    # Check for validility of age
    if (isset($_POST['user_yob'])) {
        $current_year = date('Y');
        $age = $current_year - $_POST['user_yob'];
        if ($age < 10 || $age > 110) {
            $errors[] = 'Invalid Year of Birth';
        }
    } else {
        $errors[] = 'Year of Birth cannot be empty';
    }

    # Check for validility of Email
    if(isset($_POST['user_email']))
    {
        # validate email
        if(!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL))
        {
            $errors[] = 'Invalid email address.';
        }
        if(strlen($_POST['user_email']) > 50)
        {
            $errors[] = 'The email address cannot be longer than 50 characters.';
        }
    }
    else
    {
        $errors[] = 'The Email field must not be empty.';
    }
     
    # Check for validility of Password
    if(isset($_POST['user_pass']) && isset($_POST['user_pass_check']))
    {
        if($_POST['user_pass'] != $_POST['user_pass_check'])
        {
            $errors[] = 'The two passwords did not match.';
        }
    }
    else
    {
        $errors[] = 'The password fields cannot be empty.';
    }
     
    if(!empty($errors)) /*check for an empty array, if there are errors, they're in this array (note the ! operator)*/
    {

        echo 'Uh-oh.. a couple of fields are not filled in correctly..';
        echo '<ul>';
        foreach($errors as $key => $value) /* walk through the array so all the errors get displayed */
        {
            echo '<li>' . $value . '</li>'; /* this generates a nice error list */
        }
        echo '</ul>';
    }
    else
    {
        # calculate the age
        $current_year = date('Y');
        $age = $current_year - $_POST['user_yob'];
        //the form has been posted without, so save it
        //notice the use of mysql_real_escape_string, keep everything safe!
        //also notice the sha1 function which hashes the password
        $sql = "INSERT INTO
                    user(email, password, fname ,lname, age)
                VALUES('" . mysql_real_escape_string($_POST['user_email']) . "',
                       '" . sha1($_POST['user_pass']) . "',
                       '" . mysql_real_escape_string($_POST['user_fname']) . "',
                       '" . mysql_real_escape_string($_POST['user_lname']) . "',
                       '" . mysql_real_escape_string($age) . "'
                       )";
                         
        $result = $db->query($sql);
        if(!$result)
        {
            //something went wrong, display the error
            echo 'Something went wrong while registering. Please try again later.';
            echo mysql_error(); //debugging purposes, uncomment when needed
        }
        else
        {
            echo 'Successfully registered. You can now <a href="login.php">log in</a> and start posting!';
        }
    }
}
 
include 'footer.php';
?>