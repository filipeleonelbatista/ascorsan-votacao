<?php

require '../../components/header.php';
require '../../connection.php';

$connection = new Connection();

$error_msg = '';
$error_status = false;

$url = $_SERVER['REQUEST_URI'];
$url_components = parse_url($url);
parse_str($url_components['query'], $params);
$encoded_data_url = $params['data'];

$data = json_decode(base64_decode($encoded_data_url));

$isVoted = $connection->checkVote($data->cpf, $data->matricula);

if($isVoted){        
    header("location: ../../pages/voto/index.php?data=" . $isVoted);
}

$user = $connection->checkAssociate($data->cpf, $data->matricula);

$canditate_list = $connection->getCandidates($user[0]['cod_sup']);

if(sizeof($canditate_list) == 0){
    $error_msg = "Sua superintendencia não possui candidatos.";
}

if (isset($_POST['votar'])) {    
    if(!array_key_exists('radio', $_POST)){
        $error_msg = 'É obrigatório selecionar um candidato';
        $error_status = true;
    }
    if(!array_key_exists('termos', $_POST)){
        $error_msg = 'É necessário aceitar os termos';
        $error_status = true;
    }
    if(!$error_status){

        $radio = array_key_exists('radio', $_POST) ? $_POST['radio'] : "";
        $termos = array_key_exists('termos', $_POST) ? $_POST['termos'] : "";
        
        $tz_object = new DateTimeZone('Brazil/East');
        $datetime = new DateTime();
        $datetime->setTimezone($tz_object);
        $registration_date = $datetime->format('d/m/Y h:i:s');
        $uid_registration = uniqid();
        
        $insertResult = $connection->insertVote($radio, $termos, $data->matricula, $data->cpf, $registration_date, $uid_registration);

        if($insertResult){
            $result = array(
                "vote" => $radio,
                "terms" => $termos,
                "created_at" => $registration_date,
                "matricula" => $data->matricula,
                "cpf" => $data->cpf,
                "unique_id" => $uid_registration
            );
        
            $encoded_data = base64_encode(json_encode($result));
            
            header("location: ../../pages/voto/index.php?data=" . $encoded_data);
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
            <h2><?php print_r ($user[0]['nome']); ?></h2>
            <span><strong>Matricula: </strong><?php print_r ($user[0]['matricula']); ?></span>
            <span><strong>Superintendencia: </strong><?php print_r ($user[0]['superintendencia']); ?></span>
            <?php if(sizeof($canditate_list) > 0): ?>
            <?php endif; ?>
            <form method="post" action="">   
                <p id="error_msg" style="display: <?php echo $error_msg === '' ? 'none' : 'block'; ?>"><?php echo $error_msg; ?></p>
                <?php if(sizeof($canditate_list) > 0): ?>                    
                    <label for="termos" class=container-checkbox> 
                        Aceito o <a href="https://filipedev.ga" target="_blank">Regulamento Eleitoral</a>
                        <input type="checkbox" id="termos" name="termos">
                        <span class="checkmark"></span>
                    </label>
                    <h3>Selecione seu voto</h3>
                    <?php foreach($canditate_list as $candidate): ?>                       
                        <label for="<?php echo $candidate['matricula']; ?>" class="container-radio">
                            <?php echo $candidate['nome']; ?>
                            <input 
                                type="radio" 
                                id="<?php echo $candidate['matricula']; ?>" 
                                value="<?php echo $candidate['matricula']; ?>" 
                                name="radio">
                            <span class="checkmark-radio"></span>
                        </label>
                    <?php endforeach; ?>

                    <button tabindex="3" type="submit" name="votar">Votar</button>                     
                <?php endif; ?>
            </form>
        </div>
        <p style="color: #FFF; font-weight: normal; text-align: center"><?php echo date("Y"); ?> © Ascorsan<br/>Associação de Servidores da Corsan</p>
    </section>
</div>


<?php
require '../../components/footer.php';
