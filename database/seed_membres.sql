SET @hash_defaut := '$2y$10$7AaNjAlLP3jQS7TvZxI.quHeS.CFwzNrP2s0TKZwciJzRZAINh1vK';
INSERT INTO membres (nom, prenom, categorie_id, identifiant, mot_de_passe, role) VALUES
('Sock', 'Moussa', 1, 'moussa.sock', @hash_defaut, 'membre'),
('Niang', 'Babacar', 1, 'babacar.niang', @hash_defaut, 'membre'),
('Wade', 'Cheikhou', 1, 'cheikhou.wade', @hash_defaut, 'membre'),
('Sock', 'Ablaye', 1, 'ablaye.sock', @hash_defaut, 'membre'),
('Sock', 'Mathar', 1, 'mathar.sock', @hash_defaut, 'membre'),
('Sarr', 'Khaly', 1, 'khaly.sarr', @hash_defaut, 'membre'),
('Sarr', 'Issa', 1, 'issa.sarr', @hash_defaut, 'membre'),
('Sarr', 'Mansour', 1, 'mansour.sarr', @hash_defaut, 'membre'),
('Diouf', 'Moulaye', 1, 'moulaye.diouf', @hash_defaut, 'membre'),
('Sarr', 'Lamine', 1, 'lamine.sarr', @hash_defaut, 'membre'),
('Sarr', 'Mamadou', 1, 'mamadou.sarr', @hash_defaut, 'membre');

INSERT INTO membres (nom, prenom, categorie_id, identifiant, mot_de_passe, role) VALUES
('Sarr', 'Wely', 2, 'wely.sarr', @hash_defaut, 'membre'),
('Ndiaye', 'Maman', 2, 'maman.ndiaye', @hash_defaut, 'membre'),
('Dieng', 'Aida', 2, 'aida.dieng', @hash_defaut, 'membre'),
('Ndella', 'Maman', 2, 'maman.ndella', @hash_defaut, 'membre'),
('Dieng', 'Ouly', 2, 'ouly.dieng', @hash_defaut, 'membre'),
('Sock', 'Khadiatou', 2, 'khadiatou.sock', @hash_defaut, 'membre'),
('Sock', 'Ndeye', 2, 'ndeye.sock1', @hash_defaut, 'membre'),
('Dia', 'Awa', 2, 'awa.dia', @hash_defaut, 'membre'),
('Bou Khess', 'Fatou', 2, 'fatou.boukhess', @hash_defaut, 'membre'),
('Kobar', 'Maman', 2, 'maman.kobar', @hash_defaut, 'membre'),
('Sarr', 'Nguissaly', 2, 'nguissaly.sarr', @hash_defaut, 'membre');

INSERT INTO membres (nom, prenom, categorie_id, identifiant, mot_de_passe, role) VALUES
('Sarr', 'Ablaye', 3, 'ablaye.sarr', @hash_defaut, 'membre'),
('Maguette', 'Pape', 3, 'pape.maguette', @hash_defaut, 'membre'),
('Diona', 'Ameth', 3, 'ameth.diona', @hash_defaut, 'membre'),
('Fama', 'Ameth', 3, 'ameth.fama', @hash_defaut, 'membre');

INSERT INTO membres (nom, prenom, categorie_id, identifiant, mot_de_passe, role) VALUES
('Sock', 'Yaram', 4, 'yaram.sock', @hash_defaut, 'membre'),
('Ndiaye', 'Ameth', 4, 'ameth.ndiaye', @hash_defaut, 'membre'),
('Sarr', 'Ibreu', 4, 'ibreu.sarr', @hash_defaut, 'membre'),
('Sock', 'Mankeur', 4, 'mankeur.sock', @hash_defaut, 'membre'),
('Sarr', 'Leyti', 4, 'leyti.sarr', @hash_defaut, 'membre'),
('', 'Babacar', 4, 'babacar', @hash_defaut, 'membre'),
('Sabelle', 'Maman', 4, 'maman.sabelle', @hash_defaut, 'membre'),
('Bou Ndaw', 'Ameth', 4, 'ameth.bouNdaw', @hash_defaut, 'membre'),
('Faye', 'Rokhaya', 4, 'rokhaya.faye', @hash_defaut, 'membre'),
('Sock', 'Ndeye', 4, 'ndeye.sock2', @hash_defaut, 'membre'),
('Diecka', 'Mame', 4, 'mame.diecka', @hash_defaut, 'membre');
