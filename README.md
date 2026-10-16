# 🎂 Doces e Bolos da Manu

Site (landing page) de uma confeitaria real, feito como **trabalho da faculdade**.

| | |
|---|---|
| **Cliente** | Doces e Bolos da Manu: bolos personalizados e doces artesanais (Jardim Comercial, São Paulo/SP) |
| **Instagram** | [@docesebolosda.manu](https://www.instagram.com/docesebolosda.manu/) |
| **WhatsApp** | (11) 94633-9044 |
| **Tecnologias** | HTML, CSS, JavaScript e **PHP** (o foco do trabalho) |
| **Banco de dados** | Nenhum: os dados ficam em arquivos **JSON** |
| **Funciona em** | Computador, tablet e celular (Android e iPhone) |

---

## Índice
1. [O que o site faz](#1-o-que-o-site-faz)
2. [Estrutura de pastas](#2-estrutura-de-pastas)
3. [Explicação de cada arquivo](#3-explicação-de-cada-arquivo)
4. [Funções do PHP (funcoes.php)](#4-funções-do-php-funcoesphp)
5. [O caminho de um pedido, passo a passo](#5-o-caminho-de-um-pedido-passo-a-passo)
6. [Conceitos de PHP usados no projeto](#6-conceitos-de-php-usados-no-projeto)
7. [Segurança](#7-segurança)
8. [Responsivo (celular, tablet e computador)](#8-responsivo-celular-tablet-e-computador)
9. [Como rodar no computador](#9-como-rodar-no-computador)
10. [Como publicar na internet](#10-como-publicar-na-internet)
11. [Como subir no GitHub](#11-como-subir-no-github)
12. [Como alterar produtos, preços e textos](#12-como-alterar-produtos-preços-e-textos)
13. [Como testar](#13-como-testar)
14. [Perguntas que o professor pode fazer](#14-perguntas-que-o-professor-pode-fazer)

---

## 1. O que o site faz

O site é **uma página só**, dividida em 4 seções. O menu leva até cada uma:

| Seção | O que tem |
|---|---|
| **Home** | Frase de boas-vindas, foto de um bolo, botões "Ver bolos" e "Pedir pelo WhatsApp" |
| **Sobre mim** | Apresentação da confeiteira |
| **Produtos** | Os bolos (temas). Para cada um, o cliente escolhe o **tamanho**, pode escrever o **nome e a idade** do topo e clica em **Adicionar ao carrinho** |
| **Contato** | WhatsApp, Instagram, bairro e um botão para chamar no WhatsApp |

Além das seções:
- **Carrinho lateral:** uma barra abre pela direita com os bolos escolhidos, o total e um formulário para finalizar.
- **Finalizar pedido:** o PHP confere os dados, **salva o pedido** e abre o **WhatsApp** da Manu com a mensagem pronta.
- **Botão do WhatsApp fixo:** fica sempre no canto da tela, em qualquer parte do site.

---

## 2. Estrutura de pastas

```
DOCES E BOLOS DA MANU/
│
├── index.php                  A página do site (as 4 seções)
├── carrinho.php               Processa os formulários: adicionar, remover e finalizar
│
├── includes/                  Pedaços de PHP reaproveitados
│   ├── funcoes.php            Configurações + funções usadas em vários lugares
│   ├── topo.php               <head>, logo, menu e botão do carrinho
│   ├── carrinho-lateral.php   Barra lateral do carrinho + formulário de finalizar
│   └── rodape.php             Rodapé, botão flutuante do WhatsApp e o <script>
│
├── dados/                     O "banco de dados" (arquivos JSON)
│   ├── produtos.json          Os bolos e os tamanhos com preço
│   ├── pedidos.json           Os pedidos recebidos (o PHP cria e preenche)
│   └── .htaccess              Proíbe abrir esta pasta pelo navegador
│
├── assets/                    Arquivos do visual (front-end)
│   ├── css/estilo.css         Cores, tamanhos e o layout responsivo
│   ├── js/script.js           Abre/fecha o menu e o carrinho, máscara do telefone
│   └── img/
│       ├── logo.jpg           Logotipo da cliente
│       └── bolos/             10 fotos de bolos tiradas do Instagram da cliente
│
├── .gitignore                 Arquivos que NÃO vão para o GitHub
└── README.md                  Este arquivo
```

**Por que separar assim?**
- **Front-end** (o que o usuário vê) fica em `assets/`.
- **Back-end** (o que roda no servidor) são os arquivos `.php`.
- **Dados** ficam em `dados/`, separados do código.
- Partes que se repetem ficam em `includes/`, para não copiar código (princípio **DRY**: *Don't Repeat Yourself*).

---

## 3. Explicação de cada arquivo

### `index.php`: a página
1. Carrega as funções: `require 'includes/funcoes.php';`
2. Lê o arquivo de produtos: `$dados = lerJson(ARQUIVO_PRODUTOS);`
3. Puxa o topo: `include 'includes/topo.php';`
4. Monta as 4 seções em HTML. Na seção **Produtos**, um `foreach` repete o card uma vez para cada bolo do JSON. Dentro de cada card, outro `foreach` monta as opções de tamanho.
5. Puxa o carrinho lateral e o rodapé no final.

> Cada card tem um `<form method="post" action="carrinho.php">` com campos escondidos (`hidden`) que dizem **qual ação** (`adicionar`) e **qual bolo** (`produto_id`).

### `carrinho.php`: o "cérebro" do pedido
Não mostra nada na tela: **recebe o formulário, processa e redireciona** de volta para a página.

- Só aceita **POST**. Se alguém abrir o endereço direto (GET), volta para o início.
- Lê o campo `acao` e chama a função certa:

| Ação | Função | O que faz |
|---|---|---|
| `adicionar` | `adicionarAoCarrinho()` | Busca o bolo e o tamanho no JSON e coloca na `$_SESSION['carrinho']` |
| `remover` | `removerDoCarrinho()` | Tira o item da posição informada |
| `finalizar` | `finalizarPedido()` | Valida, salva em `pedidos.json` e abre o WhatsApp |

Outras funções do arquivo:
- `montarMensagem($pedido)`: monta o texto que aparece pronto no WhatsApp.
- `voltarComCarrinhoAberto()`: redireciona para `index.php?carrinho=aberto#produtos`.

### `includes/funcoes.php`: configurações e funções
- Inicia a sessão (`session_start()`), necessária para o carrinho.
- Define o fuso horário de São Paulo.
- Guarda os dados fixos da loja em **constantes**: `WHATSAPP_NUMERO`, `INSTAGRAM_USUARIO`, `DIAS_ANTECEDENCIA`...
- Tem as funções usadas em mais de um arquivo (ver a [seção 4](#4-funções-do-php-funcoesphp)).

### `includes/topo.php`
O começo do HTML: `<head>` com o `meta viewport` (essencial para o celular), as fontes do Google, o CSS e o cabeçalho com logo, menu e botão do carrinho mostrando a **quantidade de itens**.

### `includes/carrinho-lateral.php`
- Lista os itens da sessão com `foreach`, mostrando foto, tema, tamanho, personalização e preço.
- Cada item tem um botão 🗑 (um formulário pequeno com `acao=remover`).
- Mostra o **total**.
- Tem o formulário de **finalizar** (nome, WhatsApp, data da festa, observação).
- Se o `carrinho.php` encontrou erros, eles aparecem aqui, e **o que o cliente digitou continua preenchido**.

### `includes/rodape.php`
O rodapé, o **botão flutuante do WhatsApp** e o `<script>` do JavaScript.

### `dados/produtos.json`
```json
{
    "tamanhos": [
        { "id": "P", "nome": "Pequeno (até 15 fatias)", "preco": 120.00 }
    ],
    "produtos": [
        { "id": 1, "tema": "Laço Rosa", "descricao": "...", "imagem": "bolo-laco-rosa.jpg" }
    ]
}
```
O **preço depende do tamanho**, e o tema define a decoração.

### `dados/pedidos.json`
Cada pedido salvo fica assim:
```json
{
    "id": "6710a2c5e1f3b",
    "criado_em": "2026-10-16 14:30:00",
    "cliente": "Maria",
    "telefone": "11999999999",
    "data_festa": "2026-10-25",
    "observacao": "Sem coco",
    "itens": [ { "tema": "Stitch", "tamanho": "Médio (até 25 fatias)", "preco": 180, "personalizacao": "Ana 9 anos" } ],
    "total": 180
}
```

### `assets/css/estilo.css`
O visual do site. As cores ficam em **variáveis** no começo (`--rosa-forte`, `--rosa-claro`...), então trocar uma cor é mexer em um lugar só. Foi escrito **mobile-first** (ver a [seção 8](#8-responsivo-celular-tablet-e-computador)).

### `assets/js/script.js`
Só deixa a navegação mais prática:
- abre e fecha o **menu** no celular;
- abre e fecha o **carrinho** (clicar fora também fecha);
- **máscara do telefone**: `11946339044` vira `(11) 94633-9044` enquanto digita.

> As regras importantes (preço, validação, salvar) **não** ficam no JavaScript, porque o usuário consegue alterar o JavaScript pelo navegador. Elas ficam no PHP.

---

## 4. Funções do PHP (`funcoes.php`)

| Função | O que faz | Exemplo |
|---|---|---|
| `lerJson($caminho)` | Lê um arquivo JSON e devolve um array | `lerJson(ARQUIVO_PRODUTOS)` |
| `salvarJson($caminho, $dados)` | Grava um array no arquivo JSON | `salvarJson(ARQUIVO_PEDIDOS, $pedidos)` |
| `limpar($texto)` | Tira espaços das pontas e tags HTML do que foi digitado | `limpar(" <b>Ana</b> ")` → `"Ana"` |
| `escapar($texto)` | Deixa o texto seguro para mostrar na página (contra XSS) | `escapar("<script>")` → `&lt;script&gt;` |
| `formatarPreco($valor)` | Formata como dinheiro | `formatarPreco(180)` → `R$ 180,00` |
| `buscarPorId($lista, $id)` | Procura um item pelo `id` dentro de uma lista | `buscarPorId($produtos, 3)` |
| `itensDoCarrinho()` | Devolve os itens da sessão | `count(itensDoCarrinho())` |
| `totalDoCarrinho()` | Soma os preços dos itens | `formatarPreco(totalDoCarrinho())` |
| `linkWhatsapp($mensagem)` | Monta o link `wa.me` com a mensagem | `linkWhatsapp("Olá!")` |
| `dataMinimaEntrega()` | Hoje + 2 dias, no formato `AAAA-MM-DD` | `"2026-10-18"` |

---

## 5. O caminho de um pedido, passo a passo

```
 NAVEGADOR (cliente)                          SERVIDOR (PHP)
 ───────────────────                          ──────────────
 1. Abre o site ──────── GET index.php ─────► lê produtos.json e monta o HTML
                ◄────────── HTML ────────────

 2. Escolhe "Stitch", tamanho M
    e clica em Adicionar ── POST carrinho.php ──► busca o bolo e o preço NO JSON
                                                  guarda na $_SESSION['carrinho']
                ◄── redireciona para index.php?carrinho=aberto

 3. Vê o carrinho aberto com o item e o total

 4. Preenche nome, WhatsApp e data
    e clica em Enviar ───── POST carrinho.php ──► valida os dados
                                                  ├─ tem erro?  volta com as mensagens
                                                  └─ tudo certo:
                                                       salva em pedidos.json
                                                       esvazia o carrinho
                ◄── redireciona para wa.me/55119...?text=pedido

 5. O WhatsApp abre com o pedido pronto. O cliente só aperta Enviar.
```

**Exemplo da mensagem que chega no WhatsApp:**
```
Olá! Quero fazer um pedido pelo site 🎂

Nome: Maria
Data da festa: 25/10/2026

1. Bolo Stitch - Médio (até 25 fatias) - R$ 180,00
   Topo: Ana 9 anos

Total: R$ 180,00
Observação: Sem coco
```

---

## 6. Conceitos de PHP usados no projeto

| Conceito | Onde aparece | Para que serve |
|---|---|---|
| `require` / `include` | Início do `index.php` e do `carrinho.php` | Juntar arquivos: o topo e o rodapé são escritos uma vez só |
| **Variáveis e arrays** | Em todo lugar (`$produtos`, `$pedido`) | Guardar dados |
| **Constantes** (`const`) | `funcoes.php` | Valores que não mudam (WhatsApp, caminhos) |
| **Funções** | `funcoes.php` e `carrinho.php` | Organizar o código em partes pequenas com nome claro |
| `foreach` | `index.php`, `carrinho-lateral.php` | Repetir o HTML para cada bolo, tamanho ou item |
| `if` / `elseif` | `carrinho.php` | Decidir qual ação executar |
| `$_GET` | `?carrinho=aberto` | Ler dados que vêm **no endereço** (só leitura) |
| `$_POST` | Formulários | Receber dados **enviados** pelo usuário |
| `$_SESSION` | O carrinho | Lembrar dos dados do visitante entre uma página e outra |
| `$_SERVER['REQUEST_METHOD']` | `carrinho.php` | Saber se a requisição é GET ou POST |
| `json_decode` / `json_encode` | `lerJson()` / `salvarJson()` | Converter entre JSON (texto) e array (PHP) |
| `file_get_contents` / `file_put_contents` | `lerJson()` / `salvarJson()` | Ler e gravar arquivos |
| `header('Location: ...')` | `carrinho.php` | Redirecionar o navegador para outra página |
| `htmlspecialchars` | `escapar()` | Segurança contra XSS |
| `preg_replace` / `preg_match` | Validação | Tirar o que não é número do telefone e conferir o formato da data |
| `number_format` | `formatarPreco()` | Mostrar `R$ 1.250,00` |
| `urlencode` | `linkWhatsapp()` | Colocar texto com espaços e acentos dentro de um link |
| `date` / `strtotime` | Datas | Data de hoje e cálculo de "hoje + 2 dias" |
| `<?= ... ?>` | HTML | Atalho para `<?php echo ... ?>` |

### GET x POST (fase 1 do guia)
- **GET** = pedir algo. Os dados vão no endereço (`index.php?carrinho=aberto`). Usado para **ler**.
- **POST** = enviar algo. Os dados vão escondidos no corpo da requisição. Usado para **criar ou alterar** (adicionar ao carrinho, salvar pedido).

### CRUD no projeto
| CRUD | No site |
|---|---|
| **C**reate | Adicionar ao carrinho / salvar pedido |
| **R**ead | Listar os bolos do `produtos.json` |
| **U**pdate | (não usado: o carrinho é refeito a cada ação) |
| **D**elete | Remover item do carrinho |

---

## 7. Segurança

| Risco | O que pode acontecer | Como o site se protege |
|---|---|---|
| **XSS** (injeção de código) | Alguém digita `<script>` no nome e o código roda na página | Tudo o que é mostrado passa por `escapar()` (`htmlspecialchars`), e o que é digitado passa por `limpar()` (`strip_tags`) |
| **Alterar o preço** | Alguém edita o HTML e muda o valor | O formulário só envia o **id** do bolo e do tamanho. O preço é sempre lido do `produtos.json` |
| **Burlar a validação** | Desligar o JavaScript ou editar o HTML | A validação de verdade é feita no **PHP** (servidor) |
| **Ver dados de clientes** | Abrir `/dados/pedidos.json` pelo navegador | O `.htaccess` bloqueia a pasta `dados/` |
| **Acessar o carrinho.php direto** | Abrir o endereço sem formulário | Só aceita **POST**. Se for GET, redireciona |
| **Textos enormes** | Encher o arquivo de lixo | Limite de tamanho (`maxlength` no HTML + `mb_substr` no PHP) |
| **Dois pedidos ao mesmo tempo** | Um apagar o outro no arquivo | `LOCK_EX` trava o arquivo durante a gravação |

---

## 8. Responsivo (celular, tablet e computador)

O CSS foi feito **mobile-first**: as regras normais são para o celular, e as `@media` adaptam para telas maiores.

| Tela | Largura | Como fica |
|---|---|---|
| Celular | até 639px | 1 bolo por linha, menu escondido no botão ☰ |
| Tablet | de 640px | 2 bolos por linha |
| Computador | de 960px | 3 bolos por linha, menu aberto no topo, textos e fotos lado a lado |

Cuidados para **iPhone e Android**:
- `<meta name="viewport" ...>`: sem ele, o celular mostra o site de computador em miniatura.
- Campos com `font-size: 16px`: abaixo disso o iPhone dá **zoom sozinho** ao tocar no campo.
- `type="tel"` abre o teclado numérico e `type="date"` abre o calendário do celular.
- `img { max-width: 100% }`: nenhuma imagem passa da largura da tela.
- Botões grandes, fáceis de tocar com o dedo.

---

## 9. Como rodar no computador

Precisa do **PHP 8** ou mais novo.

**Opção A: PHP portátil (sem instalar)**
1. Baixar o zip "VS16 x64 Non Thread Safe" em https://windows.php.net/download
2. Extrair numa pasta, por exemplo `C:\php`
3. Abrir o terminal **dentro da pasta do projeto** e rodar:
   ```
   C:\php\php.exe -S localhost:8000
   ```
4. Abrir **http://localhost:8000** no navegador.

**Opção B: XAMPP**
Copiar a pasta do projeto para `C:\xampp\htdocs\docesemanu`, iniciar o Apache e abrir `http://localhost/docesemanu`.

> O servidor de teste do PHP (opção A) **ignora o `.htaccess`**. A proteção da pasta `dados/` só funciona no Apache (XAMPP ou hospedagem).

---

## 10. Como publicar na internet

Exemplo com o **InfinityFree** (gratuito, aceita PHP):
1. Criar uma conta e um site (domínio gratuito, ex.: `docesemanu.infinityfreeapp.com`).
2. Abrir o **Gerenciador de Arquivos** (ou um programa de FTP, como o FileZilla).
3. Entrar na pasta `htdocs`.
4. Enviar: `index.php`, `carrinho.php`, `includes/`, `dados/` e `assets/`.
5. Abrir o link no **Android** e no **iPhone** e fazer um pedido de teste.

---

## 11. Como subir no GitHub

O `.gitignore` já impede que o `dados/pedidos.json` (com nome e telefone de clientes) vá para o GitHub.

```bash
git init
git add .
git commit -m "Site Doces e Bolos da Manu"
git branch -M main
git remote add origin https://github.com/SEU-USUARIO/doces-e-bolos-da-manu.git
git push -u origin main
```

Para os colegas baixarem:
```bash
git clone https://github.com/SEU-USUARIO/doces-e-bolos-da-manu.git
```

---

## 12. Como alterar produtos, preços e textos

| Quero mudar... | Onde |
|---|---|
| Preço ou tamanhos | `dados/produtos.json` → `"tamanhos"` |
| Adicionar um bolo | Colocar a foto em `assets/img/bolos/` e adicionar um item em `"produtos"` com um `id` novo |
| Tirar um bolo | Apagar o item em `"produtos"` |
| Número do WhatsApp ou Instagram | Constantes no começo de `includes/funcoes.php` |
| Prazo mínimo (dias) | `DIAS_ANTECEDENCIA` em `includes/funcoes.php` |
| Texto do "Sobre mim" | `index.php`, seção `id="sobre"` |
| Cores | Variáveis no começo de `assets/css/estilo.css` |

Exemplo de bolo novo:
```json
{ "id": 11, "tema": "Frozen", "descricao": "Azul e branco com flocos de neve.", "imagem": "bolo-frozen.jpg" }
```

> ⚠️ **Pendente com a cliente:** os **preços** atuais são exemplo, o texto do **Sobre mim** é provisório e falta uma **foto da Manu**.

---

## 13. Como testar

| Teste | Resultado esperado |
|---|---|
| Abrir o site | As 4 seções aparecem; o menu leva até cada uma |
| Adicionar um bolo | O carrinho abre com o item, o preço e o contador do topo aumenta |
| Remover um item | O item some e o total diminui |
| Finalizar com campos vazios | Aparecem as mensagens de erro e o carrinho continua aberto |
| Telefone com 8 números | Erro "Informe um WhatsApp válido com DDD" |
| Data de hoje | Erro de antecedência mínima |
| Finalizar certo | Abre o WhatsApp com o pedido e o pedido aparece em `dados/pedidos.json` |
| Abrir `/dados/pedidos.json` na hospedagem | Acesso bloqueado (erro 403) |
| Abrir no celular | Sem rolagem para os lados, menu ☰ funcionando, botão do WhatsApp visível |
| Testar no iPhone | Tocar nos campos não dá zoom |

---

## 14. Perguntas que o professor pode fazer

**Por que PHP e não só HTML?**
O HTML é fixo. Com o PHP, os bolos vêm de um arquivo de dados (basta mudar o JSON para mudar o site), o carrinho é guardado na sessão e o pedido é validado e salvo no servidor.

**Onde ficam os dados, se não tem banco?**
Em arquivos JSON na pasta `dados/`. O PHP lê com `file_get_contents` + `json_decode` e grava com `json_encode` + `file_put_contents`.

**Como o carrinho "lembra" dos produtos?**
Pela **sessão** (`$_SESSION`). O PHP cria um cookie com um código para o visitante e guarda os dados no servidor, ligados a esse código.

**Qual a diferença entre `include` e `require`?**
Os dois trazem outro arquivo. Se o arquivo não existir, o `require` **para** o site com erro e o `include` só avisa e continua. Usamos `require` no `funcoes.php` porque sem ele nada funciona.

**Por que validar no PHP se o HTML já tem `required`?**
O que roda no navegador pode ser desligado ou alterado pelo usuário. O servidor é o único lugar confiável.

**Por que o preço não vai no formulário?**
Se fosse, qualquer pessoa poderia editar o HTML e mudar o valor. O formulário só manda o **id**, e o PHP busca o preço no JSON.

**O que é o `htmlspecialchars`?**
Transforma `<` e `>` em códigos (`&lt;` `&gt;`). Assim, se alguém digitar código, ele aparece como texto e não é executado (proteção contra **XSS**).

**Por que redirecionar depois do POST?**
Se a página só mostrasse o resultado, apertar F5 enviaria o formulário de novo (bolo duplicado). Redirecionando, o F5 só recarrega a página. Esse padrão se chama **POST-Redirect-GET**.

**O que é o `?carrinho=aberto` no endereço?**
Um parâmetro **GET**. O PHP lê `$_GET['carrinho']` e, se for `aberto`, já desenha o carrinho aberto. Isso funciona até sem JavaScript.

**O que o JavaScript faz então?**
Só a parte visual: abrir e fechar o menu e o carrinho e formatar o telefone. Toda regra importante está no PHP.

**Como o site funciona no celular?**
Com `meta viewport`, CSS mobile-first, `@media` para telas maiores, Flexbox e Grid, imagens com `max-width: 100%` e fonte de 16px nos campos.

---

## Créditos
- Fotos dos bolos: Instagram [@docesebolosda.manu](https://www.instagram.com/docesebolosda.manu/), usadas com autorização da cliente.
- Fontes: [Google Fonts](https://fonts.google.com/) (Dancing Script e Poppins).
