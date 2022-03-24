<?php

require '../../components/header.php';
require '../../connection.php';

$connection = new Connection();

$error_msg = '';

if (isset($_POST['entrar'])) {
    $cpf = $_POST['cpf'];
    $matricula = $_POST['matricula'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];
    
    $checkIfIsAssociated = $connection->checkAssociate($cpf, $matricula);
    
    if(sizeof($checkIfIsAssociated) > 0){
        if($password == $passwordConfirm){
            $checkIfIsRegistred = $connection->auth($email, $password);
            if(sizeof($checkIfIsRegistred) > 0){            
                $error_msg = "Usuário ja está registrado";
            }else{
                $uid_registration = uniqid();
                $is_active = false;
                $registerUser = $connection->registerUser($email, $password, $cpf, $matricula, $uid_registration, $is_active);
                if($registerUser){
                    if($registerUser > 0){   
                        $url_activation = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        $activation_url = str_replace("cadastrar.php","index.php?activation_code=" . $uid_registration . "", $url_activation);
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
                    
                        if ($sent){
                            $error_msg = "Foi enviado um email para " . $email . " com o link para liberar o acesso";
                        }else{
                            $error_msg = "Houve um problema ao enviar email. Tente novamente mais tarde!";
                        }
                    }else{
                        $error_msg = "Houve um problema ao tentar registrar os dados. Tente novamente mais tarde!";
                    }
                }else{
                    $error_msg = "Encontramos o CPF, Matricula ou email já cadastrados. Caso isso esteja errado contate a administração!";
                }
                
            }
        }else{
            $error_msg = "Senhas digitadas não coincidem";
        }
    }else{
        $error_msg = "Cpf e Matricula não pertencem a organização";
    }
}

?>

<div class="content">
    <section id="cadastrar">
        <a href="https://ascorsan.com.br">
            <img src="../../assets/images/logo.png" alt="ascorsan" />
        </a>
        <div class="container">
            <span>Digite seus dados para receber acesso para a votação.</span>
            <p id="error_msg" style="display: <?php echo $error_msg === '' ? 'none' : 'block'; ?>"><?php echo $error_msg; ?></p>
            <form method="post" action="">
                <div style="display: flex; flex-direction: column-reverse;">
                    <input onfocus="handleRemoveErrorMessage()" required tabindex="1" id="cpf" name="cpf" type="text" value="<?php echo isset($_POST['cpf']) ? $_POST['cpf'] : ''; ?>" />
                    <label for="cpf">CPF</label>
                </div>
                <div style="display: flex; flex-direction: column-reverse;">
                    <input onfocus="handleRemoveErrorMessage()" required tabindex="2" id="matricula" name="matricula" type="text" value="<?php echo isset($_POST['matricula']) ? $_POST['matricula'] : ''; ?>" />
                    <label for="matricula">Matricula</label>
                </div>
                <div style="display: flex; flex-direction: column-reverse;">
                    <input onfocus="handleRemoveErrorMessage()" required tabindex="3" id="email" name="email" type="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
                    <label for="email">Email</label>
                </div>
                <div style="display: flex; flex-direction: column-reverse;">
                    <input onfocus="handleRemoveErrorMessage()" required tabindex="4" id="password" name="password" type="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" />
                    <label for="password">Senha</label>
                </div>
                <div style="display: flex; flex-direction: column-reverse;">
                    <input onfocus="handleRemoveErrorMessage()" required tabindex="5" id="passwordConfirm" name="passwordConfirm" type="password" value="<?php echo isset($_POST['passwordConfirm']) ? $_POST['passwordConfirm'] : ''; ?>" />
                    <label for="passwordConfirm">Confirmar senha</label>
                </div>

                <button tabindex="6" type="submit" name="entrar">Cadastrar</button>
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
