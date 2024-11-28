-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2024 at 07:36 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gms`
--

-- --------------------------------------------------------

--
-- Table structure for table `grad`
--

CREATE TABLE `grad` (
  `stud_id` varchar(12) NOT NULL,
  `subject_code` varchar(7) NOT NULL,
  `status` varchar(5) NOT NULL,
  `remarks` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `hop`
--

CREATE TABLE `hop` (
  `hop_id` int(5) NOT NULL,
  `hop_password` varchar(200) NOT NULL,
  `hop_name` varchar(200) NOT NULL,
  `hop_email` varchar(200) NOT NULL,
  `hop_phone_num` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hop`
--

INSERT INTO `hop` (`hop_id`, `hop_password`, `hop_name`, `hop_email`, `hop_phone_num`) VALUES
(1, 'azzad123', 'Azzad', 'azzad@gmail.com', '0134477753');

-- --------------------------------------------------------

--
-- Table structure for table `mentor`
--

CREATE TABLE `mentor` (
  `mentor_id` int(5) NOT NULL,
  `mentor_password` varchar(200) NOT NULL,
  `mentor_name` varchar(200) NOT NULL,
  `mentor_email` varchar(200) NOT NULL,
  `mentor_phone_num` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `stud_id` varchar(12) NOT NULL,
  `stud_password` varchar(12) NOT NULL,
  `stud_name` varchar(200) NOT NULL,
  `stud_email` varchar(200) NOT NULL,
  `stud_phone_num` varchar(15) NOT NULL,
  `stud_intake` varchar(4) NOT NULL,
  `mentor_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_code` varchar(7) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `subject_section` varchar(2) NOT NULL,
  `subject_credit_hour` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hop`
--
ALTER TABLE `hop`
  ADD PRIMARY KEY (`hop_id`);

--
-- Indexes for table `mentor`
--
ALTER TABLE `mentor`
  ADD PRIMARY KEY (`mentor_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`stud_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hop`
--
ALTER TABLE `hop`
  MODIFY `hop_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mentor`
--
ALTER TABLE `mentor`
  MODIFY `mentor_id` int(5) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
