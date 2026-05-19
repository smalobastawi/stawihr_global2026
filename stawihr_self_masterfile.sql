-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.2.2-MariaDB - MariaDB Server
-- Server OS:                    Win64
-- HeidiSQL Version:             12.14.0.7165
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;



-- Dumping structure for table stawi_self_client.absentees
DROP TABLE IF EXISTS `absentees`;
CREATE TABLE IF NOT EXISTS `absentees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '2026-05-19',
  `employee_id` int(10) unsigned NOT NULL,
  `absence_description` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absentees_date_employee_id_unique` (`date`,`employee_id`),
  KEY `absentees_location_id_foreign` (`location_id`),
  KEY `absentees_company_id_foreign` (`company_id`),
  CONSTRAINT `absentees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `absentees_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.absentees: ~0 rows (approximately)
DELETE FROM `absentees`;

-- Dumping structure for table stawi_self_client.activity_log
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(191) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(191) DEFAULT NULL,
  `event` varchar(191) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(191) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`),
  KEY `activity_log_company_id_foreign` (`company_id`),
  CONSTRAINT `activity_log_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.activity_log: ~0 rows (approximately)
DELETE FROM `activity_log`;
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`, `company_id`) VALUES
	(1, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 1, NULL, NULL, '{"attributes":{"id":1,"name":"January 2026","period_type":"monthly","start_date":"2025-12-31T21:00:00.000000Z","end_date":"2026-01-30T21:00:00.000000Z","input_period_start":"2025-12-25T21:00:00.000000Z","input_period_end":"2026-01-24T21:00:00.000000Z","month_number":1,"week_number":1,"biweekly_number":1,"pay_date":"2026-02-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(2, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 2, NULL, NULL, '{"attributes":{"id":2,"name":"February 2026","period_type":"monthly","start_date":"2026-01-31T21:00:00.000000Z","end_date":"2026-02-27T21:00:00.000000Z","input_period_start":"2026-01-25T21:00:00.000000Z","input_period_end":"2026-02-24T21:00:00.000000Z","month_number":2,"week_number":5,"biweekly_number":3,"pay_date":"2026-03-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(3, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 3, NULL, NULL, '{"attributes":{"id":3,"name":"March 2026","period_type":"monthly","start_date":"2026-02-28T21:00:00.000000Z","end_date":"2026-03-30T21:00:00.000000Z","input_period_start":"2026-02-25T21:00:00.000000Z","input_period_end":"2026-03-24T21:00:00.000000Z","month_number":3,"week_number":9,"biweekly_number":5,"pay_date":"2026-04-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(4, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 4, NULL, NULL, '{"attributes":{"id":4,"name":"April 2026","period_type":"monthly","start_date":"2026-03-31T21:00:00.000000Z","end_date":"2026-04-29T21:00:00.000000Z","input_period_start":"2026-03-25T21:00:00.000000Z","input_period_end":"2026-04-24T21:00:00.000000Z","month_number":4,"week_number":14,"biweekly_number":7,"pay_date":"2026-05-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(5, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 5, NULL, NULL, '{"attributes":{"id":5,"name":"May 2026","period_type":"monthly","start_date":"2026-04-30T21:00:00.000000Z","end_date":"2026-05-30T21:00:00.000000Z","input_period_start":"2026-04-25T21:00:00.000000Z","input_period_end":"2026-05-24T21:00:00.000000Z","month_number":5,"week_number":18,"biweekly_number":9,"pay_date":"2026-06-04T21:00:00.000000Z","status":"open","is_current":true,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(6, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 6, NULL, NULL, '{"attributes":{"id":6,"name":"June 2026","period_type":"monthly","start_date":"2026-05-31T21:00:00.000000Z","end_date":"2026-06-29T21:00:00.000000Z","input_period_start":"2026-05-25T21:00:00.000000Z","input_period_end":"2026-06-24T21:00:00.000000Z","month_number":6,"week_number":23,"biweekly_number":11,"pay_date":"2026-07-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(7, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 7, NULL, NULL, '{"attributes":{"id":7,"name":"July 2026","period_type":"monthly","start_date":"2026-06-30T21:00:00.000000Z","end_date":"2026-07-30T21:00:00.000000Z","input_period_start":"2026-06-25T21:00:00.000000Z","input_period_end":"2026-07-24T21:00:00.000000Z","month_number":7,"week_number":27,"biweekly_number":13,"pay_date":"2026-08-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(8, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 8, NULL, NULL, '{"attributes":{"id":8,"name":"August 2026","period_type":"monthly","start_date":"2026-07-31T21:00:00.000000Z","end_date":"2026-08-30T21:00:00.000000Z","input_period_start":"2026-07-25T21:00:00.000000Z","input_period_end":"2026-08-24T21:00:00.000000Z","month_number":8,"week_number":31,"biweekly_number":16,"pay_date":"2026-09-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(9, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 9, NULL, NULL, '{"attributes":{"id":9,"name":"September 2026","period_type":"monthly","start_date":"2026-08-31T21:00:00.000000Z","end_date":"2026-09-29T21:00:00.000000Z","input_period_start":"2026-08-25T21:00:00.000000Z","input_period_end":"2026-09-24T21:00:00.000000Z","month_number":9,"week_number":36,"biweekly_number":18,"pay_date":"2026-10-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(10, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 10, NULL, NULL, '{"attributes":{"id":10,"name":"October 2026","period_type":"monthly","start_date":"2026-09-30T21:00:00.000000Z","end_date":"2026-10-30T21:00:00.000000Z","input_period_start":"2026-09-25T21:00:00.000000Z","input_period_end":"2026-10-24T21:00:00.000000Z","month_number":10,"week_number":40,"biweekly_number":20,"pay_date":"2026-11-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(11, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 11, NULL, NULL, '{"attributes":{"id":11,"name":"November 2026","period_type":"monthly","start_date":"2026-10-31T21:00:00.000000Z","end_date":"2026-11-29T21:00:00.000000Z","input_period_start":"2026-10-25T21:00:00.000000Z","input_period_end":"2026-11-24T21:00:00.000000Z","month_number":11,"week_number":44,"biweekly_number":22,"pay_date":"2026-12-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL),
	(12, 'default', 'created', 'App\\Models\\Payroll\\PayrollPeriod', 'created', 12, NULL, NULL, '{"attributes":{"id":12,"name":"December 2026","period_type":"monthly","start_date":"2026-11-30T21:00:00.000000Z","end_date":"2026-12-30T21:00:00.000000Z","input_period_start":"2026-11-25T21:00:00.000000Z","input_period_end":"2026-12-24T21:00:00.000000Z","month_number":12,"week_number":49,"biweekly_number":24,"pay_date":"2027-01-04T21:00:00.000000Z","status":"open","is_current":false,"created_by":1,"updated_by":null,"created_at":"2026-05-19T15:07:03.000000Z","updated_at":"2026-05-19T15:07:03.000000Z","deleted_at":null,"approval_status":0,"date_approved":null,"approved_by":null,"company_id":null}}', 'af3246b9-e1f4-4aca-9d1e-11de675a7ad1', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL);

-- Dumping structure for table stawi_self_client.advanced_leave_records
DROP TABLE IF EXISTS `advanced_leave_records`;
CREATE TABLE IF NOT EXISTS `advanced_leave_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `financial_year_id` bigint(20) unsigned NOT NULL,
  `advanced_days` decimal(8,2) NOT NULL DEFAULT 0.00,
  `recovered_days` decimal(8,2) NOT NULL DEFAULT 0.00,
  `transactions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`transactions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `adv_leave_records_unique` (`employee_id`,`leave_type_id`,`financial_year_id`),
  KEY `advanced_leave_records_leave_type_id_foreign` (`leave_type_id`),
  KEY `advanced_leave_records_financial_year_id_foreign` (`financial_year_id`),
  CONSTRAINT `advanced_leave_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `advanced_leave_records_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `advanced_leave_records_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_type` (`leave_type_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.advanced_leave_records: ~0 rows (approximately)
DELETE FROM `advanced_leave_records`;

-- Dumping structure for table stawi_self_client.allowance
DROP TABLE IF EXISTS `allowance`;
CREATE TABLE IF NOT EXISTS `allowance` (
  `allowance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `allowance_name` varchar(250) NOT NULL,
  `allowance_type` varchar(100) NOT NULL,
  `percentage_of_basic` double NOT NULL,
  `limit_per_month` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`allowance_id`),
  KEY `allowance_location_id_foreign` (`location_id`),
  KEY `allowance_company_id_foreign` (`company_id`),
  CONSTRAINT `allowance_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `allowance_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.allowance: ~0 rows (approximately)
DELETE FROM `allowance`;

-- Dumping structure for table stawi_self_client.allowance_types
DROP TABLE IF EXISTS `allowance_types`;
CREATE TABLE IF NOT EXISTS `allowance_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `default_calculation_type` enum('fixed','percentage','formula') NOT NULL DEFAULT 'fixed',
  `default_amount` decimal(10,2) DEFAULT NULL,
  `default_percentage` decimal(5,2) DEFAULT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 1,
  `is_pensionable` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `allowance_types_code_unique` (`code`),
  KEY `allowance_types_created_by_foreign` (`created_by`),
  KEY `allowance_types_updated_by_foreign` (`updated_by`),
  KEY `allowance_types_company_id_foreign` (`company_id`),
  CONSTRAINT `allowance_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `allowance_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `allowance_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.allowance_types: ~0 rows (approximately)
DELETE FROM `allowance_types`;

-- Dumping structure for table stawi_self_client.anonymous_feedback
DROP TABLE IF EXISTS `anonymous_feedback`;
CREATE TABLE IF NOT EXISTS `anonymous_feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `action_type` varchar(191) DEFAULT NULL,
  `action_description` text DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `anonymous_feedback_category_id_foreign` (`category_id`),
  KEY `anonymous_feedback_company_id_foreign` (`company_id`),
  CONSTRAINT `anonymous_feedback_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `feedback_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `anonymous_feedback_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.anonymous_feedback: ~0 rows (approximately)
DELETE FROM `anonymous_feedback`;

-- Dumping structure for table stawi_self_client.app_licenses
DROP TABLE IF EXISTS `app_licenses`;
CREATE TABLE IF NOT EXISTS `app_licenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `license_id` varchar(191) DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `domain` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_licenses_location_id_foreign` (`location_id`),
  KEY `app_licenses_company_id_foreign` (`company_id`),
  CONSTRAINT `app_licenses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `app_licenses_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.app_licenses: ~0 rows (approximately)
DELETE FROM `app_licenses`;

-- Dumping structure for table stawi_self_client.approval_assignments
DROP TABLE IF EXISTS `approval_assignments`;
CREATE TABLE IF NOT EXISTS `approval_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approval_step_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_assignments_approval_step_id_foreign` (`approval_step_id`),
  KEY `approval_assignments_user_id_foreign` (`user_id`),
  KEY `approval_assignments_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_assignments_approval_step_id_foreign` FOREIGN KEY (`approval_step_id`) REFERENCES `approval_steps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_assignments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_assignments: ~0 rows (approximately)
DELETE FROM `approval_assignments`;

-- Dumping structure for table stawi_self_client.approval_delegations
DROP TABLE IF EXISTS `approval_delegations`;
CREATE TABLE IF NOT EXISTS `approval_delegations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `delegate_to_user_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) DEFAULT NULL,
  `delegation_type` varchar(191) NOT NULL DEFAULT 'all',
  `workflow_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `include_submissions` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_delegations_created_by_foreign` (`created_by`),
  KEY `approval_delegations_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `approval_delegations_delegate_to_user_id_is_active_index` (`delegate_to_user_id`,`is_active`),
  KEY `approval_delegations_model_type_is_active_index` (`model_type`,`is_active`),
  CONSTRAINT `approval_delegations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_delegations_delegate_to_user_id_foreign` FOREIGN KEY (`delegate_to_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_delegations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_delegations: ~0 rows (approximately)
DELETE FROM `approval_delegations`;

-- Dumping structure for table stawi_self_client.approval_logs
DROP TABLE IF EXISTS `approval_logs`;
CREATE TABLE IF NOT EXISTS `approval_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approvable_type` varchar(191) NOT NULL,
  `approvable_id` bigint(20) unsigned NOT NULL,
  `approval_step_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `delegated_from_user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(191) NOT NULL,
  `comments` text DEFAULT NULL,
  `batch_id` varchar(191) DEFAULT NULL,
  `action_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_logs_approval_step_id_foreign` (`approval_step_id`),
  KEY `approval_logs_user_id_foreign` (`user_id`),
  KEY `approval_logs_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`),
  KEY `approval_logs_batch_id_index` (`batch_id`),
  KEY `approval_logs_created_by_foreign` (`created_by`),
  KEY `approval_logs_company_id_foreign` (`company_id`),
  KEY `approval_logs_delegated_from_user_id_foreign` (`delegated_from_user_id`),
  CONSTRAINT `approval_logs_approval_step_id_foreign` FOREIGN KEY (`approval_step_id`) REFERENCES `approval_steps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `approval_logs_delegated_from_user_id_foreign` FOREIGN KEY (`delegated_from_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `approval_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_logs: ~0 rows (approximately)
DELETE FROM `approval_logs`;

-- Dumping structure for table stawi_self_client.approval_query_logs
DROP TABLE IF EXISTS `approval_query_logs`;
CREATE TABLE IF NOT EXISTS `approval_query_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `query` text NOT NULL,
  `bindings` text NOT NULL,
  `execution_time` float NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_record_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_query_logs_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_query_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_query_logs: ~0 rows (approximately)
DELETE FROM `approval_query_logs`;

-- Dumping structure for table stawi_self_client.approval_records
DROP TABLE IF EXISTS `approval_records`;
CREATE TABLE IF NOT EXISTS `approval_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `new` longtext DEFAULT NULL,
  `old` longtext DEFAULT NULL,
  `model_type` varchar(191) DEFAULT NULL,
  `approver_user_id` bigint(20) unsigned NOT NULL,
  `stages` bigint(20) NOT NULL COMMENT 'this coincides with the  number of approvers from the approval settings table',
  `response_approver_id` longtext DEFAULT NULL,
  `requested_by` bigint(20) unsigned DEFAULT NULL,
  `approval_notes` longtext DEFAULT NULL,
  `rejection_notes` longtext DEFAULT NULL,
  `action_type` varchar(191) DEFAULT NULL,
  `approver_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`approver_id`)),
  `route_name` varchar(191) DEFAULT NULL,
  `method` varchar(191) NOT NULL,
  `status` enum('decline','approved','pending') NOT NULL DEFAULT 'pending',
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_records_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_records_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_records: ~0 rows (approximately)
DELETE FROM `approval_records`;

-- Dumping structure for table stawi_self_client.approval_request_approvals
DROP TABLE IF EXISTS `approval_request_approvals`;
CREATE TABLE IF NOT EXISTS `approval_request_approvals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approval_request_id` bigint(20) unsigned NOT NULL,
  `approver_id` bigint(20) unsigned NOT NULL,
  `action` enum('approve','decline') NOT NULL DEFAULT 'approve',
  `notes` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_request_approvals_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_request_approvals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_request_approvals: ~0 rows (approximately)
DELETE FROM `approval_request_approvals`;

-- Dumping structure for table stawi_self_client.approval_request_db_queries
DROP TABLE IF EXISTS `approval_request_db_queries`;
CREATE TABLE IF NOT EXISTS `approval_request_db_queries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approval_request_id` bigint(20) unsigned NOT NULL,
  `query` text NOT NULL,
  `bindings` text NOT NULL,
  `execution_time` float NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `changes` text DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_request_db_queries_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_request_db_queries_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_request_db_queries: ~0 rows (approximately)
DELETE FROM `approval_request_db_queries`;

-- Dumping structure for table stawi_self_client.approval_requests
DROP TABLE IF EXISTS `approval_requests`;
CREATE TABLE IF NOT EXISTS `approval_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint(20) unsigned NOT NULL,
  `approval_setting_id` bigint(20) unsigned NOT NULL,
  `request_by` bigint(20) unsigned NOT NULL,
  `request_data` varchar(191) NOT NULL,
  `route_name` varchar(191) NOT NULL,
  `request_method` varchar(191) NOT NULL,
  `action_type` varchar(191) NOT NULL,
  `status` enum('pending','declined','approved') NOT NULL DEFAULT 'pending',
  `effected` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `uri` varchar(191) DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_requests_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_requests_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_requests: ~0 rows (approximately)
DELETE FROM `approval_requests`;

-- Dumping structure for table stawi_self_client.approval_setting_approvers
DROP TABLE IF EXISTS `approval_setting_approvers`;
CREATE TABLE IF NOT EXISTS `approval_setting_approvers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approval_setting_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `module_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_setting_approvers_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_setting_approvers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_setting_approvers: ~0 rows (approximately)
DELETE FROM `approval_setting_approvers`;

-- Dumping structure for table stawi_self_client.approval_settings
DROP TABLE IF EXISTS `approval_settings`;
CREATE TABLE IF NOT EXISTS `approval_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `module_id` varchar(191) DEFAULT NULL,
  `approvers_list` longtext DEFAULT NULL,
  `approver_numbers` bigint(20) unsigned NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_settings_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_settings: ~0 rows (approximately)
DELETE FROM `approval_settings`;

-- Dumping structure for table stawi_self_client.approval_steps
DROP TABLE IF EXISTS `approval_steps`;
CREATE TABLE IF NOT EXISTS `approval_steps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approval_workflow_id` bigint(20) unsigned NOT NULL,
  `type` varchar(191) NOT NULL,
  `level` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_steps_approval_workflow_id_foreign` (`approval_workflow_id`),
  KEY `approval_steps_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_steps_approval_workflow_id_foreign` FOREIGN KEY (`approval_workflow_id`) REFERENCES `approval_workflows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_steps_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_steps: ~0 rows (approximately)
DELETE FROM `approval_steps`;

-- Dumping structure for table stawi_self_client.approval_workflows
DROP TABLE IF EXISTS `approval_workflows`;
CREATE TABLE IF NOT EXISTS `approval_workflows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(191) NOT NULL,
  `reviewer_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`reviewer_config`)),
  `approver_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`approver_config`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_workflows_company_id_foreign` (`company_id`),
  CONSTRAINT `approval_workflows_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approval_workflows: ~0 rows (approximately)
DELETE FROM `approval_workflows`;

-- Dumping structure for table stawi_self_client.approvals
DROP TABLE IF EXISTS `approvals`;
CREATE TABLE IF NOT EXISTS `approvals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approval_name` varchar(191) NOT NULL,
  `action_item` varchar(191) NOT NULL,
  `item_id` varchar(191) NOT NULL,
  `action_type` varchar(191) NOT NULL COMMENT 'creation, deletion, editing, salaryGeneration etc. take action_type from the current route',
  `final_status` int(11) NOT NULL DEFAULT 0 COMMENT '0-pending, 1-approved, 2-send-for-amends,3-rejected ',
  `stage1_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage2_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage3_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage1_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage2_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage3_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage1_approval_comments` varchar(191) DEFAULT NULL,
  `stage2_approval_comments` varchar(191) DEFAULT NULL,
  `stage3_approval_comments` varchar(191) DEFAULT NULL,
  `stage1_approval_date` datetime DEFAULT NULL,
  `stage2_approval_date` datetime DEFAULT NULL,
  `stage3_approval_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approvals_stage1_approved_by_foreign` (`stage1_approved_by`),
  KEY `approvals_stage2_approved_by_foreign` (`stage2_approved_by`),
  KEY `approvals_stage3_approved_by_foreign` (`stage3_approved_by`),
  KEY `approvals_location_id_foreign` (`location_id`),
  KEY `approvals_company_id_foreign` (`company_id`),
  CONSTRAINT `approvals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approvals_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  CONSTRAINT `approvals_stage1_approved_by_foreign` FOREIGN KEY (`stage1_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `approvals_stage2_approved_by_foreign` FOREIGN KEY (`stage2_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `approvals_stage3_approved_by_foreign` FOREIGN KEY (`stage3_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.approvals: ~0 rows (approximately)
DELETE FROM `approvals`;

-- Dumping structure for table stawi_self_client.attendance_locations
DROP TABLE IF EXISTS `attendance_locations`;
CREATE TABLE IF NOT EXISTS `attendance_locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attendance_id` bigint(20) unsigned NOT NULL,
  `country` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `long` varchar(191) DEFAULT NULL,
  `lat` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_locations_location_id_foreign` (`location_id`),
  KEY `attendance_locations_company_id_foreign` (`company_id`),
  CONSTRAINT `attendance_locations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_locations_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.attendance_locations: ~0 rows (approximately)
DELETE FROM `attendance_locations`;

-- Dumping structure for table stawi_self_client.attendance_overtime_approvals
DROP TABLE IF EXISTS `attendance_overtime_approvals`;
CREATE TABLE IF NOT EXISTS `attendance_overtime_approvals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `month` varchar(191) NOT NULL,
  `national_id` varchar(191) NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `department_id` varchar(191) NOT NULL,
  `approved_over_time` varchar(191) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `working_time` varchar(191) DEFAULT NULL,
  `workingHours` varchar(191) DEFAULT NULL,
  `total_time_worked` varchar(191) DEFAULT NULL,
  `is_late` varchar(191) DEFAULT NULL,
  `late_time` int(11) DEFAULT NULL,
  `over_time` int(11) DEFAULT NULL,
  `approval_status` varchar(191) DEFAULT NULL,
  `presence_status` varchar(191) NOT NULL COMMENT 'PRESENT,ABSENT,OFF,AWP, AL, ML, SICK, PL, CL, Training',
  `entry_type` varchar(191) DEFAULT NULL,
  `work_shift_id` varchar(191) DEFAULT NULL,
  `employee_type` varchar(191) DEFAULT NULL,
  `attendance_entry_id` varchar(191) DEFAULT NULL,
  `stage1_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage2_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage3_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage1_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage2_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage3_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage1_approval_comments` varchar(191) DEFAULT NULL,
  `stage2_approval_comments` varchar(191) DEFAULT NULL,
  `stage3_approval_comments` varchar(191) DEFAULT NULL,
  `stage1_approval_date` datetime DEFAULT NULL,
  `stage2_approval_date` datetime DEFAULT NULL,
  `stage3_approval_date` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_overtime_approvals_employee_id_foreign` (`employee_id`),
  KEY `attendance_overtime_approvals_stage1_approved_by_foreign` (`stage1_approved_by`),
  KEY `attendance_overtime_approvals_stage2_approved_by_foreign` (`stage2_approved_by`),
  KEY `attendance_overtime_approvals_stage3_approved_by_foreign` (`stage3_approved_by`),
  KEY `attendance_overtime_approvals_location_id_foreign` (`location_id`),
  KEY `attendance_overtime_approvals_company_id_foreign` (`company_id`),
  CONSTRAINT `attendance_overtime_approvals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_overtime_approvals_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON UPDATE CASCADE,
  CONSTRAINT `attendance_overtime_approvals_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_overtime_approvals_stage1_approved_by_foreign` FOREIGN KEY (`stage1_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `attendance_overtime_approvals_stage2_approved_by_foreign` FOREIGN KEY (`stage2_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `attendance_overtime_approvals_stage3_approved_by_foreign` FOREIGN KEY (`stage3_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.attendance_overtime_approvals: ~0 rows (approximately)
DELETE FROM `attendance_overtime_approvals`;

-- Dumping structure for table stawi_self_client.attendances
DROP TABLE IF EXISTS `attendances`;
CREATE TABLE IF NOT EXISTS `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `month` varchar(191) NOT NULL,
  `national_id` varchar(191) DEFAULT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `department_id` varchar(191) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `lunch_checkin` datetime DEFAULT NULL,
  `working_time` varchar(191) DEFAULT NULL,
  `workingHours` varchar(191) DEFAULT NULL,
  `total_time_worked` varchar(191) DEFAULT NULL,
  `is_late` varchar(191) DEFAULT NULL,
  `late_time` int(11) DEFAULT NULL,
  `over_time` int(11) DEFAULT NULL,
  `approval_status` varchar(191) DEFAULT NULL,
  `presence_status` varchar(191) NOT NULL COMMENT 'PRESENT,ABSENT,OFF,AWP, AL, ML, SICK, PL, CL, Training',
  `sensor_id` varchar(191) DEFAULT NULL,
  `created_by` varchar(191) DEFAULT NULL,
  `updated_by` varchar(191) DEFAULT NULL,
  `approved_by` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `entry_type` varchar(191) NOT NULL DEFAULT '0',
  `work_shift_id` int(11) NOT NULL DEFAULT 0,
  `employee_type` int(11) DEFAULT NULL,
  `tea_checkin` datetime DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `approved_over_time` varchar(191) DEFAULT '0',
  `overtime_approval_by` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `payroll_number` varchar(191) NOT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_employee_id_foreign` (`employee_id`),
  KEY `attendances_location_id_foreign` (`location_id`),
  KEY `attendances_company_id_foreign` (`company_id`),
  CONSTRAINT `attendances_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON UPDATE CASCADE,
  CONSTRAINT `attendances_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.attendances: ~0 rows (approximately)
DELETE FROM `attendances`;

-- Dumping structure for table stawi_self_client.bank_branches
DROP TABLE IF EXISTS `bank_branches`;
CREATE TABLE IF NOT EXISTS `bank_branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_id` bigint(20) unsigned NOT NULL,
  `branch_code` varchar(10) NOT NULL,
  `branch_name` varchar(191) NOT NULL,
  `swift_code` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bank_branches_bank_id_branch_code_unique` (`bank_id`,`branch_code`),
  KEY `bank_branches_bank_id_index` (`bank_id`),
  KEY `bank_branches_company_id_foreign` (`company_id`),
  CONSTRAINT `bank_branches_bank_id_foreign` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_branches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.bank_branches: ~0 rows (approximately)
DELETE FROM `bank_branches`;

-- Dumping structure for table stawi_self_client.banks
DROP TABLE IF EXISTS `banks`;
CREATE TABLE IF NOT EXISTS `banks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `bank_code` varchar(10) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `banks_bank_code_unique` (`bank_code`),
  KEY `banks_company_id_foreign` (`company_id`),
  CONSTRAINT `banks_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.banks: ~0 rows (approximately)
DELETE FROM `banks`;

-- Dumping structure for table stawi_self_client.biometric_devices
DROP TABLE IF EXISTS `biometric_devices`;
CREATE TABLE IF NOT EXISTS `biometric_devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_ip_address` varchar(191) NOT NULL,
  `device_serial` varchar(191) NOT NULL,
  `port` int(11) NOT NULL,
  `device_location` varchar(191) NOT NULL,
  `timeout` int(11) NOT NULL,
  `device_status` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `biometric_devices_location_id_foreign` (`location_id`),
  KEY `biometric_devices_company_id_foreign` (`company_id`),
  CONSTRAINT `biometric_devices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `biometric_devices_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.biometric_devices: ~0 rows (approximately)
DELETE FROM `biometric_devices`;

-- Dumping structure for table stawi_self_client.biometric_run_logs
DROP TABLE IF EXISTS `biometric_run_logs`;
CREATE TABLE IF NOT EXISTS `biometric_run_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `machine_ip` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `biometric_run_logs_location_id_foreign` (`location_id`),
  CONSTRAINT `biometric_run_logs_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.biometric_run_logs: ~0 rows (approximately)
DELETE FROM `biometric_run_logs`;

-- Dumping structure for table stawi_self_client.bonus_setting
DROP TABLE IF EXISTS `bonus_setting`;
CREATE TABLE IF NOT EXISTS `bonus_setting` (
  `bonus_setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `festival_name` varchar(191) NOT NULL,
  `percentage_of_bonus` int(11) NOT NULL,
  `bonus_type` enum('Gross','Basic') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`bonus_setting_id`),
  KEY `bonus_setting_location_id_foreign` (`location_id`),
  KEY `bonus_setting_company_id_foreign` (`company_id`),
  CONSTRAINT `bonus_setting_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bonus_setting_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.bonus_setting: ~0 rows (approximately)
DELETE FROM `bonus_setting`;

-- Dumping structure for table stawi_self_client.companies
DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `domain` varchar(191) DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `status` enum('active','suspended','deleted') NOT NULL DEFAULT 'active',
  `kra_pin` varchar(191) DEFAULT NULL,
  `registration_number` varchar(191) DEFAULT NULL,
  `nssf_employer_number` varchar(191) DEFAULT NULL,
  `shif_employer_code` varchar(191) DEFAULT NULL,
  `employer_number` varchar(191) DEFAULT NULL,
  `nita_registration_number` varchar(191) DEFAULT NULL,
  `ecitizen_identifier` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_domain_unique` (`domain`),
  KEY `companies_company_id_foreign` (`company_id`),
  CONSTRAINT `companies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.companies: ~0 rows (approximately)
DELETE FROM `companies`;
INSERT INTO `companies` (`id`, `name`, `domain`, `country`, `status`, `kra_pin`, `registration_number`, `nssf_employer_number`, `shif_employer_code`, `employer_number`, `nita_registration_number`, `ecitizen_identifier`, `created_at`, `updated_at`, `company_id`) VALUES
	(1, 'StawiTech Solutions', 'stawitech.com', 'United States', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 15:07:01', '2026-05-19 15:07:01', NULL);

-- Dumping structure for table stawi_self_client.company_address_settings
DROP TABLE IF EXISTS `company_address_settings`;
CREATE TABLE IF NOT EXISTS `company_address_settings` (
  `company_address_setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`company_address_setting_id`),
  KEY `company_address_settings_location_id_foreign` (`location_id`),
  KEY `company_address_settings_company_id_foreign` (`company_id`),
  CONSTRAINT `company_address_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_address_settings_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.company_address_settings: ~0 rows (approximately)
DELETE FROM `company_address_settings`;

-- Dumping structure for table stawi_self_client.company_permissions
DROP TABLE IF EXISTS `company_permissions`;
CREATE TABLE IF NOT EXISTS `company_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `permission_name` varchar(191) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_permissions_user_id_foreign` (`user_id`),
  KEY `company_permissions_company_id_foreign` (`company_id`),
  KEY `company_permissions_created_by_foreign` (`created_by`),
  KEY `company_permissions_updated_by_foreign` (`updated_by`),
  CONSTRAINT `company_permissions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `company_permissions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `company_permissions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `company_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.company_permissions: ~0 rows (approximately)
DELETE FROM `company_permissions`;

-- Dumping structure for table stawi_self_client.company_settings
DROP TABLE IF EXISTS `company_settings`;
CREATE TABLE IF NOT EXISTS `company_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `legal_Name` varchar(191) NOT NULL DEFAULT 'Test Company',
  `legal_Address` varchar(191) NOT NULL DEFAULT 'Legal Address 1',
  `official_contact_number` varchar(191) NOT NULL DEFAULT '254712345678',
  `official_email` varchar(191) NOT NULL DEFAULT 'email@example.com',
  `company_contact_name` varchar(191) NOT NULL DEFAULT 'John Does',
  `representative_phone` varchar(191) NOT NULL DEFAULT '254712345678',
  `representative_email` varchar(191) NOT NULL DEFAULT 'email@example.com',
  `KRA_PIN` varchar(191) NOT NULL DEFAULT 'P01111111',
  `employer_number` varchar(191) NOT NULL DEFAULT '11111111',
  `NSSF_employer_number` varchar(191) NOT NULL DEFAULT '11111111',
  `NHIF_employer_code` varchar(191) NOT NULL DEFAULT '1111111111',
  `financial_year_start` date NOT NULL DEFAULT '2024-01-01',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  PRIMARY KEY (`id`),
  KEY `company_settings_location_id_foreign` (`location_id`),
  CONSTRAINT `company_settings_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.company_settings: ~0 rows (approximately)
DELETE FROM `company_settings`;
INSERT INTO `company_settings` (`id`, `legal_Name`, `legal_Address`, `official_contact_number`, `official_email`, `company_contact_name`, `representative_phone`, `representative_email`, `KRA_PIN`, `employer_number`, `NSSF_employer_number`, `NHIF_employer_code`, `financial_year_start`, `created_at`, `updated_at`, `deleted_at`, `location_id`, `approval_status`, `date_approved`, `status`, `approved_by`) VALUES
	(1, 'Test Company', 'Nairobi', 'Nairobi', 'Nairobi', 'Nairobi', 'Nairobi', 'Nairobi', 'Nairobi', 'Nairobi', 'Nairobi', 'Nairobi', '0000-00-00', '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.deduction_types
DROP TABLE IF EXISTS `deduction_types`;
CREATE TABLE IF NOT EXISTS `deduction_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `default_calculation_type` varchar(191) NOT NULL,
  `default_amount` decimal(10,2) DEFAULT NULL,
  `default_percentage` decimal(10,2) DEFAULT NULL,
  `is_statutory` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deduction_types_code_unique` (`code`),
  KEY `deduction_types_company_id_foreign` (`company_id`),
  CONSTRAINT `deduction_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.deduction_types: ~0 rows (approximately)
DELETE FROM `deduction_types`;

-- Dumping structure for table stawi_self_client.delivered_sms
DROP TABLE IF EXISTS `delivered_sms`;
CREATE TABLE IF NOT EXISTS `delivered_sms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `message_id` varchar(191) NOT NULL,
  `message_status` varchar(191) NOT NULL,
  `API_response` varchar(191) NOT NULL,
  `message` varchar(191) NOT NULL,
  `mobile` varchar(191) NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivered_sms_company_id_foreign` (`company_id`),
  CONSTRAINT `delivered_sms_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.delivered_sms: ~0 rows (approximately)
DELETE FROM `delivered_sms`;

-- Dumping structure for table stawi_self_client.department
DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `department_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(150) NOT NULL,
  `department_head_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `department_department_name_unique` (`department_name`),
  KEY `department_location_id_foreign` (`location_id`),
  KEY `department_company_id_foreign` (`company_id`),
  KEY `department_department_head_id_foreign` (`department_head_id`),
  CONSTRAINT `department_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_department_head_id_foreign` FOREIGN KEY (`department_head_id`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL,
  CONSTRAINT `department_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.department: ~0 rows (approximately)
DELETE FROM `department`;
INSERT INTO `department` (`department_id`, `department_name`, `department_head_id`, `created_at`, `updated_at`, `deleted_at`, `status`, `location_id`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, 'Human Resource', NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.designation
DROP TABLE IF EXISTS `designation`;
CREATE TABLE IF NOT EXISTS `designation` (
  `designation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `designation_name` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`designation_id`),
  UNIQUE KEY `designation_designation_name_unique` (`designation_name`),
  KEY `designation_location_id_foreign` (`location_id`),
  KEY `designation_company_id_foreign` (`company_id`),
  CONSTRAINT `designation_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `designation_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.designation: ~0 rows (approximately)
DELETE FROM `designation`;
INSERT INTO `designation` (`designation_id`, `designation_name`, `created_at`, `updated_at`, `deleted_at`, `status`, `location_id`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, 'Admin', '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.disciplinary_case_actions
DROP TABLE IF EXISTS `disciplinary_case_actions`;
CREATE TABLE IF NOT EXISTS `disciplinary_case_actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) unsigned NOT NULL,
  `action_type` int(11) NOT NULL,
  `remarks` longtext DEFAULT NULL,
  `action_by` bigint(20) unsigned NOT NULL,
  `action_date` date NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT '0',
  `attachment` varchar(191) DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `disciplinary_case_actions_case_id_foreign` (`case_id`),
  KEY `disciplinary_case_actions_action_by_foreign` (`action_by`),
  KEY `disciplinary_case_actions_approved_by_foreign` (`approved_by`),
  KEY `disciplinary_case_actions_company_id_foreign` (`company_id`),
  CONSTRAINT `disciplinary_case_actions_action_by_foreign` FOREIGN KEY (`action_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `disciplinary_case_actions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `disciplinary_case_actions_case_id_foreign` FOREIGN KEY (`case_id`) REFERENCES `disciplinary_cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `disciplinary_case_actions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.disciplinary_case_actions: ~0 rows (approximately)
DELETE FROM `disciplinary_case_actions`;

-- Dumping structure for table stawi_self_client.disciplinary_cases
DROP TABLE IF EXISTS `disciplinary_cases`;
CREATE TABLE IF NOT EXISTS `disciplinary_cases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `case_number` varchar(191) NOT NULL,
  `description` longtext DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `assigned_officer` bigint(20) unsigned DEFAULT NULL,
  `location` varchar(191) DEFAULT NULL,
  `attachment` varchar(191) DEFAULT NULL,
  `date_of_incident` date NOT NULL,
  `date_of_report` date NOT NULL,
  `reporter_id` bigint(20) unsigned DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `closed_date` date DEFAULT NULL,
  `closing_remarks` varchar(191) DEFAULT NULL,
  `remarks` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disciplinary_cases_case_number_unique` (`case_number`),
  KEY `disciplinary_cases_category_id_foreign` (`category_id`),
  KEY `disciplinary_cases_employee_id_foreign` (`employee_id`),
  KEY `disciplinary_cases_reporter_id_foreign` (`reporter_id`),
  KEY `disciplinary_cases_assigned_officer_foreign` (`assigned_officer`),
  KEY `disciplinary_cases_location_id_foreign` (`location_id`),
  KEY `disciplinary_cases_company_id_foreign` (`company_id`),
  CONSTRAINT `disciplinary_cases_assigned_officer_foreign` FOREIGN KEY (`assigned_officer`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `disciplinary_cases_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `disciplinary_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `disciplinary_cases_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `disciplinary_cases_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `disciplinary_cases_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  CONSTRAINT `disciplinary_cases_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.disciplinary_cases: ~0 rows (approximately)
DELETE FROM `disciplinary_cases`;

-- Dumping structure for table stawi_self_client.disciplinary_categories
DROP TABLE IF EXISTS `disciplinary_categories`;
CREATE TABLE IF NOT EXISTS `disciplinary_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `category_code` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disciplinary_categories_name_unique` (`name`),
  KEY `disciplinary_categories_company_id_foreign` (`company_id`),
  CONSTRAINT `disciplinary_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.disciplinary_categories: ~0 rows (approximately)
DELETE FROM `disciplinary_categories`;

-- Dumping structure for table stawi_self_client.document_categories
DROP TABLE IF EXISTS `document_categories`;
CREATE TABLE IF NOT EXISTS `document_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_categories_company_id_foreign` (`company_id`),
  CONSTRAINT `document_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.document_categories: ~0 rows (approximately)
DELETE FROM `document_categories`;

-- Dumping structure for table stawi_self_client.document_consents
DROP TABLE IF EXISTS `document_consents`;
CREATE TABLE IF NOT EXISTS `document_consents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `consented_at` timestamp NOT NULL,
  `ip_address` varchar(191) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `acknowledgment_text` text NOT NULL DEFAULT 'I have read and understood this document and agree to abide by the terms stated therein.',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_document_employee_consent` (`document_id`,`employee_id`),
  KEY `document_consents_employee_id_foreign` (`employee_id`),
  KEY `document_consents_user_id_foreign` (`user_id`),
  CONSTRAINT `document_consents_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `hr_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_consents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `document_consents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.document_consents: ~0 rows (approximately)
DELETE FROM `document_consents`;

-- Dumping structure for table stawi_self_client.document_views
DROP TABLE IF EXISTS `document_views`;
CREATE TABLE IF NOT EXISTS `document_views` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `count` bigint(20) NOT NULL,
  `document_id` bigint(20) unsigned NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_views_document_id_foreign` (`document_id`),
  KEY `document_views_company_id_foreign` (`company_id`),
  CONSTRAINT `document_views_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_views_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `hr_documents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.document_views: ~0 rows (approximately)
DELETE FROM `document_views`;

-- Dumping structure for table stawi_self_client.earn_leave_rule
DROP TABLE IF EXISTS `earn_leave_rule`;
CREATE TABLE IF NOT EXISTS `earn_leave_rule` (
  `earn_leave_rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_month` int(11) NOT NULL,
  `day_of_earn_leave` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`earn_leave_rule_id`),
  KEY `earn_leave_rule_location_id_foreign` (`location_id`),
  KEY `earn_leave_rule_company_id_foreign` (`company_id`),
  CONSTRAINT `earn_leave_rule_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `earn_leave_rule_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.earn_leave_rule: ~0 rows (approximately)
DELETE FROM `earn_leave_rule`;

-- Dumping structure for table stawi_self_client.employee
DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `employee_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `national_id` varchar(191) NOT NULL,
  `driving_license_number` varchar(50) DEFAULT NULL,
  `identity_type` varchar(191) DEFAULT NULL,
  `staff_no` varchar(191) DEFAULT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `designation_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned DEFAULT NULL,
  `region_id` bigint(20) unsigned DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `work_shift_id` int(10) unsigned NOT NULL,
  `hourly_salaries_id` int(10) unsigned DEFAULT 0,
  `payout_channel_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `date_of_leaving` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `marital_status` varchar(10) DEFAULT NULL,
  `photo` varchar(250) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_name` varchar(191) DEFAULT NULL,
  `emergency_phone` varchar(191) DEFAULT NULL,
  `emergency_relationship` tinyint(4) DEFAULT NULL,
  `location` varchar(191) DEFAULT NULL,
  `sub_location` varchar(191) DEFAULT NULL,
  `program` varchar(191) DEFAULT NULL,
  `sub_programs` varchar(191) DEFAULT NULL,
  `contract_type` varchar(191) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `years_in_service` int(11) DEFAULT NULL,
  `end_of_probation` date DEFAULT NULL,
  `end_of_contract` date DEFAULT NULL,
  `phone` bigint(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `permanent_status` tinyint(4) NOT NULL DEFAULT 0,
  `contract_status` varchar(191) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `pay_group` int(10) unsigned DEFAULT NULL COMMENT 'job pay-group Daily or Monthly',
  `KRA_Pin` varchar(191) DEFAULT NULL,
  `NSSF_no` varchar(191) DEFAULT NULL,
  `NHIF_no` varchar(191) DEFAULT NULL,
  `payroll_number` varchar(191) DEFAULT NULL,
  `shif_number` text DEFAULT NULL,
  `nssf_rate_type` int(11) NOT NULL DEFAULT 1 COMMENT '1=Old-rates, 2=tier1, 3=tier 1 and tier2, 4=No_deduction',
  `middle_name` varchar(191) DEFAULT NULL,
  `employee_type` int(11) NOT NULL DEFAULT 1,
  `employee_group_id` bigint(20) unsigned DEFAULT NULL,
  `employee_section_id` bigint(20) unsigned DEFAULT NULL,
  `employment_type` varchar(191) DEFAULT NULL,
  `residential_status` int(11) NOT NULL DEFAULT 1,
  `residential_area` varchar(191) DEFAULT NULL,
  `highest_qualification` varchar(191) DEFAULT NULL,
  `payroll_profile` int(11) DEFAULT NULL,
  `nationality` varchar(191) DEFAULT NULL,
  `tribe` varchar(191) NOT NULL,
  `settlement_type` varchar(191) NOT NULL,
  `personal_email` varchar(191) NOT NULL,
  `next_of_kin` varchar(191) DEFAULT NULL,
  `next_of_kin_phone` varchar(15) DEFAULT NULL,
  `personal_phone` varchar(15) DEFAULT NULL,
  `bank` varchar(191) DEFAULT NULL,
  `bank_branch` varchar(191) DEFAULT NULL,
  `brank_branch_code` varchar(191) DEFAULT NULL,
  `bank_account_number` varchar(191) DEFAULT NULL,
  `bank_account_name` varchar(191) DEFAULT NULL,
  `ethnicity` varchar(191) DEFAULT NULL,
  `biometric_upload_status` int(11) NOT NULL DEFAULT 0,
  `biometric_capture_status` varchar(191) NOT NULL DEFAULT '0',
  `biometric_user_id` int(11) DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `employee_staff_no_unique` (`staff_no`),
  UNIQUE KEY `employee_email_unique` (`email`),
  KEY `employee_employee_group_id_foreign` (`employee_group_id`),
  KEY `employee_employee_section_id_foreign` (`employee_section_id`),
  KEY `employee_payout_channel_id_foreign` (`payout_channel_id`),
  KEY `employee_region_id_foreign` (`region_id`),
  KEY `employee_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_employee_group_id_foreign` FOREIGN KEY (`employee_group_id`) REFERENCES `employee_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_employee_section_id_foreign` FOREIGN KEY (`employee_section_id`) REFERENCES `employee_sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_payout_channel_id_foreign` FOREIGN KEY (`payout_channel_id`) REFERENCES `payout_channels` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee: ~0 rows (approximately)
DELETE FROM `employee`;
INSERT INTO `employee` (`employee_id`, `user_id`, `national_id`, `driving_license_number`, `identity_type`, `staff_no`, `department_id`, `designation_id`, `location_id`, `region_id`, `supervisor_id`, `work_shift_id`, `hourly_salaries_id`, `payout_channel_id`, `email`, `first_name`, `last_name`, `date_of_birth`, `age`, `date_of_joining`, `date_of_leaving`, `gender`, `religion`, `marital_status`, `photo`, `address`, `emergency_name`, `emergency_phone`, `emergency_relationship`, `location`, `sub_location`, `program`, `sub_programs`, `contract_type`, `start_date`, `years_in_service`, `end_of_probation`, `end_of_contract`, `phone`, `status`, `permanent_status`, `contract_status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `pay_group`, `KRA_Pin`, `NSSF_no`, `NHIF_no`, `payroll_number`, `shif_number`, `nssf_rate_type`, `middle_name`, `employee_type`, `employee_group_id`, `employee_section_id`, `employment_type`, `residential_status`, `residential_area`, `highest_qualification`, `payroll_profile`, `nationality`, `tribe`, `settlement_type`, `personal_email`, `next_of_kin`, `next_of_kin_phone`, `personal_phone`, `bank`, `bank_branch`, `brank_branch_code`, `bank_account_number`, `bank_account_name`, `ethnicity`, `biometric_upload_status`, `biometric_capture_status`, `biometric_user_id`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, 3, '12345678', NULL, NULL, NULL, 1, 1, 1, NULL, NULL, 1, 0, NULL, 'smaloba3@gmail.com', 'Sam', 'Maloba', '1990-01-01', NULL, '2020-01-01', NULL, 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 254700000001, 1, 0, NULL, 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, 'A123456789B', 'NSS123456', 'NHIF123456', 'EMP001', NULL, 1, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'Luhya', 'Urban', 'sam.maloba@gmail.com', 'Jane Maloba', '+254700000011', '+254700000001', 'Equity Bank', 'CBD', NULL, '1234567890', 'Sam Maloba', NULL, 0, '0', NULL, 1, '2026-05-19 15:07:04', 1, 1),
	(2, 1, '23456789', NULL, NULL, NULL, 1, 1, 1, NULL, NULL, 1, 0, NULL, 'support@stawitech.com', 'Support', 'StawiTech', '1985-05-15', NULL, '2019-03-01', NULL, 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 254700000002, 1, 0, NULL, 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, 'B234567890C', 'NSS234567', 'NHIF234567', 'EMP002', NULL, 1, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'Kikuyu', 'Urban', 'support.personal@gmail.com', 'Mary Support', '+254700000012', '+254700000002', 'KCB', 'Industrial Area', NULL, '2345678901', 'Support StawiTech', NULL, 0, '0', NULL, 1, '2026-05-19 15:07:04', 1, 1),
	(3, 4, '34567890', NULL, NULL, NULL, 1, 1, 1, NULL, NULL, 1, 0, NULL, 'jchengasia@stawitech.com', 'Joseph', 'Chengasia', '1988-08-20', NULL, '2021-06-15', NULL, 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 254700000003, 1, 0, NULL, 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, 'C345678901D', 'NSS345678', 'NHIF345678', 'EMP003', NULL, 1, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'Luo', 'Urban', 'joseph.chengasia@gmail.com', 'Akinyi Chengasia', '+254700000013', '+254700000003', 'Cooperative Bank', 'Town', NULL, '3456789012', 'Joseph Chengasia', NULL, 0, '0', NULL, 1, '2026-05-19 15:07:04', 1, 1),
	(4, 5, '45678901', NULL, NULL, NULL, 1, 1, 1, NULL, NULL, 1, 0, NULL, 'gkoech@stawitech.com', 'Grace', 'Koech', '1992-12-10', NULL, '2022-09-01', NULL, 'Female', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 254700000004, 1, 0, NULL, 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, 'D456789012E', 'NSS456789', 'NHIF456789', 'EMP004', NULL, 1, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'Kalenjin', 'Urban', 'grace.koech@gmail.com', 'John Koech', '+254700000014', '+254700000004', 'Stanbic Bank', 'Westlands', NULL, '4567890123', 'Grace Koech', NULL, 0, '0', NULL, 1, '2026-05-19 15:07:04', 1, 1),
	(5, 6, '56789012', NULL, NULL, NULL, 1, 1, 1, NULL, NULL, 1, 0, NULL, 'cogara@stawitech.com', 'Collins', 'Ogara', '1987-03-25', NULL, '2020-11-10', NULL, 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 254700000005, 1, 0, NULL, 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, 'E567890123F', 'NSS567890', 'NHIF567890', 'EMP005', NULL, 1, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'Kisii', 'Urban', 'collins.ogara@gmail.com', 'Sarah Ogara', '+254700000015', '+254700000005', 'Absa Bank', 'Moi Avenue', NULL, '5678901234', 'Collins Ogara', NULL, 0, '0', NULL, 1, '2026-05-19 15:07:04', 1, 1);

-- Dumping structure for table stawi_self_client.employee_attendance_approve
DROP TABLE IF EXISTS `employee_attendance_approve`;
CREATE TABLE IF NOT EXISTS `employee_attendance_approve` (
  `employee_attendance_approve_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `finger_print_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `in_time` varchar(191) NOT NULL,
  `out_time` varchar(191) NOT NULL,
  `working_hour` varchar(191) NOT NULL,
  `approve_working_hour` varchar(191) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`employee_attendance_approve_id`),
  KEY `employee_attendance_approve_location_id_foreign` (`location_id`),
  KEY `employee_attendance_approve_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_attendance_approve_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_attendance_approve_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_attendance_approve: ~0 rows (approximately)
DELETE FROM `employee_attendance_approve`;

-- Dumping structure for table stawi_self_client.employee_award
DROP TABLE IF EXISTS `employee_award`;
CREATE TABLE IF NOT EXISTS `employee_award` (
  `employee_award_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `award_name` varchar(191) NOT NULL,
  `gift_item` varchar(191) NOT NULL,
  `month` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`employee_award_id`),
  KEY `employee_award_location_id_foreign` (`location_id`),
  KEY `employee_award_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_award_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_award_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_award: ~0 rows (approximately)
DELETE FROM `employee_award`;

-- Dumping structure for table stawi_self_client.employee_bonus
DROP TABLE IF EXISTS `employee_bonus`;
CREATE TABLE IF NOT EXISTS `employee_bonus` (
  `employee_bonus_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_setting_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month` varchar(191) NOT NULL,
  `gross_salary` int(11) NOT NULL,
  `basic_salary` int(11) NOT NULL,
  `bonus_amount` int(11) NOT NULL,
  `tax` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`employee_bonus_id`),
  KEY `employee_bonus_location_id_foreign` (`location_id`),
  KEY `employee_bonus_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_bonus_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_bonus_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_bonus: ~0 rows (approximately)
DELETE FROM `employee_bonus`;

-- Dumping structure for table stawi_self_client.employee_deductions
DROP TABLE IF EXISTS `employee_deductions`;
CREATE TABLE IF NOT EXISTS `employee_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `payroll_deduction_type_id` bigint(20) unsigned DEFAULT NULL,
  `deduction_category` varchar(191) DEFAULT NULL,
  `percentage` decimal(8,2) DEFAULT NULL,
  `rate` decimal(8,2) DEFAULT NULL,
  `units` int(11) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `limit_per_month` decimal(10,2) DEFAULT NULL,
  `limit_per_year` decimal(10,2) DEFAULT NULL,
  `is_tax_deductible` tinyint(1) NOT NULL DEFAULT 0,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `frequency` varchar(50) DEFAULT NULL,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `payroll_year` int(11) DEFAULT NULL,
  `payroll_month` int(11) DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `financial_year_id` bigint(20) unsigned DEFAULT NULL,
  `is_pensionable` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `reference_number` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `calculation_type` enum('fixed_amount','percentage_of_basic','percentage_of_gross','hourly_rate','daily_rate') NOT NULL DEFAULT 'fixed_amount',
  `is_statutory` tinyint(1) NOT NULL DEFAULT 0,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `batch_submission_id` varchar(191) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_deductions_reference_number_unique` (`reference_number`),
  KEY `employee_deductions_employee_id_foreign` (`employee_id`),
  KEY `employee_deductions_created_by_foreign` (`created_by`),
  KEY `employee_deductions_payroll_year_payroll_month_index` (`payroll_year`,`payroll_month`),
  KEY `employee_deductions_effective_from_index` (`effective_from`),
  KEY `employee_deductions_effective_to_index` (`effective_to`),
  KEY `employee_deductions_financial_year_id_index` (`financial_year_id`),
  KEY `employee_deductions_approved_by_foreign` (`approved_by`),
  KEY `employee_deductions_updated_by_foreign` (`updated_by`),
  KEY `employee_deductions_batch_submission_id_index` (`batch_submission_id`),
  KEY `employee_deductions_payroll_deduction_type_id_foreign` (`payroll_deduction_type_id`),
  KEY `employee_deductions_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_deductions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_deductions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_deductions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_deductions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `employee_deductions_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_deductions_payroll_deduction_type_id_foreign` FOREIGN KEY (`payroll_deduction_type_id`) REFERENCES `deduction_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `employee_deductions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_deductions: ~0 rows (approximately)
DELETE FROM `employee_deductions`;

-- Dumping structure for table stawi_self_client.employee_documents
DROP TABLE IF EXISTS `employee_documents`;
CREATE TABLE IF NOT EXISTS `employee_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_name` varchar(191) NOT NULL,
  `employee_id` bigint(20) NOT NULL,
  `national_id` varchar(191) NOT NULL,
  `date_uploaded` date NOT NULL,
  `document_type` varchar(191) NOT NULL,
  `document_link` varchar(191) NOT NULL,
  `uploaded_by` bigint(20) NOT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `uuid` varchar(191) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_documents_location_id_foreign` (`location_id`),
  KEY `employee_documents_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_documents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_documents_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_documents: ~0 rows (approximately)
DELETE FROM `employee_documents`;

-- Dumping structure for table stawi_self_client.employee_earnings
DROP TABLE IF EXISTS `employee_earnings`;
CREATE TABLE IF NOT EXISTS `employee_earnings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `payroll_earning_type_id` bigint(20) unsigned NOT NULL,
  `calculation_type` enum('fixed_amount','percentage_of_basic','percentage_of_gross','hourly_rate','daily_rate') NOT NULL DEFAULT 'fixed_amount',
  `amount` decimal(10,2) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `units` int(11) DEFAULT NULL,
  `limit_per_month` decimal(15,2) DEFAULT NULL,
  `limit_per_year` decimal(15,2) DEFAULT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 1,
  `is_pensionable` tinyint(1) NOT NULL DEFAULT 1,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 1,
  `frequency` enum('monthly','weekly','bi_weekly','quarterly','annually','one_time') NOT NULL DEFAULT 'monthly',
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `payroll_year` year(4) NOT NULL DEFAULT 2026,
  `payroll_month` tinyint(4) NOT NULL DEFAULT 5,
  `financial_year_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `earning_category` varchar(191) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `batch_submission_id` varchar(191) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_employee_earning` (`employee_id`,`payroll_earning_type_id`,`effective_from`),
  KEY `employee_earnings_approved_by_foreign` (`approved_by`),
  KEY `employee_earnings_created_by_foreign` (`created_by`),
  KEY `employee_earnings_updated_by_foreign` (`updated_by`),
  KEY `employee_earnings_employee_id_payroll_year_payroll_month_index` (`employee_id`,`payroll_year`,`payroll_month`),
  KEY `employee_earnings_payroll_earning_type_id_index` (`payroll_earning_type_id`),
  KEY `employee_earnings_effective_from_effective_to_index` (`effective_from`,`effective_to`),
  KEY `employee_earnings_is_recurring_frequency_index` (`is_recurring`,`frequency`),
  KEY `employee_earnings_financial_year_id_foreign` (`financial_year_id`),
  KEY `employee_earnings_batch_submission_id_index` (`batch_submission_id`),
  KEY `employee_earnings_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_earnings_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_earnings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_earnings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_earnings_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `employee_earnings_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_earnings_payroll_earning_type_id_foreign` FOREIGN KEY (`payroll_earning_type_id`) REFERENCES `payroll_earning_types` (`id`),
  CONSTRAINT `employee_earnings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_earnings: ~0 rows (approximately)
DELETE FROM `employee_earnings`;

-- Dumping structure for table stawi_self_client.employee_education_qualification
DROP TABLE IF EXISTS `employee_education_qualification`;
CREATE TABLE IF NOT EXISTS `employee_education_qualification` (
  `employee_education_qualification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `institute` varchar(200) NOT NULL,
  `board_university` varchar(200) NOT NULL,
  `degree` varchar(200) NOT NULL,
  `passing_year` varchar(191) DEFAULT NULL,
  `result` varchar(100) DEFAULT NULL,
  `cgpa` varchar(50) DEFAULT NULL,
  `certificate` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`employee_education_qualification_id`),
  KEY `employee_education_qualification_location_id_foreign` (`location_id`),
  KEY `employee_education_qualification_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_education_qualification_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_education_qualification_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_education_qualification: ~0 rows (approximately)
DELETE FROM `employee_education_qualification`;

-- Dumping structure for table stawi_self_client.employee_experience
DROP TABLE IF EXISTS `employee_experience`;
CREATE TABLE IF NOT EXISTS `employee_experience` (
  `employee_experience_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `organization_name` varchar(200) NOT NULL,
  `designation` varchar(200) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date DEFAULT NULL,
  `skill` text NOT NULL,
  `responsibility` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`employee_experience_id`),
  KEY `employee_experience_location_id_foreign` (`location_id`),
  KEY `employee_experience_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_experience_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_experience_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_experience: ~0 rows (approximately)
DELETE FROM `employee_experience`;

-- Dumping structure for table stawi_self_client.employee_feedback
DROP TABLE IF EXISTS `employee_feedback`;
CREATE TABLE IF NOT EXISTS `employee_feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) NOT NULL,
  `content` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `financial_year_id` bigint(20) unsigned DEFAULT NULL COMMENT 'References financial_years table',
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_feedback_employee_id_foreign` (`employee_id`),
  KEY `employee_feedback_location_id_foreign` (`location_id`),
  KEY `employee_feedback_created_by_foreign` (`created_by`),
  KEY `employee_feedback_deleted_by_foreign` (`deleted_by`),
  KEY `employee_feedback_category_id_foreign` (`category_id`),
  KEY `employee_feedback_financial_year_id_foreign` (`financial_year_id`),
  KEY `employee_feedback_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_feedback_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `feedback_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_feedback_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_feedback_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `employee_feedback_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `user` (`id`),
  CONSTRAINT `employee_feedback_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`),
  CONSTRAINT `employee_feedback_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `employee_feedback_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_feedback: ~0 rows (approximately)
DELETE FROM `employee_feedback`;

-- Dumping structure for table stawi_self_client.employee_feedback_responses
DROP TABLE IF EXISTS `employee_feedback_responses`;
CREATE TABLE IF NOT EXISTS `employee_feedback_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `feedback_id` bigint(20) unsigned NOT NULL,
  `responder_id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_feedback_responses_feedback_id_foreign` (`feedback_id`),
  KEY `employee_feedback_responses_responder_id_foreign` (`responder_id`),
  KEY `employee_feedback_responses_location_id_foreign` (`location_id`),
  KEY `employee_feedback_responses_created_by_foreign` (`created_by`),
  KEY `employee_feedback_responses_deleted_by_foreign` (`deleted_by`),
  KEY `employee_feedback_responses_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_feedback_responses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_feedback_responses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_feedback_responses_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_feedback_responses_feedback_id_foreign` FOREIGN KEY (`feedback_id`) REFERENCES `employee_feedback` (`id`),
  CONSTRAINT `employee_feedback_responses_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE CASCADE,
  CONSTRAINT `employee_feedback_responses_responder_id_foreign` FOREIGN KEY (`responder_id`) REFERENCES `employee` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_feedback_responses: ~0 rows (approximately)
DELETE FROM `employee_feedback_responses`;

-- Dumping structure for table stawi_self_client.employee_groups
DROP TABLE IF EXISTS `employee_groups`;
CREATE TABLE IF NOT EXISTS `employee_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_groups_location_id_foreign` (`location_id`),
  KEY `employee_groups_created_by_foreign` (`created_by`),
  KEY `employee_groups_approved_by_foreign` (`approved_by`),
  KEY `employee_groups_deleted_by_foreign` (`deleted_by`),
  KEY `employee_groups_deleted_approved_by_foreign` (`deleted_approved_by`),
  KEY `employee_groups_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_groups_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_groups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_groups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_groups_deleted_approved_by_foreign` FOREIGN KEY (`deleted_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_groups_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_groups_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_groups: ~0 rows (approximately)
DELETE FROM `employee_groups`;

-- Dumping structure for table stawi_self_client.employee_leavegroups
DROP TABLE IF EXISTS `employee_leavegroups`;
CREATE TABLE IF NOT EXISTS `employee_leavegroups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `leave_group_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_leavegroups_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_leavegroups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_leavegroups: ~0 rows (approximately)
DELETE FROM `employee_leavegroups`;

-- Dumping structure for table stawi_self_client.employee_movements
DROP TABLE IF EXISTS `employee_movements`;
CREATE TABLE IF NOT EXISTS `employee_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `payroll_number` varchar(191) DEFAULT NULL,
  `current_department` int(10) unsigned NOT NULL,
  `current_designation` int(10) unsigned NOT NULL,
  `current_salary` int(11) NOT NULL,
  `current_section_id` int(11) NOT NULL,
  `current_group_id` int(11) NOT NULL,
  `current_work_shift_id` int(11) NOT NULL,
  `current_branch` int(11) NOT NULL,
  `current_employee_type` int(11) DEFAULT NULL,
  `new_salary` int(11) DEFAULT NULL,
  `new_department_id` int(11) DEFAULT NULL,
  `new_designation_id` int(11) DEFAULT NULL,
  `new_employee_status` int(11) DEFAULT NULL,
  `movement_date` date NOT NULL,
  `new_section_id` int(11) DEFAULT NULL,
  `new_group_id` int(11) DEFAULT NULL,
  `new_work_shift_id` int(11) DEFAULT NULL,
  `new_branch` int(11) DEFAULT NULL,
  `new_employee_type` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `stage1_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage2_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage3_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage1_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage2_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage3_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage1_approval_comments` varchar(191) DEFAULT NULL,
  `stage2_approval_comments` varchar(191) DEFAULT NULL,
  `stage3_approval_comments` varchar(191) DEFAULT NULL,
  `stage1_approval_date` datetime DEFAULT NULL,
  `stage2_approval_date` datetime DEFAULT NULL,
  `stage3_approval_date` datetime DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_movements_stage1_approved_by_foreign` (`stage1_approved_by`),
  KEY `employee_movements_stage2_approved_by_foreign` (`stage2_approved_by`),
  KEY `employee_movements_stage3_approved_by_foreign` (`stage3_approved_by`),
  KEY `employee_movements_location_id_foreign` (`location_id`),
  KEY `employee_movements_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_movements_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_movements_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  CONSTRAINT `employee_movements_stage1_approved_by_foreign` FOREIGN KEY (`stage1_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_movements_stage2_approved_by_foreign` FOREIGN KEY (`stage2_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_movements_stage3_approved_by_foreign` FOREIGN KEY (`stage3_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_movements: ~0 rows (approximately)
DELETE FROM `employee_movements`;

-- Dumping structure for table stawi_self_client.employee_overtimes
DROP TABLE IF EXISTS `employee_overtimes`;
CREATE TABLE IF NOT EXISTS `employee_overtimes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `hours_worked` decimal(8,2) DEFAULT NULL,
  `overtime_rate` decimal(8,2) DEFAULT NULL,
  `total_amount` decimal(8,2) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = Inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `weekend_hours_totals` decimal(8,2) NOT NULL DEFAULT 0.00,
  `weekend_days_totals` int(11) NOT NULL DEFAULT 0,
  `public_holiday_hours_totals` decimal(8,2) NOT NULL DEFAULT 0.00,
  `public_holiday_days_totals` int(11) NOT NULL DEFAULT 0,
  `weekday_hours_total` decimal(8,2) NOT NULL DEFAULT 0.00,
  `weekday_days_total` int(11) NOT NULL DEFAULT 0,
  `payroll_period_id` bigint(20) unsigned NOT NULL,
  `payroll_month` varchar(7) DEFAULT NULL COMMENT 'Format: YYYY-MM',
  `weekday_amount_calculated` decimal(12,2) NOT NULL DEFAULT 0.00,
  `weekend_amount_calculated` decimal(12,2) NOT NULL DEFAULT 0.00,
  `holiday_amount_calculated` decimal(12,2) NOT NULL DEFAULT 0.00,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_overtimes_employee_id_foreign` (`employee_id`),
  KEY `employee_overtimes_created_by_foreign` (`created_by`),
  KEY `employee_overtimes_updated_by_foreign` (`updated_by`),
  KEY `employee_overtimes_payroll_period_id_foreign` (`payroll_period_id`),
  KEY `employee_overtimes_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_overtimes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_overtimes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `employee_overtimes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`),
  CONSTRAINT `employee_overtimes_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_overtimes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_overtimes: ~0 rows (approximately)
DELETE FROM `employee_overtimes`;

-- Dumping structure for table stawi_self_client.employee_payout_channels
DROP TABLE IF EXISTS `employee_payout_channels`;
CREATE TABLE IF NOT EXISTS `employee_payout_channels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `payout_channel_id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `account_number` varchar(191) DEFAULT NULL,
  `branch` varchar(191) DEFAULT NULL,
  `branch_code` varchar(191) DEFAULT NULL,
  `swift_code` varchar(191) DEFAULT NULL,
  `approval_status` tinyint(4) DEFAULT 0,
  `status` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_payout_channels_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_payout_channels_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_payout_channels: ~0 rows (approximately)
DELETE FROM `employee_payout_channels`;

-- Dumping structure for table stawi_self_client.employee_payroll_profiles
DROP TABLE IF EXISTS `employee_payroll_profiles`;
CREATE TABLE IF NOT EXISTS `employee_payroll_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_number` varchar(191) DEFAULT NULL,
  `account_name` varchar(191) DEFAULT NULL,
  `bank_name` varchar(191) DEFAULT NULL,
  `branch_name` varchar(191) DEFAULT NULL,
  `swift_code` varchar(191) DEFAULT NULL,
  `currency_code` varchar(191) DEFAULT NULL,
  `account_confirmation_letter` varchar(191) DEFAULT NULL,
  `payout_channel_id` int(11) DEFAULT NULL,
  `approval_status` int(11) NOT NULL DEFAULT 0 COMMENT '0-Pending, 1-Approved, 2-Decline',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1-Active, 0 Inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_payroll_profiles_location_id_foreign` (`location_id`),
  KEY `employee_payroll_profiles_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_payroll_profiles_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_payroll_profiles_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_payroll_profiles: ~0 rows (approximately)
DELETE FROM `employee_payroll_profiles`;

-- Dumping structure for table stawi_self_client.employee_payrolls
DROP TABLE IF EXISTS `employee_payrolls`;
CREATE TABLE IF NOT EXISTS `employee_payrolls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `payroll_number` varchar(191) NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'KES',
  `payment_method` enum('bank_transfer','mobile_money','cash','cheque') NOT NULL DEFAULT 'bank_transfer',
  `bank_name` varchar(191) DEFAULT NULL,
  `bank_branch` varchar(191) DEFAULT NULL,
  `account_number` varchar(191) DEFAULT NULL,
  `account_name` varchar(191) DEFAULT NULL,
  `kra_pin` varchar(191) DEFAULT NULL,
  `nssf_number` varchar(191) DEFAULT NULL,
  `shif_number` varchar(191) DEFAULT NULL,
  `tax_status` enum('resident','non_resident','exempt') NOT NULL DEFAULT 'resident',
  `disability_exemption` tinyint(1) NOT NULL DEFAULT 0,
  `pension_scheme_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `effective_date` date NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `overtime_rate_normal` decimal(3,2) NOT NULL DEFAULT 1.50 COMMENT 'Overtime rate multiplier for normal working days (e.g., 1.5 = 150%)',
  `overtime_rate_weekend` decimal(3,2) NOT NULL DEFAULT 2.00 COMMENT 'Overtime rate multiplier for weekends (e.g., 2.0 = 200%)',
  `overtime_rate_holiday` decimal(3,2) NOT NULL DEFAULT 2.00 COMMENT 'Overtime rate multiplier for public holidays (e.g., 2.0 = 200%)',
  `phone_number` varchar(20) DEFAULT NULL COMMENT 'Employee phone number for payroll communication',
  `income_frequency` enum('daily','weekly','monthly') NOT NULL DEFAULT 'monthly' COMMENT 'Frequency of income payment (daily, weekly, monthly)',
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `batch_submission_id` varchar(191) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` int(11) NOT NULL DEFAULT 0,
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `previous_basic_salary` decimal(12,2) DEFAULT NULL,
  `last_salary_change_date` date DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_payrolls_payroll_number_unique` (`payroll_number`),
  KEY `employee_payrolls_pension_scheme_id_foreign` (`pension_scheme_id`),
  KEY `employee_payrolls_created_by_foreign` (`created_by`),
  KEY `employee_payrolls_updated_by_foreign` (`updated_by`),
  KEY `employee_payrolls_employee_id_is_active_index` (`employee_id`,`is_active`),
  KEY `employee_payrolls_payroll_number_index` (`payroll_number`),
  KEY `idx_overtime_rates` (`overtime_rate_normal`,`overtime_rate_weekend`,`overtime_rate_holiday`),
  KEY `employee_payrolls_income_frequency_index` (`income_frequency`),
  KEY `employee_payrolls_phone_number_index` (`phone_number`),
  KEY `employee_payrolls_batch_submission_id_index` (`batch_submission_id`),
  KEY `employee_payrolls_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_payrolls_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_payrolls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `employee_payrolls_pension_scheme_id_foreign` FOREIGN KEY (`pension_scheme_id`) REFERENCES `pension_schemes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_payrolls_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_payrolls: ~0 rows (approximately)
DELETE FROM `employee_payrolls`;
INSERT INTO `employee_payrolls` (`id`, `employee_id`, `payroll_number`, `basic_salary`, `currency`, `payment_method`, `bank_name`, `bank_branch`, `account_number`, `account_name`, `kra_pin`, `nssf_number`, `shif_number`, `tax_status`, `disability_exemption`, `pension_scheme_id`, `is_active`, `effective_date`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `overtime_rate_normal`, `overtime_rate_weekend`, `overtime_rate_holiday`, `phone_number`, `income_frequency`, `approval_status`, `batch_submission_id`, `date_approved`, `status`, `approved_by`, `previous_basic_salary`, `last_salary_change_date`, `company_id`) VALUES
	(1, 1, 'EMPR0001', 150000.00, 'KES', 'bank_transfer', 'Equity Bank', 'CBD', '1234567890', 'Sam Maloba', 'A123456789B', 'NSS123456', 'SHIF123456', 'resident', 0, NULL, 1, '2020-01-01', 1, 1, NULL, NULL, NULL, 1.50, 2.00, 2.00, NULL, 'monthly', 2, NULL, NULL, 1, NULL, NULL, NULL, NULL),
	(2, 2, 'EMPR0002', 200000.00, 'KES', 'bank_transfer', 'KCB', 'Industrial Area', '2345678901', 'Support StawiTech', 'B234567890C', 'NSS234567', 'SHIF234567', 'resident', 0, NULL, 1, '2019-03-01', 1, 1, NULL, NULL, NULL, 1.50, 2.00, 2.00, NULL, 'monthly', 2, NULL, NULL, 1, NULL, NULL, NULL, NULL),
	(3, 3, 'EMPR0003', 120000.00, 'KES', 'bank_transfer', 'Cooperative Bank', 'Town', '3456789012', 'Joseph Chengasia', 'C345678901D', 'NSS345678', 'SHIF345678', 'resident', 0, NULL, 1, '2021-06-15', 1, 1, NULL, NULL, NULL, 1.50, 2.00, 2.00, NULL, 'monthly', 2, NULL, NULL, 1, NULL, NULL, NULL, NULL),
	(4, 4, 'EMPR0004', 100000.00, 'KES', 'bank_transfer', 'Stanbic Bank', 'Westlands', '4567890123', 'Grace Koech', 'D456789012E', 'NSS456789', 'SHIF456789', 'resident', 0, NULL, 1, '2022-09-01', 1, 1, NULL, NULL, NULL, 1.50, 2.00, 2.00, NULL, 'monthly', 2, NULL, NULL, 1, NULL, NULL, NULL, NULL),
	(5, 5, 'EMPR0005', 130000.00, 'KES', 'bank_transfer', 'Absa Bank', 'Moi Avenue', '5678901234', 'Collins Ogara', 'E567890123F', 'NSS567890', 'SHIF567890', 'resident', 0, NULL, 1, '2020-11-10', 1, 1, NULL, NULL, NULL, 1.50, 2.00, 2.00, NULL, 'monthly', 2, NULL, NULL, 1, NULL, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.employee_pension_schemes
DROP TABLE IF EXISTS `employee_pension_schemes`;
CREATE TABLE IF NOT EXISTS `employee_pension_schemes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_payroll_id` bigint(20) unsigned NOT NULL,
  `pension_scheme_id` bigint(20) unsigned NOT NULL,
  `employee_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `employer_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_payroll_scheme_unique` (`employee_payroll_id`,`pension_scheme_id`),
  KEY `employee_pension_schemes_pension_scheme_id_foreign` (`pension_scheme_id`),
  KEY `employee_pension_schemes_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_pension_schemes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_pension_schemes_employee_payroll_id_foreign` FOREIGN KEY (`employee_payroll_id`) REFERENCES `employee_payrolls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_pension_schemes_pension_scheme_id_foreign` FOREIGN KEY (`pension_scheme_id`) REFERENCES `pension_schemes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_pension_schemes: ~0 rows (approximately)
DELETE FROM `employee_pension_schemes`;

-- Dumping structure for table stawi_self_client.employee_salary_history
DROP TABLE IF EXISTS `employee_salary_history`;
CREATE TABLE IF NOT EXISTS `employee_salary_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `previous_salary` decimal(12,2) NOT NULL,
  `new_salary` decimal(12,2) NOT NULL,
  `salary_change_amount` decimal(12,2) NOT NULL,
  `salary_change_percentage` decimal(8,2) NOT NULL,
  `effective_date` date NOT NULL,
  `change_type` varchar(191) NOT NULL,
  `change_reason` text NOT NULL,
  `changed_by` bigint(20) unsigned DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_salary_history_changed_by_foreign` (`changed_by`),
  KEY `employee_salary_history_employee_id_effective_date_index` (`employee_id`,`effective_date`),
  KEY `employee_salary_history_effective_date_change_type_index` (`effective_date`,`change_type`),
  KEY `employee_salary_history_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_salary_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_salary_history_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_salary_history_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_salary_history: ~0 rows (approximately)
DELETE FROM `employee_salary_history`;

-- Dumping structure for table stawi_self_client.employee_sections
DROP TABLE IF EXISTS `employee_sections`;
CREATE TABLE IF NOT EXISTS `employee_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `section_head_id` bigint(20) unsigned DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_sections_location_id_foreign` (`location_id`),
  KEY `employee_sections_created_by_foreign` (`created_by`),
  KEY `employee_sections_approved_by_foreign` (`approved_by`),
  KEY `employee_sections_deleted_by_foreign` (`deleted_by`),
  KEY `employee_sections_deleted_approved_by_foreign` (`deleted_approved_by`),
  KEY `employee_sections_company_id_foreign` (`company_id`),
  KEY `employee_sections_section_head_id_foreign` (`section_head_id`),
  CONSTRAINT `employee_sections_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_sections_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_sections_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employee` (`employee_id`) ON UPDATE CASCADE,
  CONSTRAINT `employee_sections_deleted_approved_by_foreign` FOREIGN KEY (`deleted_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_sections_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_sections_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON UPDATE CASCADE,
  CONSTRAINT `employee_sections_section_head_id_foreign` FOREIGN KEY (`section_head_id`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_sections: ~0 rows (approximately)
DELETE FROM `employee_sections`;

-- Dumping structure for table stawi_self_client.employee_survey_responses
DROP TABLE IF EXISTS `employee_survey_responses`;
CREATE TABLE IF NOT EXISTS `employee_survey_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` bigint(20) unsigned NOT NULL,
  `survey_question_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `response` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_survey_responses_survey_id_foreign` (`survey_id`),
  KEY `employee_survey_responses_survey_question_id_foreign` (`survey_question_id`),
  KEY `employee_survey_responses_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_survey_responses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_survey_responses_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_survey_responses_survey_question_id_foreign` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_survey_responses: ~0 rows (approximately)
DELETE FROM `employee_survey_responses`;

-- Dumping structure for table stawi_self_client.employee_to_deductions
DROP TABLE IF EXISTS `employee_to_deductions`;
CREATE TABLE IF NOT EXISTS `employee_to_deductions` (
  `employee_id` int(11) NOT NULL,
  `deduction_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `approved_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  KEY `employee_to_deductions_location_id_foreign` (`location_id`),
  KEY `employee_to_deductions_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_to_deductions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_to_deductions_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_to_deductions: ~0 rows (approximately)
DELETE FROM `employee_to_deductions`;

-- Dumping structure for table stawi_self_client.employee_to_work_shift
DROP TABLE IF EXISTS `employee_to_work_shift`;
CREATE TABLE IF NOT EXISTS `employee_to_work_shift` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `work_shift` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_to_work_shift_location_id_foreign` (`location_id`),
  KEY `employee_to_work_shift_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_to_work_shift_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_to_work_shift_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_to_work_shift: ~0 rows (approximately)
DELETE FROM `employee_to_work_shift`;

-- Dumping structure for table stawi_self_client.employee_types
DROP TABLE IF EXISTS `employee_types`;
CREATE TABLE IF NOT EXISTS `employee_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_types_location_id_foreign` (`location_id`),
  KEY `employee_types_company_id_foreign` (`company_id`),
  CONSTRAINT `employee_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_types_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.employee_types: ~0 rows (approximately)
DELETE FROM `employee_types`;
INSERT INTO `employee_types` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`, `status`, `location_id`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, 'Permanent', 'Permanent Employees', '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL),
	(2, 'Casual', 'Casual Employees', '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.error_logs
DROP TABLE IF EXISTS `error_logs`;
CREATE TABLE IF NOT EXISTS `error_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(191) DEFAULT NULL,
  `description` text NOT NULL,
  `affected_employee_id` varchar(191) DEFAULT NULL,
  `subject` varchar(191) NOT NULL,
  `subject_id` varchar(191) NOT NULL,
  `causer` varchar(191) NOT NULL,
  `logged_check_time` datetime DEFAULT NULL,
  `date` date DEFAULT NULL,
  `error_type` varchar(191) DEFAULT NULL,
  `module` varchar(191) DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `error_logs_logged_check_time_unique` (`logged_check_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.error_logs: ~0 rows (approximately)
DELETE FROM `error_logs`;

-- Dumping structure for table stawi_self_client.ethnicities
DROP TABLE IF EXISTS `ethnicities`;
CREATE TABLE IF NOT EXISTS `ethnicities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.ethnicities: ~0 rows (approximately)
DELETE FROM `ethnicities`;
INSERT INTO `ethnicities` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'Kikuyu', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(2, 'Luhya', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(3, 'Kalenjin', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(4, 'Luo', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(5, 'Kamba', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(6, 'Kenyan Somali', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(7, 'Kisii', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(8, 'Mijikenda', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(9, 'Meru', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(10, 'Maasai', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(11, 'Turkana', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(12, 'Embu', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(13, 'Samburu', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(14, 'Taita', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(15, 'Borana', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(16, 'Tharaka', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(17, 'Pokot', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(18, 'Rendille', '2026-05-19 15:07:04', '2026-05-19 15:07:04'),
	(19, 'Orma', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(20, 'Giriama', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(21, 'Dawida', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(22, 'Kuria', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(23, 'Gabra', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(24, 'Ilchamus', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(25, 'Digo', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(26, 'Taveta', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(27, 'Elmolo', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(28, 'Ndorobo', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(29, 'Ogiek', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(30, 'Kony', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(31, 'Konso', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(32, 'Waata', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(33, 'Sagalla', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(34, 'Malakote', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(35, 'Nyika', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(36, 'Burji', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(37, 'Bajun', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(38, 'Shona', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(39, 'Makonde', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(40, 'Nubian', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(41, 'Swahili', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(42, 'Other', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(43, 'Caucasian', '2026-05-19 15:07:05', '2026-05-19 15:07:05');

-- Dumping structure for table stawi_self_client.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table stawi_self_client.feedback_categories
DROP TABLE IF EXISTS `feedback_categories`;
CREATE TABLE IF NOT EXISTS `feedback_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `approval_status` int(11) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feedback_categories_name_unique` (`name`),
  KEY `feedback_categories_created_by_foreign` (`created_by`),
  KEY `feedback_categories_deleted_by_foreign` (`deleted_by`),
  KEY `feedback_categories_company_id_foreign` (`company_id`),
  CONSTRAINT `feedback_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feedback_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `feedback_categories_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.feedback_categories: ~0 rows (approximately)
DELETE FROM `feedback_categories`;

-- Dumping structure for table stawi_self_client.financial_years
DROP TABLE IF EXISTS `financial_years`;
CREATE TABLE IF NOT EXISTS `financial_years` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financial_years_uuid_unique` (`uuid`),
  UNIQUE KEY `financial_years_start_date_end_date_status_unique` (`start_date`,`end_date`,`status`),
  KEY `financial_years_name_index` (`name`),
  KEY `financial_years_company_id_foreign` (`company_id`),
  CONSTRAINT `financial_years_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.financial_years: ~0 rows (approximately)
DELETE FROM `financial_years`;
INSERT INTO `financial_years` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`, `uuid`, `start_date`, `end_date`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, '2026', 'Financial Year 2026', 1, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 1, 1, NULL, 'f5ef3c13-39fe-4f57-9708-82b4ca09bb27', '2026-01-01', '2026-12-31', 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.front_settings
DROP TABLE IF EXISTS `front_settings`;
CREATE TABLE IF NOT EXISTS `front_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_title` varchar(191) NOT NULL,
  `home_page_big_title` text NOT NULL,
  `short_description` text NOT NULL,
  `service_title` varchar(191) NOT NULL,
  `job_title` varchar(191) NOT NULL,
  `about_us_image` varchar(191) NOT NULL,
  `logo` varchar(191) NOT NULL,
  `footer_text` text DEFAULT NULL,
  `about_us_description` text NOT NULL,
  `contact_website` varchar(191) DEFAULT NULL,
  `contact_phone` varchar(191) DEFAULT NULL,
  `contact_email` varchar(191) DEFAULT NULL,
  `contact_address` text DEFAULT NULL,
  `counter_1_title` varchar(191) NOT NULL,
  `counter_1_value` int(11) NOT NULL,
  `counter_2_title` varchar(191) NOT NULL,
  `counter_2_value` int(11) NOT NULL,
  `counter_3_title` varchar(191) NOT NULL,
  `counter_3_value` int(11) NOT NULL,
  `counter_4_title` varchar(191) NOT NULL,
  `counter_4_value` int(11) NOT NULL,
  `show_job` tinyint(4) DEFAULT 1,
  `show_service` tinyint(4) DEFAULT 1,
  `show_about` tinyint(4) DEFAULT 1,
  `show_contact` tinyint(4) DEFAULT 1,
  `show_counter` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `financial_year_end` date DEFAULT '2022-12-30',
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `front_settings_location_id_foreign` (`location_id`),
  KEY `front_settings_company_id_foreign` (`company_id`),
  CONSTRAINT `front_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `front_settings_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.front_settings: ~0 rows (approximately)
DELETE FROM `front_settings`;
INSERT INTO `front_settings` (`id`, `company_title`, `home_page_big_title`, `short_description`, `service_title`, `job_title`, `about_us_image`, `logo`, `footer_text`, `about_us_description`, `contact_website`, `contact_phone`, `contact_email`, `contact_address`, `counter_1_title`, `counter_1_value`, `counter_2_title`, `counter_2_value`, `counter_3_title`, `counter_3_value`, `counter_4_title`, `counter_4_value`, `show_job`, `show_service`, `show_about`, `show_contact`, `show_counter`, `created_at`, `updated_at`, `financial_year_end`, `deleted_at`, `status`, `location_id`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, 'Test Company', 'Title', 'Tetet', 'title', 'title1', 'image.jpg', 'logo.jpg', 'Footer', 'Description', NULL, '12345678901', 'support@stawitech.com', NULL, '', 0, '', 0, '', 0, '', 0, 1, 1, 1, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', '2022-12-30', NULL, 1, NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.grouped_menu_route_permissions
DROP TABLE IF EXISTS `grouped_menu_route_permissions`;
CREATE TABLE IF NOT EXISTS `grouped_menu_route_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(191) DEFAULT NULL,
  `permission_group` varchar(191) NOT NULL,
  `group_description` varchar(191) NOT NULL,
  `permission` varchar(191) NOT NULL,
  `permission_description` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `module_id` bigint(20) unsigned NOT NULL,
  `sub_section` varchar(191) DEFAULT NULL,
  `sub_section_description` varchar(191) DEFAULT NULL,
  `actiontype` varchar(191) DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grouped_menu_route_permissions_location_id_foreign` (`location_id`),
  KEY `grouped_menu_route_permissions_company_id_foreign` (`company_id`),
  CONSTRAINT `grouped_menu_route_permissions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grouped_menu_route_permissions_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1299 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.grouped_menu_route_permissions: ~0 rows (approximately)
DELETE FROM `grouped_menu_route_permissions`;
INSERT INTO `grouped_menu_route_permissions` (`id`, `menu_name`, `permission_group`, `group_description`, `permission`, `permission_description`, `created_at`, `updated_at`, `location_id`, `module_id`, `sub_section`, `sub_section_description`, `actiontype`, `approval_status`, `date_approved`, `status`, `approved_by`, `company_id`) VALUES
	(1, 'Payroll', 'advance_types', 'Advance Types', 'advance_types.create', 'Create Advance Types', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(2, 'Payroll', 'advance_types', 'Advance Types', 'advance_types.destroy', 'Delete Advance Types', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(3, 'Payroll', 'advance_types', 'Advance Types', 'advance_types.edit', 'Edit Advance Types', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(4, 'Payroll', 'advance_types', 'Advance Types', 'advance_types.index', 'View Advance Types', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(5, 'Payroll', 'advance_types', 'Advance Types', 'advance_types.show', 'Show Advance Types', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(6, 'Payroll', 'advance_types', 'Advance Types', 'advance_types.store', 'Create Advance Types', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(7, 'Payroll', 'advance_types', 'Advance Types', 'advance_types.update', 'Update Advance Types', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(8, 'Payroll', 'advances', 'Advances', 'advances.create', 'Create Advances', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(9, 'Payroll', 'advances', 'Advances', 'advances.destroy', 'Delete Advances', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(10, 'Payroll', 'advances', 'Advances', 'advances.edit', 'Edit Advances', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(11, 'Payroll', 'advances', 'Advances', 'advances.index', 'View Advances', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(12, 'Payroll', 'advances', 'Advances', 'advances.show', 'Show Advances', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(13, 'Payroll', 'advances', 'Advances', 'advances.store', 'Create Advances', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(14, 'Payroll', 'advances', 'Advances', 'advances.update', 'Update Advances', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(15, 'Payroll', 'default', 'Default', 'allowance.create', 'Create Allowance', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'allowance', 'Allowance', 'READ', 0, NULL, NULL, NULL, NULL),
	(16, 'Payroll', 'default', 'Default', 'allowance.delete', 'Delete Allowance', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'allowance', 'Allowance', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(17, 'Payroll', 'default', 'Default', 'allowance.edit', 'Edit Allowance', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'allowance', 'Allowance', 'READ', 0, NULL, NULL, NULL, NULL),
	(18, 'Payroll', 'default', 'Default', 'allowance.index', 'View Allowances', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'allowance', 'Allowance', 'READ', 0, NULL, NULL, NULL, NULL),
	(19, 'Payroll', 'default', 'Default', 'allowance.store', 'Create Allowance', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'allowance', 'Allowance', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(20, 'Payroll', 'default', 'Default', 'allowance.update', 'Update Allowance', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'allowance', 'Allowance', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(21, 'Recruitment', 'General', 'General', 'applicant.hire', 'Hire Applicant', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(22, 'Recruitment', 'General', 'General', 'applicant.jobInterview', 'Schedule Job Interview', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(23, 'Recruitment', 'General', 'General', 'applicant.jobInterviewStore', 'Store Job Interview', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(24, 'Recruitment', 'General', 'General', 'applicant.reject', 'Reject Applicant', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(25, 'Recruitment', 'General', 'General', 'applicant.shortlist', 'Shortlist Applicant', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(26, 'Leave Management', 'leaves', 'Leaves', 'applyForLeave.create', 'Create Leave Application', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(27, 'Leave Management', 'leaves', 'Leaves', 'applyForLeave.index', 'View Leave Applications', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(28, 'Leave Management', 'leaves', 'Leaves', 'applyForLeave.show', 'Show Leave Application', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(29, 'Leave Management', 'leaves', 'Leaves', 'applyForLeave.store', 'Store Leave Application', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(30, 'Leave Management', 'applyForLeave', 'Apply For Leave', 'applyForLeave.applyOnBehalf.create', 'Apply Leave On Behalf of Employee', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(31, 'Leave Management', 'applyForLeave', 'Apply For Leave', 'applyForLeave.applyOnBehalf.store', 'Store Leave Application On Behalf', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(32, 'Recruitment', 'approvals', 'Approvals', 'approvals.create', 'Create Approval', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(33, 'Recruitment', 'approvals', 'Approvals', 'approvals.delete', 'Delete Approval', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(34, 'approvals', 'approvals', 'Approvals', 'approvals.index', 'View Approvals', '2026-05-19 15:07:11', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'READ', 0, NULL, NULL, NULL, NULL),
	(35, 'Recruitment', 'approvals', 'Approvals', 'approvals.store', 'Store Approval', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(36, 'Recruitment', 'approvals', 'Approvals', 'approvals.update', 'Update Approval', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(37, 'Recruitment', 'approvals', 'Approvals', 'approvals.view', 'View Approval', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(38, 'Attendance', 'reports', 'Reports', 'attendance.anomalies', 'View Attendance Anomalies', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(39, 'Attendance', 'reports', 'Reports', 'attendance.anomaliesStore', 'Store Attendance Anomalies', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(40, 'Attendance', 'reports', 'Reports', 'attendance.anomalyReport', 'Generate Anomaly Report', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(41, 'Attendance', 'reports', 'Reports', 'attendance.anomalyReportFilter', 'Filter Anomaly Report', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(42, 'Attendance', 'reports', 'Reports', 'attendance.approveOvertimes', 'Approve Overtimes', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(43, 'Attendance', 'reports', 'Reports', 'attendance.correctFromExcel', 'Correct Attendance From Excel', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(44, 'Attendance', 'General', 'General', 'attendance.dashboard', 'View Attendance Dashboard', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'manual_attendance', 'Manual attendance', 'READ', 0, NULL, NULL, NULL, NULL),
	(45, 'Attendance', 'General', 'General', 'attendance.dashboard.post', 'Post Attendance Dashboard', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'manual_attendance', 'Manual attendance', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(46, 'Attendance', 'reports', 'Reports', 'attendance.filterOvertime', 'Filter Overtime', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(47, 'Attendance', 'attendance', 'Attendance', 'attendance.mealReport', 'Generate Meal Report', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(48, 'Attendance', 'attendance', 'Attendance', 'attendance.mealReportFilter', 'Filter Meal Report', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(49, 'Attendance', 'reports', 'Reports', 'attendance.overtimeApproval', 'Overtime Approval', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(50, 'Attendance', 'reports', 'Reports', 'attendance.storeFromExcel', 'Store Attendance From Excel', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(51, 'Attendance', 'reports', 'Reports', 'attendance.view_raw_logs', 'View Raw Attendance Logs', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(52, 'Attendance', 'reports', 'Reports', 'attendanceSummaryReport.attendanceSummaryReport', 'Generate Attendance Summary Report', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(53, 'Attendance', 'reports', 'Reports', 'attendanceSummaryReport.attendanceSummaryReportFilter', 'Filter Attendance Summary Report', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(54, 'Award', 'awards', 'Awards', 'award.create', 'Create Award', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 2, 'awards', 'Awards', 'READ', 0, NULL, NULL, NULL, NULL),
	(55, 'Award', 'awards', 'Awards', 'award.delete', 'Delete Award', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 2, 'awards', 'Awards', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(56, 'Award', 'awards', 'Awards', 'award.edit', 'Edit Award', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 2, 'awards', 'Awards', 'READ', 0, NULL, NULL, NULL, NULL),
	(57, 'Award', 'awards', 'Awards', 'award.index', 'View Awards', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 2, 'awards', 'Awards', 'READ', 0, NULL, NULL, NULL, NULL),
	(58, 'Award', 'awards', 'Awards', 'award.store', 'Create Award', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 2, 'awards', 'Awards', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(59, 'Award', 'awards', 'Awards', 'award.update', 'Update Award', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 2, 'awards', 'Awards', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(60, 'Administration', 'azure', 'Azure', 'azure.login', 'Login to Azure', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(61, 'Payroll', 'reports', 'Reports', 'ahlReportIndex', 'View AHL Report', '2026-05-19 15:07:11', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(62, 'Leave Management', 'leaves', 'Leaves', 'allLeaveApplications.allLeaveApplications', 'View All Leave Applications', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(63, 'Leave Management', 'manage_leaves', 'Manage leaves', 'addRolloverLeave1', 'Add Rollover Leave', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 6, 'rollover_leaves', 'Rollover leaves', 'READ', 0, NULL, NULL, NULL, NULL),
	(64, 'Attendance', 'devices', 'Devices', 'biometricGet.index', 'View Biometric Devices', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'READ', 0, NULL, NULL, NULL, NULL),
	(65, 'Attendance', 'devices', 'Devices', 'biometricUpdate', 'Update Biometric Devices', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'READ', 0, NULL, NULL, NULL, NULL),
	(66, 'Attendance', 'devices', 'Devices', 'createDevice', 'Create Devices', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'READ', 0, NULL, NULL, NULL, NULL),
	(67, 'Attendance', 'devices', 'Devices', 'zkbiometricGet.index', 'View ZK Biometric Data', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'READ', 0, NULL, NULL, NULL, NULL),
	(68, 'Attendance', 'devices', 'Devices', 'storeDevice', 'Store Device', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(69, 'Attendance', 'devices', 'Devices', 'deleteBioDevice', 'Delete Bio Device', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(70, 'Attendance', 'devices', 'Devices', 'editBioDevice', 'Edit Bio Device', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'READ', 0, NULL, NULL, NULL, NULL),
	(71, 'Attendance', 'devices', 'Devices', 'posteditBioDevice', 'Post Edit Bio Device', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(72, 'Attendance', 'devices', 'Devices', 'devices', 'Manage Devices', '2026-05-19 15:07:11', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'READ', 0, NULL, NULL, NULL, NULL),
	(73, 'Payroll', 'setup', 'Setup', 'bonus_types.create', 'Create Bonus Types', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(74, 'Payroll', 'setup', 'Setup', 'bonus_types.destroy', 'Delete Bonus Types', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(75, 'Payroll', 'setup', 'Setup', 'bonus_types.edit', 'Edit Bonus Types', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(76, 'Payroll', 'setup', 'Setup', 'bonus_types.index', 'View Bonus Types', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(77, 'Payroll', 'setup', 'Setup', 'bonus_types.show', 'Show Bonus Types', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(78, 'Payroll', 'setup', 'Setup', 'bonus_types.store', 'Create Bonus Types', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(79, 'Payroll', 'setup', 'Setup', 'bonus_types.update', 'Update Bonus Types', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(80, 'Payroll', 'setup', 'Setup', 'bonuses.create', 'Create Bonuses', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(81, 'Payroll', 'setup', 'Setup', 'bonuses.destroy', 'Delete Bonuses', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(82, 'Payroll', 'setup', 'Setup', 'bonuses.edit', 'Edit Bonuses', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(83, 'Payroll', 'setup', 'Setup', 'bonuses.index', 'View Bonuses', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(84, 'Payroll', 'setup', 'Setup', 'bonuses.show', 'Show Bonuses', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(85, 'Payroll', 'setup', 'Setup', 'bonuses.store', 'Create Bonuses', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(86, 'Payroll', 'setup', 'Setup', 'bonuses.update', 'Update Bonuses', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(87, 'Payroll', 'setup', 'Setup', 'bonusSetting.create', 'Create Bonus Settings', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'bonus', 'Bonus', 'READ', 0, NULL, NULL, NULL, NULL),
	(88, 'Payroll', 'setup', 'Setup', 'bonusSetting.delete', 'Delete Bonus Settings', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'bonus', 'Bonus', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(89, 'Payroll', 'setup', 'Setup', 'bonusSetting.edit', 'Edit Bonus Settings', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'bonus', 'Bonus', 'READ', 0, NULL, NULL, NULL, NULL),
	(90, 'Payroll', 'setup', 'Setup', 'bonusSetting.index', 'View Bonus Settings', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'bonus', 'Bonus', 'READ', 0, NULL, NULL, NULL, NULL),
	(91, 'Payroll', 'setup', 'Setup', 'bonusSetting.store', 'Create Bonus Settings', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'bonus', 'Bonus', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(92, 'Payroll', 'setup', 'Setup', 'bonusSetting.update', 'Update Bonus Settings', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'bonus', 'Bonus', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(93, 'Employee Management', 'branch', 'Locations', 'branch.create', 'Create Locations', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(94, 'Employee Management', 'branch', 'Locations', 'branch.delete', 'Delete Locations', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(95, 'Employee Management', 'branch', 'Locations', 'branch.edit', 'Edit Locations', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(96, 'Employee Management', 'branch', 'Locations', 'branch.index', 'View Locations', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(97, 'Employee Management', 'branch', 'Locations', 'branch.store', 'Create Locations', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(98, 'Employee Management', 'branch', 'Locations', 'branch.update', 'Update Locations', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(99, 'Payroll', 'salaries', 'Salaries', 'calculateManagementPay', 'Calculate Management Pay', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(100, 'Payroll', 'salaries', 'Salaries', 'calculatePaye', 'Calculate PAYE', '2026-05-19 15:07:11', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(101, 'Leave Management', 'leaves', 'Leaves', 'ceoPendingLeaveRequests.ceoPendingLeaveRequests', 'View CEO Pending Leave Requests', '2026-05-19 15:07:11', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(102, 'Administration', 'changePassword', 'Change Passwords', 'changePassword.create', 'Create Change Password Requests', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(103, 'Administration', 'changePassword', 'Change Passwords', 'changePassword.destroy', 'Delete Change Password Requests', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(104, 'Administration', 'changePassword', 'Change Passwords', 'changePassword.edit', 'Edit Change Password Requests', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(105, 'Administration', 'changePassword', 'Change Passwords', 'changePassword.index', 'View Change Password Requests', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(106, 'Administration', 'changePassword', 'Change Passwords', 'changePassword.show', 'Show Change Password Request Details', '2026-05-19 15:07:11', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(107, 'Administration', 'changePassword', 'Change Passwords', 'changePassword.store', 'Create Change Password Requests', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(108, 'Administration', 'changePassword', 'Change Passwords', 'changePassword.update', 'Update Change Password Requests', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(109, 'Settings', 'settings', 'Settings', 'company.setting', 'View Company Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'READ', 0, NULL, NULL, NULL, NULL),
	(110, 'Settings', 'settings', 'Settings', 'company.setting.post', 'Update Company Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 8, 'Front End', 'Front End', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(111, 'Employee Management', 'General', 'General', 'contract.create', 'Create Contracts', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'contracts', 'Contracts', 'READ', 0, NULL, NULL, NULL, NULL),
	(112, 'Employee Management', 'General', 'General', 'contract.delete', 'Delete Contracts', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'contracts', 'Contracts', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(113, 'Employee Management', 'General', 'General', 'contract.destroy', 'Destroy Contracts', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'contracts', 'Contracts', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(114, 'Employee Management', 'General', 'General', 'contract.edit', 'Edit Contracts', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'contracts', 'Contracts', 'READ', 0, NULL, NULL, NULL, NULL),
	(115, 'Employee Management', 'General', 'General', 'contract.index', 'View Contracts', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'contracts', 'Contracts', 'READ', 0, NULL, NULL, NULL, NULL),
	(116, 'Employee Management', 'General', 'General', 'contract.show', 'Show Contract Details', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'contracts', 'Contracts', 'READ', 0, NULL, NULL, NULL, NULL),
	(117, 'Employee Management', 'General', 'General', 'contract.store', 'Create Contracts', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'contracts', 'Contracts', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(118, 'Employee Management', 'General', 'General', 'contract.update', 'Update Contracts', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'contracts', 'Contracts', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(119, 'Attendance', 'daily_pay', 'Daily Pay', 'daily_pay.create', 'Create Daily Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(120, 'Attendance', 'daily_pay', 'Daily Pay', 'daily_pay.destroy', 'Delete Daily Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(121, 'Attendance', 'daily_pay', 'Daily Pay', 'daily_pay.edit', 'Edit Daily Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(122, 'Attendance', 'daily_pay', 'Daily Pay', 'daily_pay.index', 'View Daily Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(123, 'Attendance', 'daily_pay', 'Daily Pay', 'daily_pay.show', 'Show Daily Pay Details', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(124, 'Attendance', 'daily_pay', 'Daily Pay', 'daily_pay.store', 'Create Daily Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(125, 'Attendance', 'daily_pay', 'Daily Pay', 'daily_pay.update', 'Update Daily Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(126, 'Attendance', 'reports', 'Reports', 'dailyAttendance.dailyAttendance', 'View Daily Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(127, 'Attendance', 'reports', 'Reports', 'dailyAttendance.dailyAttendanceFilter', 'Filter Daily Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(128, 'Attendance', 'DailyPay', 'Daily Pay Management', 'DailyPay.import', 'Import Daily Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(129, 'Attendance', 'DailyPay', 'Daily Pay Management', 'dailyPay.importView', 'View Daily Pay Import', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(130, 'Payroll', 'deduction', 'Deductions', 'deduction.create', 'Create Deduction', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(131, 'Payroll', 'deduction', 'Deductions', 'deduction.delete', 'Delete Deduction', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(132, 'Payroll', 'deduction', 'Deductions', 'deduction.edit', 'Edit Deduction', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(133, 'Payroll', 'deduction', 'Deductions', 'deduction.index', 'View Deductions', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(134, 'Payroll', 'deduction', 'Deductions', 'deduction.store', 'Create Deduction', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(135, 'Payroll', 'deduction', 'Deductions', 'deduction.update', 'Update Deduction', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(136, 'Payroll', 'salaries', 'Salaries', 'delete_salary_entry', 'Delete Salary Entry', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(137, 'Employee Management', 'General', 'General', 'department.create', 'Create Department', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'department', 'Department', 'READ', 0, NULL, NULL, NULL, NULL),
	(138, 'Employee Management', 'General', 'General', 'department.delete', 'Delete Department', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'department', 'Department', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(139, 'Employee Management', 'General', 'General', 'department.edit', 'Edit Department', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'department', 'Department', 'READ', 0, NULL, NULL, NULL, NULL),
	(140, 'Employee Management', 'General', 'General', 'department.index', 'View Departments', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'department', 'Department', 'READ', 0, NULL, NULL, NULL, NULL),
	(141, 'Employee Management', 'General', 'General', 'department.store', 'Create Department', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'department', 'Department', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(142, 'Employee Management', 'General', 'General', 'department.update', 'Update Department', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'department', 'Department', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(143, 'Employee Management', 'General', 'General', 'designation.create', 'Create Designation', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'designation', 'Designation', 'READ', 0, NULL, NULL, NULL, NULL),
	(144, 'Employee Management', 'General', 'General', 'designation.delete', 'Delete Designation', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'designation', 'Designation', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(145, 'Employee Management', 'General', 'General', 'designation.edit', 'Edit Designation', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'designation', 'Designation', 'READ', 0, NULL, NULL, NULL, NULL),
	(146, 'Employee Management', 'General', 'General', 'designation.index', 'View Designations', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'designation', 'Designation', 'READ', 0, NULL, NULL, NULL, NULL),
	(147, 'Employee Management', 'General', 'General', 'designation.store', 'Create Designation', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'designation', 'Designation', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(148, 'Employee Management', 'General', 'General', 'designation.update', 'Update Designation', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'designation', 'Designation', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(149, 'Payroll', 'salaries', 'Salaries', 'downloadPayslip', 'Download Payslips', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(150, 'Payroll', 'salaries', 'Salaries', 'downloadPayslip.self', 'Download Your Payslip', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(151, 'Leave Management', 'leaves', 'Leaves', 'downloadStaffReport.downloadStaffReport', 'Download Staff Report', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(152, 'Attendance', 'General', 'General', 'duplictes.remove', 'Remove Duplicates', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'manual_attendance', 'Manual attendance', 'READ', 0, NULL, NULL, NULL, NULL),
	(153, 'Employee Management', 'General', 'General', 'employee.active', 'Activate Employee', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(154, 'Employee Management', 'General', 'General', 'employee.create', 'Create Employee', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'READ', 0, NULL, NULL, NULL, NULL),
	(155, 'Employee Management', 'General', 'General', 'employee.delete', 'Delete Employee', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(156, 'Employee Management', 'General', 'General', 'employee.disable', 'Disable Employee', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'READ', 0, NULL, NULL, NULL, NULL),
	(157, 'Employee Management', 'General', 'General', 'employee.downloadReport', 'Download Employee Report', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(158, 'Employee Management', 'General', 'General', 'employee.edit', 'Edit Employee', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'READ', 0, NULL, NULL, NULL, NULL),
	(159, 'Employee Management', 'General', 'General', 'employee.enable', 'Enable Employee', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'READ', 0, NULL, NULL, NULL, NULL),
	(160, 'Employee Management', 'General', 'General', 'employee.importView', 'View Employee Import', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(161, 'Employee Management', 'General', 'General', 'employee.index', 'View Employees', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'READ', 0, NULL, NULL, NULL, NULL),
	(162, 'Employee Management', 'General', 'General', 'employee.joinersReport', 'View Employee Joiners Report', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(163, 'Employee Management', 'General', 'General', 'employee.leaversReport', 'View Employee Leavers Report', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(164, 'Employee Management', 'General', 'General', 'employee.movementReport', 'View Employee Movement Report', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(165, 'Employee Management', 'General', 'General', 'employee.show', 'Show Employee Details', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'READ', 0, NULL, NULL, NULL, NULL),
	(166, 'Employee Management', 'General', 'General', 'employee.store', 'Create Employee', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(167, 'Employee Management', 'General', 'General', 'employee.update', 'Update Employee', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(168, 'Employee Management', 'General', 'General', 'employeeGroup.create', 'Create Employee Group', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(169, 'Employee Management', 'General', 'General', 'employeeGroup.destroy', 'Destroy Employee Group', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(170, 'Employee Management', 'General', 'General', 'employeeGroup.edit', 'Edit Employee Group', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(171, 'Employee Management', 'General', 'General', 'employeeGroup.index', 'View Employee Groups', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(172, 'Employee Management', 'General', 'General', 'employeeGroup.show', 'Show Employee Group Details', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(173, 'Employee Management', 'General', 'General', 'employeeGroup.store', 'Create Employee Group', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(174, 'Employee Management', 'General', 'General', 'employeeGroup.update', 'Update Employee Group', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(175, 'Employee Management', 'General', 'General', 'employeeMovement.create', 'Create Employee Movement', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'READ', 0, NULL, NULL, NULL, NULL),
	(176, 'Employee Management', 'General', 'General', 'employeeMovement.delete', 'Delete Employee Movement', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(177, 'Employee Management', 'General', 'General', 'employeeMovement.destroy', 'Destroy Employee Movement', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(178, 'Employee Management', 'General', 'General', 'employeeMovement.edit', 'Edit Employee Movement', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'READ', 0, NULL, NULL, NULL, NULL),
	(179, 'Employee Management', 'General', 'General', 'employeeMovement.index', 'View Employee Movements', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'READ', 0, NULL, NULL, NULL, NULL),
	(180, 'Employee Management', 'General', 'General', 'employeeMovement.show', 'Show Employee Movement Details', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'READ', 0, NULL, NULL, NULL, NULL),
	(181, 'Employee Management', 'General', 'General', 'employeeMovement.store', 'Create Employee Movement', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(182, 'Employee Management', 'General', 'General', 'employeeMovement.undoChanges', 'Undo Employee Movement Changes', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'READ', 0, NULL, NULL, NULL, NULL),
	(183, 'Employee Management', 'General', 'General', 'employeeMovement.update', 'Update Employee Movement', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(184, 'Employee Management', 'General', 'General', 'employeeMovementImport', 'Import Employee Movements', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'READ', 0, NULL, NULL, NULL, NULL),
	(185, 'Employee Management', 'General', 'General', 'employeeSection.create', 'Create Employee Section', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(186, 'Employee Management', 'General', 'General', 'employeeSection.destroy', 'Destroy Employee Section', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(187, 'Employee Management', 'General', 'General', 'employeeSection.edit', 'Edit Employee Section', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(188, 'Employee Management', 'General', 'General', 'employeeSection.index', 'View Employee Sections', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(189, 'Employee Management', 'General', 'General', 'employeeSection.show', 'Show Employee Section Details', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(190, 'Employee Management', 'General', 'General', 'employeeSection.store', 'Create Employee Section', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(191, 'Employee Management', 'General', 'General', 'employeeSection.update', 'Update Employee Section', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(192, 'Training', 'employeeTrainingReport', 'Employee Training Report', 'employeeTrainingReport.employeeTrainingReport', 'View Employee Training Report', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(193, 'Employee Management', 'General', 'General', 'export', 'Export Data', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(194, 'Settings', 'settings', 'Settings', 'front.setting', 'View Front Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'READ', 0, NULL, NULL, NULL, NULL),
	(195, 'Settings', 'settings', 'Settings', 'front.setting.submit', 'Submit Front Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(196, 'Payroll', 'salaries', 'Salaries', 'geneMgtPayroll', 'Manage Gene Payroll', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(197, 'Settings', 'settings', 'Settings', 'generalSettings.edit', 'Edit General Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 8, 'General', 'General', 'READ', 0, NULL, NULL, NULL, NULL),
	(198, 'Settings', 'settings', 'Settings', 'generalSettings.index', 'View General Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 8, 'General', 'General', 'READ', 0, NULL, NULL, NULL, NULL),
	(199, 'Settings', 'settings', 'Settings', 'generalSettings.store', 'Create General Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 8, 'General', 'General', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(200, 'Settings', 'settings', 'Settings', 'generalSettings.update', 'Update General Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 8, 'General', 'General', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(201, 'Payroll', 'salaries', 'Salaries', 'generate_payroll_request', 'Create Payroll Request', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(202, 'Payroll', 'salaries', 'Salaries', 'generate_payroll_request_mgmt', 'Manage Payroll Requests', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(203, 'Payroll', 'setup', 'Setup', 'generateBonus.create', 'Create Bonus', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'generate_bonus', 'Generate bonus', 'READ', 0, NULL, NULL, NULL, NULL),
	(204, 'Payroll', 'setup', 'Setup', 'generateBonus.filter', 'Filter Bonus', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'generate_bonus', 'Generate bonus', 'READ', 0, NULL, NULL, NULL, NULL),
	(205, 'Payroll', 'setup', 'Setup', 'generateBonus.index', 'View Bonuses', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'generate_bonus', 'Generate bonus', 'READ', 0, NULL, NULL, NULL, NULL),
	(206, 'Payroll', 'salaries', 'Salaries', 'generatePayrollExcel', 'Generate Payroll Excel Report', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(207, 'Payroll', 'salaries', 'Salaries', 'generatePayslip', 'Generate Payslips', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(208, 'Payroll', 'salaries', 'Salaries', 'generatePayslip.self', 'Generate Your Payslip', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(209, 'Leave Management', 'leaves', 'Leaves', 'generateReport.generateReport', 'Generate Reports', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(210, 'Payroll', 'salaries', 'Salaries', 'generateSalary.massGenerate', 'Mass Generate Salaries', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(211, 'Payroll', 'salaries', 'Salaries', 'generateSalarySheet.calculateEmployeeSalary', 'Calculate Employee Salary', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(212, 'Payroll', 'salaries', 'Salaries', 'generateSalarySheet.create', 'Create Salary Sheet', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(213, 'Payroll', 'salaries', 'Salaries', 'generateSalarySheet.index', 'View Salary Sheets', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(214, 'Payroll', 'salaries', 'Salaries', 'generateSalarySheet.monthSalary', 'Generate Monthly Salary Sheet', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(215, 'Leave Management', 'setup', 'Setup', 'holiday.create', 'Create Holiday', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'READ', 0, NULL, NULL, NULL, NULL),
	(216, 'Leave Management', 'setup', 'Setup', 'holiday.delete', 'Delete Holiday', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(217, 'Leave Management', 'setup', 'Setup', 'holiday.edit', 'Edit Holiday', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'READ', 0, NULL, NULL, NULL, NULL),
	(218, 'Leave Management', 'setup', 'Setup', 'holiday.index', 'View Holidays', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'READ', 0, NULL, NULL, NULL, NULL),
	(219, 'Leave Management', 'setup', 'Setup', 'holiday.store', 'Create Holiday', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(220, 'Leave Management', 'setup', 'Setup', 'holiday.update', 'Update Holiday', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(221, 'Payroll', 'setup', 'Setup', 'hourlyWages.create', 'Create Hourly Wages', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'hourly_wages', 'Hourly wages', 'READ', 0, NULL, NULL, NULL, NULL),
	(222, 'Payroll', 'setup', 'Setup', 'hourlyWages.destroy', 'Destroy Hourly Wages', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'hourly_wages', 'Hourly wages', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(223, 'Payroll', 'setup', 'Setup', 'hourlyWages.edit', 'Edit Hourly Wages', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'hourly_wages', 'Hourly wages', 'READ', 0, NULL, NULL, NULL, NULL),
	(224, 'Payroll', 'setup', 'Setup', 'hourlyWages.index', 'View Hourly Wages', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'hourly_wages', 'Hourly wages', 'READ', 0, NULL, NULL, NULL, NULL),
	(225, 'Payroll', 'setup', 'Setup', 'hourlyWages.show', 'Show Hourly Wage Details', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'hourly_wages', 'Hourly wages', 'READ', 0, NULL, NULL, NULL, NULL),
	(226, 'Payroll', 'setup', 'Setup', 'hourlyWages.store', 'Create Hourly Wages', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'hourly_wages', 'Hourly wages', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(227, 'Payroll', 'setup', 'Setup', 'hourlyWages.update', 'Update Hourly Wages', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'hourly_wages', 'Hourly wages', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(228, 'Employee Management', 'General', 'General', 'importUsers', 'Import Users', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(229, 'Administration', 'invalidLicense', 'Invalid License', 'invalidLicense', 'View Invalid License', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(230, 'Attendance', 'General', 'General', 'ip.attendance', 'View IP Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'ip_attendance', 'Ip attendance', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(231, 'Employee Management', 'job_category', 'Job Category', 'job_category.create', 'Create Job Category', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(232, 'Employee Management', 'job_category', 'Job Category', 'job_category.destroy', 'Destroy Job Category', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(233, 'Employee Management', 'job_category', 'Job Category', 'job_category.edit', 'Edit Job Category', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(234, 'Employee Management', 'job_category', 'Job Category', 'job_category.index', 'View Job Categories', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(235, 'Employee Management', 'job_category', 'Job Category', 'job_category.show', 'Show Job Category Details', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(236, 'Employee Management', 'job_category', 'Job Category', 'job_category.store', 'Create Job Category', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(237, 'Employee Management', 'job_category', 'Job Category', 'job_category.update', 'Update Job Category', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(238, 'Recruitment', 'job', 'Job Management', 'job.application', 'Manage Job Applications', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(239, 'Recruitment', 'job', 'Job Management', 'job.details', 'View Job Details', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(240, 'Administration', 'default', 'Default', 'job.internal_details', 'View Internal Job Details', '2026-05-19 15:07:12', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(241, 'Recruitment', 'General', 'General', 'jobCandidate.applyCandidateList', 'View Applied Candidates', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(242, 'Recruitment', 'General', 'General', 'jobCandidate.index', 'View Job Candidates', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(243, 'Recruitment', 'General', 'General', 'jobCandidate.jobHireList', 'View Job Hire List', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(244, 'Recruitment', 'General', 'General', 'jobCandidate.jobInterviewList', 'View Job Interview List', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(245, 'Recruitment', 'General', 'General', 'jobCandidate.rejectedApplicant', 'View Rejected Applicants', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(246, 'Recruitment', 'General', 'General', 'jobCandidate.shortListedApplicant', 'View Shortlisted Applicants', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(247, 'Recruitment', 'jobCategory', 'Job Category Management', 'jobCategory.import', 'Import Job Categories', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(248, 'Recruitment', 'jobCategory', 'Job Category Management', 'jobCategory.importView', 'View Job Category Import', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(249, 'Employee Management', 'jobGroups', 'Job Groups', 'jobGroups.create', 'Create Job Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(250, 'Employee Management', 'jobGroups', 'Job Groups', 'jobGroups.destroy', 'Destroy Job Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(251, 'Employee Management', 'jobGroups', 'Job Groups', 'jobGroups.edit', 'Edit Job Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(252, 'Employee Management', 'jobGroups', 'Job Groups', 'jobGroups.index', 'View Job Groups', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(253, 'Employee Management', 'jobGroups', 'Job Groups', 'jobGroups.show', 'Show Job Group Details', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(254, 'Employee Management', 'jobGroups', 'Job Groups', 'jobGroups.store', 'Create Job Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(255, 'Employee Management', 'jobGroups', 'Job Groups', 'jobGroups.update', 'Update Job Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(256, 'Recruitment', 'General', 'General', 'jobPost.create', 'Create Job Post', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_post', 'Job post', 'READ', 0, NULL, NULL, NULL, NULL),
	(257, 'Recruitment', 'General', 'General', 'jobPost.delete', 'Delete Job Post', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_post', 'Job post', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(258, 'Recruitment', 'General', 'General', 'jobPost.edit', 'Edit Job Post', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_post', 'Job post', 'READ', 0, NULL, NULL, NULL, NULL),
	(259, 'Recruitment', 'General', 'General', 'jobPost.index', 'View Job Posts', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_post', 'Job post', 'READ', 0, NULL, NULL, NULL, NULL),
	(260, 'Recruitment', 'General', 'General', 'jobPost.show', 'Show Job Post Details', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_post', 'Job post', 'READ', 0, NULL, NULL, NULL, NULL),
	(261, 'Recruitment', 'General', 'General', 'jobPost.store', 'Create Job Post', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_post', 'Job post', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(262, 'Recruitment', 'General', 'General', 'jobPost.update', 'Update Job Post', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 7, 'job_post', 'Job post', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(263, 'Leave Management', 'leaves', 'Leaves', 'leaveApplication.delete', 'Delete Leave Application', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(264, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leaveManagement.manualUpload', 'Upload Leave Data Manually', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 6, 'uploads', 'Uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(265, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leaveManagement.manualUploadSave', 'Create Manual Leave Data Upload', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 6, 'uploads', 'Uploads', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(266, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leaveManagement.manualUploadView', 'View Manual Leave Data Upload', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 6, 'uploads', 'Uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(267, 'Leave Management', 'leaves', 'Leaves', 'leaveReport.fullOrganizationReport', 'Generate Full Organization Leave Report', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(268, 'Leave Management', 'leaveReport', 'Leave Reports', 'leaveReport.leaveReport', 'Generate Leave Report', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(269, 'Leave Management', 'setup', 'Setup', 'leaveType.create', 'Create Leave Type', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'leave_type', 'Leave type', 'READ', 0, NULL, NULL, NULL, NULL),
	(270, 'Leave Management', 'setup', 'Setup', 'leaveType.delete', 'Delete Leave Type', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 6, 'leave_type', 'Leave type', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(271, 'Leave Management', 'setup', 'Setup', 'leaveType.edit', 'Edit Leave Type', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'leave_type', 'Leave type', 'READ', 0, NULL, NULL, NULL, NULL),
	(272, 'Leave Management', 'setup', 'Setup', 'leaveType.index', 'View Leave Types', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'leave_type', 'Leave type', 'READ', 0, NULL, NULL, NULL, NULL),
	(273, 'Leave Management', 'setup', 'Setup', 'leaveType.store', 'Create Leave Type', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'leave_type', 'Leave type', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(274, 'Leave Management', 'setup', 'Setup', 'leaveType.update', 'Update Leave Type', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 6, 'leave_type', 'Leave type', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(275, 'Administration', 'licenses', 'Licenses', 'licenses', 'Manage Licenses', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(276, 'Administration', 'login', 'Login', 'login', 'Login to the System', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(277, 'Payroll', 'salaries', 'Salaries', 'managementPay.index', 'View Management Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(278, 'Payroll', 'salaries', 'Salaries', 'managementPayrollDataExport', 'Export Management Payroll Data', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payroll9', 'Payroll9', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(279, 'Attendance', 'reports', 'Reports', 'manualAttendance.filter', 'Filter Manual Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(280, 'Attendance', 'reports', 'Reports', 'manualAttendance.manualAttendance', 'Record Manual Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(281, 'Attendance', 'reports', 'Reports', 'manualAttendance.store', 'Create Manual Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(282, 'Attendance', 'General', 'General', 'migrateAttendanceData', 'Migrate Attendance Data', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'manual_attendance', 'Manual attendance', 'READ', 0, NULL, NULL, NULL, NULL),
	(283, 'Attendance', 'reports', 'Reports', 'monthlyAttendance.monthlyAttendance', 'View Monthly Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(284, 'Attendance', 'reports', 'Reports', 'monthlyAttendance.monthlyAttendanceFilter', 'Filter Monthly Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(285, 'Attendance', 'reports', 'Reports', 'myAttendanceReport.myAttendanceReport', 'View My Attendance Report', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(286, 'Attendance', 'reports', 'Reports', 'myAttendanceReport.myAttendanceReportFilter', 'Filter My Attendance Report', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(287, 'Leave Management', 'myLeaveReport', 'My Leave Report', 'myLeaveReport.myLeaveReport', 'View My Leave Report', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(288, 'Employee Management', 'myPayroll', 'My Payroll', 'myPayroll.myPayroll', 'View My Payroll', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(289, 'Attendance', 'General', 'General', 'newAttendance.filter', 'Filter New Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'manual_attendance', 'Manual attendance', 'READ', 0, NULL, NULL, NULL, NULL),
	(290, 'Attendance', 'General', 'General', 'newAttendance.store', 'Create New Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'manual_attendance', 'Manual attendance', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(291, 'Attendance', 'General', 'General', 'newAttendanceIndex', 'View New Attendance Index', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'manual_attendance', 'Manual attendance', 'READ', 0, NULL, NULL, NULL, NULL),
	(292, 'Payroll', 'salaries', 'Salaries', 'newManagementSalaryCalculate', 'Calculate New Management Salary', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payroll9', 'Payroll9', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(293, 'Attendance', 'reports', 'Reports', 'newMonthlyAttendance.monthlyAttendance', 'View New Monthly Attendance', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(294, 'Payroll', 'salaries', 'Salaries', 'newSalaryCalculate', 'Calculate New Salary', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payroll9', 'Payroll9', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(295, 'Payroll', 'setup', 'Setup', 'nhif.create', 'Create NHIF Record', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(296, 'Payroll', 'setup', 'Setup', 'nhif.destroy', 'Destroy NHIF Record', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(297, 'Payroll', 'setup', 'Setup', 'nhif.edit', 'Edit NHIF Record', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(298, 'Payroll', 'setup', 'Setup', 'nhif.index', 'View NHIF Records', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(299, 'Payroll', 'setup', 'Setup', 'nhif.show', 'Show NHIF Record Details', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'READ', 0, NULL, NULL, NULL, NULL),
	(300, 'Payroll', 'setup', 'Setup', 'nhif.store', 'Create NHIF Record', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(301, 'Payroll', 'setup', 'Setup', 'nhif.update', 'Update NHIF Record', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'pay_group_job_category', 'Pay group job category', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(302, 'Payroll', 'reports', 'Reports', 'nhifReportsIndex', 'View NHIF Reports', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(303, 'Notice Board', 'notices', 'Notices', 'notice.create', 'Create Notice', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 3, 'notices', 'Notices', 'READ', 0, NULL, NULL, NULL, NULL),
	(304, 'Notice Board', 'notices', 'Notices', 'notice.delete', 'Delete Notice', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 3, 'notices', 'Notices', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(305, 'Notice Board', 'notices', 'Notices', 'notice.edit', 'Edit Notice', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 3, 'notices', 'Notices', 'READ', 0, NULL, NULL, NULL, NULL),
	(306, 'Notice Board', 'notices', 'Notices', 'notice.index', 'View Notices', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 3, 'notices', 'Notices', 'READ', 0, NULL, NULL, NULL, NULL),
	(307, 'Notice Board', 'notices', 'Notices', 'notice.show', 'Show Notice Details', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 3, 'notices', 'Notices', 'READ', 0, NULL, NULL, NULL, NULL),
	(308, 'Notice Board', 'notices', 'Notices', 'notice.store', 'Create Notice', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 3, 'notices', 'Notices', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(309, 'Notice Board', 'notices', 'Notices', 'notice.update', 'Update Notice', '2026-05-19 15:07:12', '2026-05-19 15:07:14', NULL, 3, 'notices', 'Notices', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(310, 'Payroll', 'reports', 'Reports', 'nssfReportsIndex', 'View NSSF Reports', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(311, 'Payroll', 'payGrade', 'Pay Grade', 'payGrade.create', 'Create Pay Grade', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(312, 'Payroll', 'payGrade', 'Pay Grade', 'payGrade.destroy', 'Destroy Pay Grade', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(313, 'Payroll', 'payGrade', 'Pay Grade', 'payGrade.edit', 'Edit Pay Grade', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(314, 'Payroll', 'payGrade', 'Pay Grade', 'payGrade.index', 'View Pay Grades', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(315, 'Payroll', 'payGrade', 'Pay Grade', 'payGrade.show', 'Show Pay Grade Details', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(316, 'Payroll', 'payGrade', 'Pay Grade', 'payGrade.store', 'Create Pay Grade', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(317, 'Payroll', 'payGrade', 'Pay Grade', 'payGrade.update', 'Update Pay Grade', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(318, 'Payroll', 'paygroup', 'Pay Group', 'paygroup.create', 'Create Pay Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(319, 'Payroll', 'paygroup', 'Pay Group', 'paygroup.destroy', 'Destroy Pay Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(320, 'Payroll', 'paygroup', 'Pay Group', 'paygroup.edit', 'Edit Pay Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(321, 'Payroll', 'paygroup', 'Pay Group', 'paygroup.index', 'View Pay Groups', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(322, 'Payroll', 'paygroup', 'Pay Group', 'paygroup.show', 'Show Pay Group Details', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(323, 'Payroll', 'paygroup', 'Pay Group', 'paygroup.store', 'Create Pay Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(324, 'Payroll', 'paygroup', 'Pay Group', 'paygroup.update', 'Update Pay Group', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(325, 'Payroll', 'paymentHistory', 'Payment History', 'paymentHistory.paymentHistory', 'View Payment History', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(326, 'Payroll', 'setup', 'Setup', 'payoutChannel.create', 'Create Payout Channel', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payout_channels', 'Payout channels', 'READ', 0, NULL, NULL, NULL, NULL),
	(327, 'Payroll', 'setup', 'Setup', 'payoutChannel.delete', 'Delete Payout Channel', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payout_channels', 'Payout channels', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(328, 'Payroll', 'setup', 'Setup', 'payoutChannel.deleteFromStaff', 'Delete Payout Channel from Staff', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payout_channels', 'Payout channels', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(329, 'Payroll', 'setup', 'Setup', 'payoutChannel.edit', 'Edit Payout Channel', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payout_channels', 'Payout channels', 'READ', 0, NULL, NULL, NULL, NULL),
	(330, 'Payroll', 'setup', 'Setup', 'payoutChannel.index', 'View Payout Channels', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payout_channels', 'Payout channels', 'READ', 0, NULL, NULL, NULL, NULL),
	(331, 'Payroll', 'setup', 'Setup', 'payoutChannel.show', 'Show Payout Channel Details', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payout_channels', 'Payout channels', 'READ', 0, NULL, NULL, NULL, NULL),
	(332, 'Payroll', 'setup', 'Setup', 'payoutChannel.store', 'Create Payout Channel', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payout_channels', 'Payout channels', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(333, 'Payroll', 'setup', 'Setup', 'payoutChannel.update', 'Update Payout Channel', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payout_channels', 'Payout channels', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(334, 'Payroll', 'setup', 'Setup', 'payoutChannel.updateStaff', 'Update Payout Channel for Staff', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payout_channels', 'Payout channels', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(335, 'Payroll', 'salaries', 'Salaries', 'payroll.view', 'View Payroll', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(336, 'Payroll', 'salaries', 'Salaries', 'payroll9.create', 'Create Payroll 9', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(337, 'Payroll', 'salaries', 'Salaries', 'payroll9.destroy', 'Destroy Payroll 9', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(338, 'Payroll', 'salaries', 'Salaries', 'payroll9.edit', 'Edit Payroll 9', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(339, 'Payroll', 'salaries', 'Salaries', 'payroll9.generate', 'Generate Payroll 9', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(340, 'Payroll', 'salaries', 'Salaries', 'payroll9.index', 'View Payroll 9', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(341, 'Payroll', 'salaries', 'Salaries', 'payroll9.massMail', 'Mass Mail Payroll 9', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(342, 'Payroll', 'salaries', 'Salaries', 'payroll9.preview', 'Preview Payroll 9', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(343, 'Payroll', 'salaries', 'Salaries', 'payroll9.preview1', 'Preview Payroll 9 (1)', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(344, 'Payroll', 'salaries', 'Salaries', 'payroll9.preview2', 'Preview Payroll 9 (2)', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(345, 'Payroll', 'salaries', 'Salaries', 'payroll9.show', 'Show Payroll 9 Details', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(346, 'Payroll', 'salaries', 'Salaries', 'payroll9.store', 'Create Payroll 9', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(347, 'Payroll', 'salaries', 'Salaries', 'payroll9.update', 'Update Payroll 9', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(348, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_ahl', 'Calculate AHL Payroll', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(349, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_gross', 'Calculate Gross Payroll', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(350, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_index', 'Index Payroll Calculator', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(351, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_insurance_relief', 'Calculate Insurance Relief', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(352, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_net_pay', 'Calculate Net Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(353, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_nhif', 'Calculate NHIF Payroll', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(354, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_nssf', 'Calculate NSSF Payroll', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(355, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_paye', 'Calculate PAYE', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(356, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_personal_relief', 'Calculate Personal Relief', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(357, 'Payroll', 'payrollcaculator', 'Payroll Calculator', 'payrollcaculator_taxable_pay', 'Calculate Taxable Pay', '2026-05-19 15:07:12', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(358, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.ahl', 'Calculate AHL Payroll (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(359, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.gross', 'Calculate Gross Payroll (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(360, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.index', 'Index Payroll Calculator (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(361, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.insurance_relief', 'Calculate Insurance Relief (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(362, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.net_pay', 'Calculate Net Pay (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(363, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.nhif', 'Calculate NHIF Payroll (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(364, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.nssf', 'Calculate NSSF Payroll (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(365, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.paye', 'Calculate PAYE (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(366, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.personal_relief', 'Calculate Personal Relief (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(367, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.taxable_pay', 'Calculate Taxable Pay (Advanced)', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(368, 'Payroll', 'salaries', 'Salaries', 'payrollDataExport', 'Export Payroll Data', '2026-05-19 15:07:12', '2026-05-19 15:07:18', NULL, 9, 'payroll9', 'Payroll9', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(369, 'Payroll', 'default', 'Default', 'payrollIndex', 'View Payroll Index', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(370, 'Leave Management', 'leaves', 'Leaves', 'pendingLeaveRequests.pendingLeaveRequests', 'View Pending Leave Requests', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(371, 'Employee Management', 'General', 'General', 'permanent.index', 'View Permanent Records', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(372, 'Employee Management', 'General', 'General', 'permanent.updatePermanent', 'Update Permanent Record', '2026-05-19 15:07:12', '2026-05-19 15:07:15', NULL, 5, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(373, 'Administration', 'role_permissions', 'Role permissions', 'permissions.create', 'Create Permission', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 10, 'permissions', 'Permissions', 'READ', 0, NULL, NULL, NULL, NULL),
	(374, 'Administration', 'role_permissions', 'Role permissions', 'permissions.destroy', 'Destroy Permission', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 10, 'permissions', 'Permissions', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(375, 'Administration', 'role_permissions', 'Role permissions', 'permissions.edit', 'Edit Permission', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 10, 'permissions', 'Permissions', 'READ', 0, NULL, NULL, NULL, NULL),
	(376, 'Administration', 'role_permissions', 'Role permissions', 'permissions.index', 'View Permissions', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 10, 'permissions', 'Permissions', 'READ', 0, NULL, NULL, NULL, NULL),
	(377, 'Administration', 'role_permissions', 'Role permissions', 'permissions.show', 'Show Permission Details', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 10, 'permissions', 'Permissions', 'READ', 0, NULL, NULL, NULL, NULL),
	(378, 'Administration', 'role_permissions', 'Role permissions', 'permissions.store', 'Create Permission', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 10, 'permissions', 'Permissions', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(379, 'Administration', 'role_permissions', 'Role permissions', 'permissions.update', 'Update Permission', '2026-05-19 15:07:12', '2026-05-19 15:07:19', NULL, 10, 'permissions', 'Permissions', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(380, 'Settings', 'settings', 'Settings', 'printHeadSettings.store', 'Store Print Head Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 8, 'General', 'General', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(381, 'Settings', 'settings', 'Settings', 'printHeadSettings.update', 'Update Print Head Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:16', NULL, 8, 'General', 'General', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(382, 'Settings', 'settings', 'Settings', 'approvalSettings.edit', 'Edit Approval Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 8, 'Approvals', 'Approvals', 'READ', 0, NULL, NULL, NULL, NULL),
	(383, 'Settings', 'settings', 'Settings', 'approvalSettings.index', 'View Approval Settings', '2026-05-19 15:07:12', '2026-05-19 15:07:17', NULL, 8, 'Approvals', 'Approvals', 'READ', 0, NULL, NULL, NULL, NULL),
	(384, 'Settings', 'settings', 'Settings', 'approvalSettings.store', 'Create Approval Settings', '2026-05-19 15:07:13', '2026-05-19 15:07:17', NULL, 8, 'Approvals', 'Approvals', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(385, 'Settings', 'settings', 'Settings', 'approvalSettings.update', 'Update Approval Settings', '2026-05-19 15:07:13', '2026-05-19 15:07:17', NULL, 8, 'Approvals', 'Approvals', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(386, 'Employee Management', 'promotion', 'Promotion', 'promotion.create', 'Create Promotion', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(387, 'Employee Management', 'promotion', 'Promotion', 'promotion.delete', 'Delete Promotion', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(388, 'Employee Management', 'promotion', 'Promotion', 'promotion.edit', 'Edit Promotion', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(389, 'Employee Management', 'promotion', 'Promotion', 'promotion.index', 'View Promotions', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(390, 'Employee Management', 'promotion', 'Promotion', 'promotion.store', 'Create Promotion', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(391, 'Employee Management', 'promotion', 'Promotion', 'promotion.update', 'Update Promotion', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(392, 'Leave Management', 'setup', 'Setup', 'publicHoliday.create', 'Create Public Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'READ', 0, NULL, NULL, NULL, NULL),
	(393, 'Leave Management', 'setup', 'Setup', 'publicHoliday.delete', 'Delete Public Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(394, 'Leave Management', 'setup', 'Setup', 'publicHoliday.edit', 'Edit Public Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'READ', 0, NULL, NULL, NULL, NULL),
	(395, 'Leave Management', 'setup', 'Setup', 'publicHoliday.index', 'View Public Holidays', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'READ', 0, NULL, NULL, NULL, NULL),
	(396, 'Leave Management', 'setup', 'Setup', 'publicHoliday.store', 'Create Public Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(397, 'Leave Management', 'setup', 'Setup', 'publicHoliday.update', 'Update Public Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'holiday', 'Holiday', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(398, 'Annalytics', 'activity', 'Activity', 'reports.activity_logs', 'View Activity Logs', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 11, 'logs', 'Logs', 'READ', 0, NULL, NULL, NULL, NULL),
	(399, 'Annalytics', 'activity', 'Activity', 'reports.activity_logs.view', 'View Detailed Activity Logs', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 11, 'logs', 'Logs', 'READ', 0, NULL, NULL, NULL, NULL),
	(400, 'Annalytics', 'activity', 'Activity', 'reports.errorLog', 'View Error Logs', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 11, 'logs', 'Logs', 'READ', 0, NULL, NULL, NULL, NULL),
	(401, 'Administration', 'reports', 'Reports', 'reports.test', 'View Test Reports', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(402, 'Leave Management', 'leaves', 'Leaves', 'requestedApplication.index', 'View Requested Applications', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 6, 'configure_leave', 'Configure leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(403, 'Leave Management', 'leaves', 'Leaves', 'requestedApplication.update', 'Update Requested Application', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 6, 'configure_leave', 'Configure leave', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(404, 'Leave Management', 'leaves', 'Leaves', 'requestedApplication.viewDetails', 'View Requested Application Details', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 6, 'configure_leave', 'Configure leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(405, 'Administration', 'resetPassword', 'Reset Password', 'reset_password_with_token', 'Reset Password with Token', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(406, 'Administration', 'resetPassword', 'Reset Password', 'reset_password_without_token', 'Reset Password without Token', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(407, 'Administration', 'resetPassword', 'Reset Password', 'resetPassword', 'General Password Reset', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(408, 'Administration', 'role_permissions', 'Role permissions', 'rolePermission.create', 'Create Role Permission', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(409, 'Administration', 'role_permissions', 'Role permissions', 'rolePermission.destroy', 'Destroy Role Permission', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(410, 'Administration', 'role_permissions', 'Role permissions', 'rolePermission.edit', 'Edit Role Permission', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(411, 'Administration', 'role_permissions', 'Role permissions', 'rolePermission.index', 'View Role Permissions', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(412, 'Administration', 'role_permissions', 'Role permissions', 'rolePermission.show', 'Show Role Permission Details', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(413, 'Administration', 'role_permissions', 'Role permissions', 'rolePermission.store', 'Create Role Permission', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(414, 'Administration', 'role_permissions', 'Role permissions', 'rolePermission.update', 'Update Role Permission', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(415, 'Administration', 'role_permissions', 'Role permissions', 'roles.create', 'Create Role', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(416, 'Administration', 'role_permissions', 'Role permissions', 'roles.destroy', 'Destroy Role', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 10, 'roles', 'Roles', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(417, 'Administration', 'role_permissions', 'Role permissions', 'roles.edit', 'Edit Role', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(418, 'Administration', 'role_permissions', 'Role permissions', 'roles.index', 'View Roles', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(419, 'Administration', 'role_permissions', 'Role permissions', 'roles.show', 'Show Role Details', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(420, 'Administration', 'role_permissions', 'Role permissions', 'roles.store', 'Create Role', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 10, 'roles', 'Roles', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(421, 'Administration', 'role_permissions', 'Role permissions', 'roles.update', 'Update Role', '2026-05-19 15:07:13', '2026-05-19 15:07:19', NULL, 10, 'roles', 'Roles', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(422, 'Leave Management', 'manage_leaves', 'Manage leaves', 'rolloverLeave.delete', 'Delete Rollover Leave', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 6, 'rollover_leaves', 'Rollover leaves', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(423, 'Leave Management', 'rolloverLeave', 'Rollover Leave', 'rolloverLeaveEdit', 'Edit Rollover Leave', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(424, 'Leave Management', 'manage_leaves', 'Manage leaves', 'rolloverLeaves', 'View Rollover Leaves', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 6, 'rollover_leaves', 'Rollover leaves', 'READ', 0, NULL, NULL, NULL, NULL),
	(425, 'Payroll', 'setup', 'Setup', 'salaryDeductionRule.index', 'View Salary Deduction Rules', '2026-05-19 15:07:13', '2026-05-19 15:07:17', NULL, 9, 'deduction', 'Deduction', 'READ', 0, NULL, NULL, NULL, NULL),
	(426, 'Payroll', 'setup', 'Setup', 'saveEmployeeBonus.store', 'Create Employee Bonus', '2026-05-19 15:07:13', '2026-05-19 15:07:17', NULL, 9, 'generate_bonus', 'Generate bonus', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(427, 'Payroll', 'salaries', 'Salaries', 'saveEmployeeSalaryDetails.store', 'Create Employee Salary Details', '2026-05-19 15:07:13', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(428, 'Attendance', 'General', 'General', 'saveMigrateAttendanceData', 'Create Migrate Attendance Data', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 1, 'manual_attendance', 'Manual attendance', 'READ', 0, NULL, NULL, NULL, NULL),
	(429, 'Settings', 'settings', 'Settings', 'service.create', 'Create Service', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'READ', 0, NULL, NULL, NULL, NULL),
	(430, 'Settings', 'settings', 'Settings', 'service.destroy', 'Destroy Service', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(431, 'Settings', 'settings', 'Settings', 'service.edit', 'Edit Service', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'READ', 0, NULL, NULL, NULL, NULL),
	(432, 'Settings', 'settings', 'Settings', 'service.index', 'View Services', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'READ', 0, NULL, NULL, NULL, NULL),
	(433, 'Settings', 'settings', 'Settings', 'service.show', 'Show Service Details', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'READ', 0, NULL, NULL, NULL, NULL),
	(434, 'Settings', 'settings', 'Settings', 'service.store', 'Create Service', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(435, 'Settings', 'settings', 'Settings', 'service.update', 'Update Service', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 8, 'Front End', 'Front End', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(436, 'Leave Management', 'manage_leaves', 'Manage leaves', 'storeRolloverLeave', 'Store Rollover Leave', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 6, 'rollover_leaves', 'Rollover leaves', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(437, '', 'summaryReport', 'Summary Report', 'summaryReport.summaryReport', 'View Summary Report', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 0, '', '', '', 0, NULL, NULL, NULL, NULL),
	(438, 'Payroll', 'default', 'Default', 'taxSetup.index', 'View Tax Setup', '2026-05-19 15:07:13', '2026-05-19 15:07:17', NULL, 9, 'taxes', 'Taxes', 'READ', 0, NULL, NULL, NULL, NULL),
	(439, 'Employee Management', 'General', 'General', 'termination.create', 'Create Termination', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'READ', 0, NULL, NULL, NULL, NULL),
	(440, 'Employee Management', 'General', 'General', 'termination.delete', 'Delete Termination', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(441, 'Employee Management', 'General', 'General', 'termination.edit', 'Edit Termination', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'READ', 0, NULL, NULL, NULL, NULL),
	(442, 'Employee Management', 'General', 'General', 'termination.import', 'Import Termination', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'READ', 0, NULL, NULL, NULL, NULL),
	(443, 'Employee Management', 'General', 'General', 'termination.importSave', 'Create Imported Termination', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(444, 'Employee Management', 'General', 'General', 'termination.index', 'View Terminations', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'READ', 0, NULL, NULL, NULL, NULL),
	(445, 'Employee Management', 'General', 'General', 'termination.show', 'Show Termination Details', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'READ', 0, NULL, NULL, NULL, NULL),
	(446, 'Employee Management', 'General', 'General', 'termination.store', 'Create Termination', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(447, 'Employee Management', 'General', 'General', 'termination.update', 'Update Termination', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(448, 'Training', 'trainings', 'Trainings', 'trainingInfo.create', 'Create Training Info', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'trainings', 'Trainings', 'READ', 0, NULL, NULL, NULL, NULL),
	(449, 'Training', 'trainings', 'Trainings', 'trainingInfo.delete', 'Delete Training Info', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'trainings', 'Trainings', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(450, 'Training', 'trainings', 'Trainings', 'trainingInfo.edit', 'Edit Training Info', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'trainings', 'Trainings', 'READ', 0, NULL, NULL, NULL, NULL),
	(451, 'Training', 'trainings', 'Trainings', 'trainingInfo.index', 'View Training Information', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'trainings', 'Trainings', 'READ', 0, NULL, NULL, NULL, NULL),
	(452, 'Training', 'trainings', 'Trainings', 'trainingInfo.show', 'Show Training Info Details', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'trainings', 'Trainings', 'READ', 0, NULL, NULL, NULL, NULL),
	(453, 'Training', 'trainings', 'Trainings', 'trainingInfo.store', 'Create Training Info', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'trainings', 'Trainings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(454, 'Training', 'trainings', 'Trainings', 'trainingInfo.update', 'Update Training Info', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'trainings', 'Trainings', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(455, 'Training', 'training_type', 'Training type', 'trainingType.create', 'Create Training Type', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'training_type', 'Training type', 'READ', 0, NULL, NULL, NULL, NULL),
	(456, 'Training', 'training_type', 'Training type', 'trainingType.delete', 'Delete Training Type', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'training_type', 'Training type', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(457, 'Training', 'training_type', 'Training type', 'trainingType.edit', 'Edit Training Type', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'training_type', 'Training type', 'READ', 0, NULL, NULL, NULL, NULL),
	(458, 'Training', 'training_type', 'Training type', 'trainingType.index', 'View Training Types', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'training_type', 'Training type', 'READ', 0, NULL, NULL, NULL, NULL),
	(459, 'Training', 'training_type', 'Training type', 'trainingType.show', 'Show Training Type Details', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'training_type', 'Training type', 'READ', 0, NULL, NULL, NULL, NULL),
	(460, 'Training', 'training_type', 'Training type', 'trainingType.store', 'Create Training Type', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'training_type', 'Training type', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(461, 'Training', 'training_type', 'Training type', 'trainingType.update', 'Update Training Type', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 4, 'training_type', 'Training type', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(462, 'Leave Management', 'manage_leaves', 'Manage leaves', 'updateDefaultRollovers', 'Update Default Rollovers', '2026-05-19 15:07:13', '2026-05-19 15:07:16', NULL, 6, 'rollover_leaves', 'Rollover leaves', 'READ', 0, NULL, NULL, NULL, NULL),
	(463, 'Attendance', 'devices', 'Devices', 'updateStatus', 'Update Status', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'READ', 0, NULL, NULL, NULL, NULL),
	(464, 'Administration', 'user', 'User', 'user.create', 'Create User', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'user', 'User', 'READ', 0, NULL, NULL, NULL, NULL),
	(465, 'Administration', 'user', 'User', 'user.destroy', 'Destroy User', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'user', 'User', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(466, 'Administration', 'user', 'User', 'user.edit', 'Edit User', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'user', 'User', 'READ', 0, NULL, NULL, NULL, NULL),
	(467, 'Administration', 'user', 'User', 'user.index', 'View Users', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'user', 'User', 'READ', 0, NULL, NULL, NULL, NULL),
	(468, 'Administration', 'user', 'User', 'user.show', 'Show User Details', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'user', 'User', 'READ', 0, NULL, NULL, NULL, NULL),
	(469, 'Administration', 'user', 'User', 'user.store', 'Create User', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'user', 'User', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(470, 'Administration', 'user', 'User', 'user.update', 'Update User', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'user', 'User', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(471, 'Administration', 'role_permissions', 'Role permissions', 'userRole.create', 'Create User Role', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(472, 'Administration', 'role_permissions', 'Role permissions', 'userRole.destroy', 'Destroy User Role', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(473, 'Administration', 'role_permissions', 'Role permissions', 'userRole.edit', 'Edit User Role', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(474, 'Administration', 'role_permissions', 'Role permissions', 'userRole.index', 'View User Roles', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(475, 'Administration', 'role_permissions', 'Role permissions', 'userRole.show', 'Show User Role Details', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'READ', 0, NULL, NULL, NULL, NULL),
	(476, 'Administration', 'role_permissions', 'Role permissions', 'userRole.store', 'Create User Role', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(477, 'Administration', 'role_permissions', 'Role permissions', 'userRole.update', 'Update User Role', '2026-05-19 15:07:13', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(478, 'Employee Management', 'General', 'General', 'warning.create', 'Create Warning', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 5, 'warning', 'Warning', 'READ', 0, NULL, NULL, NULL, NULL),
	(479, 'Employee Management', 'General', 'General', 'warning.delete', 'Delete Warning', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'warning', 'Warning', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(480, 'Employee Management', 'General', 'General', 'warning.edit', 'Edit Warning', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 5, 'warning', 'Warning', 'READ', 0, NULL, NULL, NULL, NULL),
	(481, 'Employee Management', 'General', 'General', 'warning.index', 'View Warnings', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 5, 'warning', 'Warning', 'READ', 0, NULL, NULL, NULL, NULL),
	(482, 'Employee Management', 'General', 'General', 'warning.show', 'Show Warning Details', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 5, 'warning', 'Warning', 'READ', 0, NULL, NULL, NULL, NULL),
	(483, 'Employee Management', 'General', 'General', 'warning.store', 'Create Warning', '2026-05-19 15:07:13', '2026-05-19 15:07:14', NULL, 5, 'warning', 'Warning', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(484, 'Employee Management', 'General', 'General', 'warning.update', 'Update Warning', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 5, 'warning', 'Warning', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(485, 'Attendance', 'reports', 'Reports', 'weeklyAttendance.weeklyAttendance', 'View Weekly Attendance', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(486, 'Attendance', 'reports', 'Reports', 'weeklyAttendance.weeklyAttendanceFilter', 'Filter Weekly Attendance', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(487, 'Leave Management', 'setup', 'Setup', 'weeklyHoliday.create', 'Create Weekly Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'weekly_holiday', 'Weekly holiday', 'READ', 0, NULL, NULL, NULL, NULL),
	(488, 'Leave Management', 'setup', 'Setup', 'weeklyHoliday.delete', 'Delete Weekly Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'weekly_holiday', 'Weekly holiday', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(489, 'Leave Management', 'setup', 'Setup', 'weeklyHoliday.edit', 'Edit Weekly Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'weekly_holiday', 'Weekly holiday', 'READ', 0, NULL, NULL, NULL, NULL),
	(490, 'Leave Management', 'setup', 'Setup', 'weeklyHoliday.index', 'View Weekly Holidays', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'weekly_holiday', 'Weekly holiday', 'READ', 0, NULL, NULL, NULL, NULL),
	(491, 'Leave Management', 'setup', 'Setup', 'weeklyHoliday.store', 'Create Weekly Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'weekly_holiday', 'Weekly holiday', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(492, 'Leave Management', 'setup', 'Setup', 'weeklyHoliday.update', 'Update Weekly Holiday', '2026-05-19 15:07:13', '2026-05-19 15:07:15', NULL, 6, 'weekly_holiday', 'Weekly holiday', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(493, 'Payroll', 'salaries', 'Salaries', 'workHourApproval.create', 'Create Work Hour Approval', '2026-05-19 15:07:13', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(494, 'Payroll', 'salaries', 'Salaries', 'workHourApproval.filter', 'Filter Work Hour Approval', '2026-05-19 15:07:13', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(495, 'Payroll', 'salaries', 'Salaries', 'workHourApproval.store', 'Create Work Hour Approval', '2026-05-19 15:07:13', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(496, 'Attendance', 'setup', 'Setup', 'workShift.create', 'Create Work Shift', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 1, 'work_shift', 'Work shift', 'READ', 0, NULL, NULL, NULL, NULL),
	(497, 'Attendance', 'setup', 'Setup', 'workShift.delete', 'Delete Work Shift', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 1, 'work_shift', 'Work shift', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(498, 'Attendance', 'setup', 'Setup', 'workShift.edit', 'Edit Work Shift', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 1, 'work_shift', 'Work shift', 'READ', 0, NULL, NULL, NULL, NULL),
	(499, 'Attendance', 'setup', 'Setup', 'workShift.index', 'View Work Shifts', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 1, 'work_shift', 'Work shift', 'READ', 0, NULL, NULL, NULL, NULL),
	(500, 'Attendance', 'setup', 'Setup', 'workShift.store', 'Create Work Shift', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 1, 'work_shift', 'Work shift', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(501, 'Attendance', 'setup', 'Setup', 'workShift.update', 'Update Work Shift', '2026-05-19 15:07:13', '2026-05-19 15:07:13', NULL, 1, 'work_shift', 'Work shift', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(502, 'Attendance', 'reports', 'Reports', 'attendande.daily.download', 'attendande.daily.download', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(503, 'Attendance', 'reports', 'Reports', 'attendande.daily.export', 'attendande.daily.export', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(504, 'Attendance', 'reports', 'Reports', 'attendande.weekly.download', 'attendande.weekly.download', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(505, 'Attendance', 'reports', 'Reports', 'attendande.daily.download.excel', 'attendande.daily.download.excel', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(506, 'Attendance', 'reports', 'Reports', 'attendande.monthly.download', 'attendande.monthly.download', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(507, 'Attendance', 'reports', 'Reports', 'attendance.my.download', 'attendance.my.download', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(508, 'Attendance', 'reports', 'Reports', 'attendande.summary.download', 'attendande.summary.download', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(509, 'Attendance', 'reports', 'Reports', 'attendance.mealReportattendande.meal.report', 'attendance.mealReportattendande.meal.report', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(510, 'Attendance', 'reports', 'Reports', 'attendance.mealReportFilterattendande.meal.report.filter', 'attendance.mealReportFilterattendande.meal.report.filter', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(511, 'Attendance', 'reports', 'Reports', 'attendance.overtime.update_payroll', 'attendance.overtime.update_payroll', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'READ', 0, NULL, NULL, NULL, NULL),
	(512, 'Attendance', 'reports', 'Reports', 'attendance.view_raw_logs.filter', 'attendance.view_raw_logs.filter', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'generate_view', 'Generate view', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(513, 'Attendance', 'devices', 'Devices', 'biometricDevices', 'biometricDevices', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 1, 'devices', 'Devices', 'READ', 0, NULL, NULL, NULL, NULL),
	(514, 'Training', 'training_type', 'Training type', 'trainingType.list.options', 'trainingType.list.options', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_type', 'Training type', 'READ', 0, NULL, NULL, NULL, NULL),
	(515, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.attendants.index', 'trainingInfo.attendants.index', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(516, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.attendants', 'trainingInfo.attendants', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'attendances', 'Attendances', 'READ', 0, NULL, NULL, NULL, NULL),
	(517, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.attendants.add', 'trainingInfo.attendants.add', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'attendances', 'Attendances', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(518, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.attendants.approve', 'trainingInfo.attendants.approve', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'attendances', 'Attendances', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(519, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.attendants.delete', 'trainingInfo.attendants.delete', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'attendances', 'Attendances', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(520, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.invitees', 'trainingInfo.invitees', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'invites', 'Invites', 'READ', 0, NULL, NULL, NULL, NULL),
	(521, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.invitees.add', 'trainingInfo.invitees.add', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'invites', 'Invites', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(522, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.invitees.addMultiple', 'trainingInfo.invitees.addMultiple', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'invites', 'Invites', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(523, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.invitees.approve', 'trainingInfo.invitees.approve', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'invites', 'Invites', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(524, 'Training', 'participants_and_invitees', 'Participants and invitees', 'trainingInfo.invitees.delete', 'trainingInfo.invitees.delete', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'invites', 'Invites', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(525, 'Training', 'training_report', 'Training report', 'training.report.form', 'training.report.form', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_report', 'Training report', 'READ', 0, NULL, NULL, NULL, NULL),
	(526, 'Training', 'training_report', 'Training report', 'employeeTrainingReport.employeeTrainingReport.download', 'employeeTrainingReport.employeeTrainingReport.download', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_report', 'Training report', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(527, 'Training', 'training_report', 'Training report', 'training.report.download', 'training.report.download', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_report', 'Training report', 'READ', 0, NULL, NULL, NULL, NULL),
	(528, 'Training', 'training_faclitators', 'Training faclitators', 'training.facilitator.index', 'training.facilitator.index', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_faclitators', 'Training faclitators', 'READ', 0, NULL, NULL, NULL, NULL),
	(529, 'Training', 'training_faclitators', 'Training faclitators', 'training.facilitator.form', 'training.facilitator.form', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_faclitators', 'Training faclitators', 'READ', 0, NULL, NULL, NULL, NULL),
	(530, 'Training', 'training_faclitators', 'Training faclitators', 'training.facilitator.store', 'training.facilitator.store', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_faclitators', 'Training faclitators', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(531, 'Training', 'training_faclitators', 'Training faclitators', 'training.facilitator.edit', 'training.facilitator.edit', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_faclitators', 'Training faclitators', 'READ', 0, NULL, NULL, NULL, NULL),
	(532, 'Training', 'training_faclitators', 'Training faclitators', 'training.facilitator.show', 'training.facilitator.show', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_faclitators', 'Training faclitators', 'READ', 0, NULL, NULL, NULL, NULL),
	(533, 'Training', 'training_faclitators', 'Training faclitators', 'training.facilitator.update', 'training.facilitator.update', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_faclitators', 'Training faclitators', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(534, 'Training', 'training_faclitators', 'Training faclitators', 'training.facilitator.delete', 'training.facilitator.delete', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_faclitators', 'Training faclitators', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(535, 'Training', 'training_faclitators', 'Training faclitators', 'training.facilitator.filter', 'training.facilitator.filter', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 4, 'training_faclitators', 'Training faclitators', 'READ', 0, NULL, NULL, NULL, NULL),
	(536, 'Employee Management', 'General', 'General', 'location.index', 'location.index', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'location', 'Location', 'READ', 0, NULL, NULL, NULL, NULL),
	(537, 'Employee Management', 'General', 'General', 'location.create', 'location.create', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'location', 'Location', 'READ', 0, NULL, NULL, NULL, NULL),
	(538, 'Employee Management', 'General', 'General', 'location.store', 'location.store', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'location', 'Location', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(539, 'Employee Management', 'General', 'General', 'location.edit', 'location.edit', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'location', 'Location', 'READ', 0, NULL, NULL, NULL, NULL),
	(540, 'Employee Management', 'General', 'General', 'location.update', 'location.update', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'location', 'Location', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(541, 'Employee Management', 'General', 'General', 'location.delete', 'location.delete', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'location', 'Location', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(542, 'Employee Management', 'General', 'General', 'region.index', 'region.index', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'region', 'Region', 'READ', 0, NULL, NULL, NULL, NULL),
	(543, 'Employee Management', 'General', 'General', 'region.create', 'region.create', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'region', 'Region', 'READ', 0, NULL, NULL, NULL, NULL),
	(544, 'Employee Management', 'General', 'General', 'region.store', 'region.store', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'region', 'Region', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(545, 'Employee Management', 'General', 'General', 'region.edit', 'region.edit', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'region', 'Region', 'READ', 0, NULL, NULL, NULL, NULL),
	(546, 'Employee Management', 'General', 'General', 'region.update', 'region.update', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'region', 'Region', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(547, 'Employee Management', 'General', 'General', 'region.delete', 'region.delete', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'region', 'Region', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(548, 'Employee Management', 'General', 'General', 'employee.inactive.index', 'employee.inactive.index', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'READ', 0, NULL, NULL, NULL, NULL),
	(549, 'Employee Management', 'General', 'General', 'employee.updateBiometricCaptureStatus', 'employee.updateBiometricCaptureStatus', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'READ', 0, NULL, NULL, NULL, NULL),
	(550, 'Employee Management', 'General', 'General', 'employee.updateEarning', 'employee.updateEarning', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(551, 'Employee Management', 'General', 'General', 'employee.deleteEarning', 'employee.deleteEarning', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(552, 'Employee Management', 'General', 'General', 'employee.addDeduction', 'employee.addDeduction', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(553, 'Employee Management', 'General', 'General', 'employee.updateDeduction', 'employee.updateDeduction', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(554, 'Employee Management', 'General', 'General', 'employee.deleteDeduction', 'employee.deleteDeduction', '2026-05-19 15:07:14', '2026-05-19 15:07:14', NULL, 5, 'employee', 'Employee', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(555, 'Employee Management', 'General', 'General', 'termination.doc.delete', 'termination.doc.delete', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(556, 'Employee Management', 'General', 'General', 'termination.report', 'termination.report', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'READ', 0, NULL, NULL, NULL, NULL),
	(557, 'Employee Management', 'General', 'General', 'termination.reinstate', 'termination.reinstate', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination', 'Termination', 'READ', 0, NULL, NULL, NULL, NULL),
	(558, 'Employee Management', 'General', 'General', 'termination-checklist.index', 'termination-checklist.index', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'READ', 0, NULL, NULL, NULL, NULL),
	(559, 'Employee Management', 'General', 'General', 'termination-checklist.create', 'termination-checklist.create', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'READ', 0, NULL, NULL, NULL, NULL),
	(560, 'Employee Management', 'General', 'General', 'termination-checklist.store', 'termination-checklist.store', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(561, 'Employee Management', 'General', 'General', 'termination-checklist.import', 'termination-checklist.import', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'READ', 0, NULL, NULL, NULL, NULL),
	(562, 'Employee Management', 'General', 'General', 'termination-checklist.importSave', 'termination-checklist.importSave', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(563, 'Employee Management', 'General', 'General', 'termination-checklist-action.update', 'termination-checklist-action.update', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(564, 'Employee Management', 'General', 'General', 'termination-checklist.edit', 'termination-checklist.edit', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'READ', 0, NULL, NULL, NULL, NULL),
	(565, 'Employee Management', 'General', 'General', 'termination-checklist.show', 'termination-checklist.show', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'READ', 0, NULL, NULL, NULL, NULL),
	(566, 'Employee Management', 'General', 'General', 'termination-checklist.update', 'termination-checklist.update', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(567, 'Employee Management', 'General', 'General', 'termination-checklist.delete', 'termination-checklist.delete', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'termination-checklist', 'Termination-checklist', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(568, 'Employee Management', 'General', 'General', 'importSupervisors', 'importSupervisors', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(569, 'Employee Management', 'General', 'General', 'contractsImport', 'contractsImport', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(570, 'Employee Management', 'General', 'General', 'downloadSampleSupervisorFile', 'downloadSampleSupervisorFile', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(571, 'Employee Management', 'General', 'General', 'downloadSampleContractsFile', 'downloadSampleContractsFile', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(572, 'Employee Management', 'General', 'General', 'downloadSampleEmployeeFile', 'downloadSampleEmployeeFile', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(573, 'Employee Management', 'General', 'General', 'employee.masterRoll', 'employee.masterRoll', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'reports', 'Reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(574, 'Employee Management', 'General', 'General', 'workshift.share', 'workshift.share', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(575, 'Employee Management', 'General', 'General', 'workshift.chart', 'workshift.chart', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(576, 'Employee Management', 'General', 'General', 'workshift.chart_line', 'workshift.chart_line', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'employee_section', 'Employee section', 'READ', 0, NULL, NULL, NULL, NULL),
	(577, 'Employee Management', 'General', 'General', 'employeeMovementImportSave', 'employeeMovementImportSave', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(578, 'Employee Management', 'General', 'General', 'employeeMovement.findEmployeeInfo', 'employeeMovement.findEmployeeInfo', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'employee_movement', 'Employee movement', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(579, 'Employee Management', 'General', 'General', 'employee.program.index', 'employee.program.index', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'programs', 'Programs', 'READ', 0, NULL, NULL, NULL, NULL),
	(580, 'Employee Management', 'General', 'General', 'employee.program.create', 'employee.program.create', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'programs', 'Programs', 'READ', 0, NULL, NULL, NULL, NULL),
	(581, 'Employee Management', 'General', 'General', 'employee.program.store', 'employee.program.store', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'programs', 'Programs', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(582, 'Employee Management', 'General', 'General', 'employee.program.show', 'employee.program.show', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'programs', 'Programs', 'READ', 0, NULL, NULL, NULL, NULL),
	(583, 'Employee Management', 'General', 'General', 'employee.program.edit', 'employee.program.edit', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'programs', 'Programs', 'READ', 0, NULL, NULL, NULL, NULL),
	(584, 'Employee Management', 'General', 'General', 'employee.program.update', 'employee.program.update', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'programs', 'Programs', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(585, 'Employee Management', 'General', 'General', 'employee.program.destroy', 'employee.program.destroy', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'programs', 'Programs', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(586, 'Employee Management', 'General', 'General', 'employee.project.index', 'employee.project.index', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'projects', 'Projects', 'READ', 0, NULL, NULL, NULL, NULL),
	(587, 'Employee Management', 'General', 'General', 'employee.project.create', 'employee.project.create', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'projects', 'Projects', 'READ', 0, NULL, NULL, NULL, NULL),
	(588, 'Employee Management', 'General', 'General', 'employee.project.store', 'employee.project.store', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'projects', 'Projects', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(589, 'Employee Management', 'General', 'General', 'employee.project.show', 'employee.project.show', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'projects', 'Projects', 'READ', 0, NULL, NULL, NULL, NULL),
	(590, 'Employee Management', 'General', 'General', 'employee.project.edit', 'employee.project.edit', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'projects', 'Projects', 'READ', 0, NULL, NULL, NULL, NULL),
	(591, 'Employee Management', 'General', 'General', 'employee.project.update', 'employee.project.update', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'projects', 'Projects', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(592, 'Employee Management', 'General', 'General', 'employee.project.destroy', 'employee.project.destroy', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'projects', 'Projects', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(593, 'Employee Management', 'General', 'General', 'ethnicities.index', 'ethnicities.index', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'ethnicity', 'Ethnicity', 'READ', 0, NULL, NULL, NULL, NULL),
	(594, 'Employee Management', 'General', 'General', 'ethnicities.create', 'ethnicities.create', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'ethnicity', 'Ethnicity', 'READ', 0, NULL, NULL, NULL, NULL),
	(595, 'Employee Management', 'General', 'General', 'ethnicities.store', 'ethnicities.store', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'ethnicity', 'Ethnicity', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(596, 'Employee Management', 'General', 'General', 'ethnicities.show', 'ethnicities.show', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'ethnicity', 'Ethnicity', 'READ', 0, NULL, NULL, NULL, NULL),
	(597, 'Employee Management', 'General', 'General', 'ethnicities.edit', 'ethnicities.edit', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'ethnicity', 'Ethnicity', 'READ', 0, NULL, NULL, NULL, NULL),
	(598, 'Employee Management', 'General', 'General', 'ethnicities.update', 'ethnicities.update', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'ethnicity', 'Ethnicity', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(599, 'Employee Management', 'General', 'General', 'ethnicities.destroy', 'ethnicities.destroy', '2026-05-19 15:07:15', '2026-05-19 15:07:15', NULL, 5, 'ethnicity', 'Ethnicity', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(600, 'Leave Management', 'setup', 'Setup', 'leaveGroup.index', 'leaveGroup.index', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'READ', 0, NULL, NULL, NULL, NULL),
	(601, 'Leave Management', 'setup', 'Setup', 'leaveGroup.create', 'leaveGroup.create', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'READ', 0, NULL, NULL, NULL, NULL),
	(602, 'Leave Management', 'setup', 'Setup', 'leaveGroup.store', 'leaveGroup.store', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(603, 'Leave Management', 'setup', 'Setup', 'leaveGroup.edit', 'leaveGroup.edit', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'READ', 0, NULL, NULL, NULL, NULL),
	(604, 'Leave Management', 'setup', 'Setup', 'leaveGroup.update', 'leaveGroup.update', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(605, 'Leave Management', 'setup', 'Setup', 'leaveGroup.delete', 'leaveGroup.delete', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(606, 'Leave Management', 'setup', 'Setup', 'leaveGroup.addSetting', 'leaveGroup.addSetting', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(607, 'Leave Management', 'setup', 'Setup', 'leaveGroup.deleteEmployee', 'leaveGroup.deleteEmployee', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(608, 'Leave Management', 'setup', 'Setup', 'leaveGroup.addEmployee', 'leaveGroup.addEmployee', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(609, 'Leave Management', 'setup', 'Setup', 'leaveGroup.deleteEmployees.bulk', 'leaveGroup.deleteEmployees.bulk', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(610, 'Leave Management', 'setup', 'Setup', 'leaveGroup.addEmployees.bulk', 'leaveGroup.addEmployees.bulk', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(611, 'Leave Management', 'setup', 'Setup', 'leaveGroup.listEmployees', 'leaveGroup.listEmployees', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'READ', 0, NULL, NULL, NULL, NULL),
	(612, 'Leave Management', 'setup', 'Setup', 'leaveGroup.show', 'leaveGroup.show', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_group', 'Leave group', 'READ', 0, NULL, NULL, NULL, NULL),
	(613, 'Leave Management', 'leaves', 'Leaves', 'leave.employee.balance', 'leave.employee.balance', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(614, 'Leave Management', 'leaves', 'Leaves', 'applyOnBehalf.create', 'applyOnBehalf.create', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(615, 'Leave Management', 'leaves', 'Leaves', 'applyOnBehalf.store', 'applyOnBehalf.store', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(616, 'Leave Management', 'leaves', 'Leaves', 'applyOnBehalf.balance', 'applyOnBehalf.balance', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(617, 'Leave Management', 'leaves', 'Leaves', 'applyOnBehalf.totalDays', 'applyOnBehalf.totalDays', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(618, 'Leave Management', 'leaves', 'Leaves', 'applyOnBehalf.employeeDetails', 'applyOnBehalf.employeeDetails', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(619, 'Leave Management', 'leaves', 'Leaves', 'applyOnBehalf.employeeLeaveTypes', 'applyOnBehalf.employeeLeaveTypes', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(620, 'Leave Management', 'leaves', 'Leaves', 'leaveReport.leaveReport.form', 'leaveReport.leaveReport.form', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(621, 'Leave Management', 'leaves', 'Leaves', 'leaveReport.leaveReport.download', 'leaveReport.leaveReport.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(622, 'Leave Management', 'leaves', 'Leaves', 'leave.admin.report.download', 'leave.admin.report.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(623, 'Leave Management', 'leaves', 'Leaves', 'summaryReport.summaryReport.form', 'summaryReport.summaryReport.form', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(624, 'Leave Management', 'leaves', 'Leaves', 'summaryReport.summaryReport.download', 'summaryReport.summaryReport.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(625, 'Leave Management', 'leaves', 'Leaves', 'leave.summaryReport.download', 'leave.summaryReport.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(626, 'Leave Management', 'leaves', 'Leaves', 'leave.report.balances.form', 'leave.report.balances.form', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(627, 'Leave Management', 'leaves', 'Leaves', 'leave.report.balances.download', 'leave.report.balances.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(628, 'Leave Management', 'leaves', 'Leaves', 'leaveReport.fullOrganizationReport.filter', 'leaveReport.fullOrganizationReport.filter', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(629, 'Leave Management', 'leaves', 'Leaves', 'leaveApplication.recall', 'leaveApplication.recall', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(630, 'Leave Management', 'leaves', 'Leaves', 'leave.report.onLeaveToday', 'leave.report.onLeaveToday', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(631, 'Leave Management', 'leaves', 'Leaves', 'leaveReport.monthlyLeaveConsumption', 'leaveReport.monthlyLeaveConsumption', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(632, 'Leave Management', 'leaves', 'Leaves', 'downloadleaveReport.monthlyLeaveConsumption', 'downloadleaveReport.monthlyLeaveConsumption', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(633, 'Leave Management', 'leaves', 'Leaves', 'exportleaveReport.monthlyLeaveConsumption', 'exportleaveReport.monthlyLeaveConsumption', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(634, 'Leave Management', 'leaves', 'Leaves', 'leave.report.history', 'leave.report.history', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(635, 'Leave Management', 'leaves', 'Leaves', 'leave.report.history.detail', 'leave.report.history.detail', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(636, 'Leave Management', 'leaves', 'Leaves', 'leave.report.encashment', 'leave.report.encashment', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(637, 'Leave Management', 'leaves', 'Leaves', 'leave.report.encashment.download', 'leave.report.encashment.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'admin_reports', 'Admin reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(638, 'Leave Management', 'leaves', 'Leaves', 'myLeaveReport.myLeaveReport.view', 'myLeaveReport.myLeaveReport.view', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'my_reports', 'My reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(639, 'Leave Management', 'leaves', 'Leaves', 'myLeaveReport.myLeaveReport.download', 'myLeaveReport.myLeaveReport.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'my_reports', 'My reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(640, 'Leave Management', 'leaves', 'Leaves', 'leave.myreport.download', 'leave.myreport.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'my_reports', 'My reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(641, 'Leave Management', 'manage_leaves', 'Manage leaves', 'rolloverLeaveEdit.view', 'rolloverLeaveEdit.view', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'rollover_leaves', 'Rollover leaves', 'READ', 0, NULL, NULL, NULL, NULL),
	(642, 'Leave Management', 'manage_leaves', 'Manage leaves', 'rolloverLeaveEdit.save', 'rolloverLeaveEdit.save', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'rollover_leaves', 'Rollover leaves', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(643, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.index', 'leave.adjustments.index', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'READ', 0, NULL, NULL, NULL, NULL),
	(644, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.create', 'leave.adjustments.create', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'READ', 0, NULL, NULL, NULL, NULL),
	(645, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.store', 'leave.adjustments.store', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(646, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.show', 'leave.adjustments.show', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'READ', 0, NULL, NULL, NULL, NULL),
	(647, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.edit', 'leave.adjustments.edit', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'READ', 0, NULL, NULL, NULL, NULL),
	(648, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.update', 'leave.adjustments.update', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(649, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.bulkDestroy', 'leave.adjustments.bulkDestroy', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(650, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.destroy', 'leave.adjustments.destroy', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(651, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.balance', 'leave.adjustments.balance', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'READ', 0, NULL, NULL, NULL, NULL),
	(652, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.template.download', 'leave.adjustments.template.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'READ', 0, NULL, NULL, NULL, NULL),
	(653, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.import.form', 'leave.adjustments.import.form', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'READ', 0, NULL, NULL, NULL, NULL),
	(654, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.adjustments.import', 'leave.adjustments.import', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_adjustments', 'Leave adjustments', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(655, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.manage.approve_reject', 'leave.manage.approve_reject', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'approve_reject', 'Approve reject', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(656, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.index', 'leave.schedule.index', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'READ', 0, NULL, NULL, NULL, NULL),
	(657, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.create', 'leave.schedule.create', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'READ', 0, NULL, NULL, NULL, NULL),
	(658, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.store', 'leave.schedule.store', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(659, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.bulkUpload', 'leave.schedule.bulkUpload', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'READ', 0, NULL, NULL, NULL, NULL),
	(660, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.bulkUpload.store', 'leave.schedule.bulkUpload.store', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(661, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.sample.download', 'leave.schedule.sample.download', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'READ', 0, NULL, NULL, NULL, NULL),
	(662, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.edit', 'leave.schedule.edit', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'READ', 0, NULL, NULL, NULL, NULL),
	(663, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.update', 'leave.schedule.update', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(664, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.delete', 'leave.schedule.delete', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(665, 'Leave Management', 'manage_leaves', 'Manage leaves', 'leave.schedule.reminders', 'leave.schedule.reminders', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 6, 'leave_schedule', 'Leave schedule', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(666, 'Recruitment', 'General', 'General', 'jobPost.requisition.data', 'jobPost.requisition.data', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_post', 'Job post', 'READ', 0, NULL, NULL, NULL, NULL),
	(667, 'Recruitment', 'General', 'General', 'jobRequisition.index', 'jobRequisition.index', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'READ', 0, NULL, NULL, NULL, NULL),
	(668, 'Recruitment', 'General', 'General', 'jobRequisition.create', 'jobRequisition.create', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'READ', 0, NULL, NULL, NULL, NULL),
	(669, 'Recruitment', 'General', 'General', 'jobRequisition.store', 'jobRequisition.store', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(670, 'Recruitment', 'General', 'General', 'jobRequisition.show', 'jobRequisition.show', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'READ', 0, NULL, NULL, NULL, NULL),
	(671, 'Recruitment', 'General', 'General', 'jobRequisition.edit', 'jobRequisition.edit', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'READ', 0, NULL, NULL, NULL, NULL),
	(672, 'Recruitment', 'General', 'General', 'jobRequisition.update', 'jobRequisition.update', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(673, 'Recruitment', 'General', 'General', 'jobRequisition.delete', 'jobRequisition.delete', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(674, 'Recruitment', 'General', 'General', 'jobRequisition.submit', 'jobRequisition.submit', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(675, 'Recruitment', 'General', 'General', 'jobRequisition.approve.form', 'jobRequisition.approve.form', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'READ', 0, NULL, NULL, NULL, NULL),
	(676, 'Recruitment', 'General', 'General', 'jobRequisition.approve', 'jobRequisition.approve', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(677, 'Recruitment', 'General', 'General', 'jobRequisition.reject.form', 'jobRequisition.reject.form', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'READ', 0, NULL, NULL, NULL, NULL),
	(678, 'Recruitment', 'General', 'General', 'jobRequisition.reject', 'jobRequisition.reject', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(679, 'Recruitment', 'General', 'General', 'jobRequisition.convert', 'jobRequisition.convert', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_requisition', 'Job requisition', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(680, 'Recruitment', 'General', 'General', 'applicants.search', 'applicants.search', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(681, 'Recruitment', 'General', 'General', 'view.CV', 'view.CV', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(682, 'Recruitment', 'General', 'General', 'download.CV', 'download.CV', '2026-05-19 15:07:16', '2026-05-19 15:07:16', NULL, 7, 'job_candidate', 'Job candidate', 'READ', 0, NULL, NULL, NULL, NULL),
	(683, 'Settings', 'settings', 'Settings', 'approvalSettings.create', 'approvalSettings.create', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'Approvals', 'Approvals', 'READ', 0, NULL, NULL, NULL, NULL),
	(684, 'Settings', 'settings', 'Settings', 'approvalSettings.delete', 'approvalSettings.delete', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'Approvals', 'Approvals', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(685, 'Settings', 'settings', 'Settings', 'financial_year.index', 'financial_year.index', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'Financial Years', 'Financial Years', 'READ', 0, NULL, NULL, NULL, NULL),
	(686, 'Settings', 'settings', 'Settings', 'financial_year.store', 'financial_year.store', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'Financial Years', 'Financial Years', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(687, 'Settings', 'settings', 'Settings', 'financial_year.create', 'financial_year.create', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'Financial Years', 'Financial Years', 'READ', 0, NULL, NULL, NULL, NULL),
	(688, 'Settings', 'settings', 'Settings', 'financial_year.edit', 'financial_year.edit', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'Financial Years', 'Financial Years', 'READ', 0, NULL, NULL, NULL, NULL),
	(689, 'Settings', 'settings', 'Settings', 'financial_year.update', 'financial_year.update', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'Financial Years', 'Financial Years', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(690, 'Settings', 'settings', 'Settings', 'financial_year.delete', 'financial_year.delete', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'Financial Years', 'Financial Years', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(691, 'Settings', 'settings', 'Settings', 'systemSettings.index', 'systemSettings.index', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'System Settings', 'System Settings', 'READ', 0, NULL, NULL, NULL, NULL),
	(692, 'Settings', 'settings', 'Settings', 'systemSettings.update', 'systemSettings.update', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'System Settings', 'System Settings', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(693, 'Settings', 'settings', 'Settings', 'systemSettings.testEmail', 'systemSettings.testEmail', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'System Settings', 'System Settings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(694, 'Settings', 'settings', 'Settings', 'systemSettings.testSms', 'systemSettings.testSms', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'System Settings', 'System Settings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(695, 'Settings', 'settings', 'Settings', 'systemSettings.testInApp', 'systemSettings.testInApp', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 8, 'System Settings', 'System Settings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(696, 'Payroll', 'default', 'Default', 'update.taxRule', 'update.taxRule', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'taxes', 'Taxes', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(697, 'Payroll', 'setup', 'Setup', 'salary.deduction_typesrule.update', 'salary.deduction_typesrule.update', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'deduction', 'Deduction', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(698, 'Payroll', 'setup', 'Setup', 'deduction_types.index', 'deduction_types.index', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'deduction_types', 'Deduction types', 'READ', 0, NULL, NULL, NULL, NULL),
	(699, 'Payroll', 'setup', 'Setup', 'deduction_types.create', 'deduction_types.create', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'deduction_types', 'Deduction types', 'READ', 0, NULL, NULL, NULL, NULL),
	(700, 'Payroll', 'setup', 'Setup', 'deduction_types.store', 'deduction_types.store', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'deduction_types', 'Deduction types', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(701, 'Payroll', 'setup', 'Setup', 'deduction_types.edit', 'deduction_types.edit', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'deduction_types', 'Deduction types', 'READ', 0, NULL, NULL, NULL, NULL),
	(702, 'Payroll', 'setup', 'Setup', 'deduction_types.update', 'deduction_types.update', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'deduction_types', 'Deduction types', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(703, 'Payroll', 'setup', 'Setup', 'deduction_types.delete', 'deduction_types.delete', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'deduction_types', 'Deduction types', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(704, 'Payroll', 'salaries', 'Salaries', 'makePayment', 'makePayment', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(705, 'Payroll', 'salaries', 'Salaries', 'paymentHistory.paymentHistory.view', 'paymentHistory.paymentHistory.view', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(706, 'Payroll', 'salaries', 'Salaries', 'paymentHistory.paymentHistory.post', 'paymentHistory.paymentHistory.post', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(707, 'Payroll', 'salaries', 'Salaries', 'payroll.paymenthistory.generate', 'payroll.paymenthistory.generate', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(708, 'Payroll', 'salaries', 'Salaries', 'payroll.download', 'payroll.download', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(709, 'Payroll', 'salaries', 'Salaries', 'payroll.download.full', 'payroll.download.full', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(710, 'Payroll', 'salaries', 'Salaries', 'payroll.download.payslip', 'payroll.download.payslip', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'salary_generation', 'Salary generation', 'READ', 0, NULL, NULL, NULL, NULL),
	(711, 'Payroll', 'salaries', 'Salaries', 'managementPay', 'managementPay', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(712, 'Payroll', 'salaries', 'Salaries', 'payrollIndex2', 'payrollIndex2', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(713, 'Payroll', 'salaries', 'Salaries', 'paye.report.index', 'paye.report.index', '2026-05-19 15:07:17', '2026-05-19 15:07:17', NULL, 9, 'payroll9', 'Payroll9', 'READ', 0, NULL, NULL, NULL, NULL),
	(714, 'Payroll', 'reports', 'Reports', 'shifReportsIndex', 'shifReportsIndex', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(715, 'Payroll', 'reports', 'Reports', 'payroll.reports.deductions', 'payroll.reports.deductions', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(716, 'Payroll', 'reports', 'Reports', 'payroll.reports.deductions.export', 'payroll.reports.deductions.export', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(717, 'Payroll', 'reports', 'Reports', 'payroll.reports.earnings', 'payroll.reports.earnings', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(718, 'Payroll', 'reports', 'Reports', 'payroll.reports.earnings.export', 'payroll.reports.earnings.export', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(719, 'Payroll', 'reports', 'Reports', 'payroll.reports.variance', 'payroll.reports.variance', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(720, 'Payroll', 'reports', 'Reports', 'payroll.reports.variance.export', 'payroll.reports.variance.export', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_reports', 'Payroll reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(721, 'Payroll', 'setup', 'Setup', 'tax-bands.index', 'tax-bands.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'paye_tax', 'Paye tax', 'READ', 0, NULL, NULL, NULL, NULL),
	(722, 'Payroll', 'setup', 'Setup', 'tax-bands.create', 'tax-bands.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'paye_tax', 'Paye tax', 'READ', 0, NULL, NULL, NULL, NULL),
	(723, 'Payroll', 'setup', 'Setup', 'tax-bands.store', 'tax-bands.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'paye_tax', 'Paye tax', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(724, 'Payroll', 'setup', 'Setup', 'tax-bands.show', 'tax-bands.show', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'paye_tax', 'Paye tax', 'READ', 0, NULL, NULL, NULL, NULL),
	(725, 'Payroll', 'setup', 'Setup', 'tax-bands.edit', 'tax-bands.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'paye_tax', 'Paye tax', 'READ', 0, NULL, NULL, NULL, NULL),
	(726, 'Payroll', 'setup', 'Setup', 'tax-bands.update', 'tax-bands.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'paye_tax', 'Paye tax', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(727, 'Payroll', 'setup', 'Setup', 'tax-bands.destroy', 'tax-bands.destroy', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'paye_tax', 'Paye tax', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(728, 'Payroll', 'setup', 'Setup', 'tax-bands.get-tax-bands', 'tax-bands.get-tax-bands', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'paye_tax', 'Paye tax', 'READ', 0, NULL, NULL, NULL, NULL),
	(729, 'Payroll', 'setup', 'Setup', 'earning_types.index', 'earning_types.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'earning_types', 'Earning types', 'READ', 0, NULL, NULL, NULL, NULL),
	(730, 'Payroll', 'setup', 'Setup', 'earning_types.create', 'earning_types.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'earning_types', 'Earning types', 'READ', 0, NULL, NULL, NULL, NULL),
	(731, 'Payroll', 'setup', 'Setup', 'earning_types.store', 'earning_types.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'earning_types', 'Earning types', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(732, 'Payroll', 'setup', 'Setup', 'earning_types.edit', 'earning_types.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'earning_types', 'Earning types', 'READ', 0, NULL, NULL, NULL, NULL),
	(733, 'Payroll', 'setup', 'Setup', 'earning_types.update', 'earning_types.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'earning_types', 'Earning types', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(734, 'Payroll', 'setup', 'Setup', 'earning_types.delete', 'earning_types.delete', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'earning_types', 'Earning types', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(735, 'Payroll', 'setup', 'Setup', 'earning_types.destroy', 'earning_types.destroy', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'earning_types', 'Earning types', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(736, 'Payroll', 'setup', 'Setup', 'earning_types.details', 'earning_types.details', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'earning_types', 'Earning types', 'READ', 0, NULL, NULL, NULL, NULL),
	(737, 'Payroll', 'setup', 'Setup', 'employee_earnings.index', 'employee_earnings.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'READ', 0, NULL, NULL, NULL, NULL),
	(738, 'Payroll', 'setup', 'Setup', 'employee_earnings.create', 'employee_earnings.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'READ', 0, NULL, NULL, NULL, NULL),
	(739, 'Payroll', 'setup', 'Setup', 'employee_earnings.store', 'employee_earnings.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(740, 'Payroll', 'setup', 'Setup', 'employee_earnings.import.form', 'employee_earnings.import.form', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'READ', 0, NULL, NULL, NULL, NULL),
	(741, 'Payroll', 'setup', 'Setup', 'employee_earnings.import', 'employee_earnings.import', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(742, 'Payroll', 'setup', 'Setup', 'employee_earnings.download_sample', 'employee_earnings.download_sample', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'READ', 0, NULL, NULL, NULL, NULL),
	(743, 'Payroll', 'setup', 'Setup', 'employee_earnings.show', 'employee_earnings.show', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'READ', 0, NULL, NULL, NULL, NULL),
	(744, 'Payroll', 'setup', 'Setup', 'employee_earnings.edit', 'employee_earnings.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'READ', 0, NULL, NULL, NULL, NULL),
	(745, 'Payroll', 'setup', 'Setup', 'employee_earnings.update', 'employee_earnings.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(746, 'Payroll', 'setup', 'Setup', 'employee_earnings.delete', 'employee_earnings.delete', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(747, 'Payroll', 'setup', 'Setup', 'employee_earnings.approve', 'employee_earnings.approve', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(748, 'Payroll', 'setup', 'Setup', 'employee_earnings.reject', 'employee_earnings.reject', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(749, 'Payroll', 'setup', 'Setup', 'employee_earnings.suspend', 'employee_earnings.suspend', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(750, 'Payroll', 'setup', 'Setup', 'employee_earnings.get_employee_earnings', 'employee_earnings.get_employee_earnings', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'READ', 0, NULL, NULL, NULL, NULL),
	(751, 'Payroll', 'setup', 'Setup', 'employee_earnings.calculate_total', 'employee_earnings.calculate_total', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_earnings', 'Employee earnings', 'READ', 0, NULL, NULL, NULL, NULL),
	(752, 'Payroll', 'setup', 'Setup', 'employee_deductions.index', 'employee_deductions.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(753, 'Payroll', 'setup', 'Setup', 'employee_deductions.create', 'employee_deductions.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(754, 'Payroll', 'setup', 'Setup', 'deduction_types.details', 'deduction_types.details', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(755, 'Payroll', 'setup', 'Setup', 'employee_deductions.store', 'employee_deductions.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(756, 'Payroll', 'setup', 'Setup', 'employee_deductions.import', 'employee_deductions.import', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(757, 'Payroll', 'setup', 'Setup', 'employee_deductions.download_template', 'employee_deductions.download_template', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(758, 'Payroll', 'setup', 'Setup', 'employee_deductions.download_sample', 'employee_deductions.download_sample', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(759, 'Payroll', 'setup', 'Setup', 'employee_deductions.show', 'employee_deductions.show', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(760, 'Payroll', 'setup', 'Setup', 'employee_deductions.edit', 'employee_deductions.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(761, 'Payroll', 'setup', 'Setup', 'employee_deductions.update', 'employee_deductions.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(762, 'Payroll', 'setup', 'Setup', 'employee_deductions.delete', 'employee_deductions.delete', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(763, 'Payroll', 'setup', 'Setup', 'employee_deductions.approve', 'employee_deductions.approve', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(764, 'Payroll', 'setup', 'Setup', 'employee_deductions.reject', 'employee_deductions.reject', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(765, 'Payroll', 'setup', 'Setup', 'employee_deductions.suspend', 'employee_deductions.suspend', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(766, 'Payroll', 'setup', 'Setup', 'employee_deductions.get_employee_deductions', 'employee_deductions.get_employee_deductions', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(767, 'Payroll', 'setup', 'Setup', 'employee_deductions.calculate_total', 'employee_deductions.calculate_total', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(768, 'Payroll', 'setup', 'Setup', 'employee_deductions.calculate_daily_rate', 'employee_deductions.calculate_daily_rate', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(769, 'Payroll', 'processing', 'Processing', 'payroll.dashboard', 'payroll.dashboard', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'dashboard', 'Dashboard', 'READ', 0, NULL, NULL, NULL, NULL),
	(770, 'Payroll', 'processing', 'Processing', 'payroll.dashboard.charts-data', 'payroll.dashboard.charts-data', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'dashboard', 'Dashboard', 'READ', 0, NULL, NULL, NULL, NULL),
	(771, 'Payroll', 'processing', 'Processing', 'payroll.index', 'payroll.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'READ', 0, NULL, NULL, NULL, NULL),
	(772, 'Payroll', 'processing', 'Processing', 'payroll.show', 'payroll.show', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'READ', 0, NULL, NULL, NULL, NULL),
	(773, 'Payroll', 'processing', 'Processing', 'payroll.process.form', 'payroll.process.form', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'READ', 0, NULL, NULL, NULL, NULL),
	(774, 'Payroll', 'processing', 'Processing', 'payroll.process', 'payroll.process', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(775, 'Payroll', 'processing', 'Processing', 'payroll.process.single', 'payroll.process.single', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'READ', 0, NULL, NULL, NULL, NULL),
	(776, 'Payroll', 'processing', 'Processing', 'payroll.approve', 'payroll.approve', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(777, 'Payroll', 'processing', 'Processing', 'payroll.mark-paid', 'payroll.mark-paid', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(778, 'Payroll', 'processing', 'Processing', 'payroll.export', 'payroll.export', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(779, 'Payroll', 'processing', 'Processing', 'payroll.payslip', 'payroll.payslip', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'READ', 0, NULL, NULL, NULL, NULL),
	(780, 'Payroll', 'processing', 'Processing', 'payroll.email.single', 'payroll.email.single', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(781, 'Payroll', 'processing', 'Processing', 'payroll.email.mass', 'payroll.email.mass', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_records', 'Payroll records', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(782, 'Payroll', 'processing', 'Processing', 'payroll.claims.index', 'payroll.claims.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'READ', 0, NULL, NULL, NULL, NULL),
	(783, 'Payroll', 'processing', 'Processing', 'payroll.claims.create', 'payroll.claims.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'READ', 0, NULL, NULL, NULL, NULL),
	(784, 'Payroll', 'processing', 'Processing', 'payroll.claims.store', 'payroll.claims.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(785, 'Payroll', 'processing', 'Processing', 'payroll.claims.show', 'payroll.claims.show', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'READ', 0, NULL, NULL, NULL, NULL),
	(786, 'Payroll', 'processing', 'Processing', 'payroll.claims.edit', 'payroll.claims.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'READ', 0, NULL, NULL, NULL, NULL),
	(787, 'Payroll', 'processing', 'Processing', 'payroll.claims.update', 'payroll.claims.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(788, 'Payroll', 'processing', 'Processing', 'payroll.claims.destroy', 'payroll.claims.destroy', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(789, 'Payroll', 'processing', 'Processing', 'payroll.claims.submit', 'payroll.claims.submit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(790, 'Payroll', 'processing', 'Processing', 'payroll.claims.approve', 'payroll.claims.approve', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(791, 'Payroll', 'processing', 'Processing', 'payroll.claims.reject', 'payroll.claims.reject', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(792, 'Payroll', 'processing', 'Processing', 'payroll.claims.activate', 'payroll.claims.activate', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(793, 'Payroll', 'processing', 'Processing', 'payroll.claims.cancel', 'payroll.claims.cancel', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(794, 'Payroll', 'processing', 'Processing', 'payroll.claims.recoveries', 'payroll.claims.recoveries', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'READ', 0, NULL, NULL, NULL, NULL),
	(795, 'Payroll', 'processing', 'Processing', 'payroll.claims.processRecovery', 'payroll.claims.processRecovery', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(796, 'Payroll', 'processing', 'Processing', 'payroll.claims.skipRecovery', 'payroll.claims.skipRecovery', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(797, 'Payroll', 'processing', 'Processing', 'payroll.claims.api.employee', 'payroll.claims.api.employee', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'READ', 0, NULL, NULL, NULL, NULL),
	(798, 'Payroll', 'processing', 'Processing', 'payroll.claims.api.stats', 'payroll.claims.api.stats', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_claims', 'Payroll claims', 'READ', 0, NULL, NULL, NULL, NULL),
	(799, 'Payroll', 'setup', 'Setup', 'payroll.employees.index', 'payroll.employees.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(800, 'Payroll', 'setup', 'Setup', 'payroll.employees.create', 'payroll.employees.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(801, 'Payroll', 'setup', 'Setup', 'payroll.employees.store', 'payroll.employees.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(802, 'Payroll', 'setup', 'Setup', 'payroll.employees.schemes', 'payroll.employees.schemes', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(803, 'Payroll', 'setup', 'Setup', 'payroll.employees.show', 'payroll.employees.show', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(804, 'Payroll', 'setup', 'Setup', 'payroll.employees.edit', 'payroll.employees.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(805, 'Payroll', 'setup', 'Setup', 'payroll.employees.update', 'payroll.employees.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(806, 'Payroll', 'setup', 'Setup', 'payroll.employees.delete', 'payroll.employees.delete', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(807, 'Payroll', 'setup', 'Setup', 'payroll.employees.toggle-status', 'payroll.employees.toggle-status', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(808, 'Payroll', 'setup', 'Setup', 'payroll.employees.template.download', 'payroll.employees.template.download', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(809, 'Payroll', 'setup', 'Setup', 'payroll.employees.import.form', 'payroll.employees.import.form', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(810, 'Payroll', 'setup', 'Setup', 'payroll.employees.import', 'payroll.employees.import', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(811, 'Payroll', 'setup', 'Setup', 'payroll.employees.export', 'payroll.employees.export', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(812, 'Payroll', 'setup', 'Setup', 'payroll.employees.locations', 'payroll.employees.locations', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(813, 'Payroll', 'setup', 'Setup', 'payroll.employees.salary-history', 'payroll.employees.salary-history', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(814, 'Payroll', 'setup', 'Setup', 'payroll.employees.all-salary-history', 'payroll.employees.all-salary-history', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(815, 'Payroll', 'setup', 'Setup', 'payroll.salary.history.index', 'payroll.salary.history.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(816, 'Payroll', 'setup', 'Setup', 'payroll.salary.history.employee', 'payroll.salary.history.employee', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(817, 'Payroll', 'setup', 'Setup', 'payroll.salary.history.export', 'payroll.salary.history.export', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_payroll', 'Employee payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(818, 'Payroll', 'setup', 'Setup', 'payroll.employees.allowances.index', 'payroll.employees.allowances.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_allowances', 'Employee allowances', 'READ', 0, NULL, NULL, NULL, NULL),
	(819, 'Payroll', 'setup', 'Setup', 'payroll.employees.allowances.create', 'payroll.employees.allowances.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_allowances', 'Employee allowances', 'READ', 0, NULL, NULL, NULL, NULL),
	(820, 'Payroll', 'setup', 'Setup', 'payroll.employees.allowances.store', 'payroll.employees.allowances.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_allowances', 'Employee allowances', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(821, 'Payroll', 'setup', 'Setup', 'payroll.employees.allowances.edit', 'payroll.employees.allowances.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_allowances', 'Employee allowances', 'READ', 0, NULL, NULL, NULL, NULL),
	(822, 'Payroll', 'setup', 'Setup', 'payroll.employees.allowances.update', 'payroll.employees.allowances.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_allowances', 'Employee allowances', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(823, 'Payroll', 'setup', 'Setup', 'payroll.employees.allowances.delete', 'payroll.employees.allowances.delete', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_allowances', 'Employee allowances', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(824, 'Payroll', 'setup', 'Setup', 'payroll.employees.deductions.index', 'payroll.employees.deductions.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(825, 'Payroll', 'setup', 'Setup', 'payroll.employees.deductions.create', 'payroll.employees.deductions.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(826, 'Payroll', 'setup', 'Setup', 'payroll.employees.deductions.store', 'payroll.employees.deductions.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(827, 'Payroll', 'setup', 'Setup', 'payroll.employees.deductions.edit', 'payroll.employees.deductions.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'READ', 0, NULL, NULL, NULL, NULL),
	(828, 'Payroll', 'setup', 'Setup', 'payroll.employees.deductions.update', 'payroll.employees.deductions.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(829, 'Payroll', 'setup', 'Setup', 'payroll.employees.deductions.delete', 'payroll.employees.deductions.delete', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'employee_deductions', 'Employee deductions', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(830, 'Payroll', 'reports', 'Reports', 'reports.index', 'reports.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(831, 'Payroll', 'reports', 'Reports', 'reports.paye', 'reports.paye', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(832, 'Payroll', 'reports', 'Reports', 'reports.paye.generate', 'reports.paye.generate', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(833, 'Payroll', 'reports', 'Reports', 'reports.paye.p9', 'reports.paye.p9', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(834, 'Payroll', 'reports', 'Reports', 'reports.paye.p10', 'reports.paye.p10', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(835, 'Payroll', 'reports', 'Reports', 'reports.nssf', 'reports.nssf', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(836, 'Payroll', 'reports', 'Reports', 'reports.nssf.generate', 'reports.nssf.generate', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(837, 'Payroll', 'reports', 'Reports', 'reports.shif', 'reports.shif', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(838, 'Payroll', 'reports', 'Reports', 'reports.shif.generate', 'reports.shif.generate', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(839, 'Payroll', 'reports', 'Reports', 'reports.housing-levy', 'reports.housing-levy', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(840, 'Payroll', 'reports', 'Reports', 'reports.housing-levy.generate', 'reports.housing-levy.generate', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(841, 'Payroll', 'reports', 'Reports', 'reports.summary', 'reports.summary', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(842, 'Payroll', 'reports', 'Reports', 'reports.summary.generate', 'reports.summary.generate', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(843, 'Payroll', 'reports', 'Reports', 'reports.bank-transfer', 'reports.bank-transfer', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(844, 'Payroll', 'reports', 'Reports', 'reports.bank-transfer.generate', 'reports.bank-transfer.generate', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'statutory_reports', 'Statutory reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(845, 'Payroll', 'settings', 'Settings', 'payroll.settings.allowance-types.index', 'payroll.settings.allowance-types.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'allowance_types', 'Allowance types', 'READ', 0, NULL, NULL, NULL, NULL),
	(846, 'Payroll', 'settings', 'Settings', 'payroll.settings.allowance-types.create', 'payroll.settings.allowance-types.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'allowance_types', 'Allowance types', 'READ', 0, NULL, NULL, NULL, NULL),
	(847, 'Payroll', 'settings', 'Settings', 'payroll.settings.allowance-types.store', 'payroll.settings.allowance-types.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'allowance_types', 'Allowance types', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(848, 'Payroll', 'settings', 'Settings', 'payroll.settings.allowance-types.show', 'payroll.settings.allowance-types.show', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'allowance_types', 'Allowance types', 'READ', 0, NULL, NULL, NULL, NULL),
	(849, 'Payroll', 'settings', 'Settings', 'payroll.settings.allowance-types.edit', 'payroll.settings.allowance-types.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'allowance_types', 'Allowance types', 'READ', 0, NULL, NULL, NULL, NULL),
	(850, 'Payroll', 'settings', 'Settings', 'payroll.settings.allowance-types.update', 'payroll.settings.allowance-types.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'allowance_types', 'Allowance types', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(851, 'Payroll', 'settings', 'Settings', 'payroll.settings.allowance-types.delete', 'payroll.settings.allowance-types.delete', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'allowance_types', 'Allowance types', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(852, 'Payroll', 'settings', 'Settings', 'payroll.settings.allowance-types.toggle-status', 'payroll.settings.allowance-types.toggle-status', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'allowance_types', 'Allowance types', 'READ', 0, NULL, NULL, NULL, NULL),
	(853, 'Payroll', 'settings', 'Settings', 'payroll.settings.allowance-types.create-defaults', 'payroll.settings.allowance-types.create-defaults', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'allowance_types', 'Allowance types', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(854, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.index', 'payroll.settings.pension-schemes.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'READ', 0, NULL, NULL, NULL, NULL),
	(855, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.create', 'payroll.settings.pension-schemes.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'READ', 0, NULL, NULL, NULL, NULL),
	(856, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.store', 'payroll.settings.pension-schemes.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(857, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.show', 'payroll.settings.pension-schemes.show', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'READ', 0, NULL, NULL, NULL, NULL),
	(858, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.edit', 'payroll.settings.pension-schemes.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'READ', 0, NULL, NULL, NULL, NULL),
	(859, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.update', 'payroll.settings.pension-schemes.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(860, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.delete', 'payroll.settings.pension-schemes.delete', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(861, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.toggle-status', 'payroll.settings.pension-schemes.toggle-status', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'READ', 0, NULL, NULL, NULL, NULL),
	(862, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.calculate-contribution', 'payroll.settings.pension-schemes.calculate-contribution', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(863, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.generate-report', 'payroll.settings.pension-schemes.generate-report', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'READ', 0, NULL, NULL, NULL, NULL),
	(864, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.create-defaults', 'payroll.settings.pension-schemes.create-defaults', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(865, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.download-template', 'payroll.settings.pension-schemes.download-template', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'READ', 0, NULL, NULL, NULL, NULL),
	(866, 'Payroll', 'settings', 'Settings', 'payroll.settings.pension-schemes.upload-assignments', 'payroll.settings.pension-schemes.upload-assignments', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'pension_schemes', 'Pension schemes', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(867, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.index', 'payroll.settings.periods.index', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'READ', 0, NULL, NULL, NULL, NULL),
	(868, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.create', 'payroll.settings.periods.create', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'READ', 0, NULL, NULL, NULL, NULL),
	(869, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.store', 'payroll.settings.periods.store', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(870, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.show', 'payroll.settings.periods.show', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'READ', 0, NULL, NULL, NULL, NULL),
	(871, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.edit', 'payroll.settings.periods.edit', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'READ', 0, NULL, NULL, NULL, NULL),
	(872, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.update', 'payroll.settings.periods.update', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(873, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.delete', 'payroll.settings.periods.delete', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(874, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.set-current', 'payroll.settings.periods.set-current', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'READ', 0, NULL, NULL, NULL, NULL),
	(875, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.close', 'payroll.settings.periods.close', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'READ', 0, NULL, NULL, NULL, NULL),
	(876, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.reopen', 'payroll.settings.periods.reopen', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'READ', 0, NULL, NULL, NULL, NULL),
	(877, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.bank-upload-report', 'payroll.settings.periods.bank-upload-report', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'READ', 0, NULL, NULL, NULL, NULL),
	(878, 'Payroll', 'settings', 'Settings', 'payroll.settings.periods.generate-periods', 'payroll.settings.periods.generate-periods', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'payroll_periods', 'Payroll periods', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(879, 'Payroll', 'reports', 'Reports', 'payrollReportsIndex', 'payrollReportsIndex', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'general_reports', 'General reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(880, 'Payroll', 'reports', 'Reports', 'payrollReportsChartsData', 'payrollReportsChartsData', '2026-05-19 15:07:18', '2026-05-19 15:07:18', NULL, 9, 'general_reports', 'General reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(881, 'Payroll', 'reports', 'Reports', 'reports.rawpaysumm', 'reports.rawpaysumm', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'general_reports', 'General reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(882, 'Payroll', 'reports', 'Reports', 'payroll.reports.inputs', 'payroll.reports.inputs', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'general_reports', 'General reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(883, 'Payroll', 'reports', 'Reports', 'payroll.reports.inputs.export', 'payroll.reports.inputs.export', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'general_reports', 'General reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(884, 'Payroll', 'reports', 'Reports', 'payroll.reports.inputs.upload', 'payroll.reports.inputs.upload', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'general_reports', 'General reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(885, 'Payroll', 'settings', 'Settings', 'payroll.settings.deduction-types.index', 'payroll.settings.deduction-types.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'deduction_types', 'Deduction types', 'READ', 0, NULL, NULL, NULL, NULL),
	(886, 'Payroll', 'settings', 'Settings', 'payroll.settings.deduction-types.create', 'payroll.settings.deduction-types.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'deduction_types', 'Deduction types', 'READ', 0, NULL, NULL, NULL, NULL),
	(887, 'Payroll', 'settings', 'Settings', 'payroll.settings.deduction-types.store', 'payroll.settings.deduction-types.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'deduction_types', 'Deduction types', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(888, 'Payroll', 'settings', 'Settings', 'payroll.settings.deduction-types.show', 'payroll.settings.deduction-types.show', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'deduction_types', 'Deduction types', 'READ', 0, NULL, NULL, NULL, NULL),
	(889, 'Payroll', 'settings', 'Settings', 'payroll.settings.deduction-types.edit', 'payroll.settings.deduction-types.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'deduction_types', 'Deduction types', 'READ', 0, NULL, NULL, NULL, NULL),
	(890, 'Payroll', 'settings', 'Settings', 'payroll.settings.deduction-types.update', 'payroll.settings.deduction-types.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'deduction_types', 'Deduction types', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(891, 'Payroll', 'settings', 'Settings', 'payroll.settings.deduction-types.delete', 'payroll.settings.deduction-types.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'deduction_types', 'Deduction types', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(892, 'Payroll', 'settings', 'Settings', 'payroll.settings.deduction-types.toggle-status', 'payroll.settings.deduction-types.toggle-status', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'deduction_types', 'Deduction types', 'READ', 0, NULL, NULL, NULL, NULL),
	(893, 'Payroll', 'settings', 'Settings', 'payroll.settings.deduction-types.create-defaults', 'payroll.settings.deduction-types.create-defaults', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'deduction_types', 'Deduction types', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(894, 'Payroll', 'setup', 'Setup', 'payroll.bulk_upload.earnings.index', 'payroll.bulk_upload.earnings.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'bulk_upload', 'Bulk upload', 'READ', 0, NULL, NULL, NULL, NULL),
	(895, 'Payroll', 'setup', 'Setup', 'payroll.bulk_upload.earnings.download_template', 'payroll.bulk_upload.earnings.download_template', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'bulk_upload', 'Bulk upload', 'READ', 0, NULL, NULL, NULL, NULL),
	(896, 'Payroll', 'setup', 'Setup', 'payroll.bulk_upload.earnings', 'payroll.bulk_upload.earnings', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'bulk_upload', 'Bulk upload', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(897, 'Payroll', 'setup', 'Setup', 'payroll.bulk_upload.deductions.index', 'payroll.bulk_upload.deductions.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'bulk_upload', 'Bulk upload', 'READ', 0, NULL, NULL, NULL, NULL),
	(898, 'Payroll', 'setup', 'Setup', 'payroll.bulk_upload.deductions.download_template', 'payroll.bulk_upload.deductions.download_template', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'bulk_upload', 'Bulk upload', 'READ', 0, NULL, NULL, NULL, NULL),
	(899, 'Payroll', 'setup', 'Setup', 'payroll.bulk_upload.advances.index', 'payroll.bulk_upload.advances.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'bulk_upload', 'Bulk upload', 'READ', 0, NULL, NULL, NULL, NULL),
	(900, 'Payroll', 'setup', 'Setup', 'payroll.bulk_upload.advances.download_template', 'payroll.bulk_upload.advances.download_template', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'bulk_upload', 'Bulk upload', 'READ', 0, NULL, NULL, NULL, NULL),
	(901, 'Payroll', 'setup', 'Setup', 'payroll.bulk_upload.advances', 'payroll.bulk_upload.advances', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'bulk_upload', 'Bulk upload', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(902, 'Payroll', 'banks', 'Banks', 'banks.index', 'banks.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'READ', 0, NULL, NULL, NULL, NULL),
	(903, 'Payroll', 'banks', 'Banks', 'banks.create', 'banks.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'READ', 0, NULL, NULL, NULL, NULL),
	(904, 'Payroll', 'banks', 'Banks', 'banks.store', 'banks.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(905, 'Payroll', 'banks', 'Banks', 'banks.show', 'banks.show', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'READ', 0, NULL, NULL, NULL, NULL),
	(906, 'Payroll', 'banks', 'Banks', 'banks.edit', 'banks.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'READ', 0, NULL, NULL, NULL, NULL),
	(907, 'Payroll', 'banks', 'Banks', 'banks.update', 'banks.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(908, 'Payroll', 'banks', 'Banks', 'banks.destroy', 'banks.destroy', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(909, 'Payroll', 'banks', 'Banks', 'banks.import', 'banks.import', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'READ', 0, NULL, NULL, NULL, NULL),
	(910, 'Payroll', 'banks', 'Banks', 'banks.import.process', 'banks.import.process', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(911, 'Payroll', 'banks', 'Banks', 'banks.template.download', 'banks.template.download', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'banks', 'Banks', 'READ', 0, NULL, NULL, NULL, NULL),
	(912, 'Payroll', 'banks', 'Banks', 'bank-branches.index', 'bank-branches.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'READ', 0, NULL, NULL, NULL, NULL),
	(913, 'Payroll', 'banks', 'Banks', 'bank-branches.create', 'bank-branches.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'READ', 0, NULL, NULL, NULL, NULL),
	(914, 'Payroll', 'banks', 'Banks', 'bank-branches.store', 'bank-branches.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(915, 'Payroll', 'banks', 'Banks', 'bank-branches.show', 'bank-branches.show', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'READ', 0, NULL, NULL, NULL, NULL),
	(916, 'Payroll', 'banks', 'Banks', 'bank-branches.edit', 'bank-branches.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'READ', 0, NULL, NULL, NULL, NULL),
	(917, 'Payroll', 'banks', 'Banks', 'bank-branches.update', 'bank-branches.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(918, 'Payroll', 'banks', 'Banks', 'bank-branches.destroy', 'bank-branches.destroy', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(919, 'Payroll', 'banks', 'Banks', 'bank-branches.import', 'bank-branches.import', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'READ', 0, NULL, NULL, NULL, NULL),
	(920, 'Payroll', 'banks', 'Banks', 'bank-branches.import.process', 'bank-branches.import.process', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(921, 'Payroll', 'banks', 'Banks', 'bank-branches.template.download', 'bank-branches.template.download', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'branches', 'Branches', 'READ', 0, NULL, NULL, NULL, NULL),
	(922, 'Payroll', 'default', 'Default', 'payroll.progress.check1', 'payroll.progress.check1', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(923, 'Payroll', 'default', 'Default', 'payroll.progress.check', 'payroll.progress.check', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(924, 'Payroll', 'default', 'Default', 'payroll.progress', 'payroll.progress', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(925, 'Payroll', 'default', 'Default', 'payroll.bulk.submit', 'payroll.bulk.submit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(926, 'Payroll', 'loans', 'Loans', 'loans.dashboard', 'loans.dashboard', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(927, 'Payroll', 'loans', 'Loans', 'loans.index', 'loans.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(928, 'Payroll', 'loans', 'Loans', 'loans.create', 'loans.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(929, 'Payroll', 'loans', 'Loans', 'loans.store', 'loans.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(930, 'Payroll', 'loans', 'Loans', 'loans.show', 'loans.show', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(931, 'Payroll', 'loans', 'Loans', 'loans.edit', 'loans.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(932, 'Payroll', 'loans', 'Loans', 'loans.update', 'loans.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(933, 'Payroll', 'loans', 'Loans', 'loans.delete', 'loans.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(934, 'Payroll', 'loans', 'Loans', 'loans.approve', 'loans.approve', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(935, 'Payroll', 'loans', 'Loans', 'loans.reject', 'loans.reject', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(936, 'Payroll', 'loans', 'Loans', 'loans.suspend', 'loans.suspend', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(937, 'Payroll', 'loans', 'Loans', 'loans.types.index', 'loans.types.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(938, 'Payroll', 'loans', 'Loans', 'loans.types.create', 'loans.types.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(939, 'Payroll', 'loans', 'Loans', 'loans.types.store', 'loans.types.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(940, 'Payroll', 'loans', 'Loans', 'loans.types.edit', 'loans.types.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(941, 'Payroll', 'loans', 'Loans', 'loans.types.update', 'loans.types.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(942, 'Payroll', 'loans', 'Loans', 'loans.types.delete', 'loans.types.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(943, 'Payroll', 'loans', 'Loans', 'loans.applications.index', 'loans.applications.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(944, 'Payroll', 'loans', 'Loans', 'loans.applications.pending', 'loans.applications.pending', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(945, 'Payroll', 'loans', 'Loans', 'loans.applications.approve', 'loans.applications.approve', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(946, 'Payroll', 'loans', 'Loans', 'loans.applications.reject', 'loans.applications.reject', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(947, 'Payroll', 'loans', 'Loans', 'loans.manual-deductions.index', 'loans.manual-deductions.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(948, 'Payroll', 'loans', 'Loans', 'loans.manual-deductions.store', 'loans.manual-deductions.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(949, 'Payroll', 'loans', 'Loans', 'loans.manual-deductions.delete', 'loans.manual-deductions.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(950, 'Payroll', 'loans', 'Loans', 'loans.reports.summary', 'loans.reports.summary', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'loan_management', 'Loan management', 'READ', 0, NULL, NULL, NULL, NULL),
	(951, 'Payroll', 'Payroll Calculator', 'Payroll Calculator', 'payrollcaculator.shif', 'payrollcaculator.shif', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'nssf_nhif..', 'Nssf nhif..', 'READ', 0, NULL, NULL, NULL, NULL),
	(952, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.bulk_upload.index', 'payroll.overtime.bulk_upload.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(953, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.bulk_upload', 'payroll.overtime.bulk_upload', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(954, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.bulk_upload.download_template', 'payroll.overtime.bulk_upload.download_template', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(955, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.index', 'payroll.overtime.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(956, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.create', 'payroll.overtime.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(957, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.store', 'payroll.overtime.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(958, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.show', 'payroll.overtime.show', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(959, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.edit', 'payroll.overtime.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(960, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.update', 'payroll.overtime.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(961, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.delete', 'payroll.overtime.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(962, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.template.download', 'payroll.overtime.template.download', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(963, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.import.form', 'payroll.overtime.import.form', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(964, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.import', 'payroll.overtime.import', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(965, 'Payroll', 'overtime', 'Overtime', 'payroll.overtime.getEmployeeOvertimeRate', 'payroll.overtime.getEmployeeOvertimeRate', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 9, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(966, 'Annalytics', 'annalytics', 'Annalytics', 'reports.annalytics.view', 'reports.annalytics.view', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 11, 'annalytics', 'Annalytics', 'READ', 0, NULL, NULL, NULL, NULL),
	(967, 'Settings', 'approvals', 'Approvals', 'approval-workflows.index', 'approval-workflows.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 8, 'workflows', 'Workflows', 'READ', 0, NULL, NULL, NULL, NULL),
	(968, 'Settings', 'approvals', 'Approvals', 'approval-workflows.create', 'approval-workflows.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 8, 'workflows', 'Workflows', 'READ', 0, NULL, NULL, NULL, NULL),
	(969, 'Settings', 'approvals', 'Approvals', 'approval-workflows.store', 'approval-workflows.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 8, 'workflows', 'Workflows', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(970, 'Settings', 'approvals', 'Approvals', 'approval-workflows.edit', 'approval-workflows.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 8, 'workflows', 'Workflows', 'READ', 0, NULL, NULL, NULL, NULL),
	(971, 'Settings', 'approvals', 'Approvals', 'approval-workflows.update', 'approval-workflows.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 8, 'workflows', 'Workflows', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(972, 'Settings', 'approvals', 'Approvals', 'approval-workflows.destroy', 'approval-workflows.destroy', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 8, 'workflows', 'Workflows', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(973, 'Settings', 'approvals', 'Approvals', 'approval-workflows.show', 'approval-workflows.show', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 8, 'workflows', 'Workflows', 'READ', 0, NULL, NULL, NULL, NULL),
	(974, 'approvals', 'approvals', 'Approvals', 'approvals.approve', 'approvals.approve', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(975, 'approvals', 'approvals', 'Approvals', 'approvals.reject', 'approvals.reject', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(976, 'approvals', 'approvals', 'Approvals', 'approvals.status', 'approvals.status', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'READ', 0, NULL, NULL, NULL, NULL),
	(977, 'approvals', 'approvals', 'Approvals', 'approvals.batch-approve', 'approvals.batch-approve', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(978, 'approvals', 'approvals', 'Approvals', 'approvals.batch-reject', 'approvals.batch-reject', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(979, 'approvals', 'approvals', 'Approvals', 'approvals.batch-preview', 'approvals.batch-preview', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(980, 'approvals', 'approvals', 'Approvals', 'approvals.pending-by-type', 'approvals.pending-by-type', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'READ', 0, NULL, NULL, NULL, NULL),
	(981, 'approvals', 'approvals', 'Approvals', 'approvals.pending', 'approvals.pending', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'READ', 0, NULL, NULL, NULL, NULL),
	(982, 'approvals', 'approvals', 'Approvals', 'approvals.my-pending', 'approvals.my-pending', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'READ', 0, NULL, NULL, NULL, NULL),
	(983, 'approvals', 'approvals', 'Approvals', 'approvals.pending-employee-deductions', 'approvals.pending-employee-deductions', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'READ', 0, NULL, NULL, NULL, NULL),
	(984, 'approvals', 'approvals', 'Approvals', 'approvals.history', 'approvals.history', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'READ', 0, NULL, NULL, NULL, NULL),
	(985, 'approvals', 'approvals', 'Approvals', 'approvals.batch-submit', 'approvals.batch-submit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(986, 'approvals', 'approvals', 'Approvals', 'approvals.batch-status', 'approvals.batch-status', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'READ', 0, NULL, NULL, NULL, NULL),
	(987, 'approvals', 'approvals', 'Approvals', 'approvals.submit', 'approvals.submit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(988, 'approvals', 'approvals', 'Approvals', 'approvals.batch.submit', 'approvals.batch.submit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 12, 'requests', 'Requests', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(989, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.index', 'feedback.category.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'READ', 0, NULL, NULL, NULL, NULL),
	(990, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.trash', 'feedback.category.trash', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'READ', 0, NULL, NULL, NULL, NULL),
	(991, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.create', 'feedback.category.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'READ', 0, NULL, NULL, NULL, NULL),
	(992, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.edit', 'feedback.category.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'READ', 0, NULL, NULL, NULL, NULL),
	(993, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.store', 'feedback.category.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(994, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.view', 'feedback.category.view', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'READ', 0, NULL, NULL, NULL, NULL),
	(995, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.update', 'feedback.category.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(996, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.delete', 'feedback.category.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(997, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.restore', 'feedback.category.restore', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(998, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'feedback.category.destroy', 'feedback.category.destroy', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'feedback_category', 'Feedback category', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(999, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'employee.feedback.index', 'employee.feedback.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'employee_feedback', 'Employee feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1000, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'employee.feedback.respond', 'employee.feedback.respond', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'employee_feedback', 'Employee feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1001, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'employee.feedback.store-reponse', 'employee.feedback.store-reponse', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'employee_feedback', 'Employee feedback', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1002, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'employee.feedback.view', 'employee.feedback.view', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'employee_feedback', 'Employee feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1003, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'employee.feedback.update', 'employee.feedback.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'employee_feedback', 'Employee feedback', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1004, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'employee.feedback.delete', 'employee.feedback.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'employee_feedback', 'Employee feedback', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1005, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'anonymous.feedback.index', 'anonymous.feedback.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'annonymous_feedback', 'Annonymous feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1006, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'anonymous.feedback.create', 'anonymous.feedback.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'annonymous_feedback', 'Annonymous feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1007, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'anonymous.feedback.store', 'anonymous.feedback.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'annonymous_feedback', 'Annonymous feedback', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1008, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'anonymous.feedback.view', 'anonymous.feedback.view', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'annonymous_feedback', 'Annonymous feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1009, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'anonymous.feedback.update', 'anonymous.feedback.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'annonymous_feedback', 'Annonymous feedback', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1010, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'anonymous.feedback.delete', 'anonymous.feedback.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'annonymous_feedback', 'Annonymous feedback', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1011, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'anonymous.feedback.review', 'anonymous.feedback.review', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'annonymous_feedback', 'Annonymous feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1012, 'Employee Feedback', 'employee_feedback', 'Employee feedback', 'anonymous.feedback.store-review', 'anonymous.feedback.store-review', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 13, 'annonymous_feedback', 'Annonymous feedback', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1013, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.index', 'disciplinary.category.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'READ', 0, NULL, NULL, NULL, NULL),
	(1014, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.trash', 'disciplinary.category.trash', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'READ', 0, NULL, NULL, NULL, NULL),
	(1015, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.create', 'disciplinary.category.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'READ', 0, NULL, NULL, NULL, NULL),
	(1016, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.edit', 'disciplinary.category.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'READ', 0, NULL, NULL, NULL, NULL),
	(1017, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.store', 'disciplinary.category.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1018, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.view', 'disciplinary.category.view', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'READ', 0, NULL, NULL, NULL, NULL),
	(1019, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.update', 'disciplinary.category.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1020, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.delete', 'disciplinary.category.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1021, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.restore', 'disciplinary.category.restore', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1022, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.category.destroy', 'disciplinary.category.destroy', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_category', 'Disciplinary category', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1023, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.index', 'disciplinary.cases.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'READ', 0, NULL, NULL, NULL, NULL),
	(1024, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.create', 'disciplinary.cases.create', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'READ', 0, NULL, NULL, NULL, NULL),
	(1025, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.edit', 'disciplinary.cases.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'READ', 0, NULL, NULL, NULL, NULL),
	(1026, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.store', 'disciplinary.cases.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1027, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.view', 'disciplinary.cases.view', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'READ', 0, NULL, NULL, NULL, NULL),
	(1028, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.update', 'disciplinary.cases.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1029, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.delete', 'disciplinary.cases.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1030, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.destroy', 'disciplinary.cases.destroy', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1031, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.closed', 'disciplinary.cases.closed', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'READ', 0, NULL, NULL, NULL, NULL),
	(1032, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.action', 'disciplinary.cases.action', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1033, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.close', 'disciplinary.cases.close', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1034, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.reopen', 'disciplinary.cases.reopen', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1035, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.trash', 'disciplinary.cases.trash', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'READ', 0, NULL, NULL, NULL, NULL),
	(1036, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.restore', 'disciplinary.cases.restore', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_cases', 'Disciplinary cases', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1037, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.action.index', 'disciplinary.cases.action.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_case_actions', 'Disciplinary case actions', 'READ', 0, NULL, NULL, NULL, NULL),
	(1038, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.action.view', 'disciplinary.cases.action.view', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_case_actions', 'Disciplinary case actions', 'READ', 0, NULL, NULL, NULL, NULL),
	(1039, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.action.update', 'disciplinary.cases.action.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_case_actions', 'Disciplinary case actions', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1040, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.action.delete', 'disciplinary.cases.action.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_case_actions', 'Disciplinary case actions', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1041, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.action.destroy', 'disciplinary.cases.action.destroy', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_case_actions', 'Disciplinary case actions', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1042, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.action.closed', 'disciplinary.cases.action.closed', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_case_actions', 'Disciplinary case actions', 'READ', 0, NULL, NULL, NULL, NULL),
	(1043, 'Disciplinary', 'disciplinary', 'Disciplinary', 'disciplinary.cases.action.action', 'disciplinary.cases.action.action', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 14, 'disciplinary_case_actions', 'Disciplinary case actions', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1044, 'Self Service', 'leaves', 'Leaves', 'ess.leave.index', 'ess.leave.index', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(1045, 'Self Service', 'leaves', 'Leaves', 'ess.leave.form', 'ess.leave.form', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(1046, 'Self Service', 'leaves', 'Leaves', 'ess.leave.edit', 'ess.leave.edit', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(1047, 'Self Service', 'leaves', 'Leaves', 'ess.leave.update', 'ess.leave.update', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1048, 'Self Service', 'leaves', 'Leaves', 'ess.leave.apply.store', 'ess.leave.apply.store', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1049, 'Self Service', 'leaves', 'Leaves', 'ess.leave.balance', 'ess.leave.balance', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1050, 'Self Service', 'leaves', 'Leaves', 'ess.leave.leave.employee.apply.totaldays', 'ess.leave.leave.employee.apply.totaldays', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1051, 'Self Service', 'leaves', 'Leaves', 'ess.leave.applyForLeave.show', 'ess.leave.applyForLeave.show', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'READ', 0, NULL, NULL, NULL, NULL),
	(1052, 'Self Service', 'leaves', 'Leaves', 'ess.leave.justification.delete', 'ess.leave.justification.delete', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1053, 'Self Service', 'leaves', 'Leaves', 'ess.leave.recall', 'ess.leave.recall', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'apply_for_leave', 'Apply for leave', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1054, 'Self Service', 'leaves', 'Leaves', 'ess.leave.report.view', 'ess.leave.report.view', '2026-05-19 15:07:19', '2026-05-19 15:07:19', NULL, 15, 'my_reports', 'My reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(1055, 'Self Service', 'leaves', 'Leaves', 'ess.leave.report.download', 'ess.leave.report.download', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'my_reports', 'My reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1056, 'Self Service', 'leaves', 'Leaves', 'ess.leave.report.download2', 'ess.leave.report.download2', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'my_reports', 'My reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(1057, 'Self Service', 'leaves', 'Leaves', 'ess.leave.scheduled.index', 'ess.leave.scheduled.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'scheduled_leaves', 'Scheduled leaves', 'READ', 0, NULL, NULL, NULL, NULL),
	(1058, 'Self Service', 'notifications', 'Notifications', 'ess.notifications.index', 'ess.notifications.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'notifications', 'Notifications', 'READ', 0, NULL, NULL, NULL, NULL),
	(1059, 'Self Service', 'notifications', 'Notifications', 'ess.notifications.markAllRead', 'ess.notifications.markAllRead', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'notifications', 'Notifications', 'READ', 0, NULL, NULL, NULL, NULL),
	(1060, 'Self Service', 'notifications', 'Notifications', 'ess.notifications.markRead', 'ess.notifications.markRead', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'notifications', 'Notifications', 'READ', 0, NULL, NULL, NULL, NULL),
	(1061, 'Self Service', 'notifications', 'Notifications', 'ess.notifications.delete', 'ess.notifications.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'notifications', 'Notifications', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1062, 'Self Service', 'payrol', 'Payrol', 'ess.payroll.index', 'ess.payroll.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'self_payroll', 'Self payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(1063, 'Self Service', 'payrol', 'Payrol', 'ess.payroll.payslip.generate', 'ess.payroll.payslip.generate', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'self_payroll', 'Self payroll', 'READ', 0, NULL, NULL, NULL, NULL),
	(1064, 'Self Service', 'attendance', 'Attendance', 'ess.attendance.download', 'ess.attendance.download', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'attendance', 'Attendance', 'READ', 0, NULL, NULL, NULL, NULL),
	(1065, 'Self Service', 'attendance', 'Attendance', 'ess.attendance.create', 'ess.attendance.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'attendance', 'Attendance', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1066, 'Self Service', 'approvals', 'Approvals', 'ess.approval.index', 'ess.approval.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'approval', 'Approval', 'READ', 0, NULL, NULL, NULL, NULL),
	(1067, 'Self Service', 'approvals', 'Approvals', 'ess.approval.show', 'ess.approval.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'approval', 'Approval', 'READ', 0, NULL, NULL, NULL, NULL),
	(1068, 'Self Service', 'approvals', 'Approvals', 'ess.approval.delegations.index', 'ess.approval.delegations.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'approval', 'Approval', 'READ', 0, NULL, NULL, NULL, NULL),
	(1069, 'Self Service', 'approvals', 'Approvals', 'ess.approval.delegations.store', 'ess.approval.delegations.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'approval', 'Approval', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1070, 'Self Service', 'approvals', 'Approvals', 'ess.approval.delegations.edit', 'ess.approval.delegations.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'approval', 'Approval', 'READ', 0, NULL, NULL, NULL, NULL),
	(1071, 'Self Service', 'approvals', 'Approvals', 'ess.approval.delegations.update', 'ess.approval.delegations.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'approval', 'Approval', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1072, 'Self Service', 'approvals', 'Approvals', 'ess.approval.delegations.destroy', 'ess.approval.delegations.destroy', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'approval', 'Approval', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1073, 'Self Service', 'approvals', 'Approvals', 'ess.approval.delegations.toggle-status', 'ess.approval.delegations.toggle-status', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'approval', 'Approval', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1074, 'Self Service', 'approvals', 'Approvals', 'ess.approval.delegations.deactivate', 'ess.approval.delegations.deactivate', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'approval', 'Approval', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1075, 'Self Service', 'awards', 'Awards', 'ess.awards.index', 'ess.awards.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'awards', 'Awards', 'READ', 0, NULL, NULL, NULL, NULL),
	(1076, 'Self Service', 'diciplinary', 'Diciplinary', 'ess.diciplinary.index', 'ess.diciplinary.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'diciplinary', 'Diciplinary', 'READ', 0, NULL, NULL, NULL, NULL),
	(1077, 'Self Service', 'diciplinary', 'Diciplinary', 'ess.diciplinary.show', 'ess.diciplinary.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'diciplinary', 'Diciplinary', 'READ', 0, NULL, NULL, NULL, NULL),
	(1078, 'Self Service', 'contacts', 'Contacts', 'ess.contacts.index', 'ess.contacts.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'contacts', 'Contacts', 'READ', 0, NULL, NULL, NULL, NULL),
	(1079, 'Self Service', 'trainings', 'Trainings', 'ess.trainings.index', 'ess.trainings.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'trainings', 'Trainings', 'READ', 0, NULL, NULL, NULL, NULL),
	(1080, 'Self Service', 'trainings', 'Trainings', 'ess.trainings.show', 'ess.trainings.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'trainings', 'Trainings', 'READ', 0, NULL, NULL, NULL, NULL),
	(1081, 'Self Service', 'trainings', 'Trainings', 'ess.trainings.invitation.response', 'ess.trainings.invitation.response', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'trainings', 'Trainings', 'READ', 0, NULL, NULL, NULL, NULL),
	(1082, 'Self Service', 'trainings', 'Trainings', 'ess.trainings.attendance.confirm', 'ess.trainings.attendance.confirm', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'trainings', 'Trainings', 'READ', 0, NULL, NULL, NULL, NULL),
	(1083, 'Self Service', 'trainings', 'Trainings', 'ess.trainings.', 'ess.trainings.', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'trainings', 'Trainings', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1084, 'Self Service', 'recruitment', 'Recruitment', 'ess.recruitment.job.posts', 'ess.recruitment.job.posts', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'recruitment', 'Recruitment', 'READ', 0, NULL, NULL, NULL, NULL),
	(1085, 'Self Service', 'recruitment', 'Recruitment', 'ess.recruitment.job.details', 'ess.recruitment.job.details', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'recruitment', 'Recruitment', 'READ', 0, NULL, NULL, NULL, NULL),
	(1086, 'Self Service', 'recruitment', 'Recruitment', 'ess.recruitment.apply.job', 'ess.recruitment.apply.job', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'recruitment', 'Recruitment', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1087, 'Self Service', 'shifts', 'Shifts', 'ess.shifts.index', 'ess.shifts.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'shifts', 'Shifts', 'READ', 0, NULL, NULL, NULL, NULL),
	(1088, 'Self Service', 'documents', 'Documents', 'ess.documents.index', 'ess.documents.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'documents', 'Documents', 'READ', 0, NULL, NULL, NULL, NULL),
	(1089, 'Self Service', 'documents', 'Documents', 'ess.documents.acknowledge', 'ess.documents.acknowledge', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'documents', 'Documents', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1090, 'Self Service', 'documents', 'Documents', 'ess.documents.serve', 'ess.documents.serve', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'documents', 'Documents', 'READ', 0, NULL, NULL, NULL, NULL),
	(1091, 'Self Service', 'documents', 'Documents', 'ess.documents.docs.upload', 'ess.documents.docs.upload', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'documents', 'Documents', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1092, 'Self Service', 'employee', 'Employee', 'ess.employee.edit.profile', 'ess.employee.edit.profile', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'employee', 'Employee', 'READ', 0, NULL, NULL, NULL, NULL),
	(1093, 'Self Service', 'employee', 'Employee', 'ess.employee.update.profile', 'ess.employee.update.profile', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'employee', 'Employee', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1094, 'Self Service', 'employee', 'Employee', 'ess.employee.qualification.store', 'ess.employee.qualification.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'employee', 'Employee', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1095, 'Self Service', 'employee', 'Employee', 'ess.employee.experience.store', 'ess.employee.experience.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'employee', 'Employee', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1096, 'Self Service', 'Feedback', 'Feedback', 'ess.feedback.index', 'ess.feedback.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'ess_feedback', 'Ess feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1097, 'Self Service', 'Feedback', 'Feedback', 'ess.feedback.create', 'ess.feedback.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'ess_feedback', 'Ess feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1098, 'Self Service', 'Feedback', 'Feedback', 'ess.feedback.store', 'ess.feedback.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'ess_feedback', 'Ess feedback', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1099, 'Self Service', 'Feedback', 'Feedback', 'ess.feedback.view', 'ess.feedback.view', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'ess_feedback', 'Ess feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1100, 'Self Service', 'Feedback', 'Feedback', 'ess.feedback.show', 'ess.feedback.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'ess_feedback', 'Ess feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1101, 'Self Service', 'Feedback', 'Feedback', 'ess.feedback.update', 'ess.feedback.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'ess_feedback', 'Ess feedback', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1102, 'Self Service', 'Feedback', 'Feedback', 'ess.feedback.delete', 'ess.feedback.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'ess_feedback', 'Ess feedback', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1103, 'Self Service', 'Feedback', 'Feedback', 'ess.feedback.anonymous.create', 'ess.feedback.anonymous.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'ess_feedback', 'Ess feedback', 'READ', 0, NULL, NULL, NULL, NULL),
	(1104, 'Self Service', 'Feedback', 'Feedback', 'ess.feedback.anonymous.store', 'ess.feedback.anonymous.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'ess_feedback', 'Ess feedback', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1105, 'Self Service', 'survey', 'Survey', 'ess.survey.index', 'ess.survey.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'survey', 'Survey', 'READ', 0, NULL, NULL, NULL, NULL),
	(1106, 'Self Service', 'subordinates', 'Subordinates', 'ess.subordinates.index', 'ess.subordinates.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'subordinates', 'Subordinates', 'READ', 0, NULL, NULL, NULL, NULL),
	(1107, 'Self Service', 'loans', 'Loans', 'ess.loans.index', 'ess.loans.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'my_loans', 'My loans', 'READ', 0, NULL, NULL, NULL, NULL),
	(1108, 'Self Service', 'loans', 'Loans', 'ess.loans.create', 'ess.loans.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'my_loans', 'My loans', 'READ', 0, NULL, NULL, NULL, NULL),
	(1109, 'Self Service', 'loans', 'Loans', 'ess.loans.store', 'ess.loans.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'my_loans', 'My loans', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1110, 'Self Service', 'loans', 'Loans', 'ess.loans.show', 'ess.loans.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'my_loans', 'My loans', 'READ', 0, NULL, NULL, NULL, NULL),
	(1111, 'Self Service', 'performance', 'Performance', 'ess.performance.myAppraisals', 'ess.performance.myAppraisals', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'self_evaluation', 'Self evaluation', 'READ', 0, NULL, NULL, NULL, NULL),
	(1112, 'Self Service', 'performance', 'Performance', 'ess.performance.selfEvaluation', 'ess.performance.selfEvaluation', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'self_evaluation', 'Self evaluation', 'READ', 0, NULL, NULL, NULL, NULL),
	(1113, 'Self Service', 'performance', 'Performance', 'ess.performance.selfReview', 'ess.performance.selfReview', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'self_evaluation', 'Self evaluation', 'READ', 0, NULL, NULL, NULL, NULL),
	(1114, 'Self Service', 'performance', 'Performance', 'ess.performance.saveSelfReview', 'ess.performance.saveSelfReview', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'self_evaluation', 'Self evaluation', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1115, 'Self Service', 'performance', 'Performance', 'ess.performance.submitSelfReview', 'ess.performance.submitSelfReview', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'self_evaluation', 'Self evaluation', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1116, 'Self Service', 'performance', 'Performance', 'ess.performance.show', 'ess.performance.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'self_evaluation', 'Self evaluation', 'READ', 0, NULL, NULL, NULL, NULL),
	(1117, 'Self Service', 'performance', 'Performance', 'ess.pip.myPlans', 'ess.pip.myPlans', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'my_pip', 'My pip', 'READ', 0, NULL, NULL, NULL, NULL),
	(1118, 'Self Service', 'performance', 'Performance', 'ess.pip.show', 'ess.pip.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'my_pip', 'My pip', 'READ', 0, NULL, NULL, NULL, NULL),
	(1119, 'Self Service', 'vehicles', 'Vehicles', 'ess.vehicle.myVehicle', 'ess.vehicle.myVehicle', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 15, 'my_vehicle', 'My vehicle', 'READ', 0, NULL, NULL, NULL, NULL),
	(1120, 'HR Uploads', 'documents', 'Documents', 'document-categories.index', 'document-categories.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_categories', 'Document categories', 'READ', 0, NULL, NULL, NULL, NULL),
	(1121, 'HR Uploads', 'documents', 'Documents', 'document-categories.create', 'document-categories.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_categories', 'Document categories', 'READ', 0, NULL, NULL, NULL, NULL),
	(1122, 'HR Uploads', 'documents', 'Documents', 'document-categories.store', 'document-categories.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_categories', 'Document categories', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1123, 'HR Uploads', 'documents', 'Documents', 'document-categories.edit', 'document-categories.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_categories', 'Document categories', 'READ', 0, NULL, NULL, NULL, NULL),
	(1124, 'HR Uploads', 'documents', 'Documents', 'document-categories.update', 'document-categories.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_categories', 'Document categories', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1125, 'HR Uploads', 'documents', 'Documents', 'document-categories.delete', 'document-categories.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_categories', 'Document categories', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1126, 'HR Uploads', 'documents', 'Documents', 'documents-upload.deleted-docs', 'documents-upload.deleted-docs', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'deleted-documents', 'Deleted-documents', 'READ', 0, NULL, NULL, NULL, NULL),
	(1127, 'HR Uploads', 'documents', 'Documents', 'documents-upload.restore-document', 'documents-upload.restore-document', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'deleted-documents', 'Deleted-documents', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1128, 'HR Uploads', 'documents', 'Documents', 'documents-upload.show-deleted-document', 'documents-upload.show-deleted-document', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'deleted-documents', 'Deleted-documents', 'READ', 0, NULL, NULL, NULL, NULL),
	(1129, 'HR Uploads', 'documents', 'Documents', 'documents-upload.index', 'documents-upload.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1130, 'HR Uploads', 'documents', 'Documents', 'documents-upload.create', 'documents-upload.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1131, 'HR Uploads', 'documents', 'Documents', 'documents-upload.show', 'documents-upload.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1132, 'HR Uploads', 'documents', 'Documents', 'documents-upload.store', 'documents-upload.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1133, 'HR Uploads', 'documents', 'Documents', 'documents-upload.edit', 'documents-upload.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1134, 'HR Uploads', 'documents', 'Documents', 'documents-upload.update', 'documents-upload.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1135, 'HR Uploads', 'documents', 'Documents', 'documents-upload.delete', 'documents-upload.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1136, 'HR Uploads', 'documents', 'Documents', 'documents-upload.review', 'documents-upload.review', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1137, 'HR Uploads', 'documents', 'Documents', 'documents-upload.update-review', 'documents-upload.update-review', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1138, 'HR Uploads', 'documents', 'Documents', 'documents-upload.show-document', 'documents-upload.show-document', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1139, 'HR Uploads', 'documents', 'Documents', 'documents-upload.serve', 'documents-upload.serve', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1140, 'HR Uploads', 'documents', 'Documents', 'documents-upload.download', 'documents-upload.download', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1141, 'HR Uploads', 'documents', 'Documents', 'documents-upload.consents', 'documents-upload.consents', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1142, 'HR Uploads', 'documents', 'Documents', 'documents-upload.consents.download', 'documents-upload.consents.download', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'document_uploads', 'Document uploads', 'READ', 0, NULL, NULL, NULL, NULL),
	(1143, 'HR Uploads', 'documents', 'Documents', 'documents-upload.consent-summary', 'documents-upload.consent-summary', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1144, 'Hr Uploads', 'documents', 'Documents', 'offboarding-process.index', 'offboarding-process.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'offboarding_process', 'Offboarding process', 'READ', 0, NULL, NULL, NULL, NULL),
	(1145, 'Hr Uploads', 'documents', 'Documents', 'offboarding-process.create', 'offboarding-process.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'offboarding_process', 'Offboarding process', 'READ', 0, NULL, NULL, NULL, NULL),
	(1146, 'Hr Uploads', 'documents', 'Documents', 'offboarding-process.store', 'offboarding-process.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'offboarding_process', 'Offboarding process', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1147, 'Hr Uploads', 'documents', 'Documents', 'offboarding-process.edit', 'offboarding-process.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'offboarding_process', 'Offboarding process', 'READ', 0, NULL, NULL, NULL, NULL),
	(1148, 'Hr Uploads', 'documents', 'Documents', 'offboarding-process.update', 'offboarding-process.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'offboarding_process', 'Offboarding process', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1149, 'Hr Uploads', 'documents', 'Documents', 'offboarding-process.delete', 'offboarding-process.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 16, 'offboarding_process', 'Offboarding process', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1150, 'Survey', 'survey', 'Survey', 'survey.index', 'survey.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1151, 'Survey', 'survey', 'Survey', 'survey.create', 'survey.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1152, 'Survey', 'survey', 'Survey', 'survey.google.auth', 'survey.google.auth', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1153, 'Survey', 'survey', 'Survey', 'survey.auth.google.callback', 'survey.auth.google.callback', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1154, 'Survey', 'survey', 'Survey', 'survey.forms.create', 'survey.forms.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1155, 'Survey', 'survey', 'Survey', 'survey.getLocationsByRegions', 'survey.getLocationsByRegions', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1156, 'Survey', 'survey', 'Survey', 'survey.edit', 'survey.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1157, 'Survey', 'survey', 'Survey', 'survey.forms.update', 'survey.forms.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1158, 'Survey', 'survey', 'Survey', 'survey.targeted-employees', 'survey.targeted-employees', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1159, 'Survey', 'survey', 'Survey', 'survey.delete', 'survey.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1160, 'Survey', 'survey', 'Survey', 'survey.export-employees', 'survey.export-employees', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 17, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1161, 'project', 'default', 'Default', 'project.project-allocation.store', 'project.project-allocation.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1162, 'project', 'default', 'Default', 'project.project-allocation.create', 'project.project-allocation.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1163, 'project', 'default', 'Default', 'project.project-allocation.edit', 'project.project-allocation.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1164, 'project', 'default', 'Default', 'project.project-allocation.update', 'project.project-allocation.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1165, 'project', 'default', 'Default', 'project.project-allocation.delete', 'project.project-allocation.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1166, 'project', 'default', 'Default', 'project.project-allocations.index', 'project.project-allocations.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1167, 'project', 'default', 'Default', 'project.project-allocations.store', 'project.project-allocations.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1168, 'project', 'default', 'Default', 'project.project-allocation-report.index', 'project.project-allocation-report.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1169, 'project', 'default', 'Default', 'project.project-allocation-report.export', 'project.project-allocation-report.export', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1170, 'project', 'default', 'Default', 'project.project-allocations.bulk-upload.index', 'project.project-allocations.bulk-upload.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1171, 'project', 'default', 'Default', 'project.project-allocations.bulk-upload.download-template', 'project.project-allocations.bulk-upload.download-template', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1172, 'project', 'default', 'Default', 'project.project-allocations.bulk-upload.import', 'project.project-allocations.bulk-upload.import', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 18, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1173, 'Performance Management', 'setup', 'Setup', 'performance.ratingScale.index', 'performance.ratingScale.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'rating_scale', 'Rating scale', 'READ', 0, NULL, NULL, NULL, NULL),
	(1174, 'Performance Management', 'setup', 'Setup', 'performance.ratingScale.create', 'performance.ratingScale.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'rating_scale', 'Rating scale', 'READ', 0, NULL, NULL, NULL, NULL),
	(1175, 'Performance Management', 'setup', 'Setup', 'performance.ratingScale.store', 'performance.ratingScale.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'rating_scale', 'Rating scale', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1176, 'Performance Management', 'setup', 'Setup', 'performance.ratingScale.edit', 'performance.ratingScale.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'rating_scale', 'Rating scale', 'READ', 0, NULL, NULL, NULL, NULL),
	(1177, 'Performance Management', 'setup', 'Setup', 'performance.ratingScale.update', 'performance.ratingScale.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'rating_scale', 'Rating scale', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1178, 'Performance Management', 'setup', 'Setup', 'performance.ratingScale.delete', 'performance.ratingScale.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'rating_scale', 'Rating scale', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1179, 'Performance Management', 'setup', 'Setup', 'performance.reviewPeriod.index', 'performance.reviewPeriod.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'review_period', 'Review period', 'READ', 0, NULL, NULL, NULL, NULL),
	(1180, 'Performance Management', 'setup', 'Setup', 'performance.reviewPeriod.create', 'performance.reviewPeriod.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'review_period', 'Review period', 'READ', 0, NULL, NULL, NULL, NULL),
	(1181, 'Performance Management', 'setup', 'Setup', 'performance.reviewPeriod.store', 'performance.reviewPeriod.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'review_period', 'Review period', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1182, 'Performance Management', 'setup', 'Setup', 'performance.reviewPeriod.edit', 'performance.reviewPeriod.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'review_period', 'Review period', 'READ', 0, NULL, NULL, NULL, NULL),
	(1183, 'Performance Management', 'setup', 'Setup', 'performance.reviewPeriod.update', 'performance.reviewPeriod.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'review_period', 'Review period', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1184, 'Performance Management', 'setup', 'Setup', 'performance.reviewPeriod.delete', 'performance.reviewPeriod.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'review_period', 'Review period', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1185, 'Performance Management', 'setup', 'Setup', 'performance.focusArea.index', 'performance.focusArea.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'focus_area', 'Focus area', 'READ', 0, NULL, NULL, NULL, NULL),
	(1186, 'Performance Management', 'setup', 'Setup', 'performance.focusArea.create', 'performance.focusArea.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'focus_area', 'Focus area', 'READ', 0, NULL, NULL, NULL, NULL),
	(1187, 'Performance Management', 'setup', 'Setup', 'performance.focusArea.store', 'performance.focusArea.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'focus_area', 'Focus area', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1188, 'Performance Management', 'setup', 'Setup', 'performance.focusArea.edit', 'performance.focusArea.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'focus_area', 'Focus area', 'READ', 0, NULL, NULL, NULL, NULL),
	(1189, 'Performance Management', 'setup', 'Setup', 'performance.focusArea.update', 'performance.focusArea.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'focus_area', 'Focus area', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1190, 'Performance Management', 'setup', 'Setup', 'performance.focusArea.delete', 'performance.focusArea.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'focus_area', 'Focus area', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1191, 'Performance Management', 'setup', 'Setup', 'performance.goal.index', 'performance.goal.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_goal', 'Performance goal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1192, 'Performance Management', 'setup', 'Setup', 'performance.goal.create', 'performance.goal.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_goal', 'Performance goal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1193, 'Performance Management', 'setup', 'Setup', 'performance.goal.store', 'performance.goal.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_goal', 'Performance goal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1194, 'Performance Management', 'setup', 'Setup', 'performance.goal.edit', 'performance.goal.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_goal', 'Performance goal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1195, 'Performance Management', 'setup', 'Setup', 'performance.goal.update', 'performance.goal.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_goal', 'Performance goal', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1196, 'Performance Management', 'setup', 'Setup', 'performance.goal.delete', 'performance.goal.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_goal', 'Performance goal', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1197, 'Performance Management', 'setup', 'Setup', 'performance.behavioralItem.index', 'performance.behavioralItem.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'behavioral_item', 'Behavioral item', 'READ', 0, NULL, NULL, NULL, NULL),
	(1198, 'Performance Management', 'setup', 'Setup', 'performance.behavioralItem.create', 'performance.behavioralItem.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'behavioral_item', 'Behavioral item', 'READ', 0, NULL, NULL, NULL, NULL),
	(1199, 'Performance Management', 'setup', 'Setup', 'performance.behavioralItem.store', 'performance.behavioralItem.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'behavioral_item', 'Behavioral item', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1200, 'Performance Management', 'setup', 'Setup', 'performance.behavioralItem.edit', 'performance.behavioralItem.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'behavioral_item', 'Behavioral item', 'READ', 0, NULL, NULL, NULL, NULL),
	(1201, 'Performance Management', 'setup', 'Setup', 'performance.behavioralItem.update', 'performance.behavioralItem.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'behavioral_item', 'Behavioral item', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1202, 'Performance Management', 'setup', 'Setup', 'performance.behavioralItem.delete', 'performance.behavioralItem.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'behavioral_item', 'Behavioral item', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1203, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.index', 'performance.appraisal.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1204, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.create', 'performance.appraisal.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1205, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.store', 'performance.appraisal.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1206, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.template.download', 'performance.appraisal.template.download', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1207, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.bulkUpload', 'performance.appraisal.bulkUpload', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1208, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.show', 'performance.appraisal.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1209, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.edit', 'performance.appraisal.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1210, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.update', 'performance.appraisal.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1211, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.delete', 'performance.appraisal.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1212, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.selfReview', 'performance.appraisal.selfReview', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1213, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.saveSelfReview', 'performance.appraisal.saveSelfReview', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1214, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.hodReview', 'performance.appraisal.hodReview', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1215, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.saveHodReview', 'performance.appraisal.saveHodReview', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1216, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.finalize', 'performance.appraisal.finalize', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1217, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.employeeSign', 'performance.appraisal.employeeSign', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1218, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.supervisorSign', 'performance.appraisal.supervisorSign', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1219, 'Performance Management', 'appraisals', 'Appraisals', 'performance.appraisal.hodSign', 'performance.appraisal.hodSign', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_appraisal', 'Performance appraisal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1220, 'Performance Management', 'supervisor_eval', 'Supervisor eval', 'performance.supervisor.evaluations', 'performance.supervisor.evaluations', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'supervisor_evaluation', 'Supervisor evaluation', 'READ', 0, NULL, NULL, NULL, NULL),
	(1221, 'Performance Management', 'supervisor_eval', 'Supervisor eval', 'performance.supervisor.review', 'performance.supervisor.review', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'supervisor_evaluation', 'Supervisor evaluation', 'READ', 0, NULL, NULL, NULL, NULL),
	(1222, 'Performance Management', 'supervisor_eval', 'Supervisor eval', 'performance.supervisor.saveReview', 'performance.supervisor.saveReview', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'supervisor_evaluation', 'Supervisor evaluation', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1223, 'Performance Management', 'hod_eval', 'Hod eval', 'performance.hod.evaluations', 'performance.hod.evaluations', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'hod_evaluation', 'Hod evaluation', 'READ', 0, NULL, NULL, NULL, NULL),
	(1224, 'Performance Management', 'default', 'Default', 'performance.ajax.focusAreasForEmployee', 'performance.ajax.focusAreasForEmployee', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1225, 'Performance Management', 'reports', 'Reports', 'performance.report.department', 'performance.report.department', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_reports', 'Performance reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(1226, 'Performance Management', 'reports', 'Reports', 'performance.report.department.download', 'performance.report.department.download', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_reports', 'Performance reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1227, 'Performance Management', 'reports', 'Reports', 'performance.report.employee', 'performance.report.employee', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_reports', 'Performance reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(1228, 'Performance Management', 'reports', 'Reports', 'performance.report.employee.download', 'performance.report.employee.download', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_reports', 'Performance reports', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1229, 'Performance Management', 'reports', 'Reports', 'performance.report.summary', 'performance.report.summary', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 19, 'performance_reports', 'Performance reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(1230, 'PIP Management', 'pip', 'Pip', 'pip.plan.index', 'pip.plan.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'READ', 0, NULL, NULL, NULL, NULL),
	(1231, 'PIP Management', 'pip', 'Pip', 'pip.plan.create', 'pip.plan.create', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'READ', 0, NULL, NULL, NULL, NULL),
	(1232, 'PIP Management', 'pip', 'Pip', 'pip.plan.createFromAppraisal', 'pip.plan.createFromAppraisal', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'READ', 0, NULL, NULL, NULL, NULL),
	(1233, 'PIP Management', 'pip', 'Pip', 'pip.plan.store', 'pip.plan.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1234, 'PIP Management', 'pip', 'Pip', 'pip.plan.show', 'pip.plan.show', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'READ', 0, NULL, NULL, NULL, NULL),
	(1235, 'PIP Management', 'pip', 'Pip', 'pip.plan.edit', 'pip.plan.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'READ', 0, NULL, NULL, NULL, NULL),
	(1236, 'PIP Management', 'pip', 'Pip', 'pip.plan.update', 'pip.plan.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1237, 'PIP Management', 'pip', 'Pip', 'pip.plan.delete', 'pip.plan.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1238, 'PIP Management', 'pip', 'Pip', 'pip.plan.activate', 'pip.plan.activate', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1239, 'PIP Management', 'pip', 'Pip', 'pip.plan.employeeAcknowledge', 'pip.plan.employeeAcknowledge', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1240, 'PIP Management', 'pip', 'Pip', 'pip.plan.supervisorSign', 'pip.plan.supervisorSign', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1241, 'PIP Management', 'pip', 'Pip', 'pip.plan.hrValidate', 'pip.plan.hrValidate', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1242, 'PIP Management', 'pip', 'Pip', 'pip.plan.finalizeOutcome', 'pip.plan.finalizeOutcome', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1243, 'PIP Management', 'pip', 'Pip', 'pip.plan.lock', 'pip.plan.lock', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1244, 'PIP Management', 'pip', 'Pip', 'pip.plan.employeeDetails', 'pip.plan.employeeDetails', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_plan', 'Pip plan', 'READ', 0, NULL, NULL, NULL, NULL),
	(1245, 'PIP Management', 'pip', 'Pip', 'pip.goal.index', 'pip.goal.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_goal', 'Pip goal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1246, 'PIP Management', 'pip', 'Pip', 'pip.goal.store', 'pip.goal.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_goal', 'Pip goal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1247, 'PIP Management', 'pip', 'Pip', 'pip.goal.edit', 'pip.goal.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_goal', 'Pip goal', 'READ', 0, NULL, NULL, NULL, NULL),
	(1248, 'PIP Management', 'pip', 'Pip', 'pip.goal.update', 'pip.goal.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_goal', 'Pip goal', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1249, 'PIP Management', 'pip', 'Pip', 'pip.goal.delete', 'pip.goal.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_goal', 'Pip goal', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1250, 'PIP Management', 'pip', 'Pip', 'pip.goal.updateStatus', 'pip.goal.updateStatus', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_goal', 'Pip goal', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1251, 'PIP Management', 'pip', 'Pip', 'pip.support.index', 'pip.support.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_support', 'Pip support', 'READ', 0, NULL, NULL, NULL, NULL),
	(1252, 'PIP Management', 'pip', 'Pip', 'pip.support.store', 'pip.support.store', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_support', 'Pip support', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1253, 'PIP Management', 'pip', 'Pip', 'pip.support.edit', 'pip.support.edit', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_support', 'Pip support', 'READ', 0, NULL, NULL, NULL, NULL),
	(1254, 'PIP Management', 'pip', 'Pip', 'pip.support.update', 'pip.support.update', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_support', 'Pip support', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1255, 'PIP Management', 'pip', 'Pip', 'pip.support.delete', 'pip.support.delete', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_support', 'Pip support', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1256, 'PIP Management', 'pip', 'Pip', 'pip.support.updateStatus', 'pip.support.updateStatus', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_support', 'Pip support', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1257, 'PIP Management', 'pip', 'Pip', 'pip.schedule.index', 'pip.schedule.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_schedule', 'Pip schedule', 'READ', 0, NULL, NULL, NULL, NULL),
	(1258, 'PIP Management', 'pip', 'Pip', 'pip.schedule.conduct', 'pip.schedule.conduct', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_schedule', 'Pip schedule', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1259, 'PIP Management', 'pip', 'Pip', 'pip.schedule.reschedule', 'pip.schedule.reschedule', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_schedule', 'Pip schedule', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1260, 'PIP Management', 'reports', 'Reports', 'pip.report.dashboard', 'pip.report.dashboard', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_reports', 'Pip reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(1261, 'PIP Management', 'reports', 'Reports', 'pip.report.byDepartment', 'pip.report.byDepartment', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_reports', 'Pip reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(1262, 'PIP Management', 'reports', 'Reports', 'pip.report.byOutcome', 'pip.report.byOutcome', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 20, 'pip_reports', 'Pip reports', 'READ', 0, NULL, NULL, NULL, NULL),
	(1263, 'Vehicle Management', 'reports', 'Reports', 'vehicle.assignment.index', 'vehicle.assignment.index', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 21, 'assignments', 'Assignments', 'READ', 0, NULL, NULL, NULL, NULL),
	(1264, 'Vehicle Management', 'reports', 'Reports', 'vehicle.assignment.download', 'vehicle.assignment.download', '2026-05-19 15:07:20', '2026-05-19 15:07:20', NULL, 21, 'assignments', 'Assignments', 'READ', 0, NULL, NULL, NULL, NULL),
	(1265, 'Vehicle Management', 'reports', 'Reports', 'vehicle.assignment.vehicle_history', 'vehicle.assignment.vehicle_history', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'assignments', 'Assignments', 'READ', 0, NULL, NULL, NULL, NULL),
	(1266, 'Vehicle Management', 'reports', 'Reports', 'vehicle.assignment.employee_history', 'vehicle.assignment.employee_history', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'assignments', 'Assignments', 'READ', 0, NULL, NULL, NULL, NULL),
	(1267, 'Vehicle Management', 'setup', 'Setup', 'vehicle.index', 'vehicle.index', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'READ', 0, NULL, NULL, NULL, NULL),
	(1268, 'Vehicle Management', 'setup', 'Setup', 'vehicle.create', 'vehicle.create', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'READ', 0, NULL, NULL, NULL, NULL),
	(1269, 'Vehicle Management', 'setup', 'Setup', 'vehicle.store', 'vehicle.store', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1270, 'Vehicle Management', 'setup', 'Setup', 'vehicle.get_drivers', 'vehicle.get_drivers', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'READ', 0, NULL, NULL, NULL, NULL),
	(1271, 'Vehicle Management', 'setup', 'Setup', 'vehicle.import', 'vehicle.import', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1272, 'Vehicle Management', 'setup', 'Setup', 'vehicle.download_template', 'vehicle.download_template', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'READ', 0, NULL, NULL, NULL, NULL),
	(1273, 'Vehicle Management', 'setup', 'Setup', 'vehicle.edit', 'vehicle.edit', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'READ', 0, NULL, NULL, NULL, NULL),
	(1274, 'Vehicle Management', 'setup', 'Setup', 'vehicle.show', 'vehicle.show', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'READ', 0, NULL, NULL, NULL, NULL),
	(1275, 'Vehicle Management', 'setup', 'Setup', 'vehicle.update', 'vehicle.update', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1276, 'Vehicle Management', 'setup', 'Setup', 'vehicle.delete', 'vehicle.delete', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1277, 'Vehicle Management', 'setup', 'Setup', 'vehicle.assign_driver', 'vehicle.assign_driver', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1278, 'Vehicle Management', 'setup', 'Setup', 'vehicle.unassign_driver', 'vehicle.unassign_driver', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 21, 'vehicles', 'Vehicles', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1279, 'Administration', 'default', 'Default', 'job.internal.apply', 'job.internal.apply', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1280, 'Administration', 'default', 'Default', 'home.dashboard', 'home.dashboard', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1281, 'Administration', 'default', 'Default', 'home.profile', 'home.profile', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1282, 'Administration', 'default', 'Default', 'send-password-change-otp-web', 'send-password-change-otp-web', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1283, 'Administration', 'default', 'Default', 'home.logout', 'home.logout', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'READ', 0, NULL, NULL, NULL, NULL),
	(1284, 'Administration', 'default', 'Default', 'employee.updateEarningsAndBenefits', 'employee.updateEarningsAndBenefits', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1285, 'Administration', 'default', 'Default', 'employee.addDeduction.web', 'employee.addDeduction.web', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1286, 'Administration', 'default', 'Default', 'employee.updateDeduction.web', 'employee.updateDeduction.web', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1287, 'Administration', 'default', 'Default', 'employee.deleteDeduction.web', 'employee.deleteDeduction.web', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'default', 'Default', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1288, 'Administration', 'user', 'User', 'user.inactive', 'user.inactive', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'user', 'User', 'READ', 0, NULL, NULL, NULL, NULL),
	(1289, 'Administration', 'user', 'User', 'user.active', 'user.active', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'user', 'User', 'READ', 0, NULL, NULL, NULL, NULL),
	(1290, 'Administration', 'role_permissions', 'Role permissions', 'roles.permission.menus', 'roles.permission.menus', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'roles', 'Roles', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1291, 'Administration', 'company', 'Company', 'company.index', 'company.index', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'company', 'Company', 'READ', 0, NULL, NULL, NULL, NULL),
	(1292, 'Administration', 'company', 'Company', 'company.create', 'company.create', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'company', 'Company', 'READ', 0, NULL, NULL, NULL, NULL),
	(1293, 'Administration', 'company', 'Company', 'company.store', 'company.store', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'company', 'Company', 'CREATE', 0, NULL, NULL, NULL, NULL),
	(1294, 'Administration', 'company', 'Company', 'company.show', 'company.show', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'company', 'Company', 'READ', 0, NULL, NULL, NULL, NULL),
	(1295, 'Administration', 'company', 'Company', 'company.edit', 'company.edit', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'company', 'Company', 'READ', 0, NULL, NULL, NULL, NULL),
	(1296, 'Administration', 'company', 'Company', 'company.update', 'company.update', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'company', 'Company', 'UPDATE', 0, NULL, NULL, NULL, NULL),
	(1297, 'Administration', 'company', 'Company', 'company.destroy', 'company.destroy', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'company', 'Company', 'DELETE', 0, NULL, NULL, NULL, NULL),
	(1298, 'Administration', 'company', 'Company', 'company.switch', 'company.switch', '2026-05-19 15:07:21', '2026-05-19 15:07:21', NULL, 10, 'company', 'Company', 'CREATE', 0, NULL, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.holiday
DROP TABLE IF EXISTS `holiday`;
CREATE TABLE IF NOT EXISTS `holiday` (
  `holiday_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `holiday_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`holiday_id`),
  UNIQUE KEY `holiday_holiday_name_unique` (`holiday_name`),
  KEY `holiday_location_id_foreign` (`location_id`),
  KEY `holiday_company_id_foreign` (`company_id`),
  CONSTRAINT `holiday_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `holiday_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.holiday: ~0 rows (approximately)
DELETE FROM `holiday`;

-- Dumping structure for table stawi_self_client.holiday_details
DROP TABLE IF EXISTS `holiday_details`;
CREATE TABLE IF NOT EXISTS `holiday_details` (
  `holiday_details_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `holiday_id` int(10) unsigned NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`holiday_details_id`),
  KEY `holiday_details_location_id_foreign` (`location_id`),
  KEY `holiday_details_created_by_foreign` (`created_by`),
  KEY `holiday_details_updated_by_foreign` (`updated_by`),
  KEY `holiday_details_company_id_foreign` (`company_id`),
  CONSTRAINT `holiday_details_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `holiday_details_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `holiday_details_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  CONSTRAINT `holiday_details_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.holiday_details: ~0 rows (approximately)
DELETE FROM `holiday_details`;

-- Dumping structure for table stawi_self_client.hourly_salaries
DROP TABLE IF EXISTS `hourly_salaries`;
CREATE TABLE IF NOT EXISTS `hourly_salaries` (
  `hourly_salaries_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hourly_grade` varchar(191) NOT NULL,
  `hourly_rate` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`hourly_salaries_id`),
  KEY `hourly_salaries_location_id_foreign` (`location_id`),
  KEY `hourly_salaries_company_id_foreign` (`company_id`),
  CONSTRAINT `hourly_salaries_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hourly_salaries_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.hourly_salaries: ~0 rows (approximately)
DELETE FROM `hourly_salaries`;

-- Dumping structure for table stawi_self_client.hr_documents
DROP TABLE IF EXISTS `hr_documents`;
CREATE TABLE IF NOT EXISTS `hr_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `description` longtext NOT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `review_comment` longtext DEFAULT NULL,
  `approval_comment` longtext DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `file_hash` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approved_by` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`approved_by`)),
  `rejected_by` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rejected_by`)),
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hr_documents_company_id_foreign` (`company_id`),
  CONSTRAINT `hr_documents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.hr_documents: ~0 rows (approximately)
DELETE FROM `hr_documents`;

-- Dumping structure for table stawi_self_client.interview
DROP TABLE IF EXISTS `interview`;
CREATE TABLE IF NOT EXISTS `interview` (
  `interview_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_applicant_id` int(10) unsigned NOT NULL,
  `interview_date` date NOT NULL,
  `interview_time` time NOT NULL,
  `interview_type` varchar(191) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`interview_id`),
  KEY `interview_location_id_foreign` (`location_id`),
  KEY `interview_company_id_foreign` (`company_id`),
  CONSTRAINT `interview_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `interview_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.interview: ~0 rows (approximately)
DELETE FROM `interview`;

-- Dumping structure for table stawi_self_client.ip_settings
DROP TABLE IF EXISTS `ip_settings`;
CREATE TABLE IF NOT EXISTS `ip_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(191) DEFAULT NULL,
  `ip_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = not checking it 1 = checking ip',
  `status` tinyint(4) NOT NULL COMMENT '0 = not providing employee self attendance 1 = providing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_settings_location_id_foreign` (`location_id`),
  KEY `ip_settings_company_id_foreign` (`company_id`),
  CONSTRAINT `ip_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ip_settings_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.ip_settings: ~0 rows (approximately)
DELETE FROM `ip_settings`;

-- Dumping structure for table stawi_self_client.job
DROP TABLE IF EXISTS `job`;
CREATE TABLE IF NOT EXISTS `job` (
  `job_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_requisition_id` bigint(20) unsigned DEFAULT NULL,
  `job_title` varchar(200) NOT NULL,
  `job_type` tinyint(4) DEFAULT 3,
  `employment_type` varchar(50) DEFAULT NULL,
  `job_description` text NOT NULL,
  `job_requirements` text DEFAULT NULL,
  `application_end_date` date NOT NULL,
  `publish_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` int(10) unsigned DEFAULT NULL,
  `audience_type` enum('internal','external','both') NOT NULL DEFAULT 'both',
  `jd_file` varchar(200) DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `number_of_positions` int(11) NOT NULL DEFAULT 1,
  `minimum_salary` decimal(12,2) DEFAULT NULL,
  `maximum_salary` decimal(12,2) DEFAULT NULL,
  `minimum_qualifications` text DEFAULT NULL,
  `experience_required` varchar(255) DEFAULT NULL,
  `skills_competencies` text DEFAULT NULL,
  `key_responsibilities` text DEFAULT NULL,
  `other_benefits` text DEFAULT NULL,
  PRIMARY KEY (`job_id`),
  KEY `job_location_id_foreign` (`location_id`),
  KEY `job_company_id_foreign` (`company_id`),
  KEY `job_job_requisition_id_status_index` (`job_requisition_id`,`status`),
  KEY `job_department_id_status_index` (`department_id`,`status`),
  CONSTRAINT `job_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE SET NULL,
  CONSTRAINT `job_job_requisition_id_foreign` FOREIGN KEY (`job_requisition_id`) REFERENCES `job_requisitions` (`job_requisition_id`) ON DELETE SET NULL,
  CONSTRAINT `job_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.job: ~0 rows (approximately)
DELETE FROM `job`;

-- Dumping structure for table stawi_self_client.job_applicant
DROP TABLE IF EXISTS `job_applicant`;
CREATE TABLE IF NOT EXISTS `job_applicant` (
  `job_applicant_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `applicant_email` varchar(100) NOT NULL,
  `phone` int(11) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `attached_resume` varchar(200) NOT NULL,
  `application_date` date NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `years_of_experience` int(11) NOT NULL DEFAULT 0,
  `highest_qualification` varchar(191) NOT NULL DEFAULT 'None',
  `application_source` varchar(191) DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `current_address` varchar(500) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `current_employer` varchar(255) DEFAULT NULL,
  `current_position` varchar(255) DEFAULT NULL,
  `notice_period` varchar(50) DEFAULT NULL,
  `expected_salary` decimal(12,2) DEFAULT NULL,
  `linkedin_url` varchar(500) DEFAULT NULL,
  `portfolio_url` varchar(500) DEFAULT NULL,
  `referral_source` varchar(100) DEFAULT NULL,
  `additional_comments` text DEFAULT NULL,
  PRIMARY KEY (`job_applicant_id`),
  KEY `job_applicant_location_id_foreign` (`location_id`),
  KEY `job_applicant_company_id_foreign` (`company_id`),
  CONSTRAINT `job_applicant_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_applicant_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.job_applicant: ~0 rows (approximately)
DELETE FROM `job_applicant`;

-- Dumping structure for table stawi_self_client.job_applicant_evaluations
DROP TABLE IF EXISTS `job_applicant_evaluations`;
CREATE TABLE IF NOT EXISTS `job_applicant_evaluations` (
  `evaluation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_applicant_id` int(10) unsigned NOT NULL,
  `evaluated_by` bigint(20) unsigned NOT NULL,
  `job_requisition_id` bigint(20) unsigned DEFAULT NULL,
  `education_score` int(11) DEFAULT NULL COMMENT '1-10 scale',
  `experience_score` int(11) DEFAULT NULL COMMENT '1-10 scale',
  `technical_skills_score` int(11) DEFAULT NULL COMMENT '1-10 scale',
  `communication_score` int(11) DEFAULT NULL COMMENT '1-10 scale',
  `cultural_fit_score` int(11) DEFAULT NULL COMMENT '1-10 scale',
  `problem_solving_score` int(11) DEFAULT NULL COMMENT '1-10 scale',
  `overall_score` decimal(4,2) DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `weaknesses` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recommendation` varchar(20) DEFAULT NULL COMMENT 'hire, reject, maybe, second_interview',
  `interview_id` int(10) unsigned DEFAULT NULL,
  `evaluation_stage` varchar(30) NOT NULL DEFAULT 'screening' COMMENT 'screening, first_interview, second_interview, final',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`evaluation_id`),
  KEY `jae_applicant_stage_idx` (`job_applicant_id`,`evaluation_stage`),
  KEY `jae_evaluator_created_idx` (`evaluated_by`,`created_at`),
  KEY `jae_requisition_idx` (`job_requisition_id`),
  KEY `jae_interview_idx` (`interview_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.job_applicant_evaluations: ~0 rows (approximately)
DELETE FROM `job_applicant_evaluations`;

-- Dumping structure for table stawi_self_client.job_hiring_teams
DROP TABLE IF EXISTS `job_hiring_teams`;
CREATE TABLE IF NOT EXISTS `job_hiring_teams` (
  `hiring_team_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_requisition_id` bigint(20) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'interviewer' COMMENT 'hiring_manager, interviewer, hr_business_partner, recruiter',
  `is_primary_hiring_manager` tinyint(1) NOT NULL DEFAULT 0,
  `can_screen_candidates` tinyint(1) NOT NULL DEFAULT 1,
  `can_conduct_interviews` tinyint(1) NOT NULL DEFAULT 1,
  `can_make_offers` tinyint(1) NOT NULL DEFAULT 0,
  `can_approve_hire` tinyint(1) NOT NULL DEFAULT 0,
  `interview_availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Store preferred interview days/times' CHECK (json_valid(`interview_availability`)),
  `notes` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `added_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`hiring_team_id`),
  UNIQUE KEY `job_hiring_teams_job_requisition_id_employee_id_unique` (`job_requisition_id`,`employee_id`),
  KEY `job_hiring_teams_job_requisition_id_status_index` (`job_requisition_id`,`status`),
  KEY `job_hiring_teams_employee_id_role_index` (`employee_id`,`role`),
  KEY `job_hiring_teams_added_by_index` (`added_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.job_hiring_teams: ~0 rows (approximately)
DELETE FROM `job_hiring_teams`;

-- Dumping structure for table stawi_self_client.job_requisition_templates
DROP TABLE IF EXISTS `job_requisition_templates`;
CREATE TABLE IF NOT EXISTS `job_requisition_templates` (
  `template_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `template_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `position_title` varchar(100) NOT NULL,
  `job_type` varchar(50) NOT NULL,
  `employment_type` varchar(50) NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `job_description` text NOT NULL,
  `job_requirements` text DEFAULT NULL,
  `key_responsibilities` text DEFAULT NULL,
  `skills_competencies` text DEFAULT NULL,
  `minimum_qualifications` text DEFAULT NULL,
  `experience_required` varchar(100) DEFAULT NULL,
  `default_number_of_positions` int(11) NOT NULL DEFAULT 1,
  `default_minimum_salary` decimal(12,2) DEFAULT NULL,
  `default_maximum_salary` decimal(12,2) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'KES',
  `requires_hod_approval` tinyint(1) NOT NULL DEFAULT 1,
  `requires_hr_approval` tinyint(1) NOT NULL DEFAULT 1,
  `requires_finance_approval` tinyint(1) NOT NULL DEFAULT 0,
  `requires_md_approval` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `job_requisition_templates_template_code_unique` (`template_code`),
  KEY `job_requisition_templates_location_id_foreign` (`location_id`),
  KEY `job_requisition_templates_created_by_foreign` (`created_by`),
  KEY `job_requisition_templates_department_id_status_index` (`department_id`,`status`),
  KEY `job_requisition_templates_job_type_status_index` (`job_type`,`status`),
  CONSTRAINT `job_requisition_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_requisition_templates_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE CASCADE,
  CONSTRAINT `job_requisition_templates_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.job_requisition_templates: ~0 rows (approximately)
DELETE FROM `job_requisition_templates`;

-- Dumping structure for table stawi_self_client.job_requisitions
DROP TABLE IF EXISTS `job_requisitions`;
CREATE TABLE IF NOT EXISTS `job_requisitions` (
  `job_requisition_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requisition_number` varchar(191) NOT NULL,
  `position_title` varchar(200) NOT NULL,
  `job_description` text NOT NULL,
  `key_responsibilities` text DEFAULT NULL,
  `job_requirements` text NOT NULL,
  `minimum_qualifications` text DEFAULT NULL,
  `experience_required` varchar(255) DEFAULT NULL,
  `skills_competencies` text DEFAULT NULL,
  `number_of_positions` int(11) NOT NULL DEFAULT 1,
  `job_type` varchar(50) NOT NULL,
  `employment_type` varchar(50) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `work_location` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `minimum_salary` decimal(12,2) DEFAULT NULL,
  `maximum_salary` decimal(12,2) DEFAULT NULL,
  `other_benefits` text DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'KES',
  `required_by_date` date NOT NULL,
  `proposed_start_date` date DEFAULT NULL,
  `urgency_level` varchar(20) NOT NULL DEFAULT 'normal',
  `reason_for_requisition` text NOT NULL,
  `requisition_type` varchar(50) NOT NULL DEFAULT 'new_position',
  `replaced_employee_name` varchar(200) DEFAULT NULL,
  `replacement_reason` varchar(50) DEFAULT NULL,
  `replacement_reason_other` varchar(255) DEFAULT NULL,
  `budget_justification` text DEFAULT NULL,
  `justification_for_hire` text DEFAULT NULL,
  `reporting_manager` varchar(100) NOT NULL,
  `recruitment_source` varchar(50) NOT NULL DEFAULT 'internal',
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `requested_by` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_comments` text DEFAULT NULL,
  `hod_approval_signature` text DEFAULT NULL,
  `hod_approval_date` date DEFAULT NULL,
  `hr_approval_signature` text DEFAULT NULL,
  `hr_approval_date` date DEFAULT NULL,
  `finance_approval_signature` text DEFAULT NULL,
  `finance_approval_date` date DEFAULT NULL,
  `md_approval_signature` text DEFAULT NULL,
  `md_approval_date` date DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `approved_salary_range` varchar(255) DEFAULT NULL,
  `hr_recruitment_method` varchar(50) DEFAULT NULL,
  `hr_remarks` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `is_converted_to_job` tinyint(1) NOT NULL DEFAULT 0,
  `converted_job_id` int(11) DEFAULT NULL,
  `converted_at` timestamp NULL DEFAULT NULL,
  `converted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`job_requisition_id`),
  UNIQUE KEY `job_requisitions_requisition_number_unique` (`requisition_number`),
  KEY `job_requisitions_requested_by_foreign` (`requested_by`),
  KEY `job_requisitions_approved_by_foreign` (`approved_by`),
  KEY `job_requisitions_converted_by_foreign` (`converted_by`),
  KEY `job_requisitions_company_id_foreign` (`company_id`),
  CONSTRAINT `job_requisitions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_requisitions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_requisitions_converted_by_foreign` FOREIGN KEY (`converted_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_requisitions_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.job_requisitions: ~0 rows (approximately)
DELETE FROM `job_requisitions`;

-- Dumping structure for table stawi_self_client.leave_adjustments
DROP TABLE IF EXISTS `leave_adjustments`;
CREATE TABLE IF NOT EXISTS `leave_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `leave_application_id` int(10) unsigned DEFAULT NULL,
  `adjustment_days` decimal(8,2) NOT NULL,
  `reason` varchar(191) NOT NULL,
  `adjustment_date` date NOT NULL,
  `adjusted_by` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `financial_year_id` bigint(20) unsigned DEFAULT NULL,
  `adjustment_type` varchar(191) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_adjustments_employee_id_index` (`employee_id`),
  KEY `leave_adjustments_leave_type_id_index` (`leave_type_id`),
  KEY `leave_adjustments_leave_application_id_index` (`leave_application_id`),
  KEY `leave_adjustments_adjusted_by_index` (`adjusted_by`),
  KEY `leave_adjustments_company_id_foreign` (`company_id`),
  KEY `leave_adjustments_financial_year_id_index` (`financial_year_id`),
  KEY `leave_adjustments_status_index` (`status`),
  CONSTRAINT `leave_adjustments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leave_adjustments: ~0 rows (approximately)
DELETE FROM `leave_adjustments`;

-- Dumping structure for table stawi_self_client.leave_application
DROP TABLE IF EXISTS `leave_application`;
CREATE TABLE IF NOT EXISTS `leave_application` (
  `leave_application_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `application_from_date` date NOT NULL,
  `application_to_date` date NOT NULL,
  `application_date` date NOT NULL,
  `number_of_day` int(11) NOT NULL,
  `is_half_day` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indicates if this is a half-day leave',
  `approve_date` date DEFAULT NULL,
  `reject_date` date DEFAULT NULL,
  `approve_by` int(11) DEFAULT NULL,
  `reject_by` int(11) DEFAULT NULL,
  `purpose` varchar(191) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT '1' COMMENT 'status(1,2,3) = Pending,Approve,Reject',
  `application_type` varchar(191) NOT NULL DEFAULT 'self' COMMENT 'self, manual_entry, manual_upload',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `hr_approval` varchar(191) DEFAULT NULL,
  `hr_approval_date` date DEFAULT NULL,
  `final_status` varchar(191) NOT NULL DEFAULT '1',
  `ceo_approval_date` date DEFAULT NULL,
  `ceo_approval_type` varchar(191) DEFAULT NULL,
  `ceo_approval_comments` varchar(191) DEFAULT NULL,
  `hr_approval_comments` varchar(191) DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `financial_year_id` bigint(20) unsigned DEFAULT NULL,
  `reliever_ack` int(11) NOT NULL DEFAULT 0,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`leave_application_id`),
  KEY `leave_application_location_id_foreign` (`location_id`),
  KEY `leave_application_financial_year_id_foreign` (`financial_year_id`),
  KEY `leave_application_company_id_foreign` (`company_id`),
  CONSTRAINT `leave_application_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_application_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`),
  CONSTRAINT `leave_application_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leave_application: ~0 rows (approximately)
DELETE FROM `leave_application`;

-- Dumping structure for table stawi_self_client.leave_group_settings
DROP TABLE IF EXISTS `leave_group_settings`;
CREATE TABLE IF NOT EXISTS `leave_group_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `leave_group_id` bigint(20) unsigned NOT NULL,
  `annual_entitlement` int(10) unsigned NOT NULL DEFAULT 0,
  `carryover_days` int(10) unsigned NOT NULL DEFAULT 0,
  `max_carryover_days` int(10) unsigned DEFAULT NULL,
  `earning_rate` double NOT NULL DEFAULT 0,
  `gender` enum('male','female','all') NOT NULL DEFAULT 'all',
  `probation_period_days` int(10) unsigned NOT NULL DEFAULT 0,
  `notice_period_days` int(10) unsigned NOT NULL DEFAULT 0,
  `allow_half_day` tinyint(1) NOT NULL DEFAULT 0,
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `accrual_frequency` varchar(191) NOT NULL DEFAULT 'once',
  `applicable_on` enum('calendar_days','working_days') NOT NULL DEFAULT 'calendar_days',
  `max_consecutive_days` int(10) unsigned DEFAULT NULL,
  `allow_advanced_leave` tinyint(1) NOT NULL DEFAULT 0,
  `advanced_period_months` int(11) NOT NULL DEFAULT 1,
  `advanced_limit_days` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_group_settings_leave_group_id_leave_type_id_unique` (`leave_group_id`,`leave_type_id`),
  KEY `leave_group_settings_company_id_foreign` (`company_id`),
  CONSTRAINT `leave_group_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leave_group_settings: ~0 rows (approximately)
DELETE FROM `leave_group_settings`;
INSERT INTO `leave_group_settings` (`id`, `leave_type_id`, `leave_group_id`, `annual_entitlement`, `carryover_days`, `max_carryover_days`, `earning_rate`, `gender`, `probation_period_days`, `notice_period_days`, `allow_half_day`, `paid`, `accrual_frequency`, `applicable_on`, `max_consecutive_days`, `allow_advanced_leave`, `advanced_period_months`, `advanced_limit_days`, `created_at`, `updated_at`, `active`, `approval_status`, `date_approved`, `status`, `approved_by`, `company_id`) VALUES
	(1, 1, 1, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(2, 1, 2, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(3, 2, 1, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(4, 2, 2, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(5, 5, 2, 20, 0, NULL, 1, 'female', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(6, 6, 1, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(7, 6, 2, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(8, 4, 1, 20, 0, NULL, 1, 'male', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(9, 3, 1, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(10, 3, 2, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(11, 7, 1, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL),
	(12, 7, 2, 20, 0, NULL, 1, 'all', 0, 0, 0, 1, 'once', 'calendar_days', NULL, 0, 1, NULL, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 1, 0, NULL, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.leave_groups
DROP TABLE IF EXISTS `leave_groups`;
CREATE TABLE IF NOT EXISTS `leave_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_groups_name_unique` (`name`),
  KEY `leave_groups_company_id_foreign` (`company_id`),
  CONSTRAINT `leave_groups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leave_groups: ~0 rows (approximately)
DELETE FROM `leave_groups`;
INSERT INTO `leave_groups` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `approval_status`, `date_approved`, `status`, `approved_by`, `company_id`) VALUES
	(1, 'Male Group', 'Leave group for male employees', 1, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 0, NULL, NULL, NULL, NULL),
	(2, 'Female Group', 'Leave group for female employees', 1, '2026-05-19 15:07:02', '2026-05-19 15:07:02', 0, NULL, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.leave_justifications
DROP TABLE IF EXISTS `leave_justifications`;
CREATE TABLE IF NOT EXISTS `leave_justifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `leave_application_id` int(11) NOT NULL,
  `file_name` varchar(191) DEFAULT NULL,
  `file_url` varchar(191) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_justifications_company_id_foreign` (`company_id`),
  CONSTRAINT `leave_justifications_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leave_justifications: ~0 rows (approximately)
DELETE FROM `leave_justifications`;

-- Dumping structure for table stawi_self_client.leave_region_approvers
DROP TABLE IF EXISTS `leave_region_approvers`;
CREATE TABLE IF NOT EXISTS `leave_region_approvers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `region_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_region_approvers_employee_id_region_id_unique` (`employee_id`,`region_id`),
  KEY `leave_region_approvers_region_id_foreign` (`region_id`),
  KEY `leave_region_approvers_company_id_foreign` (`company_id`),
  CONSTRAINT `leave_region_approvers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_region_approvers_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leave_region_approvers: ~0 rows (approximately)
DELETE FROM `leave_region_approvers`;

-- Dumping structure for table stawi_self_client.leave_rollovers
DROP TABLE IF EXISTS `leave_rollovers`;
CREATE TABLE IF NOT EXISTS `leave_rollovers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `default_rollover` varchar(191) DEFAULT NULL,
  `days_requested` varchar(191) DEFAULT NULL,
  `supervisor_approval` varchar(191) NOT NULL DEFAULT '1',
  `hr_approval` varchar(191) NOT NULL DEFAULT '1',
  `ceo_approval` varchar(191) NOT NULL DEFAULT '1',
  `final_status` varchar(191) NOT NULL DEFAULT '1',
  `date_approved` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `leave_type_id` bigint(20) unsigned DEFAULT NULL,
  `financial_year_id` bigint(20) unsigned DEFAULT NULL,
  `previous_financial_year_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_rollovers_employee_id_foreign` (`employee_id`),
  KEY `leave_rollovers_location_id_foreign` (`location_id`),
  KEY `leave_rollovers_company_id_foreign` (`company_id`),
  KEY `leave_rollovers_previous_financial_year_id_foreign` (`previous_financial_year_id`),
  CONSTRAINT `leave_rollovers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_rollovers_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON UPDATE CASCADE,
  CONSTRAINT `leave_rollovers_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  CONSTRAINT `leave_rollovers_previous_financial_year_id_foreign` FOREIGN KEY (`previous_financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leave_rollovers: ~0 rows (approximately)
DELETE FROM `leave_rollovers`;

-- Dumping structure for table stawi_self_client.leave_schedules
DROP TABLE IF EXISTS `leave_schedules`;
CREATE TABLE IF NOT EXISTS `leave_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `scheduled_from_date` date NOT NULL,
  `scheduled_to_date` date NOT NULL,
  `number_of_days` int(11) NOT NULL DEFAULT 0,
  `purpose` text DEFAULT NULL,
  `status` enum('scheduled','applied','cancelled','completed') NOT NULL DEFAULT 'scheduled',
  `notification_sent` tinyint(1) NOT NULL DEFAULT 0,
  `notification_sent_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_schedules_employee_id_index` (`employee_id`),
  KEY `leave_schedules_leave_type_id_index` (`leave_type_id`),
  KEY `leave_schedules_scheduled_from_date_index` (`scheduled_from_date`),
  KEY `leave_schedules_status_index` (`status`),
  CONSTRAINT `leave_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `leave_schedules_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_type` (`leave_type_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leave_schedules: ~0 rows (approximately)
DELETE FROM `leave_schedules`;

-- Dumping structure for table stawi_self_client.leave_type
DROP TABLE IF EXISTS `leave_type`;
CREATE TABLE IF NOT EXISTS `leave_type` (
  `leave_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `leave_type_name` varchar(191) NOT NULL,
  `num_of_day` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`leave_type_id`),
  UNIQUE KEY `leave_type_leave_type_name_unique` (`leave_type_name`),
  KEY `leave_type_location_id_foreign` (`location_id`),
  KEY `leave_type_company_id_foreign` (`company_id`),
  CONSTRAINT `leave_type_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_type_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leave_type: ~0 rows (approximately)
DELETE FROM `leave_type`;
INSERT INTO `leave_type` (`leave_type_id`, `leave_type_name`, `num_of_day`, `created_at`, `updated_at`, `deleted_at`, `status`, `location_id`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, 'Annual Leave', 0, '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL),
	(2, 'Casual Leave', 22, '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL),
	(3, 'Sick Leave', 20, '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL),
	(4, 'Paternity Leave', 20, '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL),
	(5, 'Maternity Leave', 20, '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL),
	(6, 'Off Day', 20, '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL),
	(7, 'Training', 20, '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.leavers_and_joiners
DROP TABLE IF EXISTS `leavers_and_joiners`;
CREATE TABLE IF NOT EXISTS `leavers_and_joiners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(191) DEFAULT NULL,
  `payroll_number` varchar(191) DEFAULT NULL,
  `national_id` varchar(191) DEFAULT NULL,
  `first_name` varchar(191) DEFAULT NULL,
  `middle_name` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `date_of_movement` date DEFAULT NULL,
  `date_approved` date DEFAULT NULL,
  `approval_status` int(11) NOT NULL DEFAULT 0 COMMENT '0-pending, 1-approved, 2-send_for_amends, 3-rejected',
  `movement_type` varchar(191) NOT NULL COMMENT 'leaving, joining',
  `reason` varchar(191) NOT NULL COMMENT 'For leaving -Resignation ,Temporary Layoff, Retrenchment, Retirement; for joining-Permanent employment, temporary employment, contract employment',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `stage1_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage2_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage3_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage1_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage2_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage3_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage1_approval_comments` varchar(191) DEFAULT NULL,
  `stage2_approval_comments` varchar(191) DEFAULT NULL,
  `stage3_approval_comments` varchar(191) DEFAULT NULL,
  `stage1_approval_date` datetime DEFAULT NULL,
  `stage2_approval_date` datetime DEFAULT NULL,
  `stage3_approval_date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leavers_and_joiners_created_by_foreign` (`created_by`),
  KEY `leavers_and_joiners_stage1_approved_by_foreign` (`stage1_approved_by`),
  KEY `leavers_and_joiners_stage2_approved_by_foreign` (`stage2_approved_by`),
  KEY `leavers_and_joiners_stage3_approved_by_foreign` (`stage3_approved_by`),
  KEY `leavers_and_joiners_location_id_foreign` (`location_id`),
  KEY `leavers_and_joiners_company_id_foreign` (`company_id`),
  CONSTRAINT `leavers_and_joiners_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leavers_and_joiners_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `leavers_and_joiners_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  CONSTRAINT `leavers_and_joiners_stage1_approved_by_foreign` FOREIGN KEY (`stage1_approved_by`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `leavers_and_joiners_stage2_approved_by_foreign` FOREIGN KEY (`stage2_approved_by`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `leavers_and_joiners_stage3_approved_by_foreign` FOREIGN KEY (`stage3_approved_by`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.leavers_and_joiners: ~0 rows (approximately)
DELETE FROM `leavers_and_joiners`;

-- Dumping structure for table stawi_self_client.loan_applications
DROP TABLE IF EXISTS `loan_applications`;
CREATE TABLE IF NOT EXISTS `loan_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `loan_type_id` bigint(20) unsigned NOT NULL,
  `amount_requested` decimal(15,2) NOT NULL,
  `duration_months` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `approval_comments` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=pending,1=approved,2=rejected',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  `amount_approved` decimal(15,2) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.loan_applications: ~0 rows (approximately)
DELETE FROM `loan_applications`;

-- Dumping structure for table stawi_self_client.loan_deductions
DROP TABLE IF EXISTS `loan_deductions`;
CREATE TABLE IF NOT EXISTS `loan_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `payroll_period_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `deduction_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.loan_deductions: ~0 rows (approximately)
DELETE FROM `loan_deductions`;

-- Dumping structure for table stawi_self_client.loan_types
DROP TABLE IF EXISTS `loan_types`;
CREATE TABLE IF NOT EXISTS `loan_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `max_amount` decimal(15,2) DEFAULT NULL,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `max_duration_months` int(11) NOT NULL DEFAULT 12,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0=inactive,1=active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.loan_types: ~0 rows (approximately)
DELETE FROM `loan_types`;

-- Dumping structure for table stawi_self_client.loans
DROP TABLE IF EXISTS `loans`;
CREATE TABLE IF NOT EXISTS `loans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `loan_type_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `duration_months` int(11) NOT NULL,
  `monthly_installment` decimal(15,2) NOT NULL,
  `total_repayable` decimal(15,2) NOT NULL,
  `balance` decimal(15,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `justification` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=inactive,1=active,2=suspended',
  `approval_status` tinyint(4) NOT NULL DEFAULT -1 COMMENT '-1=draft,0=pending,1=approved,2=rejected,3=cancelled',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `date_approved` date DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.loans: ~0 rows (approximately)
DELETE FROM `loans`;

-- Dumping structure for table stawi_self_client.location
DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `location_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` bigint(20) unsigned DEFAULT NULL,
  `location_name` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `address` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `location_location_name_unique` (`location_name`),
  KEY `location_manager_id_foreign` (`manager_id`),
  KEY `location_region_id_foreign` (`region_id`),
  KEY `location_company_id_foreign` (`company_id`),
  CONSTRAINT `location_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `location_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employee` (`employee_id`),
  CONSTRAINT `location_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.location: ~0 rows (approximately)
DELETE FROM `location`;
INSERT INTO `location` (`location_id`, `region_id`, `location_name`, `created_at`, `updated_at`, `deleted_at`, `status`, `address`, `phone`, `email`, `manager_id`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, NULL, 'Nairobi', '2026-05-19 15:07:02', '2026-05-19 15:07:02', NULL, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.location_permissions
DROP TABLE IF EXISTS `location_permissions`;
CREATE TABLE IF NOT EXISTS `location_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `permission_name` varchar(191) NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `module_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `location_permissions_user_id_foreign` (`user_id`),
  KEY `location_permissions_permission_id_foreign` (`permission_name`),
  KEY `location_permissions_location_id_foreign` (`location_id`),
  KEY `location_permissions_module_id_foreign` (`module_id`),
  KEY `location_permissions_company_id_foreign` (`company_id`),
  CONSTRAINT `location_permissions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `location_permissions_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`),
  CONSTRAINT `location_permissions_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `location_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.location_permissions: ~0 rows (approximately)
DELETE FROM `location_permissions`;

-- Dumping structure for table stawi_self_client.logs
DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `log_date` datetime NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `log_type` varchar(50) NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.logs: ~0 rows (approximately)
DELETE FROM `logs`;

-- Dumping structure for table stawi_self_client.lunch_reports
DROP TABLE IF EXISTS `lunch_reports`;
CREATE TABLE IF NOT EXISTS `lunch_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `national_id` varchar(191) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `payroll_number` varchar(191) NOT NULL,
  `first_name` varchar(191) NOT NULL,
  `middle_name` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) NOT NULL,
  `department_id` varchar(191) NOT NULL,
  `date` date NOT NULL,
  `month` varchar(191) NOT NULL,
  `lunch_checkin_time` datetime NOT NULL,
  `sensor_id` varchar(191) NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `employee_type` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lunch_reports_location_id_foreign` (`location_id`),
  KEY `lunch_reports_company_id_foreign` (`company_id`),
  CONSTRAINT `lunch_reports_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lunch_reports_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.lunch_reports: ~0 rows (approximately)
DELETE FROM `lunch_reports`;

-- Dumping structure for table stawi_self_client.manual_loan_deductions
DROP TABLE IF EXISTS `manual_loan_deductions`;
CREATE TABLE IF NOT EXISTS `manual_loan_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `deduction_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.manual_loan_deductions: ~0 rows (approximately)
DELETE FROM `manual_loan_deductions`;

-- Dumping structure for table stawi_self_client.menu_permission
DROP TABLE IF EXISTS `menu_permission`;
CREATE TABLE IF NOT EXISTS `menu_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_permission_location_id_foreign` (`location_id`),
  KEY `menu_permission_company_id_foreign` (`company_id`),
  CONSTRAINT `menu_permission_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `menu_permission_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.menu_permission: ~0 rows (approximately)
DELETE FROM `menu_permission`;

-- Dumping structure for table stawi_self_client.menus
DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `action` int(11) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `menu_url` varchar(191) DEFAULT NULL,
  `module_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menus_location_id_foreign` (`location_id`),
  KEY `menus_company_id_foreign` (`company_id`),
  CONSTRAINT `menus_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `menus_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.menus: ~0 rows (approximately)
DELETE FROM `menus`;

-- Dumping structure for table stawi_self_client.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `migrations_location_id_foreign` (`location_id`),
  CONSTRAINT `migrations_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=442 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.migrations: ~441 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`, `location_id`) VALUES
	(1, '2014_10_12_100000_create_password_resets_table', 1, NULL),
	(2, '2017_09_09_085518_MenuPermissionMigration', 1, NULL),
	(3, '2017_09_10_080607_create_menus_table', 1, NULL),
	(4, '2017_09_19_030632_create_departments_table', 1, NULL),
	(5, '2017_09_19_043154_create_designations_table', 1, NULL),
	(6, '2017_09_19_053209_create_employees_table', 1, NULL),
	(7, '2017_09_19_060623_create_employee_experiences_table', 1, NULL),
	(8, '2017_09_19_062907_create_employee_education_qualifications_table', 1, NULL),
	(9, '2017_09_1_000000_create_users_table', 1, NULL),
	(10, '2017_09_27_033248_create_branches_table', 1, NULL),
	(11, '2017_09_2_081056_create_modules_table', 1, NULL),
	(12, '2017_10_02_042807_create_holidays_table', 1, NULL),
	(13, '2017_10_04_035502_create_holiday_details_table', 1, NULL),
	(14, '2017_10_04_050224_create_weekly_holidays_table', 1, NULL),
	(15, '2017_10_04_050517_create_leave_types_table', 1, NULL),
	(16, '2017_10_04_093455_create_leave_applications_table', 1, NULL),
	(17, '2017_10_05_094341_create_SP_weekly_holiday_store_procedure', 1, NULL),
	(18, '2017_10_05_095235_create_SP_get_holiday_store_procedure', 1, NULL),
	(19, '2017_10_05_095429_create_SP_get_employee_leave_balance_store_procedure', 1, NULL),
	(20, '2017_10_09_043228_create_work_shifts_table', 1, NULL),
	(21, '2017_10_11_051354_create_SP_daily_attendance_store_procedure', 1, NULL),
	(22, '2017_10_11_083952_create_SP_monthly_attendance_store_procedure', 1, NULL),
	(23, '2017_10_11_084031_create_allownce_table', 1, NULL),
	(24, '2017_10_11_084043_create_deduction_table', 1, NULL),
	(25, '2017_10_26_064948_create_tax_rules_table', 1, NULL),
	(26, '2017_10_30_065329_create_SP_get_employee_info_store_procedure', 1, NULL),
	(27, '2017_11_01_045130_create_salary_deduction_for_late_attendances_table', 1, NULL),
	(28, '2017_11_02_051338_create_salary_details_table', 1, NULL),
	(29, '2017_11_02_053649_create_salary_details_to_allowances_table', 1, NULL),
	(30, '2017_11_02_054000_create_salary_details_to_deductions_table', 1, NULL),
	(31, '2017_11_14_061231_create_earn_leave_rules_table', 1, NULL),
	(32, '2017_11_14_092829_create_company_address_settings_table', 1, NULL),
	(33, '2017_11_15_090514_create_employee_awards_table', 1, NULL),
	(34, '2017_11_15_105135_create_notices_table', 1, NULL),
	(35, '2017_11_23_102429_create_print_head_settings_table', 1, NULL),
	(36, '2017_12_03_112226_create_training_types_table', 1, NULL),
	(37, '2017_12_03_112805_create_training_infos_table', 1, NULL),
	(38, '2017_12_04_114921_create_warnings_table', 1, NULL),
	(39, '2017_12_04_140839_create_terminations_table', 1, NULL),
	(40, '2017_12_05_154824_create_promotions_table', 1, NULL),
	(41, '2017_12_10_122540_create_hourly_salaries_table', 1, NULL),
	(42, '2017_12_13_144211_create_jobs_table', 1, NULL),
	(43, '2017_12_13_144259_create_job_applicants_table', 1, NULL),
	(44, '2017_12_13_144320_create_interviews_table', 1, NULL),
	(45, '2017_12_31_222850_create_salary_details_to_leaves_table', 1, NULL),
	(46, '2018_01_08_144502_create_employee_attendance_approves_table', 1, NULL),
	(47, '2018_01_10_150238_create_bonus_settings_table', 1, NULL),
	(48, '2018_01_10_161034_create_employee_bonuses_table', 1, NULL),
	(49, '2019_08_19_000000_create_failed_jobs_table', 1, NULL),
	(50, '2019_12_14_000001_create_personal_access_tokens_table', 1, NULL),
	(51, '2020_05_11_181701_add_hr_approved_to_leave_applications_table', 1, NULL),
	(52, '2020_05_11_182112_add_hr_approved_date_to_leave_applications_table', 1, NULL),
	(53, '2020_05_11_201152_add_final_status_to_leave_applications_table', 1, NULL),
	(54, '2020_05_13_102117_add_password_changed_date_to_users_table', 1, NULL),
	(55, '2020_05_20_021438_add_ceo_approval_to_leaveapplications_table', 1, NULL),
	(56, '2020_05_20_124147_add_hr_approval_comments_to_leave_application_table', 1, NULL),
	(57, '2020_05_22_181157_create_leave_rollovers_table', 1, NULL),
	(58, '2020_05_22_183824_add_employee_id_contraint_on_leave_rollover_table', 1, NULL),
	(59, '2020_07_18_212110_create_ip_settings_table', 1, NULL),
	(60, '2020_07_18_212205_create_white_listed_ips_table', 1, NULL),
	(61, '2020_09_21_065536_create_services_table', 1, NULL),
	(62, '2020_09_23_082756_create_front_settings_table', 1, NULL),
	(63, '2020_10_11_023523_add_payrol_info_to_employees_table', 1, NULL),
	(64, '2020_10_25_043453_create_n_h_i_fs_table', 1, NULL),
	(65, '2020_10_26_161203_add_more_fields_to_salary_details_table', 1, NULL),
	(66, '2020_11_10_012706_add_statutories_to_salary_details_table', 1, NULL),
	(67, '2020_11_20_100001_create_log_table', 1, NULL),
	(68, '2020_11_22_025215_add_nssfamount_toSalary_details_table', 1, NULL),
	(69, '2020_11_22_032422_add_no_of_holidays_worked_to_salary_details_table', 1, NULL),
	(70, '2020_11_23_140852_add_department_id_to_salary_details_table', 1, NULL),
	(71, '2020_11_26_163426_create_salary_bonuses_table', 1, NULL),
	(72, '2020_11_26_164545_create_salary_bonus_types_table', 1, NULL),
	(73, '2020_11_26_172011_salary_details_to_bonuses_table', 1, NULL),
	(74, '2020_11_26_221200_add_allowance_fields_to_salary_details_table', 1, NULL),
	(75, '2020_12_02_183117_addBonusName_to_salary_details_to_bonuses_table', 1, NULL),
	(76, '2021_01_29_071233_add_nssf_rate_types_to_Employees_table', 1, NULL),
	(77, '2021_01_29_085157_add_ssf_tiers_to_salary_details_table', 1, NULL),
	(78, '2021_03_01_140248_add_untaxed_artime_to_salary_details_table', 1, NULL),
	(79, '2021_07_30_155919_create_teams_table', 1, NULL),
	(80, '2021_10_26_174546_create_absentees_table', 1, NULL),
	(81, '2021_11_27_131246_add_status_to_leave_type_table', 1, NULL),
	(82, '2022_01_13_153412_create_paryroll9s_table', 1, NULL),
	(83, '2022_03_10_200211_create_permission_tables', 1, NULL),
	(84, '2022_03_27_111049_create_attendances_table', 1, NULL),
	(85, '2022_10_18_222216_add_lunch_check_in_to_attendance_table', 1, NULL),
	(86, '2022_10_18_225722_create_biometric_devices_table', 1, NULL),
	(87, '2022_10_30_004813_create_morpho_devices_table', 1, NULL),
	(88, '2022_10_30_004827_create_morpho_device_logs_table', 1, NULL),
	(89, '2022_11_03_090128_create_activity_log_table', 1, NULL),
	(90, '2022_11_03_090129_add_event_column_to_activity_log_table', 1, NULL),
	(91, '2022_11_03_090130_add_batch_uuid_column_to_activity_log_table', 1, NULL),
	(92, '2022_11_03_125235_create_biometric_run_logs_table', 1, NULL),
	(93, '2022_11_27_162917_create_app_licenses_table', 1, NULL),
	(94, '2023_03_19_025620_add_financial_year_end_to_settings_table', 1, NULL),
	(95, '2023_03_20_162916_create_employee_to_deductions_table', 1, NULL),
	(96, '2023_03_25_005854_add_overtime_count_time_to_workshifts', 1, NULL),
	(97, '2023_03_25_013115_create_work_shift_to_employee_table', 1, NULL),
	(98, '2023_03_25_022226_create_new_employee_to_deductions_table', 1, NULL),
	(99, '2023_03_25_022648_create_recurrent_deductions_table', 1, NULL),
	(100, '2023_03_25_143357_add_module_to_permissions_table', 1, NULL),
	(101, '2023_03_25_144959_create_module_tble', 1, NULL),
	(102, '2023_03_26_194419_add_midle_name_to_employee_table', 1, NULL),
	(103, '2023_03_29_230340_add_entry_type_to_attendance_table', 1, NULL),
	(104, '2023_03_29_233610_create_employee_types_table', 1, NULL),
	(105, '2023_03_29_235852_add_employee_type_to_employees_table', 1, NULL),
	(106, '2023_03_30_001444_add_employee_type_to_attendances_table', 1, NULL),
	(107, '2023_04_01_121938_create_employee_sections_table', 1, NULL),
	(108, '2023_04_01_122319_create_employee_groups_table', 1, NULL),
	(109, '2023_04_01_125315_create_approvals_table', 1, NULL),
	(110, '2023_04_02_153253_add_section_and_groups_to_employee_table', 1, NULL),
	(111, '2023_04_08_181410_add_device_type_to_morphodevices', 1, NULL),
	(112, '2023_04_10_003547_add_tea_checkin_to_attendances_table', 1, NULL),
	(113, '2023_04_16_173546_add_section_id_to_attendances_table', 1, NULL),
	(114, '2023_04_23_090114_create_leavers_and_joiners_table', 1, NULL),
	(115, '2023_04_29_150615_add_more_fields_to_terminations_table', 1, NULL),
	(116, '2023_04_29_181646_add_employment_type_to_employee_table--table=employee', 1, NULL),
	(117, '2023_04_29_195150_create_employee_movements_table', 1, NULL),
	(118, '2023_05_02_010834_create_overtime_approvals_table', 1, NULL),
	(119, '2023_05_06_130239_add_approved_overtime_on_attendaces_table', 1, NULL),
	(120, '2023_05_10_222858_create_lunch_reports_table', 1, NULL),
	(121, '2023_05_15_205319_create_employee_documents_table', 1, NULL),
	(122, '2023_05_17_150201_create_error_logs_table', 1, NULL),
	(123, '2023_05_18_191324_add_uuid_to_employee_documents_table', 1, NULL),
	(124, '2023_06_07_142326_add_deleted_column_to_all_tables', 1, NULL),
	(125, '2023_06_16_183419_add_statuses_to_all_tables', 1, NULL),
	(126, '2023_06_20_195338_add_payroll_number_to_attendances_table', 1, NULL),
	(127, '2023_06_20_200804_make_national_id_nullable_on_attendances', 1, NULL),
	(128, '2023_06_30_143954_change_id_no_to_nullable_in_logs', 1, NULL),
	(129, '2023_06_30_152052_add_status_to_roles_table', 1, NULL),
	(130, '2023_07_05_142800_make_nullable_date_of_movement_on_joiners_leavers', 1, NULL),
	(131, '2023_07_21_005324_add_first_name_to_user_table', 1, NULL),
	(132, '2023_08_19_091418_create_payroll_adjustments_table', 1, NULL),
	(133, '2023_08_19_134319_create_salary_deductions_table', 1, NULL),
	(134, '2023_08_19_135820_add_ahl_to_salaries_table', 1, NULL),
	(135, '2023_08_22_175728_remote_amount_of_tax_on_tax_rule', 1, NULL),
	(136, '2023_12_13_212610_create_company_settings_table', 1, NULL),
	(137, '2023_12_21_074523_add_more_details_to_payroll_table', 1, NULL),
	(138, '2024_01_05_020720_create_attendance_locations_table', 1, NULL),
	(139, '2024_01_26_203852_add_more_details_to_employee_table', 1, NULL),
	(140, '2024_01_29_212911_create_employee_payroll_profiles_table', 1, NULL),
	(141, '2024_08_05_120251_create_payout_channels_table', 1, NULL),
	(142, '2024_08_05_131851_create_staff_contracts_table', 1, NULL),
	(143, '2024_09_28_191020_add_nationality_to_employee', 1, NULL),
	(144, '2024_10_03_154910_add_hire_date_to_job_applicants', 1, NULL),
	(145, '2024_10_16_104123_add_shif_rate_to_salary_details', 1, NULL),
	(146, '2024_10_20_094932_add_shif_number_to_employee_table', 1, NULL),
	(147, '2024_11_23_180052_create_grouped_menu_route_permissions_table', 1, NULL),
	(148, '2024_12_06_020731_add_location_id_to_tables', 1, NULL),
	(149, '2024_12_06_033320_create_location_permissions', 1, NULL),
	(150, '2024_12_10_175934_create_approval_records_table', 1, NULL),
	(151, '2024_12_11_113716_add_more_details_to_branch', 1, NULL),
	(152, '2024_12_11_124329_create_approval_settings_table', 1, NULL),
	(153, '2024_12_11_124850_add_module_id_to_branch_permissions_table', 1, NULL),
	(154, '2024_12_11_182556_create_module_departments_table', 1, NULL),
	(155, '2024_12_12_100300_add_branch_id_and_module_id_to_model_has_permissions_table', 1, NULL),
	(156, '2024_12_12_104633_change_type_and_name_on_branch_permissions', 1, NULL),
	(157, '2024_12_12_105529_change_type_and_on_branch_permissions', 1, NULL),
	(158, '2024_12_12_151432_add_stages_to_approval_records_table', 1, NULL),
	(159, '2024_12_12_172529_add_requested_by_in_approval_settings_table', 1, NULL),
	(160, '2024_12_12_195050_add_approver_id_to_approval_records_table', 1, NULL),
	(161, '2024_12_13_164805_rename_shifrate_to_shif_amount_on_payroll', 1, NULL),
	(162, '2024_12_13_185737_change_percenta_on_tax_rule_to_accept_decimals', 1, NULL),
	(163, '2024_12_13_192043_change_approver_id_in_approval_records_table', 1, NULL),
	(164, '2024_12_14_190640_moneyfieldstodouble', 1, NULL),
	(165, '2024_12_15_014312_add_already_approved_user_id_to_approval_records_table', 1, NULL),
	(166, '2024_12_19_183310_create_approval_query_logs_table', 1, NULL),
	(167, '2024_12_20_004204_create_feedback_categories_table', 1, NULL),
	(168, '2024_12_20_004307_create_employee_feedback_table', 1, NULL),
	(169, '2024_12_20_004337_create_employee_feedback_responses_table', 1, NULL),
	(170, '2024_12_21_134946_add_route_name_to_approval_records_table', 1, NULL),
	(171, '2024_12_21_181753_create_anonymous_feedback_table', 1, NULL),
	(172, '2024_12_21_190330_add_approval_record_to_querylog', 1, NULL),
	(173, '2024_12_21_194959_make_category_name_unique', 1, NULL),
	(174, '2024_12_21_230127_add_category_id_to_employee_feedback', 1, NULL),
	(175, '2024_12_22_013646_add_title_to_anonymous_feedback', 1, NULL),
	(176, '2024_12_22_030013_add_category_relation_to_anonymous_feedback', 1, NULL),
	(177, '2024_12_24_122019_create_disciplinary_categories_table', 1, NULL),
	(178, '2024_12_24_123903_create_disciplinary_cases_table', 1, NULL),
	(179, '2024_12_24_123951_create_discplinary_case_actions_table', 1, NULL),
	(180, '2024_12_25_212008_add_status_to_disciplinary_case_categories', 1, NULL),
	(181, '2024_12_26_152844_add_location_id_to_disciplinary_cases', 1, NULL),
	(182, '2024_12_26_171643_rename_description_to_remarks_on_disciplinary_action', 1, NULL),
	(183, '2024_12_26_175031_add_closed_date_to_disciplinary_actions', 1, NULL),
	(184, '2024_12_27_035131_create_approval_setting_approvers_table', 1, NULL),
	(185, '2024_12_27_060824_create_approval_requests_table', 1, NULL),
	(186, '2024_12_27_060835_create_approval_request_approvals_table', 1, NULL),
	(187, '2024_12_27_074242_create_approval_request_db_queries_table', 1, NULL),
	(188, '2024_12_28_160531_addexpirationdatetopersonaltoken', 1, NULL),
	(189, '2024_12_28_163552_create_route_menu_section_groupings_table', 1, NULL),
	(190, '2024_12_30_211705_add_changes_to_approval_request_db_queries', 1, NULL),
	(191, '2025_01_02_032543_add_uri_to_approval_requests', 1, NULL),
	(192, '2025_01_07_003220_create_document_categories_table', 1, NULL),
	(193, '2025_01_07_034109_create_hr_documents_table', 1, NULL),
	(194, '2025_01_09_090328_create_document_views_table', 1, NULL),
	(195, '2025_01_09_130239_enhance_training_type', 1, NULL),
	(196, '2025_01_09_130430_create_training_facilitators_table', 1, NULL),
	(197, '2025_01_09_132256_create_trainings_table', 1, NULL),
	(198, '2025_01_10_125118_add_file_hash_to_hr_documents_table', 1, NULL),
	(199, '2025_01_10_133324_change_approved_by_in_hr_documents_table', 1, NULL),
	(200, '2025_01_11_155749_create_training_attendants_table', 1, NULL),
	(201, '2025_01_11_155845_create_training_invitees_table', 1, NULL),
	(202, '2025_01_16_053351_additioanl_employee_fields', 1, NULL),
	(203, '2025_01_16_054836_create_programs_table', 1, NULL),
	(204, '2025_01_16_123246_add_payout_channel_id_to_employee_table', 1, NULL),
	(205, '2025_01_16_135448_create_employee_payout_channels_table', 1, NULL),
	(206, '2025_01_17_155417_create_job_requisitions_table', 1, NULL),
	(207, '2025_01_17_194514_create_program_employees_table', 1, NULL),
	(208, '2025_01_18_100046_add_branch_code_to_employee_payout_channels_table', 1, NULL),
	(209, '2025_01_21_113618_create_surveys_table', 1, NULL),
	(210, '2025_01_22_044725_add_columns_to_surveys_table', 1, NULL),
	(211, '2025_01_22_050056_create_survey_questions_table', 1, NULL),
	(212, '2025_01_22_050214_create_survey_answers_table', 1, NULL),
	(213, '2025_01_22_050258_create_employee_survey_responses_table', 1, NULL),
	(214, '2025_01_22_050337_create_survey_response_comments_table', 1, NULL),
	(215, '2025_01_22_154846_add_status_to_surveys_table', 1, NULL),
	(216, '2025_01_23_171605_add_start_date_and_end_date_to_surveys_table', 1, NULL),
	(217, '2025_01_23_194730_add_passing_year_to_employee_education_qualification_table', 1, NULL),
	(218, '2025_01_24_091129_add_answer_type_to_survey_questions_table', 1, NULL),
	(219, '2025_01_26_194309_make_employee_id_nullable_to_employee_survey_responses_table', 1, NULL),
	(220, '2025_01_27_150835_add_certificate_to_employee_education_qualification_table', 1, NULL),
	(221, '2025_01_31_070541_offboarding_process_table', 1, NULL),
	(222, '2025_02_03_224026_create_termination_checklists_table', 1, NULL),
	(223, '2025_02_04_003515_add_created_by_in_termination_checklists_table', 1, NULL),
	(224, '2025_02_05_000651_change_employee_id_in_termination_checklists_table', 1, NULL),
	(225, '2025_02_05_001252_create_termination_checklist_actions_table', 1, NULL),
	(226, '2025_02_05_215333_add_status_to_termination_checklist_actions_table', 1, NULL),
	(227, '2025_02_06_231026_change_actioned_by_in_termination_checklist_actions_table', 1, NULL),
	(228, '2025_02_10_213502_add_fields_to_align_with_shofco', 1, NULL),
	(229, '2025_02_11_162745_create_financial_year', 1, NULL),
	(230, '2025_02_13_021528_add_ethnicity_to_employee_table', 1, NULL),
	(231, '2025_02_14_160034_add_google_columns_to_user_table', 1, NULL),
	(232, '2025_02_14_224542_create_delivered_sms_table', 1, NULL),
	(233, '2025_02_15_111128_create_verification_tables', 1, NULL),
	(234, '2025_02_15_122336_add_msisdn_to_user_table', 1, NULL),
	(235, '2025_02_15_130435_add_expiry_date_to_verification_codes_table', 1, NULL),
	(236, '2025_02_15_162243_create_pending_passwords_table', 1, NULL),
	(237, '2025_02_15_164932_add_password_to_pending_passwords_table', 1, NULL),
	(238, '2025_02_15_175253_add_password_change_to_verification_codes_table', 1, NULL),
	(239, '2025_02_17_174102_change_verification_codes_table', 1, NULL),
	(240, '2025_02_17_224034_add_password_expiry_to_users_table', 1, NULL),
	(241, '2025_02_23_180545_add_contraint_to_financial_years', 1, NULL),
	(242, '2025_02_27_002450_create_leave_justifications_table', 1, NULL),
	(243, '2025_02_27_043012_change_ethinicity_to_sring_on_employee', 1, NULL),
	(244, '2025_03_09_125236_create_trainings_views_table', 1, NULL),
	(245, '2025_03_10_222633_add_missing_columns_to_employee_table', 1, NULL),
	(246, '2025_03_14_002559_create_leave_groups_table', 1, NULL),
	(247, '2025_03_14_002626_create_leave_group_settings_table', 1, NULL),
	(248, '2025_03_15_111244_create_public_holiday_departments_table', 1, NULL),
	(249, '2025_03_15_111304_create_weekly_holiday_departments_table', 1, NULL),
	(250, '2025_03_21_145440_create_employee_leavegroups_table', 1, NULL),
	(251, '2025_03_25_192706_add_job_location_to_job_posts_table', 1, NULL),
	(252, '2025_03_26_111941_modify_post_column_in_job_posts_table', 1, NULL),
	(253, '2025_03_26_131358_add_audience_type_to_job_posts_table', 1, NULL),
	(254, '2025_03_27_172542_add_jd_file_to_job_posts_table', 1, NULL),
	(255, '2025_03_28_203234_increase_phone_number_characters', 1, NULL),
	(256, '2025_03_30_150254_create_public_holiday_leave_groups_table', 1, NULL),
	(257, '2025_03_30_150326_create_weekly_holiday_leave_groups_table', 1, NULL),
	(258, '2025_03_30_212730_add_year_and_leavetype_to_roll_over', 1, NULL),
	(259, '2025_04_02_184035_add_emergency_contact_details_to_employee_table', 1, NULL),
	(260, '2025_04_03_190043_make_pay_grade_id_nullable_in_employee_table', 1, NULL),
	(261, '2025_04_06_122159_add_experience_and_qualification_to_applications', 1, NULL),
	(262, '2025_04_07_090548_add_status_to_leave_group-settings', 1, NULL),
	(263, '2025_04_11_190254_create_regions_table', 1, NULL),
	(264, '2025_04_11_190618_add_region_to_branch', 1, NULL),
	(265, '2025_04_11_190853_create_leave_region_approvers_table', 1, NULL),
	(266, '2025_04_12_130251_make_to_date_nullable_in_employee_experience_table', 1, NULL),
	(267, '2025_04_22_111217_add_financial_year_id_back_to_ess_feedback', 1, NULL),
	(268, '2025_04_25_165945_add_columns_to_surveys_table', 1, NULL),
	(269, '2025_05_02_233852_add_updated_at_on_morpho_devise_logs', 1, NULL),
	(270, '2025_05_14_042413_add_employee_id_to__morpo_logs_table', 1, NULL),
	(271, '2025_05_16_112952_make_description_nullable_in_trainings_table', 1, NULL),
	(272, '2025_05_18_173614_create_training_invitees_table', 1, NULL),
	(273, '2025_05_18_223151_rename_columns_for_training_attendants_table', 1, NULL),
	(274, '2025_05_18_235554_update_training_view_columns', 1, NULL),
	(275, '2025_05_19_113211_add_responded_at_and_responded_from_to_training_invitees_table', 1, NULL),
	(276, '2025_05_19_124831_add_responded_at_column_to_training_attendants_table', 1, NULL),
	(277, '2025_05_23_221430_add_biometric_status_to_employee_table', 1, NULL),
	(278, '2025_05_24_004531_add_biometric_capture_details', 1, NULL),
	(279, '2025_05_26_154925_add_job_type_to_job_table', 1, NULL),
	(280, '2025_05_26_231229_drop_job_location_from_job_table', 1, NULL),
	(281, '2025_05_27_124456_add_application_source_to_job_applicant_table', 1, NULL),
	(282, '2025_05_27_210223_add_columns_to_anonymous_feedback', 1, NULL),
	(283, '2025_05_28_150215_add_employee_id_to_job_applicant_table', 1, NULL),
	(284, '2025_05_30_165220_add_bio_time_id_to_employees_table', 1, NULL),
	(285, '2025_06_02_103535_add_start_and_end_time_to_trainings_table', 1, NULL),
	(286, '2025_06_04_003816_change_termination_tobig_int', 1, NULL),
	(287, '2025_06_04_172631_add_manager_id_to_regions_table', 1, NULL),
	(288, '2025_06_04_230853_create_termination_docs_table', 1, NULL),
	(289, '2025_06_04_231548_add_columns_to_terminations', 1, NULL),
	(290, '2025_06_05_100347_create_notifications_table', 1, NULL),
	(291, '2025_06_09_102339_remove_employee_foreign_key_from_leave_region_approvers', 1, NULL),
	(292, '2025_06_09_154810_add_google_access_token_to_user_table', 1, NULL),
	(293, '2025_06_17_144849_add_targeted_gender_to_surveys_table', 1, NULL),
	(294, '2025_06_17_183402_create_survey_departments_table', 1, NULL),
	(295, '2025_06_18_114904_add_columns_to_survey_table', 1, NULL),
	(296, '2025_06_20_120234_create_survey_branches_table', 1, NULL),
	(297, '2025_06_20_123800_create_survey_regions_table', 1, NULL),
	(298, '2025_06_21_131046_remove_survey_branches_table', 1, NULL),
	(299, '2025_06_23_090743_create_survey_locations_table', 1, NULL),
	(300, '2025_07_17_151940_alter_programs_table_change_status_to_integer', 1, NULL),
	(301, '2025_07_19_182908_create_paye_tax_bands_table', 1, NULL),
	(302, '2025_07_21_130400_create_employee_earnings_and_deductions_table', 1, NULL),
	(303, '2025_07_21_161911_add_code_to_programs', 1, NULL),
	(304, '2025_07_21_163735_create_payroll_earning_types_table', 1, NULL),
	(305, '2025_07_21_180338_add_earning_type_to_earning_types', 1, NULL),
	(306, '2025_07_28_100000_create_projects_table', 1, NULL),
	(307, '2025_07_28_113245_add_code_to_projects_table', 1, NULL),
	(308, '2025_07_28_192236_create_projects_to_employee_payroll_allocation_table', 1, NULL),
	(309, '2025_07_28_215700_create_employee_earnings_table', 1, NULL),
	(310, '2025_07_29_023437_add_earning_category_to_employee_earnings', 1, NULL),
	(311, '2025_07_29_183816_add_recurring_to_employee_earnings_and_deductions_table', 1, NULL),
	(312, '2025_07_30_000000_create_employee_deductions_table', 1, NULL),
	(313, '2025_07_30_000000_create_employee_overtimes_table', 1, NULL),
	(314, '2025_07_30_163811_add_financial_year_id_to_employee_earnings_table', 1, NULL),
	(315, '2025_07_30_191557_add_status_column_to_deduction_table', 1, NULL),
	(316, '2025_07_31_133704_add_soft_deletes_to_employee_deductions', 1, NULL),
	(317, '2025_07_31_134721_add_calculation_type_to_employee_deductions', 1, NULL),
	(318, '2025_08_02_000001_create_payroll_configurations_table', 1, NULL),
	(319, '2025_08_02_000002_create_pension_schemes_table', 1, NULL),
	(320, '2025_08_02_000003_create_employee_payrolls_table', 1, NULL),
	(321, '2025_08_02_000004_create_allowance_types_table', 1, NULL),
	(322, '2025_08_02_000005_create_payroll_periods_table', 1, NULL),
	(323, '2025_08_02_000006_create_payroll_records_table', 1, NULL),
	(324, '2025_08_03_033635_create_deduction_types_table', 1, NULL),
	(325, '2025_08_03_203256_add_columns_to_employee_deductions', 1, NULL),
	(326, '2025_08_03_221059_create_payroll_record_details_table', 1, NULL),
	(327, '2025_08_05_093708_add_bank_account_name_to_employee', 1, NULL),
	(328, '2025_08_05_184551_add_contrains_to_payroll_periods', 1, NULL),
	(329, '2025_08_06_000001_create_payroll_claims_table', 1, NULL),
	(330, '2025_08_06_000002_create_payroll_claim_recoveries_table', 1, NULL),
	(331, '2025_08_06_000003_add_claim_recoveries_to_payroll_records', 1, NULL),
	(332, '2025_08_06_140306_change_nullable_amount_on_employee_earning', 1, NULL),
	(333, '2025_08_06_214200_add_overtime_rates_to_employee_payrolls_table', 1, NULL),
	(334, '2025_08_06_215936_add_isdeductale_to_employee_deductions', 1, NULL),
	(335, '2025_08_06_221330_add_phone_and_frequency_to_employee_payrolls_table', 1, NULL),
	(336, '2025_08_07_190312_add_input_period_dates_to_payroll_periods_table', 1, NULL),
	(337, '2025_08_10_215600_add_additional_fields_to_employee_overtimes_table', 1, NULL),
	(338, '2025_08_11_041553_add_colums_to_employee_overtimes', 1, NULL),
	(339, '2025_08_11_045021_add_colums_to_payroll_records', 1, NULL),
	(340, '2025_08_14_102630_add_code_to_deductions', 1, NULL),
	(341, '2025_08_14_111526_add_description_to_deductions', 1, NULL),
	(342, '2025_08_17_034257_create_approval_workflow_table', 1, NULL),
	(343, '2025_08_17_113034_add_approval_columns_to_all_tables', 1, NULL),
	(344, '2025_08_18_220400_add_batch_approval_tracking', 1, NULL),
	(345, '2025_08_25_185849_add_payroll_record_status_to_payroll_records', 1, NULL),
	(346, '2025_08_25_202747_add_created_by_to_payroll_records', 1, NULL),
	(347, '2025_08_27_154637_add_identity_type_to_employee_table', 1, NULL),
	(348, '2025_08_27_160659_add_columns_to_holiday_details', 1, NULL),
	(349, '2025_09_01_190530_change_status_type_on_employee_payroll', 1, NULL),
	(350, '2025_09_02_170735_null_action_date_on_approval_logs', 1, NULL),
	(351, '2025_09_02_221300_add_units_and_metadata_to_payroll_record_details_table', 1, NULL),
	(352, '2025_09_02_222540_add_earning_type_to_payroll_record_details_table', 1, NULL),
	(353, '2025_09_02_225900_add_company_contribution_columns_to_payroll_records_table', 1, NULL),
	(354, '2025_09_02_230700_add_shif_company_contribution_to_payroll_records_table', 1, NULL),
	(355, '2025_09_03_020634_add_shif_company_contribution_to_payroll_records_table', 1, NULL),
	(356, '2025_09_07_175839_add_type_id_to_payroll_records_details', 1, NULL),
	(357, '2025_09_11_150118_add_new_attributes_to_payroll_earning_types_table', 1, NULL),
	(358, '2025_09_11_163728_create_bank_records_table', 1, NULL),
	(359, '2025_09_11_163759_create_branches', 1, NULL),
	(360, '2025_09_15_190024_add_max_rates_to_pension_schemes_table', 1, NULL),
	(361, '2025_09_15_190242_create_employee_pension_schemes_table', 1, NULL),
	(362, '2025_09_15_191343_update_pension_schemes_remove_old_rates_add_max_rates', 1, NULL),
	(363, '2025_09_15_192754_remove_old_rate_columns_from_pension_schemes', 1, NULL),
	(364, '2025_09_16_100759_create_payroll_input_upload_logs_table', 1, NULL),
	(365, '2025_09_18_220657_add_stored_file_path_to_payroll_input_upload_logs_table', 1, NULL),
	(366, '2025_09_21_232139_drop_relationship_on_deduction_type', 1, NULL),
	(367, '2025_09_22_185128_remove_redundant_table', 1, NULL),
	(368, '2025_09_22_193017_remove_deductionearningstable', 1, NULL),
	(369, '2025_10_07_182040_add_employee_id_on_payroll_details', 1, NULL),
	(370, '2025_10_09_174056_add_payroll_period_id_to_payrol_records', 1, NULL),
	(371, '2025_10_09_190604_add_salary_change_history_table', 1, NULL),
	(372, '2025_10_10_113242_add_metadata_to_payroll_records', 1, NULL),
	(373, '2025_10_31_190423_add_tiers_to_payroll_table', 1, NULL),
	(374, '2025_11_07_165725_add_state_mourning_leave_adjustments', 1, NULL),
	(375, '2025_11_08_005349_create_companies_table', 1, NULL),
	(376, '2025_11_08_006942_add_company_id_to_data_tables', 1, NULL),
	(377, '2025_11_09_002126_create_company_permissions_table', 1, NULL),
	(378, '2025_11_13_211844_create_notice_departments_table', 1, NULL),
	(379, '2025_11_13_212140_create_notice_regions_table', 1, NULL),
	(380, '2025_11_13_212240_create_notice_locations_table', 1, NULL),
	(381, '2025_11_13_212440_add_target_gender_to_notices_table', 1, NULL),
	(382, '2025_12_02_203026_add_approval_delegation_table', 1, NULL),
	(383, '2025_12_06_145806_add_advanced_leave_to_leave_group_settings', 1, NULL),
	(384, '2025_12_06_155712_add_advanced_leave_columns_to_leave_group_settings', 1, NULL),
	(385, '2025_12_31_005811_create_ethnicities_table', 1, NULL),
	(386, '2026_01_30_184241_add_google_ids_to_users_table', 1, NULL),
	(387, '2026_02_19_121620_create_leave_adjustments_table', 1, NULL),
	(388, '2026_02_20_071530_update_leave_adjustments_table_structure', 1, NULL),
	(389, '2026_02_20_115627_add_adjustment_type_to_leave_adjustment', 1, NULL),
	(390, '2026_02_20_120223_addcreated_bytoleaveadjustment', 1, NULL),
	(391, '2026_03_31_120123_add_previous_year_to_leave_rollover', 1, NULL),
	(392, '2026_04_29_094000_add_job_requisition_form_fields', 1, NULL),
	(393, '2026_04_29_100001_create_performance_rating_scales_table', 1, NULL),
	(394, '2026_04_29_100002_create_performance_focus_areas_table', 1, NULL),
	(395, '2026_04_29_100003_create_performance_goals_table', 1, NULL),
	(396, '2026_04_29_100004_create_performance_behavioral_items_table', 1, NULL),
	(397, '2026_04_29_100005_create_performance_appraisals_table', 1, NULL),
	(398, '2026_04_29_100006_create_performance_appraisal_scores_table', 1, NULL),
	(399, '2026_04_29_100007_create_performance_appraisal_behavioral_scores_table', 1, NULL),
	(400, '2026_04_29_100008_create_performance_development_plans_table', 1, NULL),
	(401, '2026_04_29_100009_create_performance_learning_plans_table', 1, NULL),
	(402, '2026_04_29_100010_create_pip_plans_table', 1, NULL),
	(403, '2026_04_29_100011_create_pip_concerns_table', 1, NULL),
	(404, '2026_04_29_100012_create_pip_goals_table', 1, NULL),
	(405, '2026_04_29_100013_create_pip_support_resources_table', 1, NULL),
	(406, '2026_04_29_100014_create_pip_review_schedules_table', 1, NULL),
	(407, '2026_04_30_005340_add_department_head_id_to_department_table', 1, NULL),
	(408, '2026_05_01_000001_create_loan_tables', 1, NULL),
	(409, '2026_05_02_161143_make_contract_end_date_nullable', 1, NULL),
	(410, '2026_05_02_193110_create_notice_employees_table', 1, NULL),
	(411, '2026_05_02_234109_remove_soft_deletes_from_approval_delegations_table', 1, NULL),
	(412, '2026_05_03_000001_create_vehicle_types_table', 1, NULL),
	(413, '2026_05_03_000002_create_vehicles_table', 1, NULL),
	(414, '2026_05_03_000003_create_vehicle_assignments_table', 1, NULL),
	(415, '2026_05_03_010000_add_kenya_employer_fields_to_companies_table', 1, NULL),
	(416, '2026_05_03_020000_add_loan_deductions_to_payroll_records_table', 1, NULL),
	(417, '2026_05_03_131734_remove_department_and_driver_from_vehicles', 1, NULL),
	(418, '2026_05_03_131735_add_foreign_keys_to_vehicle_assignments', 1, NULL),
	(419, '2026_05_03_151000_remove_pay_grade_columns_from_promotions', 1, NULL),
	(420, '2026_05_03_151001_remove_pay_grade_columns_from_employee_movements', 1, NULL),
	(421, '2026_05_03_151002_remove_job_category_and_job_group_from_employees', 1, NULL),
	(422, '2026_05_03_161000_add_job_requisition_link_to_jobs', 1, NULL),
	(423, '2026_05_03_161001_create_job_requisition_templates_table', 1, NULL),
	(424, '2026_05_03_161002_create_job_applicant_evaluations_table', 1, NULL),
	(425, '2026_05_03_161003_create_job_hiring_teams_table', 1, NULL),
	(426, '2026_05_03_161004_create_recruitment_settings_table', 1, NULL),
	(427, '2026_05_03_161005_add_foreign_keys_to_vehicles_table', 1, NULL),
	(428, '2026_05_04_172609_create_review_periods_table', 1, NULL),
	(429, '2026_05_05_195447_create_leave_schedules_table', 1, NULL),
	(430, '2026_05_06_094244_create_system_settings_table', 1, NULL),
	(431, '2026_05_06_112008_add_job_requirements_to_job_table', 1, NULL),
	(432, '2026_05_06_112241_add_requisition_fields_to_job_table', 1, NULL),
	(433, '2026_05_06_113127_add_enhanced_fields_to_job_applicant_table', 1, NULL),
	(434, '2026_05_06_233318_create_document_consents_table', 1, NULL),
	(435, '2026_05_10_114900_add_section_head_id_to_employee_sections_table', 1, NULL),
	(436, '2026_05_10_122600_add_driving_license_number_to_employee_table', 1, NULL),
	(437, '2026_05_14_000000_add_arrears_paid_to_terminations', 1, NULL),
	(438, '2026_05_14_122458_add_reinstatement_status_to_terminations', 1, NULL),
	(439, '2026_05_14_135253_change_vehicles_status_to_use_general_status_enum', 1, NULL),
	(440, '2026_05_14_185245_add_is_half_day_to_leave_application_table', 1, NULL),
	(441, '2030_09_17_062133_KeyContstraintsMigration', 1, NULL);

-- Dumping structure for table stawi_self_client.model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.model_has_permissions: ~0 rows (approximately)
DELETE FROM `model_has_permissions`;

-- Dumping structure for table stawi_self_client.model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.model_has_roles: ~0 rows (approximately)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`, `approval_status`, `date_approved`, `status`, `approved_by`) VALUES
	(1, 'App\\Models\\User', 1, 0, NULL, NULL, NULL),
	(1, 'App\\Models\\User', 2, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.module
DROP TABLE IF EXISTS `module`;
CREATE TABLE IF NOT EXISTS `module` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `icon_class` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_location_id_foreign` (`location_id`),
  KEY `module_company_id_foreign` (`company_id`),
  CONSTRAINT `module_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `module_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.module: ~0 rows (approximately)
DELETE FROM `module`;

-- Dumping structure for table stawi_self_client.module_departments
DROP TABLE IF EXISTS `module_departments`;
CREATE TABLE IF NOT EXISTS `module_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_departments_company_id_foreign` (`company_id`),
  CONSTRAINT `module_departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.module_departments: ~0 rows (approximately)
DELETE FROM `module_departments`;

-- Dumping structure for table stawi_self_client.modules
DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `icon_class` varchar(191) NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_company_id_foreign` (`company_id`),
  CONSTRAINT `modules_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.modules: ~0 rows (approximately)
DELETE FROM `modules`;
INSERT INTO `modules` (`id`, `name`, `icon_class`, `approval_status`, `date_approved`, `status`, `approved_by`, `company_id`) VALUES
	(1, 'Attendance', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(2, 'Award', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(3, 'Notice Board', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(4, 'Training', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(5, 'Employee Management', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(6, 'Leave Management', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(7, 'Recruitment', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(8, 'Settings', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(9, 'Payroll', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(10, 'Administration', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(11, 'Annalytics', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(12, 'approvals', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(13, 'Employee Feedback', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(14, 'Disciplinary', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(15, 'Self Service', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(16, 'HR Uploads', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(17, 'Survey', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(18, 'project', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(19, 'Performance Management', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(20, 'PIP Management', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL),
	(21, 'Vehicle Management', 'mdi mdi-format-line-weight', 0, NULL, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.morpho_device_logs
DROP TABLE IF EXISTS `morpho_device_logs`;
CREATE TABLE IF NOT EXISTS `morpho_device_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_no` int(11) DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `user_first_name` varchar(191) NOT NULL,
  `user_name` varchar(191) NOT NULL,
  `device_id` varchar(191) NOT NULL,
  `time_logged` datetime NOT NULL,
  `location` varchar(191) NOT NULL,
  `year` varchar(191) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `hour` int(11) NOT NULL,
  `minute` int(11) NOT NULL,
  `second` int(11) NOT NULL,
  `ip_address` varchar(191) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `payroll_number` varchar(191) DEFAULT NULL,
  `updated_status` int(11) NOT NULL DEFAULT 0,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `morpho_device_logs_location_id_foreign` (`location_id`),
  KEY `morpho_device_logs_employee_id_index` (`employee_id`),
  CONSTRAINT `morpho_device_logs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `morpho_device_logs_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.morpho_device_logs: ~0 rows (approximately)
DELETE FROM `morpho_device_logs`;

-- Dumping structure for table stawi_self_client.morpho_devices
DROP TABLE IF EXISTS `morpho_devices`;
CREATE TABLE IF NOT EXISTS `morpho_devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_name` varchar(191) NOT NULL,
  `device_ip_address` varchar(191) NOT NULL,
  `device_serial` varchar(191) NOT NULL,
  `port` int(11) NOT NULL,
  `device_location` varchar(191) DEFAULT NULL,
  `timeout` int(11) NOT NULL,
  `device_status` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `device_type` varchar(191) DEFAULT 'entry_checkin',
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `morpho_devices_location_id_foreign` (`location_id`),
  KEY `morpho_devices_company_id_foreign` (`company_id`),
  CONSTRAINT `morpho_devices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `morpho_devices_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.morpho_devices: ~0 rows (approximately)
DELETE FROM `morpho_devices`;

-- Dumping structure for table stawi_self_client.nhif_rates
DROP TABLE IF EXISTS `nhif_rates`;
CREATE TABLE IF NOT EXISTS `nhif_rates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `range_start` double NOT NULL,
  `range_end` double NOT NULL,
  `amount_deductable` double NOT NULL,
  `percentage` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nhif_rates_location_id_foreign` (`location_id`),
  KEY `nhif_rates_company_id_foreign` (`company_id`),
  CONSTRAINT `nhif_rates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nhif_rates_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.nhif_rates: ~0 rows (approximately)
DELETE FROM `nhif_rates`;

-- Dumping structure for table stawi_self_client.notice
DROP TABLE IF EXISTS `notice`;
CREATE TABLE IF NOT EXISTS `notice` (
  `notice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `publish_date` date NOT NULL,
  `attach_file` varchar(191) DEFAULT NULL,
  `target_gender` varchar(191) NOT NULL DEFAULT 'all',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`notice_id`),
  KEY `notice_location_id_foreign` (`location_id`),
  KEY `notice_company_id_foreign` (`company_id`),
  CONSTRAINT `notice_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notice_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.notice: ~0 rows (approximately)
DELETE FROM `notice`;

-- Dumping structure for table stawi_self_client.notice_departments
DROP TABLE IF EXISTS `notice_departments`;
CREATE TABLE IF NOT EXISTS `notice_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notice_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notice_departments_notice_id_department_id_index` (`notice_id`,`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.notice_departments: ~0 rows (approximately)
DELETE FROM `notice_departments`;

-- Dumping structure for table stawi_self_client.notice_employees
DROP TABLE IF EXISTS `notice_employees`;
CREATE TABLE IF NOT EXISTS `notice_employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notice_id` bigint(20) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notice_employees_notice_id_employee_id_index` (`notice_id`,`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.notice_employees: ~0 rows (approximately)
DELETE FROM `notice_employees`;

-- Dumping structure for table stawi_self_client.notice_locations
DROP TABLE IF EXISTS `notice_locations`;
CREATE TABLE IF NOT EXISTS `notice_locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notice_id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notice_locations_notice_id_location_id_index` (`notice_id`,`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.notice_locations: ~0 rows (approximately)
DELETE FROM `notice_locations`;

-- Dumping structure for table stawi_self_client.notice_regions
DROP TABLE IF EXISTS `notice_regions`;
CREATE TABLE IF NOT EXISTS `notice_regions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notice_id` bigint(20) unsigned NOT NULL,
  `region_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notice_regions_notice_id_region_id_index` (`notice_id`,`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.notice_regions: ~0 rows (approximately)
DELETE FROM `notice_regions`;

-- Dumping structure for table stawi_self_client.notifications
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  KEY `notifications_company_id_foreign` (`company_id`),
  CONSTRAINT `notifications_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.notifications: ~0 rows (approximately)
DELETE FROM `notifications`;

-- Dumping structure for table stawi_self_client.offboarding_process
DROP TABLE IF EXISTS `offboarding_process`;
CREATE TABLE IF NOT EXISTS `offboarding_process` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `checklist_name` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `cleared` tinyint(1) NOT NULL DEFAULT 0,
  `comment` varchar(191) NOT NULL,
  `cleared_by_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offboarding_process_company_id_foreign` (`company_id`),
  CONSTRAINT `offboarding_process_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.offboarding_process: ~0 rows (approximately)
DELETE FROM `offboarding_process`;

-- Dumping structure for table stawi_self_client.paryroll9s
DROP TABLE IF EXISTS `paryroll9s`;
CREATE TABLE IF NOT EXISTS `paryroll9s` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `payroll_number` varchar(191) NOT NULL,
  `NHIF_number` varchar(191) NOT NULL,
  `NSSF_number` varchar(191) NOT NULL,
  `KRA_pin` varchar(191) NOT NULL,
  `national_id` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paryroll9s_location_id_foreign` (`location_id`),
  KEY `paryroll9s_company_id_foreign` (`company_id`),
  CONSTRAINT `paryroll9s_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `paryroll9s_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.paryroll9s: ~0 rows (approximately)
DELETE FROM `paryroll9s`;

-- Dumping structure for table stawi_self_client.password_resets
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_location_id_foreign` (`location_id`),
  CONSTRAINT `password_resets_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.password_resets: ~0 rows (approximately)
DELETE FROM `password_resets`;

-- Dumping structure for table stawi_self_client.paye_tax_bands
DROP TABLE IF EXISTS `paye_tax_bands`;
CREATE TABLE IF NOT EXISTS `paye_tax_bands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `country_name` varchar(191) NOT NULL,
  `band_order` int(11) NOT NULL,
  `monthly_lower_bound` decimal(12,2) NOT NULL,
  `monthly_upper_bound` decimal(12,2) DEFAULT NULL,
  `annual_lower_bound` decimal(12,2) NOT NULL,
  `annual_upper_bound` decimal(12,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paye_tax_bands_country_id_band_order_unique` (`country_id`,`band_order`),
  KEY `paye_tax_bands_company_id_foreign` (`company_id`),
  CONSTRAINT `paye_tax_bands_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.paye_tax_bands: ~0 rows (approximately)
DELETE FROM `paye_tax_bands`;

-- Dumping structure for table stawi_self_client.payout_channels
DROP TABLE IF EXISTS `payout_channels`;
CREATE TABLE IF NOT EXISTS `payout_channels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `relationship` varchar(191) DEFAULT NULL,
  `type_of_channel` varchar(191) NOT NULL COMMENT 'bank, sacco, savings plan, morgage',
  `main_account_number` varchar(191) DEFAULT NULL,
  `branch` varchar(191) DEFAULT NULL,
  `branch_code` varchar(191) DEFAULT NULL,
  `swift_code` varchar(191) DEFAULT NULL,
  `approval_status` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payout_channels_location_id_foreign` (`location_id`),
  KEY `payout_channels_company_id_foreign` (`company_id`),
  CONSTRAINT `payout_channels_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payout_channels_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payout_channels: ~0 rows (approximately)
DELETE FROM `payout_channels`;

-- Dumping structure for table stawi_self_client.payroll_adjustments
DROP TABLE IF EXISTS `payroll_adjustments`;
CREATE TABLE IF NOT EXISTS `payroll_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `salary_details_id` bigint(20) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `adjustment_type` int(11) NOT NULL,
  `payroll_number` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `month_being_adjusted` varchar(20) NOT NULL,
  `month_to_be_applied_adjustment` varchar(20) NOT NULL,
  `adjusted_basic_salary` int(11) NOT NULL DEFAULT 0,
  `adjusted_total_allowance` int(11) NOT NULL DEFAULT 0,
  `adjusted_total_deduction` int(11) NOT NULL DEFAULT 0,
  `adjusted_total_late` int(11) NOT NULL DEFAULT 0,
  `adjusted_late_amount` int(11) NOT NULL DEFAULT 0,
  `adjusted_total_absence` int(11) NOT NULL DEFAULT 0,
  `adjusted__absence_amount` int(11) NOT NULL DEFAULT 0,
  `adjusted_overtime_rate` int(11) NOT NULL DEFAULT 0,
  `adjusted_per_day_salary` int(11) NOT NULL DEFAULT 0,
  `adjusted__over_time_hour` varchar(50) NOT NULL DEFAULT '00:00',
  `adjusted_total_overtime_amount` int(11) NOT NULL DEFAULT 0,
  `adjusted_net_salary` int(11) NOT NULL DEFAULT 0,
  `adjusted_tax` int(11) NOT NULL DEFAULT 0,
  `adjusted_taxable_salary` int(11) NOT NULL DEFAULT 0,
  `adjusted_gross_salary` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `comment` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_adjustments_location_id_foreign` (`location_id`),
  KEY `payroll_adjustments_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_adjustments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_adjustments_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payroll_adjustments: ~0 rows (approximately)
DELETE FROM `payroll_adjustments`;

-- Dumping structure for table stawi_self_client.payroll_claim_recoveries
DROP TABLE IF EXISTS `payroll_claim_recoveries`;
CREATE TABLE IF NOT EXISTS `payroll_claim_recoveries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_claim_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `recovery_year` int(11) NOT NULL,
  `recovery_month` int(11) NOT NULL,
  `installment_number` int(11) NOT NULL,
  `scheduled_amount` decimal(10,2) NOT NULL,
  `actual_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processed','skipped','cancelled') NOT NULL DEFAULT 'pending',
  `processed_at` timestamp NULL DEFAULT NULL,
  `payroll_reference` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `adjustment_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `adjustment_reason` varchar(191) DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `batch_submission_id` varchar(191) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pcr_unique_claim_recovery` (`payroll_claim_id`,`recovery_year`,`recovery_month`),
  KEY `pcr_payroll_claim_installment` (`payroll_claim_id`,`installment_number`),
  KEY `pcr_employee_recovery_period` (`employee_id`,`recovery_year`,`recovery_month`),
  KEY `pcr_recovery_period` (`recovery_year`,`recovery_month`),
  KEY `pcr_status` (`status`),
  KEY `payroll_claim_recoveries_created_by_foreign` (`created_by`),
  KEY `payroll_claim_recoveries_updated_by_foreign` (`updated_by`),
  KEY `payroll_claim_recoveries_batch_submission_id_index` (`batch_submission_id`),
  KEY `payroll_claim_recoveries_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_claim_recoveries_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_claim_recoveries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `payroll_claim_recoveries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`),
  CONSTRAINT `payroll_claim_recoveries_payroll_claim_id_foreign` FOREIGN KEY (`payroll_claim_id`) REFERENCES `payroll_claims` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_claim_recoveries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payroll_claim_recoveries: ~0 rows (approximately)
DELETE FROM `payroll_claim_recoveries`;

-- Dumping structure for table stawi_self_client.payroll_claims
DROP TABLE IF EXISTS `payroll_claims`;
CREATE TABLE IF NOT EXISTS `payroll_claims` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `claim_type` varchar(191) NOT NULL DEFAULT 'general',
  `claim_title` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `claim_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'KES',
  `claim_year` int(11) NOT NULL,
  `claim_month` int(11) NOT NULL,
  `recovery_method` enum('lump_sum','installments') NOT NULL DEFAULT 'lump_sum',
  `recovery_periods` int(11) DEFAULT NULL,
  `recovery_amount_per_period` decimal(10,2) DEFAULT NULL,
  `recovery_start_year` int(11) DEFAULT NULL,
  `recovery_start_month` int(11) DEFAULT NULL,
  `status` enum('draft','pending_approval','approved','rejected','paid','partially_recovered','fully_recovered','cancelled') NOT NULL DEFAULT 'draft',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_recovered` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_reference` varchar(191) DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `reference_number` varchar(191) NOT NULL,
  `effective_date` date DEFAULT NULL,
  `recovery_completion_date` date DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `batch_submission_id` varchar(191) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_claims_reference_number_unique` (`reference_number`),
  KEY `payroll_claims_employee_id_status_index` (`employee_id`,`status`),
  KEY `payroll_claims_claim_year_claim_month_index` (`claim_year`,`claim_month`),
  KEY `payroll_claims_recovery_start_year_recovery_start_month_index` (`recovery_start_year`,`recovery_start_month`),
  KEY `payroll_claims_status_index` (`status`),
  KEY `payroll_claims_reference_number_index` (`reference_number`),
  KEY `payroll_claims_approved_by_foreign` (`approved_by`),
  KEY `payroll_claims_created_by_foreign` (`created_by`),
  KEY `payroll_claims_updated_by_foreign` (`updated_by`),
  KEY `payroll_claims_batch_submission_id_index` (`batch_submission_id`),
  KEY `payroll_claims_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_claims_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `payroll_claims_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_claims_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `payroll_claims_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`),
  CONSTRAINT `payroll_claims_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payroll_claims: ~0 rows (approximately)
DELETE FROM `payroll_claims`;

-- Dumping structure for table stawi_self_client.payroll_configurations
DROP TABLE IF EXISTS `payroll_configurations`;
CREATE TABLE IF NOT EXISTS `payroll_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `config_key` varchar(191) NOT NULL,
  `config_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`config_value`)),
  `config_type` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `effective_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_configurations_config_key_unique` (`config_key`),
  KEY `payroll_configurations_config_key_is_active_effective_date_index` (`config_key`,`is_active`,`effective_date`),
  KEY `payroll_configurations_created_by_foreign` (`created_by`),
  KEY `payroll_configurations_updated_by_foreign` (`updated_by`),
  KEY `payroll_configurations_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_configurations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_configurations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_configurations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payroll_configurations: ~0 rows (approximately)
DELETE FROM `payroll_configurations`;

-- Dumping structure for table stawi_self_client.payroll_earning_types
DROP TABLE IF EXISTS `payroll_earning_types`;
CREATE TABLE IF NOT EXISTS `payroll_earning_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `taxable` tinyint(1) NOT NULL DEFAULT 0,
  `is_pensionable` tinyint(1) NOT NULL DEFAULT 0,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `default_amount` decimal(10,2) DEFAULT NULL,
  `percentage_of_basic` decimal(5,2) DEFAULT NULL,
  `calculation_type` varchar(191) NOT NULL DEFAULT 'fixed_amount',
  `limit_per_month` decimal(8,2) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` varchar(191) DEFAULT NULL,
  `updated_by` varchar(191) DEFAULT NULL,
  `deleted_by` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `earning_type` varchar(191) NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_earning_types_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_earning_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payroll_earning_types: ~0 rows (approximately)
DELETE FROM `payroll_earning_types`;

-- Dumping structure for table stawi_self_client.payroll_input_upload_logs
DROP TABLE IF EXISTS `payroll_input_upload_logs`;
CREATE TABLE IF NOT EXISTS `payroll_input_upload_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_period_id` bigint(20) unsigned NOT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_name` varchar(191) DEFAULT NULL,
  `stored_file_path` varchar(191) DEFAULT NULL,
  `file_path` varchar(191) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_input_upload_logs_payroll_period_id_foreign` (`payroll_period_id`),
  KEY `payroll_input_upload_logs_uploaded_by_foreign` (`uploaded_by`),
  KEY `payroll_input_upload_logs_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_input_upload_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_input_upload_logs_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_input_upload_logs_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payroll_input_upload_logs: ~0 rows (approximately)
DELETE FROM `payroll_input_upload_logs`;

-- Dumping structure for table stawi_self_client.payroll_periods
DROP TABLE IF EXISTS `payroll_periods`;
CREATE TABLE IF NOT EXISTS `payroll_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `period_type` enum('monthly','weekly','bi-weekly') NOT NULL DEFAULT 'monthly',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `input_period_start` date NOT NULL,
  `input_period_end` date NOT NULL,
  `month_number` tinyint(3) unsigned DEFAULT NULL,
  `week_number` tinyint(3) unsigned DEFAULT NULL,
  `biweekly_number` tinyint(3) unsigned DEFAULT NULL,
  `pay_date` date NOT NULL,
  `status` enum('open','processing','closed') NOT NULL DEFAULT 'open',
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_periods_dates_unique` (`start_date`,`end_date`),
  KEY `payroll_periods_created_by_foreign` (`created_by`),
  KEY `payroll_periods_updated_by_foreign` (`updated_by`),
  KEY `payroll_periods_start_date_end_date_index` (`start_date`,`end_date`),
  KEY `payroll_periods_is_current_index` (`is_current`),
  KEY `payroll_periods_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_periods_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_periods_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_periods_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payroll_periods: ~0 rows (approximately)
DELETE FROM `payroll_periods`;
INSERT INTO `payroll_periods` (`id`, `name`, `period_type`, `start_date`, `end_date`, `input_period_start`, `input_period_end`, `month_number`, `week_number`, `biweekly_number`, `pay_date`, `status`, `is_current`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, 'January 2026', 'monthly', '2026-01-01', '2026-01-31', '2025-12-26', '2026-01-25', NULL, 1, 1, '2026-02-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(2, 'February 2026', 'monthly', '2026-02-01', '2026-02-28', '2026-01-26', '2026-02-25', NULL, 5, 3, '2026-03-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(3, 'March 2026', 'monthly', '2026-03-01', '2026-03-31', '2026-02-26', '2026-03-25', NULL, 9, 5, '2026-04-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(4, 'April 2026', 'monthly', '2026-04-01', '2026-04-30', '2026-03-26', '2026-04-25', NULL, 14, 7, '2026-05-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(5, 'May 2026', 'monthly', '2026-05-01', '2026-05-31', '2026-04-26', '2026-05-25', NULL, 18, 9, '2026-06-05', 'open', 1, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(6, 'June 2026', 'monthly', '2026-06-01', '2026-06-30', '2026-05-26', '2026-06-25', NULL, 23, 11, '2026-07-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(7, 'July 2026', 'monthly', '2026-07-01', '2026-07-31', '2026-06-26', '2026-07-25', NULL, 27, 13, '2026-08-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(8, 'August 2026', 'monthly', '2026-08-01', '2026-08-31', '2026-07-26', '2026-08-25', NULL, 31, 16, '2026-09-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(9, 'September 2026', 'monthly', '2026-09-01', '2026-09-30', '2026-08-26', '2026-09-25', NULL, 36, 18, '2026-10-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(10, 'October 2026', 'monthly', '2026-10-01', '2026-10-31', '2026-09-26', '2026-10-25', NULL, 40, 20, '2026-11-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(11, 'November 2026', 'monthly', '2026-11-01', '2026-11-30', '2026-10-26', '2026-11-25', NULL, 44, 22, '2026-12-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(12, 'December 2026', 'monthly', '2026-12-01', '2026-12-31', '2026-11-26', '2026-12-25', NULL, 49, 24, '2027-01-05', 'open', 0, 1, NULL, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.payroll_record_details
DROP TABLE IF EXISTS `payroll_record_details`;
CREATE TABLE IF NOT EXISTS `payroll_record_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_record_id` bigint(20) unsigned NOT NULL,
  `payroll_period_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `type_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('allowance','deduction','statutory_deduction','earning') DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `calculation_basis` decimal(10,2) DEFAULT NULL,
  `rate` decimal(6,4) DEFAULT NULL,
  `units` decimal(10,2) DEFAULT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 0,
  `is_pensionable` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_record_details_payroll_record_id_foreign` (`payroll_record_id`),
  KEY `payroll_record_details_employee_id_foreign` (`employee_id`),
  KEY `payroll_record_details_payroll_period_id_foreign` (`payroll_period_id`),
  KEY `payroll_record_details_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_record_details_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_record_details_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_record_details_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_record_details_payroll_record_id_foreign` FOREIGN KEY (`payroll_record_id`) REFERENCES `payroll_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payroll_record_details: ~0 rows (approximately)
DELETE FROM `payroll_record_details`;

-- Dumping structure for table stawi_self_client.payroll_records
DROP TABLE IF EXISTS `payroll_records`;
CREATE TABLE IF NOT EXISTS `payroll_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `employee_payroll_id` bigint(20) unsigned NOT NULL,
  `payroll_period_id` bigint(20) unsigned NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL,
  `total_allowances` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross_salary` decimal(12,2) NOT NULL,
  `total_deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `statutory_deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `non_statutory_deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `claim_recoveries` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paye_tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `nssf_contribution` decimal(12,2) NOT NULL DEFAULT 0.00,
  `nssf_tier1_contribution` decimal(10,2) NOT NULL DEFAULT 0.00,
  `nssf_tier2_contribution` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shif_contribution` decimal(12,2) NOT NULL DEFAULT 0.00,
  `housing_levy` decimal(12,2) NOT NULL DEFAULT 0.00,
  `pension_contribution` decimal(12,2) NOT NULL DEFAULT 0.00,
  `industrial_training_levy` decimal(12,2) NOT NULL DEFAULT 0.00,
  `nssf_tier1_company_contribution` decimal(12,2) NOT NULL DEFAULT 0.00,
  `nssf_tier2_company_contribution` decimal(12,2) NOT NULL DEFAULT 0.00,
  `housing_levy_company_contribution` decimal(12,2) NOT NULL DEFAULT 0.00,
  `employer_pension_contribution` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shif_company_contribution` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unpaid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(12,2) NOT NULL,
  `payment_method` enum('bank_transfer','mobile_money','cash','cheque') NOT NULL DEFAULT 'bank_transfer',
  `payment_reference` varchar(191) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `status` enum('draft','calculated','approved','paid','cancelled') NOT NULL DEFAULT 'draft',
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `batch_submission_id` varchar(191) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `payroll_record_status` int(11) NOT NULL DEFAULT 0,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `loan_deductions` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_records_employee_payroll_id_payroll_period_id_unique` (`employee_payroll_id`,`payroll_period_id`),
  KEY `payroll_records_processed_by_foreign` (`processed_by`),
  KEY `payroll_records_approved_by_foreign` (`approved_by`),
  KEY `payroll_records_created_by_foreign` (`created_by`),
  KEY `payroll_records_updated_by_foreign` (`updated_by`),
  KEY `payroll_records_payroll_period_id_status_index` (`payroll_period_id`,`status`),
  KEY `payroll_records_payment_date_index` (`payment_date`),
  KEY `payroll_records_employee_id_foreign` (`employee_id`),
  KEY `payroll_records_batch_submission_id_index` (`batch_submission_id`),
  KEY `payroll_records_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_records_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_records_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_records_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_records_employee_payroll_id_foreign` FOREIGN KEY (`employee_payroll_id`) REFERENCES `employee_payrolls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_records_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_records_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_records_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.payroll_records: ~0 rows (approximately)
DELETE FROM `payroll_records`;

-- Dumping structure for table stawi_self_client.pending_change_passwords
DROP TABLE IF EXISTS `pending_change_passwords`;
CREATE TABLE IF NOT EXISTS `pending_change_passwords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `password` varchar(191) NOT NULL,
  `password_changed_at` timestamp NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pending_change_passwords_company_id_foreign` (`company_id`),
  CONSTRAINT `pending_change_passwords_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.pending_change_passwords: ~0 rows (approximately)
DELETE FROM `pending_change_passwords`;

-- Dumping structure for table stawi_self_client.pension_schemes
DROP TABLE IF EXISTS `pension_schemes`;
CREATE TABLE IF NOT EXISTS `pension_schemes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `provider_name` varchar(191) NOT NULL,
  `provider_contact` varchar(191) DEFAULT NULL,
  `max_employee_rate` decimal(5,2) DEFAULT NULL,
  `max_employer_rate` decimal(5,2) DEFAULT NULL,
  `minimum_contribution` decimal(10,2) DEFAULT NULL,
  `maximum_contribution` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pension_schemes_code_unique` (`code`),
  KEY `pension_schemes_created_by_foreign` (`created_by`),
  KEY `pension_schemes_updated_by_foreign` (`updated_by`),
  KEY `pension_schemes_company_id_foreign` (`company_id`),
  CONSTRAINT `pension_schemes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pension_schemes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pension_schemes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.pension_schemes: ~0 rows (approximately)
DELETE FROM `pension_schemes`;

-- Dumping structure for table stawi_self_client.performance_appraisal_behavioral_scores
DROP TABLE IF EXISTS `performance_appraisal_behavioral_scores`;
CREATE TABLE IF NOT EXISTS `performance_appraisal_behavioral_scores` (
  `behavioral_score_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `appraisal_id` bigint(20) unsigned NOT NULL,
  `behavioral_item_id` bigint(20) unsigned NOT NULL,
  `itemized_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `self_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `review_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `self_comments` text DEFAULT NULL,
  `review_comments` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`behavioral_score_id`),
  KEY `performance_appraisal_behavioral_scores_appraisal_id_foreign` (`appraisal_id`),
  KEY `pa_behavioral_scores_item_id_foreign` (`behavioral_item_id`),
  CONSTRAINT `pa_behavioral_scores_item_id_foreign` FOREIGN KEY (`behavioral_item_id`) REFERENCES `performance_behavioral_items` (`behavioral_item_id`) ON DELETE CASCADE,
  CONSTRAINT `performance_appraisal_behavioral_scores_appraisal_id_foreign` FOREIGN KEY (`appraisal_id`) REFERENCES `performance_appraisals` (`appraisal_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.performance_appraisal_behavioral_scores: ~0 rows (approximately)
DELETE FROM `performance_appraisal_behavioral_scores`;

-- Dumping structure for table stawi_self_client.performance_appraisal_scores
DROP TABLE IF EXISTS `performance_appraisal_scores`;
CREATE TABLE IF NOT EXISTS `performance_appraisal_scores` (
  `score_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `appraisal_id` bigint(20) unsigned NOT NULL,
  `goal_id` bigint(20) unsigned NOT NULL,
  `itemized_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `self_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `review_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `self_comments` text DEFAULT NULL,
  `review_comments` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`score_id`),
  KEY `performance_appraisal_scores_appraisal_id_foreign` (`appraisal_id`),
  KEY `performance_appraisal_scores_goal_id_foreign` (`goal_id`),
  CONSTRAINT `performance_appraisal_scores_appraisal_id_foreign` FOREIGN KEY (`appraisal_id`) REFERENCES `performance_appraisals` (`appraisal_id`) ON DELETE CASCADE,
  CONSTRAINT `performance_appraisal_scores_goal_id_foreign` FOREIGN KEY (`goal_id`) REFERENCES `performance_goals` (`goal_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.performance_appraisal_scores: ~0 rows (approximately)
DELETE FROM `performance_appraisal_scores`;

-- Dumping structure for table stawi_self_client.performance_appraisals
DROP TABLE IF EXISTS `performance_appraisals`;
CREATE TABLE IF NOT EXISTS `performance_appraisals` (
  `appraisal_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `supervisor_id` bigint(20) unsigned DEFAULT NULL,
  `review_period` varchar(191) NOT NULL,
  `review_start_date` date DEFAULT NULL,
  `review_end_date` date DEFAULT NULL,
  `status` enum('draft','self_review','supervisor_review','hod_review','finalized','closed') NOT NULL DEFAULT 'draft',
  `total_itemized_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_self_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_review_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `employee_comments` text DEFAULT NULL,
  `supervisor_comments` text DEFAULT NULL,
  `hod_comments` text DEFAULT NULL,
  `employee_signed` tinyint(1) NOT NULL DEFAULT 0,
  `employee_sign_date` timestamp NULL DEFAULT NULL,
  `supervisor_signed` tinyint(1) NOT NULL DEFAULT 0,
  `supervisor_sign_date` timestamp NULL DEFAULT NULL,
  `hod_signed` tinyint(1) NOT NULL DEFAULT 0,
  `hod_sign_date` timestamp NULL DEFAULT NULL,
  `finalized_by` bigint(20) unsigned DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`appraisal_id`),
  KEY `performance_appraisals_employee_id_foreign` (`employee_id`),
  KEY `performance_appraisals_supervisor_id_foreign` (`supervisor_id`),
  KEY `performance_appraisals_finalized_by_foreign` (`finalized_by`),
  CONSTRAINT `performance_appraisals_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `performance_appraisals_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL,
  CONSTRAINT `performance_appraisals_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.performance_appraisals: ~0 rows (approximately)
DELETE FROM `performance_appraisals`;

-- Dumping structure for table stawi_self_client.performance_behavioral_items
DROP TABLE IF EXISTS `performance_behavioral_items`;
CREATE TABLE IF NOT EXISTS `performance_behavioral_items` (
  `behavioral_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_name` varchar(191) NOT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`behavioral_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.performance_behavioral_items: ~0 rows (approximately)
DELETE FROM `performance_behavioral_items`;

-- Dumping structure for table stawi_self_client.performance_development_plans
DROP TABLE IF EXISTS `performance_development_plans`;
CREATE TABLE IF NOT EXISTS `performance_development_plans` (
  `development_plan_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `appraisal_id` bigint(20) unsigned NOT NULL,
  `competency_name` varchar(191) NOT NULL,
  `expected_proficiency` varchar(191) NOT NULL,
  `smart_objective` text NOT NULL,
  `self_rating` decimal(3,1) DEFAULT NULL,
  `self_comments` text DEFAULT NULL,
  `reviewer_rating` decimal(3,1) DEFAULT NULL,
  `reviewer_comments` text DEFAULT NULL,
  `agreed_rating` decimal(3,1) DEFAULT NULL,
  `competencies_of_focus` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`development_plan_id`),
  KEY `performance_development_plans_appraisal_id_foreign` (`appraisal_id`),
  CONSTRAINT `performance_development_plans_appraisal_id_foreign` FOREIGN KEY (`appraisal_id`) REFERENCES `performance_appraisals` (`appraisal_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.performance_development_plans: ~0 rows (approximately)
DELETE FROM `performance_development_plans`;

-- Dumping structure for table stawi_self_client.performance_focus_areas
DROP TABLE IF EXISTS `performance_focus_areas`;
CREATE TABLE IF NOT EXISTS `performance_focus_areas` (
  `focus_area_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `focus_area_name` varchar(191) NOT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `department_id` int(10) unsigned DEFAULT NULL,
  `designation_id` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`focus_area_id`),
  KEY `performance_focus_areas_department_id_foreign` (`department_id`),
  KEY `performance_focus_areas_designation_id_foreign` (`designation_id`),
  CONSTRAINT `performance_focus_areas_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE SET NULL,
  CONSTRAINT `performance_focus_areas_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designation` (`designation_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.performance_focus_areas: ~0 rows (approximately)
DELETE FROM `performance_focus_areas`;
INSERT INTO `performance_focus_areas` (`focus_area_id`, `focus_area_name`, `weight`, `description`, `department_id`, `designation_id`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Financial Accuracy', 40.00, 'Error-free entries, reconciliations, and financial reporting accuracy.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(2, 'Reporting', 25.00, 'Timely submission and quality of reports.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(3, 'Compliance', 20.00, 'Tax compliance and policy adherence.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(4, 'Discipline', 15.00, 'Attendance and conduct.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(5, 'Technical Performance', 40.00, 'Technical delivery, code quality, and system maintenance.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(6, 'Quality & Accuracy', 20.00, 'Quality of deliverables and accuracy.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(7, 'Timeliness', 15.00, 'Meeting deadlines and SLA adherence.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(8, 'Communication', 15.00, 'Stakeholder communication and documentation.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(9, 'Behavioral Expectations', 10.00, 'Attendance and conduct.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(10, 'Delivery Efficiency', 40.00, 'On-time deliveries and route optimization.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(11, 'Inventory Accuracy', 25.00, 'Stock accuracy and warehouse management.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(12, 'Safety & Compliance', 20.00, 'Safety protocols and regulatory compliance.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(13, 'Behavioral Expectations', 15.00, 'Attendance and conduct.', NULL, NULL, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL);

-- Dumping structure for table stawi_self_client.performance_goals
DROP TABLE IF EXISTS `performance_goals`;
CREATE TABLE IF NOT EXISTS `performance_goals` (
  `goal_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `focus_area_id` bigint(20) unsigned NOT NULL,
  `strategic_objective` varchar(191) NOT NULL,
  `performance_metric` varchar(191) NOT NULL,
  `performance_target` text NOT NULL,
  `key_initiatives` text DEFAULT NULL,
  `itemized_weighting` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`goal_id`),
  KEY `performance_goals_focus_area_id_foreign` (`focus_area_id`),
  CONSTRAINT `performance_goals_focus_area_id_foreign` FOREIGN KEY (`focus_area_id`) REFERENCES `performance_focus_areas` (`focus_area_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.performance_goals: ~0 rows (approximately)
DELETE FROM `performance_goals`;
INSERT INTO `performance_goals` (`goal_id`, `focus_area_id`, `strategic_objective`, `performance_metric`, `performance_target`, `key_initiatives`, `itemized_weighting`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'Ensure financial data accuracy', 'Error-free entries', '100% accuracy in daily bookkeeping entries with zero errors', 'Double-check all entries, use automated validation tools', 20.00, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(2, 1, 'Maintain account reconciliations', 'Reconciliation accuracy', 'Complete monthly bank and ledger reconciliations by 5th of each month', 'Weekly reconciliation checks, automated matching tools', 20.00, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(3, 2, 'Deliver timely reports', 'Timely report submission', 'Submit all reports before the deadline', 'Set internal deadlines, use report templates', 12.50, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(4, 2, 'Improve report quality', 'Report quality', 'Reports should demonstrate clarity, completeness and insight', 'Add executive summary, use visual charts and graphs', 12.50, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(5, 3, 'Maintain tax compliance', 'Tax compliance', '100% on-time filing and remittance of all tax obligations', 'Calendar alerts, regular compliance audits', 10.00, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(6, 3, 'Follow financial policies', 'Policy adherence', 'Zero violations of internal financial policies', 'Regular policy reviews, training sessions', 10.00, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(7, 4, 'Maintain attendance standards', 'Attendance', 'Regular attendance and punctuality with less than 3% absenteeism', 'Plan ahead, notify supervisors early for any absences', 7.50, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(8, 4, 'Demonstrate professional conduct', 'Conduct', 'Demonstrate professional behavior and teamwork consistently', 'Active collaboration, conflict resolution training', 7.50, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(9, 5, 'Complete development tasks', 'Task completion', 'Complete at least 90% of assigned tasks per sprint', 'Daily standups, task breakdown, pair programming', 20.00, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(10, 5, 'Maintain system uptime', 'System uptime contribution', 'Achieve 99.9% system uptime through proactive maintenance', 'Monitoring alerts, incident response drills', 20.00, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(11, 6, 'Deliver quality releases', 'Bug-free releases', 'Less than 2 critical bugs per release', 'Automated testing, code reviews, QA process', 10.00, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(12, 6, 'Improve code quality', 'Code review quality', 'Average peer review score of 4.5/5 or higher', 'Follow coding standards, refactor legacy code', 10.00, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(13, 7, 'Meet sprint deadlines', 'On-time delivery', 'Deliver all committed features by sprint end', 'Sprint planning, velocity tracking, risk management', 7.50, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(14, 7, 'Resolve tickets on time', 'SLA adherence', 'Resolve 95% of tickets within SLA timeframes', 'Priority queue, ticket monitoring, escalation process', 7.50, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(15, 8, 'Maintain documentation', 'Documentation quality', 'Up-to-date technical docs and runbooks for all systems', 'Documentation sprints, wiki maintenance', 7.50, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(16, 8, 'Keep stakeholders informed', 'Stakeholder updates', 'Weekly status updates to all project stakeholders', 'Weekly reports, demo sessions, feedback loops', 7.50, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(17, 9, 'Maintain attendance standards', 'Attendance', 'Regular attendance and punctuality with less than 3% absenteeism', 'Plan ahead, notify supervisors early for any absences', 5.00, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(18, 9, 'Demonstrate professional conduct', 'Conduct', 'Demonstrate professional behavior and teamwork consistently', 'Active collaboration, conflict resolution training', 5.00, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(19, 10, 'Achieve on-time deliveries', 'On-time delivery rate', '95% of deliveries made on or before scheduled time', 'Route optimization software, real-time tracking', 20.00, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(20, 10, 'Optimize delivery routes', 'Route efficiency', 'Reduce fuel consumption by 10% through route optimization', 'GPS tracking, fuel monitoring, driver training', 20.00, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(21, 11, 'Maintain accurate inventory', 'Stock accuracy', '99.5% inventory record accuracy', 'Cycle counts, barcode scanning, inventory audits', 12.50, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(22, 11, 'Organize warehouse', 'Warehouse organization', 'Maintain cleanliness and organization standards', '5S methodology, regular inspections, storage optimization', 12.50, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(23, 12, 'Prevent safety incidents', 'Incident-free days', '90+ consecutive days without safety incidents', 'Daily safety briefings, equipment checks', 10.00, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(24, 12, 'Follow regulations', 'Regulatory compliance', '100% adherence to transport and safety regulations', 'Compliance training, vehicle inspections, permit tracking', 10.00, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(25, 13, 'Maintain attendance standards', 'Attendance', 'Regular attendance and punctuality with less than 3% absenteeism', 'Plan ahead, notify supervisors early for any absences', 7.50, 1, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL),
	(26, 13, 'Demonstrate professional conduct', 'Conduct', 'Demonstrate professional behavior and teamwork consistently', 'Active collaboration, conflict resolution training', 7.50, 2, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL);

-- Dumping structure for table stawi_self_client.performance_learning_plans
DROP TABLE IF EXISTS `performance_learning_plans`;
CREATE TABLE IF NOT EXISTS `performance_learning_plans` (
  `learning_plan_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `appraisal_id` bigint(20) unsigned NOT NULL,
  `course_title` varchar(191) NOT NULL,
  `due_date` date DEFAULT NULL,
  `learning_hours` varchar(191) DEFAULT NULL,
  `mid_year_status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `end_year_status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`learning_plan_id`),
  KEY `performance_learning_plans_appraisal_id_foreign` (`appraisal_id`),
  CONSTRAINT `performance_learning_plans_appraisal_id_foreign` FOREIGN KEY (`appraisal_id`) REFERENCES `performance_appraisals` (`appraisal_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.performance_learning_plans: ~0 rows (approximately)
DELETE FROM `performance_learning_plans`;

-- Dumping structure for table stawi_self_client.performance_rating_scales
DROP TABLE IF EXISTS `performance_rating_scales`;
CREATE TABLE IF NOT EXISTS `performance_rating_scales` (
  `rating_scale_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `points` int(11) NOT NULL,
  `rating_label` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `definition` text NOT NULL,
  `score_range` varchar(191) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`rating_scale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.performance_rating_scales: ~0 rows (approximately)
DELETE FROM `performance_rating_scales`;
INSERT INTO `performance_rating_scales` (`rating_scale_id`, `points`, `rating_label`, `description`, `definition`, `score_range`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 5, 'Outstanding', 'Exceptional performance exceeding all expectations', 'Consistently exceeds all performance expectations and demonstrates exceptional contribution to organizational goals.', '90% - 100%', 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(2, 4, 'Exceeds Expectations', 'Performance exceeds expectations in most areas', 'Regularly exceeds performance standards and makes valuable contributions beyond the role requirements.', '80% - 89%', 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(3, 3, 'Meets Expectations', 'Performance meets the required standards', 'Consistently meets performance standards and fulfills all role requirements satisfactorily.', '60% - 79%', 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(4, 2, 'Needs Improvement', 'Performance below expected standards', 'Performance is below expected standards in some key areas. Improvement plan required.', '40% - 59%', 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(5, 1, 'Unsatisfactory', 'Performance significantly below standards', 'Performance is significantly below required standards. Immediate improvement required.', '0% - 39%', 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05');

-- Dumping structure for table stawi_self_client.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `module_id` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1231 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.permissions: ~0 rows (approximately)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `module_id`, `deleted_at`, `status`, `approval_status`, `date_approved`, `approved_by`) VALUES
	(1, 'sanctum.csrf-cookie', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(2, 'workShift.index', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(3, 'workShift.create', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(4, 'workShift.store', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(5, 'workShift.edit', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(6, 'workShift.update', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(7, 'workShift.delete', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(8, 'dailyAttendance.dailyAttendance', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(9, 'dailyAttendance.dailyAttendanceFilter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(10, 'weeklyAttendance.weeklyAttendance', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(11, 'weeklyAttendance.weeklyAttendanceFilter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(12, 'monthlyAttendance.monthlyAttendance', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(13, 'monthlyAttendance.monthlyAttendanceFilter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(14, 'newMonthlyAttendance.monthlyAttendance', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(15, 'myAttendanceReport.myAttendanceReport', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(16, 'myAttendanceReport.myAttendanceReportFilter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(17, 'attendanceSummaryReport.attendanceSummaryReport', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(18, 'attendanceSummaryReport.attendanceSummaryReportFilter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(19, 'manualAttendance.manualAttendance', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(20, 'manualAttendance.filter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(21, 'manualAttendance.store', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(22, 'attendande.daily.download', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(23, 'attendande.daily.export', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(24, 'attendande.weekly.download', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(25, 'attendande.daily.download.excel', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(26, 'attendande.monthly.download', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(27, 'attendance.my.download', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(28, 'attendande.summary.download', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(29, 'attendance.mealReportattendande.meal.report', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(30, 'attendance.mealReportFilterattendande.meal.report.filter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(31, 'attendance.anomalyReport', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(32, 'attendance.anomalyReportFilter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(33, 'attendance.anomalies', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(34, 'attendance.anomaliesStore', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(35, 'attendance.correctFromExcel', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(36, 'attendance.storeFromExcel', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(37, 'attendance.approveOvertimes', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(38, 'attendance.overtimeApproval', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(39, 'attendance.filterOvertime', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(40, 'attendance.overtime.update_payroll', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(41, 'attendance.view_raw_logs', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(42, 'attendance.view_raw_logs.filter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(43, 'attendance.dashboard', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(44, 'attendance.dashboard.post', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(45, 'duplictes.remove', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(46, 'newAttendanceIndex', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(47, 'newAttendance.filter', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(48, 'newAttendance.store', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(49, 'migrateAttendanceData', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(50, 'saveMigrateAttendanceData', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(51, 'ip.attendance', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(52, 'biometricGet.index', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(53, 'biometricDevices', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(54, 'biometricUpdate', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(55, 'createDevice', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(56, 'storeDevice', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(57, 'editBioDevice', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(58, 'posteditBioDevice', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(59, 'deleteBioDevice', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(60, 'updateStatus', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(61, 'zkbiometricGet.index', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(62, 'devices', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(63, 'award.index', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(64, 'award.create', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(65, 'award.store', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(66, 'award.edit', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(67, 'award.update', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(68, 'award.delete', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(69, 'notice.index', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(70, 'notice.create', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(71, 'notice.store', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(72, 'notice.show', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(73, 'notice.edit', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(74, 'notice.update', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(75, 'notice.delete', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(76, 'trainingType.index', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(77, 'trainingType.create', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(78, 'trainingType.store', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(79, 'trainingType.list.options', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(80, 'trainingType.show', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(81, 'trainingType.edit', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(82, 'trainingType.update', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(83, 'trainingType.delete', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(84, 'trainingInfo.index', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(85, 'trainingInfo.create', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(86, 'trainingInfo.store', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(87, 'trainingInfo.show', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(88, 'trainingInfo.edit', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(89, 'trainingInfo.update', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(90, 'trainingInfo.delete', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(91, 'trainingInfo.attendants.index', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(92, 'trainingInfo.attendants', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(93, 'trainingInfo.attendants.add', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(94, 'trainingInfo.attendants.approve', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(95, 'trainingInfo.attendants.delete', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(96, 'trainingInfo.invitees', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(97, 'trainingInfo.invitees.add', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(98, 'trainingInfo.invitees.addMultiple', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(99, 'trainingInfo.invitees.approve', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(100, 'trainingInfo.invitees.delete', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(101, 'training.report.form', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(102, 'employeeTrainingReport.employeeTrainingReport.download', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(103, 'training.report.download', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(104, 'training.facilitator.index', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(105, 'training.facilitator.form', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(106, 'training.facilitator.store', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(107, 'training.facilitator.edit', 'web', '2026-05-19 15:07:05', '2026-05-19 15:07:05', 0, NULL, 1, 0, NULL, NULL),
	(108, 'training.facilitator.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(109, 'training.facilitator.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(110, 'training.facilitator.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(111, 'training.facilitator.filter', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(112, 'department.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(113, 'department.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(114, 'department.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(115, 'department.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(116, 'department.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(117, 'department.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(118, 'designation.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(119, 'designation.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(120, 'designation.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(121, 'designation.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(122, 'designation.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(123, 'designation.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(124, 'location.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(125, 'location.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(126, 'location.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(127, 'location.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(128, 'location.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(129, 'location.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(130, 'region.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(131, 'region.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(132, 'region.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(133, 'region.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(134, 'region.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(135, 'region.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(136, 'employee.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(137, 'employee.inactive.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(138, 'employee.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(139, 'employee.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(140, 'employee.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(141, 'employee.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(142, 'employee.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(143, 'employee.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(144, 'employee.disable', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(145, 'employee.enable', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(146, 'employee.updateBiometricCaptureStatus', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(147, 'employee.updateEarning', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(148, 'employee.deleteEarning', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(149, 'employee.addDeduction', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(150, 'employee.updateDeduction', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(151, 'employee.deleteDeduction', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(152, 'warning.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(153, 'warning.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(154, 'warning.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(155, 'warning.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(156, 'warning.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(157, 'warning.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(158, 'warning.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(159, 'termination.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(160, 'termination.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(161, 'termination.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(162, 'termination.import', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(163, 'termination.importSave', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(164, 'termination.doc.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(165, 'termination.report', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(166, 'termination.reinstate', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(167, 'termination.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(168, 'termination.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(169, 'termination.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(170, 'termination.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(171, 'termination-checklist.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(172, 'termination-checklist.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(173, 'termination-checklist.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(174, 'termination-checklist.import', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(175, 'termination-checklist.importSave', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(176, 'termination-checklist-action.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(177, 'termination-checklist.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(178, 'termination-checklist.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(179, 'termination-checklist.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(180, 'termination-checklist.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(181, 'permanent.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(182, 'permanent.updatePermanent', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(183, 'export', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(184, 'employee.importView', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(185, 'importUsers', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(186, 'importSupervisors', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(187, 'contractsImport', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(188, 'downloadSampleSupervisorFile', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(189, 'downloadSampleContractsFile', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(190, 'downloadSampleEmployeeFile', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(191, 'employee.active', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(192, 'employee.joinersReport', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(193, 'employee.leaversReport', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(194, 'employee.movementReport', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(195, 'employee.downloadReport', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(196, 'employee.masterRoll', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(197, 'employeeSection.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(198, 'employeeSection.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(199, 'employeeSection.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(200, 'employeeSection.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(201, 'employeeSection.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(202, 'employeeSection.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(203, 'employeeSection.destroy', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(204, 'employeeGroup.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(205, 'employeeGroup.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(206, 'employeeGroup.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(207, 'employeeGroup.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(208, 'employeeGroup.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(209, 'employeeGroup.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(210, 'employeeGroup.destroy', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(211, 'workshift.share', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(212, 'workshift.chart', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(213, 'workshift.chart_line', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(214, 'employeeMovement.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(215, 'employeeMovement.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(216, 'employeeMovement.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(217, 'employeeMovement.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(218, 'employeeMovement.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(219, 'employeeMovement.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(220, 'employeeMovement.destroy', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(221, 'employeeMovementImport', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(222, 'employeeMovementImportSave', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(223, 'employeeMovement.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(224, 'employeeMovement.undoChanges', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(225, 'employeeMovement.findEmployeeInfo', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(226, 'contract.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(227, 'contract.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(228, 'contract.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(229, 'contract.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(230, 'contract.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(231, 'contract.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(232, 'contract.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(233, 'contract.destroy', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(234, 'employee.program.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(235, 'employee.program.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(236, 'employee.program.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(237, 'employee.program.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(238, 'employee.program.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(239, 'employee.program.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(240, 'employee.program.destroy', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(241, 'employee.project.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(242, 'employee.project.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(243, 'employee.project.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(244, 'employee.project.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(245, 'employee.project.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(246, 'employee.project.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(247, 'employee.project.destroy', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(248, 'ethnicities.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(249, 'ethnicities.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(250, 'ethnicities.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(251, 'ethnicities.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(252, 'ethnicities.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(253, 'ethnicities.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(254, 'ethnicities.destroy', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(255, 'holiday.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(256, 'holiday.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(257, 'holiday.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(258, 'holiday.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(259, 'holiday.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(260, 'holiday.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(261, 'publicHoliday.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(262, 'publicHoliday.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(263, 'publicHoliday.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(264, 'publicHoliday.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(265, 'publicHoliday.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(266, 'publicHoliday.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(267, 'weeklyHoliday.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(268, 'weeklyHoliday.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(269, 'weeklyHoliday.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(270, 'weeklyHoliday.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(271, 'weeklyHoliday.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(272, 'weeklyHoliday.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(273, 'leaveType.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(274, 'leaveType.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(275, 'leaveType.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(276, 'leaveType.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(277, 'leaveType.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(278, 'leaveType.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(279, 'leaveGroup.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(280, 'leaveGroup.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(281, 'leaveGroup.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(282, 'leaveGroup.edit', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(283, 'leaveGroup.update', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(284, 'leaveGroup.delete', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(285, 'leaveGroup.addSetting', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(286, 'leaveGroup.deleteEmployee', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(287, 'leaveGroup.addEmployee', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(288, 'leaveGroup.deleteEmployees.bulk', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(289, 'leaveGroup.addEmployees.bulk', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(290, 'leaveGroup.listEmployees', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(291, 'leaveGroup.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(292, 'applyForLeave.index', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(293, 'applyForLeave.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(294, 'applyForLeave.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(295, 'leave.employee.balance', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(296, 'applyForLeave.show', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(297, 'applyOnBehalf.create', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(298, 'applyOnBehalf.store', 'web', '2026-05-19 15:07:06', '2026-05-19 15:07:06', 0, NULL, 1, 0, NULL, NULL),
	(299, 'applyOnBehalf.balance', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(300, 'applyOnBehalf.totalDays', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(301, 'applyOnBehalf.employeeDetails', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(302, 'applyOnBehalf.employeeLeaveTypes', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(303, 'requestedApplication.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(304, 'requestedApplication.viewDetails', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(305, 'requestedApplication.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(306, 'leaveReport.leaveReport.form', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(307, 'leaveReport.leaveReport.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(308, 'leave.admin.report.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(309, 'summaryReport.summaryReport.form', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(310, 'summaryReport.summaryReport.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(311, 'leave.summaryReport.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(312, 'leave.report.balances.form', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(313, 'leave.report.balances.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(314, 'leaveReport.fullOrganizationReport', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(315, 'leaveReport.fullOrganizationReport.filter', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(316, 'generateReport.generateReport', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(317, 'pendingLeaveRequests.pendingLeaveRequests', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(318, 'allLeaveApplications.allLeaveApplications', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(319, 'leaveApplication.recall', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(320, 'leaveApplication.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(321, 'ceoPendingLeaveRequests.ceoPendingLeaveRequests', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(322, 'downloadStaffReport.downloadStaffReport', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(323, 'leave.report.onLeaveToday', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(324, 'leaveReport.monthlyLeaveConsumption', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(325, 'downloadleaveReport.monthlyLeaveConsumption', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(326, 'exportleaveReport.monthlyLeaveConsumption', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(327, 'leave.report.history', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(328, 'leave.report.history.detail', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(329, 'leave.report.encashment', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(330, 'leave.report.encashment.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(331, 'myLeaveReport.myLeaveReport.view', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(332, 'myLeaveReport.myLeaveReport.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(333, 'leave.myreport.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(334, 'rolloverLeaves', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(335, 'rolloverLeaveEdit.view', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(336, 'rolloverLeaveEdit.save', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(337, 'addRolloverLeave1', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(338, 'storeRolloverLeave', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(339, 'rolloverLeave.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(340, 'updateDefaultRollovers', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(341, 'leave.adjustments.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(342, 'leave.adjustments.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(343, 'leave.adjustments.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(344, 'leave.adjustments.show', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(345, 'leave.adjustments.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(346, 'leave.adjustments.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(347, 'leave.adjustments.bulkDestroy', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(348, 'leave.adjustments.destroy', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(349, 'leave.adjustments.balance', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(350, 'leave.adjustments.template.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(351, 'leave.adjustments.import.form', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(352, 'leave.adjustments.import', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(353, 'leaveManagement.manualUpload', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(354, 'leaveManagement.manualUploadView', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(355, 'leaveManagement.manualUploadSave', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(356, 'leave.manage.approve_reject', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(357, 'leave.schedule.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(358, 'leave.schedule.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(359, 'leave.schedule.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(360, 'leave.schedule.bulkUpload', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(361, 'leave.schedule.bulkUpload.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(362, 'leave.schedule.sample.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(363, 'leave.schedule.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(364, 'leave.schedule.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(365, 'leave.schedule.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(366, 'leave.schedule.reminders', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(367, 'jobPost.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(368, 'jobPost.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(369, 'jobPost.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(370, 'jobPost.show', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(371, 'jobPost.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(372, 'jobPost.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(373, 'jobPost.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(374, 'jobPost.requisition.data', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(375, 'jobRequisition.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(376, 'jobRequisition.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(377, 'jobRequisition.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(378, 'jobRequisition.show', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(379, 'jobRequisition.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(380, 'jobRequisition.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(381, 'jobRequisition.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(382, 'jobRequisition.submit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(383, 'jobRequisition.approve.form', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(384, 'jobRequisition.approve', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(385, 'jobRequisition.reject.form', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(386, 'jobRequisition.reject', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(387, 'jobRequisition.convert', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(388, 'jobCandidate.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(389, 'jobCandidate.applyCandidateList', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(390, 'jobCandidate.shortListedApplicant', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(391, 'applicant.shortlist', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(392, 'applicant.reject', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(393, 'applicant.jobInterview', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(394, 'applicant.jobInterviewStore', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(395, 'jobCandidate.rejectedApplicant', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(396, 'jobCandidate.jobInterviewList', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(397, 'jobCandidate.jobHireList', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(398, 'applicant.hire', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(399, 'applicants.search', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(400, 'view.CV', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(401, 'download.CV', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(402, 'generalSettings.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(403, 'generalSettings.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(404, 'generalSettings.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(405, 'generalSettings.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(406, 'printHeadSettings.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(407, 'printHeadSettings.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(408, 'service.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(409, 'service.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(410, 'service.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(411, 'service.show', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(412, 'service.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(413, 'service.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(414, 'service.destroy', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(415, 'front.setting', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(416, 'front.setting.submit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(417, 'company.setting', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(418, 'company.setting.post', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(419, 'approvalSettings.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(420, 'approvalSettings.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(421, 'approvalSettings.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(422, 'approvalSettings.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(423, 'approvalSettings.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(424, 'approvalSettings.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(425, 'financial_year.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(426, 'financial_year.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(427, 'financial_year.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(428, 'financial_year.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(429, 'financial_year.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(430, 'financial_year.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(431, 'systemSettings.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(432, 'systemSettings.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(433, 'systemSettings.testEmail', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(434, 'systemSettings.testSms', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(435, 'systemSettings.testInApp', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(436, 'payrollIndex', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(437, 'taxSetup.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(438, 'update.taxRule', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(439, 'salaryDeductionRule.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(440, 'salary.deduction_typesrule.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(441, 'allowance.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(442, 'allowance.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(443, 'allowance.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(444, 'allowance.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(445, 'allowance.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(446, 'allowance.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(447, 'deduction_types.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(448, 'deduction_types.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(449, 'deduction_types.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(450, 'deduction_types.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(451, 'deduction_types.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(452, 'deduction_types.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(453, 'hourlyWages.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(454, 'hourlyWages.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(455, 'hourlyWages.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(456, 'hourlyWages.show', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(457, 'hourlyWages.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(458, 'hourlyWages.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(459, 'hourlyWages.destroy', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(460, 'generateSalarySheet.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(461, 'generateSalarySheet.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(462, 'generateSalarySheet.calculateEmployeeSalary', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(463, 'saveEmployeeSalaryDetails.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(464, 'makePayment', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(465, 'generatePayslip', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(466, 'generatePayslip.self', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(467, 'generateSalarySheet.monthSalary', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(468, 'paymentHistory.paymentHistory.view', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(469, 'paymentHistory.paymentHistory.post', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(470, 'payroll.paymenthistory.generate', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(471, 'downloadPayslip', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(472, 'downloadPayslip.self', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(473, 'payroll.download', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(474, 'payroll.download.full', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(475, 'payroll.download.payslip', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(476, 'workHourApproval.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(477, 'workHourApproval.filter', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(478, 'workHourApproval.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(479, 'generateSalary.massGenerate', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(480, 'bonusSetting.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(481, 'bonusSetting.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(482, 'bonusSetting.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(483, 'bonusSetting.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(484, 'bonusSetting.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(485, 'bonusSetting.delete', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(486, 'generateBonus.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(487, 'generateBonus.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(488, 'saveEmployeeBonus.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(489, 'generateBonus.filter', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(490, 'bonus_types.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(491, 'bonus_types.create', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(492, 'bonus_types.store', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(493, 'bonus_types.show', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(494, 'bonus_types.edit', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(495, 'bonus_types.update', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(496, 'bonus_types.destroy', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(497, 'nhif.index', 'web', '2026-05-19 15:07:07', '2026-05-19 15:07:07', 0, NULL, 1, 0, NULL, NULL),
	(498, 'nhif.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(499, 'nhif.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(500, 'nhif.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(501, 'nhif.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(502, 'nhif.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(503, 'nhif.destroy', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(504, 'bonuses.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(505, 'bonuses.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(506, 'bonuses.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(507, 'bonuses.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(508, 'bonuses.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(509, 'bonuses.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(510, 'bonuses.destroy', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(511, 'generatePayrollExcel', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(512, 'managementPay', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(513, 'managementPay.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(514, 'calculateManagementPay', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(515, 'calculatePaye', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(516, 'delete_salary_entry', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(517, 'payrollIndex2', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(518, 'geneMgtPayroll', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(519, 'generate_payroll_request', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(520, 'generate_payroll_request_mgmt', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(521, 'payroll9.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(522, 'payroll9.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(523, 'payroll9.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(524, 'payroll9.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(525, 'payroll9.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(526, 'payroll9.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(527, 'payroll9.destroy', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(528, 'payroll9.preview', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(529, 'payroll9.preview1', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(530, 'payroll9.preview2', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(531, 'payroll9.generate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(532, 'payroll9.massMail', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(533, 'paye.report.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(534, 'payroll.view', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(535, 'newSalaryCalculate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(536, 'newManagementSalaryCalculate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(537, 'payrollDataExport', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(538, 'managementPayrollDataExport', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(539, 'nhifReportsIndex', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(540, 'shifReportsIndex', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(541, 'nssfReportsIndex', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(542, 'ahlReportIndex', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(543, 'payroll.reports.deductions', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(544, 'payroll.reports.deductions.export', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(545, 'payroll.reports.earnings', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(546, 'payroll.reports.earnings.export', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(547, 'payroll.reports.variance', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(548, 'payroll.reports.variance.export', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(549, 'payoutChannel.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(550, 'payoutChannel.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(551, 'payoutChannel.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(552, 'payoutChannel.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(553, 'payoutChannel.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(554, 'payoutChannel.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(555, 'payoutChannel.delete', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(556, 'payoutChannel.updateStaff', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(557, 'payoutChannel.deleteFromStaff', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(558, 'tax-bands.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(559, 'tax-bands.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(560, 'tax-bands.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(561, 'tax-bands.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(562, 'tax-bands.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(563, 'tax-bands.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(564, 'tax-bands.destroy', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(565, 'tax-bands.get-tax-bands', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(566, 'earning_types.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(567, 'earning_types.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(568, 'earning_types.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(569, 'earning_types.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(570, 'earning_types.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(571, 'earning_types.delete', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(572, 'earning_types.destroy', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(573, 'earning_types.details', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(574, 'employee_earnings.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(575, 'employee_earnings.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(576, 'employee_earnings.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(577, 'employee_earnings.import.form', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(578, 'employee_earnings.import', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(579, 'employee_earnings.download_sample', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(580, 'employee_earnings.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(581, 'employee_earnings.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(582, 'employee_earnings.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(583, 'employee_earnings.delete', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(584, 'employee_earnings.approve', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(585, 'employee_earnings.reject', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(586, 'employee_earnings.suspend', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(587, 'employee_earnings.get_employee_earnings', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(588, 'employee_earnings.calculate_total', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(589, 'employee_deductions.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(590, 'employee_deductions.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(591, 'deduction_types.details', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(592, 'employee_deductions.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(593, 'employee_deductions.import', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(594, 'employee_deductions.download_template', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(595, 'employee_deductions.download_sample', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(596, 'employee_deductions.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(597, 'employee_deductions.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(598, 'employee_deductions.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(599, 'employee_deductions.delete', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(600, 'employee_deductions.approve', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(601, 'employee_deductions.reject', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(602, 'employee_deductions.suspend', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(603, 'employee_deductions.get_employee_deductions', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(604, 'employee_deductions.calculate_total', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(605, 'employee_deductions.calculate_daily_rate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(606, 'payroll.dashboard', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(607, 'payroll.dashboard.charts-data', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(608, 'payroll.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(609, 'payroll.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(610, 'payroll.process.form', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(611, 'payroll.process', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(612, 'payroll.process.single', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(613, 'payroll.approve', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(614, 'payroll.mark-paid', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(615, 'payroll.export', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(616, 'payroll.payslip', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(617, 'payroll.email.single', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(618, 'payroll.email.mass', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(619, 'payroll.claims.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(620, 'payroll.claims.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(621, 'payroll.claims.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(622, 'payroll.claims.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(623, 'payroll.claims.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(624, 'payroll.claims.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(625, 'payroll.claims.destroy', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(626, 'payroll.claims.submit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(627, 'payroll.claims.approve', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(628, 'payroll.claims.reject', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(629, 'payroll.claims.activate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(630, 'payroll.claims.cancel', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(631, 'payroll.claims.recoveries', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(632, 'payroll.claims.processRecovery', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(633, 'payroll.claims.skipRecovery', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(634, 'payroll.claims.api.employee', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(635, 'payroll.claims.api.stats', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(636, 'payroll.employees.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(637, 'payroll.employees.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(638, 'payroll.employees.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(639, 'payroll.employees.schemes', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(640, 'payroll.employees.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(641, 'payroll.employees.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(642, 'payroll.employees.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(643, 'payroll.employees.delete', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(644, 'payroll.employees.toggle-status', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(645, 'payroll.employees.template.download', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(646, 'payroll.employees.import.form', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(647, 'payroll.employees.import', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(648, 'payroll.employees.export', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(649, 'payroll.employees.locations', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(650, 'payroll.employees.salary-history', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(651, 'payroll.employees.all-salary-history', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(652, 'payroll.salary.history.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(653, 'payroll.salary.history.employee', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(654, 'payroll.salary.history.export', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(655, 'payroll.employees.allowances.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(656, 'payroll.employees.allowances.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(657, 'payroll.employees.allowances.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(658, 'payroll.employees.allowances.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(659, 'payroll.employees.allowances.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(660, 'payroll.employees.allowances.delete', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(661, 'payroll.employees.deductions.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(662, 'payroll.employees.deductions.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(663, 'payroll.employees.deductions.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(664, 'payroll.employees.deductions.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(665, 'payroll.employees.deductions.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(666, 'payroll.employees.deductions.delete', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(667, 'reports.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(668, 'reports.paye', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(669, 'reports.paye.generate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(670, 'reports.paye.p9', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(671, 'reports.paye.p10', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(672, 'reports.nssf', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(673, 'reports.nssf.generate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(674, 'reports.shif', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(675, 'reports.shif.generate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(676, 'reports.housing-levy', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(677, 'reports.housing-levy.generate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(678, 'reports.summary', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(679, 'reports.summary.generate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(680, 'reports.bank-transfer', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(681, 'reports.bank-transfer.generate', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(682, 'payroll.settings.allowance-types.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(683, 'payroll.settings.allowance-types.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(684, 'payroll.settings.allowance-types.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(685, 'payroll.settings.allowance-types.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(686, 'payroll.settings.allowance-types.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(687, 'payroll.settings.allowance-types.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(688, 'payroll.settings.allowance-types.delete', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(689, 'payroll.settings.allowance-types.toggle-status', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(690, 'payroll.settings.allowance-types.create-defaults', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(691, 'payroll.settings.pension-schemes.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(692, 'payroll.settings.pension-schemes.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(693, 'payroll.settings.pension-schemes.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(694, 'payroll.settings.pension-schemes.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(695, 'payroll.settings.pension-schemes.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(696, 'payroll.settings.pension-schemes.update', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(697, 'payroll.settings.pension-schemes.delete', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(698, 'payroll.settings.pension-schemes.toggle-status', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(699, 'payroll.settings.pension-schemes.calculate-contribution', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(700, 'payroll.settings.pension-schemes.generate-report', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(701, 'payroll.settings.pension-schemes.create-defaults', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(702, 'payroll.settings.pension-schemes.download-template', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(703, 'payroll.settings.pension-schemes.upload-assignments', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(704, 'payroll.settings.periods.index', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(705, 'payroll.settings.periods.create', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(706, 'payroll.settings.periods.store', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(707, 'payroll.settings.periods.show', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(708, 'payroll.settings.periods.edit', 'web', '2026-05-19 15:07:08', '2026-05-19 15:07:08', 0, NULL, 1, 0, NULL, NULL),
	(709, 'payroll.settings.periods.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(710, 'payroll.settings.periods.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(711, 'payroll.settings.periods.set-current', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(712, 'payroll.settings.periods.close', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(713, 'payroll.settings.periods.reopen', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(714, 'payroll.settings.periods.bank-upload-report', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(715, 'payroll.settings.periods.generate-periods', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(716, 'payrollReportsIndex', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(717, 'payrollReportsChartsData', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(718, 'reports.rawpaysumm', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(719, 'payroll.reports.inputs', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(720, 'payroll.reports.inputs.export', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(721, 'payroll.reports.inputs.upload', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(722, 'payroll.settings.deduction-types.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(723, 'payroll.settings.deduction-types.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(724, 'payroll.settings.deduction-types.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(725, 'payroll.settings.deduction-types.show', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(726, 'payroll.settings.deduction-types.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(727, 'payroll.settings.deduction-types.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(728, 'payroll.settings.deduction-types.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(729, 'payroll.settings.deduction-types.toggle-status', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(730, 'payroll.settings.deduction-types.create-defaults', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(731, 'payroll.bulk_upload.earnings.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(732, 'payroll.bulk_upload.earnings.download_template', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(733, 'payroll.bulk_upload.earnings', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(734, 'payroll.bulk_upload.deductions.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(735, 'payroll.bulk_upload.deductions.download_template', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(736, 'payroll.bulk_upload.advances.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(737, 'payroll.bulk_upload.advances.download_template', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(738, 'payroll.bulk_upload.advances', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(739, 'banks.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(740, 'banks.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(741, 'banks.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(742, 'banks.show', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(743, 'banks.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(744, 'banks.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(745, 'banks.destroy', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(746, 'banks.import', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(747, 'banks.import.process', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(748, 'banks.template.download', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(749, 'bank-branches.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(750, 'bank-branches.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(751, 'bank-branches.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(752, 'bank-branches.show', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(753, 'bank-branches.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(754, 'bank-branches.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(755, 'bank-branches.destroy', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(756, 'bank-branches.import', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(757, 'bank-branches.import.process', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(758, 'bank-branches.template.download', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(759, 'payroll.progress.check1', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(760, 'payroll.progress.check', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(761, 'payroll.progress', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(762, 'payroll.bulk.submit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(763, 'loans.dashboard', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(764, 'loans.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(765, 'loans.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(766, 'loans.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(767, 'loans.show', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(768, 'loans.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(769, 'loans.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(770, 'loans.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(771, 'loans.approve', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(772, 'loans.reject', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(773, 'loans.suspend', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(774, 'loans.types.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(775, 'loans.types.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(776, 'loans.types.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(777, 'loans.types.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(778, 'loans.types.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(779, 'loans.types.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(780, 'loans.applications.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(781, 'loans.applications.pending', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(782, 'loans.applications.approve', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(783, 'loans.applications.reject', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(784, 'loans.manual-deductions.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(785, 'loans.manual-deductions.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(786, 'loans.manual-deductions.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(787, 'loans.reports.summary', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(788, 'permissions.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(789, 'permissions.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(790, 'permissions.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(791, 'permissions.show', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(792, 'permissions.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(793, 'permissions.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(794, 'permissions.destroy', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(795, 'roles.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(796, 'roles.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(797, 'roles.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(798, 'roles.show', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(799, 'roles.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(800, 'roles.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(801, 'roles.destroy', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(802, 'company.permissions.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(803, 'company.permissions.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(804, 'company.permissions.view', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(805, 'company.permissions.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(806, 'company.permissions.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(807, 'company.permissions.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(808, 'company.permissions.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(809, 'company.permissions.get', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(810, 'payrollcaculator.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(811, 'payrollcaculator.paye', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(812, 'payrollcaculator.nssf', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(813, 'payrollcaculator.nhif', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(814, 'payrollcaculator.shif', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(815, 'payrollcaculator.ahl', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(816, 'payrollcaculator.gross', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(817, 'payrollcaculator.personal_relief', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(818, 'payrollcaculator.insurance_relief', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(819, 'payrollcaculator.taxable_pay', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(820, 'payrollcaculator.net_pay', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(821, 'payrollcaculator_index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(822, 'payrollcaculator_paye', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(823, 'payrollcaculator_nssf', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(824, 'payrollcaculator_nhif', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(825, 'payrollcaculator_ahl', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(826, 'payrollcaculator_gross', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(827, 'payrollcaculator_personal_relief', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(828, 'payrollcaculator_insurance_relief', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(829, 'payrollcaculator_taxable_pay', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(830, 'payrollcaculator_net_pay', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(831, 'payroll.overtime.bulk_upload.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(832, 'payroll.overtime.bulk_upload', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(833, 'payroll.overtime.bulk_upload.download_template', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(834, 'payroll.overtime.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(835, 'payroll.overtime.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(836, 'payroll.overtime.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(837, 'payroll.overtime.show', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(838, 'payroll.overtime.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(839, 'payroll.overtime.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(840, 'payroll.overtime.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(841, 'payroll.overtime.template.download', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(842, 'payroll.overtime.import.form', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(843, 'payroll.overtime.import', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(844, 'payroll.overtime.getEmployeeOvertimeRate', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(845, 'reports.annalytics.view', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(846, 'reports.activity_logs', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(847, 'reports.activity_logs.view', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(848, 'reports.errorLog', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(849, 'approval-workflows.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(850, 'approval-workflows.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(851, 'approval-workflows.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(852, 'approval-workflows.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(853, 'approval-workflows.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(854, 'approval-workflows.destroy', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(855, 'approval-workflows.show', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(856, 'approvals.approve', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(857, 'approvals.reject', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(858, 'approvals.status', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(859, 'approvals.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(860, 'approvals.batch-approve', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(861, 'approvals.batch-reject', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(862, 'approvals.batch-preview', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(863, 'approvals.pending-by-type', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(864, 'approvals.pending', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(865, 'approvals.my-pending', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(866, 'approvals.pending-employee-deductions', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(867, 'approvals.history', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(868, 'approvals.batch-submit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(869, 'approvals.batch-status', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(870, 'approvals.submit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(871, 'approvals.batch.submit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(872, 'feedback.category.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(873, 'feedback.category.trash', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(874, 'feedback.category.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(875, 'feedback.category.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(876, 'feedback.category.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(877, 'feedback.category.view', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(878, 'feedback.category.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(879, 'feedback.category.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(880, 'feedback.category.restore', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(881, 'feedback.category.destroy', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(882, 'employee.feedback.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(883, 'employee.feedback.respond', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(884, 'employee.feedback.store-reponse', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(885, 'employee.feedback.view', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(886, 'employee.feedback.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(887, 'employee.feedback.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(888, 'anonymous.feedback.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(889, 'anonymous.feedback.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(890, 'anonymous.feedback.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(891, 'anonymous.feedback.view', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(892, 'anonymous.feedback.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(893, 'anonymous.feedback.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(894, 'anonymous.feedback.review', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(895, 'anonymous.feedback.store-review', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(896, 'disciplinary.category.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(897, 'disciplinary.category.trash', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(898, 'disciplinary.category.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(899, 'disciplinary.category.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(900, 'disciplinary.category.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(901, 'disciplinary.category.view', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(902, 'disciplinary.category.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(903, 'disciplinary.category.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(904, 'disciplinary.category.restore', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(905, 'disciplinary.category.destroy', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(906, 'disciplinary.cases.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(907, 'disciplinary.cases.create', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(908, 'disciplinary.cases.edit', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(909, 'disciplinary.cases.store', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(910, 'disciplinary.cases.view', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(911, 'disciplinary.cases.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(912, 'disciplinary.cases.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(913, 'disciplinary.cases.destroy', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(914, 'disciplinary.cases.closed', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(915, 'disciplinary.cases.action', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(916, 'disciplinary.cases.close', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(917, 'disciplinary.cases.reopen', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(918, 'disciplinary.cases.trash', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(919, 'disciplinary.cases.restore', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(920, 'disciplinary.cases.action.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(921, 'disciplinary.cases.action.view', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(922, 'disciplinary.cases.action.update', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(923, 'disciplinary.cases.action.delete', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(924, 'disciplinary.cases.action.destroy', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(925, 'disciplinary.cases.action.closed', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(926, 'disciplinary.cases.action.action', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(927, 'ess.leave.index', 'web', '2026-05-19 15:07:09', '2026-05-19 15:07:09', 0, NULL, 1, 0, NULL, NULL),
	(928, 'ess.leave.form', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(929, 'ess.leave.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(930, 'ess.leave.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(931, 'ess.leave.apply.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(932, 'ess.leave.balance', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(933, 'ess.leave.leave.employee.apply.totaldays', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(934, 'ess.leave.applyForLeave.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(935, 'ess.leave.justification.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(936, 'ess.leave.recall', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(937, 'ess.leave.report.view', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(938, 'ess.leave.report.download', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(939, 'ess.leave.report.download2', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(940, 'ess.leave.scheduled.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(941, 'ess.notifications.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(942, 'ess.notifications.markAllRead', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(943, 'ess.notifications.markRead', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(944, 'ess.notifications.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(945, 'ess.payroll.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(946, 'ess.payroll.payslip.generate', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(947, 'ess.attendance.download', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(948, 'ess.attendance.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(949, 'ess.approval.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(950, 'ess.approval.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(951, 'ess.approval.delegations.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(952, 'ess.approval.delegations.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(953, 'ess.approval.delegations.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(954, 'ess.approval.delegations.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(955, 'ess.approval.delegations.destroy', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(956, 'ess.approval.delegations.toggle-status', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(957, 'ess.approval.delegations.deactivate', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(958, 'ess.awards.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(959, 'ess.diciplinary.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(960, 'ess.diciplinary.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(961, 'ess.contacts.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(962, 'ess.trainings.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(963, 'ess.trainings.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(964, 'ess.trainings.invitation.response', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(965, 'ess.trainings.attendance.confirm', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(966, 'ess.trainings.', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(967, 'ess.recruitment.job.posts', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(968, 'ess.recruitment.job.details', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(969, 'ess.recruitment.apply.job', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(970, 'ess.shifts.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(971, 'ess.documents.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(972, 'ess.documents.acknowledge', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(973, 'ess.documents.serve', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(974, 'ess.documents.docs.upload', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(975, 'ess.employee.edit.profile', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(976, 'ess.employee.update.profile', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(977, 'ess.employee.qualification.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(978, 'ess.employee.experience.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(979, 'ess.feedback.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(980, 'ess.feedback.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(981, 'ess.feedback.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(982, 'ess.feedback.view', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(983, 'ess.feedback.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(984, 'ess.feedback.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(985, 'ess.feedback.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(986, 'ess.feedback.anonymous.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(987, 'ess.feedback.anonymous.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(988, 'ess.survey.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(989, 'ess.subordinates.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(990, 'ess.loans.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(991, 'ess.loans.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(992, 'ess.loans.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(993, 'ess.loans.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(994, 'ess.performance.myAppraisals', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(995, 'ess.performance.selfEvaluation', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(996, 'ess.performance.selfReview', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(997, 'ess.performance.saveSelfReview', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(998, 'ess.performance.submitSelfReview', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(999, 'ess.performance.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1000, 'ess.pip.myPlans', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1001, 'ess.pip.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1002, 'ess.vehicle.myVehicle', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1003, 'document-categories.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1004, 'document-categories.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1005, 'document-categories.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1006, 'document-categories.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1007, 'document-categories.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1008, 'document-categories.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1009, 'documents-upload.deleted-docs', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1010, 'documents-upload.restore-document', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1011, 'documents-upload.show-deleted-document', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1012, 'documents-upload.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1013, 'documents-upload.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1014, 'documents-upload.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1015, 'documents-upload.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1016, 'documents-upload.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1017, 'documents-upload.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1018, 'documents-upload.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1019, 'documents-upload.review', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1020, 'documents-upload.update-review', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1021, 'documents-upload.show-document', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1022, 'documents-upload.serve', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1023, 'documents-upload.download', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1024, 'documents-upload.consents', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1025, 'documents-upload.consents.download', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1026, 'documents-upload.consent-summary', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1027, 'offboarding-process.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1028, 'offboarding-process.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1029, 'offboarding-process.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1030, 'offboarding-process.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1031, 'offboarding-process.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1032, 'offboarding-process.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1033, 'survey.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1034, 'survey.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1035, 'survey.google.auth', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1036, 'survey.auth.google.callback', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1037, 'survey.forms.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1038, 'survey.getLocationsByRegions', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1039, 'survey.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1040, 'survey.forms.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1041, 'survey.targeted-employees', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1042, 'survey.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1043, 'survey.export-employees', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1044, 'project.project-allocation.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1045, 'project.project-allocation.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1046, 'project.project-allocation.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1047, 'project.project-allocation.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1048, 'project.project-allocation.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1049, 'project.project-allocations.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1050, 'project.project-allocations.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1051, 'project.project-allocation-report.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1052, 'project.project-allocation-report.export', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1053, 'project.project-allocations.bulk-upload.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1054, 'project.project-allocations.bulk-upload.download-template', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1055, 'project.project-allocations.bulk-upload.import', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1056, 'performance.ratingScale.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1057, 'performance.ratingScale.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1058, 'performance.ratingScale.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1059, 'performance.ratingScale.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1060, 'performance.ratingScale.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1061, 'performance.ratingScale.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1062, 'performance.reviewPeriod.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1063, 'performance.reviewPeriod.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1064, 'performance.reviewPeriod.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1065, 'performance.reviewPeriod.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1066, 'performance.reviewPeriod.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1067, 'performance.reviewPeriod.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1068, 'performance.focusArea.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1069, 'performance.focusArea.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1070, 'performance.focusArea.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1071, 'performance.focusArea.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1072, 'performance.focusArea.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1073, 'performance.focusArea.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1074, 'performance.goal.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1075, 'performance.goal.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1076, 'performance.goal.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1077, 'performance.goal.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1078, 'performance.goal.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1079, 'performance.goal.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1080, 'performance.behavioralItem.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1081, 'performance.behavioralItem.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1082, 'performance.behavioralItem.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1083, 'performance.behavioralItem.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1084, 'performance.behavioralItem.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1085, 'performance.behavioralItem.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1086, 'performance.appraisal.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1087, 'performance.appraisal.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1088, 'performance.appraisal.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1089, 'performance.appraisal.template.download', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1090, 'performance.appraisal.bulkUpload', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1091, 'performance.appraisal.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1092, 'performance.appraisal.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1093, 'performance.appraisal.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1094, 'performance.appraisal.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1095, 'performance.appraisal.selfReview', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1096, 'performance.appraisal.saveSelfReview', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1097, 'performance.appraisal.hodReview', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1098, 'performance.appraisal.saveHodReview', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1099, 'performance.appraisal.finalize', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1100, 'performance.appraisal.employeeSign', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1101, 'performance.appraisal.supervisorSign', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1102, 'performance.appraisal.hodSign', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1103, 'performance.supervisor.evaluations', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1104, 'performance.supervisor.review', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1105, 'performance.supervisor.saveReview', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1106, 'performance.hod.evaluations', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1107, 'performance.ajax.focusAreasForEmployee', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1108, 'performance.report.department', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1109, 'performance.report.department.download', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1110, 'performance.report.employee', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1111, 'performance.report.employee.download', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1112, 'performance.report.summary', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1113, 'pip.plan.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1114, 'pip.plan.create', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1115, 'pip.plan.createFromAppraisal', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1116, 'pip.plan.store', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1117, 'pip.plan.show', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1118, 'pip.plan.edit', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1119, 'pip.plan.update', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1120, 'pip.plan.delete', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1121, 'pip.plan.activate', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1122, 'pip.plan.employeeAcknowledge', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1123, 'pip.plan.supervisorSign', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1124, 'pip.plan.hrValidate', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1125, 'pip.plan.finalizeOutcome', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1126, 'pip.plan.lock', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1127, 'pip.plan.employeeDetails', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1128, 'pip.goal.index', 'web', '2026-05-19 15:07:10', '2026-05-19 15:07:10', 0, NULL, 1, 0, NULL, NULL),
	(1129, 'pip.goal.store', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1130, 'pip.goal.edit', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1131, 'pip.goal.update', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1132, 'pip.goal.delete', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1133, 'pip.goal.updateStatus', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1134, 'pip.support.index', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1135, 'pip.support.store', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1136, 'pip.support.edit', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1137, 'pip.support.update', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1138, 'pip.support.delete', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1139, 'pip.support.updateStatus', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1140, 'pip.schedule.index', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1141, 'pip.schedule.conduct', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1142, 'pip.schedule.reschedule', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1143, 'pip.report.dashboard', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1144, 'pip.report.byDepartment', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1145, 'pip.report.byOutcome', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1146, 'vehicle.assignment.index', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1147, 'vehicle.assignment.download', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1148, 'vehicle.assignment.vehicle_history', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1149, 'vehicle.assignment.employee_history', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1150, 'vehicle.index', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1151, 'vehicle.create', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1152, 'vehicle.store', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1153, 'vehicle.get_drivers', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1154, 'vehicle.import', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1155, 'vehicle.download_template', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1156, 'vehicle.edit', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1157, 'vehicle.show', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1158, 'vehicle.update', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1159, 'vehicle.delete', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1160, 'vehicle.assign_driver', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1161, 'vehicle.unassign_driver', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1162, 'login', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1163, 'verify', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1164, 'verify-otp', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1165, 'resend-otp', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1166, 'auth.google', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1167, 'auth.google.callback', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1168, 'job.details', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1169, 'job.apply.form', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1170, 'job.external.apply', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1171, 'jobPost.viewDescription', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1172, 'jobPost.downloadDescription', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1173, 'job.internal_details', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1174, 'job.internal.apply', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1175, 'home.dashboard', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1176, 'home.profile', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1177, 'send-password-change-otp-web', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1178, 'home.logout', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1179, 'employee.updateEarningsAndBenefits', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1180, 'employee.addDeduction.web', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1181, 'employee.updateDeduction.web', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1182, 'employee.deleteDeduction.web', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1183, 'user.index', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1184, 'user.create', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1185, 'user.store', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1186, 'user.show', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1187, 'user.edit', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1188, 'user.update', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1189, 'user.destroy', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1190, 'user.inactive', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1191, 'user.active', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1192, 'userRole.index', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1193, 'userRole.create', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1194, 'userRole.store', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1195, 'userRole.show', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1196, 'userRole.edit', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1197, 'userRole.update', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1198, 'userRole.destroy', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1199, 'rolePermission.index', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1200, 'rolePermission.create', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1201, 'rolePermission.store', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1202, 'rolePermission.show', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1203, 'rolePermission.edit', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1204, 'rolePermission.update', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1205, 'rolePermission.destroy', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1206, 'roles.permission.menus', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1207, 'company.index', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1208, 'company.create', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1209, 'company.store', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1210, 'company.show', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1211, 'company.edit', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1212, 'company.update', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1213, 'company.destroy', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1214, 'company.switch', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1215, 'reset_password_without_token', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1216, 'reset_password_with_token', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1217, 'password.reset.token', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1218, 'resetPassword', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1219, 'changePassword.index', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1220, 'changePassword.create', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1221, 'changePassword.store', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1222, 'changePassword.show', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1223, 'changePassword.edit', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1224, 'changePassword.update', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1225, 'changePassword.destroy', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1226, 'sendPasswordReset', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1227, 'licenses', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1228, 'invalidLicense', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1229, 'azure.login', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL),
	(1230, 'azure.login.callback', 'web', '2026-05-19 15:07:11', '2026-05-19 15:07:11', 0, NULL, 1, 0, NULL, NULL);

-- Dumping structure for table stawi_self_client.personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.personal_access_tokens: ~0 rows (approximately)
DELETE FROM `personal_access_tokens`;

-- Dumping structure for table stawi_self_client.pip_concerns
DROP TABLE IF EXISTS `pip_concerns`;
CREATE TABLE IF NOT EXISTS `pip_concerns` (
  `concern_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pip_id` bigint(20) unsigned NOT NULL,
  `goal_id` bigint(20) unsigned DEFAULT NULL,
  `behavioral_item_id` bigint(20) unsigned DEFAULT NULL,
  `appraisal_score_id` bigint(20) unsigned DEFAULT NULL,
  `description` text NOT NULL,
  `actual_score` decimal(5,2) DEFAULT NULL,
  `target_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`concern_id`),
  KEY `pip_concerns_pip_id_foreign` (`pip_id`),
  KEY `pip_concerns_goal_id_foreign` (`goal_id`),
  KEY `pip_concerns_behavioral_item_id_foreign` (`behavioral_item_id`),
  KEY `pip_concerns_appraisal_score_id_foreign` (`appraisal_score_id`),
  CONSTRAINT `pip_concerns_appraisal_score_id_foreign` FOREIGN KEY (`appraisal_score_id`) REFERENCES `performance_appraisal_scores` (`score_id`) ON DELETE SET NULL,
  CONSTRAINT `pip_concerns_behavioral_item_id_foreign` FOREIGN KEY (`behavioral_item_id`) REFERENCES `performance_behavioral_items` (`behavioral_item_id`) ON DELETE SET NULL,
  CONSTRAINT `pip_concerns_goal_id_foreign` FOREIGN KEY (`goal_id`) REFERENCES `performance_goals` (`goal_id`) ON DELETE SET NULL,
  CONSTRAINT `pip_concerns_pip_id_foreign` FOREIGN KEY (`pip_id`) REFERENCES `pip_plans` (`pip_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.pip_concerns: ~0 rows (approximately)
DELETE FROM `pip_concerns`;
INSERT INTO `pip_concerns` (`concern_id`, `pip_id`, `goal_id`, `behavioral_item_id`, `appraisal_score_id`, `description`, `actual_score`, `target_score`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, NULL, NULL, 'Below-target performance on financial accuracy metrics.', 45.00, 80.00, '2026-05-19 15:07:05', '2026-05-19 15:07:05');

-- Dumping structure for table stawi_self_client.pip_goals
DROP TABLE IF EXISTS `pip_goals`;
CREATE TABLE IF NOT EXISTS `pip_goals` (
  `goal_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pip_id` bigint(20) unsigned NOT NULL,
  `objective` text NOT NULL,
  `action_required` text NOT NULL,
  `target_kpi` varchar(191) NOT NULL,
  `deadline` date NOT NULL,
  `status` enum('pending','in_progress','completed','overdue') NOT NULL DEFAULT 'pending',
  `progress_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`goal_id`),
  KEY `pip_goals_pip_id_foreign` (`pip_id`),
  CONSTRAINT `pip_goals_pip_id_foreign` FOREIGN KEY (`pip_id`) REFERENCES `pip_plans` (`pip_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.pip_goals: ~0 rows (approximately)
DELETE FROM `pip_goals`;
INSERT INTO `pip_goals` (`goal_id`, `pip_id`, `objective`, `action_required`, `target_kpi`, `deadline`, `status`, `progress_notes`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Improve daily entry accuracy', 'Double-check all entries before posting; use checklist', '>= 90% error-free entries', '2026-06-03', 'in_progress', 'Checklist implemented; initial results encouraging.', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(2, 1, 'Submit reports on time', 'Block calendar time every Friday for report preparation', '100% on-time submission', '2026-06-08', 'pending', NULL, '2026-05-19 15:07:05', '2026-05-19 15:07:05');

-- Dumping structure for table stawi_self_client.pip_plans
DROP TABLE IF EXISTS `pip_plans`;
CREATE TABLE IF NOT EXISTS `pip_plans` (
  `pip_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `supervisor_id` bigint(20) unsigned DEFAULT NULL,
  `hr_manager_id` bigint(20) unsigned DEFAULT NULL,
  `appraisal_id` bigint(20) unsigned DEFAULT NULL,
  `position` varchar(191) DEFAULT NULL,
  `department_id` int(10) unsigned DEFAULT NULL,
  `designation_id` int(10) unsigned DEFAULT NULL,
  `plan_period_start` date NOT NULL,
  `plan_period_end` date NOT NULL,
  `purpose` text NOT NULL,
  `trigger_score` decimal(5,2) DEFAULT NULL,
  `trigger_type` enum('automatic','manual_supervisor','manual_hr') NOT NULL DEFAULT 'automatic',
  `status` enum('draft','active','in_review','completed','extended','cancelled') NOT NULL DEFAULT 'draft',
  `outcome` enum('pending','successful_completion','partial_improvement','failure') NOT NULL DEFAULT 'pending',
  `outcome_notes` text DEFAULT NULL,
  `employee_acknowledged` tinyint(1) NOT NULL DEFAULT 0,
  `employee_ack_date` timestamp NULL DEFAULT NULL,
  `supervisor_signed` tinyint(1) NOT NULL DEFAULT 0,
  `supervisor_sign_date` timestamp NULL DEFAULT NULL,
  `hr_validated` tinyint(1) NOT NULL DEFAULT 0,
  `hr_validation_date` timestamp NULL DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pip_id`),
  KEY `pip_plans_employee_id_foreign` (`employee_id`),
  KEY `pip_plans_supervisor_id_foreign` (`supervisor_id`),
  KEY `pip_plans_hr_manager_id_foreign` (`hr_manager_id`),
  KEY `pip_plans_appraisal_id_foreign` (`appraisal_id`),
  KEY `pip_plans_department_id_foreign` (`department_id`),
  KEY `pip_plans_designation_id_foreign` (`designation_id`),
  KEY `pip_plans_created_by_foreign` (`created_by`),
  CONSTRAINT `pip_plans_appraisal_id_foreign` FOREIGN KEY (`appraisal_id`) REFERENCES `performance_appraisals` (`appraisal_id`) ON DELETE SET NULL,
  CONSTRAINT `pip_plans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL,
  CONSTRAINT `pip_plans_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE SET NULL,
  CONSTRAINT `pip_plans_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designation` (`designation_id`) ON DELETE SET NULL,
  CONSTRAINT `pip_plans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `pip_plans_hr_manager_id_foreign` FOREIGN KEY (`hr_manager_id`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL,
  CONSTRAINT `pip_plans_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.pip_plans: ~0 rows (approximately)
DELETE FROM `pip_plans`;
INSERT INTO `pip_plans` (`pip_id`, `employee_id`, `supervisor_id`, `hr_manager_id`, `appraisal_id`, `position`, `department_id`, `designation_id`, `plan_period_start`, `plan_period_end`, `purpose`, `trigger_score`, `trigger_type`, `status`, `outcome`, `outcome_notes`, `employee_acknowledged`, `employee_ack_date`, `supervisor_signed`, `supervisor_sign_date`, `hr_validated`, `hr_validation_date`, `is_locked`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, NULL, NULL, NULL, 'Accountant', 1, 1, '2026-04-19', '2026-06-18', 'Employee performance fell below threshold on financial accuracy and reporting KPIs. This PIP outlines improvement targets and support mechanisms.', 62.50, 'automatic', 'active', 'pending', NULL, 1, '2026-04-21 15:07:05', 1, '2026-04-22 15:07:05', 1, '2026-04-23 15:07:05', 0, 1, '2026-05-19 15:07:05', '2026-05-19 15:07:05', NULL);

-- Dumping structure for table stawi_self_client.pip_review_schedules
DROP TABLE IF EXISTS `pip_review_schedules`;
CREATE TABLE IF NOT EXISTS `pip_review_schedules` (
  `schedule_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pip_id` bigint(20) unsigned NOT NULL,
  `review_stage` varchar(191) NOT NULL,
  `stage_number` int(11) NOT NULL DEFAULT 1,
  `scheduled_date` date NOT NULL,
  `status` enum('pending','completed','missed','rescheduled') NOT NULL DEFAULT 'pending',
  `comments` text DEFAULT NULL,
  `findings` text DEFAULT NULL,
  `conducted_by` bigint(20) unsigned DEFAULT NULL,
  `conducted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `pip_review_schedules_pip_id_foreign` (`pip_id`),
  KEY `pip_review_schedules_conducted_by_foreign` (`conducted_by`),
  CONSTRAINT `pip_review_schedules_conducted_by_foreign` FOREIGN KEY (`conducted_by`) REFERENCES `employee` (`employee_id`) ON DELETE SET NULL,
  CONSTRAINT `pip_review_schedules_pip_id_foreign` FOREIGN KEY (`pip_id`) REFERENCES `pip_plans` (`pip_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.pip_review_schedules: ~0 rows (approximately)
DELETE FROM `pip_review_schedules`;
INSERT INTO `pip_review_schedules` (`schedule_id`, `pip_id`, `review_stage`, `stage_number`, `scheduled_date`, `status`, `comments`, `findings`, `conducted_by`, `conducted_at`, `created_at`, `updated_at`) VALUES
	(1, 1, 'First Review', 1, '2026-05-03', 'completed', 'Employee showed initial progress on daily entries accuracy.', 'Positive trend observed in first week.', NULL, '2026-05-03 15:07:05', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(2, 1, 'Second Review', 2, '2026-05-17', 'pending', NULL, NULL, NULL, NULL, '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(3, 1, 'Third Review', 3, '2026-05-31', 'pending', NULL, NULL, NULL, NULL, '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(4, 1, 'Fourth Review', 4, '2026-06-14', 'pending', NULL, NULL, NULL, NULL, '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(5, 1, 'Final Review', 5, '2026-06-28', 'pending', NULL, NULL, NULL, NULL, '2026-05-19 15:07:05', '2026-05-19 15:07:05');

-- Dumping structure for table stawi_self_client.pip_support_resources
DROP TABLE IF EXISTS `pip_support_resources`;
CREATE TABLE IF NOT EXISTS `pip_support_resources` (
  `resource_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pip_id` bigint(20) unsigned NOT NULL,
  `support_type` enum('training','mentorship','tools','counseling','other') NOT NULL,
  `description` text NOT NULL,
  `provider` enum('hr','supervisor','external','peer') NOT NULL,
  `scheduled_date` date DEFAULT NULL,
  `status` enum('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`resource_id`),
  KEY `pip_support_resources_pip_id_foreign` (`pip_id`),
  CONSTRAINT `pip_support_resources_pip_id_foreign` FOREIGN KEY (`pip_id`) REFERENCES `pip_plans` (`pip_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.pip_support_resources: ~0 rows (approximately)
DELETE FROM `pip_support_resources`;
INSERT INTO `pip_support_resources` (`resource_id`, `pip_id`, `support_type`, `description`, `provider`, `scheduled_date`, `status`, `created_at`, `updated_at`) VALUES
	(1, 1, 'training', 'Advanced bookkeeping and reconciliation workshop', 'external', '2026-05-24', 'planned', '2026-05-19 15:07:05', '2026-05-19 15:07:05'),
	(2, 1, 'mentorship', 'Weekly 1-on-1 mentoring sessions with senior accountant', 'supervisor', '2026-05-26', 'in_progress', '2026-05-19 15:07:05', '2026-05-19 15:07:05');

-- Dumping structure for table stawi_self_client.print_head_settings
DROP TABLE IF EXISTS `print_head_settings`;
CREATE TABLE IF NOT EXISTS `print_head_settings` (
  `print_head_setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`print_head_setting_id`),
  KEY `print_head_settings_location_id_foreign` (`location_id`),
  KEY `print_head_settings_company_id_foreign` (`company_id`),
  CONSTRAINT `print_head_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `print_head_settings_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.print_head_settings: ~0 rows (approximately)
DELETE FROM `print_head_settings`;

-- Dumping structure for table stawi_self_client.program_employees
DROP TABLE IF EXISTS `program_employees`;
CREATE TABLE IF NOT EXISTS `program_employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `program_employees_company_id_foreign` (`company_id`),
  CONSTRAINT `program_employees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.program_employees: ~0 rows (approximately)
DELETE FROM `program_employees`;

-- Dumping structure for table stawi_self_client.programs
DROP TABLE IF EXISTS `programs`;
CREATE TABLE IF NOT EXISTS `programs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `main_program` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `programs_main_program_foreign` (`main_program`),
  KEY `programs_company_id_foreign` (`company_id`),
  CONSTRAINT `programs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `programs_main_program_foreign` FOREIGN KEY (`main_program`) REFERENCES `programs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.programs: ~0 rows (approximately)
DELETE FROM `programs`;

-- Dumping structure for table stawi_self_client.projects
DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `main_project` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `status` enum('active','inactive','completed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_main_project_foreign` (`main_project`),
  KEY `projects_company_id_foreign` (`company_id`),
  CONSTRAINT `projects_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_main_project_foreign` FOREIGN KEY (`main_project`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.projects: ~0 rows (approximately)
DELETE FROM `projects`;

-- Dumping structure for table stawi_self_client.projects_to_employee_payroll_allocation
DROP TABLE IF EXISTS `projects_to_employee_payroll_allocation`;
CREATE TABLE IF NOT EXISTS `projects_to_employee_payroll_allocation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `percentage_allocated` decimal(5,2) NOT NULL,
  `allocation_start_date` date NOT NULL,
  `allocation_end_date` date NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_to_employee_payroll_allocation_employee_id_foreign` (`employee_id`),
  KEY `projects_to_employee_payroll_allocation_project_id_foreign` (`project_id`),
  KEY `projects_to_employee_payroll_allocation_company_id_foreign` (`company_id`),
  CONSTRAINT `projects_to_employee_payroll_allocation_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_to_employee_payroll_allocation_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `projects_to_employee_payroll_allocation_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.projects_to_employee_payroll_allocation: ~0 rows (approximately)
DELETE FROM `projects_to_employee_payroll_allocation`;

-- Dumping structure for table stawi_self_client.promotion
DROP TABLE IF EXISTS `promotion`;
CREATE TABLE IF NOT EXISTS `promotion` (
  `promotion_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `current_department` int(10) unsigned NOT NULL,
  `current_designation` int(10) unsigned NOT NULL,
  `current_salary` int(11) NOT NULL,
  `new_salary` int(11) NOT NULL,
  `promoted_department` int(10) unsigned NOT NULL,
  `promoted_designation` int(10) unsigned NOT NULL,
  `promotion_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`promotion_id`),
  KEY `promotion_location_id_foreign` (`location_id`),
  KEY `promotion_company_id_foreign` (`company_id`),
  CONSTRAINT `promotion_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `promotion_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.promotion: ~0 rows (approximately)
DELETE FROM `promotion`;

-- Dumping structure for table stawi_self_client.public_holiday_departments
DROP TABLE IF EXISTS `public_holiday_departments`;
CREATE TABLE IF NOT EXISTS `public_holiday_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `holiday_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `public_holiday_departments_holiday_id_department_id_unique` (`holiday_id`,`department_id`),
  KEY `public_holiday_departments_company_id_foreign` (`company_id`),
  CONSTRAINT `public_holiday_departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.public_holiday_departments: ~0 rows (approximately)
DELETE FROM `public_holiday_departments`;

-- Dumping structure for table stawi_self_client.public_holiday_leave_groups
DROP TABLE IF EXISTS `public_holiday_leave_groups`;
CREATE TABLE IF NOT EXISTS `public_holiday_leave_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `holiday_id` bigint(20) unsigned NOT NULL,
  `leave_group_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `public_holiday_leave_groups_holiday_id_leave_group_id_unique` (`holiday_id`,`leave_group_id`),
  KEY `public_holiday_leave_groups_company_id_foreign` (`company_id`),
  CONSTRAINT `public_holiday_leave_groups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.public_holiday_leave_groups: ~0 rows (approximately)
DELETE FROM `public_holiday_leave_groups`;

-- Dumping structure for table stawi_self_client.recruitment_settings
DROP TABLE IF EXISTS `recruitment_settings`;
CREATE TABLE IF NOT EXISTS `recruitment_settings` (
  `setting_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `recruitment_settings_setting_key_unique` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.recruitment_settings: ~0 rows (approximately)
DELETE FROM `recruitment_settings`;

-- Dumping structure for table stawi_self_client.recurrent_deductions
DROP TABLE IF EXISTS `recurrent_deductions`;
CREATE TABLE IF NOT EXISTS `recurrent_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `start_month` varchar(191) NOT NULL,
  `end_month` varchar(191) NOT NULL,
  `frequency` varchar(191) NOT NULL,
  `amount` double NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `approved_by` int(11) NOT NULL,
  `approval_status` int(11) NOT NULL COMMENT '1-approved, 0-not-approved',
  `status` int(11) NOT NULL COMMENT '1-active, 0-inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurrent_deductions_location_id_foreign` (`location_id`),
  KEY `recurrent_deductions_company_id_foreign` (`company_id`),
  CONSTRAINT `recurrent_deductions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recurrent_deductions_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.recurrent_deductions: ~0 rows (approximately)
DELETE FROM `recurrent_deductions`;

-- Dumping structure for table stawi_self_client.recurrent_employee_to_deductions
DROP TABLE IF EXISTS `recurrent_employee_to_deductions`;
CREATE TABLE IF NOT EXISTS `recurrent_employee_to_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recurrent_deduction_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurrent_employee_to_deductions_location_id_foreign` (`location_id`),
  KEY `recurrent_employee_to_deductions_company_id_foreign` (`company_id`),
  CONSTRAINT `recurrent_employee_to_deductions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recurrent_employee_to_deductions_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.recurrent_employee_to_deductions: ~0 rows (approximately)
DELETE FROM `recurrent_employee_to_deductions`;

-- Dumping structure for table stawi_self_client.regions
DROP TABLE IF EXISTS `regions`;
CREATE TABLE IF NOT EXISTS `regions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `regions_company_id_foreign` (`company_id`),
  CONSTRAINT `regions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.regions: ~0 rows (approximately)
DELETE FROM `regions`;

-- Dumping structure for table stawi_self_client.review_periods
DROP TABLE IF EXISTS `review_periods`;
CREATE TABLE IF NOT EXISTS `review_periods` (
  `period_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `period_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`period_id`),
  UNIQUE KEY `review_periods_period_name_unique` (`period_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.review_periods: ~0 rows (approximately)
DELETE FROM `review_periods`;

-- Dumping structure for table stawi_self_client.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.role_has_permissions: ~0 rows (approximately)
DELETE FROM `role_has_permissions`;

-- Dumping structure for table stawi_self_client.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  KEY `roles_location_id_foreign` (`location_id`),
  CONSTRAINT `roles_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.roles: ~0 rows (approximately)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `location_id`, `approval_status`, `date_approved`, `status`, `approved_by`) VALUES
	(1, 'SuperAdmin', 'web', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(2, 'HR Administrator', 'web', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(3, 'Employee', 'web', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(4, 'General Supervisors', 'web', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(5, 'ICT Support', 'web', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL),
	(6, 'Finance Admins', 'web', '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.salary_bonus_types
DROP TABLE IF EXISTS `salary_bonus_types`;
CREATE TABLE IF NOT EXISTS `salary_bonus_types` (
  `bonus_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_type_name` varchar(191) NOT NULL,
  `bonus_type_limit` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`bonus_type_id`),
  KEY `salary_bonus_types_location_id_foreign` (`location_id`),
  KEY `salary_bonus_types_company_id_foreign` (`company_id`),
  CONSTRAINT `salary_bonus_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_bonus_types_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.salary_bonus_types: ~0 rows (approximately)
DELETE FROM `salary_bonus_types`;

-- Dumping structure for table stawi_self_client.salary_bonuses
DROP TABLE IF EXISTS `salary_bonuses`;
CREATE TABLE IF NOT EXISTS `salary_bonuses` (
  `salary_bonus_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `amount` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month` varchar(191) NOT NULL,
  `date_issued` date DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`salary_bonus_id`),
  KEY `salary_bonuses_location_id_foreign` (`location_id`),
  KEY `salary_bonuses_company_id_foreign` (`company_id`),
  CONSTRAINT `salary_bonuses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_bonuses_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.salary_bonuses: ~0 rows (approximately)
DELETE FROM `salary_bonuses`;

-- Dumping structure for table stawi_self_client.salary_deduction_for_late_attendance
DROP TABLE IF EXISTS `salary_deduction_for_late_attendance`;
CREATE TABLE IF NOT EXISTS `salary_deduction_for_late_attendance` (
  `salary_deduction_for_late_attendance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_days` int(11) NOT NULL,
  `day_of_salary_deduction` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`salary_deduction_for_late_attendance_id`),
  KEY `salary_deduction_for_late_attendance_location_id_foreign` (`location_id`),
  KEY `salary_deduction_for_late_attendance_company_id_foreign` (`company_id`),
  CONSTRAINT `salary_deduction_for_late_attendance_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_deduction_for_late_attendance_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.salary_deduction_for_late_attendance: ~0 rows (approximately)
DELETE FROM `salary_deduction_for_late_attendance`;

-- Dumping structure for table stawi_self_client.salary_deductions
DROP TABLE IF EXISTS `salary_deductions`;
CREATE TABLE IF NOT EXISTS `salary_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `salary_details_id` bigint(20) NOT NULL DEFAULT 0,
  `employee_id` bigint(20) NOT NULL DEFAULT 0,
  `payroll_number` varchar(191) NOT NULL DEFAULT '0',
  `deduction_month` varchar(191) NOT NULL DEFAULT '0',
  `deduction_type` int(11) NOT NULL DEFAULT 0,
  `deduction_name` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_deductions_location_id_foreign` (`location_id`),
  KEY `salary_deductions_company_id_foreign` (`company_id`),
  CONSTRAINT `salary_deductions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_deductions_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.salary_deductions: ~0 rows (approximately)
DELETE FROM `salary_deductions`;

-- Dumping structure for table stawi_self_client.salary_details
DROP TABLE IF EXISTS `salary_details`;
CREATE TABLE IF NOT EXISTS `salary_details` (
  `salary_details_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `month_of_salary` varchar(20) NOT NULL,
  `basic_salary` int(11) NOT NULL DEFAULT 0,
  `total_allowance` int(11) NOT NULL DEFAULT 0,
  `total_deduction` int(11) NOT NULL DEFAULT 0,
  `total_late` int(11) NOT NULL DEFAULT 0,
  `total_late_amount` int(11) NOT NULL DEFAULT 0,
  `total_absence` int(11) NOT NULL DEFAULT 0,
  `total_absence_amount` int(11) NOT NULL DEFAULT 0,
  `overtime_rate` int(11) NOT NULL DEFAULT 0,
  `per_day_salary` int(11) NOT NULL DEFAULT 0,
  `total_over_time_hour` varchar(50) NOT NULL DEFAULT '00:00',
  `total_overtime_amount` int(11) NOT NULL DEFAULT 0,
  `hourly_rate` int(11) NOT NULL DEFAULT 0,
  `total_present` int(11) NOT NULL DEFAULT 0,
  `total_leave` int(11) NOT NULL DEFAULT 0,
  `total_working_days` int(11) NOT NULL DEFAULT 0,
  `net_salary` int(11) NOT NULL DEFAULT 0,
  `tax` int(11) NOT NULL DEFAULT 0,
  `taxable_salary` int(11) NOT NULL DEFAULT 0,
  `working_hour` varchar(191) NOT NULL DEFAULT '00:00',
  `gross_salary` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `comment` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `payroll_no` varchar(191) DEFAULT NULL,
  `gross_pay` varchar(191) DEFAULT NULL,
  `nssf_no` varchar(191) DEFAULT NULL,
  `nhif_no` varchar(191) DEFAULT NULL,
  `PAYE_tax` varchar(191) DEFAULT NULL,
  `public_holidays_pay` varchar(191) DEFAULT NULL,
  `employee_id_no` varchar(191) DEFAULT NULL,
  `kra_pin` varchar(191) DEFAULT NULL,
  `nhifRate` varchar(191) DEFAULT NULL,
  `nssf_amount` int(11) DEFAULT NULL,
  `no_of_holidays_worked` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `house_allowance` int(11) DEFAULT NULL,
  `transport_allowance` int(11) DEFAULT NULL,
  `banking_allowance` int(11) DEFAULT NULL,
  `deductible_advance` int(11) DEFAULT NULL,
  `payroll_claim` int(11) DEFAULT NULL,
  `pro_rata` int(11) DEFAULT NULL,
  `nssf_tier_1` varchar(191) DEFAULT NULL,
  `nssf_tier_2` varchar(191) DEFAULT NULL,
  `total_nssf` varchar(191) DEFAULT NULL,
  `airtime_untaxed` int(11) DEFAULT NULL,
  `ahl_amount` double DEFAULT NULL,
  `housing_levy_july` double DEFAULT NULL,
  `actual_gross_pay` int(11) DEFAULT 0 COMMENT 'gross pay after deducting lost days',
  `total_gross_pay` int(11) DEFAULT 0 COMMENT 'gross before lost days',
  `payment_period_start` datetime DEFAULT NULL,
  `payment_period_end` datetime DEFAULT NULL,
  `payout_channel` int(11) DEFAULT 0 COMMENT 'Payout channels are Banks, saccos etc',
  `payout_status` int(11) DEFAULT 0,
  `SHIF_amount` double DEFAULT 0,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `financial_year_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`salary_details_id`),
  KEY `salary_details_location_id_foreign` (`location_id`),
  KEY `salary_details_financial_year_id_foreign` (`financial_year_id`),
  KEY `salary_details_company_id_foreign` (`company_id`),
  CONSTRAINT `salary_details_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_details_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`),
  CONSTRAINT `salary_details_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.salary_details: ~0 rows (approximately)
DELETE FROM `salary_details`;

-- Dumping structure for table stawi_self_client.salary_details_to_allowance
DROP TABLE IF EXISTS `salary_details_to_allowance`;
CREATE TABLE IF NOT EXISTS `salary_details_to_allowance` (
  `salary_details_to_allowance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salary_details_id` int(11) NOT NULL,
  `allowance_id` int(11) NOT NULL,
  `amount_of_allowance` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`salary_details_to_allowance_id`),
  KEY `salary_details_to_allowance_location_id_foreign` (`location_id`),
  KEY `salary_details_to_allowance_company_id_foreign` (`company_id`),
  CONSTRAINT `salary_details_to_allowance_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_details_to_allowance_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.salary_details_to_allowance: ~0 rows (approximately)
DELETE FROM `salary_details_to_allowance`;

-- Dumping structure for table stawi_self_client.salary_details_to_bonuses
DROP TABLE IF EXISTS `salary_details_to_bonuses`;
CREATE TABLE IF NOT EXISTS `salary_details_to_bonuses` (
  `salary_details_to_bonuses_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salary_details_id` int(11) NOT NULL,
  `salary_bonus_id` int(11) NOT NULL,
  `amount_of_bonus` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bonus_name` varchar(191) DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`salary_details_to_bonuses_id`),
  KEY `salary_details_to_bonuses_location_id_foreign` (`location_id`),
  KEY `salary_details_to_bonuses_company_id_foreign` (`company_id`),
  CONSTRAINT `salary_details_to_bonuses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_details_to_bonuses_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.salary_details_to_bonuses: ~0 rows (approximately)
DELETE FROM `salary_details_to_bonuses`;

-- Dumping structure for table stawi_self_client.salary_details_to_deduction
DROP TABLE IF EXISTS `salary_details_to_deduction`;
CREATE TABLE IF NOT EXISTS `salary_details_to_deduction` (
  `salary_details_to_deduction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salary_details_id` int(11) NOT NULL,
  `deduction_id` int(11) NOT NULL,
  `amount_of_deduction` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`salary_details_to_deduction_id`),
  KEY `salary_details_to_deduction_location_id_foreign` (`location_id`),
  KEY `salary_details_to_deduction_company_id_foreign` (`company_id`),
  CONSTRAINT `salary_details_to_deduction_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_details_to_deduction_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.salary_details_to_deduction: ~0 rows (approximately)
DELETE FROM `salary_details_to_deduction`;

-- Dumping structure for table stawi_self_client.salary_details_to_leave
DROP TABLE IF EXISTS `salary_details_to_leave`;
CREATE TABLE IF NOT EXISTS `salary_details_to_leave` (
  `salary_details_to_leave_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salary_details_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `num_of_day` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`salary_details_to_leave_id`),
  KEY `salary_details_to_leave_location_id_foreign` (`location_id`),
  KEY `salary_details_to_leave_company_id_foreign` (`company_id`),
  CONSTRAINT `salary_details_to_leave_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_details_to_leave_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.salary_details_to_leave: ~0 rows (approximately)
DELETE FROM `salary_details_to_leave`;

-- Dumping structure for table stawi_self_client.services
DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_name` varchar(191) NOT NULL,
  `service_icon` varchar(191) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `services_location_id_foreign` (`location_id`),
  KEY `services_company_id_foreign` (`company_id`),
  CONSTRAINT `services_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `services_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.services: ~0 rows (approximately)
DELETE FROM `services`;

-- Dumping structure for procedure stawi_self_client.SP_getEmployeeInfo
DROP PROCEDURE IF EXISTS `SP_getEmployeeInfo`;
DELIMITER //
CREATE PROCEDURE `SP_getEmployeeInfo`(IN employeeId INT(10))
BEGIN
	       SELECT employee.*,user.`user_name` FROM employee 
            INNER JOIN `user` ON `user`.`id` = employee.`id`
            WHERE employee_id = employeeId;
        END//
DELIMITER ;

-- Dumping structure for procedure stawi_self_client.SP_getHoliday
DROP PROCEDURE IF EXISTS `SP_getHoliday`;
DELIMITER //
CREATE PROCEDURE `SP_getHoliday`(IN fromDate DATE, IN toDate DATE)
BEGIN SELECT from_date,to_date FROM holiday_details WHERE from_date >= fromDate AND to_date <=toDate; END//
DELIMITER ;

-- Dumping structure for procedure stawi_self_client.SP_getWeeklyHoliday
DROP PROCEDURE IF EXISTS `SP_getWeeklyHoliday`;
DELIMITER //
CREATE PROCEDURE `SP_getWeeklyHoliday`()
BEGIN
                SELECT day_name FROM weekly_holiday WHERE status = 1;
            END//
DELIMITER ;

-- Dumping structure for table stawi_self_client.staff_contracts
DROP TABLE IF EXISTS `staff_contracts`;
CREATE TABLE IF NOT EXISTS `staff_contracts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `hire_date` date DEFAULT NULL,
  `probation_start_date` date DEFAULT NULL,
  `probation_end_date` date DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `contract_document_draft` varchar(191) DEFAULT NULL,
  `contract_document_final` varchar(191) DEFAULT NULL,
  `contract_type` varchar(191) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `approval_status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_contracts_location_id_foreign` (`location_id`),
  KEY `staff_contracts_company_id_foreign` (`company_id`),
  CONSTRAINT `staff_contracts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_contracts_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.staff_contracts: ~0 rows (approximately)
DELETE FROM `staff_contracts`;

-- Dumping structure for table stawi_self_client.survey_answers
DROP TABLE IF EXISTS `survey_answers`;
CREATE TABLE IF NOT EXISTS `survey_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` bigint(20) unsigned NOT NULL,
  `survey_question_id` bigint(20) unsigned NOT NULL,
  `answer_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_answers_survey_id_foreign` (`survey_id`),
  KEY `survey_answers_survey_question_id_foreign` (`survey_question_id`),
  KEY `survey_answers_company_id_foreign` (`company_id`),
  CONSTRAINT `survey_answers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survey_answers_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survey_answers_survey_question_id_foreign` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.survey_answers: ~0 rows (approximately)
DELETE FROM `survey_answers`;

-- Dumping structure for table stawi_self_client.survey_departments
DROP TABLE IF EXISTS `survey_departments`;
CREATE TABLE IF NOT EXISTS `survey_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_departments_survey_id_department_id_index` (`survey_id`,`department_id`),
  KEY `survey_departments_company_id_foreign` (`company_id`),
  CONSTRAINT `survey_departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.survey_departments: ~0 rows (approximately)
DELETE FROM `survey_departments`;

-- Dumping structure for table stawi_self_client.survey_locations
DROP TABLE IF EXISTS `survey_locations`;
CREATE TABLE IF NOT EXISTS `survey_locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_locations_survey_id_foreign` (`survey_id`),
  KEY `survey_locations_location_id_foreign` (`location_id`),
  KEY `survey_locations_company_id_foreign` (`company_id`),
  CONSTRAINT `survey_locations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survey_locations_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE CASCADE,
  CONSTRAINT `survey_locations_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.survey_locations: ~0 rows (approximately)
DELETE FROM `survey_locations`;

-- Dumping structure for table stawi_self_client.survey_questions
DROP TABLE IF EXISTS `survey_questions`;
CREATE TABLE IF NOT EXISTS `survey_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` bigint(20) unsigned NOT NULL,
  `question_text` text DEFAULT NULL,
  `answer_type` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_questions_survey_id_foreign` (`survey_id`),
  KEY `survey_questions_company_id_foreign` (`company_id`),
  CONSTRAINT `survey_questions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survey_questions_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.survey_questions: ~0 rows (approximately)
DELETE FROM `survey_questions`;

-- Dumping structure for table stawi_self_client.survey_regions
DROP TABLE IF EXISTS `survey_regions`;
CREATE TABLE IF NOT EXISTS `survey_regions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` bigint(20) unsigned NOT NULL,
  `region_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_regions_survey_id_foreign` (`survey_id`),
  KEY `survey_regions_region_id_foreign` (`region_id`),
  KEY `survey_regions_company_id_foreign` (`company_id`),
  CONSTRAINT `survey_regions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survey_regions_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survey_regions_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.survey_regions: ~0 rows (approximately)
DELETE FROM `survey_regions`;

-- Dumping structure for table stawi_self_client.survey_response_comments
DROP TABLE IF EXISTS `survey_response_comments`;
CREATE TABLE IF NOT EXISTS `survey_response_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` bigint(20) unsigned NOT NULL,
  `survey_question_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `survey_response_id` bigint(20) unsigned NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_response_comments_survey_id_foreign` (`survey_id`),
  KEY `survey_response_comments_survey_question_id_foreign` (`survey_question_id`),
  KEY `survey_response_comments_survey_response_id_foreign` (`survey_response_id`),
  KEY `survey_response_comments_company_id_foreign` (`company_id`),
  CONSTRAINT `survey_response_comments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survey_response_comments_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survey_response_comments_survey_question_id_foreign` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `survey_response_comments_survey_response_id_foreign` FOREIGN KEY (`survey_response_id`) REFERENCES `employee_survey_responses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.survey_response_comments: ~0 rows (approximately)
DELETE FROM `survey_response_comments`;

-- Dumping structure for table stawi_self_client.surveys
DROP TABLE IF EXISTS `surveys`;
CREATE TABLE IF NOT EXISTS `surveys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `google_form_id` varchar(191) DEFAULT NULL,
  `form_url` varchar(191) DEFAULT NULL,
  `edit_url` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `department_id` int(10) unsigned DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `region_id` bigint(20) unsigned DEFAULT NULL,
  `gender_id` bigint(20) unsigned DEFAULT NULL,
  `target_gender` tinyint(4) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `surveys_slug_unique` (`slug`),
  UNIQUE KEY `surveys_google_form_id_unique` (`google_form_id`),
  KEY `surveys_department_id_foreign` (`department_id`),
  KEY `surveys_location_id_foreign` (`location_id`),
  KEY `surveys_region_id_foreign` (`region_id`),
  KEY `surveys_company_id_foreign` (`company_id`),
  CONSTRAINT `surveys_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `surveys_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE CASCADE,
  CONSTRAINT `surveys_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE CASCADE,
  CONSTRAINT `surveys_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.surveys: ~0 rows (approximately)
DELETE FROM `surveys`;

-- Dumping structure for table stawi_self_client.system_settings
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sms_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `inapp_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.system_settings: ~0 rows (approximately)
DELETE FROM `system_settings`;

-- Dumping structure for table stawi_self_client.tax_rule
DROP TABLE IF EXISTS `tax_rule`;
CREATE TABLE IF NOT EXISTS `tax_rule` (
  `tax_rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `amount` int(11) NOT NULL,
  `percentage_of_tax` double NOT NULL,
  `gender` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `max_amount` double NOT NULL,
  `min_amount` double NOT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`tax_rule_id`),
  KEY `tax_rule_location_id_foreign` (`location_id`),
  KEY `tax_rule_company_id_foreign` (`company_id`),
  CONSTRAINT `tax_rule_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tax_rule_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.tax_rule: ~0 rows (approximately)
DELETE FROM `tax_rule`;

-- Dumping structure for table stawi_self_client.teams
DROP TABLE IF EXISTS `teams`;
CREATE TABLE IF NOT EXISTS `teams` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teams_location_id_foreign` (`location_id`),
  KEY `teams_company_id_foreign` (`company_id`),
  CONSTRAINT `teams_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teams_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.teams: ~0 rows (approximately)
DELETE FROM `teams`;

-- Dumping structure for table stawi_self_client.termination
DROP TABLE IF EXISTS `termination`;
CREATE TABLE IF NOT EXISTS `termination` (
  `termination_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `terminate_to` int(10) unsigned NOT NULL,
  `terminate_by` int(10) unsigned NOT NULL,
  `termination_type` varchar(191) NOT NULL,
  `subject` varchar(191) NOT NULL,
  `notice_date` date NOT NULL,
  `termination_date` date NOT NULL,
  `eligible_for_rehire` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `arrears_paid` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=Not Paid, 1=Arrears Paid',
  `reinstatement_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=Not Reinstated, 1=Reinstated',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `national_id` varchar(191) NOT NULL,
  `entry_type` varchar(191) DEFAULT 'auto',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `stage1_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage2_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage3_approval_status` int(11) NOT NULL DEFAULT 0,
  `stage1_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage2_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage3_approved_by` bigint(20) unsigned DEFAULT NULL,
  `stage1_approval_comments` varchar(191) DEFAULT NULL,
  `stage2_approval_comments` varchar(191) DEFAULT NULL,
  `stage3_approval_comments` varchar(191) DEFAULT NULL,
  `stage1_approval_date` datetime DEFAULT NULL,
  `stage2_approval_date` datetime DEFAULT NULL,
  `stage3_approval_date` datetime DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`termination_id`),
  KEY `termination_created_by_foreign` (`created_by`),
  KEY `termination_stage1_approved_by_foreign` (`stage1_approved_by`),
  KEY `termination_stage2_approved_by_foreign` (`stage2_approved_by`),
  KEY `termination_stage3_approved_by_foreign` (`stage3_approved_by`),
  KEY `termination_location_id_foreign` (`location_id`),
  KEY `termination_company_id_foreign` (`company_id`),
  CONSTRAINT `termination_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `termination_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `termination_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  CONSTRAINT `termination_stage1_approved_by_foreign` FOREIGN KEY (`stage1_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `termination_stage2_approved_by_foreign` FOREIGN KEY (`stage2_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `termination_stage3_approved_by_foreign` FOREIGN KEY (`stage3_approved_by`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.termination: ~0 rows (approximately)
DELETE FROM `termination`;

-- Dumping structure for table stawi_self_client.termination_checklist_actions
DROP TABLE IF EXISTS `termination_checklist_actions`;
CREATE TABLE IF NOT EXISTS `termination_checklist_actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `termination_checklist_id` bigint(20) unsigned NOT NULL,
  `termination_id` bigint(20) unsigned NOT NULL,
  `actioned_by` bigint(20) unsigned NOT NULL,
  `comment` longtext DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `termination_checklist_actions_company_id_foreign` (`company_id`),
  CONSTRAINT `termination_checklist_actions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.termination_checklist_actions: ~0 rows (approximately)
DELETE FROM `termination_checklist_actions`;

-- Dumping structure for table stawi_self_client.termination_checklists
DROP TABLE IF EXISTS `termination_checklists`;
CREATE TABLE IF NOT EXISTS `termination_checklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `checklist_name` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `comment` text DEFAULT NULL,
  `cleared_by` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `termination_checklists_cleared_by_foreign` (`cleared_by`),
  KEY `termination_checklists_created_by_foreign` (`created_by`),
  KEY `termination_checklists_company_id_foreign` (`company_id`),
  CONSTRAINT `termination_checklists_cleared_by_foreign` FOREIGN KEY (`cleared_by`) REFERENCES `user` (`id`),
  CONSTRAINT `termination_checklists_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `termination_checklists_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.termination_checklists: ~0 rows (approximately)
DELETE FROM `termination_checklists`;

-- Dumping structure for table stawi_self_client.termination_docs
DROP TABLE IF EXISTS `termination_docs`;
CREATE TABLE IF NOT EXISTS `termination_docs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `termination_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `document_name` varchar(191) NOT NULL,
  `file_url` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `termination_docs_termination_id_foreign` (`termination_id`),
  KEY `termination_docs_employee_id_foreign` (`employee_id`),
  KEY `termination_docs_company_id_foreign` (`company_id`),
  CONSTRAINT `termination_docs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `termination_docs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `termination_docs_termination_id_foreign` FOREIGN KEY (`termination_id`) REFERENCES `termination` (`termination_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.termination_docs: ~0 rows (approximately)
DELETE FROM `termination_docs`;

-- Dumping structure for table stawi_self_client.training_attendants
DROP TABLE IF EXISTS `training_attendants`;
CREATE TABLE IF NOT EXISTS `training_attendants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `training_id` bigint(20) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_attendants_company_id_foreign` (`company_id`),
  CONSTRAINT `training_attendants_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.training_attendants: ~0 rows (approximately)
DELETE FROM `training_attendants`;

-- Dumping structure for table stawi_self_client.training_facilitators
DROP TABLE IF EXISTS `training_facilitators`;
CREATE TABLE IF NOT EXISTS `training_facilitators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `contact_email` varchar(191) DEFAULT NULL,
  `contact_phone` varchar(191) DEFAULT NULL,
  `type` enum('internal','external') NOT NULL DEFAULT 'internal',
  `expertise` varchar(191) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_facilitators_company_id_foreign` (`company_id`),
  CONSTRAINT `training_facilitators_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.training_facilitators: ~0 rows (approximately)
DELETE FROM `training_facilitators`;

-- Dumping structure for table stawi_self_client.training_info
DROP TABLE IF EXISTS `training_info`;
CREATE TABLE IF NOT EXISTS `training_info` (
  `training_info_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `training_type_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `subject` varchar(200) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text NOT NULL,
  `certificate` varchar(200) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`training_info_id`),
  KEY `training_info_location_id_foreign` (`location_id`),
  KEY `training_info_company_id_foreign` (`company_id`),
  CONSTRAINT `training_info_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `training_info_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.training_info: ~0 rows (approximately)
DELETE FROM `training_info`;

-- Dumping structure for table stawi_self_client.training_invitees
DROP TABLE IF EXISTS `training_invitees`;
CREATE TABLE IF NOT EXISTS `training_invitees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `training_id` bigint(20) unsigned NOT NULL,
  `status` tinyint(4) DEFAULT 0,
  `sent_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `responded_from` varchar(45) DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_invitees_training_id_foreign` (`training_id`),
  KEY `training_invitees_sent_by_foreign` (`sent_by`),
  KEY `training_invitees_company_id_foreign` (`company_id`),
  CONSTRAINT `training_invitees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `training_invitees_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `training_invitees_training_id_foreign` FOREIGN KEY (`training_id`) REFERENCES `trainings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.training_invitees: ~0 rows (approximately)
DELETE FROM `training_invitees`;

-- Dumping structure for table stawi_self_client.training_type
DROP TABLE IF EXISTS `training_type`;
CREATE TABLE IF NOT EXISTS `training_type` (
  `training_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `training_type_name` varchar(191) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(191) NOT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`training_type_id`),
  UNIQUE KEY `training_type_training_type_name_unique` (`training_type_name`),
  KEY `training_type_location_id_foreign` (`location_id`),
  KEY `training_type_company_id_foreign` (`company_id`),
  CONSTRAINT `training_type_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `training_type_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.training_type: ~0 rows (approximately)
DELETE FROM `training_type`;

-- Dumping structure for view stawi_self_client.training_view
DROP VIEW IF EXISTS `training_view`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `training_view` (
	`trainingID` BIGINT(20) UNSIGNED NOT NULL,
	`departmentID` INT(10) UNSIGNED NOT NULL,
	`employeeID` BIGINT(20) UNSIGNED NOT NULL,
	`trainingTypeId` BIGINT(20) UNSIGNED NOT NULL,
	`facilitatorID` BIGINT(20) UNSIGNED NOT NULL,
	`training_type` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`training` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`start_date` DATE NOT NULL,
	`end_date` DATE NOT NULL,
	`facilitator_type` ENUM('internal','external') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`facilitator_name` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`employee_department` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`employee_name` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`invited` INT(1) NOT NULL,
	`attended` INT(1) NOT NULL,
	`invited_status` TINYINT(4) NULL,
	`attendance_status` TINYINT(1) NULL
);

-- Dumping structure for table stawi_self_client.trainings
DROP TABLE IF EXISTS `trainings`;
CREATE TABLE IF NOT EXISTS `trainings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `training_type_id` bigint(20) unsigned NOT NULL,
  `facilitator_id` bigint(20) unsigned NOT NULL,
  `subject` varchar(191) NOT NULL,
  `attendance_type` enum('physical','online') NOT NULL DEFAULT 'physical',
  `attendance_link` varchar(191) DEFAULT NULL,
  `attendance_location` varchar(191) DEFAULT NULL,
  `start_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` date NOT NULL,
  `end_time` time DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  `attendance_approved` tinyint(1) NOT NULL,
  `invites_approved` tinyint(1) NOT NULL,
  `invite_approved_by` bigint(20) unsigned DEFAULT NULL,
  `attendance_approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trainings_company_id_foreign` (`company_id`),
  CONSTRAINT `trainings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.trainings: ~0 rows (approximately)
DELETE FROM `trainings`;

-- Dumping structure for table stawi_self_client.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(191) NOT NULL,
  `msisdn` varchar(191) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(200) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` date DEFAULT NULL,
  `google_id` varchar(191) DEFAULT NULL,
  `google_ids` text DEFAULT NULL,
  `token` text DEFAULT NULL,
  `google_access_token` varchar(191) DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `expires_in` int(11) DEFAULT NULL,
  `first_name` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `verification_code` varchar(191) DEFAULT NULL,
  `verification_code_expiry_date` timestamp NULL DEFAULT NULL,
  `password_expires_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_user_name_unique` (`user_name`),
  UNIQUE KEY `user_email_unique` (`email`),
  UNIQUE KEY `user_google_id_unique` (`google_id`),
  KEY `user_location_id_foreign` (`location_id`),
  KEY `user_company_id_foreign` (`company_id`),
  CONSTRAINT `user_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.user: ~0 rows (approximately)
DELETE FROM `user`;
INSERT INTO `user` (`id`, `role_id`, `user_name`, `email`, `msisdn`, `email_verified_at`, `password`, `status`, `remember_token`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `password_changed_at`, `google_id`, `google_ids`, `token`, `google_access_token`, `refresh_token`, `expires_in`, `first_name`, `last_name`, `location_id`, `verification_code`, `verification_code_expiry_date`, `password_expires_at`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, 0, 'support_stawi', 'support@stawitech.com', NULL, NULL, '$2y$10$JYHSnNVgKocG387c55PWTuRp78M1lCw1yCjpqiJGEDbSzoSJLJk.6', 1, 'YT7i0WreHu', 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Support', 'StawiTech', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
	(2, 0, 'SuperAdmin', 'admin@testrunner.co.ke', NULL, NULL, '$2y$10$JdaBjy4bxmnGMEe8frb32OUMobFHBoVU8d1fjUMASxTdEh0.svCZi', 1, 'xhmAxG0pBH', 1, 1, '2026-05-19 15:07:03', '2026-05-19 15:07:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Super', 'Admin', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
	(3, 0, 'smaloba3', 'smaloba3@gmail.com', NULL, NULL, '$2y$10$C7h/F3/TcvJkb/OCVRIOF.1ncmj4JYHwyKjtOPnPd2XYk18Et5mha', 1, 'Nlo4lK6ZVw', 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sam', 'Maloba', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
	(4, 0, 'jchengasia', 'jchengasia@stawitech.com', NULL, NULL, '$2y$10$iaWfon70ThAE5fxF.MRPv.deDRLhbz5p8QRqXSf3y8CvrANh5y6OK', 1, 'YP65o6E1Ee', 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Joseph', 'Chengasia', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
	(5, 0, 'gkoech', 'gkoech@stawitech.com', NULL, NULL, '$2y$10$xRdOEz5nLU04js/X8Go.1epCkUKMZ091L9/uvYmRFMI487MoTq19S', 1, 'cSOC5zIgRD', 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Grace', 'Koech', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
	(6, 0, 'cogara', 'cogara@stawitech.com', NULL, NULL, '$2y$10$KRVmUNse2s8CnA/qTs6LLOVIcdF.ZaWUjYAr2EsxKn9mKqeZ9LIxS', 1, '0LcSSzJv9A', 1, 1, '2026-05-19 15:07:04', '2026-05-19 15:07:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Collins', 'Ogara', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL);

-- Dumping structure for table stawi_self_client.vehicle_assignments
DROP TABLE IF EXISTS `vehicle_assignments`;
CREATE TABLE IF NOT EXISTS `vehicle_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `assigned_from` date NOT NULL,
  `assigned_to` date DEFAULT NULL,
  `assignment_reason` text DEFAULT NULL,
  `return_reason` text DEFAULT NULL,
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `returned_by` bigint(20) unsigned DEFAULT NULL,
  `returned_at` datetime DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_assignments_vehicle_id_assigned_from_index` (`vehicle_id`,`assigned_from`),
  KEY `vehicle_assignments_employee_id_assigned_from_index` (`employee_id`,`assigned_from`),
  KEY `vehicle_assignments_assigned_to_index` (`assigned_to`),
  KEY `vehicle_assignments_assigned_by_foreign` (`assigned_by`),
  KEY `vehicle_assignments_returned_by_foreign` (`returned_by`),
  KEY `vehicle_assignments_company_id_foreign` (`company_id`),
  CONSTRAINT `vehicle_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicle_assignments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicle_assignments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `vehicle_assignments_returned_by_foreign` FOREIGN KEY (`returned_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicle_assignments_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.vehicle_assignments: ~0 rows (approximately)
DELETE FROM `vehicle_assignments`;

-- Dumping structure for table stawi_self_client.vehicle_types
DROP TABLE IF EXISTS `vehicle_types`;
CREATE TABLE IF NOT EXISTS `vehicle_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_types_company_id_foreign` (`company_id`),
  KEY `vehicle_types_status_company_id_index` (`status`,`company_id`),
  CONSTRAINT `vehicle_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.vehicle_types: ~0 rows (approximately)
DELETE FROM `vehicle_types`;

-- Dumping structure for table stawi_self_client.vehicles
DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `registration_number` varchar(191) NOT NULL,
  `make` varchar(191) NOT NULL,
  `model` varchar(191) NOT NULL,
  `year_of_manufacture` year(4) DEFAULT NULL,
  `color` varchar(191) DEFAULT NULL,
  `chassis_number` varchar(191) DEFAULT NULL,
  `engine_number` varchar(191) DEFAULT NULL,
  `vehicle_type_id` bigint(20) unsigned DEFAULT NULL,
  `fuel_type` varchar(191) DEFAULT NULL,
  `fuel_capacity` decimal(10,2) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(12,2) DEFAULT NULL,
  `ownership_status` varchar(191) NOT NULL DEFAULT 'company',
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'active',
  `remarks` text DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_registration_number_unique` (`registration_number`),
  KEY `vehicles_vehicle_type_id_foreign` (`vehicle_type_id`),
  KEY `vehicles_location_id_foreign` (`location_id`),
  KEY `vehicles_company_id_foreign` (`company_id`),
  KEY `vehicles_status_company_id_index` (`status`,`company_id`),
  KEY `vehicles_registration_number_index` (`registration_number`),
  CONSTRAINT `vehicles_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicles_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  CONSTRAINT `vehicles_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.vehicles: ~0 rows (approximately)
DELETE FROM `vehicles`;

-- Dumping structure for table stawi_self_client.warning
DROP TABLE IF EXISTS `warning`;
CREATE TABLE IF NOT EXISTS `warning` (
  `warning_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warning_to` int(10) unsigned NOT NULL,
  `warning_type` varchar(191) NOT NULL,
  `subject` varchar(191) NOT NULL,
  `warning_by` int(10) unsigned NOT NULL,
  `warning_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`warning_id`),
  KEY `warning_location_id_foreign` (`location_id`),
  KEY `warning_company_id_foreign` (`company_id`),
  CONSTRAINT `warning_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warning_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.warning: ~0 rows (approximately)
DELETE FROM `warning`;

-- Dumping structure for table stawi_self_client.weekly_holiday
DROP TABLE IF EXISTS `weekly_holiday`;
CREATE TABLE IF NOT EXISTS `weekly_holiday` (
  `week_holiday_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `day_name` varchar(191) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`week_holiday_id`),
  UNIQUE KEY `weekly_holiday_day_name_unique` (`day_name`),
  KEY `weekly_holiday_location_id_foreign` (`location_id`),
  KEY `weekly_holiday_company_id_foreign` (`company_id`),
  CONSTRAINT `weekly_holiday_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `weekly_holiday_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.weekly_holiday: ~0 rows (approximately)
DELETE FROM `weekly_holiday`;

-- Dumping structure for table stawi_self_client.weekly_holiday_departments
DROP TABLE IF EXISTS `weekly_holiday_departments`;
CREATE TABLE IF NOT EXISTS `weekly_holiday_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `holiday_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `whd_holiday_dept_unique` (`holiday_id`,`department_id`),
  KEY `weekly_holiday_departments_company_id_foreign` (`company_id`),
  CONSTRAINT `weekly_holiday_departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.weekly_holiday_departments: ~0 rows (approximately)
DELETE FROM `weekly_holiday_departments`;

-- Dumping structure for table stawi_self_client.weekly_holiday_leave_groups
DROP TABLE IF EXISTS `weekly_holiday_leave_groups`;
CREATE TABLE IF NOT EXISTS `weekly_holiday_leave_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `holiday_id` bigint(20) unsigned NOT NULL,
  `leave_group_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `status` varchar(50) DEFAULT NULL COMMENT 'Status of the record',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `whd_holiday_lvgrp_unique` (`holiday_id`,`leave_group_id`),
  KEY `weekly_holiday_leave_groups_company_id_foreign` (`company_id`),
  CONSTRAINT `weekly_holiday_leave_groups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.weekly_holiday_leave_groups: ~0 rows (approximately)
DELETE FROM `weekly_holiday_leave_groups`;

-- Dumping structure for table stawi_self_client.white_listed_ips
DROP TABLE IF EXISTS `white_listed_ips`;
CREATE TABLE IF NOT EXISTS `white_listed_ips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_setting_id` int(11) DEFAULT 0,
  `white_listed_ip` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `white_listed_ips_location_id_foreign` (`location_id`),
  KEY `white_listed_ips_company_id_foreign` (`company_id`),
  CONSTRAINT `white_listed_ips_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `white_listed_ips_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.white_listed_ips: ~0 rows (approximately)
DELETE FROM `white_listed_ips`;

-- Dumping structure for table stawi_self_client.work_shift
DROP TABLE IF EXISTS `work_shift`;
CREATE TABLE IF NOT EXISTS `work_shift` (
  `work_shift_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shift_name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `late_count_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `overtime_count_time` time DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `approval_status` int(11) DEFAULT 0 COMMENT 'Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft',
  `date_approved` timestamp NULL DEFAULT NULL COMMENT 'Date when the record was approved',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User ID who approved the record',
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`work_shift_id`),
  KEY `work_shift_location_id_foreign` (`location_id`),
  KEY `work_shift_company_id_foreign` (`company_id`),
  CONSTRAINT `work_shift_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `work_shift_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table stawi_self_client.work_shift: ~0 rows (approximately)
DELETE FROM `work_shift`;
INSERT INTO `work_shift` (`work_shift_id`, `shift_name`, `start_time`, `end_time`, `late_count_time`, `created_at`, `updated_at`, `overtime_count_time`, `deleted_at`, `status`, `location_id`, `approval_status`, `date_approved`, `approved_by`, `company_id`) VALUES
	(1, 'Normal Shift', '07:30:00', '16:30:00', '07:50:00', '2026-05-19 15:07:02', '2026-05-19 15:07:02', '16:50:00', NULL, 1, NULL, 0, NULL, NULL, NULL);

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `training_view`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `training_view` AS SELECT
                tr.id AS trainingID,
                d.department_id AS departmentID,
                e.employee_id AS employeeID,
                tr.training_type_id AS trainingTypeId,
                tf.id AS facilitatorID,
                tt.training_type_name AS training_type,
                tr.subject AS training,
                tr.start_date,
                tr.end_date,
                tf.type AS facilitator_type,
                tf.name AS facilitator_name,
                d.department_name AS employee_department,
                CONCAT(e.first_name, ' ', COALESCE(e.middle_name, ''), ' ', e.last_name) AS employee_name,
                CASE WHEN ti.employee_id IS NOT NULL THEN 1 ELSE 0 END AS invited,
                CASE WHEN ta.employee_id IS NOT NULL THEN 1 ELSE 0 END AS attended,
                ti.status AS invited_status,
                ta.status AS attendance_status
            FROM (
                SELECT training_id, employee_id
                FROM training_invitees
                UNION
                SELECT training_id, employee_id
                FROM training_attendants
            ) re
            LEFT JOIN training_invitees ti ON re.training_id = ti.training_id AND re.employee_id = ti.employee_id
            LEFT JOIN training_attendants ta ON re.training_id = ta.training_id AND re.employee_id = ta.employee_id
            JOIN trainings tr ON re.training_id = tr.id
            JOIN training_type tt ON tr.training_type_id = tt.training_type_id
            JOIN training_facilitators tf ON tr.facilitator_id = tf.id
            JOIN employee e ON re.employee_id = e.employee_id
            JOIN department d ON e.department_id = d.department_id 
;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
