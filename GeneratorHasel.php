<?php
class GeneratorHasel
{
    private $pdo;
    private $dlugoscHasla;
    private $minMaleLitery;
    private $minDuzeLitery;
    private $minCyfry;
    private $minZnakiSpecjalne;
    private $uzywajMaleLitery;
    private $uzywajDuzeLitery;
    private $uzywajCyfry;
    private $uzywajZnakiSpecjalne;

    private $maleLitery = 'abcdefghijklmnopqrstuvwxyz';
    private $duzeLitery = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $cyfry = '0123456789';
    private $znakiSpecjalne = '!@#$%^&*()_+[]{}|;:,.<>?';

    public function __construct(PDO $pdo, $dlugoscHasla, $minMaleLitery, $minDuzeLitery, $minCyfry, $minZnakiSpecjalne, $uzywajMaleLitery, $uzywajDuzeLitery, $uzywajCyfry, $uzywajZnakiSpecjalne)
    {
        $this->pdo = $pdo;
        $this->dlugoscHasla = $dlugoscHasla;
        $this->minMaleLitery = $minMaleLitery;
        $this->minDuzeLitery = $minDuzeLitery;
        $this->minCyfry = $minCyfry;
        $this->minZnakiSpecjalne = $minZnakiSpecjalne;
        $this->uzywajMaleLitery = $uzywajMaleLitery;
        $this->uzywajDuzeLitery = $uzywajDuzeLitery;
        $this->uzywajCyfry = $uzywajCyfry;
        $this->uzywajZnakiSpecjalne = $uzywajZnakiSpecjalne;

        $this->walidujWejscie();
    }

    private function walidujWejscie()
    {
        $sumaMinZnakow = $this->minMaleLitery + $this->minDuzeLitery + $this->minCyfry + $this->minZnakiSpecjalne;
        if ($this->dlugoscHasla < $sumaMinZnakow) {
            throw new Exception("Suma minimalnych znaków przekracza długość hasła!");
        }

        if ($this->dlugoscHasla <= 0) {
            throw new Exception("Długość hasła musi być większa niż 0!");
        }
    }

    private function losoweZnaki($zbiorZnakow, $ilosc)
    {
        $wynik = '';
        for ($i = 0; $i < $ilosc; $i++) {
            $wynik .= $zbiorZnakow[random_int(0, strlen($zbiorZnakow) - 1)];
        }
        return $wynik;
    }

    public function generujHaslo()
    {
        $haslo = '';
        $dostepneZnaki = '';

        if ($this->uzywajMaleLitery && $this->minMaleLitery > 0) {
            $haslo .= $this->losoweZnaki($this->maleLitery, $this->minMaleLitery);
            $dostepneZnaki .= $this->maleLitery;
        }

        if ($this->uzywajDuzeLitery && $this->minDuzeLitery > 0) {
            $haslo .= $this->losoweZnaki($this->duzeLitery, $this->minDuzeLitery);
            $dostepneZnaki .= $this->duzeLitery;
        }

        if ($this->uzywajCyfry && $this->minCyfry > 0) {
            $haslo .= $this->losoweZnaki($this->cyfry, $this->minCyfry);
            $dostepneZnaki .= $this->cyfry;
        }

        if ($this->uzywajZnakiSpecjalne && $this->minZnakiSpecjalne > 0) {
            $haslo .= $this->losoweZnaki($this->znakiSpecjalne, $this->minZnakiSpecjalne);
            $dostepneZnaki .= $this->znakiSpecjalne;
        }

        $pozostalaDlugosc = $this->dlugoscHasla - strlen($haslo);
        if ($pozostalaDlugosc > 0 && !empty($dostepneZnaki)) {
            $haslo .= $this->losoweZnaki($dostepneZnaki, $pozostalaDlugosc);
        }

        $haslo = str_shuffle($haslo);

        return $haslo;
    }

    public function zapiszHaslo($haslo)
    {
        $zapytanie = "INSERT INTO wygenerowane_hasla (haslo, data_utworzenia) VALUES (:haslo, :data_utworzenia)";
        $stmt = $this->pdo->prepare($zapytanie);

        $aktualnaData = date('Y-m-d H:i:s');
        $stmt->bindParam(':haslo', $haslo);
        $stmt->bindParam(':data_utworzenia', $aktualnaData);

        return $stmt->execute();
    }
}
