# Habits API - PHP 8 with Slim Framework 4 and JWT Auth 

This template should help get you started developing with this API in Docker.

## Technologies Used

- Slim Framework 4: A micro-framework for PHP that helps you quickly write simple yet powerful web applications and APIs.
- JWT Auth: JSON Web Token authentication mechanism for securing API endpoints.
- Docker: Containerization platform used to ensure consistency and portability across environments.
- MySQL: Database management system utilized for storing application data.
- PHP DotEnv: Library for loading environment variables from `.env` files to configure the application.
- PHP Mailer: Library for sending emails from PHP applications.

---

## Build Containers

```sh
docker compose up -d
```

### Install Composer Dependencies

```sh
docker run --rm --interactive --tty \
  --volume $PWD:/app \
  composer install
```

## Set Enviroment Variables

Create a .env file using .env.example and set variables. This variables are configs to connect to the database(MySQL), sending email(PHP Mailer) and JWT config tokens

See: 
[PHP DotEnv Configuration Reference](https://github.com/vlucas/phpdotenv)
[PHP Mailer Configuration Reference](https://github.com/PHPMailer/PHPMailer)

## Conecting to Database

The HOSTNAME in .env file should be the same of docker-compose file db:container_name

---

## Authentication and Security

The API uses **JWT** for authentication and authorization.

### 1. **Obtaining the Token**

To obtain a JWT, the client must send valid credentials to the authentication endpoint:

```http
POST /signin
```

### **Request Body (JSON)**

```json
{
  "email": "user@email.com",
  "password": "YourPassword123"
}
```

### **Successful Response**

A valid response returns a **JWT token** with the following information:

```json
{
  "sub": "user-auth",
  "user": {
    "id": 3,
    "name": "User",
    "email": "user@email.com"
  },
  "iat": 1763490158,
  "exp": 1763493758
}
```

This token is required for accessing protected routes.

### 2. **Including the Token in Requests**

Include the token in all protected requests:

```
Authorization: Bearer <token>
```

### 3. **Token Expiration**

JWT tokens have an expiration time. After expiration, a new login is required.

---

## API Documentation

- [Users Registration](#users-registration)
- [Users Authentication](#users-authentication)
- [Users Update](#users-update)
- [Users Delete](#users-delete)
- [Users Forgot Password](#users-forgot-password)
- [Users Forgot Token](#users-forgot-token)
- [Users Reset Password](#users-reset-password)
- [Habits Details](#habits-details)
- [Habits Create](#habits-create)
- [Habits Update](#habits-update)
- [Habits Summary](#habits-summary)
- [Habits Day](#habits-day)
- [Habits Toggle](#habits-toggle)
- [Habits Delete](#habits-delete)

#### Users Registration

```http
  POST /signup
```

| Parameter  | Type     | Description                                             |
| :--------  | :------- | :------------------------------------------------------ |
| `name`     | `string` | **Required**. User's full name                          |
| `email`    | `string` | **Required**. User's e-mail address                     |
| `password` | `string` | **Required**. User's password                           |

**Observation:** The parameters above should be passed within a single JSON object.

**Response:** JWT with user data

#### Users Authentication

```http
  POST /signin
```

| Parameter  | Type     | Description                                             |
| :--------  | :------- | :------------------------------------------------------ |
| `email`    | `string` | **Required**. User's email address                      |
| `password` | `string` | **Required**. User's password                           |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** JWT with user data

#### Users Update

```http
  PUT /users/update/{id}
```

| Parameter | Type     | Description                                             |
| :-------- | :------- | :------------------------------------------------------ |
| `name`    | `string` | **Required**. User's name                               |
| `email`   | `string` | **Required**. User's email address                      |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** JWT with user data

#### Users Delete

```http
  DELETE /users/delete/{id}
```

**Observation:** No parameters needed.

**Response:** Void.

#### Users Forgot Password

```http
  POST /forgot
```

| Parameter  | Type     | Description                                             |
| :--------  | :------- | :------------------------------------------------------ |
| `email`    | `string` | **Required**. User's email address                      |

**Observation:** The parameters should be passed within a single JSON object.

**Note:** Send reset token to user e-mail.

**Response:** Void.

#### Users Forgot Token

```http
  POST /forgot/token
```

| Parameter  | Type     | Description                                             |
| :--------  | :------- | :------------------------------------------------------ |
| `token`    | `string` | **Required**. Token sent by email to the user           |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** Void

#### Users Reset Password

```http
  POST /forgot/reset
```

| Parameter    | Type      | Description                                             |
| :----------- | :-------- | :------------------------------------------------------ |
| `password`   | `string`  | **Required**. User's password                           |
| `userId`     | `integer` | **Required**. Logged user ID                            |
| `recoveryId` | `integer` | **Required**. Requested recovery ID                     |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** Void

#### Habits Details

```http
  POST /habits/{id}
```

**Observation:** No parameters needed.

**Response:** Habit data

#### Habits Create

```http
  POST /habits/create
```

| Parameter  | Type      | Description                                         |
| :--------- | :-------- | :-------------------------------------------------- |
| `title`    | `string`  | **Required**. Habit title                           |
| `weekDays` | `string`  | **Required**. Days of week string (Ex.: "0, 1, 2")  |
| `userId`   | `integer` | **Required**. Logged user ID                        |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** Habit data

#### Habits Update

```http
  POST /habits/update/{id}
```

| Parameter  | Type      | Description                                         |
| :--------- | :-------- | :-------------------------------------------------- |
| `title`    | `string`  | **Required**. Habit title                           |
| `weekDays` | `string`  | **Required**. Days of week string (Ex.: "0, 1, 2")  |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** Habit data

#### Habits Summary

```http
  POST /habits/summary
```

| Parameter  | Type      | Description                                        |
| :--------- | :-------- | :------------------------------------------------- |
| `userId`   | `integer` | **Required**. Logged user ID                       |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** Summary of habits

#### Habits Day

```http
  POST /habits/day
```

| Parameter | Type       | Description                                        |
| :-------- | :--------- | :------------------------------------------------- |
| `userId`  | `integer`  | **Required**. Logged user ID                       |
| `date`    | `datetime` | **Required**. Selected day date                    |

**Response:** Possible and completed habits list.

#### Habits Toggle

```http
  PUT /habits/{id}/toggle
```

| Parameter | Type       | Description                                        |
| :-------- | :--------- | :------------------------------------------------- |
| `userId`  | `integer`  | **Required**. Logged user ID                       |

**Response:** Void

#### Habits Delete

```http
  DELETE /habits/delete/{id}
```

**Observation:** No parameters needed.

**Response:** Void