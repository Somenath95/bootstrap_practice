<?php

    session_start();
    if(isset($_SESSION['carry_email'])) {
        echo "<script>location = 'welcome';</script>;";
    }

    require 'connection.php';
    if(isset($_POST['validate_submit'])) {
        
        $validate_otp = mysqli_real_escape_string($mysqli , $_POST["validate_otp"]);
        $conf_password = mysqli_real_escape_string($mysqli, $_POST["reg_user_conf_password"]);

        $password = mysqli_real_escape_string($mysqli, $_POST["reg_user_password"]);
        $hash_password =password_hash($password, PASSWORD_DEFAULT);
        $query_to_check_otp = "SELECT id FROM registration WHERE Validate_OTP = '$validate_otp'";
        $query_to_account_update = "UPDATE registration SET Validation = '1',Password = '$hash_password', Validate_OTP = NULL,VKey='' where Validate_OTP = '$validate_otp'";


        if(($result = $mysqli->query($query_to_check_otp)) && ($mysqli->affected_rows != 0)) {

            if($password != $conf_password) {
                    echo "wrong_password"; //Re-direct to OTP_Validation Page
            } else {
                if(($mysqli->query($query_to_account_update)) && ($mysqli->affected_rows != 0)){
                   echo "success"; //re-direct to User_Login Page
                } else {
                    echo "error"; //Re-direct to OTP_Validation Page
                }
            }
        } else {
            echo "wrong_otp"; //Re-direct to OTP_Validation Page
        }
        exit;
    }
   
?>


                           

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/otp_validation.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Play">
    </head>

    <body>
        
        <div class="otp">
                <form action="otp_validation.php" id="login" method="post">
                    <h2 style="color: white">Set Your Password</h2><br><br>
                    <input type="text" name="validate_otp" placeholder="Enter Your OTP"><br><br>
                    <input type="password" name="reg_user_password" placeholder="Password"><br><br>
                    <input type="password" name="reg_user_conf_password" placeholder="Confirm Password"><br><br><br><br>
                
                    <input type="submit" name="validate_submit" value="Submit"></a>
                </form>                                
        </div>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"> </script>
        <script type="text/javascript">
              $('form').submit(function(e) {
                    e.preventDefault();
                    var form = $('form');
                    if(form.valid()) {
                        var validate_otp = $("input[name=validate_otp]").val();
                        var reg_user_password = $("input[name=reg_user_password]").val();
                        var reg_user_conf_password = $("input[name=reg_user_conf_password]").val();

                        $.ajax({
                            url : 'otp_validation.php',
                            method: 'POST',
                            data: {
                                validate_submit         : 1,
                                validate_otp            : validate_otp,
                                reg_user_password       : reg_user_password,
                                reg_user_conf_password  : reg_user_conf_password
                            },
                            success: function(data) {
                                console.log(data);
                                
                                switch(data) {
                                    case "wrong_password":
                                        swal({
                                                text        :"Oops! Please Check Those Passwords agian...",
                                                icon        :"warning",
                                                dangerMode  :true
                                            });   
                                        break;

                                    case "success":
                                        swal({
                                                text            :"Great! Your Account is Registered...",
                                                icon            :"success",
                                                closeModal      :false
                                        }).then((v) => {
                                            setTimeout(function(){location = 'user_login';},150);
                                        });   
                                        break;

                                    case "error":
                                        swal({
                                                text        :"Oops! Something Wrong too...",
                                                icon        :"warning",
                                                dangerMode  :true
                                            });   
                                        break;

                                    case "wrong_otp":
                                        swal({
                                                text        :"Aha! You Have Entered a Wrong OTP...",
                                                icon        :"warning",
                                                dangerMode  :true
                                            });   
                                        break;

                                    default:
                                        swal({
                                                text        :"Oops! Something Wrong...",
                                                icon        :"warning",
                                                dangerMode  :true
                                        });   
                                        break;   
                                }
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
                validate_otp: {required: true} ,
                reg_user_password: {required: true} , 
                reg_user_conf_password: {required: true, equalTo: 'input[name=reg_user_password]' },
            }
        })
	});
</script>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.0/dist/jquery.validate.min.js"></script>
</html>