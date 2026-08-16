/* 1. Register for Bill Summary (Sales) */
CREATE TABLE IF NOT EXISTS sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_no VARCHAR(50) NOT NULL,
    customer_name VARCHAR(100) DEFAULT 'Walk-in Customer',
    payment_method VARCHAR(50) DEFAULT 'Cash',
    grand_total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* 2. Register for Bill Items (Individual Products in a bill) */
CREATE TABLE IF NOT EXISTS sales_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL
);