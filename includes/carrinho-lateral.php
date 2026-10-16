<?php
/*
 * carrinho-lateral.php
 * Barra lateral que mostra os bolos escolhidos e o formulário para finalizar o pedido.
 * Os itens vêm da sessão ($_SESSION['carrinho']), preenchida pelo carrinho.php.
 */
$itens = itensDoCarrinho();

// Se o carrinho.php encontrou erros ao finalizar, ele deixou na sessão
// os erros e o que o cliente já tinha digitado, para não precisar digitar de novo.
$erros      = $_SESSION['erros'] ?? [];
$digitado   = $_SESSION['digitado'] ?? [];
$aviso      = $_SESSION['aviso'] ?? '';
unset($_SESSION['erros'], $_SESSION['digitado'], $_SESSION['aviso']);   // mostra só uma vez

// O carrinho já abre sozinho quando o endereço tem ?carrinho=aberto (depois de adicionar ou de dar erro)
$classeAberto = (($_GET['carrinho'] ?? '') === 'aberto') ? 'aberto' : '';
?>
<div class="fundo-escuro <?= $classeAberto ?>" id="fundoCarrinho"></div>

<aside class="carrinho <?= $classeAberto ?>" id="carrinho" aria-label="Carrinho">
    <div class="carrinho-topo">
        <h2>Meu pedido</h2>
        <button class="fechar" id="fecharCarrinho" aria-label="Fechar carrinho">✕</button>
    </div>

    <?php if ($aviso): ?>
        <p class="aviso-sucesso"><?= escapar($aviso) ?></p>
    <?php endif; ?>

    <?php if (empty($itens)): ?>
        <p class="carrinho-vazio">Seu carrinho está vazio.<br>Escolha um bolo na seção Produtos 🎂</p>
    <?php else: ?>

        <ul class="carrinho-itens">
            <?php foreach ($itens as $posicao => $item): ?>
                <li>
                    <img src="assets/img/bolos/<?= escapar($item['imagem']) ?>" alt="">
                    <div>
                        <strong><?= escapar($item['tema']) ?></strong>
                        <small><?= escapar($item['tamanho']) ?></small>
                        <?php if ($item['personalizacao']): ?>
                            <small>"<?= escapar($item['personalizacao']) ?>"</small>
                        <?php endif; ?>
                        <span><?= formatarPreco($item['preco']) ?></span>
                    </div>
                    <!-- Cada item tem um formulário pequeno para remover. A posição diz qual item sai. -->
                    <form method="post" action="carrinho.php">
                        <input type="hidden" name="acao" value="remover">
                        <input type="hidden" name="posicao" value="<?= $posicao ?>">
                        <button class="remover" aria-label="Remover item">🗑</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="carrinho-total">Total: <strong><?= formatarPreco(totalDoCarrinho()) ?></strong></p>

        <!-- Dados do cliente para finalizar o pedido -->
        <form method="post" action="carrinho.php" class="formulario" id="formFinalizar" novalidate>
            <input type="hidden" name="acao" value="finalizar">

            <?php if ($erros): ?>
                <ul class="lista-erros">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= escapar($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <label for="nome">Seu nome</label>
            <input type="text" id="nome" name="nome" required
                   value="<?= escapar($digitado['nome'] ?? '') ?>">

            <label for="telefone">WhatsApp</label>
            <input type="tel" id="telefone" name="telefone" required placeholder="(11) 99999-9999"
                   value="<?= escapar($digitado['telefone'] ?? '') ?>">

            <label for="data">Data da festa</label>
            <input type="date" id="data" name="data" required min="<?= dataMinimaEntrega() ?>"
                   value="<?= escapar($digitado['data'] ?? '') ?>">
            <small class="dica">Pedidos com pelo menos <?= DIAS_ANTECEDENCIA ?> dias de antecedência.</small>

            <label for="observacao">Observação (opcional)</label>
            <textarea id="observacao" name="observacao" rows="2" maxlength="300"><?= escapar($digitado['observacao'] ?? '') ?></textarea>

            <button type="submit" class="botao botao-whatsapp">Enviar pedido pelo WhatsApp</button>
        </form>

    <?php endif; ?>
</aside>
