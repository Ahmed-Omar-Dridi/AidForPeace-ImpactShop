<?php
class Testimonial {
    private ?int $id;
    private ?string $title;
    private ?string $content;
    private ?string $author;
    private ?int $rating;
    private ?int $likes;
    private ?int $shares;
    private ?string $status;
    private ?DateTime $created_at;

    // Constructor
    public function __construct(?int $id, ?string $title, ?string $content, ?string $author, ?int $rating, ?int $likes, ?int $shares, ?string $status, ?DateTime $created_at) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
        $this->rating = $rating;
        $this->likes = $likes;
        $this->shares = $shares;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Content</th><th>Author</th><th>Rating</th><th>Likes</th><th>Shares</th><th>Status</th><th>Created At</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->title}</td>";
        echo "<td>" . substr($this->content, 0, 50) . "...</td>"; // Affiche seulement les 50 premiers caractères
        echo "<td>{$this->author}</td>";
        echo "<td>{$this->rating}/5</td>";
        echo "<td>{$this->likes}</td>";
        echo "<td>{$this->shares}</td>";
        echo "<td>{$this->status}</td>";
        echo "<td>" . ($this->created_at ? $this->created_at->format('Y-m-d H:i:s') : '') . "</td>";
        echo "</tr>";
        echo "</table>";
    }

    // Getters and Setters
    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): void {
        $this->title = $title;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): void {
        $this->content = $content;
    }

    public function getAuthor(): ?string {
        return $this->author;
    }

    public function setAuthor(?string $author): void {
        $this->author = $author;
    }

    public function getRating(): ?int {
        return $this->rating;
    }

    public function setRating(?int $rating): void {
        $this->rating = $rating;
    }

    public function getLikes(): ?int {
        return $this->likes;
    }

    public function setLikes(?int $likes): void {
        $this->likes = $likes;
    }

    public function getShares(): ?int {
        return $this->shares;
    }

    public function setShares(?int $shares): void {
        $this->shares = $shares;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function setStatus(?string $status): void {
        $this->status = $status;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->created_at;
    }

    public function setCreatedAt(?DateTime $created_at): void {
        $this->created_at = $created_at;
    }
}
?>