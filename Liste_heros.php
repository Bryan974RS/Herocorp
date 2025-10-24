<?php
require __DIR__ . '/config.php';
session_start();

// Flash (optionnel)
if (!isset($_SESSION['flash'])) $_SESSION['flash'] = [];

// CSRF simple
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Super Héros — Liste</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Héros</a>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="Formulaire.php">Formulaire</a></li>
      <li class="nav-item"><a class="nav-link" href="Liste_heros.php">Liste des Héros (BDD)</a></li>
    </ul>
  </div>
</nav>

<body class="bg-light">
<div class="container py-5">

  <?php foreach ($_SESSION['flash'] as $f): ?>
    <div class="alert alert-<?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['msg']) ?></div>
  <?php endforeach; $_SESSION['flash'] = []; ?>

  <h1 class="mb-4 text-success fw-bold">Super Héros — Classement</h1>

  <div class="row mb-3">
    <div class="col-md-6">
      <input id="search" type="text" class="form-control" placeholder="🔍 Rechercher (nom, pouvoir, ville)">
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle" id="table">
        <thead class="table-success">
          <tr>
            <th>#</th>
            <th>Pseudonyme</th>
            <th>Pouvoir</th>
            <th>Nationalité</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $stmt = $pdo->query("SELECT * FROM heros ORDER BY rank ASC, id ASC");
            foreach ($stmt as $h):
          ?>
            <tr>
              <td><span class="badge bg-dark"><?= htmlspecialchars($h['rank']) ?></span></td>
              <td><?= htmlspecialchars($h['pseudonyme']) ?></td>
              <td><?= htmlspecialchars($h['pouvoir']) ?></td>
              <td><?= htmlspecialchars($h['ville']) ?></td>
              <td class="text-end">
                <!-- Modifier -->
                <a class="btn btn-sm btn-outline-primary"
                   href="edit_hero.php?id=<?= urlencode((string)$h['id']) ?>">
                  Modifier
                </a>

                <!-- Supprimer -->
                <form method="POST" action="delete_hero.php" class="d-inline"
                      onsubmit="return confirm('Supprimer ce héros ?');">
                  <input type="hidden" name="id" value="<?= htmlspecialchars((string)$h['id']) ?>">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
  const input = document.getElementById('search');
  const rows  = document.querySelectorAll('#table tbody tr');
  input.addEventListener('keyup', () => {
    const q = input.value.toLowerCase();
    rows.forEach(r => {
      r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
</script>
</body>
</html>
