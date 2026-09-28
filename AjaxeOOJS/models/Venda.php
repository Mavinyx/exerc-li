<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Produto.php';

class Venda implements JsonSerializable{
    private $id;
    private $clienteId;
    private $produtoId;
    private $quantidade;
    private $valorTotal;
    private $dataVenda;

    public function __construct($clienteId = null, $produtoId = null, $quantidade = null, $valorTotal = null, $dataVenda = null, $id = null) {
        $this->id = $id;
        $this->clienteId = $clienteId !== null ? (int)$clienteId : null;
        $this->produtoId = $produtoId !== null ? (int)$produtoId : null;
        $this->setQuantidade($quantidade);
        $this->valorTotal = $valorTotal !== null ? (float)$valorTotal : null;
        $this->dataVenda = $dataVenda;
    }

    public function getId() { return $this->id; }
    public function getClienteId() { return $this->clienteId; }
    public function getProdutoId() { return $this->produtoId; }
    public function getValorTotal() { return $this->valorTotal; }
    public function getDataVenda() { return $this->dataVenda; }

    public function getQuantidade() { return $this->quantidade; }
    public function setQuantidade($quantidade) {
        if ($quantidade !== null && (int)$quantidade <= 0) {
            throw new Exception("Quantidade vendida deve ser no mínimo 1.");
        }
        $this->quantidade = $quantidade !== null ? (int)$quantidade : null;
    }

    public function registrar() {
        $db = Database::getConexao();

        $produto = Produto::buscarPorId($this->produtoId);
        if (!$produto) {
            throw new Exception("Produto não encontrado.");
        }

        if ($produto->getEstoque() < $this->quantidade) {
            throw new Exception("Estoque insuficiente para esta venda.");
        }

        $this->valorTotal = $produto->getPreco() * $this->quantidade;

        $db->beginTransaction();
        try {
            $produto->setEstoque($produto->getEstoque() - $this->quantidade);
            $produto->salvar();

            $stmt = $db->prepare("INSERT INTO vendas (cliente_id, produto_id, quantidade, valor_total) VALUES (:cid, :pid, :qtd, :total)");
            $stmt->execute([
                ':cid' => $this->clienteId,
                ':pid' => $this->produtoId,
                ':qtd' => $this->quantidade,
                ':total' => $this->valorTotal
            ]);

            $this->id = (int)$db->lastInsertId();
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function deletar() {
        if ($this->id === null) {
            throw new Exception("Não é possível estornar uma venda sem ID.");
        }

        $db = Database::getConexao();
        $db->beginTransaction();
        try {
            $produto = Produto::buscarPorId($this->produtoId);
            if ($produto) {
                $produto->setEstoque($produto->getEstoque() + $this->quantidade);
                $produto->salvar();
            }

            $stmt = $db->prepare("DELETE FROM vendas WHERE id = :id");
            $stmt->execute([':id' => $this->id]);

            $db->commit();
            $this->id = null;
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function todos() {
        $db = Database::getConexao();
        $stmt = $db->query("SELECT * FROM vendas ORDER BY id DESC");
        $linhas = $stmt->fetchAll();

        $lista = [];
        foreach ($linhas as $linha) {
            $lista[] = new Venda(
                (int)$linha['cliente_id'],
                (int)$linha['produto_id'],
                (int)$linha['quantidade'],
                (float)$linha['valor_total'],
                $linha['data_venda'],
                (int)$linha['id']
            );
        }
        return $lista;
    }

    public static function buscarPorId($id) {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT * FROM vendas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch();
        if ($dados) {
            return new Venda(
                (int)$dados['cliente_id'],
                (int)$dados['produto_id'],
                (int)$dados['quantidade'],
                (float)$dados['valor_total'],
                $dados['data_venda'],
                (int)$dados['id']
            );
        }
        return null;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'=> $this->id,
            'clienteId'=>$this->clienteId,
            'produtoId'=>$this->produtoId,
            'quantidade'=>$this->quantidade,
            'valorTotal'=>$this->valorTotal,
            'dataVenda'=>$this->dataVenda
        ];
    }
}