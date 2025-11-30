<?php
require_once('functions.php');
require_once('config.php');
require_once('db.php');

// Configuration
$resultsPerPage = 30;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $resultsPerPage;

$searchFieldOptions = ['Name', 'uid', 'link_id','Id']; // Valid search fields
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new_date';

$searchTerm = isset($_GET['search']) ? $_GET['search'] : "";
$searchField = isset($_GET['field']) ? $_GET['field'] : "";

$searchCondition = isset($_GET['search']) ? "WHERE " . $searchField . " LIKE '%" . $searchTerm . "%'" : "";

// Fetch data
$res = $pdo->query("SELECT * FROM links_info $searchCondition ORDER BY $sort DESC LIMIT $resultsPerPage OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
$totalResults = $pdo->query("SELECT COUNT(*) AS total FROM links_info $searchCondition")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deadtoons FileManager!</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-dark">
    <div class="container mt-5">
        <!-- Search Form -->
        <form method="GET" action="">
            <div class="input-group search-bar mb-5">
                <select name="field" class="form-control" style="max-width: 150px;">
                    <?php foreach ($searchFieldOptions as $option): ?>
                        <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $searchField === $option ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" class="form-control" name="search" placeholder="Search by <?php echo $searchField; ?>" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <div class="input-group-append">
                    <button class="btn btn-success" type="submit">Search</button>
                </div>
            </div>
            <input type="hidden" name="page" value="1"> <!-- Reset to first page on search -->
        </form>

        <!-- Card Section -->
        <div class="card">
            <div class="card-header bg-dark">
                <a href="/fff"><h4 class="display-6 text-center text-white">Manager For Deadtoons</h4></a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Name</th>
                                <th><a href="?sort=views<?php echo $searchCondition ? '&' . http_build_query(['search' => $searchTerm, 'field' => $searchField]) : ''; ?>">Views</a></th>
                                <th><a href="?sort=downloads<?php echo $searchCondition ? '&' . http_build_query(['search' => $searchTerm, 'field' => $searchField]) : ''; ?>">Downloads</a></th>
                                <th>Size</th>
                                <th><a href="?sort=new_date<?php echo $searchCondition ? '&' . http_build_query(['search' => $searchTerm, 'field' => $searchField]) : ''; ?>">Date</a></th>
                                <th>Embed</th>
                                <th>Direct</th>
                                <th>link_id</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($res as $row): ?>
                                <tr data-id="<?php echo htmlspecialchars($row['Id']); ?>" data-date="<?php echo htmlspecialchars($row['new_date']); ?>">
                                    <td><a href="https://deaddrive.icu/file/<?php echo htmlspecialchars($row['uid']); ?>" target="_blank"><?php echo htmlspecialchars($row['Name']); ?></a></td>
                                    <td><?php echo htmlspecialchars($row['views']); ?></td>
                                    <td><?php echo htmlspecialchars($row['downloads']); ?></td>
                                    <td><?php echo htmlspecialchars(formatBytes($row['size'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['new_date']); ?></td>
                                    <td><a href="https://deaddrive.icu/embed/<?= htmlspecialchars($row['uid']) ?>" target="_blank" class="btn btn-primary">Embed</a></td>
                                    <td><a href="<?= WORKER_DOWNLOAD . "/" . htmlspecialchars($row['Id']) . "/" . htmlspecialchars($row['Name']) ?>" target="_blank" class="btn btn-info">Direct</a></td>
                                    <td><?php echo htmlspecialchars($row['link_id']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php
        echo pagination($totalResults, $currentPage, $resultsPerPage, "", (isset($_GET['search']) ? "&search=" . urlencode($_GET['search']) . "&field=" . urlencode($_GET['field']) : ''), '');
        ?>

    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteRecord(id);
                }
            });
        }

        function deleteRecord(id) {
            var url = `/operations/delete.php?id=${encodeURIComponent(id)}`;
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    if (data === "success") {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Record has been deleted.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                        });
                        $(`tr[data-id="${id}"]`).remove();
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'An error occurred during the deletion.', 'error');
                });
        }

        function edit(id, currentName) {
            var editedName = prompt("Edit Name:", currentName);
            if (editedName && editedName !== currentName && editedName !== "") {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'edit.php';

                var inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = id;

                var inputName = document.createElement('input');
                inputName.type = 'hidden';
                inputName.name = 'editedName';
                inputName.value = editedName;

                form.appendChild(inputId);
                form.appendChild(inputName);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

<?php

function pagination($total, $pgno, $limit, $domain, $s, $cat) {
    if ($total == 0) {
        return;
    }
    $output = '<div class="pagination justify-content-center mt-5 mb-5" style="display: flex; flex-wrap: wrap;">';

    if ($pgno > 1) {
        $output .= '<a class="btn btn-info mr-1 mb-1" href="' . $domain . $cat . '?page=' . ($pgno - 1) . $s . '">Previous</a>';
    }
    $pages = $total % $limit == 0 ? $total / $limit : ($total / $limit) + 1;
    $pages = intval($pages);

    if ($pages > 1) {
        if (($pgno - 3) > 0) {
            $output .= '<a href="' . $domain . $cat . '?page=1' . $s . '" class="btn btn-success mr-1 mb-1 ' . (($pgno == 1) ? "active" : '') . '">1</a>';
        }
        if (($pgno - 3) > 1) {
            $output .= '<span class="btn btn-success mr-1 mb-1">…</span>';
        }

        for ($i = ($pgno - 2); $i <= ($pgno + 2); $i++) {
            if ($i < 1) continue;
            if ($i > $pages) break;
            if ($pgno == $i) {
                $output .= '<span class="btn btn-danger mr-1 mb-1">' . $i . '</span>';
            } else {
                $output .= '<a class="btn btn-success mr-1 mb-1" href="' . $domain . $cat . '?page=' . $i . $s . '">' . $i . '</a>';
            }
        }

        if (($pages - ($pgno + 2)) > 1) {
            $output .= '<span class="btn btn-success mr-1 mb-1">…</span>';
        }
        if (($pages - ($pgno + 2)) > 0) {
            if ($pgno == $pages) {
                $output .= '<span class="btn btn-success mr-1 mb-1">' . $pages . '</span>';
            } else {
                $output .= '<a class="btn btn-success mr-1 mb-1" href="' . $domain . $cat . '?page=' . $pages . $s . '">' . $pages . '</a>';
            }
        }
    }
    if ($pgno != $pages) {
        $output .= '<a class="btn btn-info mr-1 mb-1" href="' . $domain . $cat . '?page=' . ($pgno + 1) . $s . '">Next</a>';
    }
    $output .= '</div>';
    return $output;
}

?>