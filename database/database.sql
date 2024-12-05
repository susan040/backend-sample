CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(25) not null,
    address VARCHAR(25) not null,
    phone VARCHAR(10) not null,
    email VARCHAR(30) not null unique,
    password VARCHAR(255) not null,
    user_type VARCHAR(15) not null,
    image VARCHAR(255),
    created_at DATETIME
);

CREATE TABLE IF NOT EXISTS otp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT not null,
    code VARCHAR(10) NOT NULL,
    created_at datetime,
    is_verified BOOLEAN not null,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
ALTER TABLE otp
ADD CONSTRAINT fk_user_id_otp FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS tokens (
id int PRIMARY KEY AUTO_INCREMENT,
user_id int not null,
token varchar(255) not null,
created_at datetime,
FOREIGN KEY (user_id) REFERENCES users(id));
ALTER TABLE tokens
ADD CONSTRAINT fk_user_id_tokens FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


CREATE TABLE IF NOT EXISTS categories(
    id int PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(25) not null unique,
    image VARCHAR(255),
    created_at DATETIME
);

CREATE TABLE IF NOT EXISTS properties(
    id int PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(25) not null,
    category_id int not null,
    property_status VARCHAR(25) not null,
    description VARCHAR(255) not null,
    city VARCHAR(15) not null,
    district VARCHAR(15) not null,
    zip_code VARCHAR(15),
    street_address VARCHAR(25) not null,
    total_area int not null,
    bedroom int not null,
    bathroom int not null,
    price decimal(10,2) not null,
    time_intervel VARCHAR(20) not null,
    created_at datetime,
    FOREIGN KEY(category_id) REFERENCES categories(id),
);
ALTER TABLE properties
ADD CONSTRAINT fk_category_id_categories FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE;


CREATE TABLE IF NOT EXISTS appointments(
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT,
    date DATE NOT NULL,
    time TIME,
    status VARCHAR(25) NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME,
    foreign key(property_id) REFERENCES properties(id),
    foreign key(user_id) REFERENCES users(id)
);
ALTER TABLE appointments
ADD CONSTRAINT fk_property_id_appointments FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE;
ALTER TABLE appointments
ADD CONSTRAINT fk_user_id_appointments FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS rental(
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    user_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    pets_allowed BOOLEAN NOT NULL,
    max_people INT NOT NULL,
    created_at datetime,
    FOREIGN KEY (property_id) REFERENCES properties(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
ALTER TABLE rental
ADD CONSTRAINT fk_property_id_rental FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE;
ALTER TABLE rental
ADD CONSTRAINT fk_user_id_rental FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS transaction(
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    rent_id INT NOT NULL,
    payment_method VARCHAR(25) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_status VARCHAR(15) NOT NULL,
    date DATETIME,
    FOREIGN KEY (rent_id) REFERENCES rental(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
ALTER TABLE transaction
ADD CONSTRAINT fk_rental_id_transaction FOREIGN KEY (rent_id) REFERENCES rental(id) ON DELETE CASCADE;
ALTER TABLE transaction
ADD CONSTRAINT fk_user_id_transaction FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS reviews (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    user_id INT NOT NULL,
    rating DECIMAL (10,1) NOT NULL,
    comment VARCHAR(255),
    date DATETIME,
    FOREIGN KEY (property_id) REFERENCES properties(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
ALTER TABLE reviews
ADD CONSTRAINT fk_user_id_reviews FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE reviews
ADD CONSTRAINT fk_property_id_reviews FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE;




Alter table appointments AUTO_INCREMENT = 1;
ALter table categories AUTO_INCREMENT = 1;
ALTER table otp AUTO_INCREMENT = 1;
ALTER TABLE properties AUTO_INCREMENT = 1;
ALTER TABLE tokens AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;
Alter TABLE rental AUTO_INCREMENT = 1;
Alter TABLE transaction AUTO_INCREMENT = 1;
Alter TABLE reviews AUTO_INCREMENT = 1;


