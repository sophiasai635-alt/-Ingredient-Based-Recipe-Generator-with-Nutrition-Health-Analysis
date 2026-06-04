-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2026 at 04:06 PM
-- Server version: 8.0.44
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recipe`
--

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `steps` text,
  `cook_time` int DEFAULT NULL,
  `difficulty` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `image`, `description`, `steps`, `cook_time`, `difficulty`, `category`, `created_at`) VALUES
(1, 'Egg Omelette', 'omelette.jpg', 'Simple fluffy omelette', 'Beat eggs, cook on pan', 10, 'easy', 'breakfast', '2026-01-09 08:43:23'),
(2, 'Vegetable Omelette', 'veg_omelette.jpg', 'Healthy veggie omelette', 'Add veggies to eggs and cook', 12, 'easy', 'breakfast', '2026-01-09 08:43:23'),
(3, 'Banana Oats Pancake', 'banana_oats.jpg', 'Healthy breakfast pancakes', 'Blend oats and banana, cook', 15, 'easy', 'breakfast', '2026-01-09 08:43:23'),
(4, 'French Toast', 'french_toast.jpg', 'Classic breakfast toast', 'Dip bread in egg milk, fry', 10, 'easy', 'breakfast', '2026-01-09 08:43:23'),
(5, 'Paneer Bhurji', 'paneer_bhurji.jpg', 'Indian paneer scramble', 'Cook paneer with onion tomato', 15, 'medium', 'breakfast', '2026-01-09 08:43:23'),
(6, 'Grilled Chicken Salad', 'chicken_salad.jpg', 'Protein rich salad', 'Grill chicken, mix veggies', 20, 'medium', 'healthy', '2026-01-09 08:43:23'),
(7, 'Spinach Soup', 'spinach_soup.jpg', 'Low calorie soup', 'Boil spinach and blend', 18, 'easy', 'healthy', '2026-01-09 08:43:23'),
(8, 'Veg Stir Fry', 'veg_stir.jpg', 'Healthy stir fry', 'Stir fry veggies', 15, 'easy', 'healthy', '2026-01-09 08:43:23'),
(9, 'Oats Porridge', 'oats.jpg', 'Heart healthy oats', 'Cook oats with milk', 10, 'easy', 'healthy', '2026-01-09 08:43:23'),
(10, 'Fruit Salad', 'fruit_salad.jpg', 'Fresh mixed fruits', 'Chop and mix fruits', 5, 'easy', 'healthy', '2026-01-09 08:43:23'),
(11, 'Garlic Bread', 'garlic_bread.jpg', 'Quick snack', 'Toast bread with garlic butter', 8, 'easy', 'quick', '2026-01-09 08:43:23'),
(12, 'Cheese Sandwich', 'cheese_sandwich.jpg', 'Instant sandwich', 'Grill bread with cheese', 7, 'easy', 'quick', '2026-01-09 08:43:23'),
(13, 'Egg Fried Rice', 'egg_fried_rice.jpg', 'Quick fried rice', 'Fry rice with egg', 15, 'easy', 'quick', '2026-01-09 08:43:23'),
(14, 'Veg Noodles', 'veg_noodles.jpg', 'Street style noodles', 'Toss noodles with veggies', 12, 'medium', 'quick', '2026-01-09 08:43:23'),
(15, 'Tomato Soup', 'tomato_soup.jpg', 'Comfort soup', 'Boil tomatoes and blend', 15, 'easy', 'quick', '2026-01-09 08:43:23'),
(16, 'Chicken Curry', 'chicken_curry.jpg', 'Protein rich curry', 'Cook chicken with spices', 30, 'medium', 'protein', '2026-01-09 08:43:23'),
(17, 'Boiled Eggs', 'boiled_eggs.jpg', 'Simple protein food', 'Boil eggs', 8, 'easy', 'protein', '2026-01-09 08:43:23'),
(18, 'Paneer Curry', 'paneer_curry.jpg', 'High protein veg dish', 'Cook paneer gravy', 25, 'medium', 'protein', '2026-01-09 08:43:23'),
(19, 'Fish Fry', 'fish_fry.jpg', 'Crispy fish fry', 'Marinate and fry fish', 20, 'medium', 'protein', '2026-01-09 08:43:23'),
(20, 'Protein Smoothie', 'protein_smoothie.jpg', 'Healthy shake', 'Blend banana milk honey', 5, 'easy', 'protein', '2026-01-09 08:43:23'),
(21, 'Mac and Cheese', 'mac_cheese.jpg', 'Cheesy comfort food', 'Cook pasta with cheese sauce', 20, 'medium', 'comfort', '2026-01-09 08:43:23'),
(22, 'Mashed Potatoes', 'mashed_potato.jpg', 'Creamy potatoes', 'Boil and mash potatoes', 18, 'easy', 'comfort', '2026-01-09 08:43:23'),
(23, 'Chicken Soup', 'chicken_soup.jpg', 'Warm comfort soup', 'Boil chicken with veggies', 25, 'medium', 'comfort', '2026-01-09 08:43:23'),
(24, 'Veg Khichdi', 'khichdi.jpg', 'Indian comfort food', 'Cook rice with lentils', 25, 'easy', 'comfort', '2026-01-09 08:43:23'),
(25, 'Grilled Cheese', 'grilled_cheese.jpg', 'Cheesy toast', 'Grill bread with cheese', 10, 'easy', 'comfort', '2026-01-09 08:43:23'),
(26, 'Vegan Stir Fry', 'vegan_stir.jpg', 'Plant based dish', 'Stir fry vegetables', 15, 'easy', 'vegan', '2026-01-09 08:43:23'),
(27, 'Vegan Salad', 'vegan_salad.jpg', 'Fresh salad', 'Mix veggies and lemon', 5, 'easy', 'vegan', '2026-01-09 08:43:23'),
(28, 'Vegan Fried Rice', 'vegan_rice.jpg', 'Rice with veggies', 'Fry rice and vegetables', 15, 'easy', 'vegan', '2026-01-09 08:43:23'),
(29, 'Lemon Rice', 'lemon_rice.jpg', 'South Indian dish', 'Mix rice with lemon spices', 12, 'medium', 'vegan', '2026-01-09 08:43:23'),
(30, 'Corn Soup', 'corn_soup.jpg', 'Sweet corn soup', 'Boil corn and blend', 15, 'easy', 'vegan', '2026-01-09 08:43:23'),
(31, 'Chocolate Cake', 'cake.jpg', 'Rich chocolate cake', 'Bake cake', 45, 'hard', 'dessert', '2026-01-09 08:43:23'),
(32, 'Fruit Custard', 'custard.jpg', 'Sweet dessert', 'Mix fruits in custard', 20, 'easy', 'dessert', '2026-01-09 08:43:23'),
(33, 'Pancakes', 'pancakes.jpg', 'Fluffy pancakes', 'Cook batter on pan', 15, 'easy', 'dessert', '2026-01-09 08:43:23'),
(34, 'Apple Pie', 'apple_pie.jpg', 'Classic pie', 'Bake apple filling', 50, 'hard', 'dessert', '2026-01-09 08:43:23'),
(35, 'Banana Shake', 'banana_shake.jpg', 'Sweet shake', 'Blend banana milk', 5, 'easy', 'dessert', '2026-01-09 08:43:23'),
(36, 'Scrambled Eggs', 'scrambled.jpg', 'Soft scrambled eggs', 'Whisk eggs and cook gently', 8, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(37, 'Egg Sandwich', 'egg_sandwich.jpg', 'Quick egg sandwich', 'Cook egg and place in bread', 10, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(38, 'Veg Upma', 'upma.jpg', 'South Indian breakfast', 'Cook semolina with veggies', 20, 'medium', 'breakfast', '2026-01-09 08:47:00'),
(39, 'Poha', 'poha.jpg', 'Flattened rice dish', 'Cook poha with onion spices', 15, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(40, 'Idli', 'idli.jpg', 'Steamed rice cakes', 'Steam fermented batter', 25, 'medium', 'breakfast', '2026-01-09 08:47:00'),
(41, 'Dosa', 'dosa.jpg', 'Crispy dosa', 'Spread batter on pan', 20, 'medium', 'breakfast', '2026-01-09 08:47:00'),
(42, 'Egg Muffins', 'egg_muffins.jpg', 'Baked egg cups', 'Bake eggs with veggies', 18, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(43, 'Toast & Jam', 'toast.jpg', 'Simple toast', 'Toast bread and apply jam', 5, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(44, 'Avocado Toast', 'avocado_toast.jpg', 'Healthy toast', 'Mash avocado on toast', 8, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(45, 'Breakfast Burrito', 'burrito.jpg', 'Egg wrap', 'Wrap eggs in tortilla', 15, 'medium', 'breakfast', '2026-01-09 08:47:00'),
(46, 'Pancake Stack', 'pancake_stack.jpg', 'Fluffy pancakes', 'Cook pancake batter', 15, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(47, 'Corn Omelette', 'corn_omelette.jpg', 'Sweet corn omelette', 'Mix corn with eggs', 10, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(48, 'Bread Omelette', 'bread_omelette.jpg', 'Street style omelette', 'Dip bread in egg mix', 12, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(49, 'Fruit Smoothie', 'fruit_smoothie.jpg', 'Mixed fruit smoothie', 'Blend fruits with milk', 5, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(50, 'Protein Oats', 'protein_oats.jpg', 'Oats with protein', 'Cook oats with milk', 10, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(51, 'Egg Paratha', 'egg_paratha.jpg', 'Egg stuffed paratha', 'Cook egg-filled dough', 20, 'medium', 'breakfast', '2026-01-09 08:47:00'),
(52, 'Vegetable Sandwich', 'veg_sandwich.jpg', 'Healthy sandwich', 'Layer veggies in bread', 10, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(53, 'Cheese Toast', 'cheese_toast.jpg', 'Cheesy breakfast', 'Toast bread with cheese', 8, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(54, 'Milkshake', 'milkshake.jpg', 'Sweet milkshake', 'Blend milk and fruits', 6, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(55, 'Egg Salad', 'egg_salad.jpg', 'Egg-based salad', 'Mix eggs with veggies', 12, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(56, 'Mushroom Omelette', 'mushroom_omelette.jpg', 'Omelette with mushrooms', 'Cook eggs with mushrooms', 10, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(57, 'Breakfast Bowl', 'breakfast_bowl.jpg', 'Balanced breakfast', 'Combine grains and eggs', 15, 'medium', 'breakfast', '2026-01-09 08:47:00'),
(58, 'Spinach Omelette', 'spinach_omelette.jpg', 'Healthy omelette', 'Add spinach to eggs', 10, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(59, 'Peanut Butter Toast', 'pb_toast.jpg', 'Energy toast', 'Spread peanut butter', 5, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(60, 'Masala Omelette', 'masala_omelette.jpg', 'Spicy omelette', 'Add spices to egg mix', 10, 'easy', 'breakfast', '2026-01-09 08:47:00'),
(61, 'Quinoa Salad', 'quinoa.jpg', 'Healthy quinoa salad', 'Cook quinoa and mix veggies', 20, 'easy', 'healthy', '2026-01-09 08:47:31'),
(62, 'Grilled Veggies', 'grilled_veg.jpg', 'Grilled vegetables', 'Grill assorted veggies', 18, 'easy', 'healthy', '2026-01-09 08:47:31'),
(63, 'Lentil Soup', 'lentil_soup.jpg', 'Protein lentil soup', 'Boil lentils and spices', 25, 'medium', 'healthy', '2026-01-09 08:47:31'),
(64, 'Boiled Veg Bowl', 'veg_bowl.jpg', 'Low calorie bowl', 'Boil mixed vegetables', 15, 'easy', 'healthy', '2026-01-09 08:47:31'),
(65, 'Steamed Fish', 'steamed_fish.jpg', 'Healthy fish dish', 'Steam fish with spices', 20, 'medium', 'healthy', '2026-01-09 08:47:31'),
(66, 'Veg Clear Soup', 'clear_soup.jpg', 'Light soup', 'Boil veggies', 12, 'easy', 'healthy', '2026-01-09 08:47:31'),
(67, 'Grilled Paneer', 'grilled_paneer.jpg', 'Healthy paneer', 'Grill paneer cubes', 15, 'easy', 'healthy', '2026-01-09 08:47:31'),
(68, 'Brown Rice Bowl', 'brown_rice.jpg', 'Healthy rice bowl', 'Cook rice with veggies', 20, 'easy', 'healthy', '2026-01-09 08:47:31'),
(69, 'Sprouts Salad', 'sprouts.jpg', 'Protein sprouts', 'Mix sprouts with lemon', 10, 'easy', 'healthy', '2026-01-09 08:47:31'),
(70, 'Vegetable Soup', 'veg_soup.jpg', 'Nutritious soup', 'Boil vegetables', 20, 'easy', 'healthy', '2026-01-09 08:47:31'),
(71, 'Avocado Salad', 'avocado_salad.jpg', 'Healthy fats', 'Mix avocado with veggies', 10, 'easy', 'healthy', '2026-01-09 08:47:31'),
(72, 'Grilled Chicken', 'grilled_chicken.jpg', 'Lean protein', 'Grill chicken', 25, 'medium', 'healthy', '2026-01-09 08:47:31'),
(73, 'Spinach Smoothie', 'spinach_smoothie.jpg', 'Green smoothie', 'Blend spinach and fruits', 5, 'easy', 'healthy', '2026-01-09 08:47:31'),
(74, 'Zucchini Stir Fry', 'zucchini.jpg', 'Light stir fry', 'Stir fry zucchini', 10, 'easy', 'healthy', '2026-01-09 08:47:31'),
(75, 'Veggie Wrap', 'veggie_wrap.jpg', 'Healthy wrap', 'Wrap veggies in tortilla', 15, 'easy', 'healthy', '2026-01-09 08:47:31'),
(76, 'Lemon Chicken', 'lemon_chicken.jpg', 'Healthy chicken dish', 'Cook chicken with lemon', 22, 'medium', 'healthy', '2026-01-09 08:47:31'),
(77, 'Bean Salad', 'bean_salad.jpg', 'Protein salad', 'Mix beans and veggies', 12, 'easy', 'healthy', '2026-01-09 08:47:31'),
(78, 'Steamed Veggies', 'steamed_veg.jpg', 'Simple healthy dish', 'Steam vegetables', 10, 'easy', 'healthy', '2026-01-09 08:47:31'),
(79, 'Oats Smoothie', 'oats_smoothie.jpg', 'Healthy smoothie', 'Blend oats with milk', 6, 'easy', 'healthy', '2026-01-09 08:47:31'),
(80, 'Cucumber Salad', 'cucumber_salad.jpg', 'Refreshing salad', 'Slice cucumber and lemon', 5, 'easy', 'healthy', '2026-01-09 08:47:31'),
(81, 'Veg Stew', 'veg_stew.jpg', 'Light stew', 'Cook vegetables slowly', 30, 'medium', 'healthy', '2026-01-09 08:47:31'),
(82, 'Roasted Veg Bowl', 'roasted_veg.jpg', 'Oven roasted veggies', 'Roast veggies', 25, 'easy', 'healthy', '2026-01-09 08:47:31'),
(83, 'Grilled Tofu', 'tofu.jpg', 'Plant protein', 'Grill tofu', 15, 'easy', 'healthy', '2026-01-09 08:47:31'),
(84, 'Mixed Grain Bowl', 'grain_bowl.jpg', 'Whole grain meal', 'Cook grains', 30, 'medium', 'healthy', '2026-01-09 08:47:31'),
(85, 'Carrot Soup', 'carrot_soup.jpg', 'Vitamin rich soup', 'Boil carrots', 15, 'easy', 'healthy', '2026-01-09 08:47:31'),
(86, 'Veg Patties', 'veg_patties.jpg', 'Healthy snack', 'Pan fry patties', 20, 'medium', 'healthy', '2026-01-09 08:47:31'),
(87, 'Protein Salad', 'protein_salad.jpg', 'High protein bowl', 'Mix eggs & chicken', 15, 'easy', 'healthy', '2026-01-09 08:47:31'),
(88, 'Low Carb Stir Fry', 'low_carb.jpg', 'Low carb veggies', 'Stir fry vegetables', 12, 'easy', 'healthy', '2026-01-09 08:47:31'),
(89, 'Veg Rice Bowl', 'veg_rice_bowl.jpg', 'Balanced meal', 'Mix rice and veggies', 20, 'easy', 'healthy', '2026-01-09 08:47:31'),
(90, 'Healthy Khichdi', 'healthy_khichdi.jpg', 'Light khichdi', 'Cook rice and lentils', 25, 'easy', 'healthy', '2026-01-09 08:47:31'),
(91, 'Chicken Coconut Curry', 'chicken_coconut.jpg', 'Creamy coconut chicken', 'Cook chicken with coconut milk', 40, 'medium', 'chicken', '2026-01-09 08:54:53'),
(92, 'Chicken Malai Kebab', 'malai_kebab.jpg', 'Creamy kebabs', 'Grill malai marinated chicken', 35, 'medium', 'chicken', '2026-01-09 08:54:53'),
(93, 'Chicken Kolhapuri', 'kolhapuri.jpg', 'Spicy Maharashtrian curry', 'Cook chicken in kolhapuri masala', 45, 'hard', 'chicken', '2026-01-09 08:54:53'),
(94, 'Chicken Pepper Gravy', 'pepper_gravy.jpg', 'Peppery gravy', 'Cook chicken with black pepper', 35, 'medium', 'chicken', '2026-01-09 08:54:53'),
(95, 'Chicken Saagwala', 'saagwala.jpg', 'Spinach chicken', 'Cook chicken with spinach', 40, 'medium', 'chicken', '2026-01-09 08:54:53'),
(96, 'Chicken Mughlai', 'mughlai.jpg', 'Rich Mughlai curry', 'Cook chicken with cream and nuts', 50, 'hard', 'chicken', '2026-01-09 08:54:53'),
(97, 'Chicken Do Pyaza', 'do_pyaza.jpg', 'Onion based curry', 'Cook chicken with onions', 40, 'medium', 'chicken', '2026-01-09 08:54:53'),
(98, 'Chicken Jalfrezi', 'jalfrezi.jpg', 'Stir fried curry', 'Cook chicken with capsicum', 35, 'medium', 'chicken', '2026-01-09 08:54:53'),
(99, 'Chicken Ghee Roast', 'ghee_roast.jpg', 'Ghee roasted chicken', 'Roast chicken in ghee', 30, 'medium', 'chicken', '2026-01-09 08:54:53'),
(100, 'Chicken Afghani', 'afghani.jpg', 'Creamy grilled chicken', 'Grill afghani marinated chicken', 40, 'medium', 'chicken', '2026-01-09 08:54:53'),
(101, 'Chicken Pulao', 'chicken_pulao.jpg', 'Rice and chicken', 'Cook rice with chicken stock', 45, 'medium', 'chicken', '2026-01-09 08:54:53'),
(102, 'Chicken Pathia', 'pathia.jpg', 'Sweet sour curry', 'Cook chicken with tamarind', 40, 'medium', 'chicken', '2026-01-09 08:54:53'),
(103, 'Chicken Cafreal', 'cafreal.jpg', 'Goan green masala', 'Grill green masala chicken', 35, 'medium', 'chicken', '2026-01-09 08:54:53'),
(104, 'Chicken Xacuti', 'xacuti.jpg', 'Goan coconut curry', 'Cook chicken with roasted coconut', 50, 'hard', 'chicken', '2026-01-09 08:54:53'),
(105, 'Chicken Peri Peri', 'peri_peri.jpg', 'Spicy grilled chicken', 'Grill peri peri chicken', 30, 'easy', 'chicken', '2026-01-09 08:54:53'),
(106, 'Chicken Steamed Momos', 'chicken_momos.jpg', 'Steamed dumplings', 'Steam chicken momos', 35, 'medium', 'chicken', '2026-01-09 08:54:53'),
(107, 'Chicken Thai Curry', 'thai_curry.jpg', 'Thai coconut curry', 'Cook chicken in thai paste', 40, 'medium', 'chicken', '2026-01-09 08:54:53'),
(108, 'Chicken Satay', 'satay.jpg', 'Peanut sauce chicken', 'Grill chicken skewers', 30, 'easy', 'chicken', '2026-01-09 08:54:53'),
(109, 'Chicken Stuffed Paratha', 'stuffed_paratha.jpg', 'Stuffed flatbread', 'Cook chicken filled paratha', 30, 'medium', 'chicken', '2026-01-09 08:54:53'),
(110, 'Chicken Schezwan', 'schezwan.jpg', 'Spicy indo chinese', 'Cook chicken in schezwan sauce', 25, 'easy', 'chicken', '2026-01-09 08:54:53'),
(111, 'Chicken Teriyaki', 'teriyaki.jpg', 'Japanese style chicken', 'Cook chicken in teriyaki sauce', 25, 'easy', 'chicken', '2026-01-09 08:54:53'),
(112, 'Chicken Stuffed Peppers', 'stuffed_pepper.jpg', 'Baked chicken peppers', 'Bake stuffed peppers', 35, 'medium', 'chicken', '2026-01-09 08:54:53'),
(113, 'Chicken Kofta', 'kofta.jpg', 'Chicken meatballs', 'Cook chicken koftas in gravy', 45, 'medium', 'chicken', '2026-01-09 08:54:53'),
(114, 'Chicken Rara', 'rara.jpg', 'Minced chicken curry', 'Cook chicken with keema', 50, 'hard', 'chicken', '2026-01-09 08:54:53'),
(115, 'Chicken Keema Pav', 'keema_pav.jpg', 'Street food', 'Serve keema with pav', 30, 'easy', 'chicken', '2026-01-09 08:54:53'),
(116, 'Chicken Corn Soup', 'corn_soup.jpg', 'Thick soup', 'Cook chicken with corn', 25, 'easy', 'chicken', '2026-01-09 08:54:53'),
(117, 'Chicken Poppers', 'poppers.jpg', 'Snack bites', 'Deep fry chicken bites', 20, 'easy', 'chicken', '2026-01-09 08:54:53'),
(118, 'Chicken Shami Kebab', 'shami.jpg', 'Soft kebabs', 'Cook lentil chicken kebabs', 45, 'medium', 'chicken', '2026-01-09 08:54:53'),
(119, 'Chicken Hyderabadi', 'hyderabadi.jpg', 'Spicy Hyderabadi curry', 'Cook with fried onions', 55, 'hard', 'chicken', '2026-01-09 08:54:53'),
(120, 'Chicken Bharta', 'bharta.jpg', 'Smoky chicken mash', 'Roast and mash chicken', 35, 'medium', 'chicken', '2026-01-09 08:54:53'),
(121, 'Chicken Spinach Roll', 'spinach_roll.jpg', 'Healthy roll', 'Wrap chicken and spinach', 20, 'easy', 'chicken', '2026-01-09 08:54:53'),
(122, 'Chicken Creamy Gravy', 'creamy_gravy.jpg', 'Cream based curry', 'Cook chicken in cream', 40, 'medium', 'chicken', '2026-01-09 08:54:53'),
(123, 'Chicken Lemon Pepper', 'lemon_pepper.jpg', 'Zesty chicken', 'Cook chicken with lemon', 25, 'easy', 'chicken', '2026-01-09 08:54:53'),
(124, 'Chicken Chili Garlic', 'chili_garlic.jpg', 'Garlic spicy chicken', 'Stir fry chicken with garlic', 25, 'easy', 'chicken', '2026-01-09 08:54:53'),
(125, 'Chicken Stuffed Bread', 'stuffed_bread.jpg', 'Baked bread', 'Bake chicken stuffed bread', 40, 'medium', 'chicken', '2026-01-09 08:54:53'),
(126, 'Chicken Hot Pot', 'hot_pot.jpg', 'One pot dish', 'Cook everything together', 45, 'medium', 'chicken', '2026-01-09 08:54:53'),
(127, 'Chicken Kathi Roll', 'kathi.jpg', 'Street roll', 'Wrap chicken in roti', 20, 'easy', 'chicken', '2026-01-09 08:54:53'),
(128, 'Chicken Mince Curry', 'mince_curry.jpg', 'Minced curry', 'Cook minced chicken', 30, 'easy', 'chicken', '2026-01-09 08:54:53'),
(129, 'Chicken Masala Rice', 'masala_rice.jpg', 'Rice bowl', 'Mix rice with chicken masala', 25, 'easy', 'chicken', '2026-01-09 08:54:53'),
(130, 'Chicken Tangdi', 'tangdi.jpg', 'Spicy drumsticks', 'Grill drumsticks', 35, 'medium', 'chicken', '2026-01-09 08:54:53'),
(131, 'Chicken Cream Cheese', 'cream_cheese.jpg', 'Cheesy chicken', 'Cook chicken with cheese', 30, 'easy', 'chicken', '2026-01-09 08:54:53'),
(132, 'Chicken Tawa Masala', 'tawa_masala.jpg', 'Street style curry', 'Cook chicken on tawa', 35, 'medium', 'chicken', '2026-01-09 08:54:53'),
(133, 'Chicken Dhaba Style', 'dhaba.jpg', 'Roadside curry', 'Cook spicy dhaba chicken', 45, 'medium', 'chicken', '2026-01-09 08:54:53'),
(134, 'Chicken Oregano Grill', 'oregano.jpg', 'Herb grilled chicken', 'Grill with herbs', 30, 'easy', 'chicken', '2026-01-09 08:54:53'),
(135, 'Chicken Stuffed Omelette', 'stuffed_omelette.jpg', 'Protein rich', 'Stuff omelette with chicken', 20, 'easy', 'chicken', '2026-01-09 08:54:53'),
(136, 'Chicken Chilli Gravy', 'chili_gravy.jpg', 'Saucy chicken', 'Cook chicken in chili gravy', 25, 'easy', 'chicken', '2026-01-09 08:54:53'),
(137, 'Chicken Paprika', 'paprika.jpg', 'Smoky chicken', 'Cook with paprika', 30, 'easy', 'chicken', '2026-01-09 08:54:53'),
(138, 'Chicken Curry Bowl', 'curry_bowl.jpg', 'Balanced meal', 'Serve curry in bowl', 35, 'easy', 'chicken', '2026-01-09 08:54:53'),
(139, 'Chicken Sweet Corn', 'sweet_corn.jpg', 'Corn chicken mix', 'Cook chicken with corn', 25, 'easy', 'chicken', '2026-01-09 08:54:53'),
(140, 'Chicken Kashmiri', 'kashmiri.jpg', 'Mild red curry', 'Cook with kashmiri chili', 45, 'medium', 'chicken', '2026-01-09 08:54:53'),
(141, 'Mutton Masala', 'mutton_masala.jpg', 'Spicy curry', 'Cook mutton with masala', 70, 'hard', 'mutton', '2026-01-09 08:54:53'),
(142, 'Mutton Ghee Roast', 'mutton_ghee.jpg', 'Ghee roasted', 'Roast mutton in ghee', 60, 'hard', 'mutton', '2026-01-09 08:54:53'),
(143, 'Mutton Pepper Curry', 'pepper_curry.jpg', 'Peppery curry', 'Cook mutton with pepper', 65, 'hard', 'mutton', '2026-01-09 08:54:53'),
(144, 'Mutton Coconut Curry', 'mutton_coconut.jpg', 'Coconut gravy', 'Cook with coconut milk', 60, 'hard', 'mutton', '2026-01-09 08:54:53'),
(145, 'Mutton Fry Masala', 'fry_masala.jpg', 'Dry masala', 'Fry cooked mutton', 45, 'medium', 'mutton', '2026-01-09 08:54:53'),
(146, 'Mutton Chukka', 'chukka.jpg', 'South Indian fry', 'Dry cook mutton', 50, 'medium', 'mutton', '2026-01-09 08:54:53'),
(147, 'Mutton Bone Soup', 'bone_soup.jpg', 'Healthy soup', 'Boil bones slowly', 90, 'hard', 'mutton', '2026-01-09 08:54:53'),
(148, 'Mutton Mughlai', 'mughlai_mutton.jpg', 'Creamy curry', 'Cook in rich gravy', 70, 'hard', 'mutton', '2026-01-09 08:54:53'),
(149, 'Mutton Kheema Balls', 'keema_balls.jpg', 'Minced balls', 'Fry mutton balls', 35, 'medium', 'mutton', '2026-01-09 08:54:53'),
(150, 'Mutton Roast', 'mutton_roast.jpg', 'Dry roasted', 'Roast mutton slowly', 60, 'hard', 'mutton', '2026-01-09 08:54:53'),
(151, 'Mutton Handi Special', 'handi_special.jpg', 'Restaurant style', 'Slow cook in handi', 75, 'hard', 'mutton', '2026-01-09 08:54:53'),
(152, 'Mutton Curry Bowl', 'mutton_bowl.jpg', 'Balanced meal', 'Serve curry with rice', 60, 'medium', 'mutton', '2026-01-09 08:54:53'),
(153, 'Mutton Pepper Fry Dry', 'pepper_dry.jpg', 'Dry fry', 'Fry mutton with pepper', 45, 'medium', 'mutton', '2026-01-09 08:54:53'),
(154, 'Mutton Coconut Fry', 'coconut_fry.jpg', 'Dry coconut fry', 'Cook with coconut', 50, 'medium', 'mutton', '2026-01-09 08:54:53'),
(155, 'Mutton Saag', 'mutton_saag.jpg', 'Spinach curry', 'Cook mutton with greens', 65, 'hard', 'mutton', '2026-01-09 08:54:53'),
(156, 'Mutton Dal Fry', 'dal_fry.jpg', 'Dal and meat', 'Cook dal with mutton', 60, 'medium', 'mutton', '2026-01-09 08:54:53'),
(157, 'Mutton Curry Leaf Fry', 'leaf_fry.jpg', 'Aromatic fry', 'Fry with curry leaves', 45, 'medium', 'mutton', '2026-01-09 08:54:53'),
(158, 'Mutton Steamed Curry', 'steamed.jpg', 'Healthy curry', 'Steam cooked mutton', 70, 'hard', 'mutton', '2026-01-09 08:54:53'),
(159, 'Mutton Pepper Soup', 'pepper_soup.jpg', 'Spicy soup', 'Cook mutton broth', 55, 'medium', 'mutton', '2026-01-09 08:54:53'),
(160, 'Mutton Dry Masala', 'dry_masala.jpg', 'Dry masala dish', 'Roast with spices', 50, 'medium', 'mutton', '2026-01-09 08:54:53'),
(161, 'Mutton Garlic Roast', 'garlic_roast.jpg', 'Garlic flavored', 'Roast with garlic', 55, 'medium', 'mutton', '2026-01-09 08:54:53'),
(162, 'Mutton Masala Rice', 'masala_rice_mutton.jpg', 'Rice combo', 'Mix rice and curry', 60, 'medium', 'mutton', '2026-01-09 08:54:53'),
(163, 'Mutton Special Curry', 'special_curry.jpg', 'Chef special', 'Slow cooked curry', 80, 'hard', 'mutton', '2026-01-09 08:54:53'),
(164, 'Mutton Black Pepper', 'black_pepper.jpg', 'Spicy black pepper', 'Cook with crushed pepper', 60, 'hard', 'mutton', '2026-01-09 08:54:53'),
(165, 'Mutton Kadai', 'kadai_mutton.jpg', 'Kadai style curry', 'Cook in kadai masala', 65, 'hard', 'mutton', '2026-01-09 08:54:53'),
(166, 'Fish Coconut Fry', 'fish_coconut.jpg', 'Coconut coated fish', 'Shallow fry fish', 20, 'easy', 'fish', '2026-01-09 08:54:53'),
(167, 'Fish Pepper Masala', 'fish_pepper.jpg', 'Peppery fish', 'Cook fish with pepper', 25, 'easy', 'fish', '2026-01-09 08:54:53'),
(168, 'Fish Garlic Fry', 'fish_garlic.jpg', 'Garlic fish fry', 'Fry with garlic', 20, 'easy', 'fish', '2026-01-09 08:54:53'),
(169, 'Fish Tamarind Curry', 'tamarind.jpg', 'Tangy curry', 'Cook fish with tamarind', 30, 'easy', 'fish', '2026-01-09 08:54:53'),
(170, 'Fish Masala Rice', 'fish_masala_rice.jpg', 'Rice combo', 'Serve fish with rice', 30, 'easy', 'fish', '2026-01-09 08:54:53'),
(171, 'Fish Steamed Curry', 'steamed_fish.jpg', 'Healthy curry', 'Steam fish with spices', 25, 'easy', 'fish', '2026-01-09 08:54:53'),
(172, 'Fish Green Curry', 'green_curry.jpg', 'Herb based curry', 'Cook fish in green paste', 30, 'easy', 'fish', '2026-01-09 08:54:53'),
(173, 'Fish Coconut Milk', 'fish_coconut_milk.jpg', 'Creamy fish', 'Cook with coconut milk', 30, 'easy', 'fish', '2026-01-09 08:54:53'),
(174, 'Prawn Garlic Butter', 'garlic_butter.jpg', 'Butter prawns', 'Saute prawns in butter', 15, 'easy', 'seafood', '2026-01-09 08:54:53'),
(175, 'Prawn Pepper Fry', 'prawn_pepper.jpg', 'Pepper prawns', 'Stir fry prawns', 20, 'easy', 'seafood', '2026-01-09 08:54:53'),
(176, 'Prawn Coconut Curry', 'prawn_coconut.jpg', 'Coconut curry', 'Cook prawns in coconut', 25, 'easy', 'seafood', '2026-01-09 08:54:53'),
(177, 'Prawn Masala Rice', 'prawn_rice.jpg', 'Rice combo', 'Mix prawns and rice', 30, 'easy', 'seafood', '2026-01-09 08:54:53'),
(178, 'Crab Pepper Masala', 'crab_pepper.jpg', 'Spicy crab', 'Cook crab with pepper', 40, 'medium', 'seafood', '2026-01-09 08:54:53'),
(179, 'Crab Coconut Curry', 'crab_coconut.jpg', 'Coconut crab curry', 'Slow cook crab', 45, 'medium', 'seafood', '2026-01-09 08:54:53'),
(180, 'Squid Pepper Fry', 'squid_pepper.jpg', 'Pepper squid', 'Stir fry squid', 20, 'easy', 'seafood', '2026-01-09 08:54:53'),
(181, 'Squid Coconut Curry', 'squid_coconut.jpg', 'Coconut squid', 'Cook squid gently', 35, 'medium', 'seafood', '2026-01-09 08:54:53'),
(182, 'Fish Lemon Curry', 'fish_lemon.jpg', 'Zesty curry', 'Cook fish with lemon', 25, 'easy', 'fish', '2026-01-09 08:54:53'),
(183, 'Fish Mustard Curry', 'mustard.jpg', 'Bengali style', 'Cook fish with mustard', 30, 'easy', 'fish', '2026-01-09 08:54:53'),
(184, 'Fish Curry Leaf Fry', 'fish_leaf.jpg', 'Aromatic fry', 'Fry fish with curry leaves', 20, 'easy', 'fish', '2026-01-09 08:54:53'),
(185, 'Fish Green Masala', 'green_masala.jpg', 'Herb masala', 'Cook fish with herbs', 30, 'easy', 'fish', '2026-01-09 08:54:53'),
(186, 'Seafood Mixed Fry', 'mixed_fry.jpg', 'Mixed seafood', 'Fry assorted seafood', 30, 'medium', 'seafood', '2026-01-09 08:54:53'),
(187, 'Seafood Coconut Rice', 'coconut_rice.jpg', 'Rice dish', 'Cook rice with seafood', 35, 'medium', 'seafood', '2026-01-09 08:54:53'),
(188, 'Fish Stew Bowl', 'stew_bowl.jpg', 'Light bowl meal', 'Serve fish stew', 30, 'easy', 'fish', '2026-01-09 08:54:53'),
(189, 'Prawn Steamed Curry', 'steamed_prawn.jpg', 'Healthy prawn curry', 'Steam prawns', 25, 'easy', 'seafood', '2026-01-09 08:54:53'),
(190, 'Fish Special Curry', 'fish_special.jpg', 'Chef special', 'Slow cooked fish curry', 40, 'medium', 'fish', '2026-01-09 08:54:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
