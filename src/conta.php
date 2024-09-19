<?php

include_once 'db.php';

class Conta {

    private $conn;

    function __construct($conn)
    {
        $this->conn = $conn;
    }

    function getAll() {
        $sql = "SELECT 
            c.*,
            b.descricao banco
        FROM conta c
        inner join bancos b on (
            c.id_banco=b.id
        )";
        $result = $this->conn->query($sql);

        $data = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    function getById($id) {
        $sql = "SELECT 
            c.*,
            b.descricao banco
        FROM conta c
        inner join bancos b on (
            c.id_banco=b.id
        )
        WHERE id = ?";
        $stm = $this->conn->prepare($sql);

        $stm->bind_param('i', $id);
        $stm->execute();

        $result = $stm->get_result();

        $data = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    function deleteById($id) {
        $sql = "DELETE FROM conta WHERE id = ?";
        $stm = $this->conn->prepare($sql);

        $stm->bind_param('i', $id);
        $stm->execute();

        if ($stm->affected_rows > 0) {
            return ['status' => 'ok', 'msg' => 'Registro excluído com sucesso'];
        }

        return ['status' => 'error', 'msg' => 'Falha ao excluir registro'];
    }

    function updateById($id, $data) {
        $sql = "UPDATE conta SET 
            descricao = ?,
            id_banco = ?,
            saldo = ?
        WHERE id = ?";

        $stm = $this->conn->prepare($sql);

        $stm->bind_param(
            'sifi', 
            $data['descricao'], 
            $data['banco'], 
            $data['saldo'], 
            $id 
        );
        $stm->execute();

        if ($stm->affected_rows > 0) {
            return ['status' => 'ok', 'msg' => 'Registro atualizado com sucesso'];
        }

        return ['status' => 'error', 'msg' => 'Falha ao atualizar registro'];
    }

    function create($data) {
        $sql = "INSERT INTO conta ( 
          descricao,
          id_banco,
          saldo 
        ) VALUES (?, ?, ?)";

        $stm = $this->conn->prepare($sql);

        $stm->bind_param(
            'sif', 
            $data['descricao'], 
            $data['banco'], 
            $data['saldo']
        );
        $stm->execute();

        if ($stm->affected_rows > 0) {
            return ['status' => 'ok', 'msg' => 'Registro criado com sucesso'];
        }

        return ['status' => 'error', 'msg' => 'Falha ao criar registro'];
    }
}

$allowed_methods = [
    'GET',
    'POST',
    'PUT',
    'DELETE'
];

if (!in_array($_SERVER['REQUEST_METHOD'], $allowed_methods)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode( [
        'status' => 'error',
        'msg' => 'Invalid Request'
    ] );
}

$conta = new Conta($conn);

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    echo json_encode($conta->deleteById($_GET['id']));
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($conta->updateById($_GET['id'], $data));
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($conta->create($data));
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'pessoa/cadastro')) {
        echo json_encode($conta->getById($_GET['id']));
        return;
    }

    echo json_encode($conta->getAll());
    return;
}