<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/NGOController.php';

$controller = new NGOController();
$ngos = $controller->listNGOs();

if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $controller->deleteNGO($id);
    header("Location: index.php?controller=admin&action=manage_ngos");
    exit;
}

$pageTitle = 'Manage NGOs';
$activePage = 'backoffice';
require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero py-4">
  <div class="container py-3">
    <div class="row align-items-center">
      <div class="col">
        <div class="hero-badge mb-3"><i class="fas fa-handshake"></i><span>Partners</span></div>
        <h1 class="fw-bold display-6 mb-1">NGO Management</h1>
        <p class="text-white-50 mb-0">Add, edit or remove NGO partners.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="index.php" class="btn btn-outline-light px-4 py-3 me-2"><i class="fas fa-arrow-left me-2"></i>Dashboard</a>
        <a href="add_ngo.php" class="btn btn-warning px-4 py-3"><i class="fas fa-plus me-2"></i>Add NGO</a>
      </div>
    </div>
  </div>
</section>

<div class="container my-5">
  <div class="card">
    <div class="card-body">
    <?php if(empty($ngos) || $ngos->rowCount() == 0): ?>
      <div class="p-4 text-center text-muted">No NGOs registered yet.</div>
    <?php else: ?>
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Country</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($ngo = $ngos->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td>#<?= $ngo['id'] ?></td>
          <td><?= htmlspecialchars($ngo['name']) ?></td>
          <td><?= htmlspecialchars($ngo['country'] ?? '-') ?></td>
          <td><?= htmlspecialchars($ngo['address'] ?? $ngo['email'] ?? '-') ?></td>
          <td>
            <a class="btn btn-sm btn-primary" href="edit_ngo.php?id=<?= $ngo['id'] ?>"><i class="fas fa-edit"></i></a>
            <a class="btn btn-sm btn-danger" href="?delete=<?= $ngo['id'] ?>" onclick="return confirm('Delete this NGO?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
