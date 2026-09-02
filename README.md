# MeowGhor — A Cat Adoption Shelter Management System

MeowGhor is a simple web-based Cat Adoption Shelter Management System developed for a university Web Technologies project.

The system connects cat adopters/community users with shelter staff. Shelter Staff can manage cat listings and review requests, while Adopters can browse cats, apply for adoption, submit cat intake requests, and manage their account.

The project is built using basic PHP and MySQL concepts with a simple MVC structure.

---

## Technologies Used

- HTML
- CSS
- JavaScript
- PHP
- MySQL / MariaDB
- XAMPP
- phpMyAdmin
- MySQLi
- MySQLi Prepared Statements
- PHP Sessions

No external API or web framework is used.

---

## User Roles

The system contains two user roles:

### Adopter / Community User

Adopters can:

- Register an account
- Login and logout
- Browse available cats
- Search cats by name or breed
- Filter cats by gender and age
- View detailed cat information
- Submit adoption applications
- View adoption application status
- Withdraw pending adoption applications
- Submit cat intake requests
- View intake request status
- Cancel pending intake requests
- View a personal dashboard
- Manage profile and account information

### Shelter Staff

Shelter Staff can:

- Login using an existing Staff account
- View the Staff dashboard
- Add new cat listings
- Upload cat images
- View cat listings
- Edit cat information
- Archive unavailable cats
- View cat intake requests
- Accept or reject pending intake requests
- View adoption applications
- Approve or reject adoption applications
- Manage Staff name and password

Staff registration is not publicly available.

---

## Account Management

Account Management is shared by both roles and uses the existing `users` table.

### Adopter Account Management

Adopters can:

- Change name
- Change email
- Change phone number
- Change address
- Change password

### Shelter Staff Account Management

Shelter Staff can:

- Change name
- View email as read-only
- Change password

Shelter Staff cannot change their email, phone number, address, or user role.

Passwords are stored using PHP password hashing.

---

## Database

Database name:

```text
meowghor
```

The project uses four main tables:

### `users`

Stores both Adopter and Shelter Staff accounts.

Main information includes:

- User ID
- Name
- Email
- Password
- Phone
- Address
- User Type
- Created Date

The `user_type` field determines whether the account is:

```text
adopter
staff
```

### `cats`

Stores cats listed by Shelter Staff.

Information includes:

- Cat ID
- Name
- Breed
- Gender
- Age
- Color
- Health Status
- Description
- Image
- Adoption Status
- Staff Member Who Added the Cat
- Intake Request Reference
- Created Date

Possible adoption statuses are:

```text
Available
Adopted
Unavailable
```

### `cat_intake_requests`

Stores requests submitted by Adopters who want a cat to be taken into the shelter.

Possible statuses are:

```text
Pending
Accepted
Rejected
Cancelled
```

### `adoption_applications`

Stores adoption applications submitted by Adopters for available cats.

Possible statuses are:

```text
Pending
Approved
Rejected
Withdrawn
```

---

## Project Structure

```text
MeowGhor/
│
├── config/
│   └── database.php
│
├── database/
│   └── schema.sql
│
├── common/
│   ├── model/
│   │   ├── AuthModel.php
│   │   └── ProfileModel.php
│   │
│   ├── controller/
│   │   ├── AuthController.php
│   │   ├── AuthGuard.php
│   │   └── ProfileController.php
│   │
│   └── view/
│       ├── login.php
│       ├── register.php
│       ├── profile.php
│       ├── profile_menu.php
│       └── assets/
│
├── Adopter/
│   ├── model/
│   │   └── AdopterModel.php
│   │
│   ├── controller/
│   │   └── AdopterController.php
│   │
│   └── view/
│       ├── dashboard.php
│       ├── cats.php
│       ├── cat_details.php
│       ├── applications.php
│       └── intakes.php
│
├── ShelterStaff/
│   ├── model/
│   │   └── StaffModel.php
│   │
│   ├── controller/
│   │   └── StaffController.php
│   │
│   └── view/
│
└── uploads/
    └── cats/
```

---

## MVC Structure

The project follows a simple Model-View-Controller structure.

### View

Views contain:

- HTML
- Forms
- Page layout
- Displayed data

Views do not directly perform SQL queries.

### Controller

Controllers handle:

- Form submissions
- Input validation
- Actions
- Business rules
- Session-related logic
- Redirects

### Model

Models contain:

- MySQL database queries
- INSERT operations
- SELECT operations
- UPDATE operations

The project uses MySQLi prepared statements for database operations.

### Database Configuration

The shared database connection is located in:

```text
config/database.php
```

Both Adopter and Shelter Staff features use the same database connection.

---

## Main Cat Adoption Workflow

```text
Shelter Staff
      ↓
Add Cat
      ↓
cats table
      ↓
Adopter Browse Cats
      ↓
View Cat Details
      ↓
Submit Adoption Application
      ↓
adoption_applications
      ↓
Shelter Staff Review
      ↓
Approve / Reject
```

If an adoption application is approved:

```text
Application → Approved
Cat → Adopted
```

The adopted cat is no longer shown in the list of available cats.

---

## Cat Intake Workflow

```text
Adopter
    ↓
Submit Intake Request
    ↓
cat_intake_requests
    ↓
Shelter Staff
    ↓
Accept / Reject
    ↓
Adopter Views Updated Status
```

An Adopter may cancel an intake request only while its status is `Pending`.

---

## Cat Browsing

Adopters can browse only cats whose status is:

```text
Available
```

The browsing page supports:

- Search by cat name
- Search by breed
- Gender filter
- Age group filter

Age groups are:

```text
Kitten  = under 1 year
Young   = 1 to under 4 years
Adult   = 4 to under 8 years
Senior  = 8 years and above
```

---

## Cat Images

Cat images are uploaded through the Shelter Staff cat management interface.

Uploaded images are stored in:

```text
uploads/cats/
```

Supported image formats include:

```text
JPG
JPEG
PNG
WEBP
```

The database stores the image filename/path instead of storing the image binary directly.

---

## Authentication and Security

The project uses:

- PHP Sessions
- Role-based authorization
- Backend form validation
- MySQLi prepared statements
- `password_hash()`
- `password_verify()`

Protected pages verify whether the logged-in user has the correct role.

For example:

```text
Adopter pages → adopter only
Shelter Staff pages → staff only
```

Users who are not logged in are redirected to the Login page.

---

## Installation

### 1. Install XAMPP

Install XAMPP and start:

```text
Apache
MySQL
```

### 2. Add the Project

Place the project where Apache can serve it, or configure an Apache Alias for the project.

The project can be accessed locally using:

```text
http://localhost/MeowGhor/
```

### 3. Create the Database

Open:

```text
http://localhost/phpmyadmin
```

Create a database named:

```text
meowghor
```

Import:

```text
database/schema.sql
```

This creates the required database tables.

### 4. Database Configuration

Check:

```text
config/database.php
```

Default local XAMPP configuration normally uses:

```text
Host: localhost
Username: root
Password: empty
Database: meowghor
```

Update these settings if your local MySQL configuration is different.

---

## Shelter Staff Account

There is no public Shelter Staff registration page.

A Staff account must already exist in the `users` table with:

```text
user_type = staff
```

The password stored in the database must be generated using PHP password hashing.

Do not store a plain-text password in the database.

---

## Example System Flow

### Adding a Cat

```text
Staff Login
→ Cat Management
→ Add Cat
→ Enter cat information
→ Upload image
→ Submit
→ Cat saved in MySQL
```

The same cat then becomes available on the Adopter Browse Cats page if its status is `Available`.

### Applying for a Cat

```text
Adopter Login
→ Browse Cats
→ View Details
→ Apply for Adoption
→ Application Pending
→ Staff Reviews Application
```

### Submitting a Cat Intake

```text
Adopter Login
→ My Intakes
→ Submit Intake Request
→ Staff Reviews Request
→ Status Updated
```

---

## Project Goal

The goal of MeowGhor is to demonstrate the basic concepts of web application development using PHP and MySQL, including:

- Authentication
- Sessions
- Role-based access
- CRUD operations
- Form validation
- File uploads
- Database relationships
- Prepared SQL statements
- Simple MVC architecture

The project intentionally uses a simple structure so that the implementation remains understandable and maintainable.
