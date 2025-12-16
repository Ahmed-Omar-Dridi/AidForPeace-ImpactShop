<?php
// models/Category.php
// Module metier: Categorie de produits
// Date: 2025-12-02

require_once __DIR__ . '/../config/DATABASE.PHP';

class Category {
    private int $id;
    private string $name_en;
    private string $name_fr;
    private ? string $description_en;
    private ?string $description_fr;
    private ?int $parent_id;
    private ?string $img_name;
    private bool $is_active;
    private ?string $created_at;

    public function __construct(
        int $id,
        string $name_en,
        string $name_fr,
        ? string $description_en = null,
        ?string $description_fr = null,
        ? int $parent_id = null,
        ?string $img_name = null,
        bool $is_active = true,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->name_en = $name_en;
        $this->name_fr = $name_fr;
        $this->description_en = $description_en;
        $this->description_fr = $description_fr;
        $this->parent_id = $parent_id;
        $this->img_name = $img_name;
        $this->is_active = $is_active;
        $this->created_at = $created_at;
    }

    public static function findAll(): array {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM categories ORDER BY name_fr ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): ?Category {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! $row) return null;
        return self::fromRow($row);
    }

    public static function findActive(): array {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY name_fr ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findParentCategories(): array {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM categories WHERE parent_id IS NULL AND is_active = 1 ORDER BY name_fr ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function fromRow(array $row): Category {
        return new Category(
            intval($row['id']),
            $row['name_en'] ?? '',
            $row['name_fr'] ?? '',
            $row['description_en'] ?? null,
            $row['description_fr'] ?? null,
            isset($row['parent_id']) ? intval($row['parent_id']) : null,
            $row['img_name'] ??  null,
            (bool)($row['is_active'] ?? true),
            $row['created_at'] ?? null
        );
    }

    public function getProducts(): array {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM products WHERE category_id = :category_id ORDER BY name_fr ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['category_id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubCategories(): array {
        $db = Database::getConnexion();
        $sql = "SELECT * FROM categories WHERE parent_id = :parent_id AND is_active = 1 ORDER BY name_fr ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['parent_id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductCount(): int {
        $db = Database::getConnexion();
        $sql = "SELECT COUNT(*) as cnt FROM products WHERE category_id = :category_id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['category_id' => $this->id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($row['cnt'] ?? 0);
    }

    public function show(): void {
        echo "<h3>Categorie #" . htmlspecialchars($this->id) .  "</h3>";
        echo "<table border='1' cellpadding='6' style='border-collapse:collapse;'>";
        echo "<tr><th>Champ</th><th>Valeur</th></tr>";
        echo "<tr><td>Nom (FR)</td><td>" . htmlspecialchars($this->name_fr) . "</td></tr>";
        echo "<tr><td>Nom (EN)</td><td>" . htmlspecialchars($this->name_en) .  "</td></tr>";
        echo "<tr><td>Description</td><td>" .  htmlspecialchars($this->description_fr ??  '-') . "</td></tr>";
        echo "<tr><td>Active</td><td>" . ($this->is_active ? 'Oui' : 'Non') . "</td></tr>";
        echo "<tr><td>Produits</td><td>" . $this->getProductCount() . "</td></tr>";
        echo "</table>";
    }

    public function getId(): int { return $this->id; }
    public function getNameEn(): string { return $this->name_en; }
    public function getNameFr(): string { return $this->name_fr; }
    public function getDescriptionEn(): ? string { return $this->description_en; }
    public function getDescriptionFr(): ?string { return $this->description_fr; }
    public function getParentId(): ?int { return $this->parent_id; }
    public function getImgName(): ?string { return $this->img_name; }
    public function isActive(): bool { return $this->is_active; }
    public function getCreatedAt(): ?string { return $this->created_at; }
}
?>