-- Script d'initialisation des bases de données PostgreSQL
-- Ce script crée une base de données par service (database per service pattern)

-- Création de la base de données auth_service
CREATE DATABASE auth_db;

-- Création de la base de données asset_service
CREATE DATABASE asset_db;

-- Création de la base de données movement_service
CREATE DATABASE movement_db;

-- Création de la base de données inventory_service
CREATE DATABASE inventory_db;

-- Création de la base de données maintenance_service
CREATE DATABASE maintenance_db;

-- Création de la base de données reporting_service
CREATE DATABASE reporting_db;
