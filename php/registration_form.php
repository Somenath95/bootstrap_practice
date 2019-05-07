<?php

    session_start();

    require 'connection.php'; //Fetching the details from "connection.php" file
    // Load Composer's autoloader
    require '../PHPMailerAutoload.php';
    require '../PHPMailer/class.phpmailer.php';

    if(isset($_SESSION['carry_email'])) {
        echo "<script>location = 'welcome'</script>";
    }

    if (isset($_POST['reg_user_signup'])) {
        $reg_user_name = mysqli_real_escape_string($mysqli , $_POST["reg_user_name"]);
        $reg_user_age = mysqli_real_escape_string($mysqli , $_POST["reg_user_age"]);
        $reg_user_gender = mysqli_real_escape_string($mysqli , $_POST["reg_user_gender"]);
        $reg_user_address = mysqli_real_escape_string($mysqli , $_POST["reg_user_address"]);
        $reg_user_email = mysqli_real_escape_string($mysqli , $_POST["reg_user_email"]);
        $reg_user_contact = mysqli_real_escape_string($mysqli , $_POST["reg_user_contact"]);
        $reg_user_validate_otp = rand(10000,99999);
        //generate Unique key for Register
        $key = md5(time().$reg_user_name);

        //Qwery Execution for checking Same Email ID
        $query_to_check_email = "SELECT id FROM registration where Email_ID = '$reg_user_email'";
        
        //Qwery Execution for checking Same Contact No.
        $query_to_check_contact = "SELECT id FROM registration where Contact_No = '$reg_user_contact'";
        
        //Qwery for inserting value for New User

        $query_to_save = "insert into registration (Name,Age,Sex,Address,Email_ID,Contact_No,Validate_OTP,VKey) values ('$reg_user_name','$reg_user_age','$reg_user_gender','$reg_user_address','$reg_user_email','$reg_user_contact','$reg_user_validate_otp','$key')"; 

        
        //My Login Credential
        $my_email = 'somenath.chakraborty@onclavesystems.com';
        $my_password = 'Welcome@123';

        if ($mysqli->query($query_to_check_email) && $mysqli->affected_rows != 0) {
            echo 1;//Already Email Registered; Goto: registration_form
        }
        
    
        elseif($mysqli->query($query_to_check_contact) && $mysqli->affected_rows != 0) {
            echo 2;//Already No Registered; Goto: registration_form
        }

        
        //The proper way to run the query is "if(<connection_variable>,<query>){}"
        elseif($mysqli->query($query_to_save) ) {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = 2;                                        // Enable verbose debug output
            $mail->isSMTP();                                                // Set mailer to use SMTP
            $mail->Host       = 'sg2plcpnl0036.prod.sin2.secureserver.net';                           // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                                       // Enable SMTP authentication
            $mail->Username   = $my_email;                                  // SMTP username
            $mail->Password   = $my_password;                               // SMTP password
            $mail->SMTPSecure = 'ssl';                                      // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = 465;                                        // TCP port to connect to

            //Recipients
            $mail->setFrom($my_email , 'Somenath');
            $mail->addAddress($reg_user_email , $reg_user_name);     // Add a recipient
            $mail->addReplyTo($my_email);
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Email Verification!';
            $mail->Body    = "
                                Hello,<br><br>
                                <p>Please Verify your email with this <b>OTP: </b>".$reg_user_validate_otp.".</p><br>
                                <p>Please click <a href='localhost/User_registration/php/validation?key=$key'>Register Here</a> to give your OTP.</p>
                            ";

            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            if ($mail->send()) {
                
                echo 3; //OTP and Link Send; Goto Otp_validation  
            }
            
            else {
                echo $mail->error;
            }
            
    
        } 
    
        catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    
    else {
        echo $mysqli->error;
    }
    exit;
    
}
?>





                                <!--HTML code for registration form-->
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/registration_form.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Play">
    </head>
    

    <body>
        <div class="registration">
            <form action="registration_form.php" id = "login" method="post">
                <h2 style="color: white">Register Here</h2><br>
                <input type="text" name="reg_user_name" placeholder="User Name"><br><br>
                    
                <input type="number" name="reg_user_age" placeholder="Age"><br><br>
                <center>
                <table>
                    <tr><td rowspan="3" style="vertical-align: center" >Gender:</td> 
                    <td style="text-align: left"><input type="radio" name="reg_user_gender" value="Male" class="gender" checked required><label> Male </label></td></tr>
                    <tr><td  style="text-align: left"><input type="radio" name="reg_user_gender" value="Female" class="gender" required>Female </td></tr>
                    <tr><td  style="text-align: left"><input type="radio" name="reg_user_gender" value="Other"  class="gender" required >Other </td></tr>
                </table> 
                </center><br>
                       
                
                <input type="text" name="reg_user_address" placeholder="Address" ><br><br>
                
                <input type="email" name="reg_user_email" placeholder="Email ID"><br><br>

                <input type="number" name="reg_user_contact" placeholder="Contact No."><br><br><br><br>
               
                <input type="submit" name="reg_user_signup" value="Sign Up"><br><br>
                Already have an Account?<a href="user_login" name="redirect_login">&nbsp; Log In</a>
            </form>
        </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"> </script>
    <script type="text/javascript">
        $('form').submit(function(e) {
            e.preventDefault();
            var form = $('form');
            if (form.valid()) {
                var reg_user_name = $("input[name=reg_user_name]").val();
                var reg_user_age = $("input[name=reg_user_age]").val();
                var reg_user_gender = $("input[name=reg_user_gender]").val();
                var reg_user_address = $("input[name=reg_user_address]").val();
                var reg_user_email = $("input[name=reg_user_email]").val();
                var reg_user_contact = $("input[name=reg_user_contact]").val();
                $.ajax({
                    url     :"registration_form.php",
                    method  :"POST",
                    data    :{
                        reg_user_signup :1,
                        reg_user_name            :reg_user_name,
                        reg_user_age             :reg_user_age,
                        reg_user_gender          :reg_user_gender,
                        reg_user_address         :reg_user_address,
                        reg_user_email           :reg_user_email,
                        reg_user_contact         :reg_user_contact
                    },
                    success :function(data) {
                        console.log(data);
                        switch (data) {
                            case '1':
                                swal({
                                    text            :"Sorry! This email is already registered...",
                                    icon            :"error",
                                    closeModal  :false
                                });                                
                                break;
                            
                            case '2':
                                swal({
                                    text            :"Sorry! This no. is already registered...",
                                    icon            :"error",
                                    closeModal  :false
                                });                                
                                break;

                            case '3':
                                swal({
                                    title           :"Please Check Mail",
                                    text            :"An OTP And Link Has Been Sent...",
                                    icon            :"success",
                                    closeModal  :false
                                }).then((v) => {
                                    setTimeout(function(){location = 'otp_validation';},150);
                                });                                
                                break;    
                            default:
                            
                            
                                swal('Invalid Request');
                                break;
                        }
                    },
                    error   :function(data) {
                        swal('Something Went Wrong... Please try again...');
                    }
                });
            }
        });
    </script>
    </body>
    <script>
	$(function() {
		// highlight
		
        var elements = $("input[type!='submit']");
		elements.focus(function() {
			$(this).parents('li').addClass('highlight');
		});
		elements.blur(function() {
			$(this).parents('li').removeClass('highlight');
		});

		$("#login").validate({
            rules: {
                reg_user_name: {required: true},
                reg_user_age: {required: true, min:17, max:100},
                reg_user_address: {required: true}, 
                reg_user_email: {required: true},
                reg_user_contact: {required: true,minlength:10, maxlength:10},
            }
        })
	});
    </script>

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.0/dist/jquery.validate.min.js"></script>
</html>

