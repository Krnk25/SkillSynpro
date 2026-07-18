

CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) DEFAULT NULL,
    degree VARCHAR(100) DEFAULT NULL,
    gender ENUM('Male', 'Female', 'Other') DEFAULT NULL,
    age INT(11) DEFAULT NULL,
    mobile VARCHAR(15) DEFAULT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL UNIQUE,
    username VARCHAR(50) DEFAULT NULL UNIQUE,
    password VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reset_token VARCHAR(100) DEFAULT NULL,
    reset_expire DATETIME DEFAULT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE company_users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    hr_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    company_name VARCHAR(150) DEFAULT NULL,
    industry VARCHAR(100) DEFAULT NULL,
    company_size VARCHAR(50) DEFAULT NULL,
    website VARCHAR(150) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expire DATETIME DEFAULT NULL,
    PRIMARY KEY (id)
);



CREATE TABLE user_profiles (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) DEFAULT NULL,
    name VARCHAR(100) DEFAULT NULL,
    degree VARCHAR(100) DEFAULT NULL,
    gender VARCHAR(20) DEFAULT NULL,
    age INT(11) DEFAULT NULL,
    mobile VARCHAR(15) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    PRIMARY KEY (id),
    KEY user_id_idx (user_id),
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
);


CREATE TABLE company_profiles (
    id INT(11) NOT NULL AUTO_INCREMENT,
    company_user_id INT(11) NOT NULL,
    company_name VARCHAR(150) DEFAULT NULL,
    industry VARCHAR(100) DEFAULT NULL,
    company_size VARCHAR(50) DEFAULT NULL,
    website VARCHAR(150) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY company_user_idx (company_user_id),
    CONSTRAINT fk_company_user FOREIGN KEY (company_user_id) 
        REFERENCES company_users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE jobs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    company_user_id INT(11) NOT NULL,
    title VARCHAR(150) NOT NULL,
    company VARCHAR(150) DEFAULT NULL,
    location VARCHAR(100) DEFAULT NULL,
    required_skills TEXT DEFAULT NULL,
    job_type VARCHAR(50) DEFAULT NULL,
    salary VARCHAR(50) DEFAULT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    skills TEXT DEFAULT NULL,
    PRIMARY KEY (id),
    KEY company_user_idx (company_user_id),
    CONSTRAINT fk_jobs_company_user FOREIGN KEY (company_user_id)
        REFERENCES company_users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO jobs (title, skills) VALUES
('Frontend Developer','HTML,CSS,JavaScript,React,Bootstrap'),
('Backend Developer','PHP,MySQL,API,Laravel'),
('Full Stack Developer','HTML,CSS,JavaScript,PHP,MySQL');


CREATE TABLE resumes (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) DEFAULT NULL,
    filename VARCHAR(255) DEFAULT NULL,
    extracted_text LONGTEXT DEFAULT NULL,
    skills TEXT DEFAULT NULL,
    best_job VARCHAR(255) DEFAULT NULL,
    skills_matched TEXT DEFAULT NULL,
    eligibility INT(11) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_id_idx (user_id),
    CONSTRAINT fk_resumes_user FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE job_applications (
    id INT(11) NOT NULL AUTO_INCREMENT,
    job_id INT(11) DEFAULT NULL,
    student_id INT(11) DEFAULT NULL,
    resume_file VARCHAR(255) DEFAULT NULL,
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    skills TEXT DEFAULT NULL,
    PRIMARY KEY (id),
    KEY job_idx (job_id),
    KEY student_idx (student_id),
    CONSTRAINT fk_applications_job FOREIGN KEY (job_id)
        REFERENCES jobs(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_applications_student FOREIGN KEY (student_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE subscriptions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    plan_name VARCHAR(100) NOT NULL,
    status ENUM('active','expired','cancelled') DEFAULT 'active',
    expiry_date DATE DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_idx (user_id),
    CONSTRAINT fk_plan_history_user FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);



CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100),
    token VARCHAR(255),
    expires_at DATETIME
);



CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_days INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE skills_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill VARCHAR(50)
);

INSERT INTO skills_master (skill) VALUES
('html'),('css'),('javascript'),('react'),('angular'),('php'),('mysql');

CREATE TABLE skill_synonyms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill VARCHAR(50),
    synonym VARCHAR(50)
);



CREATE TABLE resume_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resume_id INT,
    matched_skills TEXT,
    percentage FLOAT
);


CREATE TABLE job_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    skill VARCHAR(50) NOT NULL,
    weight INT NOT NULL
);

INSERT INTO job_skills (job_id, skill, weight) VALUES
(1, 'html', 3),
(1, 'css', 3),
(1, 'javascript', 5),
(1, 'react', 4),
(1, 'angular', 3);


CREATE TABLE resume_job_matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resume_id INT,
    job_id INT,
    matched_skills TEXT,
    eligibility FLOAT
);