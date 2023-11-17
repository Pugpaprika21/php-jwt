CREATE TABLE users (
    id INT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    create_date DATE DEFAULT CURRENT_DATE,
    is_deleted BOOLEAN DEFAULT false
);
