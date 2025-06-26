<?php
require 'config.php';

header('Content-Type: application/json');

// Récupérer les données de la commande
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

try {
    $conn->beginTransaction();
    
    // Créer l'ID de commande
    $orderId = 'CMD-' . time() . '-' . rand(1000, 9999);
    
    // Insérer la commande principale
    $orderStmt = $conn->prepare("
        INSERT INTO orders (id, total, customer_name, customer_phone, customer_address) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $orderStmt->execute([
        $orderId, 
        $data['total'],
        $data['customer_name'] ?? '',
        $data['customer_phone'] ?? '',
        $data['customer_address'] ?? ''
    ]);
    
    // Insérer les articles de la commande
    $itemStmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, size, color, quantity, price)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($data['items'] as $item) {
        $itemStmt->execute([
            $orderId,
            $item['id'],
            $item['name'],
            $item['size'] ?? '',
            $item['color'] ?? '',
            $item['quantity'],
            $item['price']
        ]);
    }
    
    // Mettre à jour les statistiques
    $statsStmt = $conn->prepare("
        UPDATE stats 
        SET value = value + ? 
        WHERE metric = ?
    ");
    
    $statsStmt->execute([$data['total'], 'total_sales']);
    $statsStmt->execute([1, 'total_orders']);
    
    $totalProducts = array_reduce($data['items'], function($sum, $item) {
        return $sum + $item['quantity'];
    }, 0);
    
    $statsStmt->execute([$totalProducts, 'total_products_sold']);
    
    $conn->commit();
    
    echo json_encode(['success' => true, 'order_id' => $orderId]);
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Erreur de commande: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erreur système']);
}
?>