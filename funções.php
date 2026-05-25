<?php
// 1. Função para calcular os recursos iniciais com base no RM
function calcularRecursosIniciais($rm) {
    $energia = round($rm / 2); // Divide por 2 e arredonda para inteiro
    $agua = $energia * 0.15; // 15% do valor da energia
    $combustivel = ($energia * 0.10) + ($agua * 0.40); // 10% da energia + 40% da água

    return [
        'rm' => $rm,
        'energia' => $energia,
        'agua' => round($agua, 2),
        'combustivel' => round($combustivel, 2)
    ];
}

// 2. Função para salvar os dados no arquivo JSON estruturado
function salvarDadosJson($recursosCalculados) {
    $diretorioDados = __DIR__ . '/../dados'; 
    $caminhoArquivo = $diretorioDados . '/missão.json';

    // Monta a estrutura exata exigida pelo seu projeto
    $estruturaJson = [
        "rm" => $recursosCalculados['rm'],
        "recursos" => [
            "energia" => $recursosCalculados['energia'],
            "agua" => $recursosCalculados['agua'],
            "combustivel" => $recursosCalculados['combustivel']
        ],
        "fase" => [
            "nome" => "Montanha",
            "distancia" => 42,
            "altitude" => 1800,
            "dificuldade" => 4
        ]
    ];

    if (!is_dir($diretorioDados)) {
        mkdir($diretorioDados, 0777, true);
    }

    $jsonFinal = json_encode($estruturaJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($caminhoArquivo, $jsonFinal);
}

// 3. Função que processa a Fase Montanha puxando os dados do JSON
function processarFaseMontanha($dadosCompletos) {
    // Puxa os recursos iniciais de dentro da chave 'recursos' do JSON
    $recursosIniciais = $dadosCompletos['recursos'];
    
    // Puxa as configurações da viagem de dentro da chave 'fase' do JSON
    $distancia = $dadosCompletos['fase']['distancia'];
    $dificuldade = $dadosCompletos['fase']['dificuldade'];
    $altitude = $dadosCompletos['fase']['altitude'];

    // Fórmulas de consumo dadas no enunciado
    $consumoEnergia = $distancia * $dificuldade * 2;
    $consumoAgua = $distancia * 3;
    $consumoCombustivel = ($altitude / 100) * 12;

    // Subtrai os gastos dos recursos iniciais
    $energiaFinal = $recursosIniciais['energia'] - $consumoEnergia;
    $aguaFinal = $recursosIniciais['agua'] - $consumoAgua;
    $combustivelFinal = $recursosIniciais['combustivel'] - $consumoCombustivel;

    $alertas = [];
    $statusMissao = "MISSÃO CONCLUÍDA COM SUCESSO";

    // Regra 1: Valida se o combustível acabou
    if ($combustivelFinal < 0) {
        $statusMissao = "MISSÃO FALHOU";
        $alertas[] = "Combustível insuficiente para completar a subida!";
    }

    // Regra 2: Energia final menor que 30% da inicial gera aviso/penalidade
    $limiteEnergia = $recursosIniciais['energia'] * 0.30;
    if ($energiaFinal < $limiteEnergia) {
        $alertas[] = "Penalidade aplicada: -20 pontos (Energia final menor que 30%)";
    }

    // Regra 3: Água final menor que 20% da inicial dispara o alerta
    $limiteAgua = $recursosIniciais['agua'] * 0.20; 
    if ($aguaFinal < $limiteAgua) {
        $alertas[] = "ALERTA DE DESIDRATAÇÃO";
    }

    return [
        'energia_final' => $energiaFinal,
        'agua_final' => round($aguaFinal, 2),
        'combustivel_final' => round($combustivelFinal, 2),
        'status' => $statusMissao,
        'alertas' => $alertas
    ];
}