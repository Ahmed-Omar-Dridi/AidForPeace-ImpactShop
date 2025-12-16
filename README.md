# AidForPeace

Plateforme humanitaire de dons et e-commerce solidaire.

## Fonctionnalités

- **Donations** : Faire des dons aux ONG partenaires
- **Boutique solidaire** : Acheter des produits dont les bénéfices soutiennent des causes
- **Carte mondiale** : Visualiser les zones de crise et les ONG actives
- **Messagerie** : Communication entre utilisateurs
- **Programme de fidélité** : Gagner des points et des récompenses
- **Témoignages** : Partager et lire des expériences

## Installation

### Prérequis
- XAMPP (PHP 8.0+, MySQL 5.7+)
- Navigateur web moderne

### Étapes

1. Cloner le projet dans `htdocs`:
```bash
cd C:\xampp\htdocs
git clone [url-du-repo] AidForPeace
```

2. Importer la base de données:
   - Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
   - Créer une base `aidforpeace_db`
   - Importer `aidforpeace_database_complete.sql`

3. Configurer la connexion:
   - Éditer `config/DATABASE.PHP` si nécessaire

4. Lancer le site:
   - Accéder à http://localhost/AidForPeace

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@aidforpeace.com | password |
| User | marie.dupont@email.com | password |

## Structure

```
AidForPeace/
├── assets/          # CSS, JS, images
├── config/          # Configuration (DB, email, recaptcha)
├── controllers/     # Logique métier
├── models/          # Accès données
├── views/           # Interfaces utilisateur
├── includes/        # Header, footer
└── index.php        # Point d'entrée
```

## Technologies

- PHP 8.x (MVC)
- MySQL
- Bootstrap 5
- JavaScript / jQuery
- Font Awesome

## Auteurs

Projet académique - Décembre 2025

## Licence

Usage éducatif uniquement.
