<?php
// 1. Função para calcular os recursos iniciais com base no RM
function calcularRecursosIniciais($rm) {
    // Aplica as fórmulas matemáticas exigidas no enunciado do projeto
    $energia = round($rm / 2); // Divide por 2 e arredonda para número inteiro
    $agua = $energia * 0.15; // Define a água como 15% do valor da energia
    $combustivel = ($energia * 0.10) + ($agua * 0.40); // Soma 10% da energia com 40% da água

    // Retorna os dados organizados em um array, limitando os decimais a duas casas
    return [
        'rm' => $rm,
        'energia' => $energia,
        'agua' => round($agua, 2),
        'combustivel' => round($combustivel, 2)
    ];
}

// 2. Função para salvar os dados no arquivo JSON (Ajustada para a estrutura correta)
function salvarDadosJson($dados) {
    // Define o caminho para a pasta 'dados', saindo da pasta atual do projeto
    $diretorioDados = __DIR__ . '/../dados'; 
    $caminhoArquivo = $diretorioDados . '/missão.json';

    // Cria a pasta 'dados' de forma automatizada caso ela ainda não exista no sistema
    if (!is_dir($diretorioDados)) {
        mkdir($diretorioDados, 0777, true);
    }

    // Transforma o array do PHP em texto formato JSON com indentação organizada
    $jsonFinal = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    // Escreve e salva fisicamente os dados dentro do arquivo missão.json
    file_put_contents($caminhoArquivo, $jsonFinal);
}

// 3. Função para processar as regras de consumo da Fase Montanha
function processarFaseMontanha($recursosIniciais, $distancia, $dificuldade, $altitude) {
    // Calcula o gasto de cada recurso usando as fórmulas do Desafio Único
    $consumoEnergia = $distancia * $dificuldade * 2;
    $consumoAgua = $distancia * 3;
    $consumoCombustivel = ($altitude / 100) * 12;

    // Subtrai os consumos calculados dos recursos que vieram do JSON
    $energiaFinal = $recursosIniciais['energia'] - $consumoEnergia;
    $aguaFinal = $recursosIniciais['agua'] - $consumoAgua;
    $combustivelFinal = $recursosIniciais['combustivel'] - $consumoCombustivel;

    // Inicializa a lista de mensagens e define o status padrão de sucesso
    $alertas = [];
    $statusMissao = "MISSÃO CONCLUÍDA COM SUCESSO";

    // Regra 1: Se o combustível zerar ou negativar, a missão falha
    if ($combustivelFinal < 0) {
        $statusMissao = "MISSÃO FALHOU";
        $alertas[] = "Combustível insuficiente para completar a subida!";
    }

    // Regra 2: Se a energia final for menor que 30% da inicial, gera penalidade
    $limiteEnergia = $recursosIniciais['energia'] * 0.30;
    if ($energiaFinal < $limiteEnergia) {
        $alertas[] = "Penalidade aplicada: -20 pontos (Energia final menor que 30%)";
    }

    // Regra 3: Se a água final ficar abaixo de 20% da inicial, dispara o aviso
    $limiteAgua = $recursosIniciais['agua'] * 0.20; 
    if ($aguaFinal < $limiteAgua) {
        $alertas[] = "ALERTA DE DESIDRATAÇÃO";
    }

    // Retorna o balanço final completo da viagem para o index.php exibir
    return [
        'energia_final' => $energiaFinal,
        'agua_final' => round($aguaFinal, 2),
        'combustivel_final' => round($combustivelFinal, 2),
        'status' => $statusMissao,
        'alertas' => $alertas
    ];
}