create table IF NOT EXISTS vote(
    id INTEGER PRIMARY KEY,
    vote  INTEGER NOT NULL,
    terms   BOOLEAN NOT NULL,
    matricula   INTEGER NOT NULL,
    cpf  INTEGER NOT NULL,
    created_at  DATETIME NOT NULL,
    unique_id  INTEGER NOT NULL
);