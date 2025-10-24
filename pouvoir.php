<?php 
class Heros {
    private int $id;
    private string $pouvoir;
    private string $pseudonyme;
    private string $ville;
    private int $rank;

    // --- Constructeur ---
    public function __construct($id = 0, $pouvoir = "", $pseudonyme = "", $ville = "", $rank = 100) {
        $this->id = $id;
        $this->pouvoir = $pouvoir;
        $this->pseudonyme = $pseudonyme;
        $this->ville = $ville;
        $this->rank = $rank;
    }

    // --- Getters ---
    public function getId() {
        return $this->id;
    }

    public function getPseudonyme() {
        return $this->pseudonyme;
    }

    public function getPouvoir() {
        return $this->pouvoir;
    }

    public function getVille() {
        return $this->ville;
    }

    public function getRank() {
        return $this->rank;
    }

    // --- Setters ---
    public function setId($id) {
        $this->id = $id;
    }

    public function setPseudonyme($pseudonyme) {
        $this->pseudonyme = $pseudonyme;
    }

    public function setPouvoir($pouvoir) {
        $this->pouvoir = $pouvoir;
    }

    public function setVille($ville) {
        $this->ville = $ville;
    }

    public function setRank($rank) {
        if ($rank < 1 || $rank > 100) {
            throw new Exception("Le rang doit être entre 1 et 100");
        }
        $this->rank = $rank;
    }

    // --- Affichage (optionnel, utile pour debug) ---
    public function afficher() {
        echo "<b>{$this->pseudonyme}</b> — {$this->pouvoir} ({$this->ville}) [Rang : {$this->rank}]<br>";
    }
}
?>
