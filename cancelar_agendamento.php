<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    try {
        $pdo = getDBConnection();
        
        // Verificar se o agendamento pertence ao usuário
        $stmt = $pdo->prepare("SELECT usuario_id FROM agendamentos WHERE id = ?");
        $stmt->execute([$id]);
        $agendamento = $stmt->fetch();
        
        if ($agendamento && $agendamento['usuario_id'] == $_SESSION['usuario']['id']) {
            $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: agendamento.php?sucesso=Agendamento cancelado com sucesso");
            exit();
        } else {
            header("Location: agendamento.php?erro=Agendamento não encontrado");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: agendamento.php?erro=Erro ao cancelar agendamento");
        exit();
    }
} else {
    header("Location: agendamento.php");
    exit();
}
?>