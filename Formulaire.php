<?php
declare(strict_types=1);

require __DIR__ . '/pouvoir.php';
require __DIR__ . '/config.php'; // PDO $pdo
session_start();

// Flash messages
if (!isset($_SESSION['flash'])) $_SESSION['flash'] = [];

// 1) Initialiser la liste en session (affichage)
if (!isset($_SESSION['Heros']) || !is_array($_SESSION['Heros'])) {
    $_SESSION['Heros'] = [];
}

$errors = [];

// 2) Traiter le formulaire (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['pseudonyme'], $_POST['pouvoir'], $_POST['ville'], $_POST['rank'])) {

    $pseudonyme = trim($_POST['pseudonyme']);
    $pouvoir    = trim($_POST['pouvoir']);
    $ville      = trim($_POST['ville']);
    $rank       = (int) $_POST['rank'];

    // Validations
    if ($pseudonyme === '') $errors[] = "Le pseudonyme est obligatoire.";
    if ($pouvoir === '')    $errors[] = "Le pouvoir est obligatoire.";
    if ($rank < 1 || $rank > 100) $errors[] = "Le rang doit être entre 1 et 100.";

    if (!$errors) {
        try {
            // 2.1) Insérer en BDD
            $stmt = $pdo->prepare("
                INSERT INTO heros (pseudonyme, pouvoir, ville, rank)
                VALUES (:pseudonyme, :pouvoir, :ville, :rank)
            ");
            $stmt->execute([
                ':pseudonyme' => $pseudonyme,
                ':pouvoir'    => $pouvoir,
                ':ville'      => $ville,
                ':rank'       => $rank
            ]);

            $newId = (int)$pdo->lastInsertId();

            // 2.2) Empiler en session aussi (optionnel)
            $hero = new Heros($newId, $pouvoir, $pseudonyme, $ville, $rank);
            $_SESSION['Heros'][] = $hero;

            // 2.3) Message de succès + redirection (PRG)
            $_SESSION['flash'][] = ['type' => 'success', 'msg' => 'Héros ajouté à la base de données !'];
            header('Location: '.$_SERVER['REQUEST_URI']);
            exit;
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// 3) Récupération locale pour affichage
$heros = $_SESSION['Heros'];
$flashes = $_SESSION['flash'];
$_SESSION['flash'] = []; // vider après lecture
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Super Héros — Formulaire</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Héros</a>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="Formulaire.php">Formulaire</a></li>
      <li class="nav-item"><a class="nav-link" href="Liste_heros.php">Liste des Héros (BDD)</a></li>
    </ul>
  </div>
</nav>

<div class="container">
  <h1 class="mb-3">Formulaire d’intégration des Héros</h1>

  <?php foreach ($flashes as $f): ?>
    <div class="alert alert-<?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['msg']) ?></div>
  <?php endforeach; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Formulaire -->
  <form class="row g-3 mb-4" method="POST" action="">
    <div class="col-md-6">
      <label class="form-label" for="pseudonyme">Pseudonyme</label>
      <input type="text" class="form-control" id="pseudonyme" name="pseudonyme" required>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="pouvoir">Pouvoir</label>
      <input type="text" class="form-control" id="pouvoir" name="pouvoir" required>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="ville">Nationalité</label>
      <input type="text" class="form-control" id="ville" name="ville">
    </div>
    <div class="col-md-4">
      <label class="form-label" for="rank">Rang</label>
      <input type="number" class="form-control" id="rank" name="rank" min="1" max="100" required>
    </div>
    <div class="col-12">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="cgu" required>
        <label class="form-check-label" for="cgu">J’accepte les conditions</label>
      </div>
    </div>
    <div class="col-12">
      <button class="btn btn-primary" type="submit">Ajouter</button>
    </div>
  </form>

  <!-- Listing des héros en session -->
  <div class="card shadow-sm">
    <div class="card-header bg-light"><strong>Héros en mémoire</strong></div>
    <div class="card-body">
      <?php if (empty($heros)): ?>
        <p class="text-muted mb-0">Aucun héros ajouté pour l’instant.</p>
      <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($heros as $h): ?>
            <li class="list-group-item">
              <?= htmlspecialchars($h->getPseudonyme()) ?>
              — <?= htmlspecialchars($h->getPouvoir()) ?>
              <?php if ($h->getVille() !== ''): ?> (<?= htmlspecialchars($h->getVille()) ?>)<?php endif; ?>
              — <span class="badge bg-dark">Rang <?= htmlspecialchars((string)$h->getRank()) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
