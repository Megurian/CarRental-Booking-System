CREATE DATABASE car_rental_db;

CREATE TABLE cars (
	id INT AUTO_INCREMENT PRIMARY KEY,
	car_name VARCHAR(100) NOT NULL,
	car_model VARCHAR(100) NOT NULL,
	quantity INT NOT NULL
);

INSERT INTO cars (car_name, car_model, quantity) VALUES ('Toyota', 'Corolla', 5),
														('Honda', 'Civic', 4),
														('Ford', 'Mustang', 2),
														('BMW', '3 Series', 3),
														('Mercedes', 'C-Class', 2);
														
INSERT INTO cars (car_name, car_model, quantity) VALUES ('Sample', 'Car', 0);

CREATE TABLE rentals (
	id INT AUTO_INCREMENT PRIMARY KEY,
	client_name VARCHAR(100) NOT NULL,
	rental_date DATE NOT NULL,
	return_date DATE NOT NULL,
	car_id INT NOT NULL,
	remarks TEXT,
	status ENUM('Booked', 'Completed', 'Cancelled') DEFAULT 'Booked',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (car_id) REFERENCES cars(id)
);