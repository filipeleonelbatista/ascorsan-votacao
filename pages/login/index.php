<?php

require '../../components/header.php';
require '../../connection.php';

$connection = new Connection();

$error_msg = '';

$url = $_SERVER['REQUEST_URI'];

$url_components = parse_url($url);

$url_query = array_key_exists('query', $url_components) ? $url_components['query'] : false;
if ($url_query) {
    parse_str($url_query, $params);

    $activation_code = array_key_exists('activation_code', $params) ? $params['activation_code'] : false;

    if ($activation_code) {
        $result = $connection->verifyUserByCode($activation_code);
        if (sizeof($result) > 0) {
            $unlock_user = $connection->unlockUser($result[0]['email'], $result[0]['password'], $activation_code);
            if ($unlock_user) {
                $_POST['email'] = $result[0]['email'];
                $error_msg = "Login liberado";
            }
        }
    }
}

if (isset($_POST['entrar'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $isVotationTime = $connection->isVotationTime();
    if ($isVotationTime['status'] == 1) {
        $result = $connection->auth($email, $password);

        if (sizeof($result) > 0) {
            $cpf = $result[0]['cpf'];
            $matricula = $result[0]['matricula'];


            if ($result[0]['is_active']) {
                $isVoted = $connection->checkVote($cpf, $matricula);

                if ($isVoted) {
                    header("location: ../../pages/voto/index.php?data=" . $isVoted);
                } else {
                    $data = base64_encode(json_encode($result[0]));
                    header("location: ../../pages/votacao/index.php?data=" . $data);
                }
            } else {
                $url_activation = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                $activation_url = str_replace("index.php", "index.php?activation_code=" . $result[0]['unique_id'] . "", $url_activation);

                $ascorsan_email = "ascorsan@ascorsan.com.br";

                $message = "<b>Confirmação de acesso à votação no site Ascorsan.com.br</b>\n";

                $message .= "<hr>\n";
                $message .= "<p>Você solicitou acesso ao sistema de votação da ascorsan.</p>\n";
                $message .= "<p>Para liberar seu acesso basta clicar no link a baixo.</p>\n";
                $message .= "<p>Ou copie e cole a url a baixo.</p>\n";
                $message .= "<hr>\n";
                $message .= "<p>" . $activation_url . "</p>\n";
                $message .= "<hr>\n";
                $message .= "\n<b>Email automatico enviado pelo Sistema de votação da Ascorsan</b>\n";

                $to = $email;
                $subject = "Confirmação de acesso à votação no site Ascorsan.com.br ";
                $headers = 'From: ' . $ascorsan_email . "\r\n" .
                    'Reply-To: ' . $ascorsan_email . "\r\n";

                $sent = mail($to, $subject, strip_tags($message), $headers);

                if ($sent) {
                    $error_msg = 'Usuario não está liberado. Verifique seu email com o link de ativação.';
                } else {
                    $error_msg = "Houve um problema ao enviar email. Tente novamente mais tarde!";
                }
            }
        } else {
            $error_msg = 'Usuario ou senha inválidos.';
        }
    } else {
        if($isVotationTime['is_before']){
            $error_msg = 'A votação ainda não foi aberta.';
        }else{
            if($isVotationTime['is_after']){
                $error_msg = 'A votação foi encerrada.';
            }else{                
                $error_msg = 'Houve um problema, contate o administrador do sistema.';
            }
        }
        
    }
}

?>

<div class="content">
    <section id="cadastrar">
        <a href="https://ascorsan.com.br">
            <img src="../../assets/images/logo.png" alt="ascorsan" />
        </a>
        <div class="container">
            <p id="error_msg" style="display: <?php echo $error_msg === '' ? 'none' : 'block'; ?>"><?php echo $error_msg; ?></p>
            <form method="post" action="">
                <div style="display: flex; flex-direction: column-reverse;">
                    <input autocomplete="off" required tabindex="1" id="email" name="email" type="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
                    <label for="email">Email</label>
                </div>
                <div style="display: flex; flex-direction: column-reverse;">
                    <input autocomplete="off" onfocus="handleRemoveErrorMessage()" required tabindex="2" id="password" name="password" type="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" />
                    <label for="password">Senha</label>
                </div>
                <button tabindex="3" type="submit" name="entrar">Entrar</button>
                <button tabindex="4" type="button" onClick="handleNavigateRegistration();" name="cadastrar">Cadastrar</button>
            </form>
        </div>
        <p style="color: #FFF; font-weight: normal; text-align: center"><?php echo date("Y"); ?> © Ascorsan<br />Associação de Servidores da Corsan</p>
    </section>
</div>

<script>
    function handleNavigateRegistration() {
        const url = window.location.href.replace("index.php", "cadastrar.php");
        window.open(url, "_self")
    }

    function handleRemoveErrorMessage() {
        document.getElementById("error_msg").style.display = 'none';
    }
</script>

<?php
require '../../components/footer.php';
