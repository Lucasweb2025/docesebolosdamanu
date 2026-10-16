<?php
/*
 * index.php
 * Página única do site (landing page) com 4 seções: Home, Sobre mim, Produtos e Contato.
 * O PHP lê os produtos do arquivo JSON e monta os cards da seção Produtos.
 */
require 'includes/funcoes.php';

$dados    = lerJson(ARQUIVO_PRODUTOS);
$produtos = $dados['produtos'] ?? [];
$tamanhos = $dados['tamanhos'] ?? [];

include 'includes/topo.php';
?>

<main>

    <!-- ========== HOME ========== -->
    <section id="home" class="secao-home">
        <div class="container home-conteudo">
            <div class="home-texto">
                <p class="etiqueta">Bolos personalizados e doces artesanais</p>
                <h1>Doces momentos começam aqui.</h1>
                <p>Bolos feitos com carinho para aniversários, festas e eventos, do jeitinho que você imaginou.</p>
                <div class="home-botoes">
                    <a href="#produtos" class="botao">Ver bolos</a>
                    <a href="<?= linkWhatsapp() ?>" class="botao botao-whatsapp" target="_blank" rel="noopener">Pedir pelo WhatsApp</a>
                </div>
            </div>
            <img src="assets/img/bolos/bolo-princesa-bela.jpg" alt="Bolo tema Princesa Bela" class="home-foto">
        </div>
    </section>

    <!-- ========== SOBRE MIM ========== -->
    <section id="sobre" class="secao-sobre">
        <div class="container sobre-conteudo">
            <img src="assets/img/logo.jpg" alt="Logo Doces e Bolos da Manu" class="sobre-foto">
            <div>
                <p class="etiqueta">Quem sou?</p>
                <h2>Prazer, eu sou a Manu</h2>
                <!-- TEXTO PROVISÓRIO: trocar pelo texto real da Manu -->
                <p>Sou confeiteira e faço bolos personalizados e doces artesanais aqui no Jardim Comercial, em São Paulo.</p>
                <p>Cada bolo é pensado junto com você: o tema, as cores e os detalhes, para deixar a sua festa ainda mais especial.</p>
                <p>Amo transformar ideias em bolos que marcam momentos importantes na vida das minhas clientes.</p>
            </div>
        </div>
    </section>

    <!-- ========== PRODUTOS ========== -->
    <section id="produtos" class="secao-produtos">
        <div class="container">
            <p class="etiqueta">Cardápio</p>
            <h2>Escolha o tema do seu bolo</h2>
            <p class="subtitulo">Escolha o tamanho, adicione ao carrinho e envie o pedido pelo WhatsApp.</p>

            <div class="grade-produtos">
                <?php // O foreach repete o card abaixo uma vez para cada produto do JSON ?>
                <?php foreach ($produtos as $produto): ?>
                    <article class="card">
                        <img src="assets/img/bolos/<?= escapar($produto['imagem']) ?>"
                             alt="Bolo tema <?= escapar($produto['tema']) ?>" loading="lazy">
                        <div class="card-corpo">
                            <h3><?= escapar($produto['tema']) ?></h3>
                            <p><?= escapar($produto['descricao']) ?></p>

                            <!-- Envia para o carrinho.php por POST: qual bolo, qual tamanho e a personalização -->
                            <form method="post" action="carrinho.php">
                                <input type="hidden" name="acao" value="adicionar">
                                <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">

                                <label for="tamanho-<?= $produto['id'] ?>">Tamanho</label>
                                <select name="tamanho" id="tamanho-<?= $produto['id'] ?>">
                                    <?php foreach ($tamanhos as $tamanho): ?>
                                        <option value="<?= $tamanho['id'] ?>">
                                            <?= escapar($tamanho['nome']) ?> · <?= formatarPreco($tamanho['preco']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <label for="personalizacao-<?= $produto['id'] ?>">Nome e idade no topo (opcional)</label>
                                <input type="text" name="personalizacao" id="personalizacao-<?= $produto['id'] ?>"
                                       maxlength="40" placeholder="Ex.: Júlia 8 anos">

                                <button type="submit" class="botao botao-cheio">Adicionar ao carrinho</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ========== CONTATO ========== -->
    <section id="contato" class="secao-contato">
        <div class="container contato-conteudo">
            <div>
                <p class="etiqueta">Contato</p>
                <h2>Vamos conversar?</h2>
                <p>Tire dúvidas, peça um orçamento ou combine um tema diferente.</p>
                <a href="<?= linkWhatsapp() ?>" class="botao botao-whatsapp" target="_blank" rel="noopener">
                    Chamar no WhatsApp
                </a>
            </div>
            <ul class="contato-lista">
                <li><span>📱</span> <a href="<?= linkWhatsapp() ?>" target="_blank" rel="noopener"><?= WHATSAPP_EXIBIR ?></a></li>
                <li><span>📸</span> <a href="https://www.instagram.com/<?= INSTAGRAM_USUARIO ?>/" target="_blank" rel="noopener">@<?= INSTAGRAM_USUARIO ?></a></li>
                <li><span>📍</span> Jardim Comercial · São Paulo/SP</li>
                <li><span>🎉</span> Encomendas para festas e eventos</li>
            </ul>
        </div>
    </section>

</main>

<?php
include 'includes/carrinho-lateral.php';
include 'includes/rodape.php';
