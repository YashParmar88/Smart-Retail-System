/* 
   MASTER DATABASE BACKUP: Hardware & Plumbing ERP
   Created: Day 14
*/

/* 1. USERS TABLE - Auth system */
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(100),
    role VARCHAR(20)
);

/* 2. CATEGORIES TABLE - Hardware Departments */
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255)
);

/* 3. SUPPLIERS TABLE - Vendor Directory */
CREATE TABLE IF NOT EXISTS suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_name VARCHAR(150) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT
);

/* 4. CUSTOMERS TABLE - Shopper Database */
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) UNIQUE,
    email VARCHAR(100),
    address TEXT
);

/* 5. PRODUCTS TABLE - Inventory */
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    category VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    stock_level INT NOT NULL,
    low_stock_threshold INT DEFAULT 10
);

/* 6. SALES SUMMARY TABLE */
CREATE TABLE IF NOT EXISTS sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_no VARCHAR(50),
    customer_name VARCHAR(100),
    payment_method VARCHAR(50),
    grand_total DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* 7. SALES ITEMS TABLE - Bill Details */
CREATE TABLE IF NOT EXISTS sales_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT,
    product_id INT,
    quantity INT,
    unit_price DECIMAL(10,2)
);