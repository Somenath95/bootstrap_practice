<?php
    session_start(); // code for opening a seesion

    require 'connection.php';

    if(isset($_SESSION['carry_email'])) {
        echo "<script>location = 'welcome';</script>;";
    }

    if(isset($_POST['login_submit'])) {

        $_SESSION['carry_email'] = mysqli_real_escape_string($mysqli , $_POST['login_email']);
        $login_email = $_SESSION['carry_email'];
        $login_password = mysqli_real_escape_string($mysqli , $_POST['login_password']);
        //$hash_password = password_hash($login_password, PASSWORD_DEFAULT);
        
        $query_for_existing_user = "SELECT Password,Validation from registration where Email_ID = '$login_email'";

        if(($result = $mysqli->query($query_for_existing_user)) && $mysqli->affected_rows != 0) {

            // Now chcking for pasword
            $fvalue = $result->fetch_assoc();
            $server_password = $fvalue['Password'];
            $email_validate = $fvalue['Validation'];
            if($email_validate != '0') {
               //To check whether the mail is validated or not
               if(password_verify($login_password , $server_password)) {
                    echo "success_login";
                    exit();
               } else {
                    echo "wrong_credential"; // Wrong Password
                    session_unset();
                    exit();    
               }
            }else {
                echo "account_validate"; //May be new Email, So go to Register page
                session_unset();
                exit();
            }
        } else {
            echo "invalid_email"; //May be new Email, So go to Register page
            session_unset();
            exit();
        }
    }
?>

                                        <!-- Code for Login Page -->

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/user_login.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Play">
    </head>

    <body>
        <div class="signin">
            <form action="user_login.php" method="post">
                <h2 style="color: white">Log In</h2><br><br>
                <input type="email" id="login_email" name="login_email" placeholder="Email ID" required><br><br><br>
                <input type="password" id="login_password" placeholder="Password" required><br><br>

                <input type="submit"  id ="login_submit" value="Log In" ><br><br><br>
                <!-- name="login_submit" -->
                 <div id="container">
                        <a href="forgotpw_page.php" style="margin-right:0px; font-size:13px; font-family=Tahoma, Geneva, sans-serif">Forgot Password?</a>                  
                 </div>
                 
                 <br><br><br><br><br>

                Don't have an Account?<a href="registration_form" name="redirect_register">&nbsp; Sign Up</a>
            </form>
        </div>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"> </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#login_submit').on('click', function(e) {
                    e.preventDefault();
                    var email = $('input[name=email]').val();
                    var email = $("#login_email").val();
                    var password = $("#login_password").val();

                    $.ajax({
                        url : 'user_login.php',
                        method: 'POST',
                        data: {
                            login_submit    : 1,
                            login_email     : email,
                            login_password  : password
                        },
                        success: function(data) {
                            console.log(data);
                            
                            switch(data) {
                                case "success_login":
                                    window.location = 'welcome';
                                    break;

                                case  "wrong_credential":
                                    swal({
                                        text        :"Oops! You have entered wrong credentials...",
                                        icon        :"warning",
                                        dangerMode  :true
                                    });   
                                    break;

                                case "account_validate":
                                    swal({
                                        text        :"You forget to validate your account... It's okay...",
                                        icon        :"warning",
                                        dangermode  :true
                                    });
                                    break;

                                case "invalid_email":
                                    swal({
                                        text            :"Sorry! we don't have your new account... Please sign up first",
                                        type            :"warning",
                                        closeOnConfirm  :false
                                    }).then((v) => {
                                        setTimeout(function(){location = 'registration_form';},150);
                                    });
                                    break;

                                default:
                                    swal({
                                        text            :"Argg! We don't know what have you done...",
                                        type            :"danger",
                                        closeOnConfirm  :false        
                                    });
                                    break;
                            }
                        }
                    });
                });
            });    
        </script> 
    </body>
</html>