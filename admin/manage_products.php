<!-- manage_products.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../includes/db.php'; // PDO bağlantısını içerir

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_ingredients'])) {
    $ingredient_names = $_POST['ingredient_name'];
    foreach ($ingredient_names as $ingredient_name) {
        $sql = "INSERT INTO optional_ingredients (name) VALUES (:ingredient_name)";
        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute(['ingredient_name' => $ingredient_name])) {
            $_SESSION['error_message'] = "Hata: " . $stmt->errorInfo()[2];
        }
    }
    $_SESSION['success_message'] = "Malzemeler başarıyla eklendi";
    header('Location: manage_products.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_ingredient'])) {
    $ingredient_id = $_POST['ingredient_id'];
    $sql = "DELETE FROM optional_ingredients WHERE id = :ingredient_id";
    $stmt = $pdo->prepare($sql);
    if (!$stmt->execute(['ingredient_id' => $ingredient_id])) {
        $_SESSION['error_message'] = "Hata: " . $stmt->errorInfo()[2];
    } else {
        $_SESSION['success_message'] = "Malzeme başarıyla silindi";
    }
    header('Location: manage_products.php');
    exit();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Erişim reddedildi");
}

$restaurant_id = $_SESSION['restaurant_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $item_id = $_POST['item_id'];
        $sql = "DELETE FROM menu_items WHERE id = :item_id";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['item_id' => $item_id])) {
            $_SESSION['success_message'] = "Ürün başarıyla silindi";
        } else {
            $_SESSION['error_message'] = "Hata: " . $stmt->errorInfo()[2];
        }
    } elseif (isset($_POST['edit'])) {
        $item_id = $_POST['item_id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_FILES['image'];
        $optional_ingredients = isset($_POST['optional_ingredients']) ? $_POST['optional_ingredients'] : [];

        if ($image['error'] == 0) {
            $imageName = time() . '-' . $image['name'];
            $imagePath = '../uploads/' . $imageName;
            move_uploaded_file($image['tmp_name'], $imagePath);
        } else {
            $imageName = $_POST['current_image'];
        }

        $sql = "UPDATE menu_items SET name = :name, price = :price, image = :image WHERE id = :item_id";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['name' => $name, 'price' => $price, 'image' => $imageName, 'item_id' => $item_id])) {
            $pdo->prepare("DELETE FROM product_ingredients WHERE product_id = :item_id")->execute(['item_id' => $item_id]);
            foreach ($optional_ingredients as $ingredient_name) {
                $check_sql = "SELECT id FROM optional_ingredients WHERE name = :ingredient_name";
                $stmt_check = $pdo->prepare($check_sql);
                $stmt_check->execute(['ingredient_name' => $ingredient_name]);
                $ingredient_id = $stmt_check->fetchColumn();
                if (!$ingredient_id) {
                    $stmt_insert = $pdo->prepare("INSERT INTO optional_ingredients (name) VALUES (:ingredient_name)");
                    $stmt_insert->execute(['ingredient_name' => $ingredient_name]);
                    $ingredient_id = $pdo->lastInsertId();
                }
                $pdo->prepare("INSERT INTO product_ingredients (product_id, ingredient_id) VALUES (:product_id, :ingredient_id)")
                    ->execute(['product_id' => $item_id, 'ingredient_id' => $ingredient_id]);
            }
            $_SESSION['success_message'] = "Ürün başarıyla güncellendi";
        } else {
            $_SESSION['error_message'] = "Hata: " . $stmt->errorInfo()[2];
        }
    } elseif (isset($_POST['add'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_FILES['image'];
        $optional_ingredients = isset($_POST['optional_ingredients']) ? $_POST['optional_ingredients'] : [];

        if ($image['error'] == 0) {
            $imageName = time() . '-' . $image['name'];
            $imagePath = '../uploads/' . $imageName;
            move_uploaded_file($image['tmp_name'], $imagePath);
        } else {
            $imageName = 'default.png';
        }

        $sql = "INSERT INTO menu_items (name, price, image, restaurant_id) VALUES (:name, :price, :image, :restaurant_id)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['name' => $name, 'price' => $price, 'image' => $imageName, 'restaurant_id' => $restaurant_id])) {
            $product_id = $pdo->lastInsertId();
            foreach ($optional_ingredients as $ingredient_name) {
                $check_sql = "SELECT id FROM optional_ingredients WHERE name = :ingredient_name";
                $stmt_check = $pdo->prepare($check_sql);
                $stmt_check->execute(['ingredient_name' => $ingredient_name]);
                $ingredient_id = $stmt_check->fetchColumn();
                if (!$ingredient_id) {
                    $stmt_insert = $pdo->prepare("INSERT INTO optional_ingredients (name) VALUES (:ingredient_name)");
                    $stmt_insert->execute(['ingredient_name' => $ingredient_name]);
                    $ingredient_id = $pdo->lastInsertId();
                }
                $pdo->prepare("INSERT INTO product_ingredients (product_id, ingredient_id) VALUES (:product_id, :ingredient_id)")
                    ->execute(['product_id' => $product_id, 'ingredient_id' => $ingredient_id]);
            }
            $_SESSION['success_message'] = "Ürün başarıyla eklendi";
        } else {
            $_SESSION['error_message'] = "Hata: " . $stmt->errorInfo()[2];
        }
    }
    header('Location: manage_products.php');
    exit();
}

$sql = "SELECT * FROM menu_items WHERE restaurant_id = :restaurant_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['restaurant_id' => $restaurant_id]);
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM optional_ingredients";
$ingredients_result = $pdo->query($sql);
$optional_ingredients = $ingredients_result->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color) !important;
            
        }
        .container {
            padding-left: 10px;
            padding-right: 10px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            padding: 5px;
            margin-bottom: 10px;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover-color);
        }
        .btn-success {
            background-color: green;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            padding: 5px;
            margin-bottom: 10px;
        }
        .btn-success:hover {
            background-color: #006400;
        }
        .btn-danger {
            background-color: var(--danger-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            padding: 5px;
            margin-bottom: 10px;
        }
        .btn-danger:hover {
            background-color: var (--danger-hover-color);
        }
        .card {
            background-color: var(--card-bg-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .card-header, .card-footer {
            background-color: var(--card-bg-color);
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
        }
        .card-body {
            color: var(--text-color);
        }
        .list-group-item {
            background-color: var(--card-bg-color);
            color: var(--text-color);
            border: none;
            border-bottom: 1px solid var(--border-color);
        }
        .container table {
            color: var(--text-color);
        }
        .modal {
            color: var(--navbar-bg-color);
        }
        .modal-backdrop.show {
            opacity: 0.5;
        }
        .modal-dialog {
            top: 50%;
            transform: translateY(-50%);
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] == 'superadmin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Süper Yönetici Paneli</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_restaurants.php">Restoranları Yönet</a>
                        </li>
                    <?php elseif ($_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_orders.php">Siparişleri Yönet</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="add_product.php">Ürünleri Yönet</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="promotions.html">Promosyon Yönetimi</a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link" href="customer_history.php">Müşteri Geçmişi</a>
                        </li> 
                        <li class="nav-item">
                        <a class="nav-link" href="sales_analytics.php">Satış Analitikleri</a>
                    </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="restaurants.php">Restoranlar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">Sipariş Sepeti</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profil</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Çıkış Yap</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Kayıt Ol</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container" dir="ltr" style="margin-top: 50px;">
    <h2 style="margin-bottom: 50px;">Ürün Yönetimi</h2>
    <hr>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <!-- Ürün Ekle Butonları -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addIngredientModal">Yeni Malzemeler Ekle</button>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Yeni Ürün Ekle</button>
    <button class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#updateRestaurantModal">Restoran Bilgilerini Güncelle</button>
    <hr>
    <table class="table table-bordered"  style="margin-top: 50px;">
        <thead>
            <tr>
                <th>Ürün Adı</th>
                <th>Fiyat</th>
                <th>Görsel</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menu_items as $item): ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['price']; ?> TL</td>
                <td><img src="../uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" style="width: 100px;"></td>
                <td>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $item['id']; ?>">Düzenle</button>
                    <form method="post" style="display:inline-block;" onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?');">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger">Sil</button>
                    </form>
                </td>
            </tr>
            
            <!-- Ürünü Düzenle -->
            <div class="modal fade" id="editModal<?php echo $item['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ürünü Düzenle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data" onsubmit="return confirm('Bu ürünü düzenlemek istediğinizden emin misiniz?');">
                            <div class="modal-body">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Ürün Adı</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $item['name']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Fiyat</label>
                                    <input type="number" class="form-control" id="price" name="price" value="<?php echo $item['price']; ?>" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Ürün Görseli</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <input type="hidden" name="current_image" value="<?php echo $item['image']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="optional_ingredients" class="form-label">İsteğe Bağlı Malzemeler</label>
                                    <?php
      $sql = "SELECT optional_ingredients.name FROM product_ingredients 
      JOIN optional_ingredients ON product_ingredients.ingredient_id = optional_ingredients.id 
      WHERE product_ingredients.product_id = :product_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['product_id' => $item['id']]);
$added_ingredients = $stmt->fetchAll(PDO::FETCH_COLUMN);

                                    ?>
                                    <?php foreach ($optional_ingredients as $ingredient): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="optional_ingredients[]" value="<?php echo $ingredient['name']; ?>" 
                                            <?php 
                                            if (in_array($ingredient['name'], $added_ingredients)) {
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label"><?php echo $ingredient['name']; ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                <button type="submit" name="edit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Ürün Ekle Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true" dir="ltr">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Yeni Ürün Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data" onsubmit="return confirm('Bu ürünü eklemek istediğinizden emin misiniz?');">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ürün Adı</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Fiyat</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Ürün Görseli</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="optional_ingredients" class="form-label">İsteğe Bağlı Malzemeler</label>
                        <?php foreach ($optional_ingredients as $ingredient): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="optional_ingredients[]" value="<?php echo $ingredient['name']; ?>">
                                <label class="form-check-label"><?php echo $ingredient['name']; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" name="add" class="btn btn-primary">Ürünü Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Malzeme Ekle Modal -->
<div class="modal fade" id="addIngredientModal" tabindex="-1" aria-labelledby="addIngredientModalLabel" aria-hidden="true" dir="ltr">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIngredientModalLabel">Yeni Malzemeler Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" onsubmit="return confirm('Bu malzemeleri eklemek istediğinizden emin misiniz?');">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ingredient_name" class="form-label">Malzeme Adı</label>
                        <input type="text" class="form-control" id="ingredient_name" name="ingredient_name[]" required>
                    </div>
                    <div id="additionalIngredients"></div>
                    <button type="button" class="btn btn-secondary" onclick="addIngredientField()">Başka Malzeme Ekle</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" name="add_ingredients" class="btn btn-primary">Malzemeleri Ekle</button>
                </div>
            </form>

            <!-- Mevcut Malzemeleri Göster ve Silme Seçeneği -->
            <div class="modal-body">
                <h5>Mevcut Malzemeler</h5>
                <ul class="list-group">
                    <?php foreach ($optional_ingredients as $ingredient): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo $ingredient['name']; ?>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="ingredient_id" value="<?php echo $ingredient['id']; ?>">
                                <button type="submit" name="delete_ingredient" class="btn btn-danger btn-sm" onclick="return confirm('Bu malzemeyi silmek istediğinizden emin misiniz?');">Sil</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Restoran Bilgilerini Güncelle Modal -->
<div class="modal fade" id="updateRestaurantModal" tabindex="-1" aria-labelledby="updateRestaurantModalLabel" aria-hidden="true" dir="ltr">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateRestaurantModalLabel">Restoran Bilgilerini Güncelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="update_restaurant_info.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="restaurant_image" class="form-label">Restoran Görseli</label>
                        <input type="file" class="form-control" id="restaurant_image" name="restaurant_image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon Numarası</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Adres</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="working_hours" class="form-label">Çalışma Saatleri</label>
                        <input type="text" class="form-control" id="working_hours" name="working_hours" required>
                    </div>
                    <div class="mb-3">
                        <label for="google_map_link" class="form-label">Google Harita Linki</label>
                        <input type="text" class="form-control" id="google_map_link" name="google_map_link" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Güncellemeleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function addIngredientField() {
        var container = document.getElementById('additionalIngredients');
        var input = document.createElement('input');
        input.type = 'text';
        input.name = 'ingredient_name[]';
        input.className = 'form-control mt-2';
        input.required = true;
        container.appendChild(input);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
