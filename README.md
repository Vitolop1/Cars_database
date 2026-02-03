# 🚗 Cars Database - PHP + MariaDB + Docker

<div align="center">

![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**A fully dockerized CRUD application demonstrating PHP, MariaDB, and Docker Compose integration**

[Features](#-features) • [Demo](#-demo) • [Installation](#-quick-start) • [Usage](#-usage) • [Tech Stack](#-tech-stack)

</div>

---

## 📖 About

This project is a production-ready **Cars Database Management System** built with PHP and MariaDB, fully containerized using Docker. It demonstrates best practices in web development, database design, and DevOps.

Perfect for:
- 🎓 **Students** learning full-stack development
- 💼 **Developers** building portfolio projects
- 🏢 **Recruiters** evaluating technical skills
- 🐳 **DevOps** enthusiasts exploring containerization

## ✨ Features

### Core Functionality
- ✅ **CRUD Operations** - Create, Read, Update, Delete cars
- 🔍 **Advanced Search** - Multi-field search with real-time filtering
- 📊 **SQL Subqueries** - Count cars per manufacturer
- 🔒 **SQL Injection Protection** - Prepared statements with PDO
- 🎨 **Modern UI** - Dark theme with gradient effects

### Technical Highlights
- 🐳 **Fully Dockerized** - One command deployment
- 🔄 **Database Initialization** - Automatic schema and data seeding
- 💾 **Persistent Storage** - Docker volumes for data persistence
- 🏥 **Health Checks** - Ensures database is ready before app starts
- 🌐 **Nginx/Apache** - Production-ready web server configuration
- 📝 **Clean Code** - PSR standards, proper error handling

## 🎬 Demo

### Screenshots

<details>
<summary>🖼️ Click to view screenshots</summary>

**Main Dashboard**
```
┌─────────────────────────────────────────────┐
│  🚗 Cars Database                           │
│  PHP + MariaDB + Docker                     │
├─────────────────────────────────────────────┤
│  [Add Car] [Delete Car] [Search] [Subquery]│
│                                             │
│  ┌───────────────────────────────────────┐ │
│  │ ID │ Manufacturer │ Model │ Year ... │ │
│  ├───────────────────────────────────────┤ │
│  │ 1  │ BMW         │ 320i  │ 2022 ... │ │
│  │ 2  │ Audi        │ A3    │ 2021 ... │ │
│  └───────────────────────────────────────┘ │
└─────────────────────────────────────────────┘
```

</details>

### Live Features
- 🎯 **Real-time search** across all fields
- ⚡ **Instant feedback** on operations
- 🎨 **Responsive design** works on all devices

## 🚀 Quick Start

### Prerequisites
- [Docker](https://docs.docker.com/get-docker/) (version 20.10+)
- [Docker Compose](https://docs.docker.com/compose/install/) (version 2.0+)
- Git

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/Cars_database.git
cd Cars_database

# 2. Start the application
docker compose up -d --build

# 3. Wait for initialization (10-15 seconds)
docker compose logs -f

# 4. Access the application
# Open your browser at: http://localhost:8080
```

That's it! 🎉 The application should now be running.

## 📋 Usage

### Basic Operations

#### Adding a Car
1. Navigate to the **Add Car** section
2. Select manufacturer, type, and fill in details
3. Click **Add Car**
4. The new entry appears in the table immediately

#### Searching
1. Enter any term in the search box (brand, model, year, etc.)
2. Results filter automatically
3. Click **Clear** to reset

#### Deleting a Car
1. Find the car's ID in the table
2. Enter the ID in the **Delete Car** section
3. Click **Delete** to remove

#### Count Cars (Subquery Demo)
1. Select a manufacturer from the dropdown
2. Click **Count Cars**
3. See the total number of cars for that brand

### Docker Commands

```bash
# View running containers
docker compose ps

# View logs
docker compose logs -f

# Stop the application
docker compose down

# Stop and remove volumes (fresh start)
docker compose down -v

# Rebuild after code changes
docker compose up -d --build

# Access database shell
docker compose exec db mysql -u cars_user -p cars_db
# Password: cars_pass

# Access web container shell
docker compose exec web bash
```

## 🏗️ Architecture

```
┌─────────────────────────────────────────────┐
│                  Browser                    │
└────────────────┬────────────────────────────┘
                 │ HTTP :8080
        ┌────────▼────────┐
        │   Web Container │
        │   PHP 8.2       │
        │   Apache        │
        └────────┬────────┘
                 │ PDO Connection
        ┌────────▼────────┐
        │   DB Container  │
        │   MariaDB 10.4  │
        │   Port 3306     │
        └─────────────────┘
                 │
        ┌────────▼────────┐
        │  Docker Volume  │
        │  (Persistent)   │
        └─────────────────┘
```

## 📁 Project Structure

```
Cars_database/
├── app/
│   ├── config/
│   │   └── login.php           # Database connection config
│   ├── public/
│   │   └── index.php           # Main application file
│   └── style.css               # Dark theme CSS
├── db/
│   └── init.sql                # Database schema & seed data
├── docker-compose.yml          # Service orchestration
├── Dockerfile                  # PHP + Apache image
├── .gitignore                  # Git ignore rules
└── README.md                   # This file
```

## 🛠️ Tech Stack

### Backend
- **PHP 8.2** - Server-side logic
- **PDO** - Database abstraction layer
- **MariaDB 10.4** - Relational database

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Custom dark theme with gradients
- **Vanilla JavaScript** - Minimal client-side logic

### DevOps
- **Docker** - Containerization
- **Docker Compose** - Multi-container orchestration
- **Apache** - Web server

### Database Design
- **3NF Normalization** - Proper table relationships
- **Foreign Keys** - Referential integrity
- **Indexes** - Optimized queries
- **Constraints** - Data validation at DB level

## 🗃️ Database Schema

```sql
manufacturers
├── manufacturer_id (PK)
├── manufacturer_name
├── country_of_origin
└── founded_year

vehicle_types
├── type_id (PK)
└── type_name

cars
├── car_id (PK)
├── manufacturer_id (FK)
├── model
├── year
├── type_id (FK)
├── country_of_origin
└── price_usd
```

## 🔒 Security Features

- ✅ **Prepared Statements** - Prevents SQL injection
- ✅ **HTML Escaping** - Prevents XSS attacks
- ✅ **Password Protection** - Database credentials secured
- ✅ **Input Validation** - Server-side validation
- ✅ **Error Handling** - Graceful error messages

## 🐛 Troubleshooting

### Issue: Port 8080 already in use

**Solution:**
```bash
# Change port in docker-compose.yml
services:
  web:
    ports:
      - "9000:80"  # Change to any available port
```

### Issue: Database connection refused

**Solutions:**
```bash
# 1. Check if containers are running
docker compose ps

# 2. View database logs
docker compose logs db

# 3. Restart with fresh database
docker compose down -v
docker compose up -d --build
```

### Issue: Changes not reflecting

**Solution:**
```bash
# Rebuild containers
docker compose down
docker compose up -d --build
```

### Issue: Old volumes causing conflicts

**Solution:**
```bash
# Remove all volumes
docker compose down -v

# Clean Docker system (CAREFUL - removes all unused data)
docker system prune -a --volumes

# Start fresh
docker compose up -d --build
```

## 🚀 Deployment

### Production Considerations

**⚠️ Before deploying to production:**

1. **Change default passwords** in `docker-compose.yml`
2. **Use environment variables** for sensitive data
3. **Enable HTTPS** with SSL certificates
4. **Set up backups** for database volumes
5. **Configure proper logging**
6. **Add rate limiting**
7. **Enable CORS** if needed for API access

### Environment Variables

Create a `.env` file:

```env
# Database
MYSQL_ROOT_PASSWORD=your_secure_password
MYSQL_DATABASE=cars_db
MYSQL_USER=cars_user
MYSQL_PASSWORD=your_secure_password

# Application
APP_ENV=production
APP_DEBUG=false
```

Then update `docker-compose.yml`:

```yaml
services:
  db:
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: ${MYSQL_DATABASE}
      MYSQL_USER: ${MYSQL_USER}
      MYSQL_PASSWORD: ${MYSQL_PASSWORD}
```

## 📈 Performance

- ⚡ **Fast queries** - Indexed columns for optimal performance
- 🔄 **Connection pooling** - Efficient database connections
- 💾 **Volume persistence** - Data survives container restarts
- 🏥 **Health checks** - Automatic recovery on failures

## 🤝 Contributing

Contributions are welcome! Here's how:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 coding standards
- Write descriptive commit messages
- Add comments for complex logic
- Test your changes locally

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- PHP Documentation - https://www.php.net/docs.php
- MariaDB Documentation - https://mariadb.org/documentation/
- Docker Documentation - https://docs.docker.com/
- Bootstrap for inspiration

## 👤 Author

**Your Name**

- GitHub: [@yourusername](https://github.com/yourusername)
- LinkedIn: [Your Name](https://linkedin.com/in/yourprofile)
- Portfolio: [yourwebsite.com](https://yourwebsite.com)

## 💼 Open to Opportunities

I'm currently seeking opportunities as a **Junior Full-Stack Developer** or **Backend Developer**. Feel free to reach out!

---

<div align="center">

### ⭐ If you found this project helpful, please give it a star!

Made with ❤️ and ☕ | © 2025

</div>
