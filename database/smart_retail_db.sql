/* Hum users naam ki ek list (table) bana rahe hain */
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100),
    role VARCHAR(20)
);

/* Hum pehle se ek Admin user dal rahe hain login test karne ke liye */
INSERT INTO users (full_name, email, password, role) 
VALUES ('Yash Parmar', 'admin@smartshop.com', 'admin123', 'admin');