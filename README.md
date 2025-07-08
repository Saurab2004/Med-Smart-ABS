echo "# Med-Smart-HMS" >> README.md
git init
git add README.md
git commit -m "first commit"
git branch -M main
git remote add origin https://github.com/iamsandeep27/Med-Smart-HMS.git
git push -u origin main
 

 git remote add origin https://github.com/iamsandeep27/Med-Smart-HMS.git
git branch -M main
git push -u origin main
/////////////////////////////////////////////////////////////////////////////
aba cloning garna paryo 
?
git clone https://github.com/iamsandeep27/Med-Smart-HMS.git
?"
 git push -u origin 
  

Started Date : 
Date 2025/07/03 
 index.html, style.css, script.js,

 /// Database name : MedSmart

 database table :appointments

 CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  age INT NOT NULL,
  gender VARCHAR(10) NOT NULL,
  department VARCHAR(50) NOT NULL,
  contact_number VARCHAR(20) NOT NULL,
  email VARCHAR(100) NOT NULL,
  blood_group VARCHAR(10) NOT NULL,
  appointment_date DATE NOT NULL,
  appointment_time TIME NOT NULL,
  transaction_code VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
  //////////////////////admins
  CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL
);




 image ma screen short xa


for doctor :::
 Username: doctor

Password: medsmart123