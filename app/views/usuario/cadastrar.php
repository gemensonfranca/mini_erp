<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <style>
        form { max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        input { width: 100%; margin-bottom: 10px; padding: 10px; }
        button { padding: 10px 15px; background: #28a745; color: #fff; border: none; width: 100%; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Cadastro do Cliente</h2>
    <form action="/usuario/salvar" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="usuario[nome]" required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="usuario[email]" required>

        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="usuario[endereco]" required>

        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="usuario[cep]" required>

        <button type="submit" class="finalizar-pedido">Finalizar Pedido</button>
    </form>
</body>
</html>
