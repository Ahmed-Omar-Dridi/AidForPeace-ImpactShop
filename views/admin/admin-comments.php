<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commentaires - AidForPeace</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .comments-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .comments-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .comments-table th,
        .comments-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .comments-table th {
            background: #2c3e50;
            color: white;
            font-weight: bold;
        }
        
        .comments-table tr:hover {
            background: #f8f9fa;
        }
        
        .comment-content {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .status-pending {
            color: #e67e22;
            font-weight: bold;
        }
        
        .status-approved {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-rejected {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 0.8em;
            text-decoration: none;
            border-radius: 3px;
            color: white;
        }
        
        .btn-approve {
            background: #27ae60;
        }
        
        .btn-delete {
            background: #e74c3c;
        }
        
        .btn-view {
            background: #3498db;
        }
        
        .no-comments {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .filters select, .filters button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Gestion des Commentaires - AidForPeace</h1>
        <nav>
            <a href="index.php?action=admin_dashboard">← Dashboard</a>
            <a href="index.php?action=admin_testimonials">Témoignages</a>
            <a href="index.php?action=testimonials">Site Public</a>
        </nav>
    </header>

    <main class="container">
        <h2>Gestion des Commentaires</h2>
        
        <?php if(isset($_GET['message'])): ?>
            <div class="alert success">
                <?php 
                switch($_GET['message']) {
                    case 'approved': echo "Commentaire approuvé avec succès!"; break;
                    case 'deleted': echo "Commentaire supprimé avec succès!"; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="filters">
            <label>Filtrer par statut:</label>
            <select onchange="filterComments(this.value)">
                <option value="all">Tous les commentaires</option>
                <option value="pending">En attente</option>
                <option value="approved">Approuvés</option>
                <option value="rejected">Rejetés</option>
            </select>
            <button onclick="resetFilters()">Réinitialiser</button>
        </div>

        <div class="comments-table">
            <?php if($stmt->rowCount() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Auteur</th>
                        <th>Email</th>
                        <th>Contenu</th>
                        <th>Témoignage</th>
                        <th>Likes</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($comment = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr data-status="<?php echo $comment['status']; ?>">
                        <td><?php echo $comment['id']; ?></td>
                        <td><?php echo htmlspecialchars($comment['name']); ?></td>
                        <td><?php echo htmlspecialchars($comment['email']); ?></td>
                        <td class="comment-content" title="<?php echo htmlspecialchars($comment['content']); ?>">
                            <?php echo htmlspecialchars($comment['content']); ?>
                        </td>
                        <td>
                            <?php if($comment['testimonial_author']): ?>
                                Par <?php echo htmlspecialchars($comment['testimonial_author']); ?>
                            <?php else: ?>
                                Témoignage supprimé
                            <?php endif; ?>
                        </td>
                        <td><?php echo $comment['likes']; ?></td>
                        <td class="status-<?php echo $comment['status']; ?>">
                            <?php 
                            switch($comment['status']) {
                                case 'pending': echo 'En attente'; break;
                                case 'approved': echo 'Approuvé'; break;
                                case 'rejected': echo 'Rejeté'; break;
                            }
                            ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></td>
                        <td class="action-buttons">
                            <?php if($comment['status'] == 'pending'): ?>
                                <a href="index.php?action=approve_comment&id=<?php echo $comment['id']; ?>" 
                                   class="btn-small btn-approve" title="Approuver">
                                    ✓
                                </a>
                            <?php endif; ?>
                            
                            <a href="index.php?action=delete_comment&id=<?php echo $comment['id']; ?>" 
                               class="btn-small btn-delete" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire?')"
                               title="Supprimer">
                                ✕
                            </a>
                            
                            <?php if($comment['testimonial_author']): ?>
                                <a href="index.php?action=show_testimonial&id=<?php echo $comment['testimonial_id']; ?>" 
                                   class="btn-small btn-view" title="Voir le témoignage">
                                    👁
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="no-comments">Aucun commentaire à afficher.</div>
            <?php endif; ?>
        </div>

        <div class="admin-stats" style="margin-top: 30px; padding: 20px; background: white; border-radius: 10px;">
            <h3>Statistiques des commentaires</h3>
            <div style="display: flex; gap: 20px; margin-top: 15px;">
                <div style="flex: 1; text-align: center; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                    <div style="font-size: 2em; font-weight: bold; color: #2c3e50;">
                        <?php echo $stmt->rowCount(); ?>
                    </div>
                    <div>Total commentaires</div>
                </div>
                <div style="flex: 1; text-align: center; padding: 15px; background: #fff3cd; border-radius: 5px;">
                    <div style="font-size: 2em; font-weight: bold; color: #e67e22;">
                        <?php 
                        $pendingCount = 0;
                        $stmt->execute(); // Reset cursor
                        while($comment = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($comment['status'] == 'pending') $pendingCount++;
                        }
                        echo $pendingCount;
                        ?>
                    </div>
                    <div>En attente</div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function filterComments(status) {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (status === 'all' || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        function resetFilters() {
            filterComments('all');
            document.querySelector('select').value = 'all';
        }
        
        // Expand comment content on click
        document.addEventListener('DOMContentLoaded', function() {
            const commentCells = document.querySelectorAll('.comment-content');
            commentCells.forEach(cell => {
                cell.addEventListener('click', function() {
                    const fullContent = this.getAttribute('title');
                    if (fullContent && fullContent !== this.textContent) {
                        this.textContent = fullContent;
                        this.style.whiteSpace = 'normal';
                    }
                });
            });
        });
    </script>
</body>
</html>