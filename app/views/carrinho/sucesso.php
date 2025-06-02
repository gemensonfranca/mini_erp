<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Compra realizada</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            font-size: 3em;
            color: #28a745;
            text-align: center;
            margin-bottom: 30px;
        }

        a.button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            font-size: 1.2em;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        a.button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Compra realizada com sucesso!</h1>
    <a class="button" href="/produto/index">Voltar aos produtos</a>
</body>
</html>
