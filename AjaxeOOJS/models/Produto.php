<?php
require_once __DIR__ . '/../config/database.php';

class Produto {
    private $id;
    private $nome;
    private $preco;
    private $estoque;

    public function __construct($nome = null, $preco = null, $estoque = null, $id = null) {
        $this->id = $id;
        $this->setNome($nome);
        $this->setPreco($preco);
        $this->setEstoque($estoque);
    }

    public function getId() { return $this->id; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) {
        if ($nome !== null && strlen(trim($nome)) < 2) {
            throw new Exception("Nome do produto inválido.");
        }
        $this->nome = $nome !== null ? trim($nome) : null;
    }

    public function getPreco() { return $this->preco; }
    public function setPreco($preco) {
        if ($preco !== null && (float)$preco <= 0) {
            throw new Exception("Preço deve ser maior que zero.");
        }
        $this->preco = $preco !== null ? (float)$preco : null;
    }

    public function getEstoque() { return $this->estoque; }
    public function setEstoque($estoque) {
        if ($estoque !== null && (int)$estoque < 0) {
            throw new Exception("Estoque não pode ser negativo.");
        }
        $this->estoque = $estoque !== null ? (int)$estoque : null;
    }

    public function salvar() {
        $db = Database::getConexao();
        if ($this->id === null) {
            $stmt = $db->prepare("INSERT INTO produtos (nome, preco, estoque) VALUES (:nome, :preco, :estoque)");
            $stmt->execute([':nome' => $this->nome, ':preco' => $this->preco, ':estoque' => $this->estoque]);
            $this->id = (int)$db->lastInsertId();
            return true;
        } else {
            $stmt = $db->prepare("UPDATE produtos SET nome = :nome, preco = :preco, estoque = :estoque WHERE id = :id");
            return $stmt->execute([
                ':nome' => $this->nome,
                ':preco' => $this->preco,
                ':estoque' => $this->estoque,
                ':id' => $this->id
            ]);
        }
    }

    public function deletar() {
        if ($this->id === null) {
            throw new Exception("Não é possível excluir um produto sem ID.");
        }
        $db = Database::getConexao();
        $stmt = $db->prepare("DELETE FROM produtos WHERE id = :id");
        $sucesso = $stmt->execute([':id' => $this->id]);
        if ($sucesso) {
            $this->id = null;
        }
        return $sucesso;
    }

    public static function todos() {
        $db = Database::getConexao();
        $stmt = $db->query("SELECT * FROM produtos ORDER BY id DESC");
        $linhas = $stmt->fetchAll();

        $lista = [];
        foreach ($linhas as $linha) {
            $lista[] = new Produto($linha['nome'], $linha['preco'], $linha['estoque'], (int)$linha['id']);
        }
        return $lista;
    }

    public static function buscarPorId($id) {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch();
        if ($dados) {
            return new Produto($dados['nome'], $dados['preco'], $dados['estoque'], (int)$dados['id']);
        }
        return null;
    }
}