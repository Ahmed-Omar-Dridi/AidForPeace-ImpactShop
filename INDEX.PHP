<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/DATABASE.PHP';

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Support for legacy 'page' parameter (testimonials, etc.)
$page = isset($_GET['page']) ? $_GET['page'] : null;

try {
    // Handle 'page' parameter for testimonials
    if ($page !== null) {
        switch ($page) {
            case 'add-testimonial':
                require __DIR__ . '/views/messagerie/add-testimonial.php';
                exit;
            case 'testimonials':
                require __DIR__ . '/views/messagerie/testimonials.php';
                exit;
            case 'testimonial-details':
                require __DIR__ . '/views/messagerie/testimonial-details.php';
                exit;
            case 'admin-testimonials':
                require __DIR__ . '/views/admin/admin-testimonials.php';
                exit;
        }
    }

    if ($controller === 'home') {
        if ($action === 'role') {
            require __DIR__ . '/views/home/role_selection.php';
        } elseif ($action === 'donation') {
            require __DIR__ . '/views/home/donation.php';
        } elseif ($action === 'accueil') {
            require __DIR__ . '/views/home/accueil.php';
        } else {
            require __DIR__ . '/views/home/home.php';
        }
    }
    elseif ($controller === 'user') {
        // Route to the user management system
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        
        if ($action === 'index') {
            // Show user home/login page
            require __DIR__ . '/views/user/index.php';
        } else {
            // Route all other user actions to the user system
            require __DIR__ . '/views/user/index.php';
        }
    }
    elseif ($controller === 'map') {
        if ($action === 'index') {
            require __DIR__ . '/views/map/index.php';
        }
    }
    elseif ($controller === 'product') {
        require_once __DIR__ .  '/controllers/ProductController.php';
        $productController = new ProductController();

        if ($action === 'index') {
            $products = $productController->listProducts();
            require __DIR__ . '/views/admin/product_list.php';
        }
        elseif ($action === 'shop') {
            $products = $productController->listProducts();
            $categories = [];
            if (file_exists(__DIR__ . '/controllers/CategoryController.php')) {
                require_once __DIR__ .  '/controllers/CategoryController.php';
                $categoryController = new CategoryController();
                $categories = $categoryController->listActiveCategories();
            }
            require __DIR__ . '/views/shop/product_catalog.php';
        }
        elseif ($action === 'create') {
            $categories = [];
            if (file_exists(__DIR__ . '/controllers/CategoryController.php')) {
                require_once __DIR__ . '/controllers/CategoryController.php';
                $categoryController = new CategoryController();
                $categories = $categoryController->listActiveCategories();
            }
            require __DIR__ . '/views/admin/product_form.php';
        }
        elseif ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name_en' => trim($_POST['name_en']),
                'name_fr' => trim($_POST['name_fr']),
                'description_en' => trim($_POST['description_en']),
                'description_fr' => trim($_POST['description_fr']),
                'price' => floatval($_POST['price']),
                'img_name' => trim($_POST['img_name']),
                'stock' => intval($_POST['stock']),
                'category_id' => ! empty($_POST['category_id']) ? intval($_POST['category_id']) : null
            ];
            $productController->addProduct($data);
            $_SESSION['success'] = "Produit cree avec succes!";
            header("Location: index.php?controller=product&action=index");
            exit();
        }
        elseif ($action === 'edit') {
            $id = intval($_GET['id']);
            if ($id) {
                require_once __DIR__ . '/models/Product.php';
                $product = Product::findById($id);
                $categories = [];
                if (file_exists(__DIR__ . '/controllers/CategoryController.php')) {
                    require_once __DIR__ . '/controllers/CategoryController.php';
                    $categoryController = new CategoryController();
                    $categories = $categoryController->listActiveCategories();
                }
                require __DIR__ . '/views/admin/product_form.php';
            } else {
                header("Location: index.php?controller=product&action=index");
                exit();
            }
        }
        elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $data = [
                'name_en' => trim($_POST['name_en']),
                'name_fr' => trim($_POST['name_fr']),
                'description_en' => trim($_POST['description_en']),
                'description_fr' => trim($_POST['description_fr']),
                'price' => floatval($_POST['price']),
                'img_name' => trim($_POST['img_name']),
                'stock' => intval($_POST['stock']),
                'category_id' => !empty($_POST['category_id']) ?  intval($_POST['category_id']) : null
            ];
            $productController->updateProduct($data, $id);
            $_SESSION['success'] = "Produit mis a jour! ";
            header("Location: index.php?controller=product&action=index");
            exit();
        }
        elseif ($action === 'delete') {
            $id = intval($_GET['id']);
            if ($id) {
                $productController->deleteProduct($id);
                $_SESSION['success'] = "Produit supprime! ";
            }
            header("Location: index.php?controller=product&action=index");
            exit();
        }
    }
    elseif ($controller === 'order') {
        require_once __DIR__ . '/controllers/OrderController.php';
        $orderController = new OrderController();

        if ($action === 'index') {
            $orders = $orderController->listOrders();
            require __DIR__ .  '/views/admin/order_list.php';
        }
        elseif ($action === 'checkout') {
            require __DIR__ . '/views/shop/checkout_form.php';
        }
        elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerData = [
                'first_name' => isset($_POST['first_name']) ? trim($_POST['first_name']) : '',
                'last_name' => isset($_POST['last_name']) ? trim($_POST['last_name']) : '',
                'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
                'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
                'city' => isset($_POST['city']) ? trim($_POST['city']) : '',
                'postal_code' => isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '',
                'country' => isset($_POST['country']) ? trim($_POST['country']) : 'Tunisia'
            ];
            
            // Safely decode cart data
            $cartData = [];
            if (isset($_POST['cart_data']) && !empty($_POST['cart_data'])) {
                $decoded = json_decode($_POST['cart_data'], true);
                if (is_array($decoded) && count($decoded) > 0) {
                    $cartData = $decoded;
                }
            }
            
            $paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'paypal';

            // Validate cart data before creating order
            if (empty($cartData)) {
                $_SESSION['error'] = 'Votre panier est vide. Veuillez ajouter des produits avant de commander.';
                header("Location: index.php?controller=order&action=checkout");
                exit();
            }

            $orderId = $orderController->createOrderWithStock($customerData, $cartData, $paymentMethod);

            if ($orderId === false) {
                $_SESSION['error'] = $orderController->getLastError() ?? 'Erreur lors de la création de la commande.';
                header("Location: index.php?controller=order&action=checkout");
                exit();
            }

            // Get order details for email and loyalty
            $db = Database::getConnexion();
            $stmt = $db->prepare("SELECT o.*, c.id as customer_id, c.email, c.first_name, c.last_name 
                                  FROM orders o JOIN customers c ON o.customer_id = c.id WHERE o.id = :id");
            $stmt->execute(['id' => $orderId]);
            $orderData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (file_exists(__DIR__ . '/controllers/PaymentController.php') && 
                file_exists(__DIR__ . '/models/Payment.php') && 
                file_exists(__DIR__ . '/models/Shipping.php')) {
                require_once __DIR__ . '/controllers/PaymentController.php';
                require_once __DIR__ . '/models/Payment.php';
                require_once __DIR__ . '/models/Shipping.php';
                if (class_exists('PaymentController') && $orderData) {
                    $paymentCtrl = new PaymentController();
                    $paymentCtrl->createPayment($orderId, floatval($orderData['total_amount']), $paymentMethod);
                    
                    // Create shipping and get tracking number
                    $shippingData = [
                        'order_id' => $orderId,
                        'address' => $customerData['address'] ?? '',
                        'city' => $customerData['city'] ?? '',
                        'postal_code' => $customerData['postal_code'] ?? '',
                        'country' => $customerData['country'] ?? 'Tunisia'
                    ];
                    $shippingId = Shipping::create($shippingData);
                    
                    // Get tracking number
                    $shipping = Shipping::findById($shippingId);
                    $trackingNumber = $shipping ? $shipping->getTrackingNumber() : null;
                    
                    // Send confirmation email with tracking code
                    if (file_exists(__DIR__ . '/services/EmailService.php')) {
                        require_once __DIR__ . '/services/EmailService.php';
                        $orderDetails = $orderController->getOrderWithItems($orderId);
                        if ($orderDetails && $trackingNumber) {
                            EmailService::sendOrderConfirmation($orderDetails, $trackingNumber);
                        }
                    }
                }
            }

            // Add loyalty points (1 point per TND)
            if (file_exists(__DIR__ . '/models/LoyaltyPoint.php') && $orderData) {
                require_once __DIR__ . '/models/LoyaltyPoint.php';
                LoyaltyPoint::earnPoints($orderData['customer_id'], $orderId, $orderData['total_amount']);
            }

            $_SESSION['success'] = "Commande creee avec succes! Un email de confirmation vous a ete envoye.";
            header("Location: index.php?controller=order&action=confirmation&id=" . intval($orderId));
            exit();
        }
        elseif ($action === 'confirmation') {
            $orderId = intval($_GET['id']);
            if ($orderId) {
                $orderDetails = $orderController->getOrderWithItems($orderId);
                if ($orderDetails) {
                    require __DIR__ . '/views/shop/order_confirmation.php';
                } else {
                    echo "<p>Commande introuvable.</p>";
                }
            }
        }
        elseif ($action === 'view') {
            $orderId = intval($_GET['id']);
            if ($orderId) {
                $orderDetails = $orderController->getOrderWithItems($orderId);
                if ($orderDetails) {
                    require __DIR__ . '/views/shop/order_confirmation.php';
                } else {
                    echo "<p>Commande introuvable.</p>";
                }
            }
        }
        elseif ($action === 'updateStatus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = intval($_POST['order_id']);
            $status = trim($_POST['status']);
            $orderController->changeOrderStatus($orderId, $status);
            $_SESSION['success'] = "Statut mis a jour. ";
            header("Location: index.php?controller=order&action=index");
            exit();
        }
        elseif ($action === 'delete') {
            $id = intval($_GET['id']);
            if ($id) {
                $orderController->deleteOrder($id);
                $_SESSION['success'] = "Commande supprimee. ";
            }
            header("Location: index.php?controller=order&action=index");
            exit();
        }
    }
    elseif ($controller === 'category') {
        require_once __DIR__ . '/controllers/CategoryController.php';
        $categoryController = new CategoryController();

        if ($action === 'index') {
            $categories = $categoryController->listCategories();
            require __DIR__ . '/views/admin/category_list.php';
        }
        elseif ($action === 'create') {
            $parentCategories = $categoryController->listCategories();
            require __DIR__ . '/views/admin/category_form.php';
        }
        elseif ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name_en' => trim($_POST['name_en']),
                'name_fr' => trim($_POST['name_fr']),
                'description_en' => isset($_POST['description_en']) ? trim($_POST['description_en']) : '',
                'description_fr' => isset($_POST['description_fr']) ?  trim($_POST['description_fr']) : '',
                'parent_id' => !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null,
                'img_name' => isset($_POST['img_name']) ? trim($_POST['img_name']) : ''
            ];
            $categoryController->addCategory($data);
            $_SESSION['success'] = "Categorie creee! ";
            header("Location: index.php?controller=category&action=index");
            exit();
        }
        elseif ($action === 'edit') {
            $id = intval($_GET['id']);
            if ($id) {
                require_once __DIR__ . '/models/Category.php';
                $category = Category::findById($id);
                $parentCategories = $categoryController->listCategories();
                require __DIR__ . '/views/admin/category_form.php';
            } else {
                header("Location: index.php?controller=category&action=index");
                exit();
            }
        }
        elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $data = [
                'name_en' => trim($_POST['name_en']),
                'name_fr' => trim($_POST['name_fr']),
                'description_en' => isset($_POST['description_en']) ? trim($_POST['description_en']) : '',
                'description_fr' => isset($_POST['description_fr']) ?  trim($_POST['description_fr']) : '',
                'parent_id' => !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null,
                'img_name' => isset($_POST['img_name']) ? trim($_POST['img_name']) : '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            $categoryController->updateCategory($data, $id);
            $_SESSION['success'] = "Categorie mise a jour!";
            header("Location: index.php?controller=category&action=index");
            exit();
        }
        elseif ($action === 'delete') {
            $id = intval($_GET['id']);
            if ($id) {
                $categoryController->deleteCategory($id);
                $_SESSION['success'] = "Categorie supprimee!";
            }
            header("Location: index.php?controller=category&action=index");
            exit();
        }
        elseif ($action === 'toggle') {
            $id = intval($_GET['id']);
            if ($id) {
                $categoryController->toggleActive($id);
            }
            header("Location: index.php?controller=category&action=index");
            exit();
        }
    }
    elseif ($controller === 'payment') {
        require_once __DIR__ . '/controllers/PaymentController.php';
        $paymentController = new PaymentController();

        if ($action === 'index') {
            $payments = $paymentController->listPayments();
            require __DIR__ . '/views/admin/payment_list.php';
        }
        elseif ($action === 'pending') {
            $payments = $paymentController->listPendingPayments();
            require __DIR__ . '/views/admin/payment_list.php';
        }
        elseif ($action === 'confirm' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentId = intval($_POST['payment_id']);
            $transactionId = ! empty($_POST['transaction_id']) ? trim($_POST['transaction_id']) : null;
            if ($paymentId) {
                $result = $paymentController->confirmPaymentWithEmail($paymentId, $transactionId);
                $_SESSION['success'] = $result ? "Paiement confirme!" : $paymentController->getLastError();
            }
            header("Location: index.php?controller=payment&action=index");
            exit();
        }
        elseif ($action === 'refund' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentId = intval($_POST['payment_id']);
            if ($paymentId) {
                $result = $paymentController->refundPayment($paymentId);
                $_SESSION['success'] = $result ? "Paiement rembourse." : $paymentController->getLastError();
            }
            header("Location: index.php?controller=payment&action=index");
            exit();
        }
    }
    elseif ($controller === 'shipping') {
        require_once __DIR__ . '/controllers/ShippingController.php';
        $shippingController = new ShippingController();

        if ($action === 'index') {
            $shippings = $shippingController->listShippings();
            require __DIR__ . '/views/admin/shipping_list.php';
        }
        elseif ($action === 'pending') {
            $shippings = $shippingController->listPendingShippings();
            require __DIR__ .  '/views/admin/shipping_list.php';
        }
        elseif ($action === 'updateStatus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $shippingId = intval($_POST['shipping_id']);
            $newStatus = trim($_POST['status']);
            if ($shippingId && $newStatus) {
                $shippingController->updateShippingStatus($shippingId, $newStatus);
                $_SESSION['success'] = "Statut mis a jour.";
            }
            header("Location: index.php?controller=shipping&action=index");
            exit();
        }
        elseif ($action === 'track') {
            $trackingNumber = isset($_GET['tracking']) ? trim($_GET['tracking']) : '';
            if ($trackingNumber) {
                require_once __DIR__ .  '/models/Shipping.php';
                $shipping = Shipping::findByTrackingNumber($trackingNumber);
                if ($shipping) {
                    require __DIR__ . '/views/shop/tracking_result.php';
                } else {
                    $_SESSION['error'] = "Aucune livraison trouvee avec ce code.";
                    require __DIR__ . '/views/shop/tracking_form.php';
                }
            } else {
                require __DIR__ . '/views/shop/tracking_form.php';
            }
        }
        elseif ($action === 'zones') {
            require_once __DIR__ . '/models/DeliveryZone.php';
            $zones = DeliveryZone::getAll();
            require __DIR__ . '/views/shop/delivery_zones.php';
        }
        elseif ($action === 'calculateCost') {
            $shippingController->calculateCost();
        }
    }
    elseif ($controller === 'review') {
        require_once __DIR__ . '/controllers/ReviewController.php';
        $reviewController = new ReviewController();

        if ($action === 'index') {
            $reviews = $reviewController->listReviews();
            require __DIR__ . '/views/admin/review_list.php';
        }
        elseif ($action === 'pending') {
            $reviews = $reviewController->listPendingReviews();
            require __DIR__ . '/views/admin/review_list.php';
        }
        elseif ($action === 'approve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $reviewId = intval($_POST['review_id']);
            if ($reviewId) {
                $reviewController->approveReview($reviewId);
                $_SESSION['success'] = "Avis approuve. ";
            }
            header("Location: index.php?controller=review&action=index");
            exit();
        }
        elseif ($action === 'reject' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $reviewId = intval($_POST['review_id']);
            if ($reviewId) {
                $reviewController->rejectReview($reviewId);
                $_SESSION['success'] = "Avis rejete.";
            }
            header("Location: index.php?controller=review&action=index");
            exit();
        }
        elseif ($action === 'create') {
            $productId = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
            $product = null;
            if ($productId) {
                require_once __DIR__ . '/models/Product.php';
                $product = Product::findById($productId);
            }
            require __DIR__ . '/views/shop/review_form.php';
        }
        elseif ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getConnexion();
            
            $email = trim($_POST['email']);
            $stmt = $db->prepare("SELECT id FROM customers WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($customer) {
                $customerId = $customer['id'];
            } else {
                $stmt = $db->prepare("INSERT INTO customers (first_name, last_name, email, created_at) VALUES (:first_name, :last_name, :email, NOW())");
                $stmt->execute([
                    'first_name' => trim($_POST['first_name']),
                    'last_name' => trim($_POST['last_name']),
                    'email' => $email
                ]);
                $customerId = $db->lastInsertId();
            }
            
            $stmt = $db->prepare("INSERT INTO reviews (product_id, customer_id, rating, title, comment, is_approved, is_verified, created_at) VALUES (:product_id, :customer_id, :rating, :title, :comment, 0, 0, NOW())");
            $stmt->execute([
                'product_id' => intval($_POST['product_id']),
                'customer_id' => $customerId,
                'rating' => intval($_POST['rating']),
                'title' => trim($_POST['title']),
                'comment' => trim($_POST['comment'])
            ]);
            
            $_SESSION['success'] = "Merci pour votre avis! Il sera publie apres validation.";
            header("Location: index.php?controller=product&action=shop");
            exit();
        }
    }
    elseif ($controller === 'dashboard' || $controller === 'admin') {
        // Check admin access - flexible check
        $isAdmin = false;
        
        if (isset($_SESSION['user_id'])) {
            // Check session role first
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                $isAdmin = true;
            } else {
                // Check database for role
                try {
                    $db = Database::getConnexion();
                    $stmt = $db->prepare("SELECT role FROM user WHERE id_user = :id OR id = :id2 LIMIT 1");
                    $stmt->execute(['id' => $_SESSION['user_id'], 'id2' => $_SESSION['user_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user && $user['role'] === 'admin') {
                        $_SESSION['user_role'] = 'admin';
                        $isAdmin = true;
                    }
                } catch (Exception $e) {
                    // Allow access for testing if DB check fails
                    $isAdmin = true;
                }
            }
        }
        
        if (!$isAdmin && !isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to access the dashboard";
            header("Location: index.php?controller=user&action=login");
            exit();
        }
        
        // Route to unified admin dashboard
        if ($action === 'dashboard' || $action === 'index') {
            require __DIR__ . '/views/admin/index.php';
        } elseif ($action === 'pending_donations') {
            require __DIR__ . '/views/admin/pending_donations.php';
        } elseif ($action === 'manage_donations') {
            require __DIR__ . '/views/admin/manage_donations.php';
        } elseif ($action === 'manage_ngos' || $action === 'ngos') {
            require __DIR__ . '/views/admin/manage_ngos.php';
        } elseif ($action === 'edit_ngo') {
            require __DIR__ . '/views/admin/edit_ngo.php';
        } elseif ($action === 'edit_donation') {
            require __DIR__ . '/views/admin/edit_donation.php';
        } elseif ($action === 'delete_donation') {
            require __DIR__ . '/views/admin/delete_donation.php';
        } elseif ($action === 'add_ngo') {
            require __DIR__ . '/views/admin/add_ngo.php';
        } elseif ($action === 'search_donations') {
            require __DIR__ . '/views/admin/search_donations.php';
        } else {
            require_once __DIR__ . '/controllers/DashboardController.php';
            $dashboardController = new DashboardController();
            $dashboardController->index();
        }
    }
    elseif ($controller === 'page') {
        if ($action === 'profile') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error_message'] = "Vous devez être connecté pour accéder à votre profil.";
                header("Location: index.php?controller=user&action=login");
                exit();
            }
            require __DIR__ . '/views/page/profile.php';
        } else {
            // Default page action
            require __DIR__ . '/views/home/home.php';
        }
    }
    elseif ($controller === 'donation') {
        // Donation routes - Faire un Don
        if ($action === 'index' || $action === 'form') {
            require __DIR__ . '/views/donation/donation_form.php';
        }
        elseif ($action === 'donation_form') {
            // Donation form for specific NGO
            require __DIR__ . '/views/donation/donation_form.php';
        }
        elseif ($action === 'ngo_history') {
            // NGO history page
            require __DIR__ . '/views/donation/ngo_history.php';
        }
        elseif ($action === 'ngos') {
            require __DIR__ . '/views/donation/list_ngos.php';
        }
        elseif ($action === 'points') {
            require __DIR__ . '/views/donation/mes_points.php';
        }
        elseif ($action === 'history') {
            require __DIR__ . '/views/donation/ngo_history.php';
        }
        elseif ($action === 'receipt') {
            require __DIR__ . '/views/donation/receipt.php';
        }
        elseif ($action === 'scan') {
            require __DIR__ . '/views/donation/scan.php';
        }
        else {
            require __DIR__ . '/views/donation/donation_form.php';
        }
    }
    elseif ($controller === 'contact') {
        require_once __DIR__ . '/controllers/ContactController.php';
        $contactController = new ContactController();

        if ($action === 'index') {
            require __DIR__ . '/views/contact.php';
        }
        elseif ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $contactController->send();
        }
        else {
            require __DIR__ . '/views/contact.php';
        }
    }
    // Loyalty Program Routes
    elseif ($controller === 'loyalty') {
        require_once __DIR__ . '/controllers/LoyaltyController.php';
        $loyaltyController = new LoyaltyController();

        if ($action === 'index') {
            $loyaltyController->index();
        }
        elseif ($action === 'rewards') {
            $loyaltyController->rewards();
        }
        elseif ($action === 'redeem' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $loyaltyController->redeem();
        }
        elseif ($action === 'checkPoints') {
            $loyaltyController->checkPoints();
        }
        elseif ($action === 'adminIndex') {
            $loyaltyController->adminIndex();
        }
        elseif ($action === 'addBonus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $loyaltyController->addBonus();
        }
        else {
            $loyaltyController->index();
        }
    }
    // Chatbot Routes
    elseif ($controller === 'chatbot') {
        require_once __DIR__ . '/controllers/ChatbotController.php';
        $chatbotController = new ChatbotController();

        if ($action === 'respond') {
            $chatbotController->respond();
        }
        else {
            echo json_encode(['response' => 'Action non reconnue']);
        }
    }
    // Messagerie Routes
    elseif ($controller === 'messagerie') {
        require_once __DIR__ . '/controllers/MessagerieController.php';
        $messagerieController = new MessagerieController();

        if ($action === 'index') {
            require __DIR__ . '/views/messagerie/index.php';
        }
        elseif ($action === 'envoyermessage') {
            require __DIR__ . '/views/messagerie/envoyermessage.php';
        }
        elseif ($action === 'mesmessages') {
            require __DIR__ . '/views/messagerie/mesmessages.php';
        }
        elseif ($action === 'inbox') {
            $user_id = isset($_GET['user']) ? (int)$_GET['user'] : 1;
            $conversations = $messagerieController->getUserConversations($user_id);
            require __DIR__ . '/views/messagerie/inbox.php';
        }
        elseif ($action === 'chat') {
            $user_id = isset($_GET['user']) ? (int)$_GET['user'] : 1;
            $conversation_id = isset($_GET['conv']) ? (int)$_GET['conv'] : 0;
            
            if ($conversation_id) {
                $conversation = $messagerieController->getConversationById($conversation_id, $user_id);
                if ($conversation) {
                    $messages = $messagerieController->getConversationMessages($conversation_id, $user_id);
                    require __DIR__ . '/views/messagerie/chat.php';
                } else {
                    $_SESSION['error'] = "Conversation not found";
                    header("Location: index.php?controller=messagerie&action=inbox&user=" . $user_id);
                    exit();
                }
            }
        }
        elseif ($action === 'new_chat') {
            $user_id = isset($_GET['user']) ? (int)$_GET['user'] : 1;
            require __DIR__ . '/views/messagerie/new_chat.php';
        }
        elseif ($action === 'chatbot') {
            require __DIR__ . '/views/messagerie/chatbot.php';
        }
        elseif ($action === 'get_bot_response' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // API pour le chatbot
            require_once __DIR__ . '/controllers/ChatbotController.php';
            $chatbotController = new ChatbotController();
            
            header('Content-Type: application/json');
            $message = $_POST['message'] ?? '';
            
            if (empty($message)) {
                echo json_encode(['success' => false, 'response' => 'Message vide']);
            } else {
                $response = $chatbotController->processMessage($message);
                echo json_encode(['success' => true, 'response' => $response]);
            }
            exit();
        }
        elseif ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
            $conversation_id = (int)($_POST['conversation_id'] ?? 0);
            $receiver_id = (int)($_POST['receiver_id'] ?? 0);
            $content = trim($_POST['message'] ?? '');

            if ($conversation_id && $receiver_id && !empty($content)) {
                $messagerieController->sendMessage($conversation_id, $user_id, $receiver_id, $content);
                header("Location: index.php?controller=messagerie&action=chat&conv=" . $conversation_id . "&user=" . $user_id);
                exit();
            }
        }
        elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
            $message_id = (int)($_POST['message_id'] ?? 0);
            $conversation_id = (int)($_POST['conversation_id'] ?? 0);

            if ($message_id) {
                $messagerieController->deleteMessage($message_id, $user_id);
            }
            header("Location: index.php?controller=messagerie&action=chat&conv=" . $conversation_id . "&user=" . $user_id);
            exit();
        }
    }
    // Page Routes (for user profile, etc)
    elseif ($controller === 'page') {
        if ($action === 'profile') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?controller=user&action=login");
                exit();
            }
            require __DIR__ . '/views/page/profile.php';
        }
    }
    // Add zones route to shipping
    elseif ($controller === 'zones') {
        require_once __DIR__ . '/controllers/ShippingController.php';
        $shippingController = new ShippingController();
        $shippingController->zones();
    }
} catch (Exception $e) {
    if (!headers_sent()) {
        echo '<div style="padding:40px;text-align:center;"><h1>Erreur</h1><p>' . htmlspecialchars($e->getMessage()) . '</p><a href="index.php">Retour</a></div>';
    } else {
        echo '<div style="padding:40px;text-align:center;"><h1>Erreur</h1><p>' . htmlspecialchars($e->getMessage()) . '</p><a href="index.php">Retour</a></div>';
    }
}
?>
