-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Sep 27, 2025 at 12:45 AM
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
-- Database: `campus_recruitment_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_applications`
--

CREATE TABLE `tbl_applications` (
  `application_id` int(20) NOT NULL,
  `student_id` int(20) NOT NULL,
  `vacancy_id` int(20) NOT NULL,
  `applied_on` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_aptitude`
--

CREATE TABLE `tbl_aptitude` (
  `aptitude_id` int(11) NOT NULL,
  `vacancy_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_aptitude`
--

INSERT INTO `tbl_aptitude` (`aptitude_id`, `vacancy_id`, `time`) VALUES
(1, 1, 30);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_attendance`
--

CREATE TABLE `tbl_attendance` (
  `attendance_id` int(20) NOT NULL,
  `student_id` int(20) NOT NULL,
  `updated_at` date NOT NULL DEFAULT current_timestamp(),
  `application_id` int(20) NOT NULL,
  `type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_company`
--

CREATE TABLE `tbl_company` (
  `company_id` int(20) NOT NULL,
  `login_id` int(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `regno` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `phno` varchar(12) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_company`
--

INSERT INTO `tbl_company` (`company_id`, `login_id`, `name`, `regno`, `link`, `description`, `location`, `phno`, `status`, `created_at`) VALUES
(1, 3, 'gentrius solutions', ' ASFD12345566', 'http://gentriussoluions.com', 'sedfghnjmkhj', 'WERTYUIOGFXG', '5656565656', 'approved', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_course`
--

CREATE TABLE `tbl_course` (
  `course_id` int(20) NOT NULL,
  `course` varchar(100) NOT NULL,
  `duration` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_course`
--

INSERT INTO `tbl_course` (`course_id`, `course`, `duration`) VALUES
(1, 'BCA', 3),
(2, 'BBA', 3),
(20, 'MSc CS', 2),
(21, 'RTYUIKL', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_drive`
--

CREATE TABLE `tbl_drive` (
  `drive_id` int(20) NOT NULL,
  `company_id` int(20) NOT NULL,
  `prefered_date` date NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `admin_note` text NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_drive`
--

INSERT INTO `tbl_drive` (`drive_id`, `company_id`, `prefered_date`, `status`, `admin_note`, `description`, `created_at`) VALUES
(1, 1, '2024-09-03', 'approved', '', 'hi', '2023-09-06'),
(2, 1, '2025-11-26', 'approved', '', 'Technical interview', '2025-09-26');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback`
--

CREATE TABLE `tbl_feedback` (
  `feedback_id` int(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_feedback`
--

INSERT INTO `tbl_feedback` (`feedback_id`, `name`, `email`, `date`, `comment`) VALUES
(1, 'gentrius solutions', 'admin123@gmail.com', '2025-09-27', 'asdfghjk,.');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_login`
--

CREATE TABLE `tbl_login` (
  `login_id` int(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_login`
--

INSERT INTO `tbl_login` (`login_id`, `email`, `password`, `role`) VALUES
(1, 'admin123@gmail.com', 'admin123', 'admin'),
(3, 'gentriusS@gmail.com', 'QWERTY', 'company'),
(30, 'kanmani@gmail.com', 'zxcvbnm', 'student'),
(34, 'athulya123@gmail.com', 'athulya', 'student'),
(38, 'paru123@gmail.com', 'paru123', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notification`
--

CREATE TABLE `tbl_notification` (
  `notification_id` int(20) NOT NULL,
  `drive_id` int(20) NOT NULL,
  `posted_on` date DEFAULT current_timestamp(),
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_questions`
--

CREATE TABLE `tbl_questions` (
  `question_id` int(20) NOT NULL,
  `aptitude_id` int(20) NOT NULL,
  `question` text NOT NULL,
  `option1` text NOT NULL,
  `option2` text NOT NULL,
  `option3` text NOT NULL,
  `option4` text NOT NULL,
  `correct` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_questions`
--

INSERT INTO `tbl_questions` (`question_id`, `aptitude_id`, `question`, `option1`, `option2`, `option3`, `option4`, `correct`) VALUES
(1, 1, 'number of levels in se', '4', '2', '5', '2', '3'),
(2, 1, 'asdfthkj', '3', 'd', '3', 'r', '2'),
(3, 1, 'what is the color of blue', 'blue', 'yellow', 'white', 'orange', '1'),
(4, 1, 'how many fingers are there in a hand', '3', '4', '5', '8', '3'),
(5, 1, 'how many fingers are there in a hand', '3', '4', '5', '8', '3');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_result`
--

CREATE TABLE `tbl_result` (
  `result_id` int(20) NOT NULL,
  `vacancy_id` int(20) NOT NULL,
  `result_file` varchar(100) NOT NULL,
  `published_on` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reviews`
--

CREATE TABLE `tbl_reviews` (
  `review_id` int(20) NOT NULL,
  `student_id` int(20) NOT NULL,
  `company_id` int(20) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student`
--

CREATE TABLE `tbl_student` (
  `student_id` int(20) NOT NULL,
  `credential_id` int(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `admno` int(20) NOT NULL,
  `phonenumber` varchar(12) NOT NULL,
  `login_id` int(20) NOT NULL,
  `course_id` int(20) NOT NULL,
  `year` int(20) NOT NULL,
  `cgpa` float NOT NULL,
  `arrears` int(20) NOT NULL,
  `resume` varchar(100) NOT NULL,
  `photo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_student`
--

INSERT INTO `tbl_student` (`student_id`, `credential_id`, `name`, `dob`, `admno`, `phonenumber`, `login_id`, `course_id`, `year`, `cgpa`, `arrears`, `resume`, `photo`) VALUES
(24, 2, 'kanmani', '2005-10-02', 7890654, '8909878905', 30, 2, 2022, 6, 0, 'Megha M Resume N.pdf', 'Photo .jpg'),
(27, 23, 'Athulya', '2005-04-17', 12345678, '8285495622', 34, 1, 2023, 9, 0, 'Megha M Resume New.pdf', 'Photo .jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student_credentials`
--

CREATE TABLE `tbl_student_credentials` (
  `credential_id` int(20) NOT NULL,
  `admno` int(20) NOT NULL,
  `year` int(20) NOT NULL,
  `dob` date NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_student_credentials`
--

INSERT INTO `tbl_student_credentials` (`credential_id`, `admno`, `year`, `dob`, `name`) VALUES
(2, 7890654, 2022, '2005-10-02', 'kanmani'),
(5, 34567890, 2021, '2002-09-12', 'megha'),
(23, 12345678, 2023, '2005-04-17', 'athulya'),
(25, 2345689, 2025, '2005-03-12', 'meera');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student_response`
--

CREATE TABLE `tbl_student_response` (
  `response_id` int(20) NOT NULL,
  `question_id` int(20) NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vacancies`
--

CREATE TABLE `tbl_vacancies` (
  `vacancy_id` int(20) NOT NULL,
  `drive_id` int(20) NOT NULL,
  `post` text NOT NULL,
  `description` text NOT NULL,
  `posted_on` date NOT NULL DEFAULT current_timestamp(),
  `arrears_allowed` int(20) NOT NULL,
  `cutoff_cgpa` float NOT NULL,
  `last_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_vacancies`
--

INSERT INTO `tbl_vacancies` (`vacancy_id`, `drive_id`, `post`, `description`, `posted_on`, `arrears_allowed`, `cutoff_cgpa`, `last_date`) VALUES
(1, 2, 'Software engineer', 'abcdefrdesehgfytdtfh', '2025-09-26', 2, 7, '2025-10-03');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vacancy_course`
--

CREATE TABLE `tbl_vacancy_course` (
  `vacancy_course_id` int(11) NOT NULL,
  `vacancy_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_vacancy_course`
--

INSERT INTO `tbl_vacancy_course` (`vacancy_course_id`, `vacancy_id`, `course_id`) VALUES
(1, 1, 1),
(2, 1, 20);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_applications`
--
ALTER TABLE `tbl_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `vacancy_id` (`vacancy_id`);

--
-- Indexes for table `tbl_aptitude`
--
ALTER TABLE `tbl_aptitude`
  ADD PRIMARY KEY (`aptitude_id`),
  ADD KEY `vacancy_id` (`vacancy_id`);

--
-- Indexes for table `tbl_attendance`
--
ALTER TABLE `tbl_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `tbl_company`
--
ALTER TABLE `tbl_company`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `login_id` (`login_id`);

--
-- Indexes for table `tbl_course`
--
ALTER TABLE `tbl_course`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `tbl_drive`
--
ALTER TABLE `tbl_drive`
  ADD PRIMARY KEY (`drive_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `tbl_login`
--
ALTER TABLE `tbl_login`
  ADD PRIMARY KEY (`login_id`);

--
-- Indexes for table `tbl_notification`
--
ALTER TABLE `tbl_notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `drive_id` (`drive_id`);

--
-- Indexes for table `tbl_questions`
--
ALTER TABLE `tbl_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `aptitude_id` (`aptitude_id`);

--
-- Indexes for table `tbl_result`
--
ALTER TABLE `tbl_result`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `vacancy_id` (`vacancy_id`);

--
-- Indexes for table `tbl_reviews`
--
ALTER TABLE `tbl_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `tbl_student`
--
ALTER TABLE `tbl_student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `login_id` (`login_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `credential_id` (`credential_id`);

--
-- Indexes for table `tbl_student_credentials`
--
ALTER TABLE `tbl_student_credentials`
  ADD PRIMARY KEY (`credential_id`);

--
-- Indexes for table `tbl_student_response`
--
ALTER TABLE `tbl_student_response`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `tbl_vacancies`
--
ALTER TABLE `tbl_vacancies`
  ADD PRIMARY KEY (`vacancy_id`),
  ADD KEY `drive_id` (`drive_id`);

--
-- Indexes for table `tbl_vacancy_course`
--
ALTER TABLE `tbl_vacancy_course`
  ADD PRIMARY KEY (`vacancy_course_id`),
  ADD KEY `vacancy_id` (`vacancy_id`),
  ADD KEY `course_id` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_applications`
--
ALTER TABLE `tbl_applications`
  MODIFY `application_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_aptitude`
--
ALTER TABLE `tbl_aptitude`
  MODIFY `aptitude_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_attendance`
--
ALTER TABLE `tbl_attendance`
  MODIFY `attendance_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_company`
--
ALTER TABLE `tbl_company`
  MODIFY `company_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_course`
--
ALTER TABLE `tbl_course`
  MODIFY `course_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_drive`
--
ALTER TABLE `tbl_drive`
  MODIFY `drive_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  MODIFY `feedback_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_login`
--
ALTER TABLE `tbl_login`
  MODIFY `login_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tbl_notification`
--
ALTER TABLE `tbl_notification`
  MODIFY `notification_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_questions`
--
ALTER TABLE `tbl_questions`
  MODIFY `question_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_result`
--
ALTER TABLE `tbl_result`
  MODIFY `result_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_reviews`
--
ALTER TABLE `tbl_reviews`
  MODIFY `review_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_student`
--
ALTER TABLE `tbl_student`
  MODIFY `student_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tbl_student_credentials`
--
ALTER TABLE `tbl_student_credentials`
  MODIFY `credential_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tbl_student_response`
--
ALTER TABLE `tbl_student_response`
  MODIFY `response_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_vacancies`
--
ALTER TABLE `tbl_vacancies`
  MODIFY `vacancy_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_vacancy_course`
--
ALTER TABLE `tbl_vacancy_course`
  MODIFY `vacancy_course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_applications`
--
ALTER TABLE `tbl_applications`
  ADD CONSTRAINT `tbl_applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_applications_ibfk_2` FOREIGN KEY (`vacancy_id`) REFERENCES `tbl_vacancies` (`vacancy_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_aptitude`
--
ALTER TABLE `tbl_aptitude`
  ADD CONSTRAINT `tbl_aptitude_ibfk_1` FOREIGN KEY (`vacancy_id`) REFERENCES `tbl_vacancies` (`vacancy_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_attendance`
--
ALTER TABLE `tbl_attendance`
  ADD CONSTRAINT `tbl_attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_attendance_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `tbl_applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_company`
--
ALTER TABLE `tbl_company`
  ADD CONSTRAINT `tbl_company_ibfk_1` FOREIGN KEY (`login_id`) REFERENCES `tbl_login` (`login_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_drive`
--
ALTER TABLE `tbl_drive`
  ADD CONSTRAINT `tbl_drive_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `tbl_company` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_notification`
--
ALTER TABLE `tbl_notification`
  ADD CONSTRAINT `tbl_notification_ibfk_1` FOREIGN KEY (`drive_id`) REFERENCES `tbl_drive` (`drive_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_questions`
--
ALTER TABLE `tbl_questions`
  ADD CONSTRAINT `tbl_questions_ibfk_1` FOREIGN KEY (`aptitude_id`) REFERENCES `tbl_aptitude` (`aptitude_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_result`
--
ALTER TABLE `tbl_result`
  ADD CONSTRAINT `tbl_result_ibfk_1` FOREIGN KEY (`vacancy_id`) REFERENCES `tbl_vacancies` (`vacancy_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_reviews`
--
ALTER TABLE `tbl_reviews`
  ADD CONSTRAINT `tbl_reviews_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_reviews_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `tbl_company` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_student`
--
ALTER TABLE `tbl_student`
  ADD CONSTRAINT `tbl_student_ibfk_1` FOREIGN KEY (`login_id`) REFERENCES `tbl_login` (`login_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_student_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `tbl_course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_student_ibfk_3` FOREIGN KEY (`credential_id`) REFERENCES `tbl_student_credentials` (`credential_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_student_response`
--
ALTER TABLE `tbl_student_response`
  ADD CONSTRAINT `tbl_student_response_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `tbl_questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_vacancies`
--
ALTER TABLE `tbl_vacancies`
  ADD CONSTRAINT `tbl_vacancies_ibfk_1` FOREIGN KEY (`drive_id`) REFERENCES `tbl_drive` (`drive_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_vacancy_course`
--
ALTER TABLE `tbl_vacancy_course`
  ADD CONSTRAINT `tbl_vacancy_course_ibfk_1` FOREIGN KEY (`vacancy_id`) REFERENCES `tbl_vacancies` (`vacancy_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_vacancy_course_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `tbl_course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
