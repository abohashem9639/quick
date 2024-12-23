<!-- dashboard.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'superadmin') {
    die("Erişim reddedildi");
}

$mesaj = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['restaurant_name'])) {
        $restaurant_name = $_POST['restaurant_name'];
        $resimAdi = null;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $resimAdi = time() . '-' . $_FILES['image']['name'];
            $resimYolu = '../uploads/' . $resimAdi;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $resimYolu)) {
                $sql = "INSERT INTO restaurants (name, image) VALUES (:restaurant_name, :resimAdi)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['restaurant_name' => $restaurant_name, 'resimAdi' => $resimAdi]);
                $mesaj = "<div class='alert alert-success'>Restoran başarıyla eklendi</div>";
            } else {
                $mesaj = "<div class='alert alert-danger'>Resim yüklenemedi.</div>";
            }
        } else {
            $sql = "INSERT INTO restaurants (name) VALUES (:restaurant_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['restaurant_name' => $restaurant_name]);
            $mesaj = "<div class='alert alert-success'>Restoran başarıyla eklendi</div>";
        }
    } elseif (isset($_POST['edit_restaurant_id'])) {
        $restaurant_id = $_POST['edit_restaurant_id'];
        $restaurant_name = $_POST['edit_restaurant_name'];
        $resimAdi = $_POST['current_image'];

        if (isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] == 0) {
            $resimAdi = time() . '-' . $_FILES['edit_image']['name'];
            $resimYolu = '../uploads/' . $resimAdi;
            if (move_uploaded_file($_FILES['edit_image']['tmp_name'], $resimYolu)) {
                $sql = "UPDATE restaurants SET name = :restaurant_name, image = :resimAdi WHERE id = :restaurant_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['restaurant_name' => $restaurant_name, 'resimAdi' => $resimAdi, 'restaurant_id' => $restaurant_id]);
                $mesaj = "<div class='alert alert-success'>Restoran başarıyla güncellendi</div>";
            } else {
                $mesaj = "<div class='alert alert-danger'>Resim yüklenemedi.</div>";
            }
        } else {
            $sql = "UPDATE restaurants SET name = :restaurant_name, image = :resimAdi WHERE id = :restaurant_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['restaurant_name' => $restaurant_name, 'resimAdi' => $resimAdi, 'restaurant_id' => $restaurant_id]);
            $mesaj = "<div class='alert alert-success'>Restoran başarıyla güncellendi</div>";
        }
    }
}

$sql = "SELECT restaurants.id, restaurants.name, restaurants.image, users.id AS user_id 
        FROM restaurants 
        LEFT JOIN users ON restaurants.id = users.restaurant_id AND users.user_role = 'admin'";
$stmt = $pdo->query($sql);
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick - Restoran Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-color: #121212;
            --text-color: #ffffff;
            --navbar-bg-color: #1c1c1c;
            --border-color: #444;
            --primary-color: red;
            --primary-hover-color: #e65c00;
            --secondary-color: #888;
            --secondary-hover-color: #777;
            --danger-color: #ff0000;
            --danger-hover-color: #cc0000;
            --card-bg-color: #1c1c1c;
            --modal-bg-color: #333;
            --modal-content-bg-color: #2c2c2c;
            --muted-text-color: #bbb;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: var(--navbar-bg-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
            font-size: 1.5rem;
            text-align: left;
        }
        .navbar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
        }
        .nav-item {
            margin-left: 15px;
        }
        .nav-link {
            color: var(--text-color) !important;
            font-size: 1.1rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        .container {
            padding-left: 10px;
            padding-right: 10px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-primary, .btn-success, .btn-danger, .btn-secondary {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            padding: 5px;
            margin-bottom: 10px;
        }
        .btn-primary {
            background-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--primary-hover-color);
        }
        .btn-success {
            background-color: green;
        }
        .btn-success:hover {
            background-color: #006400;
        }
        .btn-danger {
            background-color: var(--danger-color);
        }
        .btn-danger:hover {
            background-color: var(--danger-hover-color);
        }
        .btn-secondary {
            background-color: var(--secondary-color);
        }
        .btn-secondary:hover {
            background-color: var(--secondary-hover-color);
        }
        .card, .list-group-item {
            background-color: var(--card-bg-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 20px;
            color: var(--text-color);
        }
        .modal-content {
            background-color: var(--modal-content-bg-color);
            color: var(--text-color);
        }
        .modal-header, .modal-footer {
            background-color: var(--modal-bg-color);
            color: var(--text-color);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light w-100">
    <div class="container">
        <a class="navbar-brand" href="#">Quick</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="manage_restaurants.php">Restoranları Yönet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Çıkış Yap</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <h2 style="margin-top: 50px;">Restoran Yönetimi</h2>
    <?php echo $mesaj; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="restaurant_name" class="form-label">Restoran Adı</label>
            <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Restoran Resmi</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Restoran Ekle</button>
    </form>
    <hr>
    <h3>Eklenmiş Restoranlar</h3>
    <ul class="list-group">
        <?php foreach ($restaurants as $restaurant): ?>
            <li class="list-group-item d-flex align-items-center">
                <?php if ($restaurant['image']): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($restaurant['image']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                <?php endif; ?>
                <span><?php echo htmlspecialchars($restaurant['name']); ?></span>
                <?php if ($restaurant['user_id']): ?>
                    <span class="badge bg-success ms-auto">Aktif</span>
                <?php else: ?>
                    <span class="badge bg-danger ms-auto">Pasif</span>
                    <a href="add_restaurant_account.php?restaurant_id=<?php echo $restaurant['id']; ?>" class="btn btn-sm btn-primary ms-2">Hesap Oluştur</a>
                <?php endif; ?>
                <button class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $restaurant['id']; ?>">Düzenle</button>
            </li>
            <!-- Restoranı Düzenle -->
            <div class="modal fade" id="editModal<?php echo $restaurant['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $restaurant['id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?php echo $restaurant['id']; ?>">Restoranı Düzenle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="edit_restaurant_id" value="<?php echo $restaurant['id']; ?>">
                                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($restaurant['image']); ?>">
                                <div class="mb-3">
                                    <label for="edit_restaurant_name" class="form-label">Restoran Adı</label>
                                    <input type="text" class="form-control" id="edit_restaurant_name" name="edit_restaurant_name" value="<?php echo htmlspecialchars($restaurant['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_image" class="form-label">Restoran Resmi</label>
                                    <input type="file" class="form-control" id="edit_image" name="edit_image" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </ul>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>


</body>
</html>
