<?php
class Aluno {
    private $nome;
    private $matricula;
    private $curso;

    public function __construct($nome, $matricula, $curso) {
        $this->nome = $nome;
        $this->matricula = $matricula;
        $this->curso = $curso;
    }
    public function getNome() {
        return $this->nome;
    }
    public function getMatricula() {
        return $this->matricula;
    }
    public function getCurso() {
        return $this->curso;
    }
    public function setNome($nome) {
        $this->nome = $nome;
    }
    public function setCurso($curso) {
        $this->curso = $curso;
    }
}

class CadastroAlunos {
    private $alunos = [];
    private $arquivo = 'alunos.txt';

    public function __construct() {
        if (file_exists($this->arquivo)) {
            $data = file_get_contents($this->arquivo);
            if ($data) {
                $alunosArray = unserialize($data);
                if (is_array($alunosArray)) {
                    $this->alunos = $alunosArray;
                }
            }
        }
    }

    public function cadastrarAluno($aluno) {
        foreach ($this->alunos as $a) {
            if ($a->getMatricula() == $aluno->getMatricula()) {
                return "Erro: Matrícula já existe!";
            }
        }
        $this->alunos[] = $aluno;
        $this->salvarArquivo();
        return "Aluno cadastrado com sucesso!";
    }

    public function listarAlunos() {
        return $this->alunos;
    }

    public function removerAluno($matricula) {
        foreach ($this->alunos as $key => $aluno) {
            if ($aluno->getMatricula() == $matricula) {
                unset($this->alunos[$key]);
                $this->alunos = array_values($this->alunos);
                $this->salvarArquivo();
                return "Aluno removido com sucesso!";
            }
        }
        return "Erro: Aluno não encontrado!";
    }

    public function buscarAlunoPorMatricula($matricula) {
        foreach ($this->alunos as $aluno) {
            if ($aluno->getMatricula() == $matricula) {
                return $aluno;
            }
        }
        return null;
    }

    public function editarAluno($matricula, $novoNome, $novoCurso) {
        foreach ($this->alunos as $aluno) {
            if ($aluno->getMatricula() == $matricula) {
                $aluno->setNome($novoNome);
                $aluno->setCurso($novoCurso);
                $this->salvarArquivo();
                return "Aluno editado com sucesso!";
            }
        }
        return "Erro: Aluno não encontrado!";
    }

    private function salvarArquivo() {
        file_put_contents($this->arquivo, serialize($this->alunos));
    }
}

$cadastro = new CadastroAlunos();
$mensagem = "";
$alunoParaEditar = null;

if (isset($_POST['acao'])) {
    if ($_POST['acao'] === 'cadastrar') {
        $nome = trim($_POST['nome']);
        $matricula = trim($_POST['matricula']);
        $curso = trim($_POST['curso']);
        if ($nome && $matricula && $curso) {
            $aluno = new Aluno($nome, $matricula, $curso);
            $mensagem = $cadastro->cadastrarAluno($aluno);
        } else {
            $mensagem = "Preencha todos os campos.";
        }
    } elseif ($_POST['acao'] === 'remover') {
        $matricula = trim($_POST['matricula']);
        $mensagem = $cadastro->removerAluno($matricula);
    } elseif ($_POST['acao'] === 'salvar_edicao') {
        $matricula = trim($_POST['matricula']);
        $nome = trim($_POST['nome']);
        $curso = trim($_POST['curso']);
        $mensagem = $cadastro->editarAluno($matricula, $nome, $curso);
    }
}

if (isset($_GET['acao']) && $_GET['acao'] === 'editar' && isset($_GET['matricula'])) {
    $alunoParaEditar = $cadastro->buscarAlunoPorMatricula($_GET['matricula']);
}

$listaAlunos = $cadastro->listarAlunos();

$cursosDisponiveis = [
    "ADMINISTRAÇÃO",
    "ARQUITETURA E URBANISMO",
    "BIOMEDICINA",
    "CIÊNCIAS CONTÁBEIS",
    "DIREITO",
    "ENFERMAGEM",
    "ENGENHARIA AGRONÔMICA",
    "ENGENHARIA CIVIL",
    "ENGENHARIA DE SOFTWARE",
    "ENGENHARIA ELÉTRICA",
    "ENGENHARIA MECÂNICA",
    "FARMÁCIA",
    "FISIOTERAPIA",
    "MEDICINA",
    "MEDICINA VETERINÁRIA",
    "NUTRIÇÃO",
    "ODONTOLOGIA",
    "PSICOLOGIA",
    "PUBLICIDADE E PROPAGANDA"
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Cadastro de Alunos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link 
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" 
      rel="stylesheet"
    >
    <style>
        * {
            margin: 0; 
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #121212;
            color: #ffffff;
            padding: 20px;
        }
        .titulo-principal {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 1.8rem;
        }
        .container {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .form-container, .table-container {
            background: #1E1E1E;
            border-radius: 8px;
            padding: 20px;
            width: 45%;
        }
        .form-container h2, .table-container h2 {
            margin-bottom: 15px;
            font-weight: 500;
            color: #ffffff;
        }
        label {
            display: block;
            margin: 8px 0 5px 0;
            font-weight: 500;
            color: #cccccc;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #333333;
            border-radius: 4px;
            background: #2B2B2B;
            color: #ffffff;
        }
        input[type="submit"] {
            background: #5700ff;
            color: #ffffff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        input[type="submit"]:hover {
            background: #7b33ff;
        }
        .mensagem {
            background: #2e7d32;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #333333;
        }
        th {
            background: #5700ff;
            color: #ffffff;
            font-weight: 500;
        }
        .remover-form, .editar-link {
            display: inline-block;
            margin: 0;
        }
        .remover-button {
            background: none;
            border: none;
            cursor: pointer;
            color: #ff4d4d;
            font-size: 18px;
            font-weight: 600;
            margin-right: 8px;
        }
        .remover-button:hover {
            color: #ff1c1c;
        }
        .editar-link a {
            text-decoration: none;
            background: #5700ff;
            color: #ffffff;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
        }
        .editar-link a:hover {
            background: #7b33ff;
        }
        .no-data {
            text-align: center;
            color: #aaaaaa;
        }

        /* ESTILOS PARA O MODAL */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .modal-content {
            background: #1E1E1E;
            border-radius: 8px;
            padding: 20px;
            width: 400px;
            position: relative;
        }
        .modal-content h2 {
            margin-bottom: 15px;
        }
        .close-modal {
            display: inline-block;
            margin-top: 10px;
            background: #5700ff;
            padding: 8px 12px;
            border-radius: 4px;
            color: #ffffff;
            text-decoration: none;
            font-weight: 500;
        }
        .close-modal:hover {
            background: #7b33ff;
        }
    </style>
</head>
<body>
    <h1 class="titulo-principal">Sistema de Cadastro de Alunos em PHP</h1>

    <!-- MODAL DE EDIÇÃO -->
    <?php if ($alunoParaEditar): ?>
        <div class="modal">
            <div class="modal-content">
                <h2>Editar Aluno</h2>
                <form method="post">
                    <input type="hidden" name="acao" value="salvar_edicao">
                    <input type="hidden" name="matricula" value="<?php echo $alunoParaEditar->getMatricula(); ?>">
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?php echo $alunoParaEditar->getNome(); ?>" required>
                    <label>Curso</label>
                    <select name="curso" required>
                        <option value="">Selecione um curso</option>
                        <?php foreach($cursosDisponiveis as $cursoItem): ?>
                            <option value="<?php echo $cursoItem; ?>"
                                <?php if($alunoParaEditar->getCurso() === $cursoItem) echo 'selected'; ?>>
                                <?php echo $cursoItem; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Salvar Alterações">
                </form>
                <!-- Link para fechar o modal (retorna para index.php sem parâmetros) -->
                <a class="close-modal" href="exercicio1.php">Fechar</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="form-container">
            <?php if ($mensagem) { ?>
                <div class="mensagem"><?php echo $mensagem; ?></div>
            <?php } ?>
            <h2>Cadastrar Aluno</h2>
            <form method="post">
                <input type="hidden" name="acao" value="cadastrar">
                <label>Nome</label>
                <input type="text" name="nome" required>
                <label>Matrícula</label>
                <input type="text" name="matricula" required>
                <label>Curso</label>
                <select name="curso" required>
                    <option value="">Selecione um curso</option>
                    <?php foreach($cursosDisponiveis as $cursoItem): ?>
                        <option value="<?php echo $cursoItem; ?>"><?php echo $cursoItem; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Cadastrar">
            </form>
        </div>
        <div class="table-container">
            <h2>Alunos Cadastrados</h2>
            <table>
                <tr>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>Curso</th>
                    <th>Ações</th>
                </tr>
                <?php if (!empty($listaAlunos)) { 
                    foreach($listaAlunos as $aluno) { ?>
                    <tr>
                        <td><?php echo $aluno->getNome(); ?></td>
                        <td><?php echo $aluno->getMatricula(); ?></td>
                        <td><?php echo $aluno->getCurso(); ?></td>
                        <td>
                            <form method="post" class="remover-form" onsubmit="return confirm('Deseja remover este aluno?');">
                                <input type="hidden" name="acao" value="remover">
                                <input type="hidden" name="matricula" value="<?php echo $aluno->getMatricula(); ?>">
                                <button type="submit" class="remover-button">&times;</button>
                            </form>
                            <span class="editar-link">
                                <a href="?acao=editar&matricula=<?php echo $aluno->getMatricula(); ?>">Editar</a>
                            </span>
                        </td>
                    </tr>
                <?php } } else { ?>
                    <tr>
                        <td colspan="4" class="no-data">Nenhum aluno cadastrado.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
