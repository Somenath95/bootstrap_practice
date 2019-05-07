<?php

    session_start();
    require 'connection.php';

    if(isset($_SESSION['carry_email'])) {
        echo "<script>location = 'welcome';</script>;";
    }
    
    if(isset($_POST['update_submit'])) {

        // Load Composer's autoloader
        require '../PHPMailerAutoload.php';
        require '../PHPMailer/class.phpmailer.php';

        $update_email = mysqli_real_escape_string($mysqli , $_POST['update_email']);
        $update_validate_otp = mt_rand(10000,99999);
        //generate Unique key for Register
        $key = md5(time().$update_validate_otp);
        // Code for Encrypting the Password
        $query_for_get_email = "SELECT id FROM registration WHERE Email_ID = '$update_email'";
        // $query_for_update_password = "UPDATE registration SET Password = '$hash_password' WHERE Email_ID = '$update_email'";
        $query_to_update_validation = "UPDATE registration SET Validation = '0',Validate_OTP ='$update_validate_otp',VKey = '$key',Password = '' WHERE Email_ID = '$update_email'";
        //My Login Credential
        $my_email = 'somenath.chakraborty@onclavesystems.com';
        $my_password = 'Welcome@123';
        //To Check whether the User Exists or not
        if(($mysqli->query($query_for_get_email)) && ($mysqli->affected_rows != 0)) {

            if(($mysqli->query($query_to_update_validation)) && ($mysqli->affected_rows != 0)){
                // Instantiation and passing `true` enables exceptions
                $mail = new PHPMailer(true);
                try {
                    
                    $mail->isSMTP();                                                // Set mailer to use SMTP
                    $mail->Host       = 'sg2plcpnl0036.prod.sin2.secureserver.net';                           // Specify main and backup SMTP servers
                    $mail->SMTPAuth   = true;                                       // Enable SMTP authentication
                    $mail->Username   = $my_email;                                  // SMTP username
                    $mail->Password   = $my_password;                               // SMTP password
                    $mail->SMTPSecure = 'ssl';                                      // Enable TLS encryption, `ssl` also accepted
                    $mail->Port       = 465;                                        // TCP port to connect to
                    //Recipients
                    $mail->setFrom($my_email , 'Somenath');
                    $mail->addAddress($update_email, 'User');     // Add a recipient
                    $mail->addReplyTo($my_email);
                    
                    // Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = 'Email Verification!';                            $mail->Body    = "
                                            Hello,<br><br>
                                            <p>Please Verify your email with this <b>OTP: </b>".$update_validate_otp.".</p><br>
                                            <p>Please click <a href='localhost/User_registration/php/validation?key=$key'>here</a> to give your OTP.</p>
                                        ";

                    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                    if ($mail->send()) {
                        echo "success";// location.href = 'otp_validation.php  
                    }else {
                        echo $mail->error;
                    }                   
                }catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }else {
                echo $mysqli->error;
            }
        
        }else {
            echo "error_email"; //Same Page  
        }
        exit;
    }    
?>




<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/forgotpw_page.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Play">
    </head>

    <body>
        <div class= forgot_password>
            <form action="forgotpw_page.php" method="post">
            <h2 style="color: white">Update Your Password</h2><br>
            
            <input type="text" name="update_email" placeholder="Email ID"><br><br><br><br>
            <input type="submit" name= "update_submit" value="Update Password"><br><br><br><br>

            Don't have an Account?<a href="registration_form" name="redirect_register">&nbsp; Sign Up</a>
            </form>
        </div>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"> </script>
        <script type="text/javascript">
                $('form').submit(function (e){
                    e.preventDefault();
                    var update_email = $("input[name=update_email]").val();
                    
                    $.ajax({
                        url : 'forgotpw_page.php',
                        method: 'POST',
                        data: {
                            update_submit    : 1,
                            update_email     : update_email
                        },
                        success: function(data){
                            console.log(data);
                            
                            switch (data) {
                                case 'success':
                                swal({
                                        text            :"Check! we have send an OTP and Link to your mail...",
                                        type            :"success",
                                        closeModal      :false
                                        }).then((v) => {
                                            setTimeout(function(){location = 'otp_validation';},150);
                                        });
                                    break;
                                
                                case 'error_email':
                                swal({
                                        text        :"Oops! This email is not present in our database",
                                        icon        :"warning",
                                        dangermode  :true
                                    });
                                    break;
                                // default:
                                //     location.reload();
                                //     break;
                            }
                        },
                        error: function(data){
                            alert("Error Occured");
                            console.log(data);                            
                        }
                    });
                });
        </script>
    </body>
    <script>
    </script>
</html>

