<?php
/**
 * ChatbotController - Chatbot intelligent pour ImpactShop
 * Répond aux questions des clients sur les produits, commandes, livraisons, etc.
 */

require_once __DIR__ . '/../config/DATABASE.PHP';

class ChatbotController
{
    private $responses;

    public function __construct()
    {
        $this->initResponses();
    }

    /**
     * Initialiser les réponses du chatbot
     */
    private function initResponses()
    {
        $this->responses = [
            'greeting' => [
                'patterns' => ['bonjour', 'salut', 'hello', 'hi', 'bonsoir', 'coucou', 'hey'],
                'responses' => [
                    "Bonjour! 👋 Je suis l'assistant ImpactShop. Comment puis-je vous aider aujourd'hui?",
                    "Salut! 😊 Bienvenue sur ImpactShop. Que puis-je faire pour vous?",
                    "Bonjour! Je suis là pour vous aider. Posez-moi vos questions!"
                ]
            ],
            'products' => [
                'patterns' => ['produit', 'article', 'catalogue', 'acheter', 'commander', 'disponible', 'stock'],
                'responses' => [
                    "🛍️ Nous avons une large gamme de produits humanitaires! Visitez notre <a href='index.php?controller=product&action=shop'>boutique</a> pour découvrir notre catalogue.",
                    "Nos produits sont disponibles dans la <a href='index.php?controller=product&action=shop'>section boutique</a>. Vous y trouverez des kits alimentaires, médicaux, et bien plus!"
                ]
            ],
            'order' => [
                'patterns' => ['commande', 'commander', 'panier', 'acheter', 'passer commande'],
                'responses' => [
                    "📦 Pour passer une commande:\n1. Ajoutez des produits au panier\n2. Cliquez sur 'Commander'\n3. Remplissez vos informations\n4. Confirmez le paiement\n\nVous recevrez un email avec votre code de suivi!",
                    "C'est simple! Visitez la <a href='index.php?controller=product&action=shop'>boutique</a>, ajoutez vos articles au panier, puis finalisez votre commande."
                ]
            ],
            'tracking' => [
                'patterns' => ['suivi', 'livraison', 'colis', 'tracking', 'où est', 'suivre', 'code'],
                'responses' => [
                    "🚚 Pour suivre votre colis, utilisez le code de suivi reçu par email. <a href='index.php?controller=shipping&action=track'>Cliquez ici pour accéder au suivi</a>.",
                    "Vous avez reçu un code de suivi par email (format: IMP-XXXXXX-XXXX). Entrez-le sur notre <a href='index.php?controller=shipping&action=track'>page de suivi</a>."
                ]
            ],
            'payment' => [
                'patterns' => ['paiement', 'payer', 'paypal', 'carte', 'prix', 'coût', 'tarif'],
                'responses' => [
                    "💳 Nous acceptons les paiements via PayPal. Le paiement est sécurisé et vous recevrez une confirmation par email.",
                    "Les paiements sont traités de manière sécurisée via PayPal. Vous pouvez payer par carte ou compte PayPal."
                ]
            ],
            'loyalty' => [
                'patterns' => ['fidélité', 'fidelite', 'points', 'récompense', 'recompense', 'bonus'],
                'responses' => [
                    "🎁 Notre programme de fidélité vous récompense! Gagnez 1 point par TND dépensé. Échangez vos points contre des réductions! <a href='index.php?controller=loyalty&action=index'>Voir mes points</a>",
                    "Avec chaque achat, vous gagnez des points de fidélité! 100 points = 5% de réduction. <a href='index.php?controller=loyalty&action=rewards'>Découvrir les récompenses</a>"
                ]
            ],
            'contact' => [
                'patterns' => ['contact', 'aide', 'support', 'problème', 'question', 'email', 'téléphone'],
                'responses' => [
                    "📧 Besoin d'aide? Contactez-nous:\n- Email: contact@impactshop.tn\n- <a href='index.php?controller=contact&action=index'>Formulaire de contact</a>\n\nNous répondons sous 24h!",
                    "Notre équipe est là pour vous! <a href='index.php?controller=contact&action=index'>Envoyez-nous un message</a> et nous vous répondrons rapidement."
                ]
            ],
            'delivery_zones' => [
                'patterns' => ['zone', 'livraison', 'région', 'ville', 'tunisie', 'délai', 'delai'],
                'responses' => [
                    "🗺️ Nous livrons dans toute la Tunisie!\n- Tunis & banlieue: 2-3 jours\n- Autres régions: 4-7 jours\n\n<a href='index.php?controller=shipping&action=zones'>Voir les zones de livraison</a>",
                    "La livraison est disponible partout en Tunisie. Les délais varient selon votre région. <a href='index.php?controller=shipping&action=zones'>Consultez les zones</a>"
                ]
            ],
            'return' => [
                'patterns' => ['retour', 'rembours', 'annuler', 'échanger', 'problème produit'],
                'responses' => [
                    "↩️ Vous avez 14 jours pour retourner un produit non utilisé. Contactez-nous via le <a href='index.php?controller=contact&action=index'>formulaire</a> pour initier un retour.",
                    "Pour un retour ou remboursement, contactez notre support avec votre numéro de commande. Nous traiterons votre demande rapidement!"
                ]
            ],
            'about' => [
                'patterns' => ['impactshop', 'qui êtes', 'à propos', 'mission', 'humanitaire', 'solidaire'],
                'responses' => [
                    "❤️ ImpactShop est une boutique solidaire d'aide humanitaire. Chaque achat contribue à des causes humanitaires. Merci de nous soutenir!",
                    "Nous sommes une boutique engagée dans l'aide humanitaire. Vos achats font la différence! 🌍"
                ]
            ],
            'thanks' => [
                'patterns' => ['merci', 'super', 'parfait', 'génial', 'excellent', 'top'],
                'responses' => [
                    "Avec plaisir! 😊 N'hésitez pas si vous avez d'autres questions!",
                    "Je suis content d'avoir pu vous aider! Bonne journée! 🌟",
                    "Merci à vous! À bientôt sur ImpactShop! ❤️"
                ]
            ],
            'goodbye' => [
                'patterns' => ['au revoir', 'bye', 'à bientôt', 'ciao', 'bonne journée'],
                'responses' => [
                    "Au revoir! 👋 Merci d'avoir visité ImpactShop. À bientôt!",
                    "Bonne journée! N'hésitez pas à revenir si vous avez des questions! 😊"
                ]
            ]
        ];
    }

    /**
     * Traiter un message et retourner une réponse
     */
    public function processMessage($message)
    {
        $message = strtolower(trim($message));

        // Chercher une correspondance
        foreach ($this->responses as $category => $data) {
            foreach ($data['patterns'] as $pattern) {
                if (strpos($message, $pattern) !== false) {
                    $responses = $data['responses'];
                    return $responses[array_rand($responses)];
                }
            }
        }

        // Recherche de produit spécifique
        if ($this->containsProductQuery($message)) {
            return $this->searchProducts($message);
        }

        // Recherche de commande par numéro
        if (preg_match('/\d{6}/', $message, $matches)) {
            return $this->searchOrder($matches[0]);
        }

        // Réponse par défaut
        return $this->getDefaultResponse();
    }

    /**
     * Vérifier si c'est une recherche de produit
     */
    private function containsProductQuery($message)
    {
        $keywords = ['cherche', 'trouve', 'recherche', 'besoin de', 'je veux'];
        foreach ($keywords as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Rechercher des produits
     */
    private function searchProducts($query)
    {
        try {
            $db = Database::getConnexion();
            $searchTerm = '%' . $query . '%';
            $sql = "SELECT id, name_fr, price FROM products WHERE name_fr LIKE :term OR description_fr LIKE :term LIMIT 3";
            $stmt = $db->prepare($sql);
            $stmt->execute(['term' => $searchTerm]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($products)) {
                $response = "🔍 J'ai trouvé ces produits pour vous:\n";
                foreach ($products as $p) {
                    $response .= "• <a href='index.php?controller=product&action=shop'>" . htmlspecialchars($p['name_fr']) . "</a> - " . number_format($p['price'], 2) . " TND\n";
                }
                return $response;
            }
        } catch (Exception $e) {
            error_log("Chatbot search error: " . $e->getMessage());
        }

        return "Je n'ai pas trouvé de produit correspondant. Visitez notre <a href='index.php?controller=product&action=shop'>boutique</a> pour voir tous nos produits!";
    }

    /**
     * Rechercher une commande
     */
    private function searchOrder($orderId)
    {
        return "📦 Pour voir les détails de la commande #$orderId, veuillez vous connecter ou utiliser votre code de suivi reçu par email sur la <a href='index.php?controller=shipping&action=track'>page de suivi</a>.";
    }

    /**
     * Réponse par défaut
     */
    private function getDefaultResponse()
    {
        $defaults = [
            "🤔 Je ne suis pas sûr de comprendre. Pouvez-vous reformuler?\n\nJe peux vous aider avec:\n• Produits et commandes\n• Suivi de livraison\n• Programme de fidélité\n• Contact et support",
            "Hmm, je n'ai pas compris. Essayez de me demander:\n• Comment passer commande?\n• Où est ma livraison?\n• Quels sont vos produits?\n• Comment fonctionne la fidélité?",
            "Je suis là pour vous aider! Posez-moi des questions sur nos produits, vos commandes, ou le suivi de livraison. 😊"
        ];
        return $defaults[array_rand($defaults)];
    }

    /**
     * API endpoint pour le chatbot
     */
    public function respond()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $message = $input['message'] ?? '';

        if (empty($message)) {
            echo json_encode(['response' => 'Veuillez entrer un message.']);
            return;
        }

        $response = $this->processMessage($message);
        echo json_encode(['response' => $response]);
    }

    /**
     * Suggestions rapides
     */
    public function getSuggestions()
    {
        return [
            'Comment passer commande?',
            'Où est ma livraison?',
            'Programme de fidélité',
            'Zones de livraison',
            'Contacter le support'
        ];
    }
}
