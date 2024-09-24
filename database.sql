CREATE TABLE IF NOT EXISTS urls (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS url_checks (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    url_id INTEGER REFERENCES urls(id) NOT NULL,
    status_code SMALLINT NOT NULL,
    h1 VARCHAR(1000),
    title TEXT,
    description TEXT,
    created_at TIMESTAMP NOT NULL
);
