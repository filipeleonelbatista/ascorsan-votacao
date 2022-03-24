<?php

require '../../components/header.php';
require '../../connection.php';

$connection = new Connection();

$error_msg = '';

if (isset($_POST['entrar'])) {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    $result = $connection->authConfig($usuario, $senha);

    $data = base64_encode(json_encode($result));

    if(sizeof($result) > 0){
        header("location: ../../pages/apuracao/apuracao.php?data=" . $data);
    }else{        
        $error_msg = 'Usuário ou senha inválidos';
    }    
}

?>

<div class="content">
    <section id="cadastrar">
        <a href="https://ascorsan.com.br">
            <img src="../../assets/images/logo.png" alt="ascorsan" />
        </a>
        <div class="container">
            <h3> Digite Seu usuario para entrar </h3>
            <p id="error_msg" style="display: <?php echo $error_msg === '' ? 'none' : 'block'; ?>"><?php echo $error_msg; ?></p>
            <form method="post" action="">
                <div style="display: flex; flex-direction: column-reverse;">
                    <input required tabindex="2" id="usuario" name="usuario" type="text" value="<?php echo isset($_POST['usuario']) ? $_POST['usuario'] : ''; ?>" />
                    <label for="usuario">Usuário</label>
                </div>
            <div style="display: flex; flex-direction: column-reverse;">
                <input onfocus="handleRemoveErrorMessage()" required tabindex="1" id="senha" name="senha" type="password" value="<?php echo isset($_POST['senha']) ? $_POST['senha'] : ''; ?>" />
                <label for="senha">Senha</label>
            </div>

                <button tabindex="3" type="submit" name="entrar">Entrar</button>
            </form>
        </div>
        <p style="color: #FFF; font-weight: normal; text-align: center"><?php echo date("Y"); ?> © Ascorsan<br/>Associação de Servidores da Corsan</p>
    </section>
</div>

<script>
    function handleRemoveErrorMessage(){            
            document.getElementById("error_msg").style.display = 'none';
        }
</script>

<?php
require '../../components/footer.php';
