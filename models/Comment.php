<?php
class Comment {
    private ?int $id;
    private ?int $testimonial_id;
    private ?string $author;
    private ?string $content;
    private ?int $reactions;
    private ?DateTime $created_at;

    // Constructor
    public function __construct(?int $id, ?int $testimonial_id, ?string $author, ?string $content, ?int $reactions, ?DateTime $created_at) {
        $this->id = $id;
        $this->testimonial_id = $testimonial_id;
        $this->author = $author;
        $this->content = $content;
        $this->reactions = $reactions;
        $this->created_at = $created_at;
    }

    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Testimonial ID</th><th>Author</th><th>Content</th><th>Reactions</th><th>Created At</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->testimonial_id}</td>";
        echo "<td>{$this->author}</td>";
        echo "<td>" . substr($this->content, 0, 50) . "...</td>"; // Affiche seulement les 50 premiers caractères
        echo "<td>{$this->reactions}</td>";
        echo "<td>" . ($this->created_at ? $this->created_at->format('Y-m-d H:i:s') : '') . "</td>";
        echo "</tr>";
        echo "</table>";
    }

    // Getters and Setters
    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getTestimonialId(): ?int {
        return $this->testimonial_id;
    }

    public function setTestimonialId(?int $testimonial_id): void {
        $this->testimonial_id = $testimonial_id;
    }

    public function getAuthor(): ?string {
        return $this->author;
    }

    public function setAuthor(?string $author): void {
        $this->author = $author;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): void {
        $this->content = $content;
    }

    public function getReactions(): ?int {
        return $this->reactions;
    }

    public function setReactions(?int $reactions): void {
        $this->reactions = $reactions;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->created_at;
    }

    public function setCreatedAt(?DateTime $created_at): void {
        $this->created_at = $created_at;
    }
}
?>