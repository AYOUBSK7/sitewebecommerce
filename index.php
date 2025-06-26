<?php
// Configuration des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
require("config.php");

session_start();

// Gestion des erreurs de connexion
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Désactiver temporairement les vérifications de clés étrangères
    $conn->exec("SET FOREIGN_KEY_CHECKS=0");
} catch(PDOException $e) {
    die("<div style='color: red; padding: 20px; border: 2px solid red; background: #ffe6e6;'>"
        . "Erreur de connexion à la base de données: " . $e->getMessage()
        . "</div>");
}

// Création des tables si nécessaire
$tables = [
    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        images TEXT NOT NULL,
        badge VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS orders (
        id VARCHAR(50) PRIMARY KEY,
        date DATETIME NOT NULL,
        status ENUM('pending','sold','cancelled') DEFAULT 'pending',
        total DECIMAL(10,2) NOT NULL,
        items TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL
    )"
];

foreach ($tables as $sql) {
    try {
        $conn->exec($sql);
    } catch(PDOException $e) {
        echo "<div style='color: #d9534f; padding: 15px; background: #f2dede; border: 1px solid #ebccd1;'>"
            . "Erreur lors de la création de la table: " . $e->getMessage()
            . "</div>";
    }
}

// Ajout des colonnes manquantes
$conn->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS category VARCHAR(100) NOT NULL AFTER name");
$conn->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS images TEXT NOT NULL AFTER description");

// Création de l'admin par défaut si nécessaire
$checkAdmin = $conn->query("SELECT COUNT(*) FROM admin")->fetchColumn();
if ($checkAdmin == 0) {
    // Correction: Création avec username = '' et password = ''
    //modifie la avec bos informations 
    $hashedPassword = password_hash('', PASSWORD_DEFAULT);
    try {
        $conn->exec("INSERT INTO admin (username, password) VALUES ('', '$hashedPassword')");
    } catch(PDOException $e) {
        echo "<div style='color: #d9534f; padding: 15px; background: #f2dede; border: 1px solid #ebccd1;'>"
            . "Erreur lors de la création de l'admin: " . $e->getMessage()
            . "</div>";
    }
}

// Récupérer les messages de session
$product_success = $_SESSION['product_success'] ?? '';
$product_error = $_SESSION['product_error'] ?? '';
$login_error = $_SESSION['login_error'] ?? '';

// Effacer les messages après les avoir récupérés
unset($_SESSION['product_success'], $_SESSION['product_error'], $_SESSION['login_error']);

// Traitement des formulaires admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connexion admin
    if (isset($_POST['admin_login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
        } else {
            $_SESSION['login_error'] = "Identifiants incorrects";
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
    
    // Ajout d'un produit
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $price = floatval($_POST['price']);
        $description = $_POST['description'];
        $badge = $_POST['badge'];
        
        // Gestion de l'upload des images
        $images = [];
        $uploadDir = 'uploads/';
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $uploadSuccess = true;
        $uploadError = '';
        
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = basename($_FILES['images']['name'][$key]);
                $filePath = $uploadDir . uniqid() . '_' . $fileName;
                
                if (move_uploaded_file($tmp_name, $filePath)) {
                    $images[] = $filePath;
                } else {
                    $uploadSuccess = false;
                    $uploadError = "Erreur lors de l'upload de l'image: " . $_FILES['images']['name'][$key];
                    break;
                }
            }
        } else {
            $uploadSuccess = false;
            $uploadError = "Aucune image sélectionnée";
        }
        
        if ($uploadSuccess && !empty($images)) {
            $imagesJson = json_encode($images);
            
            try {
                $stmt = $conn->prepare("INSERT INTO products (name, category, price, description, images, badge) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $category, $price, $description, $imagesJson, $badge]);
                
                $_SESSION['product_success'] = "Produit ajouté avec succès!";
                
            } catch(PDOException $e) {
                $_SESSION['product_error'] = "Erreur base de données: " . $e->getMessage();
            }
        } else {
            $_SESSION['product_error'] = $uploadError ?: "Aucune image valide n'a été téléchargée";
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
    
    // Modification d'un produit
    if (isset($_POST['update_product'])) {
        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $category = $_POST['category'];
        $price = floatval($_POST['price']);
        $description = $_POST['description'];
        $badge = $_POST['badge'];
        
        try {
            $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, description = ?, badge = ? WHERE id = ?");
            $stmt->execute([$name, $category, $price, $description, $badge, $product_id]);
            
            $_SESSION['product_success'] = "Produit mis à jour avec succès!";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } catch(PDOException $e) {
            $_SESSION['product_error'] = "Erreur lors de la mise à jour: " . $e->getMessage();
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }
    }
    
    // Mise à jour du statut d'une commande
    if (isset($_POST['update_order_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        
        // Marquer pour recalculer les stats
        $_SESSION['recalculate_stats'] = true;
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
    
    // Suppression d'une commande
    if (isset($_POST['delete_order'])) {
        $order_id = $_POST['order_id'];
        
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        
        // Marquer pour recalculer les stats
        $_SESSION['recalculate_stats'] = true;
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
    
    // Suppression d'un produit
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        
        try {
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            
            $_SESSION['product_success'] = "Produit supprimé avec succès!";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } catch(PDOException $e) {
            $_SESSION['product_error'] = "Erreur lors de la suppression: " . $e->getMessage();
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// Déconnexion admin
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Réactiver les vérifications de clés étrangères
$conn->exec("SET FOREIGN_KEY_CHECKS=1");

// Récupérer les produits
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convertir les images de JSON en tableau
foreach ($products as &$product) {
    if ($product['images']) {
        $product['images'] = json_decode($product['images'], true);
    } else {
        $product['images'] = ['placeholder.jpg']; // Image par défaut
    }
}
unset($product);

// Récupérer les commandes pour l'admin
$orders = [];
if (isset($_SESSION['admin_logged_in'])) {
    $stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Traitement sécurisé des commandes
    foreach ($orders as &$order) {
        // Gestion de la date
        $orderDate = $order['date'] ?? $order['created_at'] ?? date('Y-m-d H:i:s');
        try {
            $dateObj = new DateTime($orderDate);
            $order['formatted_date'] = $dateObj->format('d/m/Y H:i');
        } catch (Exception $e) {
            $order['formatted_date'] = date('d/m/Y H:i');
        }
        
        // Conversion sécurisée des items
        $order['items'] = isset($order['items']) && !empty($order['items']) 
                         ? json_decode($order['items'], true) 
                         : [];
    }
    unset($order);
}

// Calculer les statistiques - TOUJOURS calculer après les mises à jour
$stats = [
    'total_sales' => 0,
    'total_orders' => 0,
    'total_products' => 0
];

// Recalculer si nécessaire ou à chaque chargement
$recalculate = $_SESSION['recalculate_stats'] ?? true;
if ($recalculate) {
    unset($_SESSION['recalculate_stats']);
    
    $stmt = $conn->query("SELECT * FROM orders WHERE status = 'sold'");
    $soldOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($soldOrders)) {
        $stats['total_orders'] = count($soldOrders);
        
        foreach ($soldOrders as $order) {
            $stats['total_sales'] += $order['total'];
            
            $items = isset($order['items']) ? json_decode($order['items'], true) : [];
            foreach ($items as $item) {
                $stats['total_products'] += $item['quantity'] ?? 0;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Los angeles boutik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>
    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/21628354929" class="whatsapp-float" id="whatsappButton">
      <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Zone secrète pour l'accès admin (invisible) -->
    <div id="secretAdminZone"></div>

    <!-- Image Gallery Modal -->
    <div class="gallery-modal" id="galleryModal">
      <span class="close-gallery" id="closeGallery">&times;</span>
      <div class="gallery-container">
        <img src="" alt="" class="gallery-main-image" id="galleryMainImage" />
        <div class="gallery-nav gallery-prev" id="galleryPrev">
          <i class="fas fa-chevron-left"></i>
        </div>
        <div class="gallery-nav gallery-next" id="galleryNext">
          <i class="fas fa-chevron-right"></i>
        </div>
        <div class="gallery-thumbnails" id="galleryThumbnails"></div>
      </div>
    </div>

    <!-- Header -->
    <header>
      <nav class="navbar">
        <a href="#accueil" class="logo">
          <i class="fas fa-flag-usa"></i>
          <span>LA boutik</span>
        </a>
        <ul class="nav-links">
          <li><a href="#accueil">Accueil</a></li>
          <li><a href="#produits">Produits</a></li>
          <li><a href="#apropos">À propos</a></li>
          <li><a href="#process">Processus</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="cart-icon" id="cartIcon">
          <i class="fas fa-shopping-cart"></i>
          <div class="cart-count" id="cartCount">0</div>
        </div>
      </nav>
    </header>

    <!-- Shopping Cart Panel -->
    <div class="cart-panel" id="cartPanel">
      <div class="cart-header">
        <h3>Votre Panier</h3>
        <button class="close-cart" id="closeCart">&times;</button>
      </div>
      <div class="cart-items" id="cartItems">
        <!-- Cart items will be added here dynamically -->
      </div>
      <div class="cart-total">Total: <span id="cartTotal">0 DT</span></div>
      <div class="cart-actions">
        <button class="btn checkout-btn" id="checkoutBtn">
          <i class="fab fa-whatsapp"></i> Commander via WhatsApp
        </button>
        <button class="btn" id="continueShopping" style="background: #6c757d">
          Continuer mes achats
        </button>
      </div>
    </div>
    <div class="overlay" id="overlay"></div>

    <!-- Admin Login -->
    <div class="admin-login" id="adminLogin">
      <h3>Accès Administrateur</h3>
      <form method="post" id="adminLoginForm">
        <?php if (!empty($login_error)): ?>
          <div class="notification error show"><?php echo $login_error; ?></div>
        <?php endif; ?>
        <div class="form-group">
          <label for="username">Nom d'utilisateur</label>
          <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
          <div class="password-input-container">
            <input type="password" id="adminPassword" name="password" class="form-control" placeholder="Entrez le mot de passe" required>
            <span class="toggle-password" id="togglePassword">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>
        <button type="submit" class="btn checkout-btn" name="admin_login" style="width: 100%; margin-top: 15px">
          Se connecter
        </button>
      </form>
    </div>

    <!-- Hero Section -->
    <section class="hero" id="accueil">
      <h1>Shoppez Américain. Portez la Différence.</h1>
      <p>
        Découvrez nos produits exclusifs importés directement des États-Unis.
        Livraison rapide et gratuite et paiement à la réception!
      </p>
      <a href="#produits" class="btn"
        >Voir les produits <i class="fas fa-arrow-right"></i
      ></a>
    </section>

    <!-- Products Section -->
    <section class="section products" id="produits">
      <h2 class="section-title">Nos Produits</h2>
      
      <!-- Barre de filtres par catégorie -->
      <div class="products-filter" id="productsFilter">
        <button class="filter-btn active" data-category="all">Tous</button>
        <button class="filter-btn" data-category="Sacs">Sacs</button>
        <button class="filter-btn" data-category="Vêtements">Vêtements</button>
        <button class="filter-btn" data-category="Cravates">Cravates</button>
        <button class="filter-btn" data-category="Sandales">Sandales</button>
        <button class="filter-btn" data-category="Écharpes">Écharpes</button>
        <button class="filter-btn" data-category="Ceintures">Ceintures</button>
        <button class="filter-btn" data-category="Casquettes">Casquettes</button>
        <button class="filter-btn" data-category="Portefeuilles">Portefeuilles</button>
      </div>
      
      <div class="products-grid" id="productsContainer">
        <?php foreach ($products as $product): ?>
          <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>">
            <?php if ($product['badge']): ?>
              <div class="product-badge"><?php echo $product['badge']; ?></div>
            <?php endif; ?>
            <div class="product-image" data-id="<?php echo $product['id']; ?>">
              <img src="<?php echo $product['images'][0]; ?>" alt="<?php echo $product['name']; ?>">
            </div>
            <div class="product-info">
              <div class="product-category"><?php echo $product['category']; ?></div>
              <h3 class="product-name"><?php echo $product['name']; ?></h3>
              <div class="product-price"><?php echo number_format($product['price'], 2); ?> DT</div>
              <p class="product-description"><?php echo $product['description']; ?></p>
              <button class="order-btn add-to-cart" data-id="<?php echo $product['id']; ?>">
                <i class="fas fa-cart-plus"></i> Ajouter au panier
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>



    <!-- Process Section -->
    <section class="section process" id="process">
      <h2 class="section-title">Comment Commander</h2>
      <div class="process-steps">
        <div class="step">
          <div class="step-icon">
            <i class="fas fa-search"></i>
          </div>
          <h3>Choisissez</h3>
          <p>
            Parcourez notre sélection de produits américains de qualité et
            choisissez ceux qui vous plaisent.
          </p>
        </div>

        <div class="step">
          <div class="step-icon">
            <i class="fab fa-whatsapp"></i>
          </div>
          <h3>Commandez via WhatsApp</h3>
          <p>
            Cliquez sur "Commander" et envoyez-nous votre demande via WhatsApp
            en indiquant le produit choisi.
          </p>
        </div>

        <div class="step">
          <div class="step-icon">
            <i class="fas fa-truck"></i>
          </div>
          <h3>Livraison Rapide</h3>
          <p>
            Nous expédions votre commande dans les 24-48h. Suivez votre colis en
            temps réel.
          </p>
        </div>

        <div class="step">
          <div class="step-icon">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <h3>Paiement à la Livraison</h3>
          <p>
            Payez uniquement lorsque vous recevez votre commande. Aucun paiement
            anticipé nécessaire!
          </p>
        </div>
      </div>
    </section>

    <!-- Admin Page -->
    <?php if (isset($_SESSION['admin_logged_in'])): ?>
    <div class="admin-page" id="adminPage">
      <div class="admin-header">
        <h1>Administration US Imports</h1>
        <button id="logoutAdmin" class="btn">
          <i class="fas fa-sign-out-alt"></i> Déconnexion
        </button>
      </div>
      
      <div class="admin-stats">
        <div class="stat-card">
          <div class="stat-value" id="adminTotalSales"><?php echo number_format($stats['total_sales'], 2); ?> DT</div>
          <div class="stat-label">Total des ventes</div>
        </div>
        <div class="stat-card">
          <div class="stat-value" id="adminTotalOrders"><?php echo $stats['total_orders']; ?></div>
          <div class="stat-label">Commandes effectuées</div>
        </div>
        <div class="stat-card">
          <div class="stat-value" id="adminTotalProducts"><?php echo $stats['total_products']; ?></div>
          <div class="stat-label">Produits vendus</div>
        </div>
      </div>

      <div class="admin-content">
        <div class="admin-orders">
          <h2 class="admin-section-title">Commandes</h2>
          <div class="orders-list" id="adminOrdersList">
            <?php if (empty($orders)): ?>
              <p>Aucune commande enregistrée</p>
            <?php else: ?>
              <?php foreach ($orders as $order): ?>
                <div class="order-card">
                  <div class="order-header">
                    <div class="order-id">Commande #<?php echo substr($order['id'], 0, 8); ?></div>
                    <div class="order-date"><?php echo $order['formatted_date']; ?></div>
                    <div class="status-badge status-<?php echo $order['status']; ?>">
                      <?php 
                        echo $order['status'] === 'sold' ? 'Vendu' : 
                             ($order['status'] === 'cancelled' ? 'Annulé' : 'En attente'); 
                      ?>
                    </div>
                  </div>
                  <div class="order-products">
                    <?php foreach (($order['items'] ?? []) as $item): ?>
                      <div>
                        <strong><?php echo htmlspecialchars($item['name'] ?? 'Produit inconnu'); ?></strong> 
                        (x<?php echo htmlspecialchars($item['quantity'] ?? 1); ?>) - 
                        <?php echo number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2); ?> DT
                      </div>
                    <?php endforeach; ?>
                  </div>
                  <div class="order-total">Total: <?php echo number_format($order['total'] ?? 0, 2); ?> DT</div>
                  <div class="order-actions">
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                      <select name="status" class="form-control" style="width: auto; margin-right:10px;">
                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>En attente</option>
                        <option value="sold" <?php echo $order['status'] === 'sold' ? 'selected' : ''; ?>>Vendu</option>
                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Annulé</option>
                      </select>
                      <button type="submit" name="update_order_status" class="btn-success">
                        <i class="fas fa-sync"></i> Mettre à jour
                      </button>
                    </form>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                      <button type="submit" name="delete_order" class="btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                      </button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="admin-products">
          <!-- Formulaire de mise à jour des produits -->
          <div class="update-product-form" id="updateProductForm">
            <h2 class="admin-section-title">Modifier un produit</h2>
            <form method="post" class="admin-product-form">
              <input type="hidden" name="product_id" id="update_product_id">
              <div class="form-group">
                <label for="update_name">Nom du produit</label>
                <input type="text" id="update_name" name="name" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="update_category">Catégorie</label>
                <input type="text" id="update_category" name="category" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="update_price">Prix (DT)</label>
                <input type="number" id="update_price" name="price" step="0.01" min="0" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="update_description">Description</label>
                <textarea id="update_description" name="description" class="form-control" rows="3" required></textarea>
              </div>
              <div class="form-group">
                <label for="update_badge">Badge (optionnel)</label>
                <input type="text" id="update_badge" name="badge" class="form-control">
              </div>
              <div class="form-group">
                <button type="submit" name="update_product" class="btn checkout-btn" style="width: 100%;">
                  <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
                <button type="button" id="cancelUpdate" class="btn" style="background: #6c757d; width: 100%; margin-top: 10px;">
                  Annuler
                </button>
              </div>
            </form>
          </div>
          
          <!-- Formulaire d'ajout de produit -->
          <h2 class="admin-section-title">Ajouter un produit</h2>
          <form method="post" enctype="multipart/form-data" class="admin-product-form">
            <?php if (!empty($product_success)): ?>
              <div class="notification success show"><?php echo $product_success; ?></div>
            <?php endif; ?>
            <?php if (!empty($product_error)): ?>
              <div class="notification error show"><?php echo $product_error; ?></div>
            <?php endif; ?>
            <div class="form-group">
              <label for="name">Nom du produit</label>
              <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="category">Catégorie</label>
              <input type="text" id="category" name="category" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="price">Prix (DT)</label>
              <input type="number" id="price" name="price" step="0.01" min="0" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="badge">Badge (optionnel)</label>
              <input type="text" id="badge" name="badge" class="form-control">
            </div>
            <div class="form-group">
              <label for="images">Images (plusieurs possibles)</label>
              <input type="file" id="images" name="images[]" class="form-control" multiple accept="image/*" required>
              <div class="image-preview" id="imagePreview"></div>
            </div>
            <button type="submit" name="add_product" class="btn checkout-btn">
              <i class="fas fa-plus"></i> Ajouter le produit
            </button>
          </form>
          
          <!-- Liste des produits existants -->
          <h2 class="admin-section-title">Produits existants</h2>
          <div class="products-grid" id="adminProductsList">
            <?php foreach ($products as $product): ?>
              <div class="product-card">
                <?php if ($product['badge']): ?>
                  <div class="product-badge"><?php echo $product['badge']; ?></div>
                <?php endif; ?>
                <div class="product-image" data-id="<?php echo $product['id']; ?>">
                  <img src="<?php echo $product['images'][0]; ?>" alt="<?php echo $product['name']; ?>">
                </div>
                <div class="product-info">
                  <div class="product-category"><?php echo $product['category']; ?></div>
                  <h3 class="product-name"><?php echo $product['name']; ?></h3>
                  <div class="product-price"><?php echo number_format($product['price'], 2); ?> DT</div>
                  <p class="product-description"><?php echo $product['description']; ?></p>
                  <div class="order-actions">
                    <button class="btn-success edit-product" data-id="<?php echo $product['id']; ?>" 
                            data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                            data-category="<?php echo htmlspecialchars($product['category']); ?>" 
                            data-price="<?php echo $product['price']; ?>" 
                            data-description="<?php echo htmlspecialchars($product['description']); ?>" 
                            data-badge="<?php echo htmlspecialchars($product['badge']); ?>">
                      <i class="fas fa-edit"></i> Modifier
                    </button>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                      <button type="submit" name="delete_product" class="btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer id="contact">
      <div class="footer-content">
        <div class="footer-column">
          <h3>Los angeles boutik</h3>
          <p>
            Votre spécialiste en produits américains importés directement des
            États-Unis. Qualité garantie et livraison rapide.
          </p>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/losangelesboutik?igsh=MWpyaG94YXFuaHI1cg=="><i class="fab fa-instagram"></i></a>
            <a href="https://api.whatsapp.com/send/?phone=21628712893&text&type=phone_number&app_absent=0"><i class="fab fa-whatsapp"></i></a>
          </div>
        </div>

        <div class="footer-column">
          <h3>Liens Rapides</h3>
          <ul class="footer-links">
            <li><a href="#accueil">Accueil</a></li>
            <li><a href="#produits">Produits</a></li>
            <li><a href="#apropos">À Propos</a></li>
            <li><a href="#process">Processus</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>

        <div class="footer-column">
          <h3>Contactez-Nous</h3>
          <ul class="contact-info">
            <li>
              <i class="fas fa-phone"></i>
              <span class="blanc">+216 28 893 712</span>
            </li>
            <li>
              <i class="fas fa-envelope"></i>
              <span>contact@usimports.tn</span>
            </li>
            <li>
              <i class="fab fa-whatsapp"></i>
              <span>+216 28 893 712</span>
            </li>
            <li>
              <i class="fas fa-map-marker-alt"></i>
              <span>Tunis, Tunisie</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>
          &copy; 2025 Los angeles boutik. Tous droits réservés. | Paiement à la
          livraison
        </p>
      </div>
    </footer>

    <!-- Notification -->
    <div class="notification" id="notification"></div>
    
    <script>
        let cart = [];
        let products = <?php echo json_encode($products); ?>;
        let adminAttempts = 0;
        const MAX_ADMIN_ATTEMPTS = 3;
        let blockedUntil = 0;

        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Convertir les prix en nombres
            products = products.map(product => ({
                ...product,
                price: parseFloat(product.price)
            }));
            
            loadCart();
            setupEventListeners();
            
            // Attacher les événements aux images produits
            document.querySelectorAll('.product-image').forEach(image => {
                image.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const product = products.find(p => p.id == productId);
                    if (product) {
                        openGallery(product);
                    }
                });
            });
            
            // Vérifier si l'admin est connecté
            <?php if (isset($_SESSION['admin_logged_in'])): ?>
                document.getElementById('adminPage').style.display = 'block';
            <?php endif; ?>
            
            // Initialiser le filtrage des produits
            initProductFiltering();
            
            // Zone secrète admin
            setupSecretAdminZone();
        });

        // Configurer la zone secrète admin
        function setupSecretAdminZone() {
            const secretZone = document.getElementById('secretAdminZone');
            let clickCount = 0;
            
            secretZone.addEventListener('click', function() {
                clickCount++;
                if(clickCount === 3) {
                    showAdminLogin();
                    clickCount = 0;
                }
                
                // Réinitialiser après 1 seconde
                setTimeout(() => { clickCount = 0; }, 1000);
            });
        }

        // Fonction pour initialiser le filtrage des produits
        function initProductFiltering() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Mettre à jour le bouton actif
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Récupérer la catégorie sélectionnée
                    const category = this.dataset.category;
                    
                    // Filtrer les produits
                    filterProducts(category);
                });
            });
        }

        // Fonction pour filtrer les produits par catégorie
        function filterProducts(category) {
            const productsContainer = document.getElementById('productsContainer');
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Fonction pour configurer les écouteurs d'événements
        function setupEventListeners() {
            // Bouton d'ajout au panier
            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('add-to-cart')) {
                    const productId = e.target.dataset.id;
                    const product = products.find(p => p.id == productId);
                    
                    if (product) {
                        addToCart(product);
                        updateCartUI();
                        showNotification(`${product.name} ajouté au panier!`, 'success');
                    }
                }
            });
            
            // Boutons du panier
            const cartIcon = document.getElementById('cartIcon');
            cartIcon.addEventListener('click', openCart);
            
            // Correction pour iOS
            cartIcon.addEventListener('touchend', function(e) {
                e.preventDefault();
                openCart();
            });

            document.getElementById('closeCart').addEventListener('click', closeCart);
            document.getElementById('overlay').addEventListener('click', closeCart);
            document.getElementById('continueShopping').addEventListener('click', closeCart);
            document.getElementById('checkoutBtn').addEventListener('click', checkout);
            
            // Administration
            document.getElementById('togglePassword').addEventListener('click', togglePasswordVisibility);
            if (document.getElementById('logoutAdmin')) {
                document.getElementById('logoutAdmin').addEventListener('click', logoutAdmin);
            }
            
            // Galerie d'images
            document.getElementById('closeGallery').addEventListener('click', closeGallery);
            document.getElementById('galleryPrev').addEventListener('click', showPrevImage);
            document.getElementById('galleryNext').addEventListener('click', showNextImage);
            
            // Prévisualisation des images pour l'admin
            const imagesInput = document.getElementById('images');
            if (imagesInput) {
                imagesInput.addEventListener('change', function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.innerHTML = '';
                    
                    for (const file of e.target.files) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'preview-image';
                            preview.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            // Boutons de modification des produits
            document.querySelectorAll('.edit-product').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const name = this.dataset.name;
                    const category = this.dataset.category;
                    const price = this.dataset.price;
                    const description = this.dataset.description;
                    const badge = this.dataset.badge;
                    
                    // Remplir le formulaire de modification
                    document.getElementById('update_product_id').value = productId;
                    document.getElementById('update_name').value = name;
                    document.getElementById('update_category').value = category;
                    document.getElementById('update_price').value = price;
                    document.getElementById('update_description').value = description;
                    document.getElementById('update_badge').value = badge || '';
                    
                    // Afficher le formulaire de modification
                    document.getElementById('updateProductForm').style.display = 'block';
                    
                    // Faire défiler jusqu'au formulaire
                    document.getElementById('updateProductForm').scrollIntoView({ behavior: 'smooth' });
                });
            });
            
            // Annuler la modification
            if (document.getElementById('cancelUpdate')) {
                document.getElementById('cancelUpdate').addEventListener('click', function() {
                    document.getElementById('updateProductForm').style.display = 'none';
                });
            }
        }

        // Fonctions pour le panier
        function addToCart(product) {
            const existingItem = cart.find(item => item.id == product.id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    image: product.images[0],
                    quantity: 1
                });
            }
            
            saveCart();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id != id);
            saveCart();
            updateCartUI();
        }

        function updateQuantity(id, quantity) {
            const item = cart.find(item => item.id == id);
            if (item) {
                item.quantity = parseInt(quantity);
                if (item.quantity <= 0) {
                    removeFromCart(id);
                } else {
                    saveCart();
                    updateCartUI();
                }
            }
        }

        function saveCart() {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }

        function loadCart() {
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                
                // Convertir les prix en nombres dans le panier
                cart = cart.map(item => ({
                    ...item,
                    price: parseFloat(item.price)
                }));
                
                updateCartCount();
            }
        }

        function updateCartCount() {
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            document.getElementById('cartCount').textContent = count;
        }

        function updateCartUI() {
            const cartItemsElement = document.getElementById('cartItems');
            const cartTotalElement = document.getElementById('cartTotal');
            
            if (cart.length === 0) {
                cartItemsElement.innerHTML = '<p>Votre panier est vide</p>';
                cartTotalElement.textContent = '0 DT';
                return;
            }
            
            let itemsHTML = '';
            let total = 0;
            
            cart.forEach(item => {
                // S'assurer que le prix est un nombre
                const price = parseFloat(item.price);
                const itemTotal = price * item.quantity;
                total += itemTotal;
                
                itemsHTML += `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                    <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${price.toFixed(2)} DT x ${item.quantity}</div>
                    <div class="cart-item-actions">
                        <button class="quantity-btn minus" data-id="${item.id}">-</button>
                        <input type="number" class="item-quantity" value="${item.quantity}" min="1" data-id="${item.id}">
                        <button class="quantity-btn plus" data-id="${item.id}">+</button>
                        <button class="remove-item" data-id="${item.id}">
                        <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    </div>
                </div>
                `;
            });
            
            cartItemsElement.innerHTML = itemsHTML;
            cartTotalElement.textContent = `${total.toFixed(2)} DT`;
            
            // Ajouter les écouteurs d'événements pour les boutons du panier
            document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const item = cart.find(item => item.id == id);
                    if (item && item.quantity > 1) {
                        updateQuantity(id, item.quantity - 1);
                    }
                });
            });
            
            document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const item = cart.find(item => item.id == id);
                    if (item) {
                        updateQuantity(id, item.quantity + 1);
                    }
                });
            });
            
            document.querySelectorAll('.item-quantity').forEach(input => {
                input.addEventListener('change', function() {
                    const id = this.dataset.id;
                    updateQuantity(id, this.value);
                });
            });
            
            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    removeFromCart(id);
                });
            });
        }

        function openCart() {
            updateCartUI();
            document.getElementById('cartPanel').classList.add('open');
            document.getElementById('overlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeCart() {
            document.getElementById('cartPanel').classList.remove('open');
            document.getElementById('overlay').classList.remove('active');
            document.body.style.overflow = '';
        }

        function checkout() {
            if (cart.length === 0) {
                showNotification('Votre panier est vide!', 'error');
                return;
            }
            
            // Construire le message WhatsApp
            let message = "Bonjour, je souhaite commander les produits suivants :\n\n";
            let total = 0;
            
            cart.forEach(item => {
                const price = parseFloat(item.price);
                const itemTotal = price * item.quantity;
                total += itemTotal;
                message += `- ${item.name} (x${item.quantity}) : ${itemTotal.toFixed(2)} DT\n`;
            });
            
            message += `\nTotal : ${total.toFixed(2)} DT\n`;
            message += "Je confirme la commande avec paiement à la livraison.";
            
            // Sauvegarder la commande côté serveur
            saveOrderToServer(total, cart);
            
            // Ouvrir WhatsApp
            const whatsappNumber = "21628354929";
            const encodedMessage = encodeURIComponent(message);
            
            window.open(`https://wa.me/${whatsappNumber}?text=${encodedMessage}`, '_blank');
            
            // Vider le panier
            cart = [];
            saveCart();
            updateCartUI();
            updateCartCount();
            closeCart();
            
            // Notification de succès
            showNotification('Commande envoyée via WhatsApp!', 'success');
        }

        function saveOrderToServer(total, items) {
            // Envoyer les données au serveur via AJAX
            const data = {
                total: total,
                items: items.map(item => ({
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    quantity: item.quantity
                }))
            };
            
            fetch('save_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (!result.success) {
                    console.error('Erreur lors de la sauvegarde de la commande');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        }

        // Fonctions d'administration
        function showAdminLogin() {
            const now = Date.now();
            if (blockedUntil > now) {
                const remainingTime = Math.ceil((blockedUntil - now) / 1000);
                showNotification(`Trop de tentatives. Réessayez dans ${remainingTime} secondes.`, 'error');
                return;
            }
            
            document.getElementById('adminLogin').style.display = 'block';
            document.getElementById('overlay').classList.add('active');
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('adminPassword');
            const toggleIcon = document.getElementById('togglePassword').querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function logoutAdmin() {
            window.location.href = '?logout=1';
        }

        // Fonction pour afficher les notifications
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type} show`;
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Fonctions pour la galerie d'images
        let currentGalleryProduct = null;
        let currentImageIndex = 0;

        function openGallery(product) {
            if (!product || !product.images || product.images.length === 0) return;

            currentGalleryProduct = product;
            currentImageIndex = 0;
            
            // Afficher la première image
            document.getElementById('galleryMainImage').src = product.images[0];
            
            // Créer les miniatures
            const thumbnailsContainer = document.getElementById('galleryThumbnails');
            thumbnailsContainer.innerHTML = '';
            
            product.images.forEach((image, index) => {
                const thumbnail = document.createElement('img');
                thumbnail.src = image;
                thumbnail.className = 'gallery-thumbnail' + (index === 0 ? ' active' : '');
                thumbnail.dataset.index = index;
                thumbnail.addEventListener('click', () => {
                    showImage(index);
                });
                thumbnailsContainer.appendChild(thumbnail);
            });
            
            // Afficher la galerie
            document.getElementById('galleryModal').classList.add('active');
            document.getElementById('overlay').classList.add('active');
        }

        function closeGallery() {
            document.getElementById('galleryModal').classList.remove('active');
            document.getElementById('overlay').classList.remove('active');
        }

        function showImage(index) {
            if (!currentGalleryProduct || index < 0 || index >= currentGalleryProduct.images.length) return;
            
            currentImageIndex = index;
            document.getElementById('galleryMainImage').src = currentGalleryProduct.images[index];
            
            // Mettre à jour les miniatures actives
            document.querySelectorAll('.gallery-thumbnail').forEach((thumb, i) => {
                if (i === index) {
                    thumb.classList.add('active');
                } else {
                    thumb.classList.remove('active');
                }
            });
        }

        function showPrevImage() {
            if (!currentGalleryProduct) return;
            
            const newIndex = (currentImageIndex - 1 + currentGalleryProduct.images.length) % currentGalleryProduct.images.length;
            showImage(newIndex);
        }

        function showNextImage() {
            if (!currentGalleryProduct) return;
            
            const newIndex = (currentImageIndex + 1) % currentGalleryProduct.images.length;
            showImage(newIndex);
        }
    </script>
</body>
</html>