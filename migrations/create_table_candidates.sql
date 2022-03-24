create table IF NOT EXISTS candidates(
    id INTEGER PRIMARY KEY,
    nome varchar(100) NOT NULL,
    matricula   INTEGER NOT NULL,
    superintendencia  varchar(100) NOT NULL,
    cod_sup  INTEGER NOT NULL
);