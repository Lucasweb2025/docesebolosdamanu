<?php
/*
 * funcoes.php
 * Configurações do site e funções usadas em mais de uma página.
 * Toda página começa com: require 'includes/funcoes.php';
 */

// A sessão guarda o carrinho de cada visitante enquanto ele navega.
// Precisa ser chamada antes de qualquer HTML ser enviado.
session_start();

// Garante que "hoje" seja o dia de São Paulo, e não o do servidor da hospedagem.
date_default_timezone_set('America/Sao_Paulo');

// Dados fixos da loja (se mudar o número, muda só aqui)
const NOME_LOJA        = 'Doces e Bolos da Manu';
const WHATSAPP_NUMERO  = '5511946339044';   // formato do wa.me: 55 + DDD + número
const WHATSAPP_EXIBIR  = '(11) 94633-9044';
const INSTAGRAM_USUARIO = 'docesebolosda.manu';
const DIAS_ANTECEDENCIA = 2;                // prazo mínimo para fazer um bolo

// Caminhos dos arquivos JSON (o nosso "banco de dados")
const ARQUIVO_PRODUTOS = __DIR__ . '/../dados/produtos.json';
const ARQUIVO_PEDIDOS  = __DIR__ . '/../dados/pedidos.json';


/* Lê um arquivo JSON e devolve o conteúdo como array do PHP. */
function lerJson($caminho)
{
    if (!file_exists($caminho)) {
        return [];
    }
    $texto = file_get_contents($caminho);
    return json_decode($texto, true) ?? [];   // true = devolve array, e não objeto
}

/* Transforma o array em JSON e grava no arquivo. */
function salvarJson($caminho, $dados)
{
    $texto = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    // LOCK_EX trava o arquivo durante a gravação, para dois pedidos ao mesmo tempo não se misturarem
    file_put_contents($caminho, $texto, LOCK_EX);
}

/* Limpa o que o usuário digitou: tira espaços das pontas e qualquer tag HTML. */
function limpar($texto)
{
    return trim(strip_tags($texto ?? ''));
}

/*
 * Prepara um texto para ser mostrado na página com segurança.
 * Converte < > " em códigos HTML, evitando que alguém injete código na página (ataque XSS).
 */
function escapar($texto)
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/* 180 -> "R$ 180,00" */
function formatarPreco($valor)
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/* Procura um item pelo campo "id" dentro de uma lista. Devolve null se não achar. */
function buscarPorId($lista, $id)
{
    foreach ($lista as $item) {
        if ($item['id'] == $id) {
            return $item;
        }
    }
    return null;
}

/* Devolve os itens do carrinho guardados na sessão (ou lista vazia). */
function itensDoCarrinho()
{
    return $_SESSION['carrinho'] ?? [];
}

/* Soma o preço de todos os itens do carrinho. */
function totalDoCarrinho()
{
    $total = 0;
    foreach (itensDoCarrinho() as $item) {
        $total += $item['preco'];
    }
    return $total;
}

/* Monta o link que abre o WhatsApp da loja com a mensagem já escrita. */
function linkWhatsapp($mensagem = 'Olá! Vim pelo site e quero fazer uma encomenda.')
{
    // urlencode troca espaços e acentos por códigos que podem ir dentro de um link
    return 'https://wa.me/' . WHATSAPP_NUMERO . '?text=' . urlencode($mensagem);
}

/* Primeira data que o cliente pode escolher (hoje + dias de antecedência), no formato do <input type="date">. */
function dataMinimaEntrega()
{
    return date('Y-m-d', strtotime('+' . DIAS_ANTECEDENCIA . ' days'));
}
