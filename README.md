# TechnicalAssessment-LimitOrderExchangeMiniEngine

This project is a Laravel-based backend for a cryptocurrency trading system. It provides APIs to manage user authentication, assets, and limit orders, including order matching.

---

## Requirements

- PHP >= 8.4
- Composer
- MySQL 8+
- Redis (optional, for queue jobs)
- Node.js & NPM (for frontend if needed)
- Laravel 12

---

## Installation

1. **Clone the repository**

```bash
git clone https://github.com/your-repo/trading-api.git
cd trading-api
````

2. **Install dependencies**

```bash
composer install
```

3. **Environment setup**

```bash
cp .env.example .env
```

Edit `.env` and set your database credentials and other configuration:

```dotenv
APP_NAME=TradingAPI
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trading_db
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
```

4. **Generate application key**

```bash
php artisan key:generate
```

5. **Run database migrations and seeders**

```bash
php artisan migrate --seed
```

This will create all tables including `users`, `orders`, `assets`, etc.

---

## Queue Setup

This API uses jobs for order matching. Ensure queue worker is running:

```bash
php artisan queue:work
```

> Alternatively, you can use `redis` or `database` queue driver as configured in `.env`.

---

## API Endpoints

### Authentication

| Method | Endpoint      | Description                 |
| ------ | ------------- | --------------------------- |
| POST   | `/api/login`  | Login and receive API token |
| POST   | `/api/logout` | Logout and revoke tokens    |
| GET    | `/api/me`     | Get authenticated user info |

### User & Assets

| Method | Endpoint       | Description                        |
| ------ | -------------- | ---------------------------------- |
| GET    | `/api/profile` | Get USD balance and asset balances |

### Orders

| Method | Endpoint                  | Description                      |
| ------ | ------------------------- | -------------------------------- |
| GET    | `/api/orders?symbol=BTC`  | Get all open orders for a symbol |
| POST   | `/api/orders`             | Create a limit order             |
| POST   | `/api/orders/{id}/cancel` | Cancel an open order             |

### Order Matching (Internal / Job)

| Method | Endpoint            | Description                     |
| ------ | ------------------- | ------------------------------- |
| POST   | `/api/orders/match` | Trigger order matching manually |

---

## Usage Example

**Login:**

```bash
curl -X POST http://localhost:8000/api/login \
-H "Content-Type: application/json" \
-d '{"email": "user@example.com", "password": "password"}'
```

**Create Limit Order:**

```bash
curl -X POST http://localhost:8000/api/orders \
-H "Authorization: Bearer YOUR_API_TOKEN" \
-H "Content-Type: application/json" \
-d '{
    "symbol": "BTC",
    "side": "buy",
    "price": "30000",
    "amount": "0.5"
}'
```

**Cancel Order:**

```bash
curl -X POST http://localhost:8000/api/orders/1/cancel \
-H "Authorization: Bearer YOUR_API_TOKEN"
```

---

## Running the Server

```bash
php artisan serve
```

Default URL: `http://127.0.0.1:8000`

---

## Queue Workers

Make sure to run the queue worker for jobs:

```bash
php artisan queue:work
```

For production, consider using **supervisor** to keep queue workers running.

---

## Testing

```bash
php artisan test
```

---

## Notes

* All monetary values (price, amount, balances) use `string` format with `bc*` functions to avoid floating-point errors.
* The API uses **Sanctum** for token-based authentication.
* Matching engine is asynchronous using Laravel jobs.

---

## Contributing

1. Fork the repository
2. Create a new branch
3. Make changes
4. Submit a pull request

---

## License

MIT License

