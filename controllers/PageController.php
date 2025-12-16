<?php
require_once __DIR__ . '/../core/Controller.php';

/**
 * PageController - Pages publiques
 */
class PageController extends Controller {
    
    public function home(): void {
        $this->render('pages/home', [
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    public function about(): void {
        $this->render('pages/about');
    }
    
    public function contact(): void {
        $this->render('pages/contact');
    }
}
