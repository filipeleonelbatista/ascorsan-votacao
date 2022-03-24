create table IF NOT EXISTS config(
    id INTEGER PRIMARY KEY,
    start_date INTEGER NOT NULL,
    end_date INTEGER NOT NULL,
    admin_user varchar(100) NOT NULL,
    admin_password varchar(100) NOT NULL,
    admin_name varchar(100) NOT NULL
);