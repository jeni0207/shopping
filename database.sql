CREATE DATABASE IF NOT EXISTS shopease;
USE shopease;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(500),
    description TEXT,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'cod',
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Admin login: admin@shopease.com / admin123
INSERT INTO admin (name, email, password) 
VALUES ('Admin', 'admin@shopease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO products (name, category, price, image, description, stock) VALUES
('Wireless Headphones', 'Electronics', 2999, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400', 'Comfortable over-ear wireless headphones.', 25),
('Running Shoes', 'Footwear', 3499, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400', 'Lightweight running shoes for daily training.', 40),
('Smart Watch', 'Electronics', 4999, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400', 'Fitness tracking smart watch.', 15),
('Backpack', 'Accessories', 1899, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400', 'Durable backpack for daily use.', 30);