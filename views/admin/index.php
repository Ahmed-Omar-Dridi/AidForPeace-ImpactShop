<?php
/**
 * Admin Dashboard - Unified Backend Management
 * All business tasks centralized
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/DATABASE.PHP';

// Allow access for development (REMOVE IN PRODUCTION)
$isAdmin = true;

try {
    $db = Database::getConnexion();
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

$stats = [];

// Users stats - with error handling
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM user");
    $stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['users'] = 0; }

// Products stats
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM products");
    $stats['products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['products'] = 0; }

// Orders stats
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM orders");
    $stats['orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['orders'] = 0; }

try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['pending_orders'] = 0; }

// Revenue
try {
    $stmt = $db->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status != 'cancelled'");
    $stats['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['revenue'] = 0; }

// Categories
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM categories");
    $stats['categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['categories'] = 0; }

// Donations
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM donations");
    $stats['donations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $stmt = $db->query("SELECT COUNT(*) as total FROM donations WHERE status = 'attente'");
    $stats['pending_donations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['donations'] = 0; $stats['pending_donations'] = 0; }

// NGOs
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM ngos");
    $stats['ngos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['ngos'] = 0; }

// Pending testimonials
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM testimonials WHERE status = 'pending'");
    $stats['pending_testimonials'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['pending_testimonials'] = 0; }

// Pending reviews
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM reviews WHERE is_approved = 0");
    $stats['pending_reviews'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['pending_reviews'] = 0; }

// Unread messages
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM contact_messages WHERE is_read = 0");
    $stats['unread_messages'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['unread_messages'] = 0; }

// Pending payments
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM payments WHERE status = 'pending'");
    $stats['pending_payments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['pending_payments'] = 0; }

// Active shippings
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM shippings WHERE status IN ('pending', 'shipped')");
    $stats['active_shippings'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['active_shippings'] = 0; }

// Low stock products
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM products WHERE stock < 10");
    $stats['low_stock'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) { $stats['low_stock'] = 0; }

// Recent orders
try {
    $stmt = $db->query("SELECT o.*, c.first_name, c.last_name, c.email 
                        FROM orders o LEFT JOIN customers c ON o.customer_id = c.id 
                        ORDER BY o.created_at DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $recent_orders = []; }

// Recent users
try {
    $stmt = $db->query("SELECT * FROM user ORDER BY created_at DESC LIMIT 5");
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $recent_users = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AidForPeace</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/global-theme.css">
    <style>
        /* Admin-specific overrides */
        .admin-container { display: flex; min-height: 100vh; position: relative; z-index: 1; }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--glass-bg) !important;
            backdrop-filter: blur(20px);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            border-right: 1px solid var(--glass-border);
            z-index: 100;
        }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid var(--glass-border); text-align: center; }
        .sidebar-header h2 { color: var(--primary); font-size: 1.5rem; }
        .sidebar-header p { color: var(--text-muted); font-size: 0.85rem; margin-top: 5px; }
        .sidebar-menu { padding: 20px 0; }
        .menu-section { margin-bottom: 25px; }
        .menu-section-title {
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 20px;
            margin-bottom: 10px;
        }
        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
            margin: 2px 10px;
            border-radius: 0 12px 12px 0;
        }
        .menu-item:hover, .menu-item.active {
            background: rgba(255, 215, 0, 0.15);
            color: var(--primary);
            border-left-color: var(--primary);
        }
        .menu-item i { width: 25px; margin-right: 10px; }
        .menu-item .badge {
            margin-left: auto;
            background: linear-gradient(135deg, var(--danger), #d32f2f);
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            box-shadow: 0 2px 10px rgba(244, 67, 54, 0.3);
        }

        /* Main Content */
        .main-content { margin-left: 280px; flex: 1; padding: 30px; position: relative; z-index: 1; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { color: var(--text-light); font-size: 1.8rem; }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 55px; height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
        }
        .stat-icon.blue { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.green { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .stat-icon.orange { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-icon.yellow { background: linear-gradient(135deg, #f6d365, #fda085); }
        .stat-icon.purple { background: linear-gradient(135deg, #a18cd1, #fbc2eb); }
        .stat-icon.red { background: linear-gradient(135deg, #ff416c, #ff4b2b); }
        .stat-info h3 { font-size: 1.5rem; color: var(--secondary); }
        .stat-info p { color: #666; font-size: 0.85rem; }
        
        /* Section Title */
        .section-title {
            font-size: 1.2rem;
            color: var(--secondary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i { color: var(--primary); }
        
        /* Task Cards */
        .tasks-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .task-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary);
        }
        .task-card h3 { color: var(--secondary); font-size: 1.1rem; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .task-card h3 i { color: var(--primary); }
        .task-card p { color: #666; font-size: 0.9rem; margin-bottom: 15px; line-height: 1.5; }
        .task-card .task-features { margin-bottom: 15px; }
        .task-card .task-features li { color: #555; font-size: 0.85rem; margin-bottom: 5px; padding-left: 20px; position: relative; }
        .task-card .task-features li:before { content: "✓"; position: absolute; left: 0; color: var(--success); font-weight: bold; }
        .task-card .btn-group { display: flex; gap: 10px; flex-wrap: wrap; }
        
        /* Buttons */
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-size: 0.85rem;
        }
        .btn-primary { background: var(--primary); color: var(--secondary); }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); }
        .btn-secondary { background: var(--secondary); color: white; }
        .btn-secondary:hover { background: var(--secondary-dark); }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
        
        /* Tables */
        .card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); margin-bottom: 30px; }
        .card-header { padding: 20px 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { color: var(--secondary); font-size: 1.1rem; }
        .card-body { padding: 25px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: var(--light); color: var(--secondary); font-weight: 600; }
        tr:hover { background: #f8f9fa; }
        .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-completed { background: #d4edda; color: #155724; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        
        @media (max-width: 992px) {
            .sidebar { width: 70px; }
            .sidebar-header h2, .sidebar-header p, .menu-section-title, .menu-item span { display: none; }
            .menu-item { justify-content: center; padding: 15px; }
            .menu-item i { margin: 0; }
            .main-content { margin-left: 70px; }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-hand-holding-heart"></i> AidForPeace</h2>
                <p>Admin Panel</p>
            </div>
            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-section-title">Main</div>
                    <a href="index.php?controller=admin&action=dashboard" class="menu-item active">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                    </a>
                    <a href="index.php" class="menu-item">
                        <i class="fas fa-home"></i><span>Back to Site</span>
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">Shop Management</div>
                    <a href="index.php?controller=product&action=index" class="menu-item">
                        <i class="fas fa-box"></i><span>Products</span>
                        <?php if($stats['low_stock'] > 0): ?><span class="badge"><?= $stats['low_stock'] ?></span><?php endif; ?>
                    </a>
                    <a href="index.php?controller=category&action=index" class="menu-item">
                        <i class="fas fa-tags"></i><span>Categories</span>
                    </a>
                    <a href="index.php?controller=order&action=index" class="menu-item">
                        <i class="fas fa-shopping-cart"></i><span>Orders</span>
                        <?php if($stats['pending_orders'] > 0): ?><span class="badge"><?= $stats['pending_orders'] ?></span><?php endif; ?>
                    </a>
                    <a href="index.php?controller=payment&action=index" class="menu-item">
                        <i class="fas fa-credit-card"></i><span>Payments</span>
                        <?php if($stats['pending_payments'] > 0): ?><span class="badge"><?= $stats['pending_payments'] ?></span><?php endif; ?>
                    </a>
                    <a href="index.php?controller=shipping&action=index" class="menu-item">
                        <i class="fas fa-truck"></i><span>Shipping</span>
                    </a>
                    <a href="index.php?controller=review&action=index" class="menu-item">
                        <i class="fas fa-star"></i><span>Reviews</span>
                        <?php if($stats['pending_reviews'] > 0): ?><span class="badge"><?= $stats['pending_reviews'] ?></span><?php endif; ?>
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">Donations</div>
                    <a href="index.php?controller=admin&action=pending_donations" class="menu-item">
                        <i class="fas fa-inbox"></i><span>Pending Donations</span>
                        <?php if($stats['pending_donations'] > 0): ?><span class="badge"><?= $stats['pending_donations'] ?></span><?php endif; ?>
                    </a>
                    <a href="index.php?controller=admin&action=manage_donations" class="menu-item">
                        <i class="fas fa-hand-holding-usd"></i><span>Manage Donations</span>
                    </a>
                    <a href="index.php?controller=admin&action=manage_ngos" class="menu-item">
                        <i class="fas fa-building-ngo"></i><span>NGO Partners</span>
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">Community</div>
                    <a href="index.php?page=admin-testimonials" class="menu-item">
                        <i class="fas fa-quote-left"></i><span>Testimonials</span>
                        <?php if($stats['pending_testimonials'] > 0): ?><span class="badge"><?= $stats['pending_testimonials'] ?></span><?php endif; ?>
                    </a>
                    <a href="index.php?controller=loyalty&action=adminIndex" class="menu-item">
                        <i class="fas fa-gem"></i><span>Loyalty Program</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p style="color: #666;"><?= date('l, F j, Y') ?></p>
                </div>
                <div>
                    <span style="color: var(--secondary);">
                        <i class="fas fa-user-shield"></i> 
                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrator') ?>
                    </span>
                </div>
            </div>
            
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['users']) ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['orders']) ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-coins"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['revenue'], 2) ?> TND</h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="fas fa-box"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['products']) ?></h3>
                        <p>Products</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-hand-holding-heart"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['donations']) ?></h3>
                        <p>Donations</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-building-ngo"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['ngos']) ?></h3>
                        <p>NGO Partners</p>
                    </div>
                </div>
            </div>
            
            <!-- Business Tasks Section -->
            <h2 class="section-title"><i class="fas fa-tasks"></i> Business Tasks - Shop Management</h2>
            <div class="tasks-grid">
                <!-- Product Management -->
                <div class="task-card">
                    <h3><i class="fas fa-box"></i> Product Management</h3>
                    <p>Complete CRUD operations for product catalog with inventory tracking.</p>
                    <ul class="task-features">
                        <li>Create new products with images</li>
                        <li>Update product details & pricing</li>
                        <li>Manage stock levels</li>
                        <li>Activate/Deactivate products</li>
                        <li>Bulk import/export</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=product&action=index" class="btn btn-primary"><i class="fas fa-list"></i> View All</a>
                        <a href="index.php?controller=product&action=create" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>
                    </div>
                </div>
                
                <!-- Category Management -->
                <div class="task-card">
                    <h3><i class="fas fa-tags"></i> Category Management</h3>
                    <p>Organize products into hierarchical categories for better navigation.</p>
                    <ul class="task-features">
                        <li>Create parent/child categories</li>
                        <li>Set category images</li>
                        <li>Enable/Disable categories</li>
                        <li>Reorder display sequence</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=category&action=index" class="btn btn-primary"><i class="fas fa-list"></i> View All</a>
                        <a href="index.php?controller=category&action=create" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>
                    </div>
                </div>
                
                <!-- Order Management -->
                <div class="task-card">
                    <h3><i class="fas fa-shopping-cart"></i> Order Management</h3>
                    <p>Process and track customer orders from placement to delivery.</p>
                    <ul class="task-features">
                        <li>View order details & items</li>
                        <li>Update order status</li>
                        <li>Process refunds</li>
                        <li>Generate invoices</li>
                        <li>Track order history</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=order&action=index" class="btn btn-primary"><i class="fas fa-list"></i> All Orders</a>
                    </div>
                </div>
                
                <!-- Payment Management -->
                <div class="task-card">
                    <h3><i class="fas fa-credit-card"></i> Payment Processing</h3>
                    <p>Handle payment confirmations, refunds, and transaction records.</p>
                    <ul class="task-features">
                        <li>Confirm pending payments</li>
                        <li>Process refunds</li>
                        <li>View transaction history</li>
                        <li>Generate payment reports</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=payment&action=index" class="btn btn-primary"><i class="fas fa-list"></i> All Payments</a>
                        <a href="index.php?controller=payment&action=pending" class="btn btn-secondary"><i class="fas fa-clock"></i> Pending</a>
                    </div>
                </div>
                
                <!-- Shipping Management -->
                <div class="task-card">
                    <h3><i class="fas fa-truck"></i> Shipping & Delivery</h3>
                    <p>Manage shipments, tracking, and delivery zones.</p>
                    <ul class="task-features">
                        <li>Update shipping status</li>
                        <li>Generate tracking codes</li>
                        <li>Manage delivery zones</li>
                        <li>Set shipping rates</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=shipping&action=index" class="btn btn-primary"><i class="fas fa-list"></i> All Shipments</a>
                        <a href="index.php?controller=shipping&action=zones" class="btn btn-secondary"><i class="fas fa-map"></i> Zones</a>
                    </div>
                </div>
                
                <!-- Review Moderation -->
                <div class="task-card">
                    <h3><i class="fas fa-star"></i> Review Moderation</h3>
                    <p>Moderate customer reviews to maintain quality feedback.</p>
                    <ul class="task-features">
                        <li>Approve/Reject reviews</li>
                        <li>Flag inappropriate content</li>
                        <li>Respond to reviews</li>
                        <li>View rating analytics</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=review&action=index" class="btn btn-primary"><i class="fas fa-list"></i> All Reviews</a>
                        <a href="index.php?controller=review&action=pending" class="btn btn-secondary"><i class="fas fa-clock"></i> Pending</a>
                    </div>
                </div>
            </div>

            <!-- Donation Tasks -->
            <h2 class="section-title"><i class="fas fa-hand-holding-heart"></i> Business Tasks - Donation Management</h2>
            <div class="tasks-grid">
                <!-- Pending Donations -->
                <div class="task-card">
                    <h3><i class="fas fa-inbox"></i> Pending Donations</h3>
                    <p>Review and process incoming donation requests.</p>
                    <ul class="task-features">
                        <li>Accept valid donations</li>
                        <li>Reject fraudulent requests</li>
                        <li>Send confirmation emails</li>
                        <li>Generate donation receipts</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=admin&action=pending_donations" class="btn btn-primary"><i class="fas fa-inbox"></i> View Pending (<?= $stats['pending_donations'] ?>)</a>
                    </div>
                </div>
                
                <!-- Donation Records -->
                <div class="task-card">
                    <h3><i class="fas fa-hand-holding-usd"></i> Donation Records</h3>
                    <p>Manage all donation records and generate reports.</p>
                    <ul class="task-features">
                        <li>View donation history</li>
                        <li>Edit donation details</li>
                        <li>Delete invalid records</li>
                        <li>Export donation data</li>
                        <li>Generate impact reports</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=admin&action=manage_donations" class="btn btn-primary"><i class="fas fa-list"></i> All Donations</a>
                        <a href="index.php?controller=admin&action=search_donations" class="btn btn-secondary"><i class="fas fa-search"></i> Search</a>
                    </div>
                </div>
                
                <!-- NGO Management -->
                <div class="task-card">
                    <h3><i class="fas fa-building-ngo"></i> NGO Partners</h3>
                    <p>Manage partner NGO organizations and their profiles.</p>
                    <ul class="task-features">
                        <li>Add new NGO partners</li>
                        <li>Update NGO information</li>
                        <li>Upload NGO images</li>
                        <li>View donation statistics</li>
                        <li>Manage partnerships</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=admin&action=manage_ngos" class="btn btn-primary"><i class="fas fa-list"></i> All NGOs</a>
                        <a href="index.php?controller=admin&action=add_ngo" class="btn btn-success"><i class="fas fa-plus"></i> Add NGO</a>
                    </div>
                </div>
                
                <!-- Rejected Donations -->
                <div class="task-card">
                    <h3><i class="fas fa-times-circle"></i> Rejected Donations</h3>
                    <p>Review rejected donations and manage appeals.</p>
                    <ul class="task-features">
                        <li>View rejection reasons</li>
                        <li>Reconsider appeals</li>
                        <li>Permanently delete</li>
                        <li>Contact donors</li>
                    </ul>
                    <div class="btn-group">
                        <a href="rejected_donations.php" class="btn btn-danger"><i class="fas fa-ban"></i> View Rejected</a>
                    </div>
                </div>
            </div>
            
            <!-- Community Tasks -->
            <h2 class="section-title"><i class="fas fa-users"></i> Business Tasks - Community Management</h2>
            <div class="tasks-grid">
                <!-- Testimonial Moderation -->
                <div class="task-card">
                    <h3><i class="fas fa-quote-left"></i> Testimonial Moderation</h3>
                    <p>Review and publish user testimonials and stories.</p>
                    <ul class="task-features">
                        <li>Approve/Reject testimonials</li>
                        <li>Edit content if needed</li>
                        <li>Feature best stories</li>
                        <li>Manage comments</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?page=admin-testimonials" class="btn btn-primary"><i class="fas fa-list"></i> All Testimonials</a>
                    </div>
                </div>
                
                <!-- Loyalty Program -->
                <div class="task-card">
                    <h3><i class="fas fa-gem"></i> Loyalty Program</h3>
                    <p>Manage customer loyalty points and rewards.</p>
                    <ul class="task-features">
                        <li>View member points</li>
                        <li>Add bonus points</li>
                        <li>Manage rewards catalog</li>
                        <li>Process redemptions</li>
                        <li>Set earning rules</li>
                    </ul>
                    <div class="btn-group">
                        <a href="index.php?controller=loyalty&action=adminIndex" class="btn btn-primary"><i class="fas fa-list"></i> Manage Loyalty</a>
                    </div>
                </div>
                
                <!-- User Management -->
                <div class="task-card">
                    <h3><i class="fas fa-user-cog"></i> User Management</h3>
                    <p>Manage user accounts, roles, and permissions.</p>
                    <ul class="task-features">
                        <li>View all users</li>
                        <li>Edit user profiles</li>
                        <li>Assign roles (Admin/User)</li>
                        <li>Ban/Suspend accounts</li>
                        <li>Reset passwords</li>
                    </ul>
                    <div class="btn-group">
                        <a href="#" class="btn btn-primary"><i class="fas fa-users"></i> All Users</a>
                    </div>
                </div>
                
                <!-- Contact Messages -->
                <div class="task-card">
                    <h3><i class="fas fa-envelope"></i> Contact Messages</h3>
                    <p>Respond to customer inquiries and support requests.</p>
                    <ul class="task-features">
                        <li>View unread messages</li>
                        <li>Reply to inquiries</li>
                        <li>Mark as resolved</li>
                        <li>Archive old messages</li>
                    </ul>
                    <div class="btn-group">
                        <a href="#" class="btn btn-primary"><i class="fas fa-inbox"></i> Messages (<?= $stats['unread_messages'] ?>)</a>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-shopping-cart"></i> Recent Orders</h3>
                    <a href="index.php?controller=order&action=index" class="btn btn-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_orders)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?></td>
                                <td><?= number_format($order['total_amount'] ?? 0, 2) ?> TND</td>
                                <td>
                                    <span class="badge-status badge-<?= $order['status'] ?? 'pending' ?>">
                                        <?= ucfirst($order['status'] ?? 'pending') ?>
                                    </span>
                                </td>
                                <td><?= isset($order['created_at']) ? date('M d, Y', strtotime($order['created_at'])) : 'N/A' ?></td>
                                <td>
                                    <a href="index.php?controller=order&action=view&id=<?= $order['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 20px;">No recent orders</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Users Table -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Recent Users</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_users)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_users as $user): ?>
                            <tr>
                                <td>#<?= $user['id_user'] ?? $user['id'] ?? 'N/A' ?></td>
                                <td><?= htmlspecialchars($user['nom'] ?? $user['username'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($user['role'] ?? 'user') ?></td>
                                <td><?= isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 20px;">No recent users</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Animate stat numbers
        document.querySelectorAll('.stat-info h3').forEach(el => {
            const text = el.textContent;
            const value = parseFloat(text.replace(/[^\d.-]/g, ''));
            if (!isNaN(value) && value > 0) {
                let current = 0;
                const increment = value / 40;
                const suffix = text.includes('TND') ? ' TND' : '';
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= value) {
                        current = value;
                        clearInterval(timer);
                    }
                    el.textContent = suffix ? current.toFixed(2) + suffix : Math.floor(current).toLocaleString();
                }, 25);
            }
        });
    </script>
</body>
</html>
