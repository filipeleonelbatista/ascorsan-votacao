create table IF NOT EXISTS users(
    id INTEGER PRIMARY KEY,    
    cpf   INTEGER NOT NULL UNIQUE,
    matricula   INTEGER NOT NULL UNIQUE,
    email  varchar(100) NOT NULL UNIQUE,
    password  varchar(100) NOT NULL,
    unique_id varchar(100) NOT NULL,
    is_active  boolean NOT NULL
);