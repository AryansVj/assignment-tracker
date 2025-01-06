
CREATE DATABASE AssignmentTracker;
USE AssignmentTracker;

CREATE TABLE Roles (
    role_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    role_title VARCHAR(128),
    PRIMARY KEY(role_id)
) ENGINE = InnoDB;

CREATE TABLE Groups (
    group_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    group_name VARCHAR(128),
    PRIMARY KEY(group_id)
) ENGINE = InnoDB;

CREATE TABLE People (
    person_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(128),
    role_id INT UNSIGNED,
    group_id INT UNSIGNED,
    PRIMARY KEY(person_id),
    INDEX USING BTREE (name),

    CONSTRAINT FOREIGN KEY(role_id) REFERENCES Roles (role_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY(group_id) REFERENCES Groups (group_id) ON DELETE CASCADE ON UPDATE CASCADE    
) ENGINE = InnoDB;

CREATE TABLE AssignmentCategories (
    category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_title VARCHAR(128),
    PRIMARY KEY (category_id)
)ENGINE = InnoDB ;

CREATE TABLE PerformanceLevels (
    performace_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    levels VARCHAR(128),
    PRIMARY KEY (performace_id)
) ENGINE = InnoDB;

CREATE TABLE Weeks (
    week_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    weekly_date DATE UNIQUE,
    special_notes VARCHAR(255), 
    PRIMARY KEY (week_id)
) ENGINE = InnoDB;

CREATE TABLE Status (
    status_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    status_descriptor VARCHAR(128),
    PRIMARY KEY (status_id)
) ENGINE = InnoDB;

CREATE TABLE WeeklyTracker (
    assignment_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    person_id INT UNSIGNED,
    category_id INT UNSIGNED,
    assistant_id INT UNSIGNED,
    week_id INT UNSIGNED,
    status_id INT UNSIGNED,
    performace_id INT UNSIGNED,
    hall INT,

    PRIMARY KEY (assignment_id),

    CONSTRAINT FOREIGN KEY(person_id) REFERENCES People(person_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY(category_id) REFERENCES AssignmentCategories(category_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY(assistant_id) REFERENCES People(person_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY(week_id) REFERENCES Weeks(week_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY(status_id) REFERENCES Status(status_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY(performace_id) REFERENCES PerformanceLevels(performace_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB ;