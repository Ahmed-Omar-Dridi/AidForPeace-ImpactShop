<?php
/**
 * Vue: Liste des produits (Admin)
 * Date: 2025-11-25
 * Author: dridi10331
 */

// Convertir PDOStatement en array
$productsArray = [];
if (isset($products)) {
    foreach ($products as $product) {
        $productsArray[] = $product;
    }
}
$products = $productsArray;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - ImpactShop</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #ffb600;
            --primary-dark: #e6a500;
            --secondary: #1e3149;
            --secondary-dark: #15202e;
            --text-dark: #5a6570;
            --heading-color: #282f39;
            --border-color: #ebebeb;
            --bg-light: #f9f9f9;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        /* Header - Bleu Constra */
        header {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            color: white;
            padding: 30px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        header h1 {
            font-size: 2rem;
            font-weight: 800;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .back-btn {
            background: var(--primary);
            color: var(--secondary);
            padding: 12px 30px;
            border: 2px solid var(--primary);
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 3px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .back-btn:hover {
            background: white;
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        
        /* Navigation Tabs */
        .nav-tabs {
            display: flex;
            gap: 0;
            margin-bottom: 40px;
            background: white;
            border-radius: 3px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .nav-item {
            flex: 1;
            padding: 20px 30px;
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            transition: all 0.3s;
            border-right: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .nav-item:last-child {
            border-right: none;
        }
        
        .nav-item:hover {
            background: var(--bg-light);
            color: var(--secondary);
        }
        
        .nav-item.active {
            background: var(--secondary);
            color: white;
        }
        
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--heading-color);
            text-transform: uppercase;
        }
        
        .btn-add {
            background: var(--primary);
            color: var(--secondary);
            padding: 15px 35px;
            border: 2px solid var(--primary);
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 3px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
        }
        
        .btn-add:hover {
            background: var(--secondary);
            border-color: var(--secondary);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Alerts */
        .alert {
            padding: 20px 25px;
            border-radius: 3px;
            margin-bottom: 30px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 3px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: var(--secondary);
            color: white;
        }
        
        thead th {
            padding: 20px 15px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 14px;
            text-align: left;
        }
        
        tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s;
        }
        
        tbody tr:hover {
            background: #f9f9f9;
        }
        
        tbody td {
            padding: 20px 15px;
            vertical-align: middle;
        }
        
        /* Product Image */
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 3px;
            border: 2px solid var(--border-color);
        }
        
        /* Price */
        .price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }
        
        /* Action Buttons */
        .action-btns {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-edit {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        
        .btn-edit:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        
        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 1.8rem;
            color: #999;
            margin-bottom: 10px;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .table-container {
                overflow-x: auto;
            }
            
            table {
                min-width: 900px;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .page-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .nav-tabs {
                flex-direction: column;
            }
            
            .nav-item {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="header-content">
            <h1>
                <i class="fas fa-boxes"></i>
                GESTION DES PRODUITS
            </h1>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                RETOUR À L'ACCUEIL
            </a>
        </div>
    </header>

    <!-- Container -->
    <div class="container">

        <!-- Admin Navigation Bar -->
        <?php include __DIR__ . '/admin_navbar.php'; ?>

        <!-- Alerts -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>Succès!</strong> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Erreur!</strong> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header">
            <h2>LISTE DES PRODUITS</h2>
            <a href="index.php?controller=product&action=create" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                AJOUTER UN PRODUIT
            </a>
        </div>

        <!-- Products Table -->
        <div class="table-container">
            <?php if (!empty($products)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IMAGE</th>
                            <th>NOM (EN)</th>
                            <th>NOM (FR)</th>
                            <th>PRIX</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($product['id']); ?></strong></td>
                                <td>
                                    <img src="assets/images/<?php echo htmlspecialchars($product['img_name']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name_fr']); ?>"
                                         class="product-img"
                                         onerror="this.src='https://via.placeholder.com/80x80/1e3149/ffb600?text=<?php echo urlencode(substr($product['name_fr'], 0, 1)); ?>'">
                                </td>
                                <td><?php echo htmlspecialchars($product['name_en']); ?></td>
                                <td><strong><?php echo htmlspecialchars($product['name_fr']); ?></strong></td>
                                <td>
                                    <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="index.php?controller=product&action=edit&id=<?php echo $product['id']; ?>" 
                                           class="btn-edit">
                                            <i class="fas fa-edit"></i>
                                            MODIFIER
                                        </a>
                                        <form method="get" 
                                              action="index.php" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                            <input type="hidden" name="controller" value="product">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn-delete">
                                                <i class="fas fa-trash"></i>
                                                SUPPRIMER
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>Aucun produit trouvé</h3>
                    <p style="margin-top: 10px;">Commencez par ajouter votre premier produit</p>
                    <a href="index.php?controller=product&action=create" 
                       class="btn-add" 
                       style="margin-top: 30px;">
                        <i class="fas fa-plus-circle"></i>
                        AJOUTER UN PRODUIT
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

</body>
</html>