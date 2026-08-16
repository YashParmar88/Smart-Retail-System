/* create categories table */
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* insert dummy categories */
INSERT INTO categories (category_name, description) VALUES 
('Electronics', 'Gadgets, phones and accessories'),
('Beverages', 'Soft drinks, tea and coffee'),
('Groceries', 'Daily household items'),
('Fitness', 'Gym and sports equipment');