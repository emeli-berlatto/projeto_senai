<?php

include_once 'db.php';

class Receita {

    private $conn;

    function __construct($conn)
    {
        $this->conn = $conn;
    }

    function getAll() {
        $sql = "SELECT 
            t.*,
            c.descricao categoria, 
            c2.descricao conta,
            DATE_FORMAT(t.data, '%d/%m/%Y') \"data\"
        FROM transacoes t
        inner join categoria c on (
            t.id_categoria=c.id
            and c.tipo='receita'
        )
        inner join conta c2 on (
            t.id_conta=c2.id
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
            t.*,
            c.descricao categoria, 
            c2.descricao conta,
            DATE_FORMAT(t.data, '%d/%m/%Y') data_formatada
        FROM transacoes t
        inner join categoria c on (
            t.id_categoria=c.id
            and c.tipo='receita'
        )
        inner join conta c2 on (
            t.id_conta=c2.id
        )
        WHERE t.id = ?";
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
        $sql = "DELETE FROM transacoes WHERE id = ?";
        $stm = $this->conn->prepare($sql);

        $stm->bind_param('i', $id);
        $stm->execute();

        if ($stm->affected_rows > 0) {
            return ['status' => 'ok', 'msg' => 'Registro excluído com sucesso'];
        }

        return ['status' => 'error', 'msg' => 'Falha ao excluir registro'];
    }

    function updateById($id, $data) {
        $sql = "UPDATE transacoes SET 
            valor = ?,
            efetuado = ?,
            data = ?,
            descricao = ?,
            id_categoria = ?,
            id_conta = ?
        WHERE id = ?";

        $stm = $this->conn->prepare($sql);

        $stm->bind_param(
           'dissiii', 
            $data['valor'], 
            $data['efetuado'], 
            $data['data'], 
            $data['descricao'], 
            $data['categoria'], 
            $data['conta'], 
            $id 
        );
        $stm->execute();

        if (!$stm->error) {
            return ['status' => 'ok', 'msg' => 'Registro atualizado com sucesso'];
        }

        return ['status' => 'error', 'msg' => 'Falha ao atualizar registro'];
    }

    function create($data) {
        $sql = "INSERT INTO transacoes ( 
            valor,
            efetuado,
            data,
            descricao,
            id_categoria,
            id_conta
        ) VALUES (?, ?, ?, ?, ?, ?)";

        $stm = $this->conn->prepare($sql);

        $valor = $data['valor'];
        $efetuado = !empty($data['efetuado']) && is_numeric($data['efetuado']) ? $data['efetuado'] : 1;
        $dataValue = $data['data'];
        $descricao = $data['descricao'];
        $id_categoria = $data['categoria'];
        $id_conta = $data['conta'];

        $stm->bind_param(
            'dissii', 
            $valor, 
            $efetuado, 
            $dataValue, 
            $descricao, 
            $id_categoria, 
            $id_conta
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

$receita = new Receita($conn);

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    echo json_encode($receita->deleteById($_GET['id']));
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($receita->updateById($_GET['id'], $data));
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($receita->create($data));
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'receita/cadastro')) {
        echo json_encode($receita->getById($_GET['id']));
        return;
    }

    echo json_encode($receita->getAll());
    return;
}