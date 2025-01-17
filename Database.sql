CREATE TABLE Users (
    userid INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (LENGTH(password) >= 8),
    CHECK (email LIKE '%@%.%')
);

CREATE TABLE Transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    userid_added INT NOT NULL,
    userid_for INT NOT NULL,
    FOREIGN KEY (userid_added) REFERENCES Users(userid) 
        ON DELETE NO ACTION 
        ON UPDATE CASCADE,
    FOREIGN KEY (userid_for) REFERENCES Users(userid) 
        ON DELETE NO ACTION 
        ON UPDATE CASCADE,
    CONSTRAINT transactions_chk_1 CHECK (type IN ('lend', 'borrow'))
);

CREATE INDEX idx_transactions_userid_added ON Transactions(userid_added);
CREATE INDEX idx_transactions_userid_for ON Transactions(userid_for);
CREATE INDEX idx_transactions_date ON Transactions(date);

DELIMITER //
CREATE TRIGGER tr_check_different_users
BEFORE INSERT ON Transactions
FOR EACH ROW
BEGIN
    IF NEW.userid_added = NEW.userid_for THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'userid_added and userid_for must be different';
    END IF;
END;//
DELIMITER ;