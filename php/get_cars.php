<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $per_page = 6; // Number of cars per page
    $offset = ($page - 1) * $per_page;

    // Build the WHERE clause based on filters
    $where_clauses = [];
    $params = [];

    if (!empty($_POST['carType'])) {
        $where_clauses[] = "type = ?";
        $params[] = $_POST['carType'];
    }

    if (!empty($_POST['priceRange'])) {
        $range = explode('-', $_POST['priceRange']);
        if (count($range) == 2) {
            $where_clauses[] = "price BETWEEN ? AND ?";
            $params[] = (float)$range[0];
            $params[] = (float)$range[1];
        } elseif (substr($_POST['priceRange'], -1) === '+') {
            $min_price = (float)substr($_POST['priceRange'], 0, -1);
            $where_clauses[] = "price >= ?";
            $params[] = $min_price;
        }
    }

    $where_sql = !empty($where_clauses) ? "WHERE " . implode(' AND ', $where_clauses) : "";

    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as total FROM cars $where_sql";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get cars for current page
    $sql = "SELECT * FROM cars $where_sql ORDER BY id DESC LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'cars' => $cars,
        'total' => $total,
        'per_page' => $per_page
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
