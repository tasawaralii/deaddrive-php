<?php

checklogin();

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "share") {
    
    $id = $_POST["id"];
    
    $email = AES('decrypt', $_COOKIE['ddeml']);
    $api_key = $pdo->query("SELECT api_key,user_id FROM users WHERE email = '$email'")->fetch();
    
    $result = fetchContent(API_URL . "?api=".$api_key['api_key']."&drive_id=$id");
    
    if($api_key['user_id'] == 2) {
        
        $r = json_decode($result, true);
        $drive_slug = "https://dbase.deaddrive.icu/api/drive-slug.php?drive="  . $r['driveId'] .  "&size=" . $r['size'] . "&slug=" . $r['key'] ."&key=deadtoonszylith";
        fetchContent($drive_slug);
    
    }
    
    echo $result;
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "extract" && isset($_POST["folder"])) {

    $folderId = $_POST["folder"];
    echo json_encode(['files' => GoogleDrive::fetchFilesFromFolder($folderId)]);
    exit;
    
}

$page = array('title' => 'Share');


$User = new User($pdo);
$User->setUserEmail(AES('decrypt', $_COOKIE['ddeml']));

$user = $User->info();

$user_id = $User->getUserId();

$links = [];

if($user_id == 2) {
        
    $links = json_decode(fetchContent("http://dbase.deaddrive.icu/api/deaddrive.php?key=deadtoonszylith"), true);
    if($links) {
      $links = array_map(function($link){return "https://drive.google.com/file/d/" . $link . "/view?usp=drivesdk";},$links);
    }
    
}

require_once('includes/head.html');


?>

<body>

<?php require_once('includes/header.html'); ?>

<main style="margin-top: 58px">
    <div class="container pt-4">
        <?php
            if($User->userWebsite()) {
        ?>
        <section class="mb-4">
            <div class="card">
                <div class="card-header text-center py-3"> Share </div>
                <div class="card-body">
                    <center><div id="custom_notice" class="mb-4"></div></center>
                    <div class="input-group mb-4">
                        <input type="text" class="form-control" id="folder" placeholder="Link folder" />
                        <button class="btn btn-primary" type="button" id="btn-folder" data-mdb-ripple-color="dark"> Extract Folder </button>
                        <!--<button type="button" class="btn btn-light" id="btn-pack" data-mdb-ripple-color="dark"> Pack </button>-->
                    </div>
                    <div id="pack_here"></div>
                    <div id="pack_data"></div>
                    <div id="packid"></div>
                    <div class="form-outline mb-4">
                        <textarea class="form-control" id="link" rows="4"><?= implode(PHP_EOL, $links) ?></textarea>
                        <label class="form-label">File Links</label>
                    </div>
                    <?= (!DEVELOPMENT_MODE) ? '<button class="btn btn-primary" id="shares">Share</button>' : ''?>
                </div>
            </div>
        </section>
        <div class="card mb-4" id="one" style="display: none;">
        <div class="card-body">
            <ul class="list-group" id="filesx"></ul>
        </div>
        </div>
        <div class="card" id="two" style="display: none;">
            <div class="card-body">
                <div class="form-outline mb-4">
                    <textarea class="form-control" id="data-link" rows="4"></textarea>
                    <label class="form-label">Url Links</label>
                </div>
        </div>
            <div class="card-body">
            <div class="form-outline mb-4">
                <textarea class="form-control" id="data-html" rows="4"></textarea>
                <label class="form-label">Url HTML</label>
            </div>
        </div>
        </div>
        <?php
            } else {echo showAddWebsiteHTML();}
        ?>
    </div>
</main>

<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>

<script>

  var sid = [];
  
  let custom_share = "<?= DEADDRIVE_DOMAIN ?>";
  var c_domain = custom_share ? custom_share : window.location.origin;
  var files = [],
  data = [];
  
  if(custom_share != window.location.origin){
      var cmsg = document.getElementById("custom_notice");
      cmsg.innerHTML = "You have enabled custom domain, make sure it redirected to our site.";
  }
  
  async function createpack(name) {
    let fid = null;
    if (sid.length >= 1) {
      fid = sid;
    }
    var formData = new FormData;
    formData.append("action", "create");
    formData.append("name", name);
    formData.append("fid", fid);
    var response = await fetch("/share.php", {
      method: "POST",
      body: formData
    }),
      data = "";
    try {
      data = await response.json()
    } catch (error) { }
    return data
  }

  function add() {
    var div3 = document.createElement('div');
    div3.setAttribute('class', 'input-group mb-4');
    div3.setAttribute('id', 'divpack');
    var input = document.createElement('input');
    input.setAttribute('class', 'form-control');
    input.setAttribute('type', 'text');
    input.setAttribute('name', 'pack_name');
    input.setAttribute('id', 'pack_name');
    input.setAttribute('placeholder', 'Enter Pack Name Here');
    var btn = document.createElement('button');
    btn.setAttribute('class', 'btn btn-light');
    btn.setAttribute('type', 'button');
    btn.setAttribute('id', 'create_pack');
    btn.innerHTML = 'Create Pack';
    btn.onclick = function () {
      this.disabled = true;
      let pack_name;
      if (document.getElementById("pack_name") !== null) {
        pack_name = document.getElementById("pack_name").value;
      }
      let today_date = new Date().toLocaleString('hi-IN');
      var pname = pack_name ? pack_name : today_date + ' Pack';
      if (pname.length <= 180) {
        createpack(pname).then(data => {
          if (data.url) {
            pack_id(data.id);
            var element = document.getElementById("divpack");
            element.style.display = "none";
            pack_func(data.url);
          }
        })
      }
    }
    div3.appendChild(input);
    div3.appendChild(btn);
    document.getElementById('pack_here').appendChild(div3)
    document.querySelectorAll('.form-outline').forEach((formOutline) => {
      new mdb.Input(formOutline).init();
    });
  }

  function pack_id(id) {
    var input = document.createElement("input");
    input.setAttribute("type", "hidden");
    input.setAttribute("id", "pack_id");
    input.setAttribute("name", "pack_id");
    input.setAttribute("value", id);
    document.getElementById("packid").appendChild(input);
  }

  function pack_func(pack_url) {
    if (pack_url) {
      var div4 = document.createElement('div');
      div4.setAttribute('class', 'input-group mb-4');
      var input = document.createElement('input');
      input.setAttribute('class', 'form-control');
      input.value = pack_url;
      var btn = document.createElement('button');
      btn.setAttribute('class', 'btn btn-outline-light');
      btn.setAttribute('type', 'button');
      btn.innerHTML = '<i class="fas fa-copy"> </i> Copy';
      btn.onclick = function () {
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand("copy");
      }
      div4.appendChild(input);
      div4.appendChild(btn);
      document.getElementById('pack_data').appendChild(div4)
      document.querySelectorAll('.form-outline').forEach((formOutline) => {
        new mdb.Input(formOutline).init();
      });
    }
  }
  
    
//   document.getElementById("btn-pack").onclick = function () {
//     this.disabled = true;
//     add();
//   };
  
  document.getElementById("btn-folder").onclick = function () {
      
    this.disabled = true;
    var url = new URL(document.getElementById("folder").value),
    folder = url.pathname.split("/folders/");
      
    if (folder.length == 2) {
        
        var form = new FormData;
        form.append("action", "extract");
        form.append("folder", folder[1]);
        fetch("/share.php", {
        method: "POST",
        body: form
        }).then(response => response.json()).then(result => {
        if (result.files.length >= 1) {
            if(document.getElementById("link").value !== "")
            {
              document.getElementById("link").value += "\n";  
            }
          result.files.forEach(function (a, b) {
            var link = result.files.length != b + 1 ? a.webViewLink + "\n" : a.webViewLink;
            document.getElementById("link").value += link
          });
          document.querySelectorAll(".form-outline").forEach(formOutline => {
            new mdb.Input(formOutline).init()
          })
        }
        this.disabled = false
        }).catch(error => {
        alert("Error: " + error.message);
        this.disabled = false
        })
    }
  };

  document.getElementById("shares").onclick = function () {
    this.disabled = true;
    
    splitLinks();
    
    var links = document.getElementById("link").value.split("\n"),
      lists = document.getElementById("filesx");
    files = [];
    data = [];
    lists.innerHTML = "";
    document.getElementById("data-link").value = "";
    document.getElementById("data-html").value = "";
    links.forEach(function (a, b) {
      if (a !== "") {
        try {
          var id = a.match(/https?:\/\/(?:[\w\-]+\.)*(?:drive|docs)\.google\.com\/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation|spreadsheets)\/d\/|spreadsheet\/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})/i)[1];
          files.push(id)
        } catch (e) {
          alert(e);
        }
      }
    });
    files.forEach(function (a, b) {
      var li = document.createElement("li"),
        q = document.createElement("div"),
        w = document.createElement("div");
      q.innerHTML = a;
      li.setAttribute("id", b);
      li.setAttribute("style", "color:#fff;");
      li.setAttribute("class", "list-group-item d-flex justify-content-between align-items-center");
      q.setAttribute("class", "a");
      w.setAttribute("class", "b");
      li.appendChild(q);
      li.appendChild(w);
      lists.appendChild(li)
    });
    document.getElementById("one").removeAttribute("style");
    share(0)
  };

  function share(index) {
    if (index >= files.length) {
      document.getElementById("shares").disabled = false;
      if (data.length >= 1) {
        data.forEach(function (a, b) {
          document.getElementById("data-link").value += idtourl(a.key, a.size, a.name) + "\n"
          document.getElementById("data-html").value += idtohtml(a.key, a.size, a.name) + "\n"
        });
        document.getElementById("two").removeAttribute("style");
        document.querySelectorAll(".form-outline").forEach(formOutline => {
          new mdb.Input(formOutline).init()
        })
      }
    } else {
      if (index - 1 >= 0) {
        document.getElementById(index - 1).classList.remove("active")
      }
      document.getElementById(index).classList.add("active");
      let p_id;
      if (document.getElementById("pack_id") !== null) {
        p_id = document.getElementById("pack_id").value;
      }
      var pacid = p_id ? p_id : "";
      var form = new FormData;
      form.append("action", "share");
      form.append("id", files[index]);
      form.append("pid", pacid);
      fetch("/share.php", {
        method: "POST",
        body: form
      }).then(response => response.json()).then(result => {
    //   alert(result);
        var target = document.getElementById(index).getElementsByClassName("a")[0];
        target.scrollIntoView(false);
        if (result.key) {
    target.innerHTML = result.name;
    var copy = document.getElementById(index).getElementsByClassName("b")[0];
    var changecolor = document.getElementById(index); // corrected typo here
    copy.setAttribute("style", "cursor: pointer;");
    copy.innerHTML = '<i class="far fa-clipboard"></i>';
    copy.onclick = function () {
        changecolor.style.background = "#ffa900";
        var dr = idtourl(result.key);
        navigator.clipboard.writeText(dr).then(() => {
            toastr("Alert", "Text copied to clipboard")
        }).catch(err => {
            toastr("alert", "error")
        })
    };
    data.push(result);
    sid.push(result.id);
}
 else {
          if (result.error) {
            target.innerHTML = result.message;
          } else {
            target.innerHTML = "Cannot Share File";
          }
        }
        share(index + 1)
      })["catch"](error => {
          error.text();
        alert(error);
        share(index + 1)
      })
    }
  }

  function toastr(title, message) {
    var toast = document.createElement("div");
    toast.innerHTML = `
	<div class="toast-header">
		<strong class="me-auto">${title}</strong>
		<button type="button" class="btn-close" data-mdb-dismiss="toast" aria-label="Close"></button>
	</div>
	<div class="toast-body">${message}</div>`;
    toast.classList.add("toast", "fade");
    document.body.appendChild(toast);
    var toastInstance = new mdb.Toast(toast, {
      stacking: true,
      hidden: true,
      width: "450px",
      position: "top-right",
      autohide: true,
      delay: 3e3
    });
    toastInstance.show()
  }

  function idtourl(id, size = false, name = false,) {
    if (name) {
      return `${name} [${formatBytes(size)}] \n${c_domain}/file/${id}\n`
    }
    return `${c_domain}/file/${id}`
  }
  
  function idtohtml(id, size = false, name = false) {
        if (name) {
            return `<a href="${window.location.origin}/file/${id}">${name} [${formatBytes(size)}]</a>`
        }
        return `${window.location.origin}/file/${id}`
    }

  function formatBytes(a, b = 2, k = 1024) {
    with (Math) {
      let d = floor(log(a) / log(k));
      return 0 == a ? "0 Bytes" : parseFloat((a / pow(k, d)).toFixed(max(0, b))) + " " + ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"][d]
    }
  }
  
  function splitLinks() {
      var linksInput = document.getElementById("link");
      var links = linksInput.value.split(",");
      var formattedLinks = links.map(function(link) {
        return link.trim();
      }).join("\n");
      linksInput.value = formattedLinks;
  }

// document.getElementById("shares").addEventListener("click", splitLinks);
</script>

<?php require_once('includes/footer.html'); ?>

</body>

</html>