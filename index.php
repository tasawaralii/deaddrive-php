<?PHP 
require('functions.php');
require('config.php');
$page = array('title' => 'DeadDrive');
require_once('includes/head.html');
echo "<body>";

if(isset($_COOKIE['ddeml'])){
    $email = AES('decrypt', $_COOKIE['ddeml']);
    require('db.php');
    $user = userinfo($email, $pdo);
require('includes/header.html');
} else {
require('includes/header-not-login.html');
}
?>
<main style="<?php echo (!isset($_COOKIE['ddeml'])) ? "padding-left:0px;" : "" ; ?>margin-top: 58px">
<div class="container pt-4">
    <div class="card">
        <div class="card-body">
            
            
            
            
            <center>
                <h4><i class="fab fa-google-drive" style="color:red" ></i> <?= NAME ." ".VERSION." ".VERSION_RELEASE_DATE ?></h4>
                <hr />
                
                
                 
                <p><?= NAME ?> Released.Stay with us to get <a href="<?= CONTACT_TELEGRAM ?>">Notified about our New Release.</a></p>
                <p>
                    Your friends can download or play the video Through Multiple Servers including FilePress, Doodstream etc.
                </p>

                <p>Sharing Your Files For Free has Never been this Easy! Grow with Us. Join and Get full Control of your Data</p>

                <p>
                    <?= NAME ?> provide a unique solution to share google drive files without any issue.
                </p>
                <p>
                    Read More About <?= NAME ?> On Our <a href="/about-us"><b> About Us Page Here</b></a>.
                </p>
                
                
                                    

                <div class="container-fluid">
                    <?PHP if(!isset($_COOKIE['ddeml'])) {
         
          echo '<a href="/login.php" class="btn btn-block btn-danger ripple-surface" style="max-width: 300px;"> <i class="fab fa-google-plus mr-2"></i> Sign in using Google+ </a>';
            } else {
            echo '<a href="/dashboard.php" class="btn btn-block btn-info ripple-surface" style="max-width: 300px;">Go to Dashboard </a>';
           
           }
                    ?>

                                    </div>
                                    <hr>
                <?PHP require_once('includes/features.php') ?>
                <?PHP require_once('includes/footer.html') ?>               <br>
            </center>
        </div>
    </div>
</div>
<br />
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
<script type="text/javascript" src="/js/mdb.min.js"></script>


</body>

</html>