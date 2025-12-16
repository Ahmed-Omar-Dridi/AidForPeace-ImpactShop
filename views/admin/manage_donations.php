<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/DonationController.php';

$donationController = new DonationController();
$allDonations = $donationController->getDonationsByStatus('completed');

$pageTitle = 'Manage Donations';
$activePage = 'backoffice';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero py-4">
  <div class="container py-3">
    <div class="row align-items-center">
      <div class="col">
        <div class="hero-badge mb-3"><i class="fas fa-donate"></i><span>Backoffice</span></div>
        <h1 class="fw-bold display-6 mb-1">Donation Management</h1>
        <p class="text-white-50 mb-0">View completed donations, edit or delete records.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="index.php" class="btn btn-outline-light px-4 py-3"><i class="fas fa-arrow-left me-2"></i>Dashboard</a>
      </div>
    </div>
  </div>
</section>

<div class="container my-5">
  <div class="card">
    <div class="card-body">
    <?php if(empty($allDonations)): ?>
      <div class="p-4 text-center text-muted">No completed donations yet.</div>
    <?php else: ?>
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Donor</th>
          <th>Email</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($allDonations as $don): ?>
        <tr>
          <td>#<?= $don['id'] ?></td>
          <td><?= htmlspecialchars($don['donor_name'] ?? $don['first_name'] ?? 'Anonymous') ?></td>
          <td><?= htmlspecialchars($don['donor_email'] ?? $don['email'] ?? '-') ?></td>
          <td><?= number_format($don['amount'], 2) ?> TND</td>
          <td><span class="badge bg-success"><?= ucfirst($don['status']) ?></span></td>
          <td><?= date('M d, Y', strtotime($don['created_at'])) ?></td>
          <td>
            <a class="btn btn-sm btn-primary" href="edit_donation.php?id=<?= $don['id'] ?>"><i class="fas fa-edit"></i></a>
            <a class="btn btn-sm btn-danger" href="delete_donation.php?id=<?= $don['id'] ?>" onclick="return confirm('Delete this donation?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
