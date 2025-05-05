# USSD Voting System

This is a **Student Union Voting System** that allows registered students to vote for their representatives (President, Vice President, Secretary, Treasurer) using a **USSD interface**. The system is built using **PHP** and **MySQL**, and is designed to run on any platform supporting HTTP and USSD gateway integration (e.g., Africa's Talking).

## 🧩 Features

- Student registration using phone number and registration number.
- Vote for different positions (President, VP, Secretary, Treasurer).
- Prevents double voting.
- Admin panel (optional) can manage candidates and view results.
- View candidate details via USSD.
- Supports position-wise voting and records vote counts.

## 📂 Project Structure

```bash
ussd_voting/
├── index.php             # USSD logic entry point
├── functions.php         # Voting and utility functions
├── db.php                # Database connection
├── .env                  # (optional) for database credentials
├── README.md             # Project documentation
 ```

## 🧪 Technologies Used

* PHP (Pure PHP, no frameworks)
* MySQL
* Git + GitHub
* USSD-compatible design (Africa's Talking or other gateway)
* Render.com (for deployment) or Heroku (with limitations)

## ⚙️ Setup Instructions

1. **Clone the repository**

   ``` 
   git clone git@github.com:Jadowacu1/voting-System-Ussd.git
   cd voting-System-Ussd
   ```

2. Create a MySQL Database
   Import the required tables:

   ```sql
   CREATE DATABASE ussd;

    USE ussd;

    CREATE TABLE positions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL UNIQUE
    );

    CREATE TABLE candidates (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      position_id INT NOT NULL,
      FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE
    );

    CREATE TABLE votes (
      id INT AUTO_INCREMENT PRIMARY KEY,
      reg_number VARCHAR(50) NOT NULL,
      position_id INT NOT NULL,
      candidate_id INT NOT NULL,
      UNIQUE (reg_number, position_id),
      FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE,
      FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
      FOREIGN KEY (reg_number) REFERENCES students(reg_number) ON DELETE CASCADE
    );


    CREATE TABLE admins (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(50) NOT NULL UNIQUE,
      phoneNumber VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

   ```

3. **Edit the database configuration in `db.php`**

   ```php
   $conn = new mysqli('localhost', 'username', 'password', 'your_database');
   ```

##  How It Works

* User dials a USSD code (e.g., `*123#`)
* Prompted to register (if new) or continue
* Can vote for each position once
* Options to view candidates before voting
* Votes stored in a secure database

## 🙋 Author

**Jadowacu**
* Email: [jadowacu@gmail.com](mailto:jadowacu@gmail.com)
* GitHub: [github.com/Jadowacu1](https://github.com/Jadowacu1)




