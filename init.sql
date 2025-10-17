CREATE DATABASE IF NOT EXISTS bibliotheque CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'symfony'@'%' IDENTIFIED BY 'symfony';
GRANT ALL PRIVILEGES ON bibliotheque.* TO 'symfony'@'%';
FLUSH PRIVILEGES;

USE bibliotheque;

CREATE TABLE IF NOT EXISTS Author (
    id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    biography LONGTEXT,
    birth_date DATE
);

CREATE TABLE IF NOT EXISTS Category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT
);

CREATE TABLE IF NOT EXISTS Book (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    publish_date DATE,
    available BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (author_id) REFERENCES Author(id),
    FOREIGN KEY (category_id) REFERENCES Category(id)
);

CREATE TABLE IF NOT EXISTS User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS Rent (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rent_date DATETIME NOT NULL,
    return_date DATE,
    actual_return_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES User(id),
    FOREIGN KEY (book_id) REFERENCES Book(id)
);

INSERT INTO Author (last_name, first_name, biography, birth_date) VALUES
('Hugo', 'Victor', 'French writer of the 19th century', '1802-02-26'),
('Dumas', 'Alexandre', 'Author of The Three Musketeers', '1802-07-24');

INSERT INTO Category (name, description) VALUES
('Novel', 'Fictional narrative books'),
('History', 'Historical books and biographies');

INSERT INTO User (last_name, first_name) VALUES
('Achouchi', 'Rayane'),
('Vovard', 'Mathéo');

INSERT INTO Book (author_id, category_id, title, publish_date, available) VALUES
(1, 1, 'Les Misérables', '1862-01-01', TRUE),
(1, 1, 'The Hunchback of Notre-Dame', '1831-01-01', TRUE),
(2, 1, 'The Three Musketeers', '1844-03-14', TRUE);

INSERT INTO Rent (user_id, book_id, rent_date, return_date, actual_return_date) VALUES
(1, 1, NOW(), '2025-11-01', NULL),
(2, 2, NOW(), '2025-11-05', NULL);
