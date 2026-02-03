# 🚀 Setup Instructions

## Estructura de Archivos Requerida

Antes de empezar, asegúrate de tener esta estructura:

```
Cars_database/
├── app/
│   ├── config/
│   │   └── login.php
│   ├── public/
│   │   └── index.php
│   └── style.css
├── db/
│   └── init.sql
├── docker-compose.yml
├── Dockerfile
├── .gitignore
└── README.md
```

## 📥 Instalación Paso a Paso

### 1. Verificar Requisitos

```bash
# Verificar Docker
docker --version
# Debe mostrar: Docker version 20.10.x o superior

# Verificar Docker Compose
docker compose version
# Debe mostrar: Docker Compose version v2.x.x o superior
```

### 2. Clonar el Repositorio

```bash
git clone https://github.com/tuusuario/Cars_database.git
cd Cars_database
```

### 3. Limpieza (Importante si ya tenías el proyecto)

```bash
# Detener contenedores anteriores
docker compose down -v

# Limpiar volúmenes viejos (SOLO si tienes problemas)
docker volume prune

# Limpiar todo el sistema Docker (CUIDADO - borra todo)
# docker system prune -a --volumes -f
```

### 4. Construir y Levantar Contenedores

```bash
# Opción 1: Build y start en un comando
docker compose up -d --build

# Opción 2: Build separado
docker compose build
docker compose up -d
```

### 5. Verificar que Todo Funcione

```bash
# Ver estado de contenedores
docker compose ps

# Deberías ver algo como:
# NAME        IMAGE              STATUS         PORTS
# cars_db     mariadb:10.4       Up (healthy)   3306/tcp
# cars_web    cars_database-web  Up             0.0.0.0:8080->80/tcp
```

### 6. Ver Logs en Tiempo Real

```bash
# Ver todos los logs
docker compose logs -f

# Ver solo logs de la base de datos
docker compose logs -f db

# Ver solo logs del servidor web
docker compose logs -f web
```

### 7. Acceder a la Aplicación

Abre tu navegador en: **http://localhost:8080**

Si todo funcionó correctamente, deberías ver la interfaz de Cars Database.

---

## 🔧 Comandos Útiles

### Gestión de Contenedores

```bash
# Detener contenedores
docker compose stop

# Iniciar contenedores (sin rebuild)
docker compose start

# Reiniciar contenedores
docker compose restart

# Detener y eliminar contenedores + volúmenes
docker compose down -v

# Ver recursos usados
docker stats
```

### Acceso a Contenedores

```bash
# Entrar al contenedor web (bash)
docker compose exec web bash

# Entrar a la base de datos (MySQL client)
docker compose exec db mysql -u cars_user -p
# Password: cars_pass

# Ejecutar comandos PHP
docker compose exec web php -v
docker compose exec web php /var/www/html/public/index.php
```

### Debugging

```bash
# Ver variables de entorno del contenedor
docker compose exec web env

# Ver archivos en el contenedor
docker compose exec web ls -la /var/www/html

# Verificar conexión a la BD desde el contenedor web
docker compose exec web php -r "new PDO('mysql:host=db;dbname=cars_db', 'cars_user', 'cars_pass');"
```

---

## ❌ Solución de Errores Comunes

### Error: "dockerfile not found"

**Causa:** Docker busca `dockerfile` pero el archivo se llama `Dockerfile` (con D mayúscula)

**Solución:**
```bash
# En docker-compose.yml, cambiar:
dockerfile: Dockerfile  # (con D mayúscula)
```

### Error: "Access denied for user"

**Causa:** Credenciales incorrectas o volúmenes viejos

**Solución:**
```bash
# 1. Eliminar volúmenes
docker compose down -v

# 2. Verificar credenciales en docker-compose.yml y login.php
# Deben coincidir:
# - MYSQL_USER: cars_user
# - MYSQL_PASSWORD: cars_pass
# - MYSQL_DATABASE: cars_db

# 3. Rebuild
docker compose up -d --build
```

### Error: "Port 8080 already in use"

**Solución:**
```bash
# Opción 1: Cambiar puerto en docker-compose.yml
ports:
  - "9000:80"

# Opción 2: Liberar el puerto
# En Windows:
netstat -ano | findstr :8080
taskkill /PID <PID> /F

# En Linux/Mac:
sudo lsof -i :8080
sudo kill -9 <PID>
```

### Error: "init.sql: No such file or directory"

**Causa:** Ruta incorrecta en docker-compose.yml

**Solución:**
```bash
# Verificar que existe el archivo
ls -la db/init.sql

# En docker-compose.yml debe ser:
volumes:
  - ./db/init.sql:/docker-entrypoint-initdb.d/init.sql
```

### La página no carga / Error 500

**Diagnóstico:**
```bash
# 1. Ver logs del web container
docker compose logs web

# 2. Verificar que Apache esté corriendo
docker compose exec web ps aux | grep apache

# 3. Verificar permisos
docker compose exec web ls -la /var/www/html

# 4. Probar conexión a DB
docker compose exec web php -r "echo (new PDO('mysql:host=db;dbname=cars_db', 'cars_user', 'cars_pass')) ? 'OK' : 'FAIL';"
```

### Base de datos vacía

**Causa:** init.sql no se ejecutó

**Solución:**
```bash
# 1. Eliminar volúmenes
docker compose down -v

# 2. Verificar que init.sql está en la ruta correcta
ls -la db/init.sql

# 3. Rebuild
docker compose up -d --build

# 4. Verificar que se ejecutó
docker compose logs db | grep init.sql
```

---

## 🧪 Testing

### Verificar Instalación de PHP

```bash
docker compose exec web php -v
# Debe mostrar: PHP 8.2.x

docker compose exec web php -m | grep pdo
# Debe mostrar: pdo_mysql
```

### Verificar Base de Datos

```bash
# Entrar a MySQL
docker compose exec db mysql -u cars_user -p cars_db

# Ejecutar queries
SHOW TABLES;
SELECT COUNT(*) FROM cars;
SELECT * FROM manufacturers;
```

### Test de Conexión PHP → MariaDB

```bash
docker compose exec web php -r "
try {
  \$pdo = new PDO('mysql:host=db;dbname=cars_db', 'cars_user', 'cars_pass');
  echo 'Connection: SUCCESS\n';
} catch (PDOException \$e) {
  echo 'Connection: FAILED - ' . \$e->getMessage() . '\n';
}
"
```

---

## 📊 Monitoreo

```bash
# Ver uso de recursos
docker stats

# Ver espacio en disco
docker system df

# Ver logs específicos
docker compose logs --tail=50 web
docker compose logs --since=10m db
```

---

## 🔄 Actualización del Código

```bash
# 1. Pull cambios de Git
git pull origin main

# 2. Rebuild containers
docker compose up -d --build

# 3. Si hay cambios en la BD, eliminar volúmenes
docker compose down -v
docker compose up -d --build
```

---

## 📝 Notas Importantes

1. **Volúmenes persistentes:** Los datos de la BD se guardan en un volumen Docker y sobreviven a reinicios de contenedores.

2. **Hot reload:** Los cambios en archivos PHP se reflejan inmediatamente (no necesitas rebuild).

3. **Primera ejecución:** El contenedor de MariaDB tarda 10-15 segundos en inicializarse completamente.

4. **Health checks:** El contenedor web espera a que la DB esté completamente lista antes de iniciar.

---

## 🎯 Next Steps

Una vez que todo funcione:

1. ✅ Prueba agregar, buscar y eliminar autos
2. ✅ Explora las subqueries
3. ✅ Revisa el código en `app/public/index.php`
4. ✅ Personaliza el diseño en `app/style.css`
5. ✅ Añade nuevas funcionalidades
6. ✅ Sube tu proyecto a GitHub

---

¿Necesitas ayuda? Abre un issue en GitHub o contacta al autor.
