<?php
    require 'connection.php';

    if(isset($_GET['key']) || isset($_POST['login_submit']) ) {
        if (isset($_GET['key'])) {
            $key = $mysqli->real_escape_string($_GET['key']);
            $query = "SELECT id FROM registration WHERE Validation = '0' AND VKey = '$key'";
            if(!(($result = $mysqli->query($query)) && ($mysqli->affected_rows != 0))){
                echo 2;//Link Expired
            }    
        }
        if (isset($_POST['login_submit'])) {
            
            $password = password_hash($mysqli->real_escape_string($_POST['password']), PASSWORD_DEFAULT);
            $token = $mysqli->real_escape_string($_POST['token']);
            
            $update = "UPDATE registration SET Password='$password', Validate_OTP='0', Validation='1', VKey='' WHERE VKey='$token'";
            if ($mysqli->query($update)) {
                echo 1;
            } else {
                echo $mysqli->error;
            }
            exit;
        }
    } else {
        header('location: user_login');
    }
?>

<!DOCTYPE html>
<html>
<head>
    <!-- <link rel="stylesheet" href="../css/validation.css"> -->
    <link rel="stylesheet" href="../css/user_login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Play">
</head>
<body>
    <div class='signin'>
        <form action="validaion.php" id="login" method="post">
            <h2 style="color: white">Update Your Password</h2><br><br><br>

            <input type="hidden" id="token" value="<?= $key?>">
            <input type="password" name="password" id="password" placeholder="Password" required><br><br>
            <input type="password" name="conf_password" placeholder="Confirm Password" required><br><br><br>
            <input type="submit" id="update" value= "Update">
        </form>
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"> </script>
    <script type="text/javascript">

            $('form').submit(function (e){
                e.preventDefault();
                var form = $('form');
                if (form.valid()) {
                    // console.log('Submitted');
                    var password = $("#password").val();
                    var token = $("#token").val();
                    $.ajax({
                        url: "validation.php",
                        method: "POST",
                        data: {
                            login_submit   : 1,
                            password       : password,
                            token          : token
                        },
                        success: function(data){
                            console.log(data);
                            
                            switch (data) {
                                case '1':
                                swal({
                                    text            :"Great! Your Password Has Been Updated...",
                                    icon            :"success",
                                    closeOnConfirm  :false
                                    }).then((v) => {
                                        setTimeout(function(){location = 'user_login';},150);
                                    });                                       
                                    break;
                                case '2':
                                swal({
                                    text            :"Link Has Been Expired ...",
                                    icon            :"error",
                                    closeOnConfirm  :false
                                    });   
                                    break;
                                default:
                                    swal('Invalid Request');
                                    break;
                                }
                        },
                        error: function(data){
                            swal('Something Went Wrong, Please Try Again Later');
                        }
                    })
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
                password: {     required: true,     } , 
                conf_password: {required: true, equalTo: '#password' },
            }
        })
	});
</script>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.0/dist/jquery.validate.min.js"></script>
</html>