<?php

namespace App\View\Principal;
use Cake\View\View;
?>

<!-- filepath: c:\Users\Usuario\Desktop\-\Precify\templates\layout\principal.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $this->Html->meta('icon') ?>
    <title><?= $this->fetch('title') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Nunito+Sans:opsz,wght@6..12,400;6..12,600;6..12,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <?= $this->Html->css(['style']) ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>

</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <i class="fas fa-calculator header-icon"></i>
            <h1><?= $this->fetch('title') ?></h1>
            <p>Calcule o preço de venda ideal para seus produtos com facilidade.</p>
        </div>
    </header>
    <?= $this->fetch('content') ?>
</body>



</html>
