<?php
// controllers/ContactController.php

require_once __DIR__ . '/../config/DATABASE.PHP';

class ContactController {
    public function index() {
        require __DIR__ . '/../views/contact.php';
    }

    public function send() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=contact&action=index");
            exit();
        }
        
        try {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');
            
            if (empty($name) || empty($email) || empty($subject) || empty($message)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires";
                header("Location: index.php?controller=contact&action=index");
                exit();
            }
            
            if (file_exists(__DIR__ . '/../models/Message.php')) {
                require_once __DIR__ . '/../models/Message.php';
                Message::create([
                    'name' => $name,
                    'email' => $email,
                    'subject' => $subject,
                    'message' => $message
                ]);
            }
            
            $_SESSION['success'] = "Votre message a été envoyé avec succès!";
            header("Location: index.php?controller=contact&action=index");
            exit();
            
        } catch (Exception $e) {
            error_log("ContactController::send error: " . $e->getMessage());
            $_SESSION['error'] = "Erreur lors de l'envoi du message";
            header("Location: index.php?controller=contact&action=index");
            exit();
        }
    }
}