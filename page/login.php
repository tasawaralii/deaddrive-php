<?php

require_once("config.php");

$user_auth = '';
if(isset($_COOKIE['ddeml'])) {
    header('Location: /dashboard');
    exit;
  }
  
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require('db.php');
    require('functions.php');
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to select user based on email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        setCookie('ddeml' , AES("encrypt", $email), time() + 8600*30, '/');
        header("Location: dashboard.php");
        exit(); // Stop further execution
    } else {
        $user_auth = "false";
    }
}

$page = array('title' => 'Login');
require('site_info.php');
require_once('includes/head.html');
echo "<body>";
require('includes/header-not-login.html');
?>

<div class="container pt-4" style="margin-top: 58px">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-md-8">

                        
            <center>
            

        
</center>
<?php
if($user_auth == "false") {
    echo '<div class="alert alert-danger" role="alert" data-mdb-color="danger"> Email is not registered or Password is not set. </div>';
}
?>
 <form class="bg-white rounded shadow-5-strong p-5" method="POST">
                <div class="form-outline mb-4">
                    <input name="email" style="color:black;" type="email" id="form1Example1" class="form-control" required />
                    <label style="color:#424242;" class="form-label" for="form1Example1">Email address</label>
                </div>
                <div class="form-outline mb-4">
                    <input name="password" style="color:black;" type="password" id="form1Example2" class="form-control" required />
                    <label style="color:#424242;" class="form-label" for="form1Example2"  >Password</label>
                </div>
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="form1Example3" checked disabled="" />
                        <label style="color:black;" class="form-check-label" for="form1Example3"> Remember me </label>
                    </div>
                                        <div class="form-group">
                        <div id="logincaptcha"></div>
                        <input id="logintoken" name="logintoken" type="hidden">
                    </div>
                    <input type="hidden" id="captcha-response" name="captcha-response" />
                                        
                </div>
                <button type="submit" name="signin" id="signin" class="btn btn-primary btn-block recaptcha" captcha-response="">Sign
                    in</button>

                                <div class="text-center mt-3">
                    <p style="color:black;">OR</p>
                    <a href="/check_auth" class="btn btn-block btn-danger"> <i class="fab fa-google-plus mr-2"></i> Sign in using Google+ </a>
                </div>
                
            </form>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&amp;render=explicit" async defer></script>

 <center>
     <br>
     <center>
            
        
</center> </center>
 

<script type="text/javascript">

    var l = window.location.pathname,
        e = l.split('/');

    if (e.length >= 2) {

        var n = (e.length > 2) ? 2 : 1,
            c = document.getElementById(e[n]);

        if (c) {
            c.classList.add('active')
        }

    }

</script>

</body>
   <?php
require_once('includes/footer.html') ?>
</html>