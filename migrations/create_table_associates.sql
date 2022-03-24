create table IF NOT EXISTS associates(
    id INTEGER PRIMARY KEY,
    nome varchar(100) NOT NULL,
    cpf   INTEGER NOT NULL,
    matricula   INTEGER NOT NULL,
    lota  INTEGER NOT NULL,
    tipo  INTEGER NOT NULL,
    superintendencia  varchar(100) NOT NULL,
    cod_sup  INTEGER NOT NULL
);