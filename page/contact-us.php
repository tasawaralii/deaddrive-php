<?php 
    

    $page = array('title' => '');
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
                    <div class="alert alert-success" role="alert" style="padding: 5px; width:90%;">
                        <h2><i class="fa fa-envelope"></i> Contact Us </h2>
                    </div>

                    <hr>
                    <h4>This our email contact : <?PHP echo CONTACT_EMAIL ?>

                    <hr>
                    <h4>File Reporting</h4>
                </center>
                <hr>
                <div>Please note that we deal only with messages that meet the following requirements:</div>
                <ul>
                    <li>Explain which copyrighted material is affected.</li>
                    <li>Please provide the exact and complete URL link.</li>
                    <li>If it a case of files with illegal contents, please describe the contents briefly in two or
                        three points.</li>
                    <li>Please write to us only in English</li>
                </ul>

                <hr>
                <div align="center">Please provide the file URL when filing a DMCA complain: e.g:
                  <?php echo DEADDRIVE_DOMAIN ?>/file/XxXxXxX <br><br>
                    Written notice should be sent to our designated agent as follows: - via email: <?PHP echo CONTACT_EMAIL ?></a>
                </div>

            </div>
  </div>
<?php
  require('includes/footer.html');
?>
  

    </div>
    <br>
</div>
  </main>
<script type="text/javascript" src="/js/mdb.min.js"></script>

  </body>
</html>