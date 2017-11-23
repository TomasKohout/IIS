SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS osetrovatel;
DROP TABLE IF EXISTS cisteni;
DROP TABLE IF EXISTS krmeni;
DROP TABLE IF EXISTS skoleni;
DROP TABLE IF EXISTS vybeh;
DROP TABLE IF EXISTS typ_vybehu;
DROP TABLE IF EXISTS druh_zvirete;
DROP TABLE IF EXISTS zvire;
DROP TABLE IF EXISTS dobrovolnik;
DROP TABLE IF EXISTS zamestnanec;
DROP TABLE IF EXISTS provadi_cisteni;
DROP TABLE IF EXISTS provadi_krmeni;
DROP TABLE IF EXISTS ma_skoleni;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE skoleni (
  id_skoleni int NOT NULL AUTO_INCREMENT ,
  nazev varchar(30) NOT NULL,
  datum datetime NOT NULL,
  popis varchar(200),
  PRIMARY KEY (id_skoleni)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE druh_zvirete (
  id_druh_zvirete int NOT NULL AUTO_INCREMENT,
  naSkoleni int NOT NULL,
  nazev varchar(30) NOT NULL,
  vyskyt varchar(30),
  PRIMARY KEY (id_druh_zvirete),
  CONSTRAINT FK_NaSkoleniDruhZvirete FOREIGN KEY (naSkoleni) REFERENCES skoleni(id_skoleni)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE typ_vybehu (
  id_typ_vybehu int NOT NULL AUTO_INCREMENT,
  naSkoleni int NOT NULL,
  nazev varchar(30) NOT NULL UNIQUE,
  pocet_osetrovatelu int NOT NULL,
  pomucka_k_cisteni varchar(30),
  doba_cisteni int,
  PRIMARY KEY (id_typ_vybehu),
  CONSTRAINT FK_NaSkoleniTypVybehu FOREIGN KEY (naSkoleni) REFERENCES skoleni(id_skoleni)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE vybeh (
  id_vybeh int NOT NULL AUTO_INCREMENT,
  naTypVybehu int NOT NULL,
  poloha varchar(30) NOT NULL,
  rozloha int NOT NULL,
  popis varchar(200),
  PRIMARY KEY (id_vybeh),
  CONSTRAINT FK_NaTypVybehuVybeh FOREIGN KEY (naTypVybehu) REFERENCES typ_vybehu(id_typ_vybehu)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE zvire (
  id_zvire int NOT NULL AUTO_INCREMENT,
  obyva int NULL,
  jeDruhu int NOT NULL,
  jmeno varchar(30) NOT NULL,
  pohlavi varchar(1) NOT NULL,
  vaha int NOT NULL,
  vyska int NOT NULL,
  zeme_puvodu VARCHAR(30) NOT NULL,
  jmeno_matky VARCHAR(30),
  jmeno_otce VARCHAR(30),
  datum_narozeni DATE NOT NULL,
  datum_umrti DATE,
  PRIMARY KEY (id_zvire),
  CONSTRAINT FK_ObyvaZvire FOREIGN KEY (obyva) REFERENCES vybeh(id_vybeh),
  CONSTRAINT FK_JeDruhuZvire FOREIGN KEY (jeDruhu) REFERENCES druh_zvirete(id_druh_zvirete)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE krmeni (
  id_krmeni int NOT NULL AUTO_INCREMENT,
  jeKrmeno int NOT NULL,
  datum date NOT NULL,
  druh varchar(30) NOT NULL,
  mnozstvi int NOT NULL,
  PRIMARY KEY (id_krmeni),
  CONSTRAINT FK_JeKrmenoKrmeni FOREIGN KEY (jeKrmeno) REFERENCES zvire(id_zvire)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE cisteni (
  id_cisteni int NOT NULL AUTO_INCREMENT,
  jeCisten int NOT NULL,
  datum date NOT NULL,
  PRIMARY KEY(id_cisteni),
  CONSTRAINT FK_JeCistenCisteni FOREIGN KEY (jeCisten) REFERENCES vybeh(id_vybeh)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE osetrovatel (
  role INT(1) NOT NULL,
  login VARCHAR(255) NOT NULL UNIQUE,
  heslo VARCHAR(255) NOT NULL,
  rodne_cislo VARCHAR(10) NOT NULL,
  jmeno varchar(20) NOT NULL,
  prijmeni varchar(30) NOT NULL,
  datum_narozeni date NOT NULL,
  titul varchar(15),
  adresa varchar(30) NOT NULL,
  tel_cislo int NOT NULL,
  pohlavi varchar(1) NOT NULL,
  datum_nastupu date NOT NULL,
  datum_odchodu date,
  CONSTRAINT PK_Osetrovatel PRIMARY KEY(rodne_cislo)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE ma_skoleni (
  id int NOT NULL AUTO_INCREMENT,
  rd_osetrovatel VARCHAR(10) NOT NULL,
  id_skoleni int NOT NULL,
  PRIMARY KEY(id),
  CONSTRAINT FK_RdOsetrovatelMaSkoleni FOREIGN KEY (rd_osetrovatel) REFERENCES osetrovatel(rodne_cislo),
  CONSTRAINT FK_IdSkoleniMaSkoleni FOREIGN KEY (id_skoleni) REFERENCES skoleni(id_skoleni)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE provadi_krmeni (
  id int NOT NULL AUTO_INCREMENT,
  rd_osetrovatel VARCHAR(10) NOT NULL,
  id_krmeni int NOT NULL,
  provedl TINYINT(1) DEFAULT '0',
  PRIMARY KEY(id),
  CONSTRAINT FK_RdOsetrovatelProvadiKrmeni FOREIGN KEY (rd_osetrovatel) REFERENCES osetrovatel(rodne_cislo),
  CONSTRAINT FK_IdKrmeniProvadiKrmeni FOREIGN KEY (id_krmeni) REFERENCES krmeni(id_krmeni)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE provadi_cisteni (
  id int NOT NULL AUTO_INCREMENT,
  rd_osetrovatel VARCHAR(10) NOT NULL,
  id_cisteni int NOT NULL,
  provedl TINYINT(1) DEFAULT '0',
  PRIMARY KEY(id),
  CONSTRAINT FK_RdOsetrovatelProvadiCisteni FOREIGN KEY (rd_osetrovatel) REFERENCES osetrovatel(rodne_cislo),
  CONSTRAINT FK_IdCisteniProvadiCisteni FOREIGN KEY (id_cisteni) REFERENCES cisteni(id_cisteni)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE dobrovolnik (
  osetrovatel VARCHAR(10) NOT NULL,
  organizace VARCHAR(30),
  zodpovedna_osoba VARCHAR(10) NOT NULL,
  PRIMARY KEY(osetrovatel),
  CONSTRAINT FK_ZodpovednaOsoba FOREIGN KEY (zodpovedna_osoba) REFERENCES osetrovatel(rodne_cislo),
  CONSTRAINT FK_RDPropojeniDobrovolnik FOREIGN KEY (osetrovatel) REFERENCES osetrovatel(rodne_cislo)
)ENGINE=InnoDB, CHARSET=utf8;

CREATE TABLE zamestnanec (
  osetrovatel VARCHAR(10),
  mzda int,
  pozice VARCHAR(25),
  specializace VARCHAR(25),
  PRIMARY KEY(osetrovatel),
  CONSTRAINT FK_RDPropojeniZamestnanec FOREIGN KEY (osetrovatel) REFERENCES osetrovatel(rodne_cislo)
)ENGINE=InnoDB, CHARSET=utf8;

INSERT INTO skoleni (nazev,datum,popis) VALUES('Papoušci','2010.10.18','');
INSERT INTO skoleni (nazev,datum,popis) VALUES('Vodní živočichové','2006.10.18','Školení na vodní živočichy.');
INSERT INTO skoleni (nazev,datum,popis) VALUES('Ptáci','2011.1.5','Základní informace o krmivu a dalším stravování ptáků.');
INSERT INTO skoleni (nazev,datum,popis) VALUES('Želvy','2012.10.18','');
INSERT INTO skoleni (nazev,datum,popis) VALUES('Koně','2015.11.18','');
INSERT INTO skoleni (nazev,datum,popis) VALUES('A','2010.10.18','Nejmenší typy (Voliery,klícky).');
INSERT INTO skoleni (nazev,datum,popis) VALUES('B','2006.10.18','Střední typy (Klece,Voliréry,Menší výběhy).');
INSERT INTO skoleni (nazev,datum,popis) VALUES('C','2011.1.5','Velké typy (Venkovní výběhy)');


INSERT INTO druh_zvirete (nazev,vyskyt,naSkoleni) VALUES('Papoušci','Exotické krajiny','1');
INSERT INTO druh_zvirete (nazev,vyskyt,naSkoleni) VALUES('Vodní živočichové','Ve vodě','2');
INSERT INTO druh_zvirete (nazev,vyskyt,naSkoleni) VALUES('Ptáci','Všude','3');
INSERT INTO druh_zvirete (nazev,vyskyt,naSkoleni) VALUES('Želvy','Exotické krajiny','4');
INSERT INTO druh_zvirete (nazev,vyskyt,naSkoleni) VALUES('Koně','Všude','5');
INSERT INTO druh_zvirete (nazev,vyskyt,naSkoleni) VALUES('Osli','Všude','5');

INSERT INTO osetrovatel (role, login, heslo,rodne_cislo, jmeno, prijmeni, datum_narozeni, titul, adresa, tel_cislo, pohlavi, datum_nastupu, datum_odchodu) VALUES('2','xkohou08','1234567','9509121237', 'Tomáš', 'Kohout', '1995.09.12', 'Ing.', 'Pod mostem 84/2, Brno 123 45', '721503535', 'M', '2007.07.07', NULL);
INSERT INTO osetrovatel (role, login, heslo,rodne_cislo, jmeno, prijmeni, datum_narozeni, titul, adresa, tel_cislo, pohlavi, datum_nastupu, datum_odchodu) VALUES('1','xblaze31','1234567','9508041235', 'Tomáš', 'Blažek', '1995.08.04', 'Ing.', 'Nad mostem 42, Brno 123 45', '123456789' , 'M' , '2008.08.08', NULL);
INSERT INTO osetrovatel (role, login, heslo,rodne_cislo, jmeno, prijmeni, datum_narozeni, titul, adresa, tel_cislo, pohlavi, datum_nastupu, datum_odchodu) VALUES('2','xkrest07','1234567','9509141235', 'Tamara', 'Krestianková', '1995.09.14', 'Ing.' , 'Vedle mostu 42, Brno 123 45', '998767865', 'Z', '2009.09.09', NULL);
INSERT INTO osetrovatel (role, login, heslo,rodne_cislo, jmeno, prijmeni, datum_narozeni, titul, adresa, tel_cislo, pohlavi, datum_nastupu, datum_odchodu) VALUES('1','xkozel01','1234567','9501011234', 'Vojta', 'Kozel', '1995.01.01', 'Bc.', 'Pod zemí 42, Brno 424 24', '723747882', 'M', '2010.10.10', NULL);
INSERT INTO osetrovatel (role, login, heslo,rodne_cislo, jmeno, prijmeni, datum_narozeni, titul, adresa, tel_cislo, pohlavi, datum_nastupu, datum_odchodu) VALUES('1','xbures01','1234567','9502021232', 'Adéla', 'Burešová', '1995.02.02', 'Mgr.', 'Pod zemí 42, Brno 424 24', '721009321', 'Z', '2011.11.11', NULL);
INSERT INTO osetrovatel (role, login, heslo,rodne_cislo, jmeno, prijmeni, datum_narozeni, titul, adresa, tel_cislo, pohlavi, datum_nastupu, datum_odchodu) VALUES('1','xdobia01','1234567','8712121231', 'Roman', 'Dobiáš', '1987.12.12', 'Doc.Ing.Csc.', 'Nad zemí 42, Brno 424 24', '607876543', 'M', '2004.09.19', NULL);
INSERT INTO osetrovatel (role, login, heslo,rodne_cislo, jmeno, prijmeni, datum_narozeni, titul, adresa, tel_cislo, pohlavi, datum_nastupu, datum_odchodu) VALUES('1','xbabja02','1234567','7002021224', 'Lenka', 'Babjovčáková', '1970.02.02', 'Mgr.', 'Plzeňská 87, Brno 123 45', '678345213', 'Z', '2005.07.29', NULL);
INSERT INTO osetrovatel (role, login, heslo,rodne_cislo, jmeno, prijmeni, datum_narozeni, titul, adresa, tel_cislo, pohlavi, datum_nastupu, datum_odchodu) VALUES('0','admin','admin','7002021235', 'Admin', 'Admin', '2011.11.11', 'Mgr.', 'Plzeňská 87, Brno 123 45', '678345213', 'Z', '2005.07.29', NULL);

INSERT INTO dobrovolnik (osetrovatel, organizace, zodpovedna_osoba) VALUES ('9509121237', 'Green Peace', '9508041235');
INSERT INTO dobrovolnik (osetrovatel, organizace, zodpovedna_osoba) VALUES ('9509141235', 'Green Peace', '9502021232');

INSERT INTO zamestnanec (osetrovatel, mzda, pozice, specializace) VALUES ('9508041235', '11000', 'Výkonný ředitel', 'Divoké kočky');
INSERT INTO zamestnanec (osetrovatel, mzda, pozice, specializace) VALUES ('9501011234', '110000', 'Zoolog', 'Vodní ptáci');
INSERT INTO zamestnanec (osetrovatel, mzda, pozice, specializace) VALUES ('9502021232', '42000', 'Zoolog', 'Ptáci');
INSERT INTO zamestnanec (osetrovatel, mzda, pozice, specializace) VALUES ('8712121231', '1100000', 'Vrchní zoolog', 'Želvy');
INSERT INTO zamestnanec (osetrovatel, mzda, pozice, specializace) VALUES ('7002021224', '55000', 'Sekretářka', '');

INSERT INTO typ_vybehu (naSkoleni,nazev,pocet_osetrovatelu,pomucka_k_cisteni,doba_cisteni) VALUES('6','A','1','Malá sada','30');
INSERT INTO typ_vybehu (naSkoleni,nazev,pocet_osetrovatelu,pomucka_k_cisteni,doba_cisteni) VALUES('7','B','2','Střední sada','120');
INSERT INTO typ_vybehu (naSkoleni,nazev,pocet_osetrovatelu,pomucka_k_cisteni,doba_cisteni) VALUES('8','C','3','Velká sada','300');
INSERT INTO typ_vybehu (naSkoleni,nazev,pocet_osetrovatelu,pomucka_k_cisteni,doba_cisteni) VALUES('8','Ohrada','1','Velká sada','30');

INSERT INTO vybeh (naTypVybehu,poloha,rozloha,popis) VALUES('1','Pavilon-A','1','');
INSERT INTO vybeh (naTypVybehu,poloha,rozloha,popis) VALUES('2','Pavilon-B','5','Moderni typ vyběhu.');
INSERT INTO vybeh (naTypVybehu,poloha,rozloha,popis) VALUES('2','Pavilon-B','6','Jeden z prvnich výběhů v naší ZOO.');
INSERT INTO vybeh (naTypVybehu,poloha,rozloha,popis) VALUES('3','Pavilon-C','20','');
INSERT INTO vybeh (naTypVybehu,poloha,rozloha,popis) VALUES('3','Pavilon-C','40','');

INSERT INTO cisteni(jeCisten,datum) VALUES('1','2014.3.12');
INSERT INTO cisteni(jeCisten,datum) VALUES('2','2014.3.1');
INSERT INTO cisteni(jeCisten,datum) VALUES('3','2014.5.22');
INSERT INTO cisteni(jeCisten,datum) VALUES('1','2014.3.18');
INSERT INTO cisteni(jeCisten,datum) VALUES('2','2014.7.13');


INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('9508041235', '1');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('9501011234', '2');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('9502021232', '3');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('8712121231', '4');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('8712121231', '5');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('9508041235', '6');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('9501011234', '6');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('9502021232', '7');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('8712121231', '7');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('8712121231', '8');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('9501011234', '8');
INSERT INTO ma_skoleni(rd_osetrovatel, id_skoleni) VALUES ('9508041235', '8');



INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('1', '3', 'Bambino', 'M', '3', '4', 'Belgie', 'Pepa', 'Sisa','2006.02.21');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('2', '1', 'Kongo', 'M', '3', '7', 'Belgie', 'Karel', 'Linda','2006.02.11');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('3', '4', 'Žvak', 'M', '3', '4', 'Belgie', 'Ondra', 'Sisa','2006.03.01');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('4', '6', 'Fňak', 'M', '78', '40', 'Belgie', 'Tomáš', 'Lisa','2007.02.02');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('4', '5', 'Bobek', 'M', '30', '4', 'Belgie', 'Jura', 'Sara','2007.04.30');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('2', '2', 'Bob', 'M', '13', '14', 'Belgie', 'Dušan', 'Susan','2007.08.23');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('3', '4', 'Božena', 'M', '36', '48', 'Belgie', 'Pepa', 'Veronika','2006.09.20');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('1', '3', 'Kobliha', 'Z', '23', '44', 'Belgie', 'Olda', 'Monika','2006.01.23');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('2', '1', 'Milena', 'Z', '18', '14', 'Belgie', 'Jarda', 'Sisa','2006.09.08');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('3', '4', 'Růža', 'Z', '3', '6', 'Belgie', 'Karel', 'Karolína','2008.01.01');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('4', '6', 'Alena', 'Z', '2', '1', 'Belgie', 'Pepa', 'Jarmila','2008.2.18');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('4', '5', 'Lenka', 'Z', '11', '22', 'Belgie', 'Pepa', 'Lila','2006.2.16');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('2', '2', 'Lea', 'Z', '17', '19', 'Belgie', 'Gugu', 'Risa','2007.01.05');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('3', '4', 'Džena', 'Z', '5', '7', 'Belgie', 'Fufu', 'Sira','2007.02.21');
INSERT INTO zvire (obyva, jeDruhu, jmeno, pohlavi, vaha, vyska, zeme_puvodu, jmeno_otce, jmeno_matky, datum_narozeni) VALUES ('1', '1', 'Božena', 'Z', '7', '8', 'Belgie', 'Fufin', 'Pipi','2006.02.27');


INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('1', '2008.12.20', 'zrní', '50');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('2', '2008.12.21', 'zrní', '20');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('3', '2008.12.22', 'tráva', '20');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('4', '2008.12.21', 'seno', '2000');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('5', '2008.12.24', 'seno', '6000');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('6', '2008.12.26', 'brouci', '40');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('7', '2008.12.01', 'salát', '200');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('8', '2008.12.20', 'zrní', '50');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('9', '2008.12.21', 'zrní', '20');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('10', '2008.12.21', 'tráva', '20');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('11', '2008.12.21', 'seno', '2000');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('12', '2008.12.21', 'seno', '6000');
INSERT INTO krmeni (jeKrmeno, datum, druh, mnozstvi) VALUES ('13', '2008.12.11', 'salát', '150');


INSERT INTO provadi_krmeni (rd_osetrovatel, id_krmeni) VALUES ('7002021224', '1');
INSERT INTO provadi_krmeni (rd_osetrovatel, id_krmeni) VALUES ('9502021232', '2');
INSERT INTO provadi_krmeni (rd_osetrovatel, id_krmeni) VALUES ('9501011234', '3');
INSERT INTO provadi_krmeni (rd_osetrovatel, id_krmeni) VALUES ('9509121237', '4');

INSERT INTO provadi_cisteni (rd_osetrovatel, id_cisteni) VALUES ('7002021224', '1');
INSERT INTO provadi_cisteni (rd_osetrovatel, id_cisteni) VALUES ('9502021232', '2');
INSERT INTO provadi_cisteni (rd_osetrovatel, id_cisteni) VALUES ('9501011234', '3');
INSERT INTO provadi_cisteni (rd_osetrovatel, id_cisteni) VALUES ('9509121237', '4');
INSERT INTO provadi_cisteni (rd_osetrovatel, id_cisteni) VALUES ('9509121237', '5');

