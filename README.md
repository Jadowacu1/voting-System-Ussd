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
