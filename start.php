<?php

try{

    $db = new PDO("sqlite:database/db.sqlite");
    
    echo "Created database";

    $create_config = file_get_contents("./migrations/create_table_config.sql");
    $create_users = file_get_contents("./migrations/create_table_users.sql");
    $create_associates = file_get_contents("./migrations/create_table_associates.sql");
    $create_candidates = file_get_contents("./migrations/create_table_candidates.sql");
    $create_vote = file_get_contents("./migrations/create_table_vote.sql");

    echo "\nCreating Tables";
    $db->exec($create_config);
    $db->exec($create_users);
    $db->exec($create_associates);
    $db->exec($create_candidates);
    $db->exec($create_vote);
    echo "\nTables Created";

    $insert_config = file_get_contents("./seeds/insert_config_seed.sql");
    $insert_associates = file_get_contents("./seeds/insert_associates_seed.sql");
    $insert_candidates = file_get_contents("./seeds/insert_candidates_seed.sql");

    echo "\nSeeding Tables";
    $db->exec($insert_config);
    $db->exec($insert_associates);
    $db->exec($insert_candidates);
    echo "\nTables Seeded";

    echo "\nSuccess!";

}catch(PDOException $e){
    echo $e->getMessage();
}

