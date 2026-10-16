<?php
/*
 * rodape.php
 * Rodapé, botão flutuante do WhatsApp e o JavaScript.
 * É incluído no fim da página com: include 'includes/rodape.php';
 */
?>
<footer class="rodape">
    <div class="container">
        <img src="assets/img/logo.jpg" alt="Logo Doces e Bolos da Manu" class="rodape-logo">
        <p>© <?= date('Y') ?> <?= NOME_LOJA ?> · Feito com carinho 💗</p>
    </div>
</footer>

<!-- Botão do WhatsApp fixo no canto da tela: aparece em todas as seções -->
<a href="<?= linkWhatsapp() ?>" class="whatsapp-flutuante" target="_blank" rel="noopener" aria-label="Conversar no WhatsApp">
    <svg viewBox="0 0 32 32" width="30" height="30" aria-hidden="true"><path fill="currentColor" d="M16 3C9 3 3.3 8.7 3.3 15.7c0 2.2.6 4.4 1.7 6.3L3.2 29l7.2-1.9c1.8 1 3.9 1.5 5.9 1.5 7 0 12.7-5.7 12.7-12.7S23 3 16 3zm0 23.2c-1.9 0-3.8-.5-5.4-1.5l-.4-.2-4.3 1.1 1.1-4.2-.3-.4c-1.1-1.7-1.6-3.6-1.6-5.6C5.1 9.9 10 5.1 16 5.1s10.9 4.9 10.9 10.9S22 26.2 16 26.2zm6-8.2c-.3-.2-1.9-.9-2.2-1s-.5-.2-.7.2-.8 1-1 1.2-.4.2-.7.1c-.3-.2-1.4-.5-2.6-1.6-1-.9-1.6-1.9-1.8-2.2s0-.5.1-.6l.5-.6c.2-.2.2-.3.3-.6.1-.2 0-.4 0-.6l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4s-1.2 1.1-1.2 2.8 1.2 3.2 1.4 3.5c.2.2 2.4 3.6 5.7 5 .8.3 1.4.5 1.9.7.8.3 1.5.2 2.1.1.6-.1 1.9-.8 2.2-1.5.3-.7.3-1.4.2-1.5-.1-.2-.3-.3-.6-.4z"/></svg>
</a>

<script src="assets/js/script.js"></script>
</body>
</html>
