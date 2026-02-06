# Project: Habits REST API

## 1. Project Overview

### Environment
- **Development Environment**: Docker-based (all tools run through Docker containers)
- **PHP Version**: 8.4
- **Framework**: Slim Framework 4.x
- **Language**: Portuguese (validation messages, error messages, test output) and English (code and documentation comments)

### Key Characteristics
- RESTful API architecture
- Layered architecture inspired by Domain-Driven Design (DDD) and Clean Architecture
- Dependency injection using PHP-DI
- Docker-first development workflow

---

## 2. CRITICAL RULES ⚠️

These rules must NEVER be violated under any circumstances:

### Rule #1: No Git Operations
**NEVER** use shell commands like `git add` or `git commit`. All Git operations (staging and committing) must be done manually by the user. The assistant should only create, modify, or delete files as requested.

### Rule #2: No Local Composer
**NEVER** suggest running Composer locally. All Composer commands must be executed through Docker:
```bash
docker compose exec api composer <command>
```

### Rule #3: Docker-Only Context
- All PHP tools run through Docker containers
- Always provide complete Docker commands with container name
- Consider Docker context for file permissions, paths, and execution environment
- Always show complete Docker commands with proper syntax

### Rule #4: No Hardcoded Credentials
- Never hardcode credentials, secrets, or sensitive data
- Always use environment variables for sensitive information
- Ensure newly created sensitive files (`.env`, private keys, logs, temporary files) are immediately added to `.gitignore`

---

## 3. Architecture & Code Structure

### 3.1 Layered Architecture

The project follows a layered architecture inspired by Domain-Driven Design (DDD) and Clean Architecture principles:

#### **Presentation Layer** (`src/Presentation`)
- **Responsibility**: Handle HTTP requests and responses
- **Contains**: API controllers that receive requests and return responses
- **Dependencies**: Can depend on Application layer

#### **Application Layer** (`src/Application`)
- **Responsibility**: Orchestrate business logic
- **Contains**: Use cases, DTOs, validation logic
- **Dependencies**: Can depend on Domain layer

#### **Domain Layer** (`src/Domain`)
- **Responsibility**: Core business logic
- **Contains**: Business entities, value objects, repository interfaces
- **Dependencies**: Independent of frameworks and external dependencies
- **Principle**: This is the most stable layer

#### **Infrastructure Layer** (`src/Infrastructure`)
- **Responsibility**: Technical implementations
- **Contains**: Database repositories, caching services, external services, HTTP clients, mailers, security implementations
- **Dependencies**: Implements interfaces defined in Domain layer

### 3.2 Design Principles

**SOLID Principles**: Organize code following SOLID principles throughout the codebase.

**Key Principles**:
- **Single Responsibility**: Keep methods small and focused on one task
- **Dependency Injection**: Use PHP-DI instead of instantiating objects directly
- **Composition Over Inheritance**: Prefer composition when appropriate
- **Interface Segregation**: Define focused repository interfaces in Domain layer

---

## 4. Code Quality Standards

### 4.1 Code Style (PSR-12)

Follow PSR-12 (PHP Standards Recommendations) for all PHP code:

- Use strict type declarations at the beginning of files:
  ```php
  declare(strict_types=1);
  ```
- Use type hints for function parameters and return types whenever possible
- Use class constants instead of magic strings when appropriate
- Avoid global functions; prefer class methods or namespaced functions
- Follow existing coding style in the project
- Always use Constructor Property Promotion to simplify class property declarations

### 4.2 Documentation (PHPDoc)

Ensure all new code has proper PHPDoc comment blocks:

**Required annotations**:
- `@param` - Document all parameters
- `@return` - Document return types
- `@throws` - Document exceptions that can be thrown
- `@var` - Document class properties

**Best practices**:
- Document the purpose and behavior of complex classes and methods
- Add usage examples in comments when functionality is complex
- Keep documentation up-to-date with code changes

### 4.3 Code Quality Practices

- Keep cyclomatic complexity low for maintainability
- Avoid deep nesting; refactor complex conditionals into separate methods
- Use code formatters (PHP-CS-Fixer) to maintain consistent style
- Prefer PSR-4 autoloading for class loading
- Follow PSR standards: PSR-4, PSR-12, PSR-7

---

## 5. Security Requirements

### Authentication & Authorization
- Use JWT tokens for API authentication
- Implement proper token validation and expiration

### Input Validation
- Input validation and sanitization required for all user inputs
- Data input validations using `symfony/validator` should be placed in Request DTOs (`src/Application/DTO`)

### Environment Variables
- Always use environment variables for sensitive data
- Never hardcode credentials, API keys, or secrets
- Store configuration in `.env` files (excluded from version control)

### Sensitive Files
- Always ensure newly created sensitive files are immediately added to `.gitignore`:
  - `.env` files
  - Private keys
  - Log files
  - Temporary files with sensitive data

---

## 6. Development Workflow

### 6.1 Performance and Best Practices

- Implement caching where appropriate (Redis, Memcached, or file cache)
- Use dependency injection (PHP-DI) instead of instantiating objects directly
- Prefer composition over inheritance when appropriate
- Keep methods small and focused (Single Responsibility Principle)

### 6.2 Validation

- Place data input validations using `symfony/validator` in Request DTOs
- Location: `src/Application/DTO`
- Validate at the application layer boundary

### 6.3 Testing Strategy

#### Test Structure
When creating functional tests:
- Create a folder for each group of endpoints
- Inside the folder root, create a test file containing all tests for the respective endpoint
- Extend the `FunctionalTestCase` class

#### Test Organization
- **Unit Tests** (`tests/Unit`): Test individual components in isolation
- **Integration Tests** (`tests/Integration`): Test component interactions
- **Functional Tests** (`tests/Functional`): Test API endpoints end-to-end

### 6.4 Refactoring Guidelines

When refactoring existing code:
- Maintain existing functionality
- Preserve environment variable usage
- Keep Docker compatibility
- Update `composer.json` if needed (using `docker compose exec api composer` commands)
- Ensure all configuration files in `tools/` directory are properly referenced

---

## 7. Commands Reference

### 7.1 Composer Commands

```bash
# Install dependencies
docker compose exec api composer install

# Update dependencies
docker compose exec api composer update

# Require new package
docker compose exec api composer require <package/name>

# Require dev package
docker compose exec api composer require --dev <package/name>

# Remove package
docker compose exec api composer remove <package/name>
```

**NOTE**: Use Composer scripts when available. Prefer `composer test` over direct PHPUnit calls for common tasks.

### 7.2 Code Quality Commands

```bash
# Check code style (dry-run, no changes)
docker compose exec api composer cs-check

# Fix code style automatically
docker compose exec api composer cs-fix

# Run Rector refactoring
docker compose exec api composer rector

# Simulate Rector refactoring (dry-run)
docker compose exec api composer rector:dry
```

### 7.3 Testing Commands

#### Basic Testing
```bash
# Run all tests
docker compose exec api composer test

# Run tests with detailed output (testdox)
docker compose exec api composer test:testdox

# Run only unit tests
docker compose exec api composer test:unit

# Run only integration tests
docker compose exec api composer test:integration

# Run only functional tests (API)
docker compose exec api composer test:functional

# Generate code coverage report
docker compose exec api composer test:coverage
```

#### Advanced Testing Workflow

**1. Run specific test file with text coverage:**
```bash
# Replace <TestFileName> with actual test file name
# Replace <SourceDirectory> with corresponding source directory for coverage filtering
docker compose exec api vendor/bin/phpunit tests/Unit/Domain/Entity/<TestFileName>.php \
  --coverage-filter src/Domain/Entity \
  --coverage-text
```

**NOTE**: If you get a "No filter is configured" warning, ensure `--coverage-filter` points to the *source directory* (e.g., `src/Domain/Entity`), not the test file.

**2. Inspect HTML coverage reports:**
```bash
# Generate full HTML code coverage report (output: tools/coverage/)
docker compose exec api composer test:coverage

# View summary for specific module (e.g., Domain)
cat tools/coverage/Domain/index.html

# Inspect line-by-line coverage for specific class (e.g., ErrorLog)
cat tools/coverage/Domain/Entity/ErrorLog.php.html
```

**NOTE**: Coverage reports are typically ignored by `.gitignore`. Use `cat` to inspect them directly.

**3. Iterative test development cycle:**
- **Identify gaps**: Use coverage reports (HTML or text) to find untested lines/methods/classes
- **Read source & existing test**: Understand the code and current test approach
- **Write/Refactor test**: Add new test cases or improve existing ones to cover identified gaps
- **Run specific test with coverage**: Use commands above to verify changes and coverage
- **Debug**: Analyze test output; pay attention to `TypeErrors` which may indicate bugs in main codebase
- **Repeat**: Continue until desired coverage for the component is achieved

---

## 8. File Structure

```
project/
├── config/                      # Application configuration
│   ├── bootstrap.php            # Application bootstrap
│   ├── container.php            # Dependency injection container
│   ├── routes.php               # API routes definition
│   └── settings.php             # Application settings
│
├── database/
│   └── schema.sql               # Database schema
│
├── docs/                        # Project documentation
│   ├── API.md                   # API documentation
│   └── postman_collection.json  # Postman collection for testing
│
├── public/
│   └── index.php                # Application entry point
│
├── src/
│   ├── Application/             # Application layer (use cases)
│   │   ├── DTO/                 # Data Transfer Objects & validation
│   │   ├── UseCase/             # Business use cases
│   │   └── Validation/          # Validation logic
│   │
│   ├── Domain/                  # Domain layer (business logic)
│   │   ├── Entity/              # Business entities
│   │   ├── Repository/          # Repository interfaces
│   │   └── Exception/           # Domain exceptions
│   │
│   ├── Infrastructure/          # Infrastructure layer (technical implementations)
│   │   ├── Http/                # HTTP clients
│   │   ├── Persistence/         # Database repositories (implementations)
│   │   ├── Security/            # Security implementations (JWT, etc.)
│   │   └── Mailer/              # Email services
│   │
│   └── Presentation/            # Presentation layer (API)
│       └── Api/V1/              # API version 1 controllers
│
├── tests/
│   ├── Unit/                    # Unit tests
│   ├── Integration/             # Integration tests
│   └── Functional/              # Functional tests (API)
│
├── tools/                       # Development tools configuration
│   ├── .php-cs-fixer.dist.php   # PHP CS Fixer configuration
│   ├── phpunit.xml              # PHPUnit configuration
│   └── rector.php               # Rector configuration
│
└── composer.json                # PHP dependencies
```

---

## 9. Quick Reference

### Must Remember
✅ Always use Docker commands: `docker compose exec api composer <command>`  
✅ Never run Composer locally  
✅ Never use Git commands (git add, git commit)  
✅ Follow PSR-12 for code style  
✅ Use strict types: `declare(strict_types=1);`  
✅ Document with PHPDoc  
✅ Use environment variables for sensitive data  
✅ Add sensitive files to `.gitignore`  
✅ Follow layered architecture  
✅ Use dependency injection (PHP-DI)  

### Provide Working Code
- Test commands before suggesting
- Consider Docker context for paths and permissions
- Use environment variables, never hardcode configuration
- Follow existing project patterns and style