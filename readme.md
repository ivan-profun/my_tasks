# My tasks
A simple SPA.

## Technology Stack

### Backend: 
- Laravel 12
- PostgreSQL
- Laravel Sanctum (API auth)

### Frontend: 
- Vue 3
- Composition API
- Vue Router
- Pinia
- Axios
- Common CSS

## Requirements

- Docker
- Docker Compose
- Node.js 18+ and npm

## Installation

### 1. Clone the repository
   ```bash
   git clone https://github.com/ivan-profun/my_tasks
   cd <project_dir>
   ```

### 2. Configure docker environment
   ```bash
   cd ./docker
   cp .env_template .env
   ```

### 3. Build and start containers
   ```bash
   docker compose up -d --build
   ```

### 4. Configure laravel environment
   ```bash
   cd ../src
   cp .env.example .env
   ```

### 5. Run migrations
   ```bash
   cd ../docker
   docker compose exec app php artisan migrate
   ```

### 6. Run frontend

#### for a dev server
   ```bash
   cd ../src
   npm install
   npm run dev
   ```
   
#### for a prod server
   ```bash
   cd ../src
   npm install
   npm run build
   ```

### Making default data
   ```bash
   cd ../docker
   docker compose exec app php artisan db:seed
   ```

## Usage

After seeding, log in with:

| Field    | Value            |
| -------- | ---------------- |
| Email    | `test@test.test` |
| Password | `12345678`       |

## API

| Method | Endpoint                   | Description                           |
| ------ | -------------------------- | ------------------------------------- |
| GET    | `/sanctum/csrf-cookie`     | Get CSRF cookie before login/register |
| POST   | `/api/register`            | Register a new user                   |
| POST   | `/api/login`               | Log in                                |
| POST   | `/api/logout`              | Log out                               |
| GET    | `/api/user`                | Get current authenticated user        |
| GET    | `/api/tasks`               | List current user's tasks             |
| POST   | `/api/tasks`               | Create a task                         |
| PUT    | `/api/tasks/{id}`          | Update a task (own only)              |
| DELETE | `/api/tasks/{id}`          | Delete a task (own only)              |
| POST   | `/api/tasks/{id}/reminder` | Set or update a reminder              |
| DELETE | `/api/tasks/{id}/reminder` | Remove a reminder                     |
