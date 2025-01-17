INSERT INTO Users (name, email, phone, address, password) VALUES
('Maria García', 'maria.garcia@email.com', '+34 611 234 567', 'Calle Mayor 123, Madrid, Spain', 'securePass123'),
('Hans Schmidt', 'hans.schmidt@email.com', '+49 170 1234567', 'Hauptstraße 45, Berlin, Germany', 'deutschPass456'),
('Sophie Dubois', 'sophie.dubois@email.com', '+33 6 12 34 56 78', '15 Rue de la Paix, Paris, France', 'françaisPass789'),
('Marco Rossi', 'marco.rossi@email.com', '+39 333 123 4567', 'Via Roma 67, Rome, Italy', 'italiaPass101'),
('Anna Kowalski', 'anna.kowalski@email.com', '+48 512 345 678', 'ul. Długa 7, Warsaw, Poland', 'polskaPass202');

-- Now insert the transactions
INSERT INTO Transactions (date, type, description, userid_added, userid_for, amount) VALUES
-- Maria lends to Hans
('2025-01-01 10:00:00', 'Lend', 'Short term loan for car repair', 1, 2, 500.00),

-- Hans borrows from Sophie
('2025-01-02 15:30:00', 'Borrow', 'Emergency home repairs', 2, 3, 1000.00),

-- Sophie lends to Marco
('2025-01-03 09:15:00', 'Lend', 'Business startup loan', 3, 4, 2000.00),

-- Marco borrows from Anna
('2025-01-04 14:20:00', 'Borrow', 'Education course fees', 4, 5, 1500.00),

-- Anna lends to Maria
('2025-01-05 11:45:00', 'Lend', 'Wedding expenses support', 5, 1, 3000.00),

-- Maria borrows from Hans
('2025-01-06 16:00:00', 'borrow', 'House down payment', 1, 2, 5000.00),

-- Hans lends to Sophie
('2025-01-07 12:30:00', 'Lend', 'Vacation expenses', 2, 3, 800.00),

-- Sophie borrows from Marco
('2025-01-08 17:20:00', 'Borrow', 'New laptop purchase', 3, 4, 1200.00),

-- Marco lends to Anna
('2025-01-09 13:10:00', 'Lend', 'Medical expenses', 4, 5, 2500.00),

-- Anna borrows from Maria
('2025-01-10 10:45:00', 'Borrow', 'Home renovation loan', 5, 1, 4000.00);
