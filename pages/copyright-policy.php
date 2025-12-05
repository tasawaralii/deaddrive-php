<?php 
    
    
  $page = array('title' => 'Copyright Policy');
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
                    <div class="alert alert-warning" role="alert" style="padding: 5px; width:90%;">
                        <h2> <i class="fa fa-copyright"></i>
                            Copyright Policy </h2>
                    </div>
                </center>
                <hr>

                <p align="center"><strong><?PHP echo NAME ?> </strong> intends to fully comply with the Digital
                    Millennium Copyright Act ("DMCA"), including the notice and "take down" provisions, and to benefit
                    from the safe harbors immunizing <?PHP echo NAME ?> from liability to the fullest extent of the
                    law. NeoDrive reserves the right to terminate the account of any Member who infringes upon the
                    copyright rights of others upon receipt of proper notification by the copyright owner or the
                    copyright owner's legal agent. If you believe that your work has been copied in a way that
                    constitutes copyright infringement, or that your intellectual property rights have been otherwise
                    violated, please provide <?PHP echo NAME ?>'s Copyright Agent with the following information:<br />
                </p>
                <hr>
                <ul>
                    <li>An electronic or physical signature of the person authorized to act on behalf of the owner (the
                        "Complainant:) of the copyright or other intellectual property interest that has allegedly been
                        infringed.</li>

                    <li>A description of the copyrighted work or other intellectual property that the Complainant claims
                        has been infringed.</li>

                    <li>A description of where the infringing material or activity that the Complainant is located on
                        the Site, with enough detail that we may find it on the Site (e.g., Profile ID).</li>

                    <li>The name, address, telephone number and email address of the Complainant.-A statement by the
                        Complainant that upon a good faith belief the disputed use of the material or activity is not
                        authorized by the copyright or intellectual property owner, its agent or the law.</li>

                    <li>A statement by the Complainant made under penalty of perjury, that the Complainant is the
                        copyright or intellectual property owner or is authorized to act on behalf of the copyright or
                        intellectual property owner and that the information provided in the notice is accurate.</li>

                </ul>
                <hr>
                <p align="center"> Please provide the file URL when filing a DMCA complain: e.g:
                    <?PHP echo DEADDRIVE_DOMAIN ?>/file/XxXxXx Written notice should be sent to our designated agent as
                    follows: - via email: <?PHP echo CONTACT_EMAIL ?></p>
            
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