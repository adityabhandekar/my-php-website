CREATE TABLE adminlogin_tb (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE contact_tb (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    feedback_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders_tb (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT,
    user_id INT,
    customer_name VARCHAR(100),
    contact VARCHAR(15),
    address TEXT,
    appointment_date DATE,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    service_title VARCHAR(100),
    service_description TEXT,
    service_rate DECIMAL(10, 2),
    technician_name VARCHAR(100),
    order_status VARCHAR(50),
    transaction_id VARCHAR(100),
    email VARCHAR(100),
    pincode VARCHAR(10),
    FOREIGN KEY (user_id) REFERENCES userinfo_tb(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services_tb(id) ON DELETE SET NULL
);


CREATE TABLE services_tb (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    rate DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE technicians_tb (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    aadhaar VARCHAR(12) NOT NULL UNIQUE,
    skills TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE userinfo_tb (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    pass VARCHAR(255) NOT NULL,  -- Store hashed passwords
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


