<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "auth_system";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


/*CREATE DATABASE auth_system;
USE auth_system;


CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_email VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_email) REFERENCES employers(email) ON DELETE CASCADE ON UPDATE CASCADE
);




CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  verification_code VARCHAR(6),
  is_verified TINYINT(1) DEFAULT 0,
  reset_token VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 


INSERT INTO jobs (employer_email, title, company, location, description)
VALUES
('despostcompany@gmail.com', 'Front-End Web Developer', 'Despost Company Ltd', 'Nairobi', 'We are looking for a skilled front-end developer proficient in React.js and Tailwind CSS.'),
('despostcompany@gmail.com', 'Graphic Designer', 'Despost Company Ltd', 'Mombasa', 'Creative designer needed for digital marketing materials and brand visuals.'),
('despostcompany@gmail.com', 'IT Support Specialist', 'Despost Company Ltd', 'Kisumu', 'Provide technical support and troubleshoot hardware/software issues.'),
('despostcompany@gmail.com', 'Mobile App Developer', 'Despost Company Ltd', 'Nakuru', 'Develop and maintain Android applications using Java or Kotlin.'),
('despostcompany@gmail.com', 'Backend Developer (PHP)', 'Despost Company Ltd', 'Nairobi', 'Work on backend APIs and database management using PHP and MySQL.'),
('despostcompany@gmail.com', 'Digital Marketing Executive', 'Despost Company Ltd', 'Eldoret', 'Plan and execute social media marketing campaigns and SEO strategies.'),
('despostcompany@gmail.com', 'Data Analyst', 'Despost Company Ltd', 'Thika', 'Analyze data trends and generate reports for business decision-making.');


CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('read','unread') DEFAULT 'unread',
    FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
);
                 // ✅ Add welcome notification
        $title = "Welcome to JobFinder!";
        $message = "Hello $name, welcome to JobFinder. Start exploring the latest job opportunities today!";

        $insert_notif = "INSERT INTO notifications (user_email, title, message)
                         VALUES ('$email', '$title', '$message')";
        mysqli_query($conn, $insert_notif);

INSERT INTO notifications (user_email, title, message, status) VALUES
('mwangiantony414@gmail.com', 'Job Application Received', 'Your application for Web Developer was successfully received.', 'unread'),
('mwangiantony414@gmail.com', 'Interview Scheduled', 'Your interview for Frontend Developer is scheduled for Monday, 21 Oct at 10:00 AM.', 'unread'),
('mwangiantony414@gmail.com', 'Job Matched', 'A new Backend Developer job matches your profile.', 'unread'),
('mwangiantony414@gmail.com', 'Resume Viewed', 'Your resume was viewed by TechCorp.', 'read'),
('mwangiantony414@gmail.com', 'Profile Complete', 'Your profile is 80% complete. Add skills to improve visibility.', 'unread');

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
);

INSERT INTO posts (user_email, content) VALUES
('mwangiantony414@gmail.com', 'Excited to start applying for new Web Developer jobs today!'),
('mwangiantony414@gmail.com', 'Just updated my resume and portfolio.'),
('mwangiantony414@gmail.com', 'Attended a great online course on React.js yesterday.'),
('mwangiantony414@gmail.com', 'Looking for internship opportunities in Nairobi.'),
('mwangiantony414@gmail.com', 'My recent project on PHP and MySQL is live!'),
('mwangiantony414@gmail.com', 'Networking is key! Reached out to 5 recruiters today.'),
('mwangiantony414@gmail.com', 'Read an article about job trends in 2025 — very insightful.'),
('mwangiantony414@gmail.com', 'Applied to 3 frontend positions this morning. Fingers crossed!'),
('mwangiantony414@gmail.com', 'Started learning Tailwind CSS for building modern UIs.'),
('mwangiantony414@gmail.com', 'Feeling motivated to improve my coding skills every day.');



CREATE TABLE employers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  company_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  contact_number VARCHAR(20),
  verify ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


UPDATE employers SET verify='verified' WHERE email='despostcompany@gmail.com';


CREATE TABLE applicants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_name VARCHAR(100) NOT NULL,
    applicant_email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    applied_on DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'notified', 'shortlisted', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE employer_updates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  emply_email VARCHAR(100) NOT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('read', 'unread') DEFAULT 'unread',
  FOREIGN KEY (emply_email) REFERENCES employers(email) ON DELETE CASCADE ON UPDATE CASCADE
);







CREATE TABLE IF NOT EXISTS fraud_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reporter_email VARCHAR(150) NOT NULL,
  reported_entity VARCHAR(255) NOT NULL,
  details TEXT NOT NULL,
  evidence_path VARCHAR(255),
  status ENUM('new','under_review','closed') DEFAULT 'new',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (reporter_email) REFERENCES users(email) ON DELETE CASCADE
) ;


CREATE TABLE IF NOT EXISTS help_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_email VARCHAR(150) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('open','answered','closed') DEFAULT 'open',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
) ;



CREATE TABLE IF NOT EXISTS communities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  owner_email VARCHAR(150) NOT NULL,
  is_private TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_email) REFERENCES users(email) ON DELETE CASCADE
) ;

CREATE TABLE IF NOT EXISTS community_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  community_id INT NOT NULL,
  user_email VARCHAR(150) NOT NULL,
  joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  role ENUM('member','moderator','owner') DEFAULT 'member',
  FOREIGN KEY (community_id) REFERENCES communities(id) ON DELETE CASCADE,
  FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
) ;

CREATE TABLE IF NOT EXISTS community_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  community_id INT NOT NULL,
  user_email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (community_id) REFERENCES communities(id) ON DELETE CASCADE,
  FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
) ;

CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(150) NOT NULL,
    profile_picture LONGBLOB DEFAULT NULL,
    resume LONGBLOB DEFAULT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE user_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(150) NOT NULL,
    skill VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE user_bio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(150) NOT NULL,
    bio TEXT NOT NULL,
    FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE user_schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(150) NOT NULL,
    school_name VARCHAR(150) NOT NULL,
    degree VARCHAR(100),
    start_year YEAR,
    end_year YEAR,
    FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE ON UPDATE CASCADE
);


*/

?>
