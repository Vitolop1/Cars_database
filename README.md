# 🚗 Cars Database – PHP + MariaDB + Docker

<div align="center">

![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)

**Dockerized CRUD web application built with PHP and MariaDB**

</div>

---

## 📖 About

**Cars Database** is a full-stack CRUD web application developed with **PHP (PDO)** and **MariaDB**, fully containerized using **Docker Compose**.

This project was built as an **academic and portfolio project**, focused on showing backend development, SQL usage, and containerized environments.

---

## ✨ Features

- ✅ Create, read, and delete cars  
- 🔍 Search across multiple fields  
- 📊 SQL subquery: count cars per manufacturer  
- 🔐 Secure queries using PDO prepared statements  
- 🐳 Fully dockerized (PHP + Apache + MariaDB)  
- 🗃️ Automatic database initialization  

---

## 🚀 Quick Start

### Requirements
- Docker  
- Docker Compose  
- Git  

### Run locally

```bash
git clone https://github.com/Vitolop1/Cars_database.git
cd Cars_database
docker compose up -d --build
