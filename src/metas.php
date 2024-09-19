<?php

include_once 'db.php';

class Metas {

    private $conn;

    function __construct($conn)
    {
        $this->conn = $conn;
    }

    function getAll() {
        $sql = "SELECT 
            m.*,
            DATE_FORMAT(m.data, '%d/%m/%Y') \"data\"
        FROM metas m ";
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
            m.*,
            DATE_FORMAT(m.data, '%d/%m/%Y') \"data\"
        FROM metas m 
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
        $sql = "DELETE FROM metas WHERE id = ?";
        $stm = $this->conn->prepare($sql);

        $stm->bind_param('i', $id);
        $stm->execute();

        if ($stm->affected_rows > 0) {
            return ['status' => 'ok', 'msg' => 'Registro excluído com sucesso'];
        }

        return ['status' => 'error', 'msg' => 'Falha ao excluir registro'];
    }

    function updateById($id, $data) {
        $sql = "UPDATE metas SET 
            \"data\" = ?,
            descricao = ?,
            valor = ?
        WHERE id = ?";

        $stm = $this->conn->prepare($sql);

        $stm->bind_param(
           'ssfi',  
            $data['data'], 
            $data['descricao'], 
            $data['valor'], 
            $id 
        );
        $stm->execute();

        if ($stm->affected_rows > 0) {
            return ['status' => 'ok', 'msg' => 'Registro atualizado com sucesso'];
        }

        return ['status' => 'error', 'msg' => 'Falha ao atualizar registro'];
    }

    function create($data) {
        $sql = "INSERT INTO metas ( 
            \"data\" = ?,
            descricao = ?,
            valor = ?
        ) VALUES (?, ?, ?)";

        $stm = $this->conn->prepare($sql);

        $stm->bind_param(
            'ssf',  
            $data['data'], 
            $data['descricao'], 
            $data['valor'], 
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

$metas = new Metas($conn);

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    echo json_encode($metas->deleteById($_GET['id']));
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($metas->updateById($_GET['id'], $data));
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($metas->create($data));
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'pessoa/cadastro')) {
        echo json_encode($metas->getById($_GET['id']));
        return;
    }

    echo json_encode($metas->getAll());
    return;
}