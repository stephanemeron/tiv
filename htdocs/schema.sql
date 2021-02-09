-- MySQL dump 10.13  Distrib 5.5.29, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: real_tiv
-- ------------------------------------------------------
-- Server version	5.5.29-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bloc`
--

DROP TABLE IF EXISTS `bloc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bloc` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_club` varchar(16) NULL,
  `nom_proprietaire` varchar(255) NULL,
  `is_club` int(1) NULL,
  `adresse` varchar(255) NULL,
  `constructeur` varchar(128) NULL,
  `marque` varchar(128) NULL,
  `numero` varchar(128) NULL,
  `capacite` varchar(32) NULL,
  `filetage` varchar(32) NULL,
  `id_robinet` int(15) NULL,
  `date_premiere_epreuve` date NULL,
  `date_derniere_epreuve` date NULL,
  `date_dernier_tiv` date NULL,
  `pression_epreuve` int(5) NULL,
  `pression_service` int(5) NULL,
  `gaz` varchar(16) NULL,
  `etat` varchar(16) NULL,
  `etat_int` int(1) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des blocs du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detendeur`
--

DROP TABLE IF EXISTS `detendeur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detendeur` (
  `id` int(15) NOT NULL,
  `id_club` varchar(16) NULL,
  `marque` varchar(128) NULL,
  `etat_1ier_etage` varchar(16) NULL,  
  `id_1ier_etage` varchar(64) NULL,  
  `etat_2e_etage` varchar(16) NULL,
  `id_2e_etage` varchar(64) NULL,
  `etat_octopus` varchar(16) NULL,
  `id_octopus` varchar(64) NULL,
  `etat_direct_system` varchar(16) NULL,
  `etat_mano` varchar(16) NULL,
  `date_achat` date NULL,
  `observation` varchar(255) NULL,
  `embout_enfant` varchar(16) NULL,
  `eaux_froides` int(1) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des detendeurs du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_element` int(11) NULL,
  `type_element` varchar(64) NULL,
  `commentaire` varchar(255) NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inspecteur_tiv`
--

DROP TABLE IF EXISTS `inspecteur_tiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inspecteur_tiv` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NULL,
  `numero_tiv` varchar(16) NULL,
  `adresse_tiv` varchar(255) NULL,
  `telephone_tiv` varchar(32) NULL,
  `date_dernier_tiv` date NULL,
  `date_prochain_recyclage` date NULL,
  `actif` varchar(3) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des inspecteurs TIV du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inspection_tiv`
--

DROP TABLE IF EXISTS `inspection_tiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inspection_tiv` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `id_bloc` int(16) NULL,
  `id_inspecteur_tiv` int(16) NULL,
  `date` date NULL,
  `etat_exterieur` varchar(16) NULL,
  `remarque_exterieur` varchar(255) NULL,
  `etat_interieur` varchar(16) NULL,
  `remarque_interieur` varchar(255) NULL,
  `etat_filetage` varchar(16) NULL,
  `remarque_filetage` varchar(255) NULL,
  `etat_robineterie` varchar(16) NULL,
  `remarque_robineterie` varchar(255) NULL,
  `decision` varchar(16) NULL,
  `remarque` varchar(255) NULL,
  PRIMARY KEY (`id`),
  KEY `id_bloc` (`id_bloc`),
  KEY `id_inspecteur_tiv` (`id_inspecteur_tiv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal_tiv`
--

DROP TABLE IF EXISTS `journal_tiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_tiv` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `date` datetime NULL,
  `element` varchar(64) NULL,
  `id_element` int(16) NULL,
  `message` varchar(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Journal de l''application';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personne`
--

DROP TABLE IF EXISTS `personne`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personne` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `groupe` varchar(32) NULL,
  `licence` varchar(32) NULL,
  `nom` varchar(255) NULL,
  `prenom` varchar(255) NULL,
  `adresse` varchar(255) NULL,
  `code_postal` varchar(16) NULL,
  `ville` varchar(255) NULL,
  `telephone_domicile` varchar(32) NULL,
  `telephone_bureau` varchar(32) NULL,
  `telephone_portable` varchar(32) NULL,
  `email` varchar(255) NULL,
  `date_naissance` date NULL,
  `lieu_naissance` varchar(255) NULL,
  `niveau` varchar(32) NULL,
  `date_obtention_niveau` date NULL,
  `nombre_plongee` int(16) NULL,
  `date_derniere_plongee` date NULL,
  `type_assurance` varchar(32) NULL,
  `date_derniere_maj` date NULL,
  `qualifications` varchar(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Personne du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pret`
--

DROP TABLE IF EXISTS `pret`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pret` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `id_personne` int(16) NULL,
  `id_detendeur` int(16) NULL,
  `id_stab` int(16) NULL,
  `id_bloc` int(16) NULL,
  `debut_pret` date NULL,
  `fin_prevu` date NULL,
  `fin_reel` date NULL,
  `etat` varchar(64) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des prêts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stab`
--

DROP TABLE IF EXISTS `stab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stab` (
  `id` int(15) NOT NULL,
  `modele` varchar(128) NULL,
  `taille` varchar(32) NULL,
  `date_achat` date NULL,
  `observation` varchar(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des stabs du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `robinet`
--

DROP TABLE IF EXISTS `robinet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `robinet` (
  `id` int(15) NOT NULL,
  `marque` varchar(128) NULL,
  `serial_number` varchar(32) NULL,
  `filetage` varchar(32) NULL,
  `nb_sortie` int(1) NULL,
  `filetage_sortie` varchar(25) NULL,
  `observation` varchar(255) NULL,
  `spec_robinet` varchar(32) NULL,
  `net_ultrason` int(1) NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des robinets du club';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-02-18 22:48:34
