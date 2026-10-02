<?php
class Entrada
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function registrar($id_produto, $qtd, $valor, $data)
    {
        $sql = "INSERT INTO entradas (id_produto, quantidade, valor_compra, data_entrada) 
                VALUES (:produto, :qtd, :valor, :data)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'produto' => $id_produto,
            'qtd'     => $qtd,
            'valor'   => $valor,
            'data'    => $data
        ]);
    }

    public function listarTodas()
    {
        $sql = "SELECT e.*, p.nome as nome_produto FROM entradas e 
                JOIN produtos p ON e.id_produto = p.id_produto";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
