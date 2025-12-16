 <?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/Testimonial.php');

class TestimonialController {

    public function listTestimonials() {
        $sql = "SELECT * FROM testimonials WHERE status = 'approved' ORDER BY created_at DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function listPendingTestimonials() {
        $sql = "SELECT * FROM testimonials WHERE status = 'pending' ORDER BY created_at DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteTestimonial($id) {
        $sql = "DELETE FROM testimonials WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addTestimonial(Testimonial $testimonial) {
        $sql = "INSERT INTO testimonials VALUES (NULL, :title, :content, :author, :rating, :likes, :shares, :status, :created_at)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'title' => $testimonial->getTitle(),
                'content' => $testimonial->getContent(),
                'author' => $testimonial->getAuthor(),
                'rating' => $testimonial->getRating(),
                'likes' => $testimonial->getLikes() ?? 0,
                'shares' => $testimonial->getShares() ?? 0,
                'status' => $testimonial->getStatus() ?? 'pending',
                'created_at' => $testimonial->getCreatedAt() ? $testimonial->getCreatedAt()->format('Y-m-d H:i:s') : date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateTestimonial(Testimonial $testimonial, $id) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE testimonials SET 
                    title = :title,
                    content = :content,
                    author = :author,
                    rating = :rating,
                    likes = :likes,
                    shares = :shares,
                    status = :status
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'title' => $testimonial->getTitle(),
                'content' => $testimonial->getContent(),
                'author' => $testimonial->getAuthor(),
                'rating' => $testimonial->getRating(),
                'likes' => $testimonial->getLikes(),
                'shares' => $testimonial->getShares(),
                'status' => $testimonial->getStatus()
            ]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function showTestimonial($id) {
        $sql = "SELECT * FROM testimonials WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);

        try {
            $query->execute();
            $testimonial = $query->fetch(PDO::FETCH_ASSOC);
            return $testimonial;
        } catch (Exception $e) {
            die('Error: '. $e->getMessage());
        }
    }

    public function incrementLikes($id) {
        $sql = "UPDATE testimonials SET likes = likes + 1 WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function incrementShares($id) {
        $sql = "UPDATE testimonials SET shares = shares + 1 WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE testimonials SET status = :status WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        $req->bindValue(':status', $status);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getTestimonialsCount() {
        $sql = "SELECT COUNT(*) as count FROM testimonials WHERE status = 'approved'";
        $db = config::getConnexion();
        try {
            $result = $db->query($sql);
            return $result->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getPendingCount() {
        $sql = "SELECT COUNT(*) as count FROM testimonials WHERE status = 'pending'";
        $db = config::getConnexion();
        try {
            $result = $db->query($sql);
            return $result->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
}
?>