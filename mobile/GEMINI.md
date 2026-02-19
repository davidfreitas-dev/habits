# Project: Habits Mobile App

## 1. Project Overview

### Environment
- **Framework**: Ionic Framework 7.x with Vue 3
- **Language**: JavaScript (Composition API with `<script setup>`)
- **Build Tool**: Vite
- **Mobile Engine**: Capacitor 8.x
- **State Management**: Pinia
- **Styling**: Vanilla CSS with Ionic Variables (CSS Variables)

### Key Characteristics
- Hybrid Mobile Architecture (iOS & Android)
- Layered architecture (Separation of Concerns)
- Composition API focused
- Modular components (UI, Layout, Domain)

---

## 2. CRITICAL RULES ⚠️

These rules must NEVER be violated under any circumstances:

### Rule #1: No Git Operations
**NEVER** use shell commands like `git add` or `git commit`. All Git operations (staging and committing) must be done manually by the user. The assistant should only create, modify, or delete files as requested.

### Rule #2: Composition API Only
**ALWAYS** use the Vue 3 Composition API with the `<script setup>` syntax. Avoid Options API or non-setup composition.

### Rule #3: Naming Convention
- **Components**: PascalCase (e.g., `HabitDay.vue`)
- **Composables**: camelCase starting with 'use' (e.g., `useLoading.js`)
- **Services/Stores**: PascalCase for exports, camelCase for files (e.g., `AuthService.js`, `auth.js`)
- **Views**: PascalCase (e.g., `Signin.vue`)

### Rule #4: No Hardcoded Credentials
- Never hardcode API keys, Base URLs, or sensitive data
- Always use `.env` files and `import.meta.env` for configuration
- Ensure `.env` files are in `.gitignore`

---

## 3. Architecture & Code Structure

### 3.1 Layered Architecture

The project follows a layered architecture to ensure separation of concerns:

#### **Presentation Layer** (`src/views`)
- **Responsibility**: Handle user interaction and screen structure
- **Organization**: Grouped by context (`auth`, `habits`, `settings`)
- **Dependencies**: Uses Components, Stores, and Composables

#### **Component Layer** (`src/components`)
- **`ui/`**: Base/Atomic components (Button, Input, Checkbox)
- **`layout/`**: Structural components (Header, Container, Modal)
- **`habits/`**: Domain-specific components (HabitDay, HabitForm)

#### **Service Layer** (`src/services`)
- **Responsibility**: Handle API communication (I/O)
- **Pattern**: Stateless objects that use the API client
- **Example**: `AuthService.login(credentials)`

#### **State Layer** (`src/stores`)
- **Responsibility**: Manage global application state
- **Contains**: Auth state, profile data, habit lists
- **Dependencies**: Calls Services to fetch data and updates local state

#### **Composables Layer** (`src/composables`)
- **Responsibility**: Reusable logic and lifecycle hooks
- **Contains**: Network status, toast notifications, date parsing

#### **API Client** (`src/api`)
- **Responsibility**: Axios configuration and interceptors
- **Location**: `src/api/index.js` (exported as `api`)

---

## 4. Code Quality Standards

### 4.1 Vue Style Guide
- Use `PascalCase` for component names in templates
- Use `kebab-case` for events (`@handle-change`)
- Keep components small and focused
- Prefer Vanilla CSS with CSS Variables for styling

### 4.2 Composables Pattern
- Encapsulate logic that uses Vue lifecycle hooks
- Return objects with refs and functions
- Example: `const { isLoading, withLoading } = useLoading()`

### 4.3 Documentation
- Use JSDoc for complex functions and service methods
- Document Props and Emits in components

---

## 5. Security & Authentication

### JWT Management
- Access and Refresh tokens are handled via `AuthStore` and `api` interceptors
- Tokens are stored in `localStorage`
- The `api` interceptor automatically handles 401 errors by attempting a token refresh

### Constants
- Always use `src/constants/storage.js` for storage keys
- Always use `src/constants/endpoints.js` for API paths

---

## 6. Development Workflow

### 6.1 Local Development
```bash
ionic serve
```

### 6.2 Mobile Platform Sync
```bash
# After making changes to web assets
npx cap sync
```

### 6.3 Testing Strategy
- **Unit Tests**: Using Vitest and Vue Test Utils (`tests/unit`)
- **E2E Tests**: Using Cypress (`tests/e2e`)

---

## 7. Commands Reference

### Dependency Management
```bash
npm install <package>
npm install --save-dev <package>
```

### Mobile Specifics
```bash
# Open native IDE
npx cap open ios
npx cap open android

# Build for production
npm run build
```

---

## 8. File Structure

```
mobile/
├── public/                      # Static assets
├── src/
│   ├── api/                    # Axios client configuration
│   ├── assets/                 # Images and icons
│   ├── components/             # Components organized by type
│   │   ├── ui/                 # Base UI components
│   │   ├── layout/             # Structural components
│   │   └── habits/             # Habit-specific components
│   ├── composables/            # Reusable Vue hooks
│   ├── constants/              # App-wide constants (endpoints, storage keys)
│   ├── router/                 # Vue Router configuration
│   ├── services/               # API communication layer
│   ├── stores/                 # Pinia state management
│   ├── theme/                  # Global styles and CSS variables
│   ├── views/                  # Pages organized by context
│   │   ├── auth/
│   │   ├── habits/
│   │   └── settings/
│   ├── App.vue                 # Main entry component
│   └── main.js                 # App initialization
├── tests/                      # Unit and E2E tests
└── vite.config.js              # Vite configuration
```

---

## 9. Quick Reference

### Must Remember
✅ Use `<script setup>` in all components  
✅ Follow the layered architecture (View -> Store -> Service -> API)  
✅ Use `@/` alias for all imports  
✅ Use `useToast` for user notifications  
✅ Use `useLoading` for async operations  
✅ Define all API endpoints in `src/constants/endpoints.js`  
✅ Never use Git commands  
✅ Verify Capacitor sync after asset changes  
