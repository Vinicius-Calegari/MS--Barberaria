<?php

declare(strict_types=1);

$defaults = [
    'Degradê' => [
        'preco' => 50.00,
        'duracao' => 60,
        'descricao' => 'Transição precisa, acabamento limpo e visual moderno.',
        'imagem' => 'img/degrade.png',
        'icone' => 'fa-scissors',
    ],
    'Social' => [
        'preco' => 45.00,
        'duracao' => 60,
        'descricao' => 'Corte clássico, alinhado e versátil para qualquer ocasião.',
        'imagem' => 'img/Social.png',
        'icone' => 'fa-user-tie',
    ],
    'Barba' => [
        'preco' => 35.00,
        'duracao' => 45,
        'descricao' => 'Modelagem, contorno e acabamento para valorizar o rosto.',
        'imagem' => 'img/barba.jpg',
        'icone' => 'fa-user',
    ],
    'Pigmentação' => [
        'preco' => 120.00,
        'duracao' => 90,
        'descricao' => 'Definição e contraste com aplicação cuidadosa e acabamento natural.',
        'imagem' => 'img/Pigmentacao.jpg',
        'icone' => 'fa-droplet',
    ],
    'Luzes' => [
        'preco' => 90.00,
        'duracao' => 90,
        'descricao' => 'Técnica de iluminação para destacar textura, movimento e estilo.',
        'imagem' => 'img/Luzes.jpg',
        'icone' => 'fa-wand-magic-sparkles',
    ],
    'Nevou' => [
        'preco' => 80.00,
        'duracao' => 90,
        'descricao' => 'Descoloração de impacto para um visual marcante e moderno.',
        'imagem' => 'img/Nevou.jpg',
        'icone' => 'fa-snowflake',
    ],
    'Corte+Barba' => [
        'preco' => 70.00,
        'duracao' => 90,
        'descricao' => 'Pacote completo com corte e barba no mesmo atendimento.',
        'imagem' => 'img/Corte.jpg',
        'icone' => 'fa-star',
    ],
];

try {
    require_once __DIR__ . '/database.php';
    $pdo = getDBConnection();
    $rows = $pdo->query('SELECT nome, preco, duracao, ativo FROM servicos WHERE ativo = 1 ORDER BY nome')->fetchAll();

    $ativos = [];
    foreach ($rows as $row) {
        $nome = (string) $row['nome'];
        if (!isset($defaults[$nome])) {
            continue;
        }
        $item = $defaults[$nome];
        $item['preco'] = (float) $row['preco'];
        $item['duracao'] = (int) $row['duracao'];
        $ativos[$nome] = $item;
    }

    if ($ativos !== []) {
        return $ativos;
    }
} catch (Throwable $e) {
    error_log('Falha ao carregar catálogo do banco: ' . $e->getMessage());
}

return $defaults;
