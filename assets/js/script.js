/*
 * script.js
 * Só deixa o site mais prático: abre/fecha o menu e o carrinho e formata o telefone.
 * As regras de verdade (preço, validação, salvar o pedido) ficam no PHP.
 */

// ---------- Menu do celular ----------
const botaoMenu = document.getElementById('botaoMenu');
const menu = document.getElementById('menu');

botaoMenu.addEventListener('click', function () {
    menu.classList.toggle('aberto');   // toggle: se está aberto fecha, se está fechado abre
});

// Fecha o menu quando o cliente toca em um link
menu.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () {
        menu.classList.remove('aberto');
    });
});

// ---------- Carrinho lateral ----------
const carrinho = document.getElementById('carrinho');
const fundoCarrinho = document.getElementById('fundoCarrinho');

function abrirCarrinho() {
    carrinho.classList.add('aberto');
    fundoCarrinho.classList.add('aberto');
}

function fecharCarrinho() {
    carrinho.classList.remove('aberto');
    fundoCarrinho.classList.remove('aberto');
}

document.getElementById('abrirCarrinho').addEventListener('click', abrirCarrinho);
document.getElementById('fecharCarrinho').addEventListener('click', fecharCarrinho);
fundoCarrinho.addEventListener('click', fecharCarrinho);   // clicar fora também fecha

// ---------- Máscara do telefone: 11946339044 vira (11) 94633-9044 ----------
const campoTelefone = document.getElementById('telefone');

if (campoTelefone) {   // o campo só existe quando o carrinho tem itens
    campoTelefone.addEventListener('input', function () {
        let numeros = campoTelefone.value.replace(/\D/g, '').slice(0, 11);

        if (numeros.length > 6) {
            campoTelefone.value = '(' + numeros.slice(0, 2) + ') ' + numeros.slice(2, numeros.length - 4) + '-' + numeros.slice(-4);
        } else if (numeros.length > 2) {
            campoTelefone.value = '(' + numeros.slice(0, 2) + ') ' + numeros.slice(2);
        } else {
            campoTelefone.value = numeros;
        }
    });
}
