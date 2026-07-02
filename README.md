# 🗺️ Population Density Observation Dashboard — Johor State

> 🌐 **Live Demo:** [https://johordashboard.ifree.page](https://johordashboard.ifree.page)

![Dashboard](screenshots/dashboard.png)

> **Final Year Project (Web-Based GIS)**
> Faculty of Built Environment and Surveying, Universiti Teknologi Malaysia (UTM)
> Department of Geoinformation

---

## 📋 Project Overview

This project presents an interactive **Web-Based GIS Dashboard** for observing and analysing population density patterns across the **10 administrative districts of Johor State, Malaysia**. The dashboard visualises demographic data sourced from the **Department of Statistics Malaysia (DOSM)** for the period **2020–2024**.

The system enables users to explore spatial and temporal trends in population distribution, supporting data-driven decision making in urban planning, resource allocation, and regional development.

---

## 🎯 Objectives

1. To develop a web-based GIS dashboard for visualising population density across Johor districts.
2. To integrate real demographic data from DOSM into a structured MySQL database.
3. To provide interactive map visualisation, trend analysis, and comparative statistics for all 10 districts.
4. To enable year-based filtering (2020–2024) for temporal observation of population change.

---

## 🏗️ Methodology

### System Architecture
```
DOSM Excel Data
      ↓
MySQL Database (via phpMyAdmin)
      ↓
PHP Backend (db.php + index.php)
      ↓
Frontend (HTML + CSS + Chart.js + Leaflet.js)
      ↓
Web Browser (localhost via XAMPP)
```

### Technology Stack

| Component | Technology |
|---|---|
| Web Server | Apache (XAMPP) |
| Database | MySQL (XAMPP) |
| DB Management | phpMyAdmin |
| Backend | PHP 8.x |
| Map Visualisation | Leaflet.js v1.9.4 |
| Charts | Chart.js v4 |
| Spatial Data | GeoJSON (WGS84 / EPSG:4326) |
| Data Source | DOSM Malaysia — Principal Statistics of Population |

### Data Processing
- Raw data extracted from DOSM Excel file (2020–2024)
- Data cleaned and structured into relational MySQL table
- Spatial boundaries derived from MYS_adm2 shapefile
- Kulai district extracted from northern Johor Bahru boundary
- Tangkak district extracted from northern Muar boundary
- GeoJSON simplified for web rendering performance

### Dashboard Features
- 📊 **KPI Cards** — Total population, average density, area, growth rate
- 🗺️ **Choropleth Map** — District-level density visualisation with colour classification
- 📈 **Line Chart** — Population trend (2020–2024) for Johor State
- 📊 **Bar Chart** — District density comparison for selected year
- 📋 **Data Table** — Full breakdown per district (population, density, growth rate, gender, citizenship)
- 🔍 **Year Filter** — Dynamic filtering across all components (2020–2024)

---

## 🗄️ Database Structure

**Database name:** `johor_population`
**Table:** `population_data`

| Column | Type | Description |
|---|---|---|
| district | VARCHAR(50) | District name |
| year | SMALLINT | Year (2020–2024) |
| population_000 | DECIMAL | Total population ('000) |
| density_per_km2 | DECIMAL | Population density (per km²) |
| growth_rate_pct | DECIMAL | Annual growth rate (%) |
| male_000 | DECIMAL | Male population ('000) |
| female_000 | DECIMAL | Female population ('000) |
| citizens_000 | DECIMAL | Citizens ('000) |
| non_citizens_000 | DECIMAL | Non-citizens ('000) |
| bumiputera_pct | DECIMAL | Bumiputera (%) |
| chinese_pct | DECIMAL | Chinese (%) |
| indian_pct | DECIMAL | Indian (%) |
| age_0_14_pct | DECIMAL | Age 0–14 (%) |
| age_15_64_pct | DECIMAL | Age 15–64 (%) |
| age_65plus_pct | DECIMAL | Age 65+ (%) |
| median_age | DECIMAL | Median age |
| households_000 | DECIMAL | Number of households ('000) |
| avg_household_size | DECIMAL | Average household size |
| is_preliminary | TINYINT | 1 = 2024 preliminary data |

> Total records: **55 rows** (11 entities × 5 years including Johor State level)

---

## ⚙️ System Setup & Installation

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)
- Web browser (Chrome / Firefox recommended)

### Step 1 — Start XAMPP
1. Open **XAMPP Control Panel**
2. Click **Start** on **Apache** and **MySQL**
3. Both status bars should turn green

### Step 2 — Create Database
1. Open browser → go to `http://localhost/phpmyadmin`
2. Click **Import** tab
3. Choose file → select `johor_population.sql`
4. Click **Go**

### Step 3 — Setup Project Folder
1. Copy all project files into:
```
C:\xampp\htdocs\johor_dashboard\
```

Make sure the folder contains:
```
johor_dashboard/
├── index.php
├── db.php
├── johor_10districts.geojson
└── johor_population.sql
```

### Step 4 — Configure Database Connection
Open `db.php` and verify settings:
```php
$host = "localhost";
$user = "root";
$pass = "";                   // default XAMPP = no password
$db   = "johor_population";
```

### Step 5 — Run Dashboard
Open browser and go to:
```
http://localhost/johor_dashboard/
```
---

## 📊 Data Source

| Item | Details |
|---|---|
| Publisher | Department of Statistics Malaysia (DOSM) |
| Dataset | Principal Statistics of Population, Administrative District — Johor |
| Coverage | 2020–2024 (2024 = Preliminary) |
| Districts | 10 districts + Johor State level |
| Access | [open.dosm.gov.my](https://open.dosm.gov.my) |

---

## 🗺️ Spatial Data

| Item | Details |
|---|---|
| File | `johor_10districts.geojson` |
| Format | GeoJSON |
| Projection | WGS84 (EPSG:4326) |
| Districts | 10 (including Kulai & Tangkak) |
| Source | Derived from MYS_adm2 shapefile |
| Note | Kulai boundary extracted from Johor Bahru; Tangkak from Muar (post-2006/2008 districts) |

---

## ✅ Testing & Validation

| Test | Result |
|---|---|
| Database connection | ✅ Connected via mysqli |
| Data import (55 rows) | ✅ All records verified |
| Map rendering (10 districts) | ✅ Choropleth displayed |
| Year filter (2020–2024) | ✅ Dynamic update works |
| Line chart (trend) | ✅ Rendered correctly |
| Bar chart (district comparison) | ✅ Colour-coded by density |
| Data table | ✅ All columns displayed |
| Responsive layout | ✅ Tested on Chrome & Firefox |

---

## 📝 Conclusion

This Web-Based GIS Dashboard successfully integrates spatial and demographic data to provide an accessible platform for observing population density patterns in Johor State. The system demonstrates how open government data from DOSM can be effectively combined with web GIS technologies (Leaflet.js, PHP, MySQL) to support spatial decision-making.

Key findings from the data (2020–2024):
- **Johor Bahru** consistently records the highest population density
- **Mersing** records the lowest population density
- Overall Johor State population shows a steady upward trend across all years
- 2024 preliminary data indicates continued population growth
  

*Data source: Department of Statistics Malaysia (DOSM). 2024 data is preliminary and subject to revision.*

---

## 🌐 Live Demo

| Item | Link |
|---|---|
| **Dashboard** | https://johordashboard.ifree.page |
| **GitHub Repo** | [Add your GitHub link here] |

---

## 📁 Repository Structure

```
johor-population-dashboard/
│
├── index.php                   # Main dashboard page
├── db.php                      # Database connection
├── johor_10districts.geojson   # Spatial boundary data (all 10 districts)
├── johor_population.sql        # Database schema + all 55 records
├── README.md                   # Project documentation
└── screenshots/
    ├── dashboard_overview.png
    ├── map_choropleth.png
    ├── chart_trend.png
    └── data_table.png
```
