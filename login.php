<?php
//signin.php
include 'connect.php';
include 'header.php';
?>
<h3>Sign in</h3>

<?php
//first, check if the user is already signed in. If that is the case, there is no need to display this page
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
    echo 'You are already signed in, you can <a href="signout.php">sign out</a> if you want.';
} else {
    if($_SERVER['REQUEST_METHOD'] != 'POST') {
        /*the form hasn't been posted yet, display it
          note that the action="" will cause the form to post to the same page it is on */
          if(isset($_GET['error'])) {
            if ($_GET['error'] == 1) {
                ?>
                <p class="login_error">Incorrect Email or Password. Please try again</p>
        <?php
            } else if ($_GET['error'] == 2) {
                ?>
                <p class="login_error">Error occurs when trying to login. Please try again</p>
        <?php
            }
            
          }
    ?>
        <form method="post" action="">
            Email: <input type="text" name="email" />
            Password: <input type="password" name="user_pass">
            <input type="submit" value="Sign in" />
        </form>
    <?php
    } else {
        /* so, the form has been posted, we'll process the data in three steps:
            1.  Check the data
            2.  Let the user refill the wrong fields (if necessary)
            3.  Varify if the data is correct and return the correct response
        */
        $errors = array(); /* declare the array for later use */
         
        if(!isset($_POST['email'])) {
            $errors[] = 'The email field must not be empty.';
        }
         
        if(!isset($_POST['user_pass'])) {
            $errors[] = 'The password field must not be empty.';
        }
         
        if(!empty($errors)) { /*check for an empty array, if there are errors, they're in this array (note the ! operator)*/
            print 'Uh-oh.. a couple of fields are not filled in correctly..';
            ?>
            <ul>
            <?php
            foreach($errors as $key => $value) /* walk through the array so all the errors get displayed */
            {
            ?>
                <li><?= $value ?></li> <-- this generates a nice error list -->
            <?php
            }
            ?>
            </ul>
            <?php
        }
        else {
            //the form has been posted without errors, so save it
            //notice the use of mysql_real_escape_string, keep everything safe!
            //also notice the sha1 function which hashes the password
            $sql = "SELECT 
                        id,
                        email
                    FROM
                        user
                    WHERE
                        email = '".mysql_real_escape_string($_POST['email'])."'
                    AND
                        password = '".sha1($_POST['user_pass'])."'";
                         
            $result = $db->query($sql);
            if($result === FALSE) {
                # heading back to the login page with the error code 2 indicates that 
                # there was problem when retrieving the login info
                # echo 'Something went wrong while signing in. Please try again later.';
                # print_r ($db->errorInfo); //debugging purposes, uncomment when needed
                header("Location: login.php?error=2"); // wrong password
                die();
            }
            else {
                //the query was successfully executed, there are 2 possibilities
                //1. the query returned data, the user can be signed in
                //2. the query returned an empty result set, the credentials were wrong
                if($result->rowCount() == 0) {
                    # heading back to the login page with the argument saying that the password/user is incorrect
                    header("Location: login.php?error=1"); // wrong password
                    die();
                }
                else {
                    //set the $_SESSION['signed_in'] variable to TRUE
                    $_SESSION['signed_in'] = true;
                     
                    //we also put the user_id and user_name values in the $_SESSION, so we can use it at various pages
                    foreach($result as $row) {
                        $_SESSION['user_email']  = $row['email'];
                        $_SESSION['user_fname']  = $row['fname'];
                        $_SESSION['user_lname']  = $row['lname'];
                    }
                     
                    echo 'Welcome, ' . $_SESSION['user_fname'] . ' ' . $_SESSION['user_lname'] . '. <a href="index.php">Proceed to the forum overview</a>.';
                }
            }
        }
    }
}
 
include 'footer.php';
?>