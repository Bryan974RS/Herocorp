<?php
// liste_heros.php
// ──────────────────────────────────────────────────────────────────────────
// Prérequis :
//  - config.php (PDO $pdo en utf8mb4)
//  - heros.php (classe Heros avec getters: getId, getPseudonyme, getPouvoir, getUnivers?, getVille, getRank)

require __DIR__ . '/config.php';
require __DIR__ . '/heros.php';

// Requête (avec recherche optionnelle via ?search=)
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $sql = "SELECT * FROM heros
            WHERE pseudonyme LIKE :q
               OR pouvoir    LIKE :q
               OR univers    LIKE :q
               OR ville      LIKE :q
            ORDER BY rank ASC";
    $stmt = $pdo->prepare($sql);
    $q = '%' . $_GET['search'] . '%';
    $stmt->bindParam(':q', $q, PDO::PARAM_STR);
    $stmt->execute();
} else {
    $stmt = $pdo->query("SELECT * FROM heros ORDER BY rank ASC");
}

// Retour en objets (même esprit que ton prof)
$stmt->setFetchMode(PDO::FETCH_CLASS, 'Heros');
$heros = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Super Héros — Listing</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- DataTables (Bootstrap 5 integration) -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"/>

  <style>
    body { background: #f8f9fa; }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 text-success mb-0">Classement des super héros</h1>

    <!-- (Optionnel) Recherche via GET (comme le prof) -->
    <form class="d-flex gap-2" method="get" action="">
      <input class="form-control" name="search" type="text" placeholder="Rechercher (GET)"
             value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
      <button class="btn btn-outline-success" type="submit">Chercher</button>
    </form>
  </div>

  <div class="card shadow-sm">
    <div class="table-responsive p-2">
      <table id="tableHeros" class="table table-striped table-hover align-middle" style="width:100%">
        <thead class="table-success">
          <tr>
            <th>ID</th>
            <th>Pseudonyme</th>
            <th>Pouvoir</th>
            <th>Univers</th>
            <th>Ville</th>
            <th>Rang (1 = top)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($heros as $h): ?>
            <tr>
              <td><?= htmlspecialchars($h->getId()) ?></td>
              <td><?= htmlspecialchars($h->getPseudonyme()) ?></td>
              <td><?= htmlspecialchars($h->getPouvoir()) ?></td>
              <td><?= htmlspecialchars(method_exists($h,'getUnivers') ? $h->getUnivers() : '') ?></td>
              <td><?= htmlspecialchars($h->getVille()) ?></td>
              <td><span class="badge bg-dark"><?= htmlspecialchars($h->getRank()) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- jQuery + DataTables + Bootstrap 5 integration -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
  // Active DataTables (barre de recherche dynamique, tri, pagination)
  new DataTable('#tableHeros', {
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50, 100],
    language: {
      url: 'https://cdn.datatables.net/plug-ins/2.0.0/i18n/fr-FR.json'
    }
  });
</script>
</body>
</html>