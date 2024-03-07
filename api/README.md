# Habits API - PHP 8 with Slim Framework 4 and JWT Auth 

This template should help get you started developing with this API in Docker.

## Technologies Used

- Slim Framework 4: A micro-framework for PHP that helps you quickly write simple yet powerful web applications and APIs.
- JWT Auth: JSON Web Token authentication mechanism for securing API endpoints.
- Docker: Containerization platform used to ensure consistency and portability across environments.
- MySQL: Database management system utilized for storing application data.
- PHP DotEnv: Library for loading environment variables from `.env` files to configure the application.
- PHP Mailer: Library for sending emails from PHP applications.

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

## API Documentation

- [User Registration](#user-registration)
- [User Authentication](#user-authentication)
- [User Forgot Password](#user-forgot-password)
- [User Forgot Token](#user-forgot-token)
- [Habits Create](#habits-create)
- [Habits Summary](#habits-summary)
- [Habits Day](#habits-day)
- [Habits Toggle](#habits-toggle)

#### User Registration

```http
  POST /signup
```

| Parameter  | Type     | Description                                             |
| :--------  | :------- | :------------------------------------------------------ |
| `name`     | `string` | **Required**. User's full name                          |
| `email`    | `string` | **Required**. User's e-mail address                     |
| `password` | `string` | **Required**. User's password                           |

**Observation:** The parameters above should be passed within a single JSON object.

**Response:** Void

#### User Authentication

```http
  POST /signin
```

| Parameter  | Type     | Description                                             |
| :--------  | :------- | :------------------------------------------------------ |
| `email`    | `string` | **Required**. User's email address                       |
| `password` | `string` | **Required**. User's password                           |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** Authenticated user data.

#### User Forgot Password

```http
  POST /forgot
```

| Parameter  | Type     | Description                                             |
| :--------  | :------- | :------------------------------------------------------ |
| `email`    | `string` | **Required**. User's email address                      |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** Send reset token to user e-mail.

#### User Forgot Token

```http
  POST /forgot/token
```

| Parameter  | Type     | Description                                             |
| :--------  | :------- | :------------------------------------------------------ |
| `token`    | `string` | **Required**. Token sent by email to the user           |

**Observation:** The parameters should be passed within a single JSON object.

**Response:** Void

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

**Response:** Void

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
  PUT /habits/toggle
```

| Parameter | Type       | Description                                        |
| :-------- | :--------- | :------------------------------------------------- |
| `userId`  | `integer`  | **Required**. Logged user ID                       |

**Response:** Void