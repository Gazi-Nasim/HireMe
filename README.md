# 🚀 HireMe – Job Board API  

A **Laravel-based RESTful API** for a Job Board platform.  
This project allows **users** to register and apply for jobs, while **employers** can post/manage jobs. It also supports **payments and invoices** for applications.  

---

## 📌 Features  

- 🔐 **Authentication** – JWT-based register/login
                        -Role based route permission
- 🏢 **Admin Management** – Manage all users (create/update/delete), manage jobs, see company analytics
                            -View all users, jobs, applications
                            -Filter by company or status
                            -Google analytics for page traffic
- 🏢 **Employer Management** – Post/edit/delete jobs for their company, view applicants, accept/reject them
                            -View applications to their jobs
                            -Accept or reject applications
- 👨‍💼 **Job Applications** – Apply with CV upload  
                          – View job listings
                          – Apply for jobs they haven’t already applied to
                          – View their application history.
                          – Payment for Application.
- 💳 **Payment Integration** – Pay to apply (SSLCommerz)  
- 📄 **Invoices** – Basic invoice objects for payments  
- 🔍 **Filtering** – Applications and Jobs by status/company

---

## ⚙️ Installation & Setup  

### Requirements  
- PHP 8.2+  
- Composer  
- MySQL / PostgreSQL  
- Laravel 11+  

### Steps  

```bash
# Clone repository
git clone https://github.com/Gazi-Nasim/HireMe.git
cd HireMe

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate --seed

# Start development server
php artisan serve
```

Server will run at:  
👉 `http://127.0.0.1:8000`  

---

## 🔑 Authentication  

This API uses **JWT Authentication**.  
Login returns an `access_token` which must be included in headers for protected routes.  

```
Authorization: Bearer <your_token>
```

---
## Follow to use postman

## 📡 API Endpoints  

### 🧑 User Auth  

#### Register  
**POST** `/api/register`  
Headers:  
```json
{
  "Accept": "application/json",
  "Content-Type": "application/json"
}
```  
Body:  
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "role": "admin",
  "password": "password123",
  "password_confirmation": "password123"
}
```  

#### Login  
**POST** `/api/login`  
Body:  
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```  

---

#### Login response look like this use the access token
**POST** `/api/login`  
Body:  
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzU2NjMwNzA1LCJleHAiOjE3NTY2MzQzMDUsIm5iZiI6MTc1NjYzMDcwNSwianRpIjoiWlF1elZub0c4V3E2YndHSCIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.GoALpUMO357YVOudTU3ObrKH3CVHqJ0TJXpQjJuIU8o",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@gmail.com",
        "role": "admin",
        "email_verified_at": "2025-08-31T07:47:43.000000Z",
        "created_at": "2025-08-31T07:47:43.000000Z",
        "updated_at": "2025-08-31T07:47:43.000000Z"
    }
}
```  

---

### 🏢 Employer Endpoints  

#### Post a Job  
**POST** `/api/employer/post-job`  
```json
{
  "title": "Frontend Developer",
  "description": "React & Laravel experience required",
  "salary_range": "50000",
  "location": "Dhaka"
}
```  
#### View Companies all Job
**GET** `/api/employer/jobs`

#### Edit a Job
**GET** `/api/employer/{id}/edit-job`  

#### Update a Job
**PUT** `/api/employer/{id}/update-job`

#### Delete a Job
**DELETE** `/api/employer/{id}/delete-job`  

---
### 👨‍💼 Applicant/Joseeker Endpoints

#### View own Applications  
**GET** `/api/jobseeker/applications`  

#### View all jobs  
**GET** `/api/jobseeker/jobs`  

#### View a job
**GET** `/api/jobseeker/jobs/{id}/view`  


####  Apply for a job  
**POST** `/api/jobseeker/jobs/{id}/apply`  
  
Body (form-data):  
```
cv: (upload file.pdf/.doc/.docx)
```  

### 💳 Payments 

####  Application Payment 
**POST** `/jobseeker/jobs/{id}/pay`

- 💵 Application Fee: **100 Taka**  
- Stores application with `payment_status`  
- Creates a **basic invoice**  

#### Example Invoice Object  
```json
{
  "id": 101,
  "user_id": 2,
  "amount": 100,
  "status": "paid",
  "created_at": "2025-08-30 15:22:00"
}
```  


---
### 🏢 Employer Endpoints  

#### View Applicants  Job
**GET** `/api/employer/applicants`  

#### Accept Application
**PATCH** `api/employer/applications/{id}/accept`  

#### Reject Application
**PATCH** `api/employer/applications/{id}/reject`


### 🏢 Admin Endpoints 

### Manage all User

#### View all User 
**GET** `/api/admin/users/{id}/edit`  

#### Edit a User
**GET** `/api/admin/users/{id}/edit`  

#### Update a User
**PUT** `/api/admin/users/{id}/update`
**can not update email address and role**

```json
{
"name": "Job Seeker updated",
"password": "12345678"
    }
``` 


#### Delete a User
**DELETE** `/api/admin/users/{id}/delete`

### Manage all Job
#### View all Job 
**GET** `/api/admin/jobs`  

#### Edit a Job
**GET** `/api/admin/jobs/{id}/edit`  

#### Update a Job
**PUT** `/api/admin/jobs/{id}/update`

#### Delete a Job
**DELETE** `/api/admin/jobs/{id}/delete`  


#### Filter Applications  
**GET** `/api/admin/applications?status=accepted&company_id=5`  

- `status` → `paid | pending | rejected | accepted`  
- `company_id` → Employer’s ID  


#### Filter Jobs  
**GET** `/api/admin/jobs?status=paid&company_id=5`  

- `status` → `paid | active | inactive`  
- `company_id` → Employer’s ID 

#### Company analytics
**GET** `/api/admin/analytics` 

#### Google analytics
**GET** `/api/admin/google-analytics`  






## 🧪 Testing with Postman  

1. Open **Postman**  
2. Set `Content-Type: application/json`  
3. Use `Authorization: Bearer <your_token>` for protected routes  
4. Import provided Postman collection (if available)  


