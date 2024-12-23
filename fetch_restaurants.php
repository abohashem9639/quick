<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';

try {
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    $sql = "SELECT restaurants.id, restaurants.name, restaurants.image, 
        COALESCE(AVG(ratings.rating), 0) AS avg_rating
        FROM restaurants
        LEFT JOIN ratings ON restaurants.id = ratings.restaurant_id";

if (!empty($searchTerm)) {
    $sql .= " WHERE LOWER(restaurants.name) LIKE LOWER(:search)";
}

$sql .= " GROUP BY restaurants.id, restaurants.name, restaurants.image";


    $stmt = $pdo->prepare($sql);

    if (!empty($searchTerm)) {
        $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
    }

    $stmt->execute();
    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($restaurants)) {
        foreach ($restaurants as $row) {
            // Favori durumu kontrol ediliyor
            $isFavoriteStmt = $pdo->prepare("SELECT COUNT(*) FROM favorite_restaurants WHERE user_id = :user_id AND restaurant_id = :restaurant_id");
            $isFavoriteStmt->execute([
                'user_id' => $_SESSION['user_id'],
                'restaurant_id' => $row['id']
            ]);
            $isFavorite = $isFavoriteStmt->fetchColumn() > 0;

            echo '<div class="col-md-4 mb-4">
                    <div class="card position-relative">
                        <button class="btn btn-link favorite-btn p-0 position-absolute top-0 end-0 m-2" data-restaurant-id="' . $row['id'] . '">
                            <i class="fas fa-star ' . ($isFavorite ? 'gold' : '') . '" style="color: ' . ($isFavorite ? 'gold' : 'grey') . ';"></i>
                        </button>
                        <img src="uploads/' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>
                            <p class="card-text">Puan: ' . round($row['avg_rating'], 1) . '/10</p>
                            <a href="menu.php?restaurant_id=' . $row['id'] . '" class="btn btn-primary">Menüyü Görüntüle</a>
                        </div>
                    </div>
                </div>';
        }
    } else {
        echo '<p class="text-center">Sonuç bulunamadı.</p>';
    }
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?>
