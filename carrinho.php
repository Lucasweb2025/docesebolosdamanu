<?php
/*
 * carrinho.php
 * Recebe os formulários do site (sempre por POST) e faz 3 ações:
 *   - adicionar: coloca um bolo no carrinho (guardado na sessão)
 *   - remover:   tira um bolo do carrinho
 *   - finalizar: valida os dados, salva o pedido em JSON e abre o WhatsApp
 * Este arquivo não mostra HTML: ele processa e redireciona de volta para a página.
 */
require 'includes/funcoes.php';

// Só aceita POST. Se alguém abrir carrinho.php direto pelo navegador (GET), volta para o início.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$acao = $_POST['acao'] ?? '';

if ($acao === 'adicionar') {
    adicionarAoCarrinho();
} elseif ($acao === 'remover') {
    removerDoCarrinho();
} elseif ($acao === 'finalizar') {
    finalizarPedido();
}

// Qualquer ação desconhecida também volta para o início
voltarComCarrinhoAberto();


/* ---------------- Funções de cada ação ---------------- */

function adicionarAoCarrinho()
{
    $dados = lerJson(ARQUIVO_PRODUTOS);

    // Busca o produto e o tamanho no JSON. Não confiamos no preço vindo do formulário:
    // o preço é sempre o do arquivo, assim ninguém consegue alterar o valor pelo navegador.
    $produto = buscarPorId($dados['produtos'], $_POST['produto_id'] ?? '');
    $tamanho = buscarPorId($dados['tamanhos'], $_POST['tamanho'] ?? '');

    if ($produto && $tamanho) {
        $_SESSION['carrinho'][] = [
            'tema'           => $produto['tema'],
            'imagem'         => $produto['imagem'],
            'tamanho'        => $tamanho['nome'],
            'preco'          => $tamanho['preco'],
            'personalizacao' => mb_substr(limpar($_POST['personalizacao'] ?? ''), 0, 40),
        ];
        $_SESSION['aviso'] = 'Bolo ' . $produto['tema'] . ' adicionado!';
    }
    voltarComCarrinhoAberto();
}

function removerDoCarrinho()
{
    $posicao = (int) ($_POST['posicao'] ?? -1);
    unset($_SESSION['carrinho'][$posicao]);
    // array_values reorganiza as posições (0, 1, 2...) depois de remover um item do meio
    $_SESSION['carrinho'] = array_values(itensDoCarrinho());
    voltarComCarrinhoAberto();
}

function finalizarPedido()
{
    $nome       = limpar($_POST['nome'] ?? '');
    $telefone   = limpar($_POST['telefone'] ?? '');
    $data       = limpar($_POST['data'] ?? '');
    $observacao = mb_substr(limpar($_POST['observacao'] ?? ''), 0, 300);

    // ---- Validação no servidor ----
    // O navegador também valida, mas isso pode ser burlado. Quem decide de verdade é o PHP.
    $erros = [];

    if (empty(itensDoCarrinho())) {
        $erros[] = 'Seu carrinho está vazio.';
    }
    if (mb_strlen($nome) < 3) {
        $erros[] = 'Informe seu nome.';
    }
    $somenteNumeros = preg_replace('/\D/', '', $telefone);   // \D = tudo que não é número
    if (strlen($somenteNumeros) < 10 || strlen($somenteNumeros) > 11) {
        $erros[] = 'Informe um WhatsApp válido com DDD.';
    }
    // A data vem como "2026-10-30". Comparar textos nesse formato funciona como comparar datas.
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) || $data < dataMinimaEntrega()) {
        $erros[] = 'Escolha uma data com pelo menos ' . DIAS_ANTECEDENCIA . ' dias de antecedência.';
    }

    // Se tem erro, guarda os erros e o que foi digitado na sessão e volta para o carrinho
    if ($erros) {
        $_SESSION['erros']    = $erros;
        $_SESSION['digitado'] = $_POST;
        voltarComCarrinhoAberto();
    }

    // ---- Salva o pedido no arquivo JSON ----
    $pedido = [
        'id'          => uniqid(),
        'criado_em'   => date('Y-m-d H:i:s'),
        'cliente'     => $nome,
        'telefone'    => $somenteNumeros,
        'data_festa'  => $data,
        'observacao'  => $observacao,
        'itens'       => itensDoCarrinho(),
        'total'       => totalDoCarrinho(),
    ];
    $pedidos   = lerJson(ARQUIVO_PEDIDOS);
    $pedidos[] = $pedido;
    salvarJson(ARQUIVO_PEDIDOS, $pedidos);

    // ---- Monta a mensagem e abre o WhatsApp ----
    $mensagem = montarMensagem($pedido);
    $_SESSION['carrinho'] = [];   // esvazia o carrinho depois de enviar
    header('Location: ' . linkWhatsapp($mensagem));
    exit;
}

/* Monta o texto do pedido que vai aparecer pronto no WhatsApp. */
function montarMensagem($pedido)
{
    $texto  = "Olá! Quero fazer um pedido pelo site 🎂\n\n";
    $texto .= "Nome: {$pedido['cliente']}\n";
    $texto .= 'Data da festa: ' . date('d/m/Y', strtotime($pedido['data_festa'])) . "\n\n";

    foreach ($pedido['itens'] as $numero => $item) {
        $texto .= ($numero + 1) . ". Bolo {$item['tema']} - {$item['tamanho']} - " . formatarPreco($item['preco']) . "\n";
        if ($item['personalizacao']) {
            $texto .= "   Topo: {$item['personalizacao']}\n";
        }
    }

    $texto .= "\nTotal: " . formatarPreco($pedido['total']);
    if ($pedido['observacao']) {
        $texto .= "\nObservação: {$pedido['observacao']}";
    }
    return $texto;
}

/* Volta para a página inicial com a barra do carrinho aberta. */
function voltarComCarrinhoAberto()
{
    header('Location: index.php?carrinho=aberto#produtos');
    exit;
}
