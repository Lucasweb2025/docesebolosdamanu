/*
 * app.js
 * JavaScript do site (versão estática, para o GitHub Pages).
 * Faz 4 coisas: menu do celular, carrinho lateral, carrinho de compras e envio do pedido pelo WhatsApp.
 * O carrinho fica guardado no navegador (localStorage), então não precisa de servidor.
 */

const NUMERO_WHATSAPP = '5511946339044';   // 55 + DDD + número
const DIAS_ANTECEDENCIA = 4;               // prazo mínimo para fazer um bolo

// Lista de bolos escolhidos. Cada item: { tema, imagem, tamanho, preco, personalizacao }
let carrinho = lerCarrinhoSalvo();


/* ---------------- Menu do celular ---------------- */

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


/* ---------------- Abrir e fechar o carrinho lateral ---------------- */

const painelCarrinho = document.getElementById('carrinho');
const fundoCarrinho = document.getElementById('fundoCarrinho');

function abrirCarrinho() {
    painelCarrinho.classList.add('aberto');
    fundoCarrinho.classList.add('aberto');
}

function fecharCarrinho() {
    painelCarrinho.classList.remove('aberto');
    fundoCarrinho.classList.remove('aberto');
}

document.getElementById('abrirCarrinho').addEventListener('click', abrirCarrinho);
document.getElementById('fecharCarrinho').addEventListener('click', fecharCarrinho);
fundoCarrinho.addEventListener('click', fecharCarrinho);   // clicar fora também fecha


/* ---------------- Guardar e ler o carrinho ---------------- */

function lerCarrinhoSalvo() {
    const texto = localStorage.getItem('carrinhoManu');
    return texto ? JSON.parse(texto) : [];   // se não tem nada salvo, começa vazio
}

function salvarCarrinho() {
    localStorage.setItem('carrinhoManu', JSON.stringify(carrinho));
}

/* 180 -> "R$ 180,00" */
function formatarPreco(valor) {
    return 'R$ ' + valor.toFixed(2).replace('.', ',');
}


/* ---------------- Adicionar um bolo ao carrinho ---------------- */

// Cada card de produto tem um formulário com a classe "form-produto"
document.querySelectorAll('.form-produto').forEach(function (formulario) {
    formulario.addEventListener('submit', function (evento) {
        evento.preventDefault();   // impede a página de recarregar

        const card = formulario.closest('.card');                   // o card que contém este formulário
        const select = formulario.elements['tamanho'];
        const opcao = select.options[select.selectedIndex];         // a opção escolhida (P, M ou G)

        carrinho.push({
            tema: card.dataset.tema,
            imagem: card.dataset.imagem,
            tamanho: opcao.text.split(' · ')[0],                    // só o nome do tamanho, sem o preço
            preco: Number(opcao.dataset.preco),
            personalizacao: formulario.elements['personalizacao'].value.trim()
        });

        salvarCarrinho();
        formulario.reset();
        mostrarCarrinho();
        mostrarAviso('Bolo ' + card.dataset.tema + ' adicionado!');
        abrirCarrinho();
    });
});


/* ---------------- Desenhar o carrinho na tela ---------------- */

const listaItens = document.getElementById('listaItens');
const areaPedido = document.getElementById('areaPedido');
const carrinhoVazio = document.getElementById('carrinhoVazio');

function mostrarCarrinho() {
    listaItens.innerHTML = '';   // limpa a lista para desenhar de novo
    let total = 0;

    carrinho.forEach(function (item, posicao) {
        total += item.preco;

        const li = document.createElement('li');
        li.innerHTML =
            '<img src="assets/img/bolos/' + item.imagem + '" alt="">' +
            '<div>' +
                '<strong></strong>' +
                '<small></small>' +
                '<small class="topo-personalizado"></small>' +
                '<span>' + formatarPreco(item.preco) + '</span>' +
            '</div>' +
            '<button class="remover" aria-label="Remover item">🗑</button>';

        // textContent coloca o texto "puro", sem interpretar HTML (evita que alguém injete código)
        li.querySelector('strong').textContent = item.tema;
        li.querySelector('small').textContent = item.tamanho;
        if (item.personalizacao) {
            li.querySelector('.topo-personalizado').textContent = '"' + item.personalizacao + '"';
        } else {
            li.querySelector('.topo-personalizado').remove();
        }

        li.querySelector('.remover').addEventListener('click', function () {
            carrinho.splice(posicao, 1);   // splice remove 1 item a partir dessa posição
            salvarCarrinho();
            mostrarCarrinho();
        });

        listaItens.appendChild(li);
    });

    document.getElementById('total').textContent = formatarPreco(total);
    document.getElementById('contador').textContent = carrinho.length;

    // Carrinho vazio: mostra a mensagem. Com itens: mostra o total e o formulário.
    const temItens = carrinho.length > 0;
    carrinhoVazio.hidden = temItens;
    areaPedido.hidden = !temItens;
}

const aviso = document.getElementById('aviso');

function mostrarAviso(texto) {
    aviso.textContent = texto;
    aviso.hidden = false;
    setTimeout(function () { aviso.hidden = true; }, 2500);   // some depois de 2,5 segundos
}


/* ---------------- Máscara do telefone: 11946339044 vira (11) 94633-9044 ---------------- */

const campoTelefone = document.getElementById('telefone');

campoTelefone.addEventListener('input', function () {
    const numeros = campoTelefone.value.replace(/\D/g, '').slice(0, 11);   // \D = tudo que não é número

    if (numeros.length > 6) {
        campoTelefone.value = '(' + numeros.slice(0, 2) + ') ' + numeros.slice(2, numeros.length - 4) + '-' + numeros.slice(-4);
    } else if (numeros.length > 2) {
        campoTelefone.value = '(' + numeros.slice(0, 2) + ') ' + numeros.slice(2);
    } else {
        campoTelefone.value = numeros;
    }
});


/* ---------------- Data mínima da festa ---------------- */

// Hoje + 4 dias, no formato "2026-11-01" que o campo de data entende
function dataMinimaEntrega() {
    const data = new Date();
    data.setDate(data.getDate() + DIAS_ANTECEDENCIA);
    // toLocaleDateString('sv-SE') devolve exatamente no formato ano-mês-dia
    return data.toLocaleDateString('sv-SE');
}

document.getElementById('data').min = dataMinimaEntrega();


/* ---------------- Finalizar: validar e abrir o WhatsApp ---------------- */

const formFinalizar = document.getElementById('formFinalizar');
const listaErros = document.getElementById('listaErros');

formFinalizar.addEventListener('submit', function (evento) {
    evento.preventDefault();

    const nome = formFinalizar.elements['nome'].value.trim();
    const telefone = formFinalizar.elements['telefone'].value.replace(/\D/g, '');
    const data = formFinalizar.elements['data'].value;
    const observacao = formFinalizar.elements['observacao'].value.trim();

    // ---- Validação ----
    const erros = [];

    if (nome.length < 3) {
        erros.push('Informe seu nome.');
    }
    if (telefone.length < 10 || telefone.length > 11) {
        erros.push('Informe um WhatsApp válido com DDD.');
    }
    // Comparar textos no formato "2026-11-01" funciona como comparar datas
    if (data === '' || data < dataMinimaEntrega()) {
        erros.push('Escolha uma data com pelo menos ' + DIAS_ANTECEDENCIA + ' dias de antecedência. É o tempo que a Manu precisa para comprar os ingredientes, preparar e decorar o seu bolo com todo o carinho 💗');
    }

    if (erros.length > 0) {
        listaErros.innerHTML = '';
        erros.forEach(function (erro) {
            const li = document.createElement('li');
            li.textContent = erro;
            listaErros.appendChild(li);
        });
        listaErros.hidden = false;
        return;   // para aqui: não abre o WhatsApp
    }

    listaErros.hidden = true;

    // ---- Monta a mensagem e abre o WhatsApp ----
    const mensagem = montarMensagem(nome, data, observacao);
    window.open('https://wa.me/' + NUMERO_WHATSAPP + '?text=' + encodeURIComponent(mensagem), '_blank');

    carrinho = [];   // esvazia o carrinho depois de enviar
    salvarCarrinho();
    formFinalizar.reset();
    mostrarCarrinho();
    fecharCarrinho();
});

function montarMensagem(nome, data, observacao) {
    const partes = data.split('-');   // "2026-11-01" vira ["2026", "11", "01"]
    const dataBrasileira = partes[2] + '/' + partes[1] + '/' + partes[0];

    let texto = 'Olá! Quero fazer um pedido pelo site 🎂\n\n';
    texto += 'Nome: ' + nome + '\n';
    texto += 'Data da festa: ' + dataBrasileira + '\n\n';

    let total = 0;
    carrinho.forEach(function (item, posicao) {
        total += item.preco;
        texto += (posicao + 1) + '. Bolo ' + item.tema + ' - ' + item.tamanho + ' - ' + formatarPreco(item.preco) + '\n';
        if (item.personalizacao) {
            texto += '   Topo: ' + item.personalizacao + '\n';
        }
    });

    texto += '\nTotal: ' + formatarPreco(total);
    if (observacao) {
        texto += '\nObservação: ' + observacao;
    }
    return texto;
}


// Desenha o carrinho assim que a página abre (mostra o que ficou salvo da última visita)
mostrarCarrinho();
