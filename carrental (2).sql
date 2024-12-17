-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2024 at 11:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carrental`
--

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

CREATE TABLE `availability` (
  `Vehicle_id` int(4) NOT NULL,
  `Available_start` date DEFAULT NULL,
  `Available_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`Vehicle_id`, `Available_start`, `Available_end`) VALUES
(1012, '2018-04-20', '2018-05-20'),
(1013, '2018-04-24', '2018-05-20'),
(1014, '2018-04-21', '2018-05-20'),
(1015, '2018-04-20', '2018-05-20'),
(1016, '2018-04-20', '2018-05-20'),
(1017, '2018-04-20', '2018-05-20'),
(1019, '2018-04-20', '2018-05-20'),
(1020, '2018-04-20', '2018-05-20'),
(1021, '2018-04-20', '2018-05-20'),
(1022, '2018-04-25', '2018-05-20'),
(1023, '2018-04-20', '2018-05-20'),
(1024, '2018-04-24', '2018-05-20'),
(1025, '2018-04-20', '2018-05-20'),
(1026, '2018-04-20', '2018-05-20'),
(1027, '2018-04-25', '2018-05-20'),
(1028, '2018-04-20', '2018-05-20'),
(1029, '2018-04-20', '2018-05-20'),
(1030, '2018-04-25', '2018-05-20'),
(1031, '2018-04-20', '2018-05-20'),
(1032, '2018-04-20', '2018-05-20'),
(1033, '2018-04-20', '2018-05-20'),
(1034, '2018-04-20', '2018-05-20'),
(1035, '2018-04-20', '2018-05-20'),
(1036, '2018-04-21', '2018-05-21'),
(1037, '2018-04-21', '2018-05-21');

-- --------------------------------------------------------

--
-- Table structure for table `car`
--

CREATE TABLE `car` (
  `Vehicle_id` int(4) NOT NULL,
  `Model` varchar(255) NOT NULL,
  `Year` decimal(4,0) NOT NULL,
  `Type_id` int(11) DEFAULT NULL,
  `Make` varchar(255) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `Img` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `FuelConsumption` decimal(5,2) DEFAULT NULL,
  `EngineSize` varchar(50) DEFAULT NULL,
  `RentalPrice` decimal(10,2) DEFAULT NULL,
  `RentalCompany` varchar(255) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `RentalConditions` text DEFAULT NULL,
  `Mileage` int(11) DEFAULT NULL,
  `Accessories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Accessories`)),
  `Functionalities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Functionalities`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `car`
--

INSERT INTO `car` (`Vehicle_id`, `Model`, `Year`, `Type_id`, `Make`, `Type`, `Img`, `Description`, `FuelConsumption`, `EngineSize`, `RentalPrice`, `RentalCompany`, `Address`, `RentalConditions`, `Mileage`, `Accessories`, `Functionalities`) VALUES
(1038, 'Chevrolet', 1998, 1, 'Camaro', 'Convertible', 'https://res.cloudinary.com/dkqxaid79/image/upload/v1695483660/cars/chevrolet_camaro_htdc8i.jpg', 'The Chevrolet Camaro is an American muscle car legend with a rich heritage, boasting aggressive styling, powerful engines, and exhilarating performance.', 13.50, '5.7L V8', 200.00, 'Muscle Car Rentals', '123 Example Street, Lviv, Ghana', 'Minimum age: 25\nValid driver\'s license\nSecurity deposit and insurance required', 6330, '[\"Leather upholstery\", \"Bose premium sound system\", \"Performance suspension\"]', '[\"Rear-Wheel Drive\", \"Limited-slip differential\", \"Power-operated convertible top\"]'),
(1039, 'Mercedes-Benz', 2006, 1, 'SLK-Class', 'Convertible', 'https://res.cloudinary.com/dkqxaid79/image/upload/v1695483659/cars/mercedes_slk_class_rimgix.jpg', 'The Mercedes-Benz SLK-Class is a luxurious and sporty convertible that offers a perfect balance of style, performance, and refinement, delivering an enjoyable open-top driving experience.', 9.80, '3.5L V6', 250.00, 'Luxury Car Rentals', '456 Example Avenue, Kyiv, Ghana', 'Minimum age: 25\nValid driver\'s license\nSecurity deposit and insurance required', 6280, '[\"Airscarf neck-level heating\", \"Harman Kardon surround sound system\", \"Magic Sky Control panoramic roof\"]', '[\"Rear-Wheel Drive\", \"Dynamic Stability Control\", \"Retractable hardtop roof\"]'),
(1040, 'Chrysler', 2020, 1, 'Voyager', 'Van/Minivan', 'https://res.cloudinary.com/dkqxaid79/image/upload/v1695483659/cars/chrysler_voyager_xt64zr.jpg', 'The Chrysler Voyager is a practical and versatile van/minivan that provides comfortable seating, ample cargo space, and a range of convenient features for family-oriented transportation.', 8.20, '3.6L V6', 100.00, 'Family Car Rentals', '789 Example Boulevard, Odesa, Ghana', 'Minimum age: 21\nValid driver\'s license\nSecurity deposit and insurance required', 5807, '[\"Stow \'n Go seating\", \"Uconnect infotainment system\", \"Blind Spot Monitoring\"]', '[\"Front-Wheel Drive\", \"Electronic Stability Control\", \"Power sliding side doors\"]'),
(1041, 'Kia', 2020, 1, 'Rio', 'Hatchback', 'https://res.cloudinary.com/dkqxaid79/image/upload/v1695483659/cars/kia_rio_hatchback_nq64ia.jpg', 'The Kia Rio is a compact and fuel-efficient hatchback that offers a blend of affordability, reliability, and practicality, making it an excellent choice for urban commuting and everyday driving.', 6.20, '1.6L 4-cylinder', 50.00, 'Economy Car Rentals', '321 Example Lane, Kharkiv, Ghana', 'Minimum age: 21\nValid driver\'s license\nSecurity deposit and insurance required', 4618, '[\"Apple CarPlay and Android Auto integration\", \"Smart Key with Push Button Start\", \"Automatic climate control\"]', '[\"Front-Wheel Drive\", \"Electronic Stability Control\", \"Rearview camera\"]'),
(1042, 'Volvo', 2020, 1, 'XC60', 'SUV', 'https://res.cloudinary.com/dkqxaid79/image/upload/v1695483660/cars/volvo_xc60_2_lvq5e5.jpg', 'The Volvo XC60 is a luxurious and versatile SUV that combines Scandinavian design, advanced safety features, and a comfortable driving experience, making it an ideal choice for families and adventure enthusiasts.', 8.50, '2.0L 4-cylinder', 150.00, 'Premium Car Rentals', '987 Example Road, Dnipro, Ghana', 'Minimum age: 25\nValid driver\'s license\nSecurity deposit and insurance required', 6618, '[\"Leather upholstery\", \"Panoramic sunroof\", \"Harman Kardon premium sound system\"]', '[\"All-Wheel Drive\", \"City Safety collision avoidance technology\", \"Power tailgate\"]'),
(1043, 'Land Rover', 2020, 1, 'Range Rover Sport', 'SUV', 'https://res.cloudinary.com/dkqxaid79/image/upload/v1695483659/cars/land_rover_range_rover_sport_zzlwby.jpg', 'The Land Rover Range Rover Sport is a premium SUV that offers a perfect blend of luxury, off-road capability, and dynamic performance, providing a refined driving experience both on and off the road.', 11.80, '3.0L V6', 300.00, 'Luxury SUV Rentals', '654 Example Street, Lviv, Ghana', 'Minimum age: 25\nValid driver\'s license\nSecurity deposit and insurance required', 4856, '[\"Premium Windsor leather seats\", \"Meridian surround sound system\", \"Adaptive Dynamics suspension\"]', '[\"All-Wheel Drive\", \"Terrain Response 2 system\", \"Power-operated gesture tailgate\"]'),
(1044, 'Kia', 2020, 1, 'Rio', 'Sedan, Hatchback', 'https://images.unsplash.com/photo-1592805723127-004b174a1798?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80', 'The Kia Rio is a versatile and fuel-efficient vehicle available in both sedan and hatchback body styles, offering a comfortable cabin, modern features, and a smooth driving experience, making it an attractive choice for urban commuters.', 6.20, '1.6L 4-cylinder', 50.00, 'Economy Car Rentals', '321 Lane, Kharkiv, Ghana', 'Minimum age: 21\nValid driver\'s license\nSecurity deposit and insurance required', 6234, '[\"Apple CarPlay and Android Auto integration\", \"Smart Key with Push Button Start\", \"Automatic climate control\"]', '[\"Front-Wheel Drive\", \"Electronic Stability Control\", \"Rearview camera\"]'),
(1045, 'Mercedes-Benz', 2022, 1, 'S-Class', 'Sedan', 'https://images.unsplash.com/photo-1657976763238-baab2fee9c53?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80', 'The Mercedes-Benz S-Class is the epitome of luxury and technology, offering a plush ride and cutting-edge features.', 9.20, '4.0L V8', 75.00, 'Luxury Car Rentals', '456 Example Boulevard, Kiev, Ghana', 'Minimum age: 25\nValid driver\'s license\nSecurity deposit required', 2547, '[\"Nappa leather seats\", \"MBUX infotainment system\", \"Adaptive cruise control\"]', '[\"Magic Body Control\", \"Voice-activated controls\", \"Lane-keeping assist\"]'),
(1046, 'Tesla', 2021, 1, 'Model 3', 'Electric', 'https://images.unsplash.com/photo-1561580125-028ee3bd62eb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80', 'The Tesla Model 3 is a popular electric sedan known for its impressive range and advanced autopilot features.', 0.00, 'Electric', 60.00, 'Electric Car Rentals', '789 Avenue, Kiev, Ghana', 'Minimum age: 21\nValid driver\'s license\nSecurity deposit required', 3200, '[\"Premium interior\", \"Autopilot\", \"17-inch touchscreen display\"]', '[\"Full self-driving capability\", \"Supercharger network access\", \"Summon feature\"]'),
(1047, 'Ford', 2022, 1, 'F-150', 'Truck', 'https://images.unsplash.com/photo-1590053936004-faca6038bfec?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80', 'The Ford F-150 is a rugged and capable pickup truck known for its towing capacity and modern technology.', 15.70, '3.5L V6', 50.00, 'Truck Rentals', '101 Drive, Kiev, Ghana', 'Minimum age: 25\nValid driver\'s license\nSecurity deposit required', 4685, '[\"Leather-trimmed seats\", \"Sync 4 infotainment\", \"Pro Power Onboard generator\"]', '[\"Towing package\", \"Adaptive cruise control\", \"360-degree camera\"]'),
(1048, 'BMW', 2020, 1, '5 Series', 'Sedan', 'https://images.unsplash.com/photo-1652890041546-2de2829c43b5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80', 'The BMW 5 Series offers a perfect blend of performance and luxury, making it a top choice in the executive sedan segment.', 8.90, '2.0L Inline-4', 65.00, 'Luxury Car Rentals', '321 Hero Lane, Kiev, Ghana', 'Minimum age: 25\nValid driver\'s license\nSecurity deposit required', 3800, '[\"Dakota leather seats\", \"iDrive infotainment\", \"Harman Kardon sound system\"]', '[\"Heads-up display\", \"Gesture control\", \"Parking assistant\"]'),
(1049, 'Toyota', 2021, 1, 'Prius', 'Hybrid', 'https://images.unsplash.com/photo-1602343504619-b057fdc14213?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80', 'The Toyota Prius is an eco-friendly hybrid known for its exceptional fuel efficiency and low emissions.', 0.00, '1.8L Inline-4 Hybrid', 45.00, 'Green Car Rentals', '555 Green Road, Kiev, Ghana', 'Minimum age: 21\nValid driver\'s license\nSecurity deposit required', 6200, '[\"Fabric seats\", \"Toyota Safety Sense\", \"8-inch touchscreen\"]', '[\"Hybrid Synergy Drive\", \"Lane departure alert\", \"Smart Key System\"]'),
(1050, 'Audi', 2022, 1, 'Q5', 'SUV', 'https://images.unsplash.com/photo-1617195920950-1145bf9a9c72?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1965&q=80', 'The Audi Q5 is a versatile luxury SUV with a comfortable interior and advanced technology features.', 9.20, '2.0L Inline-4', 55.00, 'Luxury Car Rentals', '789 Audi Avenue, Kiev, Ghana', 'Minimum age: 25\nValid driver\'s license\nSecurity deposit required', 4200, '[\"Leather seats\", \"Audi Virtual Cockpit\", \"Bang & Olufsen sound system\"]', '[\"Quattro all-wheel drive\", \"Audi Pre Sense\", \"Adaptive cruise control\"]'),
(1051, 'Honda', 2021, 1, 'NSX', 'Hybrid', 'https://images.unsplash.com/photo-1560361586-8242b1fc06c5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1927&q=80', 'The Acura / Honda NSX is a reliable and fuel-efficient compact hybrid that\'s perfect for city driving.', 0.00, '2.0L Inline-4', 35.00, 'City Car Rentals', '987 Peace Place, Kiev, Ghana', 'Minimum age: 21\nValid driver\'s license\nSecurity deposit required', 5500, '[\"Cloth seats\", \"Honda Sensing suite\", \"Apple CarPlay/Android Auto\"]', '[\"Lane keeping assist\", \"Collision mitigation braking\", \"Rearview camera\"]'),
(1052, 'Jeep', 2022, 1, 'Wrangler', 'SUV', 'https://images.unsplash.com/photo-1613793479775-10283b5fe6bb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1932&q=80', 'The Jeep Wrangler is a rugged and off-road-capable SUV, perfect for outdoor adventures.', 11.80, '3.6L V6', 50.00, 'Adventure Car Rentals', '678 Jeep Road, Kiev, Ghana', 'Minimum age: 25\nValid driver\'s license\nSecurity deposit required', 3800, '[\"Cloth seats\", \"Uconnect infotainment\", \"Removable top and doors\"]', '[\"4x4 capability\", \"Trail-rated\", \"Off-road pages\"]');

-- --------------------------------------------------------

--
-- Table structure for table `cartype`
--

CREATE TABLE `cartype` (
  `Type_id` int(11) NOT NULL,
  `Weekly_rate` decimal(5,2) DEFAULT NULL,
  `Daily_rate` decimal(5,2) DEFAULT NULL,
  `Car_type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cartype`
--

INSERT INTO `cartype` (`Type_id`, `Weekly_rate`, `Daily_rate`, `Car_type`) VALUES
(1, 100.50, 10.50, 'van'),
(2, 100.00, 6.00, 'compact'),
(3, 300.50, 30.50, 'medium'),
(4, 400.50, 40.50, 'large'),
(5, 500.50, 50.50, 'suv'),
(6, 600.50, 60.50, 'truck');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `Idno` int(11) NOT NULL,
  `Phone` varchar(12) DEFAULT NULL,
  `Customer_type` varchar(20) DEFAULT NULL,
  `Initial` char(1) DEFAULT NULL,
  `Lname` varchar(20) DEFAULT NULL,
  `Cname` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`Idno`, `Phone`, `Customer_type`, `Initial`, `Lname`, `Cname`) VALUES
(12, '123-456-7890', 'Individual', 'S', 'Shree', NULL),
(13, '234-567-8901', 'Individual', 'R', 'Surya', NULL),
(14, '345-678-9012', 'Individual', 'P', 'Priya', NULL),
(15, '456-789-0123', 'Individual', 'U', 'Ramya', NULL),
(16, '567-890-1234', 'Individual', 'A', 'Kavya', NULL),
(17, '678-901-2345', 'Company', NULL, NULL, 'Google'),
(18, '789-012-3456', 'Company', NULL, NULL, 'Wiseloan'),
(19, '890-123-4567', 'Company', NULL, NULL, 'Redhat'),
(20, '901-234-5678', 'Company', NULL, NULL, 'WWE'),
(21, '101-101-1101', 'Company', NULL, NULL, 'EF Johnson'),
(22, '653-276-1234', 'Individual', 'a', 'avinash', NULL),
(23, '425-876-1324', 'Company', NULL, NULL, 'tcs');

-- --------------------------------------------------------

--
-- Table structure for table `owner`
--

CREATE TABLE `owner` (
  `Owner_id` int(11) NOT NULL,
  `Owner_type` varchar(20) DEFAULT NULL,
  `Cname` varchar(20) DEFAULT NULL,
  `Bname` varchar(20) DEFAULT NULL,
  `Fname` varchar(20) DEFAULT NULL,
  `Lname` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `owner`
--

INSERT INTO `owner` (`Owner_id`, `Owner_type`, `Cname`, `Bname`, `Fname`, `Lname`) VALUES
(11, 'Company', 'HPE', NULL, NULL, NULL),
(12, 'Company', 'Ericson', NULL, NULL, NULL),
(13, 'Bank', NULL, 'Axis', NULL, NULL),
(14, 'Bank', NULL, 'HDFC', NULL, NULL),
(15, 'Individual', NULL, NULL, 'Shree', 'Shilpa'),
(16, 'Individual', NULL, NULL, 'Surya', 'Ritin'),
(1001540443, 'Bank', NULL, 'Axis Bank', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rents`
--

CREATE TABLE `rents` (
  `Customer_id` int(11) NOT NULL,
  `Vehicle_id` int(4) NOT NULL,
  `Start_date` date DEFAULT NULL,
  `Return_date` date DEFAULT NULL,
  `Amount_due` decimal(10,2) DEFAULT NULL,
  `Noofdays` int(11) DEFAULT NULL,
  `Noofweeks` int(11) DEFAULT NULL,
  `Active` tinyint(1) DEFAULT NULL,
  `Scheduled` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `rents`
--

INSERT INTO `rents` (`Customer_id`, `Vehicle_id`, `Start_date`, `Return_date`, `Amount_due`, `Noofdays`, `Noofweeks`, `Active`, `Scheduled`) VALUES
(12, 1024, '2018-04-20', '2018-04-23', 100.50, 0, 1, 1, 0),
(12, 1027, '2018-04-20', '2018-04-24', 122.00, 4, 0, 1, 0),
(13, 1022, '2018-04-20', '2018-04-24', 122.00, 4, 0, 1, 0),
(14, 1013, '2018-04-20', '2018-04-23', 151.50, 3, 0, 1, 0),
(15, 1030, '2018-04-20', '2018-04-24', 42.00, 4, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_id` int(11) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('admin','customer') NOT NULL,
  `Customer_id` int(11) DEFAULT NULL,
  `Fname` varchar(50) NOT NULL,
  `Lname` varchar(50) NOT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_id`, `Password`, `Role`, `Customer_id`, `Fname`, `Lname`, `Email`, `Image`, `Phone`) VALUES
(1, 'password123', 'admin', NULL, 'John', 'Doe', 'john.doe@example.com', 'path/to/image1.jpg', '123-456-7890'),
(2, 'password456', 'customer', 12, 'Jane', 'Smith', 'jane.smith@example.com', 'path/to/image2.jpg', '234-567-8901'),
(3, 'password789', 'customer', 13, 'Alice', 'Brown', 'alice.brown@example.com', 'path/to/image3.jpg', '345-678-9012'),
(4, '$2y$10$UTywZA9t.eRcBXMlmkvdq.9LVQxwAywXyR8Ep50j7wZNtGPCKA1DC', 'customer', NULL, 'Whoopi', 'Lloyd', 'pyrizec@mailinator.com', NULL, NULL),
(5, '$2y$10$shJkxiLu9BbOVUIvq2vUleu4Ole5bdPC/LlCW76D.954y/l8QAntq', 'customer', NULL, 'Price', 'Britt', 'sumufoj@mailinator.com', NULL, NULL),
(6, '$2y$10$PqvhB8SU2P5Iwdz6r6gE/OPMzX5lC84EWFFfkhhX0J9FmlPWEAzmS', 'admin', NULL, 'Edem', 'Anagbah', 'anagbahedem@gmail.com', NULL, NULL),
(7, '$2y$10$Y/GZnTm/nJLEnEMKTEGULOQhkqbaOOwyZNcFrZ0y4.4R1A.DEFmsm', 'customer', NULL, 'Alika', 'Head', 'peminola@mailinator.com', NULL, NULL),
(8, '$2y$10$rCw5cUdDYna8neiBeQwkfO1ZXwRW3tzhZW8VSbNx1XFPjB6BB2zZ.', 'customer', NULL, 'Jerome', 'Reyes', 'cosuwucyda@mailinator.com', NULL, NULL),
(9, '$2y$10$NnBefi9Ui82bPSyHti.UGe.ixcXU9ZrUk1wsCzMDxW2FF9BxoQcku', 'customer', NULL, 'Barclay', 'Conner', 'getudix@mailinator.com', NULL, NULL),
(10, '$2y$10$Yragj8Om/opAeJD9PH9Dr.t6npaAuwaivXWtsCCsbPSZL6xvlhfYa', 'customer', NULL, 'Upton', 'Keller', 'duqavule@mailinator.com', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_owner`
--

CREATE TABLE `vehicle_owner` (
  `Vehicle_id` int(4) NOT NULL,
  `Owner_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `vehicle_owner`
--

INSERT INTO `vehicle_owner` (`Vehicle_id`, `Owner_id`) VALUES
(1012, 11),
(1013, 12),
(1014, 13),
(1015, 14),
(1016, 15),
(1017, 16),
(1019, 11),
(1020, 12),
(1021, 13),
(1022, 14),
(1023, 15),
(1024, 12),
(1025, 13),
(1026, 14),
(1027, 15),
(1028, 16),
(1029, 11),
(1030, 12),
(1031, 13),
(1032, 14),
(1033, 15),
(1034, 16),
(1035, 11),
(1036, 15),
(1037, 13);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `availability`
--
ALTER TABLE `availability`
  ADD PRIMARY KEY (`Vehicle_id`);

--
-- Indexes for table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`Vehicle_id`),
  ADD KEY `Type_id` (`Type_id`);

--
-- Indexes for table `cartype`
--
ALTER TABLE `cartype`
  ADD PRIMARY KEY (`Type_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`Idno`);

--
-- Indexes for table `owner`
--
ALTER TABLE `owner`
  ADD PRIMARY KEY (`Owner_id`);

--
-- Indexes for table `rents`
--
ALTER TABLE `rents`
  ADD PRIMARY KEY (`Customer_id`,`Vehicle_id`),
  ADD KEY `Vehicle_id` (`Vehicle_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_id`),
  ADD KEY `Customer_id` (`Customer_id`);

--
-- Indexes for table `vehicle_owner`
--
ALTER TABLE `vehicle_owner`
  ADD PRIMARY KEY (`Vehicle_id`,`Owner_id`),
  ADD KEY `Owner_id` (`Owner_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `car`
--
ALTER TABLE `car`
  MODIFY `Vehicle_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1053;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `Idno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `availability`
--
ALTER TABLE `availability`
  ADD CONSTRAINT `availability_ibfk_1` FOREIGN KEY (`Vehicle_id`) REFERENCES `car` (`Vehicle_id`);

--
-- Constraints for table `car`
--
ALTER TABLE `car`
  ADD CONSTRAINT `car_ibfk_1` FOREIGN KEY (`Type_id`) REFERENCES `cartype` (`Type_id`);

--
-- Constraints for table `rents`
--
ALTER TABLE `rents`
  ADD CONSTRAINT `rents_ibfk_1` FOREIGN KEY (`Customer_id`) REFERENCES `customer` (`Idno`),
  ADD CONSTRAINT `rents_ibfk_2` FOREIGN KEY (`Vehicle_id`) REFERENCES `car` (`Vehicle_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`Customer_id`) REFERENCES `customer` (`Idno`);

--
-- Constraints for table `vehicle_owner`
--
ALTER TABLE `vehicle_owner`
  ADD CONSTRAINT `vehicle_owner_ibfk_1` FOREIGN KEY (`Vehicle_id`) REFERENCES `car` (`Vehicle_id`),
  ADD CONSTRAINT `vehicle_owner_ibfk_2` FOREIGN KEY (`Owner_id`) REFERENCES `owner` (`Owner_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
