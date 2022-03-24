<?php

require '../../components/header.php';
require '../../connection.php';

$connection = new Connection();

$url = $_SERVER['REQUEST_URI'];
$url_components = parse_url($url);
parse_str($url_components['query'], $params);
$encoded_data_url = $params['data'];

$data = json_decode(base64_decode($encoded_data_url));

$user = $connection->checkAssociate($data->cpf, $data->matricula);

$candidate = $connection->getCandidateByMatricula($data->vote);

?>

<div class="content">
    <section id="cadastrar">
        <a href="https://ascorsan.com.br">
            <img src="../../assets/images/logo.png" alt="ascorsan" />
        </a>
        <div class="container">
            <h2>Dados do meu voto</h2>
            <form> 
                <h2 style="margin: 1.4rem 0;"><?php print_r ($user[0]['nome']); ?></h2>     
                <span>CPF:  <strong><?php echo $data->cpf; ?></strong></span> 
                <span>Matricula:  <strong><?php echo $data->matricula; ?></strong></span>         
                <span>Data de registro do voto:  <br /><strong><?php echo $data->created_at; ?></strong></span>
                <span>Aceito os termos: <strong><?php echo $data->terms == 'on' ? 'Sim':'Não'; ?></strong></span>
                <span>Candidato: <strong><?php echo $candidate[0]['nome']; ?></strong></span>
                <span>Identificador do voto:  <strong><?php echo $data->unique_id; ?></strong></span>
                <button tabindex="3" type="button" onClick="handleImprimir()">Imprimir</button>
            </form>
        </div>
        <p style="color: #FFF; font-weight: normal; text-align: center"><?php echo date("Y"); ?> © Ascorsan<br/>Associação de Servidores da Corsan</p>
    </section>
</div>

<script>
    function handleImprimir(){            
            window.print()
        }
</script>


<?php
require '../../components/footer.php';
