<?php
session_start();

// ---------------------
// Função para logout
// ---------------------
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ---------------------
// Definição das classes
// ---------------------
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
}

class CadastroAlunos {
    private $alunos = [];
    
    // Método para cadastrar um aluno com validação de matrícula única
    public function cadastrarAluno(Aluno $aluno) {
        foreach ($this->alunos as $a) {
            if ($a->getMatricula() === $aluno->getMatricula()) {
                return false;
            }
        }
        $this->alunos[] = $aluno;
        return true;
    }
    
    public function listarAlunos() {
        return $this->alunos;
    }
}

// ---------------------
// Lógica de Login
// ---------------------
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    $loginMsg = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_usuario'], $_POST['login_senha'])) {
        $usuario = $_POST['login_usuario'];
        $senha = $_POST['login_senha'];
        // Usuário e senha fixos para exemplo: admin/12345
        if ($usuario === 'admin' && $senha === '12345') {
            $_SESSION['logado'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $loginMsg = "Usuário ou senha incorretos!";
        }
    }
    // Exibe o formulário de login
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <title>Login - Cadastro de Alunos</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f2f2f2; }
            .login-container { width: 300px; margin: 100px auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            input[type=text], input[type=password] { width: 100%; padding: 8px; margin: 5px 0 10px; border: 1px solid #ccc; border-radius: 4px; }
            input[type=submit] { background-color: #4CAF50; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
            input[type=submit]:hover { background-color: #45a049; }
            .error { color: red; text-align: center; margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>Login</h2>
            <?php if(!empty($loginMsg)) { echo '<p class="error">'.$loginMsg.'</p>'; } ?>
            <form method="post" action="">
                <label for="login_usuario">Usuário:</label>
                <input type="text" name="login_usuario" id="login_usuario" required>
                <label for="login_senha">Senha:</label>
                <input type="password" name="login_senha" id="login_senha" required>
                <input type="submit" value="Entrar">
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// ---------------------
// Lógica de Cadastro de Alunos
// ---------------------
// Recupera o objeto de cadastro armazenado na sessão ou cria um novo
if (isset($_SESSION['cadastro'])) {
    $cadastro = unserialize($_SESSION['cadastro']);
} else {
    $cadastro = new CadastroAlunos();
}

$cadastroMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'], $_POST['matricula'], $_POST['curso'])) {
    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $curso = $_POST['curso'];
    
    $aluno = new Aluno($nome, $matricula, $curso);
    
    if ($cadastro->cadastrarAluno($aluno)) {
        $cadastroMsg = "Aluno cadastrado com sucesso!";
    } else {
        $cadastroMsg = "Erro: Matrícula já cadastrada!";
    }
    // Atualiza o objeto de cadastro na sessão
    $_SESSION['cadastro'] = serialize($cadastro);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Alunos</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; margin: 0; padding: 0; }
        .container { width: 80%; margin: 20px auto; padding: 20px; }
        h1, h2 { text-align: center; }
        form { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        label { display: block; margin-top: 10px; }
        input[type="text"] { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        input[type="submit"] { margin-top: 15px; background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #0069d9; }
        .msg { padding: 10px; margin-bottom: 10px; border-radius: 4px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .lista ul { list-style-type: none; padding: 0; }
        .lista li { background: #fff; margin-bottom: 10px; padding: 10px; border-radius: 4px; box-shadow: 0 0 5px rgba(0,0,0,0.05); }
        .logout { text-align: right; margin-bottom: 10px; }
        .logout a { text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="?action=logout">Sair</a>
        </div>
        <h1>Cadastro de Alunos</h1>
        
        <?php if(!empty($cadastroMsg)): ?>
            <div class="msg <?php echo strpos($cadastroMsg, 'sucesso') !== false ? 'success' : 'error'; ?>">
                <?php echo $cadastroMsg; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulário de Cadastro de Alunos -->
        <form method="post" action="">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>
            
            <label for="matricula">Matrícula:</label>
            <input type="text" name="matricula" id="matricula" required>
            
            <label for="curso">Curso:</label>
            <input type="text" name="curso" id="curso" required>
            
            <input type="submit" value="Cadastrar">
        </form>
        
        <!-- Lista de Alunos Cadastrados -->
        <div class="lista">
            <h2>Alunos Cadastrados:</h2>
            <?php 
            $alunos = $cadastro->listarAlunos();
            if(empty($alunos)){
                echo "<p>Nenhum aluno cadastrado.</p>";
            } else {
                echo "<ul>";
                foreach ($alunos as $aluno) {
                    echo "<li><strong>Nome:</strong> " . $aluno->getNome() . " - <strong>Matrícula:</strong> " . $aluno->getMatricula() . " - <strong>Curso:</strong> " . $aluno->getCurso() . "</li>";
                }
                echo "</ul>";
            }
            ?>
        </div>
    </div>
</body>
</html>
