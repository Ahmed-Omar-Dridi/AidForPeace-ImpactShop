<?php
require_once __DIR__ . "/../models/Review.php";
require_once __DIR__ . "/../config/DATABASE.PHP";

class ReviewController {
    private $lastError = null;

    public function getLastError() {
        return $this->lastError;
    }

    public function listReviews() {
        try {
            return Review::findAll();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function listPendingReviews() {
        try {
            return Review::findPendingApproval();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function showById($id) {
        $review = Review::findById($id);
        if ($review) {
            echo "<h3>Avis #" . $review->getId() .  "</h3>";
            echo "<p>Note: " . $review->getRating() . "/5</p>";
            echo "<p>Commentaire: " . htmlspecialchars($review->getComment()) . "</p>";
        } else {
            echo "<p>Avis introuvable.</p>";
        }
    }

    public function getProductReviews($productId, $approvedOnly = true) {
        try {
            return Review::findByProduct($productId, $approvedOnly);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function getProductAverageRating($productId) {
        try {
            return Review::getAverageRating($productId);
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getProductReviewCount($productId) {
        try {
            return Review::getReviewCount($productId);
        } catch (Exception $e) {
            return 0;
        }
    }

    public function approveReview($reviewId) {
        try {
            $review = Review::findById($reviewId);
            if (!$review) {
                throw new Exception("Avis introuvable.");
            }
            return $review->approve();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function rejectReview($reviewId) {
        try {
            $review = Review::findById($reviewId);
            if (!$review) {
                throw new Exception("Avis introuvable.");
            }
            return $review->reject();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}


