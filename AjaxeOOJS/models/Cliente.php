<?php
require_once __DIR__ . '/../config/database.php';

class Cliente implements JsonSerializable {
    private $id;
    private $nome;
    private $telefone;

    public function __construct($nome = null, $telefone = null, $id = null) {
        $this->id = $id;
        $this->setNome($nome);
        $this->setTelefone($telefone);
    }

    public function getId() { return $this->id; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) {
        if ($nome !== null && strlen(trim($nome)) < 2) {
            throw new Exception("Nome inválido: mínimo de 2 caracteres.");
        }
        $this->nome = $nome !== null ? trim($nome) : null;
    }

    public function getTelefone() { return $this->telefone; }
    public function setTelefone($telefone) {
        if ($telefone !== null && strlen(trim($telefone)) < 8) {
            throw new Exception("Telefone inválido: informe pelo menos 8 dígitos.");
        }
        $this->telefone = $telefone !== null ? trim($telefone) : null;
    }
    
    public function salvar() {
        $db = Database::getConexao();
        if ($this->id === null) {
            $stmt = $db->prepare("INSERT INTO clientes (nome, telefone) VALUES (:nome, :telefone)");
            $stmt->execute([':nome' => $this->nome, ':telefone' => $this->telefone]);
            $this->id = (int)$db->lastInsertId();
            return true;
        } else {
            $stmt = $db->prepare("UPDATE clientes SET nome = :nome, telefone = :telefone WHERE id = :id");
            return $stmt->execute([':nome' => $this->nome, ':telefone' => $this->telefone, ':id' => $this->id]);
        }
    }


    public function deletar() {
        if ($this->id === null) {
            throw new Exception("Não é possível excluir um cliente sem ID.");
        }
        $db = Database::getConexao();
        $stmt = $db->prepare("DELETE FROM clientes WHERE id = :id");
        $sucesso = $stmt->execute([':id' => $this->id]);
        if ($sucesso) {
            $this->id = null;
        }
        return $sucesso;
    }

    public static function todos() {
        $db = Database::getConexao();
        $stmt = $db->query("SELECT * FROM clientes ORDER BY id DESC");
        $linhas = $stmt->fetchAll();

        $lista = [];
        foreach ($linhas as $linha) {
            $lista[] = new Cliente($linha['nome'], $linha['telefone'], (int)$linha['id']);
        }
        return $lista;
    }

    public static function buscarPorId($id) {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT * FROM clientes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch();
        if ($dados) {
            return new Cliente($dados['nome'], $dados['telefone'], (int)$dados['id']);
        }
        return null;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'telefone' => $this->telefone,
     
        ];
    }
}