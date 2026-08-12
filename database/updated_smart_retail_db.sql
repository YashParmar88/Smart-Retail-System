/* Database Backup Code for Day 6 */

/* Table structure for table `products` */
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    category VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    stock_level INT NOT NULL,
    low_stock_threshold INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* Dumping data for table `products` */
INSERT INTO products (product_name, sku, category, price, stock_level, low_stock_threshold) VALUES
('UltraSound Wireless Pro', 'SKU-001', 'Electronics', 299.00, 85, 15),
('Artisan Roast Coffee', 'SKU-042', 'Beverages', 24.50, 12, 15),
('Titanium Sport Flask', 'SKU-089', 'Fitness', 45.00, 45, 10),
('Chronos Smart Watch', 'SKU-015', 'Electronics', 399.00, 5, 10);