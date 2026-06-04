# 🍽️ Ingredient-Based Recipe Generator with Nutrition & Health Analysis

A PHP and MySQL web application that helps users discover recipes based on ingredients they already have at home, complete with nutritional analysis and health evaluation.

---

## 📌 Overview

Deciding what to cook with available ingredients is a daily challenge. This project solves it with an intelligent ingredient-to-recipe matching engine, paired with a built-in nutritional analysis module — all in one unified platform.

Built as a final year BSc project at **Kristu Jayanti College (Autonomous), Bengaluru** under the Department of Physical Sciences (2024–25).

---

## ✨ Features

- **Ingredient-Based Recipe Matching** — Enter ingredients you have; get ranked recipe suggestions using a dual-weighted fuzzy matching algorithm (60% recipe coverage + 40% ingredient utilization)
- **Nutritional Analysis** — Calories, protein, carbohydrates, fat, and fiber breakdown for every recipe
- **Health Evaluation Engine** — Rule-based analysis that flags high-calorie/fat recipes and highlights high-protein/fiber ones
- **User Authentication** — Secure registration, login, and session management with hashed passwords
- **Dietary Preference Support** — Vegetarian / Non-vegetarian preference filtering
- **Recipe Bookmarking** — Save and manage your favourite recipes with duplicate prevention
- **Dishes Catalogue** — Browse curated dishes filtered by Breakfast, Lunch, Snack, or Dinner
- **Search History Logging** — Automatic per-user ingredient search history
- **Profile Management** — Update personal info, bio, age, gender, and profile picture

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.x |
| Database | MySQL 8.0 |
| Frontend | HTML, CSS, JavaScript |
| Server | Apache (via XAMPP) |
| DB Management | phpMyAdmin |
| Dev Tools | VS Code, Git, Postman |

---

## 🗄️ Database Schema

The application uses **8 tables**:

- `users` — Account and profile information
- `recipes` — Recipe metadata and cooking steps
- `ingredients` — Master ingredient list
- `recipe_ingredients` — Many-to-many junction (recipe ↔ ingredient + quantity)
- `nutrition` — One-to-one nutrition profile per recipe
- `saved_recipes` — User bookmarks
- `dishes` — Independent curated dishes catalogue
- `ingredient_search_logs` — Per-user search history

---

## 🚀 Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8.x)
- A modern web browser (Chrome, Firefox, Edge)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/recipe-generator.git
   ```

2. **Move to XAMPP's htdocs directory**
   ```bash
   mv recipe-generator /path/to/xampp/htdocs/
   ```

3. **Start Apache and MySQL** from the XAMPP Control Panel

4. **Import the database**
   - Open `http://localhost/phpmyadmin`
   - Create a new database (e.g. `recipe_db`)
   - Import the provided `.sql` file from the `/database` folder

5. **Configure the database connection**
   - Open `api/db.php`
   - Update credentials:
     ```php
     $host = "localhost";
     $dbname = "recipe_db";
     $username = "root";
     $password = "";
     ```

6. **Run the app**
   - Visit `http://localhost/recipe-generator/` in your browser

---

## 📁 Project Structure

```
recipe-generator/
├── api/
│   ├── db.php              # Database connection
│   └── session.php         # Session management
├── auth/
│   ├── login.php
│   ├── register.php
│   ├── register-submission.php
│   └── logout.php
├── assets/
│   └── css/                # Page-specific stylesheets
├── partials/
│   └── navbar.php          # Shared navigation component
├── uploads/                # User profile pictures
├── dashboard.php
├── ingredients.php
├── recipes.php
├── recipe-detail.php
├── dishes.php
├── profile.php
├── profile-edit.php
├── saved-recipes.php
└── index.php
```

---

## 🧠 Matching Algorithm

The recipe ranking uses a **dual-weighted fuzzy scoring** system:

```
Final Score = (Recipe Coverage × 0.6) + (Ingredient Utilization × 0.4)
```

- **Recipe Coverage (60%)** — How many of the recipe's required ingredients you have
- **Ingredient Utilization (40%)** — How many of your ingredients are actually used by the recipe

---

## 📸 Screenshots

| Dashboard | Recipe Results | Nutrition View |
|---|---|---|
| ![Dashboard](assets/screenshots/dashboard.png) | ![Results](assets/screenshots/results.png) | ![Nutrition](assets/screenshots/nutrition.png) |

> Add screenshots to `assets/screenshots/` to display them here.

---

## 🔮 Future Enhancements

- [ ] Real-time nutrition API integration (USDA / Edamam)
- [ ] Image-based ingredient recognition (computer vision)
- [ ] ML-based personalized recipe recommendations
- [ ] Weekly meal planner + grocery list generation
- [ ] Mobile app (Android / iOS)
- [ ] Community features — recipe submissions, ratings, social sharing
- [ ] Multilingual support

---

## 👩‍💻 Author

**Sophia S** (23PHCS18)  
BSc Physics & Computer Science  
Kristu Jayanti College (Autonomous), Bengaluru  
Under the guidance of **Shiny TL**

---

## 📄 License

This project is submitted as part of the BSc degree requirements at Kristu Jayanti College (Autonomous), affiliated to Bengaluru North University, 2024–25.
