-- Creazione del database
CREATE DATABASE film_recommendation;

USE film_recommendation;

-- Creazione della tabella utenti
CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);

-- Creazione della tabella film
CREATE TABLE film (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(255) NOT NULL
);

-- Creazione della tabella valutazioni
CREATE TABLE valutazioni (
    utente_id INT,
    film_id INT,
    valutazione INT,
    PRIMARY KEY (utente_id, film_id),
    FOREIGN KEY (utente_id) REFERENCES utenti(id),
    FOREIGN KEY (film_id) REFERENCES film(id)
);

-- Inserimento di alcuni utenti
INSERT INTO utenti (nome) VALUES ('Alice'), ('Bob'), ('Carol'), ('Dave');

-- Inserimento di alcuni film
INSERT INTO film (titolo) VALUES ('Inception'), ('Titanic'), ('Avatar'), ('The Matrix');

-- Inserimento delle valutazioni degli utenti
INSERT INTO valutazioni (utente_id, film_id, valutazione) VALUES
(1, 1, 5), -- Alice ha dato 5 a Inception
(1, 2, 3), -- Alice ha dato 3 a Titanic
(2, 1, 4), -- Bob ha dato 4 a Inception
(2, 3, 5), -- Bob ha dato 5 a Avatar
(3, 2, 4), -- Carol ha dato 4 a Titanic
(3, 4, 5), -- Carol ha dato 5 a The Matrix
(4, 3, 2), -- Dave ha dato 2 a Avatar
(4, 4, 3); -- Dave ha dato 3 a The Matrix
