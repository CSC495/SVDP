INSERT INTO user VALUES 
("asmith", "abc123", "Addy",  "Smith", "asmith@123.com", "1231231234", "1231231234", 'A', 0, 1),
("badams", "abc123", "Ben",  "Adams", "badams@123.com", "1231231234", "1231231234", 'M', 0, 1),
("jcombs", "abc123", "Julie",  "Combs", "jcombs@123.com", "1231231234", "1231231234", 'T', 1, 1);

INSERT INTO client VALUES 
(1, "badams", "John", "Doe", NULL, 1, '1968-12-12', 1111, "1231231235", "1231231236", "1231231237", '2008-12-12', "St. Vincent DePaul", 1),
(2, "badams", "Kathy", "Doe", NULL, 1, '1968-1-1', 1114, "1231231235", "1231231236", "1231231237", '2008-12-12', "St. Vincent DePaul", 0),
(3, "asmith", "Mary", "Stevens", "Jane", 0, '1968-1-13', 1112, "1231231238", "1231231239", "1231231240", '2006-7-12', "St. Peter and Paul", 0),
(4, "jcombs", "Julie", "Johnson", "Rose", 0, '1973-10-3', 1117, "1231231244", "1231231245", "1231231246", '2010-3-8', "St. Vincent DePaul", 0),
(5, "jcombs", "Jake", "Johnson", NULL, 0, '1970-8-2', 1113, "1231231241", "1231231242", "1231231243", '2010-3-8', "St. Vincent DePaul", 0);


INSERT INTO address VALUES
(1, 1, "13302 Wellesley Circle", NULL, "Plainfield",'IL', '60585', "St. Peter and Paul"),
(2, 1, "1310 Brush Hill Circle", NULL, "Naperville", 'IL', '60540', "St. Vincent DePaul"),
(3, 5, "30 N. Brainard", NULL, "Naperville",'IL', '60540', "St. Vincent DePaul"),
(4, 4, "892 Benedetti Dr.", "Apt 101", "Naperville",'IL', '60563', "St. Vincent DePaul"),
(5, 3, "1204 Brook Lane", NULL, "Naperville",'IL', '60540', "St. Vincent DePaul");

INSERT INTO household VALUES
(1, 3, 5, 4, 0),
(2, 1, 1,2, 1),
(3, 5, 3, null, 1),
(4, 3, 5, null, 1),
(5, 4, 4, null, 1);

INSERT INTO hmember VALUES
(1, 1, "Susie", "Johnson", "child", '2000-7-6', NULL),
(2, 2, "Jake", "Doe", "child", '2000-7-8', NULL),
(3, 3, "Jane", "Stevens", "mother", '1940-3-4', NULL),
(4, 1, "Susie", "Johnson", "child", '2004-7-6', NULL),
(5, 4, "Becky", "Johnson", "child", '2000-3-4', NULL);

INSERT INTO employment VALUES
("1", 1, "North Central College", "Maintenance", '2009-4-6','2011-1-20'),
("2", 3, "Private Empolyer", "Nanny", '2000-5-19',NULL),
("3", 5, "Nicor", "Security", '2001-8-3', '2011-3-9'),
("4", 1, "Nicor", "Maintenance", '2011-4-9', NULL);

INSERT INTO client_comment VALUES
("1", 1, "badams", '2011-3-3 01:12:30', "This is a comment placed here "),
("2", 4, "asmith", '2011-3-3 20:12:30', "This is a comment placed here "),
("6", 4, "asmith", '2011-3-10 20:12:30', "This is a comment placed here "),
("3", 5, "asmith", '2011-3-3 03:12:30', "This is a comment placed here "),
("4", 3, "jcombs", '2011-3-3 07:12:30', "This is a comment placed here "),
("5", 2, "badams", '2011-3-3 13:12:30', "This is a comment placed here" );

INSERT INTO client_case VALUES
(1, 1, "badams", '2008-12-12', "Open"),
(2, 4, "asmith", '2010-3-8', "Closed");

INSERT INTO case_visit VALUES
(1, 1, '2008-12-22', 9, 3),
(2, 2, '2010-3-20', 3, 4);

INSERT INTO case_visitors VALUES
(1, "badams"),
(1, "jcombs"),
(2, "asmith");

INSERT INTO case_comment VALUES
("comment1", 1, "badams", '2008-12-29 13:24:30', "This is a comment about a case");

INSERT INTO case_need VALUES
(1, 1, "rent", 1000.00),
(2, 1, "car payment", 750.00),
(3, 2, "food", 100.00);

INSERT INTO check_request VALUES
(1, 1, "badams", '2009-1-9', 1000.00, "I hate making comments. please write this check quickly.", "jcombs", "7321", '2009-1-18', "1370498509384", "Lener Apts", "123 abc street", "naperville", 'IL', '60560', "1231237890", "John", "Lener"),
(2, 2, "asmith", '2010-3-25', 750.00, "I hate making comments. please write this check quickly.", null, null, null, "1370498509384", "Toyota", "123 abc street", "naperville", 'IL', '60560', "1231237890", "Mike", "Young");

INSERT INTO referral VALUES
(1, 3, '2010-3-8', "food needs", "Loaves and Fishes");

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

