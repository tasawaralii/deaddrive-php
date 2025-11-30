<?PHP
require('../db.php');
$page_number = $_GET['pgno'];
$page_size = $_GET['pgsize'];;
$offset = ($page_number - 1) * $page_size;
$email = $_GET['email'];
try {
$stmt = $pdo->prepare("SELECT Links_info.Name, Links_info.Size, Links_info.views,Links_info.new_date, Servers.uid 
                      FROM Links_info 
                      JOIN Servers ON Links_info.Id = Servers.Id 
                      WHERE user = (SELECT user_id FROM users WHERE email = :email) AND Links_info.deleted = 1
                      ORDER BY Links_info.new_date DESC 
                      LIMIT :limit OFFSET :offset");

$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->bindParam(':limit', $page_size, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

    // Fetching data
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt->closeCursor();
print(json_encode($result));
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
?>