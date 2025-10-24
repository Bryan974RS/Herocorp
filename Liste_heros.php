<?php require __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Super Héros — Liste</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  
</head>
<ul>
  <li><a href="Formulaire.php">Formulaire</a></li>
  <li><a href="Liste_heros.php">Liste des Héros</a></li>
</ul>
<body class="bg-light">
<div class="container py-5">

  <h1 class="mb-4 text-success fw-bold">Super Héros — Classement</h1>

  <!-- Barre de recherche (client-side pour l’instant) -->
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
            <th>Nom</th>
            <th>Pouvoir</th>
            <th>ville</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $stmt = $pdo->query("SELECT * FROM heros ORDER BY rank ASC");
            foreach ($stmt as $h):
          ?>
            <tr>
              <td><span class="badge bg-dark"><?= htmlspecialchars($h['rank']) ?></span></td>
              <td><?= htmlspecialchars($h['nom']) ?></td>
              <td><?= htmlspecialchars($h['pouvoir']) ?></td>
              <td><?= htmlspecialchars($h['ville']) ?></td>
              
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