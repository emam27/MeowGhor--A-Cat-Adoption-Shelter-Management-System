-- Database: meowghor

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(255),
    user_type ENUM('adopter', 'staff') NOT NULL DEFAULT 'adopter',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cat_intake_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cat_name VARCHAR(100) NOT NULL,
    breed VARCHAR(100),
    gender ENUM('Male', 'Female') NOT NULL,
    age DECIMAL(4,1),
    health_status VARCHAR(255),
    description TEXT,
    reason TEXT NOT NULL,
    image VARCHAR(255),
    request_status ENUM(
        'Pending',
        'Accepted',
        'Rejected',
        'Cancelled'
    ) NOT NULL DEFAULT 'Pending',
    staff_comment TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME NULL,

    CONSTRAINT fk_intake_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
);

CREATE TABLE cats (
    cat_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(100),
    gender ENUM('Male', 'Female') NOT NULL,
    age DECIMAL(4,1),
    color VARCHAR(100),
    health_status VARCHAR(255),
    description TEXT,
    image VARCHAR(255),
    adoption_status ENUM(
        'Available',
        'Adopted',
        'Unavailable'
    ) NOT NULL DEFAULT 'Available',
    added_by INT NOT NULL,
    intake_request_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_cat_staff
        FOREIGN KEY (added_by)
        REFERENCES users(user_id),

    CONSTRAINT fk_cat_intake
        FOREIGN KEY (intake_request_id)
        REFERENCES cat_intake_requests(request_id)
);

CREATE TABLE adoption_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cat_id INT NOT NULL,
    reason TEXT NOT NULL,
    living_situation TEXT NOT NULL,
    application_status ENUM(
        'Pending',
        'Approved',
        'Rejected',
        'Withdrawn'
    ) NOT NULL DEFAULT 'Pending',
    staff_comment TEXT,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME NULL,

    CONSTRAINT fk_application_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id),

    CONSTRAINT fk_application_cat
        FOREIGN KEY (cat_id)
        REFERENCES cats(cat_id)
);
