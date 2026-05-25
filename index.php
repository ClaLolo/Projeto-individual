<?php
// Inclui o arquivo de funções
require_once 'funções.php';

$resultadoCalculo = null;
$resultadoFase = null;

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rm']) && !empty($_POST['rm'])) {
        $rm = intval($_POST['rm']);
        
        // 1. Calcula os recursos baseados no seu RM
        $recursosIniciais = calcularRecursosIniciais($rm);
        
        // 2. Cria e salva o arquivo JSON estruturado com os recursos e os dados da fase
        salvarDadosJson($recursosIniciais);
        
        // 3. Lê o arquivo JSON que acabou de ser gravado para garantir a integridade dos dados
        $conteudoJson = file_get_contents(__DIR__ . '/../dados/missão.json');
        $dadosDoJson = json_decode($conteudoJson, true);
        
        // 4. Executa a fase da montanha lendo as informações vindas de dentro do arquivo JSON
        $resultadoCalculo = $dadosDoJson; // Guarda a estrutura do JSON para exibir na tela
        $resultadoFase = processarFaseMontanha($dadosDoJson);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Desafio - Fase Montanha</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
        
        <?php if (!$resultadoCalculo): ?>
            <h2>Regras de Geração dos Recursos</h2>
            <form method="POST" action="">
                <label for="rm">Digite o número do RM:</label>
                <input type="number" id="rm" name="rm" placeholder="Ex: 3183" required>
                <button type="submit">Calcular e Executar</button>
            </form>

        <?php else: ?>
            <h2>Resultados Gerados para o RM: <?= $resultadoCalculo['rm'] ?></h2>

            <div class="resultado">
                <h3>1. Recursos Iniciais:</h3>
                <p><strong>Energia:</strong> <?= $resultadoCalculo['recursos']['energia'] ?></p>
                <p><strong>Água:</strong> <?= $resultadoCalculo['recursos']['agua'] ?></p>
                <p><strong>Combustível:</strong> <?= $resultadoCalculo['recursos']['combustivel'] ?></p>
            </div>

            <div class="resultado resultado-montanha">
                <h3>2. Resultado da Fase Montanha:</h3>
                <p><strong>Cenário:</strong> <?= $resultadoCalculo['fase']['nome'] ?> (Distância: <?= $resultadoCalculo['fase']['distancia'] ?>km)</p>
                <p><strong>Status:</strong> <?= $resultadoFase['status'] ?></p>
                <p><strong>Energia Restante:</strong> <?= $resultadoFase['energia_final'] ?></p>
                <p><strong>Água Restante:</strong> <?= $resultadoFase['agua_final'] ?></p>
                <p><strong>Combustível Restante:</strong> <?= $resultadoFase['combustivel_final'] ?></p>

                <?php if (!empty($resultadoFase['alertas'])): ?>
                    <div class="alerta-container">
                        <strong>Ocorrências:</strong>
                        <ul>
                            <?php foreach ($resultadoFase['alertas'] as $alerta): ?>
                                <li><?= $alerta ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="btn-container">
                <a href="index.php" class="btn-novo-rm">
                     Calcular Novo RM
                </a>
            </div>
            
        <?php endif; ?>

    </div>
</body>
</html>