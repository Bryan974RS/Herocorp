<?php
require __DIR__ . '/config.php';
session_start();
if (!isset($_SESSION['flash'])) $_SESSION['flash'] = [];

$id   = (int)($_POST['id']  ?? 0);
$csrf = $_POST['csrf'] ?? '';

if ($id <= 0) {
  $_SESSION['flash'][] = ['type'=>'danger','msg'=>'ID invalide'];
  header('Location: Liste_heros.php'); exit;
}

if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)) {
  $_SESSION['flash'][] = ['type'=>'danger','msg'=>'CSRF invalide'];
  header('Location: Liste_heros.php'); exit;
}

$stmt = $pdo->prepare("DELETE FROM heros WHERE id = :id");
$stmt->execute([':id'=>$id]);

$_SESSION['flash'][] = ['type'=>'success','msg'=>'Héros supprimé avec succès'];
header('Location: Liste_heros.php'); exit;
?>