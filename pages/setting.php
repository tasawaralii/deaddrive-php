<?php

checklogin();


$email = AES('decrypt', $_COOKIE['ddeml']);
$User = new User($pdo);
$User->setUserEmail($email);

$user = $User->info();
$user_id = $User->getUserId();

  
if($_SERVER['REQUEST_METHOD'] == "POST") {

    if($_POST['action'] == "embedIndwnld") {$User->toogleEmbedInDownloadPage();echo "1";exit;}

    if($_POST['action'] == "dwnldPlayer") {$User->toogleDownloadButtonInPlayer();echo "1";exit;}

    if ($_POST['action'] == "toggle") {
        
        if($_POST['type'] == "enable") {
            $User->toogleServerPermanently($_POST['server_id']);
        } else if($_POST['type'] == "disTem") {
            $User->toogleServerTemporarily($_POST['server_id']);
        } else if($_POST['type'] == "instant") {
            $User->toogleInstantDownloadButton();
        }
        echo json_encode(["status" => "success"]);
        exit;
    }
    
    if($_POST['action'] == "getDetails") {
        
        echo json_encode($User->getUserServerDetails($_POST['server_id']));
    
        exit;
    }
    
    if($_POST['action'] == "edit") {
        
        $User->setCustomServerName($_POST['server_id'], $_POST['server_name']);
        
        $User->setServerDomain($_POST['server_id'],$_POST['server_domain']);
        
        $api_fields = json_decode($_POST['api_fields'],true);
        
        if($api_fields['api'] != "") {
            $changeApi = $User->setServerApi($_POST['server_id'],$api_fields);
            if($changeApi['status'] == "success") {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode($changeApi);
            }
        }
        exit;
    }
    
    if($_POST['action'] == "moveServer") {
        echo json_encode($User->moveServer($_POST['server_id'],$_POST['direction']));
        exit;
    }
    
}
  
$apis = $User->UserApis("all");

$page = array('title' => 'Setting');
?>

    <div class="container pt-4">
        <div class="card">
            <?php if(!$User->userWebsite()) {
                echo showAddWebsiteHTML();
            } else {
            ?>
            <div class="card-header text-center py-3"> User Servers Setting </div>  
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <li>Show Download in Player
                            <label class="custom-switch">
                                <input id="dwnldPlayer" type="checkbox" class="custom-toggle" onclick="toogleDownloadInPlayer(event)" <?= $user['dwnldPlayer'] ? "checked" : "" ?>>
                                <span class="custom-slider round"></span>
                            </label>
                        </li>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <li>Show Player in Download Page
                            <label class="custom-switch">
                                <input id="embedIndwnld" type="checkbox" class="custom-toggle" onclick="embedIndwnld(event)" <?= ($user['embedInDwnld'] ? 'checked' : '' ) ?>>
                                <span class="custom-slider round"></span>
                            </label>
                        </li>
                    </div>
                
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <li>Enable Instant Download
                            <label class="custom-switch">
                                <input id="instantDownloadButton" type="checkbox" class="custom-toggle" onclick="toggleInstantDownload(event)" <?= ($user['instant_download'] ? 'checked' : '' ) ?>>
                                <span class="custom-slider round"></span>
                            </label>
                        </li>
                    </div>
</div>
                <hr>
                <div class="table-responsive" id="server_table">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th><strong>Order</strong></th>
                                <th><strong>Change</strong></th>
                                <th><strong>Name</strong></th>
                                <th><strong>Hide</strong></th>
                                <th><strong>Upload</strong></th>
                                <th><strong>Add Api</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($apis as $i => $a) { ?>
                        <tr data-server-id="<?= $a['server_id'] ?>">
                            <td class="server_order"><strong><?= $i+1 ?></strong></td>
                            <td class="change_order">
                            <span class="order-arrow" style="cursor: pointer;" data-direction="up"><i class="fa fa-arrow-up"></i> </span>
                            &nbsp;&nbsp;&nbsp;
                            <span class="order-arrow" style="cursor: pointer;" data-direction="down"><i class="fa fa-arrow-down"></i></span>
                            </td>
                            <td class="server_name"><?= $a['server_name'] ?></td>
                            <td class="disable-tem">
                                <label class="custom-switch">
                                    <input type="checkbox" class="custom-toggle" data-toggle-type="disTem" <?= $a['disTem'] ? "checked" : "" ?>>
                                    <span class="custom-slider round"></span>
                                </label>
                            </td>
                            <td class="disable">
                                <label class="custom-switch">
                                    <input type="checkbox" class="custom-toggle" data-toggle-type="enable" <?= $a['enable'] ? "checked" : "" ?>>
                                    <span class="custom-slider round"></span>
                                </label>
                            </td>
                            <td class="server_edit">
                                <button class="btn btn-danger btn-sm edit-btn">Edit</button>
                            </td>
                        </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    
<!--Modals-->
<div class="modal fade" id="serverDetailsModal" tabindex="-1" aria-labelledby="serverDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serverDetailsLabel">Server Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="serverDetailsForm">
                    <div class="mb-3">
                        <label class="form-label text-light"><strong>Original Name:</strong></label>
                        <input type="text" class="form-control" style="background: #424242" id="originalName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light"><strong>User Defined Name:</strong></label>
                        <input type="text" class="form-control" id="userDefinedName">
                        <label class="form-label text-light"><strong>Server Domain: </strong></label>
                        <input type="text" class="form-control" id="serverDomain" required>
                    </div>
                    <center>API Fields</center><hr>
                    <div id="apiFields"></div>
                    <div class="mb-3 center">
                        <center>
                            <a class="btn btn-success" id="apiLinkButton" href="#" target="_blank">Get API</a>
                        </center>
                    </div>
                </form>
            </div>
            <div class="modal-footer text-center">
                <center>
                    <button type="submit" class="btn btn-primary" id="saveChanges">Save Changes</button>
                </center>
            </div>
        </div>
    </div>
</div>


<style>

.custom-switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 24px;
}

/* Hide the default HTML checkbox */
.custom-switch .custom-toggle {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider specific to custom-switch class */
.custom-switch .custom-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: red;
  transition: 0.4s;
  border-radius: 34px;
}

.custom-switch .custom-slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 4px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

.custom-switch .custom-toggle:checked + .custom-slider {
  background-color: green;
}

.custom-switch .custom-toggle:checked + .custom-slider:before {
  transform: translateX(18px);
}

.custom-switch .custom-slider:before {
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
}

</style>


<script>

    document.addEventListener("DOMContentLoaded", loadEvents());


    function loadEvents() {
        document.querySelectorAll(".custom-toggle").forEach(toggle => {
            toggle.addEventListener("change", function (event) {
                event.preventDefault();
                const row = this.closest("tr");
                const serverId = row.dataset.serverId;
                const toggleType = this.dataset.toggleType;
                const previousState = this.checked;
                
                fetch("", {
                    method : "POST",
                    headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "server_id="+serverId+"&action=toggle&type="+toggleType
                })
                .then(response => response.json())
                .then(res => {
                    if(res.status != "success") {
                        this.checked = !previousState;
                        alert("Failed");
                    }
                });
            });
        });
        document.querySelectorAll(".edit-btn").forEach(button => {
            button.addEventListener("click", function () {
                const row = this.closest("tr");
                const serverId = row.dataset.serverId;
                
                fetch("", {
                    method : "POST",
                    headers : {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body : "server_id="+serverId+"&action=getDetails"
                })
                .then(response => response.json())
                .then(res => {
                    if(res.status == "success") {
                        document.getElementById("originalName").value = res.originalName;
                        document.getElementById("originalName").dataset.server_id = res.server_id;
                        document.getElementById("userDefinedName").value = res.userDefinedName;
                        document.getElementById("serverDomain").value = res.server_domain;
                        document.getElementById("apiLinkButton").href = res.apiLink;
                        
                        const apiFieldsContainer = document.getElementById("apiFields");
                        apiFieldsContainer.innerHTML = "";
                
                        Object.entries(res.api_fields).forEach(([key, value]) => {
                            const fieldDiv = document.createElement("div");
                            fieldDiv.className = "mb-3";
                            fieldDiv.innerHTML = `
                                <label class="form-label text-light"><strong>${key.replace("_", " ").toUpperCase()}:</strong></label>
                                <input type="text" class="form-control api-input" name="${key}" value="${value}">
                            `;
                            apiFieldsContainer.appendChild(fieldDiv);
                        });
                        
                        new bootstrap.Modal(document.getElementById("serverDetailsModal")).show();
                    }
                });
            });
        });
        document.getElementById("saveChanges").addEventListener("click",function(event) {
            
            const form = document.getElementById("serverDetailsForm");
            
            if (!form.checkValidity()) {
                alert("Fill required fields");
                return;
            }
            
            event.preventDefault();

            const serverId = document.getElementById("originalName").dataset.server_id;
            const userDefinedName = document.getElementById("userDefinedName").value;
            const server_domain = document.getElementById("serverDomain").value;
    
            let apiData = {};
            
            document.querySelectorAll(".api-input").forEach(input => {
                apiData[input.name] = input.value;
            });
            
            const formData = new URLSearchParams();
            formData.append("action", "edit");
            formData.append("server_id", serverId);
            formData.append("server_name", userDefinedName);
            formData.append("server_domain",server_domain);
            formData.append("api_fields", JSON.stringify(apiData));

            
            fetch("", {
                method: "POST",
                headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                },
                body: formData.toString()
                
            })
            .then(response => response.json())
            .then(res => {
                if(res.status == "success") {
                    location.reload();
                } else {
                    alert(res.message);
                }
            })
        });
        document.querySelectorAll(".order-arrow").forEach(arrow => {
            arrow.addEventListener("click", function() {
                 const row = this.closest("tr");
                 var server_id = row.dataset.serverId;
                 const direction = this.dataset.direction;  
                //  alert(server_id + " " + direction);
                fetch("", {
                    method : "POST",
                    headers : {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body : "server_id="+server_id+"&direction="+direction+"&action=moveServer"
                })
                .then(response => response.json())
                .then(res => {
                    if(res.status = "sucess"){
                        
                        fetch("")
                        .then(response => response.text())
                        .then(html => {
                            let parser = new DOMParser();
                            let newDoc = parser.parseFromString(html, "text/html");
                            let newTable = newDoc.getElementById("server_table");
                
                            let oldTable = document.getElementById("server_table");
                            oldTable.replaceWith(newTable);
                            loadEvents();
                        })
                        
                    } else {
                        alert(res.message);
                    }
                })
            });
        });
    }

    function embedIndwnld(event) {
        event.preventDefault();
        var formData = new FormData();
        formData.append("action", "embedIndwnld");
        fetch("", {
            method:"POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if(data == 1) {
                var checkbox = document.getElementById("embedIndwnld");
                checkbox.checked = !checkbox.checked;
            }
        })
    }

    function toogleDownloadInPlayer(event) {
        event.preventDefault();
        var formData = new FormData();
        formData.append("action", "dwnldPlayer");
        fetch("", {
            method:"POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if(data == 1) {
                var checkbox = document.getElementById("dwnldPlayer");
                checkbox.checked = !checkbox.checked;
            }
        })
    }
    
    function toggleInstantDownload(event) {
        event.preventDefault();
        var formData = new FormData();
        formData.append("action", "toggle");
        formData.append("type", "instant")
        fetch("", {
            method:"POST",
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if(res.status == "success") {
                var checkbox = document.getElementById("instantDownloadButton");
                checkbox.checked = !checkbox.checked;
            }
        })
    }

</script>