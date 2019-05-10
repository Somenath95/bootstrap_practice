<?php
    session_start();
    
    if(!isset($_SESSION['carry_email'])){
        echo "<script>location = 'user_login'</script>";
    }
            
    require 'connection.php';


    $welcome_email = $_SESSION['carry_email'];

    $query_to_find_name = "SELECT Name FROM registration WHERE Email_ID = '$welcome_email'";
            
    if(($result = $mysqli->query($query_to_find_name)) && ($mysqli->affected_rows != 0)) {
                    
        $fvalue = $result->fetch_array(MYSQLI_ASSOC);
    }
    
    
    
    if(isset($_POST['logout_submit'])) {
      
        //Removing the Session Variable
        session_unset();

        //Destroy the Current Session
        session_destroy(); 

        echo "logout";
        exit();
    }

    
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Welcome Page</title>
        <link rel="stylesheet" href="../css/welcome.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Play">
    </head>

    <body>
        <div class="welcome">
                <form action="welcome.php" method="post"><br><br><br><br>
                    <h2 style="color: white">Welcome <br><?php echo "$fvalue[Name]"; ?></h2><br><br>
                    <input type="submit" id="logout_submit" value="Log Out">
                </form>
        </div>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"> </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#logout_submit').on('click', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url : 'welcome.php',
                        method: 'POST',
                        data: {
                            logout_submit    : 1
                        },
                        success: function(data) {
                            swal({
                                        text        :"You are successfully Log Out...",
                                        icon        :"success",
                                        closeOnConfirm  :false
                            }).then((v) => {
                                        setTimeout(function(){location = 'user_login';},150);
                            });   
                        }
                    });
                });
            });
        </script>
    </body>
</html>