<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/Comment.php');

class CommentController {

    public function listComments() {
        $sql = "SELECT * FROM comments ORDER BY created_at DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function listCommentsByTestimonial($testimonial_id) {
        $sql = "SELECT * FROM comments WHERE testimonial_id = :testimonial_id ORDER BY created_at DESC";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':testimonial_id', $testimonial_id);
        try {
            $req->execute();
            return $req;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteComment($id) {
        $sql = "DELETE FROM comments WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addComment(Comment $comment) {
        $sql = "INSERT INTO comments VALUES (NULL, :testimonial_id, :author, :content, :reactions, :created_at)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'testimonial_id' => $comment->getTestimonialId(),
                'author' => $comment->getAuthor(),
                'content' => $comment->getContent(),
                'reactions' => $comment->getReactions() ?? 0,
                'created_at' => $comment->getCreatedAt() ? $comment->getCreatedAt()->format('Y-m-d H:i:s') : date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateComment(Comment $comment, $id) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE comments SET 
                    testimonial_id = :testimonial_id,
                    author = :author,
                    content = :content,
                    reactions = :reactions
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'testimonial_id' => $comment->getTestimonialId(),
                'author' => $comment->getAuthor(),
                'content' => $comment->getContent(),
                'reactions' => $comment->getReactions()
            ]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function showComment($id) {
        $sql = "SELECT * FROM comments WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);

        try {
            $query->execute();
            $comment = $query->fetch(PDO::FETCH_ASSOC);
            return $comment;
        } catch (Exception $e) {
            die('Error: '. $e->getMessage());
        }
    }

    public function incrementReactions($id) {
        $sql = "UPDATE comments SET reactions = reactions + 1 WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getCommentsCount() {
        $sql = "SELECT COUNT(*) as count FROM comments";
        $db = config::getConnexion();
        try {
            $result = $db->query($sql);
            return $result->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getCommentsCountByTestimonial($testimonial_id) {
        $sql = "SELECT COUNT(*) as count FROM comments WHERE testimonial_id = :testimonial_id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':testimonial_id', $testimonial_id);
        try {
            $req->execute();
            $result = $req->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function listCommentsWithTestimonial() {
        $sql = "SELECT c.*, t.title as testimonial_title 
                FROM comments c 
                LEFT JOIN testimonials t ON c.testimonial_id = t.id 
                ORDER BY c.created_at DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
}
?>