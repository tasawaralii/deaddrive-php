<?php 
    require("config.php");
    
  $page = array('title' => 'About Us');
  require('includes/head.html');
  echo "<body>";
  require('includes/header-not-login.html');
?>
<main style="margin-top: 58px">
  <div class="container pt-4">

    <div class="card">

        <div class="card-body">
            <div class="container">

                <center>
                    <div class="alert alert-primary" role="alert" style="padding: 5px; width:90%;">
                        <h2> <i class="fa fa-info-circle"></i> About Us </h2>
                    </div>
                    <hr>

                </center>

                <center>
                    <p><?PHP echo NAME ?> provide a unique solution to share google drive files without any issue.
                        Sharing files has never been this easy. <?PHP echo NAME ?> is a simple place to share your Work
                        Project Video that is uploaded to your Google Drive, with your Friends, Colleague or with Your
                        Classmates. Your friends can download or play the video to learn and understand the project.
                        Additionally they can download the video too. We have designed very easy interface, Which will
                        be very friendly and Convenient to use. You can share your files with anyone using our platform
                        for free. Totally Clean & Responsive UI and easy to use. Anyone can share there files here for
                        free. </p>

                    <p> <?PHP echo NAME ?> also Provides Google Drive File Manager. Simple Plot to Manage and Share
                        Drive with your Friends! You have to Sign Up through google and allow our application the
                        Permissions to access and manage your google drive. With <?PHP echo NAME ?> you can share all your Personal Files completely Safe & Secure. Create short permalinks easily by browsing your
                        Google Drive files and folders. This application helping to keep your files accessible without
                        worrying about quota limits reached. </p>

                </center>

                <hr>
                <div id="#version3">


                    <h5 class="font-weight-bold text-light" align="left">New Updates : <?PHP echo NAME . "[" . VERSION . "]" ?></h5>




                    <hr>
                    <ul>
                        <li> Fully New Dark Theme and UI with Buttons and Animation </li>
                        <li> New Profile Panel with Download Folder Change Option </li>
                        <li> Dashboard Updated with Drive & Profile Details </li>
                        <li> Team Drive and Share Drive Support & Drive List & Browse Added </li>
                        <li> Bulk Delete & Export Links, Search Files & Files Panel Updated </li>
                        <li> Now You can simply Share Files from Drive & Share Drives </li>
                        <li> 403: User Rate Limit Exceeded - Fixed to Great Extent </li>
                        <li> New Layers of Backup Drive System Added to Keep Files Alive </li>
                        <li> You can set Password and can Login with Email & Password anytime </li>
                    </ul>
                    <hr>
                </div>
                <center>

                    <p>
                        First You need to Login to start sharing your files from Google Drive. Below you can visit our
                        Privacy Policies & Terms and Conditions pages you have to agree before using our services.
                    </p>
                </center>

            </div>
            <center>
  
<?php
  require('includes/footer.html');
?>

</main>
<script type="text/javascript" src="/js/mdb.min.js"></script>
</body>
</html>