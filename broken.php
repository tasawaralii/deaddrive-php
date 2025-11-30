<?php 
require('functions.php');
checklogin();
require('db.php');
$page = array('title' => 'Files');

$user = userinfo(AES('decrypt', $_COOKIE['ddeml']), $pdo);
$user_id = $user['user_id'];
$pgsize = isset($_COOKIE['fpp']) ? $_COOKIE['fpp'] : (setcookie('ffp', '10', time() + 86000 * 30, '/') ? '10' : '');
$pgnum = (isset($_GET['page'])) ? $_GET['page'] : 1;
$offset = $pgnum != 1 ? ($pgnum - 1) * $pgsize : 0;
require('site_info.php');

$filter = '';

$search = isset($_GET['search']) ? $_GET['search'] : "Name" ;

$filter .= isset($_GET['keyword']) ? "AND $search LIKE '%".$_GET['keyword']."%'" : '' ;

$sortby = isset($_GET['filter']) ? $_GET['filter'] : 'new_date';

$order = isset($_GET['order']) ? $_GET['order'] : "DESC" ;


$files = $pdo->query("SELECT * FROM links_info WHERE user = $user_id AND live = 0 $filter ORDER BY $sortby $order LIMIT $pgsize OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
$total_files = $pdo->query("SELECT COUNT(*) AS total FROM links_info WHERE user = $user_id")->fetchColumn();
# print_r($files);
require('includes/head.html');
echo "<body>";
require('includes/header.html');
?>
<main style="margin-top: 58px">
    <div class="container pt-4">

        <div class="mb-4">

            <form method="GET">

                <div class="input-group">
                    <input name="keyword" type="text" class="form-control" placeholder="Keyword"  autocomplete="off" />
                    <select name="search" class="select">
                        <option value="Name" >Name</option>
                        <option value="uid" >File ID</option>
                    </select>
                    <select name="order" class="select">
                        <option value="DESC" >DESC</option>
                        <option value="ASC" >ASC</option>
                    </select>
                    <button class="btn btn-primary" type="submit" data-mdb-ripple-color="dark">
                        Search
                    </button>
                </div>

            </form>

        </div>

        <div class="card">
            <div class="card-body">
<p class="mb-0">
  <center> <button id="panel" data-mdb-toggle="modal" data-mdb-target="#Panel" class="btn btn-info"
                      style="padding: .2rem 1rem;font-size: .7rem; line-height: 1.8;">Files Per Page</button></center>
  <hr> </p>
             
              
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <input class="form-check-input" type="checkbox" id="select_all" />
                                </th>
                                <th>Copy</th>
                                <th scope="col"><a href="/files?filter=Name&order=ASC">Name</a></th>
                                <th scope="col">Size</th>
                                <th scope="col">Downloads</th>
                                <th scope="col">Views</th>
                                <th scope="col">Create at</th>
                                <th scope="col">Edit</th>
                                <th scope="col">Logs</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?PHP foreach($files as $file) { ?>
                                <tr>
                                <th scope="row">
               <input class="form-check-input" type="checkbox" id="select" value="<?PHP echo $file['uid'] ?>"
                                fname="<?PHP echo $file['Name'] ?>" fsize="<?PHP echo $file['size'] ?>" />
                                </th>
                                <td><div style="cursor: pointer;" onclick="copy('<?PHP echo $file['uid'] ?>')"><i class="fa fa-copy" ></i></div></td>
                                <td>
                                    <a href="/file/<?PHP echo $file['uid'] ?>" class="name">
                                        <span data-mdb-toggle="tooltip" title="<?PHP echo $file['Name'] ?>"><?PHP echo $file['Name'] ?></span>
                                    </a>
                                </td>
                                <td><?PHP echo formatBytes($file['size']) ?></td>
                                <td><?PHP echo $file['downloads'] ?></td>
                                <td><?PHP echo $file['views'] ?></td>
                                <td><?PHP echo $file['new_date'] ?></td>
                                <td>
                                    <button class="btn btn-primary"
                                        onclick="rename('<?PHP echo $file['uid'] ?>', '<?PHP echo $file['Name'] ?>');" data-mdb-toggle="modal"
                                        data-mdb-target="#Rename"><i class="fas fa-pen"></i></button>
                                </td>
                                <td>
                                    <button class="btn btn-danger" onclick="showLogs('<?= $file['uid'] ?>')">Logs</button>
                                </td>
                            </tr>
                                                        <tr>
                                 <?PHP } ?>
                          </tbody>
                    </table>
                </div>

              <center>  <div style="margin: 1rem;" id="page">
                    <nav aria-label="Page navigation example">
                        <center><ul class="pagination" id="pagination"></ul></center>
                    </nav>
                </div>

            </div>
        </div></center>

        <div class="mt-4 mb-4" id="menu" style="display: none;">
            <button class="btn btn-light" id="link" data-mdb-toggle="modal" data-mdb-target="#Link">Link</button>
            <button class="btn btn-light" id="export" data-mdb-toggle="modal" data-mdb-target="#Export">Export</button>
            <button class="btn btn-danger" id="delete" data-mdb-toggle="modal" data-mdb-target="#Delete">Delete</button>
            <button class="btn btn-primary" id="pack" onclick="pack();" data-mdb-toggle="modal"
                data-mdb-target="#Pack">Pack</button>
                        <button class="btn btn-primary" id="count" onclick="backup_count();">Count</button>
        </div>

    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="Pack" tabindex="-1" aria-labelledby="PackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="PackModalLabel">Alert</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/admin/all-files">
                <div class="modal-body">
                    <input type="hidden" name="action" value="pack">
                    <div id="packmsg" class="mb-4"></div>
                    <div id="packlist"></div>
                                        <select class="form-select" aria-label="Default select example" name="pid" id="pid">
                                                <option value="3731">Tt</option>
                                            </select>
                    

                </div>
                <div class="modal-footer">
                    <a class="btn btn-light" href="/packs">Create Pack</a>
                    <button type="button" class="btn btn-light" data-mdb-dismiss="modal"> Close </button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!--Modal Logs-->

<div class="modal fade" id="fileDetailsModal" tabindex="-1" aria-labelledby="fileDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileDetailsLabel">File Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body">
                <h6 id="fileName"></h6>
                <h6 id="updateDate"></h6>
                <ul id="serverDetails" class="list-group"></ul>
            </div>
        </div>
    </div>
</div>


<!-- Modal Backup -->
<div class="modal fade" id="Backup" tabindex="-1" aria-labelledby="BackupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="BackupModalLabel">Alert</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/admin/all-files">

                <div class="modal-body">
                    <input type="hidden" name="action" value="backup">
                    <div id="Backupmsg" class="mb-4"></div>
                    <div id="Backuplist"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-mdb-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="Delete" tabindex="-1" aria-labelledby="DeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DeleteModalLabel">Alert</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/admin/all-files">

                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <div id="Deletemsg" class="mb-4"></div>
                    <div id="Deletelist"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-mdb-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-danger">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Export -->
<div class="modal fade" id="Export" tabindex="-1" aria-labelledby="ExportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ExportModalLabel">Links</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="form-outline mb-4">
                    <textarea class="form-control" id="export-link" rows="4"></textarea>
                    <label class="form-label">Export Files</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-mdb-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Link -->
<div class="modal fade" id="Link" tabindex="-1" aria-labelledby="LinkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="LinkModalLabel">Links</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-outline mb-4">
                    <textarea class="form-control" id="data-link" rows="4"></textarea>
                    <label class="form-label">Shared Links</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-mdb-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Files Per Page -->
<div class="modal fade" id="Panel" tabindex="-1" aria-labelledby="PanelModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="PanelModalLabel">Panel UI Settings</h5>
        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
      </div>
      
        <div class="modal-body">
          <input type="hidden" name="action" value="panel">
          <div class="form-outline mb-4">
            <input type="email" id="email" class="form-control" value="<?PHP echo $user['email'] ?>" disabled />
            <label class="form-label">Email</label>
          </div>
          <div class="mb-4">
           <select id="plimit" class="select">
                            <option value="10" >10</option>
                            <option value="11" >11</option>
                            <option value="12" >12</option>
                            <option value="13" >13</option>
                            <option value="14" >14</option>
                            <option value="15" >15</option>
                            <option value="16" >16</option>
                            <option value="17" >17</option>
                            <option value="18" >18</option>
                            <option value="19" >19</option>
                            <option value="20" >20</option>
                            <option value="21" >21</option>
                            <option value="22" >22</option>
                            <option value="23" >23</option>
                            <option value="24" >24</option>
                            <option value="25" >25</option>
                            <option value="26" >26</option>
                            <option value="27" >27</option>
                            <option value="28" >28</option>
                            <option value="29" >29</option>
                            <option value="30" >30</option>
                            <option value="31" >31</option>
                            <option value="32" >32</option>
                            <option value="33" >33</option>
                            <option value="34" >34</option>
                            <option value="35" >35</option>
                            <option value="36" >36</option>
                            <option value="37" >37</option>
                            <option value="38" >38</option>
                            <option value="39" >39</option>
                            <option value="40" >40</option>
                            <option value="41" >41</option>
                            <option value="42" >42</option>
                            <option value="43" >43</option>
                            <option value="44" >44</option>
                            <option value="45" >45</option>
                            <option value="46" >46</option>
                            <option value="47" >47</option>
                            <option value="48" >48</option>
                            <option value="49" >49</option>
                            <option value="50" >50</option>
                            <option value="51" >51</option>
                            <option value="52" >52</option>
                            <option value="53" >53</option>
                            <option value="54" >54</option>
                            <option value="55" >55</option>
                            <option value="56" >56</option>
                            <option value="57" >57</option>
                            <option value="58" >58</option>
                            <option value="59" >59</option>
                            <option value="60" >60</option>
                            <option value="61" >61</option>
                            <option value="62" >62</option>
                            <option value="63" >63</option>
                            <option value="64" >64</option>
                            <option value="65" >65</option>
                            <option value="66" >66</option>
                            <option value="67" >67</option>
                            <option value="68" >68</option>
                            <option value="69" >69</option>
                            <option value="70" >70</option>
                            <option value="71" >71</option>
                            <option value="72" >72</option>
                            <option value="73" >73</option>
                            <option value="74" >74</option>
                            <option value="75" >75</option>
                            <option value="76" >76</option>
                            <option value="77" >77</option>
                            <option value="78" >78</option>
                            <option value="79" >79</option>
                            <option value="80" >80</option>
                            <option value="81" >81</option>
                            <option value="82" >82</option>
                            <option value="83" >83</option>
                            <option value="84" >84</option>
                            <option value="85" >85</option>
                            <option value="86" >86</option>
                            <option value="87" >87</option>
                            <option value="88" >88</option>
                            <option value="89" >89</option>
                            <option value="90" >90</option>
                            <option value="91" >91</option>
                            <option value="92" >92</option>
                            <option value="93" >93</option>
                            <option value="94" >94</option>
                            <option value="95" >95</option>
                            <option value="96" >96</option>
                            <option value="97" >97</option>
                            <option value="98" >98</option>
                            <option value="99" >99</option>
                            <option value="100" >100</option>
              
            </select>
            <label class="form-label select-label" for="status">Page Size</label>
          </div>
          
          

          <div class="mb-4">Applied settings will be Saved in Browser.</div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-mdb-dismiss="modal"> Close </button>
          <button onclick="setCookie()" class="btn btn-primary">Apply</button>
        </div>
      
    </div>
  </div>
</div>

<!-- Modal Rename -->
<div class="modal fade" id="Rename" tabindex="-1" aria-labelledby="RenameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="RenameModalLabel">Alert</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="/admin/all-files">
                <div class="modal-body">
                    <div class="form-outline">
                        <input type="hidden" name="action" value="rename">
                        <input type="text" id="n" name="rename" class="form-control" />
                        <label class="form-label" for="n">Name</label>
                        <input type="hidden" id="i" name="id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-mdb-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Rename</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script type="text/javascript">
  let custom_share = "<?PHP echo $site['domain'] ?>";
  var c_domain = custom_share ? custom_share : window.location.origin;
  var total = <?PHP echo ($result = intval($total_files / $pgsize));?>,
    page = <?PHP echo $pgnum ?>,
    prev = document.createElement("button"),
    next = document.createElement("button"),
    ids = [],
    html = "";

function pagination(index, size) {
    for (var i = index; i < size; i++) {
        if (page == i) {
            html += '<li class="page-item disabled"><a class="page-link" href="#">' + i + "</a></li>";
        } else {
            html += '<li class="page-item"><a class="page-link" href="' + urlpage(i) + '">' + i + "</a></li>";
        }
    }
}

function paginationlast() {
    html += '<li class="page-item"><a class="page-link" href="' + urlpage(total) + '">' + total + '</a></li>';
}

function paginationfirst() {
    html += '<li class="page-item"><a class="page-link" href="' + urlpage(1) + '">1</a></li>';
}

function paginationbuild() {
    if (total < 3 * 2 + 6) {
        pagination(1, total + 1);
    } else if (page == 1) {
        pagination(1, 4);
        html += '<li class="page-item"><a class="page-link" href="#">...</a></li>';
        paginationlast();
    } else if (page < 3 * 2 + 1) {
        pagination(1, 3 * 2 + 2);
        html += '<li class="page-item"><a class="page-link" href="#">...</a></li>';
        paginationlast();
    } else if (page > total - 3 * 2) {
        paginationfirst();
        html += '<li class="page-item"><a class="page-link" href="#">...</a></li>';
        pagination(total - 3 * 2 - 1, total + 1);
    } else {
        paginationfirst();
        html += '<li class="page-item"><a class="page-link" href="#">...</a></li>';
        pagination(page - 1, page + 2);
        html += '<li class="page-item"><a class="page-link" href="#">...</a></li>';
        paginationlast();
    }
}

function paginationcreate() {
    paginationbuild();
    document.getElementById("pagination").innerHTML = html;
}
paginationcreate();

function urlpage(halaman) {

    var url = new URL(window.location),
        keyword = url.searchParams.get('keyword'),
        search = url.searchParams.get('search'),
        order = url.searchParams.get('order');

    if (search && order) {

        return '?search=' + search + '&keyword=' + keyword + '&order=' + order + '&page=' + halaman;

    }

    return "?page=" + halaman;
}

</script>

<script>

    function showLogs(id) {
        
        fetch("/ajax/show-logs", {
            "method" : "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            "body" : "uid=" + id
        }).then(response => response.json())
            .then(res => {
            if(res.status == "success") {
                
                var responseData = JSON.parse(res.logs);
                
                document.getElementById("fileName").textContent = responseData.Name[0];
                document.getElementById("updateDate").textContent = responseData.Date[0];
                const serverDetails = document.getElementById("serverDetails");
                
                serverDetails.innerHTML = "";
                
                for (const [server, details] of Object.entries(responseData)) {
                    if (server !== "Name" && server !== "Date") {
                        const listItem = document.createElement("li");
                        listItem.className = "list-group-item";
                        listItem.innerHTML = `<strong>${server}:</strong> ${details.join(", ")}`;
                        serverDetails.appendChild(listItem);
                    }
                }
                new bootstrap.Modal(document.getElementById("fileDetailsModal")).show();
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("File Not Updated");
        });
    }

    function unchoose() {
        var alls = document.getElementById("select_all");
        alls.checked = false;
        document.querySelectorAll("#select").forEach(function (a, b) {
            a.checked = false;
        });
        setan();
    }

    if (document.getElementById("select_all")) {
        document.getElementById("select_all").onclick = function () {
            document.querySelectorAll("#select").forEach(function (a, b) {
                if (a.checked) {
                    a.checked = false
                } else {
                    a.checked = true
                }
            });
            hitung()
        }
    }

    if (document.getElementById("select")) {
        document.querySelectorAll("#select").forEach(function (a, b) {
            a.onclick = function () {
                hitung()
            }
        })
    }

    function hitung() {
        var total = 0,
            menu = document.getElementById("menu");
        document.querySelectorAll("#select").forEach(function (a, b) {
            if (a.checked) {
                total++
            }
        });
        if (total >= 1) {
            setan(true);
            menu.scrollIntoView()
        } else {
            setan()
        }
    }

    function setan(hide = false) {
        var menu = document.getElementById('menu');
        if (!hide) {
            menu.setAttribute("style", "display:none")
        } else {
            menu.removeAttribute("style")
        }

    }


    function pack() {
        var ids = [],
            msg = document.getElementById('packmsg'),
            list = document.getElementById('packlist');
        document.querySelectorAll("#select").forEach(function (a, b) {
            if (a.checked) {
                ids.push(a.value)
                var input = document.createElement('input');
                input.setAttribute('name', 'id[]');
                input.setAttribute('type', 'hidden');
                input.setAttribute('value', a.value)
                list.appendChild(input)
            }
        });
        if (ids.length >= 1) {
            msg.innerHTML = 'Are you sure, want to add ' + ids.length + ' files to following Pack'
        }
    }

    document.getElementById('delete').onclick = function () {
        var ids = [],
            msg = document.getElementById('Deletemsg'),
            list = document.getElementById('Deletelist');
        list.innerHTML = '';
        document.querySelectorAll("#select").forEach(function (a, b) {
            if (a.checked) {
                ids.push(a.value)
                var input = document.createElement('input');
                input.setAttribute('name', 'id[]');
                input.setAttribute('type', 'hidden');
                input.setAttribute('value', a.value)
                list.appendChild(input)
            }
        });

        if (ids.length >= 1) {
            msg.innerHTML = 'Are you sure, want to Delete ' + ids.length + ' files?'
        }
    }

    


    document.getElementById('link').onclick = function () {

        var data = document.getElementById('data-link');
        data.value = '';

        document.querySelectorAll("#select").forEach(function (a, b) {
            if (a.checked) {
                console.log(`${c_domain}/file/${a.getAttribute('value')}`)
                data.value += `${c_domain}/file/${a.getAttribute('value')}\n`
            }
        });
    }

    document.getElementById('export').onclick = function () {

        var data = document.getElementById('export-link');
        data.value = '';

        document.querySelectorAll("#select").forEach(function (a, b) {
            if (a.checked) {
                console.log(`${c_domain}/file/${a.getAttribute('value')}`)
                data.value += `${a.getAttribute('fname')} [${formatBytes(a.getAttribute('fsize'))} ]\n${c_domain}/file/${a.getAttribute('value')}\n\n`
            }
        });
    }

    function rename(id, name) {
        document.getElementById('i').value = id;
        document.getElementById('n').value = name;
    }

    function abbrNum(number, decPlaces) {
        decPlaces = Math.pow(10, decPlaces);
        var abbrev = ["k", "m", "b", "t"];
        for (var i = abbrev.length - 1; i >= 0; i--) {
            var size = Math.pow(10, (i + 1) * 3);
            if (size <= number) {
                number = Math.round(number * decPlaces / size) / decPlaces;

                if ((number == 1000) && (i < abbrev.length - 1)) {
                    number = 1;
                    i++;
                }
                number += abbrev[i];
                break;
            }
        }
        return number;
    }

    function formatBytes(a, b = 2, k = 1024) {
        with (Math) {
            let d = floor(log(a) / log(k));
            return 0 == a ? "0 Bytes" : parseFloat((a / pow(k, d)).toFixed(max(0, b))) + " " + ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"][d]
        }
    }
function setCookie() {
    var value = document.getElementById('plimit').value;
    // Create a new Date object for one month ahead
    var expiryDate = new Date();
    expiryDate.setMonth(expiryDate.getMonth() + 1);

    // Convert the expiry date to UTC format
    var expires = expiryDate.toUTCString();

    // Set the cookie
    document.cookie = "fpp=" + value + "; expires=" + expires + "; path=/";
    // alert(value);
    window.location.reload();
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
      width: "330px",
      position: "top-right",
      autohide: true,
      delay: 3e3
    });
    toastInstance.show()
  }

function copy(id) {
    id = "<?PHP echo $site['domain'] ?>/file/" + id;
        navigator.clipboard.writeText(id).then(() => {
            toastr("Alert", "Link copied to clipboard")
        })
    }
</script>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html><?php
require('includes/footer.html');
?>