CREATE TABLE IF NOT EXISTS urls (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    name VARCHAR(255) NOT NULL,
    created_at VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS url_checks (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    url_id INTEGER NOT NULL,
    status_code VARCHAR(255) NOT NULL,
    h1 VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at VARCHAR(255) NOT NULL
);
