<?php

checklogin();

$email = AES('decrypt', $_COOKIE['ddeml']);
$User = new User($pdo);

$User->setUserEmail($email);

$user = $User->info();
$user_id = $User->getUserId();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if($_POST['password'] != '') {$User->setUserPassword($_POST['password']);}
    
    $User->setUserWebsite($_POST['site_tag'],$_POST['site_url']);
    
    header("Location: /account");
    exit;
}
  
$page = array('title' => 'Account');
require('includes/head.html');
?>


<body>
<?php require('includes/header.html'); ?>

    <main style="margin-top: 58px">
        <div class="container pt-4">
            <div class="card">
              <div class="card-header text-center py-3"> User Profile edit </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="container-sm">
                            <div class="row">
                                <div class="col-sm-5 col-md-6">
                                    <div class="form-outline mb-4">
                                        <input type="text" id="user_name" class="form-control" value="<?PHP echo $user['first_name'] . " " . $user['last_name'] ?>" disabled />
                                        <label class="form-label" for="user_name">Name</label>
                                    </div>
                                </div>
                                <div class="col-sm-5 offset-sm-2 col-md-6 offset-md-0">
                                  <div class="form-outline mb-4">
                                    <input type="text" class="form-control" value="<?PHP echo $user['api_key'] ?>" disabled />
                                    <label class="form-label">Api</label>
                                  </div>
                                </div>
                            </div>
                        </div>
                        <div class="container-sm">
                          <div class="row">
                            <div class="col-sm-5 col-md-6">
                        
                              <div class="form-outline mb-4">
                                <input type="text" id="user_email" class="form-control" value="<?PHP echo $user['email'] ?>" disabled />
                                <label class="form-label" for="user_email">Email</label>
                              </div>
                        
                            </div>
                            <div class="col-sm-5 offset-sm-2 col-md-6 offset-md-0">
                              <div class="form-outline mb-4">
                                <input type="text" id="user_role" class="form-control"
                                  value="<?PHP echo $user['role'] ?>"
                                  disabled />
                                <label class="form-label" for="user_role">Role</label>
                              </div>
                            </div>
                          </div>
                    </div>
                        <div class="container-sm">
                          <div class="text-center "><?PHP echo ($user['password'] != '') ? 'Change Password' : 'Set Password';   ?></div>
                          <small class="text-muted">
                            <font color="grey"> Minimum Length of Password : 8 </font>
                          </small>
                          <div class="row">
                            <div class="col-sm-5 col-md-6">
                              <div class="form-outline mb-4">
                                <input type="password" class="form-control" name="password" minlength="8" autocomplete="off" id="new_password" oninput="validatePassword()" />
                                <label class="form-label">New Password</label>
                              </div>
                            </div>
                            <div class="col-sm-5 offset-sm-2 col-md-6 offset-md-0">
                              <div class="form-outline mb-4">
                                <input id="retype_password" type="password" class="form-control" minlength="8" name="re_password" oninput="validatePassword()" />
                                <label class="form-label">Retype Password</label>
                              </div>
                            </div>
                          </div>
                    </div>
                        <div class="container-sm">
                          <div class="text-center">
                            Fill Your Tag or Site Details </div>
                          <small class="text-muted">
                            <font color="grey"> Site Name </font>
                          </small>
                          <div class="row">
                            <div class="col-sm-5 col-md-6">
                              <div class="form-outline mb-4">
                                <input type="text" class="form-control" name="site_tag" placeholder="DeadDrive" value="<?= $user['site_name'] ?>" />
                                <label class="form-label">Site Tag</label>
                              </div>
                            </div>
                            <div class="col-sm-5 offset-sm-2 col-md-6 offset-md-0">
                              <div class="form-outline mb-4">
                                <input type="text" class="form-control" name="site_url" placeholder="<?= NAME ?>" value="<?= $user['site_url'] ?>" />
                                <label class="form-label">Site URL</label>
                              </div>
                            </div>
                          </div>
                    </div>
                        <div class="container-sm">
                          <div class="text-center">
                            Referer Protection
                          </div>
                          <small class="text-muted">
                            <font color="grey"> Multiple Seperate by Comma </font>
                          </small>
                          <div class="row">
                            <div class="col-sm-5 col-md-6">
                              <div class="form-outline mb-4">
                                <input type="text" class="form-control" name="referer_url" value="" />
                                <label class="form-label">Allowed Domains List</label>
                              </div>
                            </div>
                            <div class="col-sm-5 offset-sm-2 col-md-6 offset-md-0">
                              <div class="mb-4">
                                <select class="select" name="file_dead" id="file_dead">
                                  <option value="redirect" >Redirect to Your Site</option>
                                  <option value="404" selected>Page Not Found</option>
                                </select>
                                <label class="form-label select-label">Select Protection Action</label>
                              </div>
                            </div>
                          </div>
                    </div>
                        <div class="container-sm">
                          <div class="text-center">
                        
                        
                                    <div class="mb-4">
                                      </div>
                                    <div class="mb-4">
                                      </div>
                                    <div class="mb-4">
                                      </div>
                                    <div class="mb-4">
                                      </div>
                                    <div class="mb-4">
                                      </div>
                                    <div class="mb-4">
                                      </div>
                                    <center><button id="submit_button" type="submit" class="btn btn-primary">Update Profile</button></center>
                        
                            </div>
                    </div>
                    </form>
                </div>
          </div>
        </div>
    </main>

<script>
  function validatePassword() {
    var newPassword = document.getElementById('new_password').value;
    var retypePassword = document.getElementById('retype_password').value;
    var retypePasswordField = document.getElementById('retype_password');
    var submitButton = document.getElementById('submit_button');

    if (newPassword !== retypePassword) {
      retypePasswordField.style.borderColor = 'red';
      submitButton.disabled = true;
    } else {
      retypePasswordField.style.borderColor = '';
      submitButton.disabled = false;
    }
  }
</script>

<?php require('includes/footer.html');?>

</body>
</html>