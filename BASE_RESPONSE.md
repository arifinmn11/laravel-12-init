# Base Response Format Documentation

## Overview

All API endpoints in this Laravel application return responses in a consistent, standardized format using the `ApiResponse` trait and global exception handlers.

## Response Structure

### Success Response (2xx)

```json
{
  "success": true,
  "message": "Operation successful message",
  "data": {
    // Response data here
  }
}
```

### Error Response (4xx, 5xx)

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors or additional error details (optional)
  }
}
```

### Paginated Response

```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [
    // Array of items
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15
  }
}
```

## HTTP Status Codes

| Status Code | Method | Description |
|-------------|--------|-------------|
| 200 | `successResponse()` | General success |
| 201 | `createdResponse()` | Resource created |
| 200 | `deletedResponse()` | Resource deleted |
| 400 | `errorResponse()` | Bad request |
| 401 | `unauthorizedResponse()` | Unauthenticated (auto-handled) |
| 403 | `forbiddenResponse()` | Insufficient permissions (auto-handled) |
| 404 | `notFoundResponse()` | Resource not found (auto-handled) |
| 422 | `validationErrorResponse()` | Validation failed (auto-handled) |
| 500 | - | Server error (auto-handled) |

## ApiResponse Trait Methods

### Available in Controllers

Add `use ApiResponse;` trait to any controller:

```php
use App\Traits\ApiResponse;

class YourController extends Controller
{
    use ApiResponse;
    
    // Now you can use all response methods
}
```

### Success Responses

```php
// General success (200)
return $this->successResponse($data, 'Custom message');

// Resource created (201)
return $this->createdResponse($data, 'Resource created successfully');

// Resource deleted (200)
return $this->deletedResponse('Resource deleted successfully');

// Paginated data
return $this->paginatedResponse($paginatedData, 'Data retrieved');
```

### Error Responses

```php
// General error (400 or custom)
return $this->errorResponse('Error message', 400);

// Not found (404)
return $this->notFoundResponse('Product not found');

// Unauthorized (401)
return $this->unauthorizedResponse('Invalid credentials');

// Forbidden (403)
return $this->forbiddenResponse('Insufficient permissions');

// Validation error (422)
return $this->validationErrorResponse($validator->errors(), 'Validation failed');
```

## Global Exception Handling

The application automatically catches and formats the following exceptions:

### 401 - Unauthenticated
Triggered when:
- User is not logged in
- Token is invalid or expired
- `auth:api` middleware fails

Response:
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 403 - Forbidden
Triggered when:
- User doesn't have required role
- `role:admin` middleware fails
- Authorization policies fail

Response:
```json
{
  "success": false,
  "message": "Unauthorized. Required roles: admin"
}
```

### 404 - Not Found
Triggered when:
- Route doesn't exist
- Resource not found (e.g., `/api/products/999`)
- Model binding fails

Response:
```json
{
  "success": false,
  "message": "Resource not found."
}
```

### 422 - Validation Error
Triggered when:
- FormRequest validation fails
- Manual validation fails
- Data doesn't meet requirements

Response:
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 405 - Method Not Allowed
Triggered when:
- Wrong HTTP method used (e.g., GET instead of POST)

Response:
```json
{
  "success": false,
  "message": "Method not allowed."
}
```

### 500 - Server Error
Triggered when:
- Unhandled exceptions occur
- Database errors
- Server-side errors

Response (Production):
```json
{
  "success": false,
  "message": "Server error."
}
```

Response (Development):
```json
{
  "success": false,
  "message": "Actual error message for debugging"
}
```

## Examples

### Product API Examples

#### List Products (Success)
```bash
GET /api/products
Authorization: Bearer {token}
```

Response (200):
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Laptop",
      "sku": "LAPTOP-001",
      "price": 999.99,
      "formatted_price": "$999.99",
      "stock": 50
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  }
}
```

#### Create Product (Validation Error)
```bash
POST /api/products
Authorization: Bearer {admin-token}
Content-Type: application/json

{
  "name": "",
  "price": -10
}
```

Response (422):
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "name": ["Product name is required."],
    "sku": ["SKU is required."],
    "price": ["Price must be at least 0."],
    "stock": ["Stock quantity is required."]
  }
}
```

#### Access Admin Route (Unauthorized)
```bash
POST /api/products
Authorization: Bearer {user-token}  // Non-admin user
```

Response (403):
```json
{
  "success": false,
  "message": "Unauthorized. Required roles: admin"
}
```

#### Get Product (Not Found)
```bash
GET /api/products/9999
Authorization: Bearer {token}
```

Response (404):
```json
{
  "success": false,
  "message": "Resource not found."
}
```

### Auth API Examples

#### Login (Success)
```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

Response (200):
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com"
    },
    "roles": ["admin"],
    "permissions": ["view products", "create products", "edit products", "delete products"],
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer"
  }
}
```

#### Login (Invalid Credentials)
```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "wrong-password"
}
```

Response (401):
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

## Implementation Notes

1. **Global Handlers**: Exception handlers in `bootstrap/app.php` automatically format errors for all API routes (`/api/*`)
2. **Controllers**: Use `ApiResponse` trait methods for manual responses
3. **Middleware**: `CheckRole` throws proper exceptions that are caught by global handlers
4. **Consistency**: All errors follow the same format whether thrown by framework or custom code
5. **Environment**: Error messages are sanitized in production, detailed in development

## Testing

All API responses can be tested at: `http://localhost:8000/docs/api`

The Scramble documentation will show the response format for each endpoint.
