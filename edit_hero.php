<?php
declare(strict_types=1);
ini_set('display_errors','1'); error_reporting(E_ALL);

require __DIR__ . '/config.php';
session_start();

if (!isset($_SESSION['flash'])) $_SESSION['flash'] = [];
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

$errors = [];
$hero   = null;

// ------- GET: afficher le formulaire --------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id <= 0) {
    $_SESSION[ 'flash'][] = ['type'=>'danger','msg'=>'ID manquant'];
    header('Location: Liste_heros.php'); exit;
  }

  $stmt = $pdo->prepare("SELECT * FROM `heros` WHERE `id` = :id");
  $stmt->execute([':id'=>$id]);
  $hero = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$hero) {
    $_SESSION['flash'][] = ['type'=>'danger','msg'=>'Héros introuvable'];
    header('Location: Liste_heros.php'); exit;
  }
}

// ------- POST: enregistrer --------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $postedCsrf = $_POST['csrf'] ?? '';
  if (!hash_equals($_SESSION['csrf'], $postedCsrf)) {
    $_SESSION['flash'][] = ['type'=>'danger','msg'=>'CSRF invalide'];
    header('Location: Liste_heros.php'); exit;
  }

  $id         = (int)($_POST['id'] ?? 0);
  $pseudonyme = trim($_POST['pseudonyme'] ?? '');
  $pouvoir    = trim($_POST['pouvoir'] ?? '');
  $ville      = trim($_POST['ville'] ?? '');
  $rank       = (int)($_POST['rank'] ?? 0);

  if ($id <= 0)            $errors[] = "ID invalide.";
  if ($pseudonyme === '')  $errors[] = "Le pseudonyme est obligatoire.";
  if ($pouvoir === '')     $errors[] = "Le pouvoir est obligatoire.";
  if ($rank < 1 || $rank > 100) $errors[] = "Le rang doit être entre 1 et 100.";

  if (!$errors) {
    $stmt = $pdo->prepare("
      UPDATE `heros`
      SET `pseudonyme` = :pseudonyme,
          `pouvoir`    = :pouvoir,
          `ville`      = :ville,
          `rank`       = :rank
      WHERE `id` = :id
    ");
    $stmt->execute([
      ':pseudonyme'=>$pseudonyme,
      ':pouvoir'   =>$pouvoir,
      ':ville'     =>$ville,
      ':rank'      =>$rank,
      ':id'        =>$id
    ]);

    if ($stmt->rowCount() >= 0) { // >=0 pour accepter “aucune modif” si valeurs identiques
      $_SESSION['flash'][] = ['type'=>'success','msg'=>'Héros modifié avec succès'];
      header('Location: Liste_heros.php'); exit;
    } else {
      $errors[] = "Aucune ligne modifiée (id introuvable ?).";
    }
  } else {
    // Recharger le formulaire avec les valeurs saisies
    $hero = ['id'=>$id,'pseudonyme'=>$pseudonyme,'pouvoir'=>$pouvoir,'ville'=>$ville,'rank'=>$rank];
  }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Modifier un Héros</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4">Modifier un Héros</h1>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><ul class="mb-0">
      <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul></div>
  <?php endif; ?>

  <form method="POST" class="row g-3" action="">
    <input type="hidden" name="id" value="<?= htmlspecialchars((string)$hero['id']) ?>">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

    <div class="col-md-6">
      <label class="form-label" for="pseudonyme">Pseudonyme</label>
      <input type="text" class="form-control" id="pseudonyme" name="pseudonyme"
             value="<?= htmlspecialchars($hero['pseudonyme'] ?? '') ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="pouvoir">Pouvoir</label>
      <input type="text" class="form-control" id="pouvoir" name="pouvoir"
             value="<?= htmlspecialchars($hero['pouvoir'] ?? '') ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="ville">Nationalité</label>
      <input type="text" class="form-control" id="ville" name="ville"
             value="<?= htmlspecialchars($hero['ville'] ?? '') ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label" for="rank">Rang</label>
      <input type="number" class="form-control" id="rank" name="rank" min="1" max="100"
             value="<?= htmlspecialchars((string)($hero['rank'] ?? 1)) ?>" required>
    </div>

    <div class="col-12">
      <a href="Liste_heros.php" class="btn btn-secondary">Annuler</a>
      <button class="btn btn-primary" type="submit">Enregistrer</button>
    </div>
  </form>
</div>
</body>
</html>
