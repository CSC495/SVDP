CREATE DATABASE svdp;

USE svdp;

GRANT select, insert, delete, update
 ON *
 TO 'webuser'@'localhost';


DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS address;
DROP TABLE IF EXISTS household;
DROP TABLE IF EXISTS hmember;
DROP TABLE IF EXISTS employment;
DROP TABLE IF EXISTS client_comment;
DROP TABLE IF EXISTS client_case;
DROP TABLE IF EXISTS case_comment;
DROP TABLE IF EXISTS case_visit;
DROP TABLE IF EXISTS case_visitors;
DROP TABLE IF EXISTS case_need;
DROP TABLE IF EXISTS check_request;
DROP TABLE IF EXISTS referral;
DROP TABLE IF EXISTS do_not_help;
DROP TABLE IF EXISTS case_need;
DROP TABLE IF EXISTS fund_limit;

CREATE TABLE user(
     user_id VARCHAR(30) NOT NULL,
     password VARCHAR(256),
	first_name VARCHAR(30),
	last_name VARCHAR(30),
	email VARCHAR(100),
	cell_phone CHAR(10),
	home_phone CHAR(10),
	role CHAR(1),
	change_pswd TINYINT(1),
	active_flag TINYINT(1),
	PRIMARY KEY (user_id)
) ENGINE = InnoDB;

CREATE TABLE client(
     client_id INT NOT NULL AUTO_INCREMENT,
	created_user_id VARCHAR(30),
	first_name VARCHAR(30),
	last_name VARCHAR(30),
	other_name VARCHAR(30),
	marriage_status VARCHAR(15),
	birthdate DATE,
	ssn4 INT,
	cell_phone CHAR(10),
	home_phone CHAR(10),
	work_phone CHAR(10),
	created_date DATE,
	member_parish VARCHAR(50),
	veteran_flag TINYINT(1),
     PRIMARY KEY (client_id),
     FOREIGN KEY (created_user_id) REFERENCES user(user_id)
) ENGINE = InnoDB;

CREATE TABLE address(
	address_id INT NOT NULL AUTO_INCREMENT,
	client_id INT,
	street VARCHAR(100),
	apt VARCHAR(30),
	city VARCHAR(50),
	state CHAR(2),
	zipcode CHAR(5),
	reside_parish VARCHAR(50),
	PRIMARY KEY (address_id)
)ENGINE = 	InnoDB;


CREATE TABLE household(
	household_id INT NOT NULL AUTO_INCREMENT,
	address_id INT,
     	mainclient_id INT,
	spouse_id INT,
	current_flag TINYINT(1),
 	PRIMARY KEY (household_id),
     	FOREIGN KEY (address_id) REFERENCES address(address_id),
     	FOREIGN KEY (mainclient_id) REFERENCES client(client_id)
) ENGINE = InnoDB;


CREATE TABLE hmember(
     	hmember_id INT NOT NULL AUTO_INCREMENT,
	household_id INT,
	first_name VARCHAR(30),
	last_name VARCHAR(30),
	relationship VARCHAR(30),
     	birthdate DATE,
	left_date DATE,
     	PRIMARY KEY (hmember_id),
     	FOREIGN KEY (household_id) REFERENCES household(household_id)
) ENGINE = InnoDB;

CREATE TABLE employment(
	employment_id INT NOT NULL AUTO_INCREMENT,
	client_id INT,
	company VARCHAR(50),
	position VARCHAR(50),
	start_date DATE,
	end_date DATE,
	PRIMARY KEY (employment_id),
	FOREIGN KEY (client_id) REFERENCES client(client_id)
) ENGINE = InnoDB;


CREATE TABLE client_comment(
     clientcomment_id INT NOT NULL AUTO_INCREMENT,
     client_id INT,
	user_id VARCHAR(30),
     comment_date DATETIME,
     comment TEXT,
     PRIMARY KEY (clientcomment_id),
     FOREIGN KEY (user_id) REFERENCES user(user_id),
     FOREIGN KEY (client_id) REFERENCES client(client_id)
) ENGINE = InnoDB;


CREATE TABLE client_case(
     case_id INT NOT NULL AUTO_INCREMENT,
     household_id INT,
     opened_user_id VARCHAR(30),
     opened_date DATE,
     status VARCHAR(10),
     PRIMARY KEY (case_id),
     FOREIGN KEY (household_id) REFERENCES household(household_id),
     FOREIGN KEY (opened_user_id) REFERENCES user(user_id)
) ENGINE = InnoDB;

CREATE TABLE case_visit(
	visit_id INT NOT NULL AUTO_INCREMENT,
	case_id INT,
	visit_date DATE,
     	miles DECIMAL(5,2),
     	hours DECIMAL(5,2),
	PRIMARY KEY (visit_id),
	FOREIGN KEY (case_id) REFERENCES client_case(case_id)
) ENGINE = InnoDB;

CREATE TABLE case_visitors(
	visit_id INT,
	user_id VARCHAR(30),
	PRIMARY KEY(visit_id, user_id),
	FOREIGN KEY(visit_id) REFERENCES case_visit(visit_id),
	FOREIGN KEY(user_id) REFERENCES user(user_id)
)ENGINE = InnoDB;

CREATE TABLE case_comment(
     	casecomment_id INT NOT NULL AUTO_INCREMENT,
     	case_id INT,
	user_id VARCHAR(30),
     	case_date DATETIME,
     	comment TEXT,
     	PRIMARY KEY (casecomment_id),
     	FOREIGN KEY (case_id) REFERENCES client_case(case_id),
	FOREIGN KEY (user_id) REFERENCES user(user_id)
) ENGINE = InnoDB;


CREATE TABLE case_need(
        caseneed_id INT NOT NULL AUTO_INCREMENT,
        case_id INT,
        need VARCHAR(30),
        amount DECIMAL(7,2),
     	PRIMARY KEY (caseneed_id),
     	FOREIGN KEY (case_id) REFERENCES client_case(case_id)
) ENGINE = InnoDB;


CREATE TABLE check_request(
     	checkrequest_id INT NOT NULL AUTO_INCREMENT,
     	caseneed_id INT,
	user_id VARCHAR(30),
     	request_date DATE,
     	amount DECIMAL(7,2),
     	comment TEXT,
     	signee_userid VARCHAR(30),
     	check_number VARCHAR(15),
     	issue_date DATE,
	status CHAR(1),
	account_number VARCHAR(30),
     	payee_name VARCHAR(50),
     	street VARCHAR(100),
     	city VARCHAR(50),
     	state CHAR(2),
     	zipcode CHAR(5),
     	phone CHAR(10),
     	contact_fname VARCHAR(30),
     	contact_lname VARCHAR(30),
     	PRIMARY KEY (checkrequest_id),
     	FOREIGN KEY (caseneed_id) REFERENCES case_need(caseneed_id),
     	FOREIGN KEY (user_id) REFERENCES user(user_id)
) ENGINE = InnoDB;


CREATE TABLE referral(
        referral_id INT NOT NULL AUTO_INCREMENT,
        caseneed_id INT,
        referred_date DATE,
        reason TEXT,
        referred_to VARCHAR(256),
     	PRIMARY KEY (referral_id),
     	FOREIGN KEY (caseneed_id) REFERENCES case_need(caseneed_id)
) ENGINE = InnoDB;


CREATE TABLE do_not_help(
        client_id INT,
        create_user_id VARCHAR(30),
        added_date DATE,
        reason TEXT,
        PRIMARY KEY (client_id),
        FOREIGN KEY (create_user_id) REFERENCES user(user_id)
) ENGINE = InnoDB;


CREATE TABLE parish_funds(
        available_funds DECIMAL(8,2),
        year_limit INT,
        lifetime_limit DECIMAL(7,2),
        case_limit INT,
	casefund_limit DECIMAL(7,2)
) ENGINE = InnoDB;

CREATE TABLE schedule(
	week_id INT NOT NULL AUTO_INCREMENT,
	start_date DATE,
	user_id VARCHAR(30),
	PRIMARY KEY (week_id),
	FOREIGN KEY (user_id) REFERENCES user(user_id)
) ENGINE = InnoDB;

CREATE TABLE documents(
	doc_id INT NOT NULL AUTO_INCREMENT,
	filename VARCHAR(50),
	url VARCHAR(2083),
	internal_flag TINYINT(1),
	PRIMARY KEY (doc_id)
) ENGINE = InnoDB;

INSERT INTO user VALUES
("admin", "abc12345", "Default",  "Administrator", "noreply@raphaelsvdp.org", 1231231234, 1231231234, 'A', 0, 1),
("migrate", "abc12345", "User",  "Migration", "migrate@raphaelsvdp.org", 1231231234, 1231231234, 'M', 0, 1);
update user set password=sha2('tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8abc12345', 256) where user_id="admin";
update user set password=sha2('tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8abc12345', 256) where user_id="migrate";

