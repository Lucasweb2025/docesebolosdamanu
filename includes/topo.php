<?php
/*
 * topo.php
 * Início do HTML (<head>) e o cabeçalho com logo, menu e botão do carrinho.
 * É incluído no começo da página com: include 'includes/topo.php';
 */
$quantidadeNoCarrinho = count(itensDoCarrinho());
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <!-- Faz o site se ajustar à largura do celular (sem isso o celular mostra a versão de computador em miniatura) -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bolos personalizados e doces artesanais. Encomendas para festas e eventos.">
    <title><?= NOME_LOJA ?></title>

    <link rel="icon" href="assets/img/logo.jpg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>

<header class="cabecalho">
    <div class="container cabecalho-conteudo">
        <a href="#home" class="marca">
            <img src="assets/img/logo.jpg" alt="Logo Doces e Bolos da Manu">
            <span>Doces e Bolos <strong>da Manu</strong></span>
        </a>

        <!-- Os links levam até as seções da própria página (âncoras com #) -->
        <nav class="menu" id="menu">
            <a href="#home">Home</a>
            <a href="#sobre">Sobre mim</a>
            <a href="#produtos">Produtos</a>
            <a href="#contato">Contato</a>
        </nav>

        <div class="cabecalho-botoes">
            <button class="botao-carrinho" id="abrirCarrinho" aria-label="Abrir carrinho">
                🛒 <span class="contador"><?= $quantidadeNoCarrinho ?></span>
            </button>
            <!-- Botão de 3 risquinhos: só aparece no celular (controlado pelo CSS) -->
            <button class="botao-menu" id="botaoMenu" aria-label="Abrir menu">☰</button>
        </div>
    </div>
</header>
