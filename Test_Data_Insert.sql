INSERT INTO user VALUES
("asmith", "abc12345", "Addy",  "Smith", "asmith@123.com", 1231231234, 1231231234, 'M', 0, 1),
("badams", "abc12345", "Ben",  "Adams", "badams@123.com", 1231231234, 1231231234, 'M', 0, 1),
("jcombs", "abc12345", "Julie",  "Combs", "jcombs@123.com", 1231231234, 1231231234, 'M', 0, 1),
("rmoney", "abc12345", "Rain",  "Money", "makeitrain@123.com", 1231231234, 1231231234, 'T', 0, 1),
("dboss", "abc12345", "Da",  "Boss", "dboss@123.com", 1231231234, 1231231234, 'A', 0, 1),
("dmigrate", "abc12345", "Data",  "Migration", "dmigrate@123.com", 1231231234, 1231231234, 'D', 0, 1),
("admin", "abc12345", "Da",  "Boss", "dboss@123.com", 1231231234, 1231231234, 'A', 0, 1),
("cpassword", "abc12345", "Change",  "Password", "cpassword@123.com", 1231231234, 1231231234, 'M', 1, 1);

update user set password=sha2('tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8abc12345', 256) where user_id="asmith";
update user set password=sha2('tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8abc12345', 256) where user_id="badams";
update user set password=sha2('tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8abc12345', 256) where user_id="jcombs";
update user set password=sha2('tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8abc12345', 256) where user_id="rmoney";
update user set password=sha2('tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8abc12345', 256) where user_id="dboss";
update user set password=sha2('tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8abc12345', 256) where user_id="dmigrate";
update user set password=sha2('tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8abc12345', 256) where user_id="cpassword";

INSERT INTO client VALUES
(NULL, "badams", "John", "Doe", NULL, "Married", '1968-12-12', 1111, 1231231235, 1231231236, 1231231237, '2008-12-12', "St. Raphael", 1),
(NULL, "badams", "Kathy", "Doe", NULL, "Married", '1968-1-1', 1114, 1231231235, 1231231236, 1231231237, '2008-12-12', "St. Raphael", 0),
(NULL, "asmith", "Mary", "Stevens", "Jane", "Single", '1968-1-13', 1112, 1231231238, 1231231239, 1231231240, '2006-7-12', "SS. Peter & Paul", 0),
(NULL, "jcombs", "Julie", "Johnson", "Rose", "Divorced", '1973-10-3', 1117, 1231231244, 1231231245, 1231231246, '2010-3-8', "St. Raphael", 0),
(NULL, "jcombs", "Jake", "Johnson", NULL, "Divorced", '1970-8-2', 1113, 1231231241, 1231231242, 1231231243, '2010-3-8', "St. Raphael", 0);

INSERT INTO address VALUES
(NULL, 1, "13302 Wellesley Circle", NULL, "Plainfield",'IL', '60585', "Other"),
(NULL, 1, "1310 Brush Hill Circle", NULL, "Naperville", 'IL', '60540', "St. Raphael"),
(NULL, 5, "30 N. Brainard", NULL, "Naperville",'IL', '60540', "SS. Peter & Paul"),
(NULL, 4, "892 Benedetti Dr.", "Apt 101", "Naperville",'IL', '60563', "St. Raphael"),
(NULL, 3, "1204 Brook Lane", NULL, "Naperville",'IL', '60540', "St. Raphael");

INSERT INTO household VALUES
(NULL, 3, 5, 4, 0),
(NULL, 1, 1,2, 1),
(NULL, 5, 3, null, 1),
(NULL, 3, 5, null, 1),
(NULL, 4, 4, null, 1);

INSERT INTO hmember VALUES
(NULL, 1, "Susie", "Johnson", "child", '2000-7-6', NULL),
(NULL, 2, "Jake", "Doe", "child", '2000-7-8', NULL),
(NULL, 3, "Jane", "Stevens", "mother", '1940-3-4', NULL),
(NULL, 1, "Susie", "Johnson", "child", '2004-7-6', NULL),
(NULL, 4, "Becky", "Johnson", "child", '2000-3-4', NULL);

INSERT INTO employment VALUES
(NULL, 1, "North Central College", "Maintenance", '2009-4-6','2011-1-20'),
(NULL, 3, "Private Empolyer", "Nanny", '2000-5-19',NULL),
(NULL, 5, "Nicor", "Security", '2001-8-3', '2011-3-9'),
(NULL, 1, "Nicor", "Maintenance", '2011-4-9', NULL);

INSERT INTO client_comment VALUES
(NULL, 1, "badams", '2011-3-3 01:12:30', "This is a comment placed here "),
(NULL, 4, "asmith", '2011-3-3 20:12:30', "This is a comment placed here "),
(NULL, 4, "asmith", '2011-3-10 20:12:30', "This is a comment placed here "),
(NULL, 5, "asmith", '2011-3-3 03:12:30', "This is a comment placed here "),
(NULL, 3, "jcombs", '2011-3-3 07:12:30', "This is a comment placed here "),
(NULL, 2, "badams", '2011-3-3 13:12:30', "This is a comment placed here" );

INSERT INTO client_case VALUES
(NULL, 1, "badams", '2008-12-12', "Open"),
(NULL, 4, "asmith", '2010-3-8', "Closed");

INSERT INTO case_visit VALUES
(NULL, 1, '2008-12-22', 9.2, 3.3),
(NULL, 2, '2010-3-20', 3.9, 4.5);

INSERT INTO case_visitors VALUES
(1, "badams"),
(1, "jcombs"),
(2, "asmith");

INSERT INTO case_comment VALUES
(NULL, 1, "badams", '2008-12-29 13:24:30', "This is a comment about a case");

INSERT INTO case_need VALUES
(NULL, 1, "Rent", 1000.00),
(NULL, 1, "Auto", 750.00),
(NULL, 2, "Food", 100.00);

INSERT INTO check_request VALUES
(NULL, 1, "badams", '2009-1-9', 100.00, "Please write this check quickly.", "jcombs", NULL, '2009-1-18', "D", "1370498509384", "Lener Apts", "123 abc street", "naperville", 'IL', '60560', 1231237890, "John", "Lener"),
(NULL, 1, "badams", '2009-1-9', 1000.00, "Please write this check quickly.", "jcombs", "7321", '2009-1-18', "I", "1370498509384", "Lener Apts", "123 abc street", "naperville", 'IL', '60560', 1231237890, "John", "Lener"),
(NULL, 2, "asmith", '2010-3-25', 750.00, "Please write this check quickly.", null, null, null, "P","1370498509384", "Toyota", "123 abc street", "naperville", 'IL', '60560', 1231237890, "Mike", "Young");

INSERT INTO referral VALUES
(NULL, 3, '2010-3-8', "Food", "Loaves & Fishes");

INSERT INTO do_not_help VALUES
(3, "badams", '2012-1-1', "Hopping parish help services");

INSERT INTO parish_funds VALUES
(20000.00, 1, 5000.00, 4, 1500.00);

INSERT INTO schedule VALUES
(1, '2012-3-1', "asmith"),
(2, '2012-3-7', "badams"),
(3, '2012-3-14', "jcombs"),
(4, '2012-3-21', "asmith"),
(5, '2012-3-28', "badams");
