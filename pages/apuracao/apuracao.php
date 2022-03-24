<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/apuracao.css">
    <link rel="icon" type="image/png" href="../../assets/images/icon.png">
    <script src="https://kit.fontawesome.com/0d69fdfc2e.js" crossorigin="anonymous"></script>

    <!-- SEO  -->
    <title>Sistema de votação - ASCORSAN</title>
    <meta name="title" content="Sistema de votação - ASCORSAN" />
    <meta name="description" content="Sistema de votação da superintendencia da Ascorsan." />
    <link rel="canonical" href='https://ascorsan.com.br/votacao' />
    <meta name="author" content="Sistema de votação - ASCORSAN" />
    <meta name="robots" content="index" />

    <meta itemProp="name" content="Sistema de votação - ASCORSAN" />
    <meta itemProp="description" content="Sistema de votação da superintendencia da Ascorsan." />
    <meta itemProp="image" content="../../assets/images/banner.png" />

    <meta property="og:title" content="Sistema de votação - ASCORSAN" />
    <meta property="og:description" content="Sistema de votação da superintendencia da Ascorsan." />
    <meta property="og:url" content=https://ascorsan.com.br/votacao />
    <meta property="og:site_name" content="Sistema de votação - ASCORSAN" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="../../assets/images/banner.png" />
    <meta property="og:image:width" content="3751" />
    <meta property="og:image:height" content="2813" />

    <meta name="twitter:title" content="Sistema de votação - ASCORSAN" />
    <meta name="twitter:description" content="Sistema de votação da superintendencia da Ascorsan." />
    <meta name="twitter:url" content=https://ascorsan.com.br/votacao />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:image" content="../../assets/images/banner.png" />
    <!-- Fim SEO  -->
</head>

<body>
    <?php

    require '../../connection.php';

    $connection = new Connection();

    $url_complete = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/votacao/admin";

    $error_msg = '';
    $votes = [];

    $url = $_SERVER['REQUEST_URI'];
    $url_components = parse_url($url);
    parse_str($url_components['query'], $params);
    $data = $params['data'];
    $decoded_data = json_decode(base64_decode($data));

    $superintendencia_list = $connection->getSuperintendenciasList();

    $_POST['start_date'] = $decoded_data[0]->start_date;
    $start_time_array = explode(" ", $decoded_data[0]->start_date);
    $start_time = $start_time_array[1];
    $formatted_start_date = $start_time_array[0];

    $_POST['end_date'] = $decoded_data[0]->end_date;
    $end_time_array = explode(" ", $decoded_data[0]->end_date);
    $end_time = $end_time_array[1];
    $formatted_end_date = $end_time_array[0];

    $_POST['admin_name'] = $decoded_data[0]->admin_name;
    $_POST['admin_user'] = $decoded_data[0]->admin_user;

    $total_sup_participants = '';
    $first_cod_sup = '';

    if (!empty($_POST)) {
        if (isset($_POST['ver'])) {
            $first_cod_sup = $_POST['superintendencia'];
            $votes = $connection->getVotesCounterBySuperintendencia($first_cod_sup);
            $candidates = $connection->getVotesBySuperintendencia($first_cod_sup);
            $total_sup_participants = $connection->getSuperintendenciaParticipants($first_cod_sup);
        } else {
            $first_cod_sup = $superintendencia_list[0]['cod_sup'];
            $votes = $connection->getVotesCounterBySuperintendencia($first_cod_sup);
            $candidates = $connection->getVotesBySuperintendencia($first_cod_sup);
            $total_sup_participants = $connection->getSuperintendenciaParticipants($first_cod_sup);
        }

        if (isset($_POST['salvar'])) {
            echo  $_POST['new_start_date'];
            echo  $_POST['new_start_time'];
            $new_start_date = "" . $_POST['new_start_date'] . " " . $_POST['new_start_time'];
            $new_end_date = "" . $_POST['new_end_date'] . " " . $_POST['new_end_time'];
            $result = $connection->updateDates($new_start_date, $new_end_date, $decoded_data[0]->admin_user, $decoded_data[0]->id);
            $result = $connection->authConfig($decoded_data[0]->admin_user, $decoded_data[0]->admin_password);
            $data = base64_encode(json_encode($result));
            header("location: ../../pages/apuracao/apuracao.php?data=" . $data);
        }
    }
    ?>

    <div class="container">
        <aside class="navigation-dashboard">
            <a href="https://ascorsan.com.br">
                <img src="../../assets/images/icon.png" alt="Ascorsan" />
            </a>
            <button onClick="window.open('<?php echo $url_complete ?>', '_self');" title="Sair">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </aside>
        <main class="dashboard">
            <div class="header-dashboard">
                <h2>Apuração de votos eleição Ascorsan</h2>
                <form method="post" action="" class="header-dashboard-input">
                    <label for="superintendencia">Selecione a superintendencia:</label>
                    <div class="header-dashboard-input-row">
                        <select name="superintendencia" id="superintendencia">
                            <?php foreach ($superintendencia_list as $superintendencia) : ?>
                                <option <?php echo $first_cod_sup == $superintendencia['cod_sup'] ? "selected" : ""; ?> value="<?php echo $superintendencia['cod_sup']; ?>"><?php echo $superintendencia['superintendencia'] . " - " . $superintendencia['cod_sup']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button title="Pesquisar" type="submit" name="ver"><i class="fa fa-search" aria-hidden="true"></i></button>
                        <button title="Imprimir" type="button" onclick="handleImprimir();"><i class="fa fa-print"></i></button>
                    </div>
                </form>
            </div>

            <form method="post" action="" class="header-dashboard-input">
                <div class="header-dashboard-input-row">
                    <div class="date-input-group">
                        <label for="new_start_date">Data de inicio da votação:</label>
                        <input required id="new_start_date" name="new_start_date" type="date" value="<?php echo $formatted_start_date; ?>" />
                    </div>
                    <div class="date-input-group">
                        <label for="new_start_time">Hora de inicio da votação:</label>
                        <input required id="new_start_time" name="new_start_time" type="time" value="<?php echo $start_time; ?>" />
                    </div>
                    <div class="date-input-group">
                        <label for="new_end_date">Data de termino da votação:</label>
                        <input required id="new_end_date" name="new_end_date" type="date" value="<?php echo $formatted_end_date; ?>" />
                    </div>
                    <div class="date-input-group">
                        <label for="new_end_time">Hora de termino da votação:</label>
                        <input required id="new_end_time" name="new_end_time" type="time" value="<?php echo $end_time; ?>" />
                    </div>
                    <button title="Salvar" type="submit" name="salvar"><i class="fas fa-save"></i></button>
                </div>
            </form>
            <div class="dashboard-votation-info">
                <div class="dashboard-votation-info-container">
                    <h3>Quantidade de votos</h3>
                    <span><?php echo $votes[0]['votos']; ?><sup>/<?php echo $votes[0]['total']; ?></span>
                </div>
                <div class="dashboard-votation-info-container">
                    <h3>Total de participantes</h3>
                    <span><?php echo $total_sup_participants[0]['total']; ?></span>
                </div>
                <div class="dashboard-votation-info-container">
                    <h3>Candidato mais votado</h3>
                    <?php if (sizeof($candidates) == 0) : ?>
                        <p>
                            Nenhum candidato recebeu votos ainda...
                        </p>
                    <?php else : ?>
                        <table>
                            <thead>
                                <tr>
                                    <th align="left">Matricula</th>
                                    <th align="center">Nome</th>
                                    <th align="right">Votos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="left"><?php echo $candidates[0]['matricula']; ?></td>
                                    <th align="center"><?php echo $candidates[0]['nome']; ?></th>
                                    <td align="right"><?php echo $candidates[0]['voto']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            <div class="header-votation-datatable">
                <h2><?php echo 'Candidatos da Superintendencia ' . $first_cod_sup; ?></h2>
                <?php if (sizeof($candidates) == 0) : ?>
                    <p>
                        Nenhum candidato recebeu votos ainda...
                    </p>
                <?php else : ?>
                    <?php foreach ($candidates as $candidate) : ?>
                        <div class="datatable-row">
                            <p>
                                <?php echo $candidate['nome']; ?><br />
                                <sub>Matricula: <?php echo $candidate['matricula']; ?></sub>
                            </p>
                            <p>Votos: <?php echo $candidate['voto']; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <footer>
                <a href="https://filipedev.ga">Desenvolvido por Leonel Informatica</a>
            </footer>
        </main>
    </div>
    <script>
        function handleImprimir() {
            window.print()
        }
    </script>
</body>

</html>