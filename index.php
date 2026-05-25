<?php
// Inclui o arquivo que contém a lógica de cálculos e regras de negócio
require_once 'funções.php';

// Inicializa as variáveis de controle como nulas
$resultadoCalculo = null;
$resultadoFase = null;

// Verifica se o formulário foi enviado via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valida se o RM foi preenchido e o converte para inteiro
    if (isset($_POST['rm']) && !empty($_POST['rm'])) {
        $rm = intval($_POST['rm']);
        
        // Calcula os recursos iniciais da matrícula e salva no arquivo JSON
        $resultadoCalculo = calcularRecursosIniciais($rm);
        salvarDadosJson($resultadoCalculo);
        
        // Definição dos parâmetros do cenário da montanha (Desafio Único)
        $distancia = 50;
        $dificuldade = 3;
        $altitude = 1500;
        
        // Processa os gastos da viagem e valida as condições especiais
        $resultadoFase = processarFaseMontanha($resultadoCalculo, $distancia, $dificuldade, $altitude);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Desafio</title>
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
                <h3>1. Recursos Iniciais Salvos no JSON:</h3>
                <p><strong>Energia:</strong> <?= $resultadoCalculo['energia'] ?></p>
                <p><strong>Água:</strong> <?= $resultadoCalculo['agua'] ?></p>
                <p><strong>Combustível:</strong> <?= $resultadoCalculo['combustivel'] ?></p>
            </div>

            <div class="resultado resultado-montanha">
                <h3>2. Resultado da Fase Montanha:</h3>
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