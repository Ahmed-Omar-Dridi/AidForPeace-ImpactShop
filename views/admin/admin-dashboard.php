 <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AidForPeace</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-dashboard">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-chart-line"></i> <span>AidForPeace</span></h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php?action=admin_dashboard" class="nav-link active">
                    <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
                </a>
                <a href="index.php?action=admin_testimonials" class="nav-link">
                    <i class="fas fa-comment-alt"></i> <span>Témoignages</span>
                </a>
                <a href="index.php?action=admin_comments" class="nav-link">
                    <i class="fas fa-comments"></i> <span>Commentaires</span>
                </a>
                <a href="index.php?action=testimonials" class="nav-link">
                    <i class="fas fa-external-link-alt"></i> <span>Voir le site</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <div class="admin-topbar">
                <h1><i class="fas fa-chart-line"></i> Dashboard Analytics</h1>
                <div class="admin-actions">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher...">
                    </div>
                    <div class="user-menu">
                        <div class="avatar">AD</div>
                        <div>
                            <div style="font-weight: 600;">Admin User</div>
                            <div style="font-size: 12px; color: var(--text-light);">Administrator</div>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card facebook">
                    <div class="stat-icon">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Social Engagement</h3>
                        <div class="stat-number">2,847</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> 12.5%
                        </div>
                    </div>
                </div>

                <div class="stat-card twitter">
                    <div class="stat-icon">
                        <i class="fab fa-twitter"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Community Reach</h3>
                        <div class="stat-number">1,563</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> 8.3%
                        </div>
                    </div>
                </div>

                <div class="stat-card linkedin">
                    <div class="stat-icon">
                        <i class="fab fa-linkedin-in"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Professional Impact</h3>
                        <div class="stat-number">892</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> 5.7%
                        </div>
                    </div>
                </div>

                <div class="stat-card analytics">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Testimonials Growth</h3>
                        <div class="stat-number"><?php echo $testimonials_count; ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> <?php echo round(($testimonials_count / max($testimonials_count - $pending_testimonials, 1)) * 100 - 100, 1); ?>%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-card">
                    <h3><i class="fas fa-chart-line"></i> Traffic Overview</h3>
                    <div class="chart-placeholder">
                        <div class="chart-line"></div>
                    </div>
                </div>

                <div class="world-map-card">
                    <h3><i class="fas fa-globe-americas"></i> Global Reach</h3>
                    <div class="map-placeholder">
                        <div class="map-region"></div>
                        <div class="map-region"></div>
                        <div class="map-region"></div>
                    </div>
                </div>
            </div>

            <!-- Profile & Mini Stats -->
            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-avatar">AD</div>
                    <h3>Admin User</h3>
                    <div class="profile-role">Super Administrator</div>
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="number">48</span>
                            <span class="label">Projects</span>
                        </div>
                        <div class="stat-item">
                            <span class="number">2.4k</span>
                            <span class="label">Followers</span>
                        </div>
                    </div>
                </div>

                <div class="mini-stats-grid">
                    <div class="mini-stat-card visits">
                        <div class="mini-stat-header">
                            <h4>Monthly Visits</h4>
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="mini-stat-number">24,589</div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                    </div>

                    <div class="mini-stat-card users">
                        <div class="mini-stat-header">
                            <h4>Active Users</h4>
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="mini-stat-number"><?php echo $comments_count; ?></div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                    </div>

                    <div class="mini-stat-card sales">
                        <div class="mini-stat-header">
                            <h4>Engagement Rate</h4>
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="mini-stat-number">85%</div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                    </div>

                    <div class="mini-stat-card">
                        <div class="mini-stat-header">
                            <h4>Distribution</h4>
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="pie-chart"></div>
                    </div>
                </div>
            </div>

            <footer class="admin-footer">
                <p>AidForPeace Admin Dashboard &copy; <?php echo date('Y'); ?> | Modern Analytics Interface</p>
            </footer>
        </main>
    </div>

    <script src="../../assets/js/admin.js"></script>
</body>
</html>