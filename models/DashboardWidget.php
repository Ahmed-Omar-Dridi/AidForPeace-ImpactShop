<?php
require_once(__DIR__ . '/../config/config.php');

/**
 * Modèle pour les widgets du tableau de bord personnalisé
 */
class DashboardWidget
{
    public int $id;
    public int $userId;
    public string $widgetType;
    public int $position;
    public string $size;
    public array $config;
    public bool $isVisible;
    public string $createdAt;
    public string $updatedAt;

    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';

    // Types de widgets disponibles
    const TYPE_STATS = 'stats';
    const TYPE_ACTIVITY = 'activity';
    const TYPE_CALENDAR = 'calendar';
    const TYPE_TASKS = 'tasks';
    const TYPE_PROFILE = 'profile';
    const TYPE_NOTIFICATIONS = 'notifications';
    const TYPE_CHART = 'chart';
    const TYPE_QUICK_LINKS = 'quick_links';

    public function __construct(
        int $userId = 0,
        string $widgetType = self::TYPE_STATS,
        int $position = 0,
        string $size = self::SIZE_MEDIUM,
        array $config = [],
        bool $isVisible = true
    ) {
        $this->id = 0;
        $this->userId = $userId;
        $this->widgetType = $widgetType;
        $this->position = $position;
        $this->size = $size;
        $this->config = $config;
        $this->isVisible = $isVisible;
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    // CRUD Methods
    public function create(): bool {
        try {
            $pdo = config::getConnexion();
            $sql = "INSERT INTO dashboard_widgets (user_id, widget_type, widget_position, widget_size, widget_config, is_visible) 
                    VALUES (:user_id, :widget_type, :position, :size, :config, :visible)";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'user_id' => $this->userId,
                'widget_type' => $this->widgetType,
                'position' => $this->position,
                'size' => $this->size,
                'config' => json_encode($this->config),
                'visible' => $this->isVisible ? 1 : 0
            ]);

            if ($result) {
                $this->id = $pdo->lastInsertId();
            }

            return $result;
        } catch (Exception $e) {
            error_log("Erreur création widget: " . $e->getMessage());
            return false;
        }
    }

    public function update(): bool {
        try {
            $pdo = config::getConnexion();
            $sql = "UPDATE dashboard_widgets SET 
                    widget_type = :widget_type,
                    widget_position = :position,
                    widget_size = :size,
                    widget_config = :config,
                    is_visible = :visible
                    WHERE id_widget = :id";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                'widget_type' => $this->widgetType,
                'position' => $this->position,
                'size' => $this->size,
                'config' => json_encode($this->config),
                'visible' => $this->isVisible ? 1 : 0,
                'id' => $this->id
            ]);
        } catch (Exception $e) {
            error_log("Erreur mise à jour widget: " . $e->getMessage());
            return false;
        }
    }

    public function delete(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM dashboard_widgets WHERE id_widget = :id");
            return $stmt->execute(['id' => $this->id]);
        } catch (Exception $e) {
            error_log("Erreur suppression widget: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les widgets d'un utilisateur
     */
    public static function getByUserId(int $userId): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM dashboard_widgets WHERE user_id = :user_id ORDER BY widget_position ASC");
            $stmt->execute(['user_id' => $userId]);
            $results = $stmt->fetchAll();

            $widgets = [];
            foreach ($results as $row) {
                $widget = new DashboardWidget(
                    $row['user_id'],
                    $row['widget_type'],
                    $row['widget_position'],
                    $row['widget_size'],
                    json_decode($row['widget_config'], true) ?? [],
                    (bool)$row['is_visible']
                );
                $widget->id = $row['id_widget'];
                $widget->createdAt = $row['created_at'];
                $widget->updatedAt = $row['updated_at'];
                $widgets[] = $widget;
            }

            return $widgets;
        } catch (Exception $e) {
            error_log("Erreur récupération widgets: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Créer les widgets par défaut pour un nouvel utilisateur
     */
    public static function createDefaultWidgets(int $userId): bool {
        $defaultWidgets = [
            ['type' => self::TYPE_PROFILE, 'position' => 0, 'size' => self::SIZE_MEDIUM],
            ['type' => self::TYPE_STATS, 'position' => 1, 'size' => self::SIZE_MEDIUM],
            ['type' => self::TYPE_ACTIVITY, 'position' => 2, 'size' => self::SIZE_LARGE],
            ['type' => self::TYPE_NOTIFICATIONS, 'position' => 3, 'size' => self::SIZE_SMALL],
        ];

        foreach ($defaultWidgets as $widgetData) {
            $widget = new DashboardWidget(
                $userId,
                $widgetData['type'],
                $widgetData['position'],
                $widgetData['size']
            );
            if (!$widget->create()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtenir le titre du widget
     */
    public function getTitle(): string {
        switch ($this->widgetType) {
            case self::TYPE_STATS:
                return '📊 Statistiques';
            case self::TYPE_ACTIVITY:
                return '📈 Activité récente';
            case self::TYPE_CALENDAR:
                return '📅 Calendrier';
            case self::TYPE_TASKS:
                return '✅ Tâches';
            case self::TYPE_PROFILE:
                return '👤 Profil';
            case self::TYPE_NOTIFICATIONS:
                return '🔔 Notifications';
            case self::TYPE_CHART:
                return '📉 Graphique';
            case self::TYPE_QUICK_LINKS:
                return '🔗 Liens rapides';
            default:
                return 'Widget';
        }
    }

    /**
     * Obtenir la classe CSS pour la taille
     */
    public function getSizeClass(): string {
        return 'widget-' . $this->size;
    }
}
?>
