-- =============================================
-- BiblioTech — Gestió de Biblioteca
-- =============================================

DROP DATABASE IF EXISTS bibliotech;
CREATE DATABASE bibliotech CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE bibliotech;

CREATE TABLE autors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    pais VARCHAR(50) NOT NULL,
    any_naixement INT NOT NULL
);

CREATE TABLE llibres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titol VARCHAR(150) NOT NULL,
    genere VARCHAR(50) NOT NULL,
    any_publicacio INT NOT NULL,
    id_autor INT NOT NULL,
    FOREIGN KEY (id_autor) REFERENCES autors(id)
);

-- Autors
INSERT INTO autors (nom, pais, any_naixement) VALUES
('Isaac Asimov', 'Rússia', 1920),
('Ursula K. Le Guin', 'Estats Units', 1929),
('J.R.R. Tolkien', 'Regne Unit', 1892),
('Stephen King', 'Estats Units', 1947),
('Agatha Christie', 'Regne Unit', 1890),
('Gabriel García Márquez', 'Colòmbia', 1927),
('Liu Cixin', 'Xina', 1963),
('Mary Shelley', 'Regne Unit', 1797),
('Frank Herbert', 'Estats Units', 1920),
('Terry Pratchett', 'Regne Unit', 1948);

-- Llibres
INSERT INTO llibres (titol, genere, any_publicacio, id_autor) VALUES
('Fundació', 'Ciència-ficció', 1951, 1),
('Jo, Robot', 'Ciència-ficció', 1950, 1),
('El fin de la eternidad', 'Ciència-ficció', 1955, 1),
('La mà esquerra de la foscor', 'Ciència-ficció', 1969, 2),
('Els desposseïts', 'Ciència-ficció', 2001, 2),
('El Senyor dels Anells', 'Fantasia', 1954, 3),
('El Hòbbit', 'Fantasia', 1937, 3),
('It', 'Terror', 1986, 4),
('El resplandor', 'Terror', 1977, 4),
('Misery', 'Terror', 1987, 4),
('Assassinat a l\'Orient Express', 'Misteri', 1934, 5),
('Deu negrets', 'Misteri', 1939, 5),
('Cent anys de solitud', 'Realisme màgic', 1967, 6),
('L\'amor en els temps del còlera', 'Realisme màgic', 1985, 6),
('El problema dels tres cossos', 'Ciència-ficció', 2008, 7),
('El bosc fosc', 'Ciència-ficció', 2015, 7),
('Frankenstein', 'Terror', 1818, 8),
('Dune', 'Ciència-ficció', 1965, 9),
('El color de la màgia', 'Fantasia', 1983, 10),
('Mort', 'Fantasia', 1987, 10);