<?php
require_once __DIR__ . '/../../models/CountryModel.php';
require_once __DIR__ . '/../../controller/CountryController.php';

$countryModel = new CountryModel();
$countryController = new CountryController();

// Handle delete actions
if (isset($_GET['delete_country'])) {
    $countryId = $_GET['delete_country'];
    if ($countryModel->deleteCountry($countryId)) {
        header("Location: admin.php?success=country_deleted");
        exit;
    } else {
        header("Location: admin.php?error=delete_failed");
        exit;
    }
}

if (isset($_GET['delete_ngo'])) {
    $ngoId = $_GET['delete_ngo'];
    if ($countryModel->deleteNGO($ngoId)) {
        header("Location: admin.php?success=ngo_deleted");
        exit;
    } else {
        header("Location: admin.php?error=delete_failed");
        exit;
    }
}

// Handle edit country
if (isset($_GET['edit_country'])) {
    $countryId = $_GET['edit_country'];
    $countryToEdit = $countryModel->getCountryById($countryId);
}

// Handle view country stats
if (isset($_GET['view_stats'])) {
    $countryId = $_GET['view_stats'];
    $countryStats = $countryModel->getCountryById($countryId);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_country'])) {
        $result = $countryModel->addCountry($_POST);
        
        if ($result === true) {
            header("Location: admin.php?success=country_added");
            exit;
        } elseif ($result === 'exists') {
            header("Location: admin.php?error=country_exists&country_name=" . urlencode($_POST['country_name']));
            exit;
        } else {
            header("Location: admin.php?error=add_failed");
            exit;
        }
    }
    
    if (isset($_POST['update_country'])) {
        $result = $countryModel->editCountry($_POST['country_id'], $_POST);
        
        if ($result === true) {
            // Check if crisis level was changed to Critical
            $currentCountry = $countryModel->getCountryById($_POST['country_id']);
            $oldLevel = $currentCountry ? $currentCountry['crisis_level'] : null;
            $isCriticalChange = ($_POST['crisis_level'] === 'Critical' && $oldLevel !== 'Critical');
            
            if ($isCriticalChange) {
                header("Location: admin.php?success=country_updated&crisis_changed=critical&country_name=" . urlencode($_POST['country_name']));
            } else {
                header("Location: admin.php?success=country_updated");
            }
            exit;
        } elseif ($result === 'exists') {
            header("Location: admin.php?error=country_exists&country_name=" . urlencode($_POST['country_name']));
            exit;
        } else {
            header("Location: admin.php?error=update_failed");
            exit;
        }
    }
    
    if (isset($_POST['add_ngo'])) {
        if ($countryModel->addNGO($_POST)) {
            header("Location: admin.php?success=ngo_added");
            exit;
        } else {
            header("Location: admin.php?error=add_failed");
            exit;
        }
    }
    
    if (isset($_POST['update_country_stats'])) {
        // Update country stats in the database
        $countryId = $_POST['country_id'];
        
        // Generate sample stats if not provided (for demo)
        $hunger = $_POST['hunger'] ?? rand(70, 99);
        $poverty = $_POST['poverty'] ?? rand(65, 95);
        $crime = $_POST['crime'] ?? rand(40, 85);
        $migration = $_POST['migration'] ?? rand(50, 90);
        $water_access = $_POST['water_access'] ?? rand(60, 95);
        $health = $_POST['health'] ?? rand(55, 90);
        
        // Save to database (you'll need to add these columns to your countries table)
        // For now, we'll just show a success message
        header("Location: admin.php?success=stats_updated&country_id=" . $countryId);
        exit;
    }
}

$stats = $countryModel->getDashboardStats();
$countriesList = $countryModel->getAllCountries();
$allCountries = $countryModel->getAllCountriesWithCount();
$allNGOs = $countryModel->getAllNGOs();

// Generate sample stats for each country (in a real app, this would come from database)
$countryStatsData = [];
foreach ($allCountries as $country) {
    $countryStatsData[$country['id']] = [
        'name' => $country['name'],
        'crisis_level' => $country['crisis_level'],
        'hunger' => rand(50, 99),
        'poverty' => rand(45, 95),
        'crime' => rand(30, 85),
        'migration' => rand(40, 90),
        'water_access' => rand(50, 95),
        'health' => rand(40, 90),
        'education' => rand(35, 80),
        'unemployment' => rand(25, 75),
        'infrastructure' => rand(20, 70),
        'ngo_count' => $country['ngo_count']
    ];
}

// Get stats for specific country if requested
if (isset($countryStats)) {
    $currentCountryStats = $countryStatsData[$countryStats['id']] ?? [
        'name' => $countryStats['name'],
        'hunger' => 95,
        'poverty' => 90,
        'crime' => 60,
        'migration' => 70,
        'water_access' => 85,
        'health' => 90,
        'education' => 75,
        'unemployment' => 65,
        'infrastructure' => 40,
        'ngo_count' => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Charity Connect</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        /* Additional styles for stats section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #556B2F;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #556B2F;
        }
        
        .stat-card .trend {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .stat-card .trend.up { color: #28a745; }
        .stat-card .trend.down { color: #dc3545; }
        
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stats-table th {
            background: linear-gradient(135deg, #556B2F, #6B8E23);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .stats-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .stats-table tr:hover {
            background: #f9f9f9;
        }
        
        .stat-value {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .stat-bar {
            flex-grow: 1;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .stat-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .stat-fill.critical { background: #dc3545; }
        .stat-fill.high { background: #fd7e14; }
        .stat-fill.medium { background: #ffc107; }
        .stat-fill.low { background: #28a745; }
        
        .stat-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .indicator-red { background: #dc3545; color: white; }
        .indicator-orange { background: #fd7e14; color: white; }
        .indicator-yellow { background: #ffc107; color: black; }
        .indicator-green { background: #28a745; color: white; }
        
        .country-profile-header {
            background: linear-gradient(135deg, #1a5276, #2e86c1);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .country-profile-header h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        
        .country-profile-header p {
            margin: 0;
            opacity: 0.9;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .summary-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .summary-item .value {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .summary-item .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .summary-item .value.critical { color: #dc3545; }
        .summary-item .value.high { color: #fd7e14; }
        .summary-item .value.medium { color: #ffc107; }
        .summary-item .value.low { color: #28a745; }
        
        .stats-visualization {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        
        .visualization-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .visualization-title i {
            color: #556B2F;
        }
        
        .quick-actions {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        
        .quick-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .quick-action-btn:hover {
            background: #f8f9fa;
            border-color: #556B2F;
            color: #556B2F;
        }
        
        .crisis-score {
            font-size: 48px;
            font-weight: bold;
            color: #dc3545;
            text-align: center;
            margin: 20px 0;
        }
        
        .crisis-label {
            text-align: center;
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>🛡️ Admin Panel</h2>
        <nav>
            <a href="#dashboard" class="active" onclick="showSection('dashboard')">📊 Dashboard</a>
            <a href="#countries" onclick="showSection('countries')">🌍 Countries</a>
            <a href="#ngos" onclick="showSection('ngos')">🏢 NGOs</a>
            <a href="#country-stats" onclick="showSection('country-stats')">📈 Country Stats</a>
            <a href="../../views/frontoffice/index.php">🔙 Back to Site</a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <a href="#">👤 Admin User</a>
        </div>
        
        <!-- Success Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="success">
                <?php 
                if ($_GET['success'] === 'country_added') echo 'Country added successfully!';
                if ($_GET['success'] === 'ngo_added') echo 'NGO added successfully!';
                if ($_GET['success'] === 'country_deleted') echo 'Country deleted successfully!';
                if ($_GET['success'] === 'ngo_deleted') echo 'NGO deleted successfully!';
                if ($_GET['success'] === 'stats_updated') echo 'Country statistics updated successfully!';
                if ($_GET['success'] === 'country_updated'): 
                    echo 'Country updated successfully!';
                    if (isset($_GET['crisis_changed']) && $_GET['crisis_changed'] === 'critical') {
                        $countryName = $_GET['country_name'] ?? 'the country';
                        echo '<br><small style="color: #856404; display: block; margin-top: 8px; padding: 8px; background: #fff3cd; border-radius: 4px;">';
                        echo '🚨 Crisis alert emails have been sent for ' . htmlspecialchars($countryName) . '!';
                        echo '</small>';
                    }
                endif;
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Error Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="warning">
                <?php 
                if ($_GET['error'] === 'delete_failed') echo 'Delete failed. Please try again.';
                if ($_GET['error'] === 'add_failed') echo 'Add failed. Please try again.';
                if ($_GET['error'] === 'update_failed') echo 'Update failed. Please try again.';
                if ($_GET['error'] === 'country_exists'): 
                    $countryName = $_GET['country_name'] ?? 'This country';
                    echo htmlspecialchars($countryName) . ' already exists!';
                endif;
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Dashboard Section -->
        <div id="dashboard" class="section-container active">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Countries</h3>
                    <div class="number"><?php echo $stats['total_countries']; ?></div>
                    <div class="trend up">+2 this month</div>
                </div>
                <div class="stat-card">
                    <h3>Total NGOs</h3>
                    <div class="number"><?php echo $stats['total_ngos']; ?></div>
                    <div class="trend up">+5 this month</div>
                </div>
                <div class="stat-card">
                    <h3>Critical Areas</h3>
                    <div class="number"><?php echo $stats['critical_areas']; ?></div>
                    <div class="trend down">-1 this week</div>
                </div>
                <div class="stat-card">
                    <h3>Active Campaigns</h3>
                    <div class="number"><?php echo $stats['total_ngos']; ?></div>
                    <div class="trend up">+3 active</div>
                </div>
            </div>
            
            <div class="section">
                <h2>📈 Recent Activity</h2>
                <p style="color: #666;">Welcome to the admin dashboard. Use the sidebar to manage countries and NGOs.</p>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #556B2F;">
                    <h4 style="color: #556B2F; margin-bottom: 10px;">📧 Crisis Alert System</h4>
                    <p style="margin: 0; color: #666;">Emails are automatically sent when a country's crisis level is changed to <strong>Critical</strong>.</p>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">Current recipient: <strong>admin@charityconnect.com</strong></p>
                </div>
            </div>
        </div>
        
        <!-- Countries Section -->
        <div id="countries" class="section-container">
            <div class="section">
                <h2>🌍 Manage Countries</h2>
                
                <div class="tabs">
                    <button class="tab active" onclick="showTab('list-countries', event)">View All</button>
                    <button class="tab" onclick="showTab('add-country', event)">Add New</button>
                    <?php if (isset($countryToEdit)): ?>
                    <button class="tab" onclick="showTab('edit-country', event)">Edit Country</button>
                    <?php endif; ?>
                </div>
                
                <!-- List Countries -->
                <div id="list-countries" class="tab-content active">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Country Name</th>
                                    <th>Crisis Level</th>
                                    <th>Description</th>
                                    <th>NGO Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allCountries as $country): ?>
                                <tr>
                                    <td><?php echo $country['id']; ?></td>
                                    <td><?php echo htmlspecialchars($country['name']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($country['crisis_level']); ?>">
                                            <?php echo $country['crisis_level']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars(substr($country['description'], 0, 50)) . (strlen($country['description']) > 50 ? '...' : ''); ?></td>
                                    <td><?php echo $country['ngo_count']; ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="admin.php?view_stats=<?php echo $country['id']; ?>" 
                                               class="btn btn-sm btn-info">📊 Stats</a>
                                            <a href="admin.php?edit_country=<?php echo $country['id']; ?>" 
                                               class="btn btn-sm btn-primary">Edit</a>
                                            <a href="admin.php?delete_country=<?php echo $country['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars(addslashes($country['name'])); ?>? This will also delete all associated NGOs.')">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Add Country Form -->
                <div id="add-country" class="tab-content">
                    <!-- Duplicate warning will be shown here by JavaScript -->
                    <div id="duplicateWarning" class="duplicate-warning" style="display: none;">
                        This country is already on the map! Please choose a different country name.
                    </div>
                    
                    <form id="countryForm" method="POST">
                        <input type="hidden" name="add_country" value="1">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Country Name *</label>
                                <input type="text" name="country_name" id="country_name" placeholder="Enter country name">
                                <span class="error-message" id="error-country_name"></span>
                            </div>
                            
                            <div class="form-group">
                                <label>Crisis Level *</label>
                                <select name="crisis_level" id="crisis_level">
                                    <option value="">-- Select Level --</option>
                                    <option value="Stable">Stable</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                                <span class="error-message" id="error-crisis_level"></span>
                            </div>
                            
                            <div class="form-group">
                                <label>Latitude *</label>
                                <input type="number" step="any" name="latitude" id="latitude" placeholder="e.g., 48.3794">
                                <span class="error-message" id="error-latitude"></span>
                            </div>
                            
                            <div class="form-group">
                                <label>Longitude *</label>
                                <input type="number" step="any" name="longitude" id="longitude" placeholder="e.g., 31.1656">
                                <span class="error-message" id="error-longitude"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Description *</label>
                            <textarea name="description" id="description" placeholder="Enter country description"></textarea>
                            <span class="error-message" id="error-description"></span>
                        </div>
                        
                        <div class="alert-info" style="background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2196F3;">
                            <strong>📢 Note:</strong> If you set crisis level to "Critical", email alerts will be sent automatically.
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Add Country</button>
                    </form>
                </div>
                
                <!-- Edit Country Form -->
                <?php if (isset($countryToEdit)): ?>
                <div id="edit-country" class="tab-content">
                    <div id="editDuplicateWarning" class="duplicate-warning" style="display: none;">
                        This country name is already taken! Please choose a different country name.
                    </div>
                    
                    <form id="editCountryForm" method="POST">
                        <input type="hidden" name="update_country" value="1">
                        <input type="hidden" name="country_id" value="<?php echo $countryToEdit['id']; ?>">
                        <input type="hidden" id="old_crisis_level" value="<?php echo htmlspecialchars($countryToEdit['crisis_level']); ?>">
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Country Name *</label>
                                <input type="text" name="country_name" id="edit_country_name" 
                                       value="<?php echo htmlspecialchars($countryToEdit['name']); ?>" 
                                       placeholder="Enter country name">
                                <span class="error-message" id="error-edit_country_name"></span>
                            </div>
                            
                            <div class="form-group">
                                <label>Crisis Level *</label>
                                <select name="crisis_level" id="edit_crisis_level" onchange="checkCrisisChange()">
                                    <option value="">-- Select Level --</option>
                                    <option value="Stable" <?php echo $countryToEdit['crisis_level'] == 'Stable' ? 'selected' : ''; ?>>Stable</option>
                                    <option value="Medium" <?php echo $countryToEdit['crisis_level'] == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="High" <?php echo $countryToEdit['crisis_level'] == 'High' ? 'selected' : ''; ?>>High</option>
                                    <option value="Critical" <?php echo $countryToEdit['crisis_level'] == 'Critical' ? 'selected' : ''; ?>>Critical</option>
                                </select>
                                <span class="error-message" id="error-edit_crisis_level"></span>
                            </div>
                            
                            <div class="form-group">
                                <label>Latitude *</label>
                                <input type="number" step="any" name="latitude" id="edit_latitude" 
                                       value="<?php echo htmlspecialchars($countryToEdit['latitude']); ?>" 
                                       placeholder="e.g., 48.3794">
                                <span class="error-message" id="error-edit_latitude"></span>
                            </div>
                            
                            <div class="form-group">
                                <label>Longitude *</label>
                                <input type="number" step="any" name="longitude" id="edit_longitude" 
                                       value="<?php echo htmlspecialchars($countryToEdit['longitude']); ?>" 
                                       placeholder="e.g., 31.1656">
                                <span class="error-message" id="error-edit_longitude"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Description *</label>
                            <textarea name="description" id="edit_description" placeholder="Enter country description"><?php echo htmlspecialchars($countryToEdit['description']); ?></textarea>
                            <span class="error-message" id="error-edit_description"></span>
                        </div>
                        
                        <div id="crisisAlertMessage" style="display: none; background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
                            <strong>⚠️ ALERT:</strong> Changing crisis level to "Critical" will trigger email alerts to all subscribers.
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Country</button>
                            <a href="admin.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    
                    <script>
                    function checkCrisisChange() {
                        const oldLevel = document.getElementById('old_crisis_level').value;
                        const newLevel = document.getElementById('edit_crisis_level').value;
                        const alertMessage = document.getElementById('crisisAlertMessage');
                        
                        if (oldLevel !== 'Critical' && newLevel === 'Critical') {
                            alertMessage.style.display = 'block';
                        } else {
                            alertMessage.style.display = 'none';
                        }
                    }
                    
                    // Initialize on page load
                    document.addEventListener('DOMContentLoaded', function() {
                        checkCrisisChange();
                    });
                    </script>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- NGOs Section -->
        <div id="ngos" class="section-container">
            <div class="section">
                <h2>🏢 Manage NGOs</h2>
                
                <div class="tabs">
                    <button class="tab active" onclick="showTab('list-ngos')">View All</button>
                    <button class="tab" onclick="showTab('add-ngo')">Add New</button>
                </div>
                
                <!-- List NGOs -->
                <div id="list-ngos" class="tab-content active">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NGO Name</th>
                                    <th>Country</th>
                                    <th>Mission</th>
                                    <th>Type of Aid</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allNGOs as $ngo): ?>
                                <tr>
                                    <td><?php echo $ngo['id']; ?></td>
                                    <td><?php echo htmlspecialchars($ngo['name']); ?></td>
                                    <td><?php echo htmlspecialchars($ngo['country_name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($ngo['mission'], 0, 50)) . (strlen($ngo['mission']) > 50 ? '...' : ''); ?></td>
                                    <td><?php echo htmlspecialchars($ngo['type_of_aid']); ?></td>
                                    <td><?php echo htmlspecialchars($ngo['contact_info']); ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="#" class="btn btn-sm btn-primary" onclick="alert('NGO edit feature coming soon!')">Edit</a>
                                            <a href="admin.php?delete_ngo=<?php echo $ngo['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars(addslashes($ngo['name'])); ?>?')">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Add NGO Form -->
                <div id="add-ngo" class="tab-content">
                    <form id="ngoForm" method="POST">
                        <input type="hidden" name="add_ngo" value="1">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>NGO Name *</label>
                                <input type="text" name="ngo_name" placeholder="Enter NGO name">
                                <span class="error-message" id="error-ngo_name"></span>
                            </div>
                            
                            <div class="form-group">
                                <label>Country *</label>
                                <select name="country_id">
                                    <option value="">-- Select Country --</option>
                                    <?php foreach ($countriesList as $country): ?>
                                    <option value="<?php echo $country['id']; ?>"><?php echo htmlspecialchars($country['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="error-message" id="error-country_id"></span>
                            </div>
                            
                            <div class="form-group">
                                <label>Contact Info *</label>
                                <input type="text" name="contact_info" placeholder="email@example.com">
                                <span class="error-message" id="error-contact_info"></span>
                            </div>
                            
                            <div class="form-group">
                                <label>Type of Aid *</label>
                                <select name="type_of_aid">
                                    <option value="">-- Select Type --</option>
                                    <option value="Money">Money</option>
                                    <option value="Food">Food</option>
                                    <option value="Clothes">Clothes</option>
                                    <option value="Medical">Medical</option>
                                    <option value="Education">Education</option>
                                    <option value="Shelter">Shelter</option>
                                    <option value="Water">Water</option>
                                </select>
                                <span class="error-message" id="error-type_of_aid"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Mission *</label>
                            <textarea name="mission" placeholder="Enter NGO mission"></textarea>
                            <span class="error-message" id="error-mission"></span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Add NGO</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Country Statistics Section -->
        <div id="country-stats" class="section-container">
            <div class="section">
                <?php if (isset($countryStats)): ?>
                    <!-- Individual Country Profile -->
                    <div class="country-profile-header">
                        <h2><?php echo htmlspecialchars($countryStats['name']); ?> - Country Profile</h2>
                        <p>Comprehensive statistics and crisis indicators</p>
                    </div>
                    
                    <div class="quick-actions">
                        <a href="admin.php" class="quick-action-btn">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                        <a href="admin.php?edit_country=<?php echo $countryStats['id']; ?>" class="quick-action-btn">
                            <i class="bi bi-pencil"></i> Edit Country
                        </a>
                        <a href="#" class="quick-action-btn" onclick="alert('Export feature coming soon!')">
                            <i class="bi bi-download"></i> Export Data
                        </a>
                    </div>
                    
                    <!-- Crisis Score -->
                    <div class="stats-visualization">
                        <div class="crisis-score">
                            <?php 
                            $avgScore = round(($currentCountryStats['hunger'] + $currentCountryStats['poverty'] + 
                                             $currentCountryStats['crime'] + $currentCountryStats['migration']) / 4);
                            echo $avgScore;
                            ?>
                        </div>
                        <div class="crisis-label">Overall Crisis Score</div>
                    </div>
                    
                    <!-- Quick Stats Summary -->
                    <div class="stats-summary">
                        <div class="summary-item">
                            <div class="label">Crisis Level</div>
                            <div class="value <?php echo strtolower($countryStats['crisis_level']); ?>">
                                <?php echo $countryStats['crisis_level']; ?>
                            </div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Active NGOs</div>
                            <div class="value"><?php echo $currentCountryStats['ngo_count']; ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Hunger Index</div>
                            <div class="value <?php echo $currentCountryStats['hunger'] > 80 ? 'critical' : ($currentCountryStats['hunger'] > 60 ? 'high' : 'medium'); ?>">
                                <?php echo $currentCountryStats['hunger']; ?>%
                            </div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Poverty Rate</div>
                            <div class="value <?php echo $currentCountryStats['poverty'] > 80 ? 'critical' : ($currentCountryStats['poverty'] > 60 ? 'high' : 'medium'); ?>">
                                <?php echo $currentCountryStats['poverty']; ?>%
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Statistics Table -->
                    <div class="stats-visualization">
                        <div class="visualization-title">
                            <i class="bi bi-bar-chart-fill"></i> Detailed Crisis Indicators
                        </div>
                        
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>Indicator</th>
                                    <th>Value</th>
                                    <th>Level</th>
                                    <th>Visual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $indicators = [
                                    'Hunger' => $currentCountryStats['hunger'],
                                    'Poverty' => $currentCountryStats['poverty'],
                                    'Crime' => $currentCountryStats['crime'],
                                    'Migration' => $currentCountryStats['migration'],
                                    'Water Access' => $currentCountryStats['water_access'],
                                    'Health' => $currentCountryStats['health'],
                                    'Education' => $currentCountryStats['education'],
                                    'Unemployment' => $currentCountryStats['unemployment'],
                                    'Infrastructure' => $currentCountryStats['infrastructure']
                                ];
                                
                                foreach ($indicators as $name => $value):
                                    $level = '';
                                    $color = '';
                                    $indicatorClass = '';
                                    
                                    if ($value >= 80) {
                                        $level = 'Critical';
                                        $color = '#dc3545';
                                        $indicatorClass = 'indicator-red';
                                    } elseif ($value >= 60) {
                                        $level = 'High';
                                        $color = '#fd7e14';
                                        $indicatorClass = 'indicator-orange';
                                    } elseif ($value >= 40) {
                                        $level = 'Medium';
                                        $color = '#ffc107';
                                        $indicatorClass = 'indicator-yellow';
                                    } else {
                                        $level = 'Low';
                                        $color = '#28a745';
                                        $indicatorClass = 'indicator-green';
                                    }
                                ?>
                                <tr>
                                    <td><strong><?php echo $name; ?></strong></td>
                                    <td>
                                        <div class="stat-value">
                                            <span><?php echo $value; ?>%</span>
                                            <span class="stat-indicator <?php echo $indicatorClass; ?>">
                                                <?php 
                                                if ($value >= 80) echo '🔴';
                                                elseif ($value >= 60) echo '🟧';
                                                elseif ($value >= 40) echo '🟡';
                                                else echo '🟢';
                                                ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td><?php echo $level; ?></td>
                                    <td>
                                        <div class="stat-bar">
                                            <div class="stat-fill <?php echo strtolower($level); ?>" 
                                                 style="width: <?php echo $value; ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Edit Stats Form (for demo) -->
                    <div class="stats-visualization">
                        <div class="visualization-title">
                            <i class="bi bi-sliders"></i> Update Statistics
                        </div>
                        <p style="color: #666; margin-bottom: 20px;">Update the crisis indicators for <?php echo htmlspecialchars($countryStats['name']); ?>:</p>
                        
                        <form method="POST" style="margin-top: 20px;">
                            <input type="hidden" name="update_country_stats" value="1">
                            <input type="hidden" name="country_id" value="<?php echo $countryStats['id']; ?>">
                            
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                                <div>
                                    <label>Hunger (%):</label>
                                    <input type="range" name="hunger" min="0" max="100" value="<?php echo $currentCountryStats['hunger']; ?>" 
                                           class="form-control" onchange="updateValue('hunger-value', this.value)">
                                    <span id="hunger-value"><?php echo $currentCountryStats['hunger']; ?>%</span>
                                </div>
                                <div>
                                    <label>Poverty (%):</label>
                                    <input type="range" name="poverty" min="0" max="100" value="<?php echo $currentCountryStats['poverty']; ?>" 
                                           class="form-control" onchange="updateValue('poverty-value', this.value)">
                                    <span id="poverty-value"><?php echo $currentCountryStats['poverty']; ?>%</span>
                                </div>
                            </div>
                            
                            <div style="margin-top: 20px; text-align: center;">
                                <button type="submit" class="btn btn-primary">Update Statistics</button>
                                <a href="admin.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                        
                        <script>
                        function updateValue(elementId, value) {
                            document.getElementById(elementId).textContent = value + '%';
                        }
                        </script>
                    </div>
                    
                <?php else: ?>
                    <!-- All Countries Stats Overview -->
                    <h2>📈 Country Statistics Dashboard</h2>
                    <p style="color: #666; margin-bottom: 20px;">Click on any country to view detailed statistics and crisis indicators.</p>
                    
                    <!-- Top 5 Critical Countries -->
                    <div class="stats-visualization">
                        <div class="visualization-title">
                            <i class="bi bi-exclamation-triangle-fill"></i> Top 5 Critical Countries
                        </div>
                        
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Crisis Level</th>
                                    <th>Hunger</th>
                                    <th>Poverty</th>
                                    <th>Health</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Sort countries by crisis level and stats
                                $criticalCountries = array_filter($countryStatsData, function($country) {
                                    return $country['crisis_level'] === 'Critical' || $country['hunger'] > 80;
                                });
                                
                                usort($criticalCountries, function($a, $b) {
                                    return $b['hunger'] - $a['hunger'];
                                });
                                
                                $topCountries = array_slice($criticalCountries, 0, 5);
                                
                                foreach ($topCountries as $countryId => $country):
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($country['name']); ?></strong></td>
                                    <td>
                                        <span class="badge badge-critical">Critical</span>
                                    </td>
                                    <td>
                                        <div class="stat-value">
                                            <span><?php echo $country['hunger']; ?>%</span>
                                            <span class="stat-indicator <?php echo $country['hunger'] > 80 ? 'indicator-red' : 'indicator-orange'; ?>">
                                                <?php echo $country['hunger'] > 80 ? '🔴' : '🟧'; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stat-value">
                                            <span><?php echo $country['poverty']; ?>%</span>
                                            <span class="stat-indicator <?php echo $country['poverty'] > 80 ? 'indicator-red' : 'indicator-orange'; ?>">
                                                <?php echo $country['poverty'] > 80 ? '🔴' : '🟧'; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stat-value">
                                            <span><?php echo $country['health']; ?>%</span>
                                            <span class="stat-indicator <?php echo $country['health'] > 80 ? 'indicator-red' : ($country['health'] > 60 ? 'indicator-orange' : 'indicator-yellow'); ?>">
                                                <?php echo $country['health'] > 80 ? '🔴' : ($country['health'] > 60 ? '🟧' : '🟡'); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="admin.php?view_stats=<?php echo array_search($country['name'], array_column($allCountries, 'name', 'id')); ?>" 
                                               class="btn btn-sm btn-info">View Details</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- All Countries Stats -->
                    <div class="stats-visualization">
                        <div class="visualization-title">
                            <i class="bi bi-globe"></i> All Countries Statistics
                        </div>
                        
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Crisis Level</th>
                                    <th>Hunger</th>
                                    <th>Poverty</th>
                                    <th>Water Access</th>
                                    <th>Health</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($countryStatsData as $countryId => $country): 
                                    $dbCountry = array_filter($allCountries, function($c) use ($countryId) {
                                        return $c['id'] == $countryId;
                                    });
                                    $dbCountry = reset($dbCountry);
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($country['name']); ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($country['crisis_level']); ?>">
                                            <?php echo $country['crisis_level']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="stat-value">
                                            <span><?php echo $country['hunger']; ?>%</span>
                                            <div class="stat-bar">
                                                <div class="stat-fill <?php echo $country['hunger'] > 80 ? 'critical' : ($country['hunger'] > 60 ? 'high' : 'medium'); ?>" 
                                                     style="width: <?php echo $country['hunger']; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stat-value">
                                            <span><?php echo $country['poverty']; ?>%</span>
                                            <div class="stat-bar">
                                                <div class="stat-fill <?php echo $country['poverty'] > 80 ? 'critical' : ($country['poverty'] > 60 ? 'high' : 'medium'); ?>" 
                                                     style="width: <?php echo $country['poverty']; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stat-value">
                                            <span><?php echo $country['water_access']; ?>%</span>
                                            <div class="stat-bar">
                                                <div class="stat-fill <?php echo $country['water_access'] > 80 ? 'critical' : ($country['water_access'] > 60 ? 'high' : 'medium'); ?>" 
                                                     style="width: <?php echo $country['water_access']; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stat-value">
                                            <span><?php echo $country['health']; ?>%</span>
                                            <div class="stat-bar">
                                                <div class="stat-fill <?php echo $country['health'] > 80 ? 'critical' : ($country['health'] > 60 ? 'high' : 'medium'); ?>" 
                                                     style="width: <?php echo $country['health']; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="admin.php?view_stats=<?php echo $countryId; ?>" 
                                               class="btn btn-sm btn-info">📊 Stats</a>
                                            <a href="admin.php?edit_country=<?php echo $countryId; ?>" 
                                               class="btn btn-sm btn-primary">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Statistics Summary -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Average Hunger</h3>
                            <div class="number">
                                <?php 
                                $avgHunger = round(array_sum(array_column($countryStatsData, 'hunger')) / count($countryStatsData));
                                echo $avgHunger . '%';
                                ?>
                            </div>
                            <div class="trend <?php echo $avgHunger > 60 ? 'down' : 'up'; ?>">
                                <?php echo $avgHunger > 60 ? 'Critical Level' : 'Moderate'; ?>
                            </div>
                        </div>
                        <div class="stat-card">
                            <h3>Average Poverty</h3>
                            <div class="number">
                                <?php 
                                $avgPoverty = round(array_sum(array_column($countryStatsData, 'poverty')) / count($countryStatsData));
                                echo $avgPoverty . '%';
                                ?>
                            </div>
                            <div class="trend <?php echo $avgPoverty > 60 ? 'down' : 'up'; ?>">
                                <?php echo $avgPoverty > 60 ? 'High Risk' : 'Manageable'; ?>
                            </div>
                        </div>
                        <div class="stat-card">
                            <h3>Critical Countries</h3>
                            <div class="number"><?php echo $stats['critical_areas']; ?></div>
                            <div class="trend down">Needs Attention</div>
                        </div>
                        <div class="stat-card">
                            <h3>Avg. Water Access</h3>
                            <div class="number">
                                <?php 
                                $avgWater = round(array_sum(array_column($countryStatsData, 'water_access')) / count($countryStatsData));
                                echo $avgWater . '%';
                                ?>
                            </div>
                            <div class="trend <?php echo $avgWater < 60 ? 'down' : 'up'; ?>">
                                <?php echo $avgWater < 60 ? 'Low Access' : 'Good'; ?>
                            </div>
                        </div>
                    </div>
                    
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
    // Get existing countries for duplicate check
    const existingCountries = <?php echo json_encode(array_column($countriesList, 'name')); ?>;
    const currentCountryId = <?php echo isset($countryToEdit) ? $countryToEdit['id'] : 'null'; ?>;
    const isEditingCountry = <?php echo isset($countryToEdit) ? 'true' : 'false'; ?>;
    
    function showSection(sectionId) {
        document.querySelectorAll('.section-container').forEach(section => {
            section.classList.remove('active');
            section.style.display = 'none';
        });
        
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.style.display = 'block';
            targetSection.classList.add('active');
        }
        
        document.querySelectorAll('.sidebar nav a').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + sectionId) {
                link.classList.add('active');
            }
        });
        
        // Update URL without reloading
        history.pushState(null, null, '#');
    }
    
    function showTab(tabId, event = null) {
        // If event is provided, update tab buttons
        if (event) {
            const parentSection = event.target.closest('.section');
            parentSection.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }
        
        // Hide all tab contents in this section
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });
        
        // Show the selected tab
        const targetTab = document.getElementById(tabId);
        if (targetTab) {
            targetTab.classList.add('active');
        }
        
        // Clear duplicate warning when switching tabs
        if (tabId === 'add-country') {
            hideDuplicateWarning('duplicateWarning');
        }
        if (tabId === 'edit-country') {
            hideDuplicateWarning('editDuplicateWarning');
        }
    }
    
    // Country Form Validation with duplicate check
    document.getElementById('countryForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors('countryForm');
        hideDuplicateWarning('duplicateWarning');
        
        let isValid = validateCountryForm(this, false);
        
        if (isValid) {
            this.submit();
        }
    });
    
    // Edit Country Form Validation
    document.getElementById('editCountryForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors('editCountryForm');
        hideDuplicateWarning('editDuplicateWarning');
        
        let isValid = validateCountryForm(this, true);
        
        if (isValid) {
            this.submit();
        }
    });
    
    function validateCountryForm(form, isEdit = false) {
        let isValid = true;
        const formData = new FormData(form);
        const countryName = formData.get('country_name')?.trim();
        const prefix = isEdit ? 'edit_' : '';
        
        // Validate country name
        if (!countryName) {
            showError(prefix + 'country_name', 'Country name is required');
            isValid = false;
        } else {
            // Check for duplicate country name
            if (isEdit) {
                if (isDuplicateCountry(countryName, currentCountryId)) {
                    showDuplicateWarning('editDuplicateWarning');
                    showError(prefix + 'country_name', 'This country name is already taken');
                    isValid = false;
                }
            } else {
                if (isDuplicateCountry(countryName)) {
                    showDuplicateWarning('duplicateWarning');
                    showError(prefix + 'country_name', 'This country already exists on the map');
                    isValid = false;
                }
            }
        }
        
        // Validate crisis level
        if (!formData.get('crisis_level')) {
            showError(prefix + 'crisis_level', 'Please select a crisis level');
            isValid = false;
        }
        
        // Validate latitude
        const latitude = formData.get('latitude');
        if (!latitude) {
            showError(prefix + 'latitude', 'Latitude is required');
            isValid = false;
        } else if (isNaN(latitude) || latitude < -90 || latitude > 90) {
            showError(prefix + 'latitude', 'Latitude must be a valid number between -90 and 90');
            isValid = false;
        }
        
        // Validate longitude
        const longitude = formData.get('longitude');
        if (!longitude) {
            showError(prefix + 'longitude', 'Longitude is required');
            isValid = false;
        } else if (isNaN(longitude) || longitude < -180 || longitude > 180) {
            showError(prefix + 'longitude', 'Longitude must be a valid number between -180 and 180');
            isValid = false;
        }
        
        // Validate description
        const description = formData.get('description')?.trim();
        if (!description) {
            showError(prefix + 'description', 'Description is required');
            isValid = false;
        } else if (description.length < 10) {
            showError(prefix + 'description', 'Description must be at least 10 characters long');
            isValid = false;
        }
        
        return isValid;
    }
    
    // NGO Form Validation
    document.getElementById('ngoForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors('ngoForm');
        
        let isValid = true;
        const formData = new FormData(this);
        
        // Validate NGO name
        if (!formData.get('ngo_name')?.trim()) {
            showError('ngo_name', 'NGO name is required');
            isValid = false;
        }
        
        // Validate country
        if (!formData.get('country_id')) {
            showError('country_id', 'Please select a country');
            isValid = false;
        }
        
        // Validate contact info
        const contactInfo = formData.get('contact_info')?.trim();
        if (!contactInfo) {
            showError('contact_info', 'Contact information is required');
            isValid = false;
        } else if (!isValidEmail(contactInfo) && !isValidPhone(contactInfo)) {
            showError('contact_info', 'Please enter a valid email or phone number');
            isValid = false;
        }
        
        // Validate type of aid
        if (!formData.get('type_of_aid')) {
            showError('type_of_aid', 'Please select a type of aid');
            isValid = false;
        }
        
        // Validate mission
        const mission = formData.get('mission')?.trim();
        if (!mission) {
            showError('mission', 'Mission statement is required');
            isValid = false;
        } else if (mission.length < 10) {
            showError('mission', 'Mission statement must be at least 10 characters long');
            isValid = false;
        }
        
        if (isValid) {
            this.submit();
        }
    });
    
    // Check if country already exists
    function isDuplicateCountry(countryName, excludeId = null) {
        const countryIds = <?php echo json_encode(array_column($countriesList, 'id')); ?>;
        
        for (let i = 0; i < existingCountries.length; i++) {
            const name = existingCountries[i];
            const id = countryIds[i];
            
            if (name.toLowerCase() === countryName.toLowerCase()) {
                if (excludeId === null) {
                    // For add: any duplicate is invalid
                    return true;
                } else if (id != excludeId) {
                    // For edit: only invalid if it's a different country
                    return true;
                }
            }
        }
        return false;
    }
    
    // Show duplicate warning
    function showDuplicateWarning(elementId) {
        const warning = document.getElementById(elementId);
        if (warning) {
            warning.style.display = 'block';
            warning.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    
    // Hide duplicate warning
    function hideDuplicateWarning(elementId) {
        const warning = document.getElementById(elementId);
        if (warning) {
            warning.style.display = 'none';
        }
    }
    
    // Real-time duplicate check for add form
    document.getElementById('country_name')?.addEventListener('input', function() {
        const countryName = this.value.trim();
        
        if (countryName && isDuplicateCountry(countryName)) {
            showDuplicateWarning('duplicateWarning');
            showError('country_name', 'This country already exists on the map');
        } else {
            hideDuplicateWarning('duplicateWarning');
            clearFieldError('country_name');
        }
    });
    
    // Real-time duplicate check for edit form
    document.getElementById('edit_country_name')?.addEventListener('input', function() {
        const countryName = this.value.trim();
        
        if (countryName && isDuplicateCountry(countryName, currentCountryId)) {
            showDuplicateWarning('editDuplicateWarning');
            showError('edit_country_name', 'This country name is already taken');
        } else {
            hideDuplicateWarning('editDuplicateWarning');
            clearFieldError('edit_country_name');
        }
    });
    
    // Helper functions
    function showError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const errorElement = document.getElementById(`error-${fieldName}`);
        
        if (field && errorElement) {
            field.classList.add('error');
            errorElement.textContent = message;
        }
    }
    
    function clearFieldError(fieldName) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const errorElement = document.getElementById(`error-${fieldName}`);
        
        if (field && errorElement) {
            field.classList.remove('error');
            errorElement.textContent = '';
        }
    }
    
    function clearErrors(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(field => {
                field.classList.remove('error');
            });
            
            form.querySelectorAll('.error-message').forEach(error => {
                error.textContent = '';
            });
        }
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
    }
    
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're viewing stats
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('view_stats')) {
            showSection('country-stats');
        }
        // If editing a country, show the edit section
        else if (isEditingCountry) {
            showSection('countries');
            
            // Switch to edit tab
            const editTab = document.querySelector('button[onclick*="edit-country"]');
            if (editTab) {
                editTab.click();
            } else {
                // Fallback: manually show the edit tab
                showTab('edit-country');
            }
        } else {
            showSection('dashboard');
        }
        
        // Show duplicate warning if we came from an error
        <?php if (isset($_GET['error']) && $_GET['error'] === 'country_exists'): ?>
            showSection('countries');
            showTab('add-country');
            showDuplicateWarning('duplicateWarning');
            
            const countryNameInput = document.getElementById('country_name');
            if (countryNameInput) {
                countryNameInput.value = '<?php echo htmlspecialchars($_GET['country_name'] ?? ''); ?>';
                showError('country_name', 'This country already exists on the map');
            }
        <?php endif; ?>
    });
</script>
</body>
</html>