# Aplikasi Manajemen Transaksi

Aplikasi ini adalah contoh implementasi dari sistem manajemen transaksi menggunakan Laravel. Aplikasi ini mencakup fitur otentikasi menggunakan Laravel Passport, pemrosesan transaksi menggunakan queue, caching dengan Redis, dan throttle limit untuk endpoint API.

## Fitur

1. **Otentikasi**: Menggunakan Laravel Passport untuk otentikasi pengguna.
2. **Pemrosesan Transaksi**: Transaksi diproses menggunakan queue.
3. **Caching**: Menggunakan Redis untuk caching.
4. **Throttle Limit**: Batasan throttle untuk endpoint API guna mencegah penyalahgunaan.
5. **Pengujian**: Unit dan feature test untuk memastikan aplikasi berjalan dengan benar.

## Prasyarat

-   PHP >= 8.0
-   Composer
-   Laravel >= 11.x
-   Redis
-   MySQL, PostgreSQL, atau Sqlite

## Instalasi

1. **Clone Repository**

    ```bash
    git clone https://github.com/ak4bento/konnco_test.git
    cd repo
    ```

2. **Instal Dependensi**

    ```bash
    composer install
    ```

3. **Salin File .env**

    ```bash
    cp .env.example .env
    ```

4. **Konfigurasi Database**

    Edit file `.env` dan sesuaikan pengaturan database:

    ```env
    DB_CONNECTION=sqlite
    # DB_HOST=127.0.0.1
    # DB_PORT=3306
    # DB_DATABASE=nama_database
    # DB_USERNAME=username
    # DB_PASSWORD=password
    ```

5. **Konfigurasi Redis**

    Pastikan Redis sudah berjalan dan sesuaikan pengaturan Redis di `.env`:

    ```env
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    ```

6. **Generate Key Aplikasi**

    ```bash
    php artisan key:generate
    ```

7. **Migrasi Database**

    Jalankan migrasi untuk membuat tabel-tabel yang diperlukan:

    ```bash
    php artisan migrate:fresh && php artisan db:seed
    ```

8. **Config Laravel Passport**

    ```bash
    php artisan passport:client --personal
    ```

9. **Jalankan Server**

    ```bash
    php artisan serve
    ```

## Penggunaan

### Otentikasi

-   **Login Pengguna**

    Endpoint: `POST /api/login`

    ```json
    {
        "email": "admin@akil.co.id",
        "password": "Ve5JbvSCBXBkdni"
    }
    ```

### Transaksi

-   **Buat Transaksi Baru**

    Endpoint: `POST /api/transactions`

    Headers: `Authorization: Bearer {token}`

    ```json
    {
        "amount": 100.0
    }
    ```

-   **Riwayat Transaksi Pengguna (Dengan Pagination)**

    Endpoint: `GET /api/user/transactions?search=failed&sort_field=transactions.id&sort_order=desc&per_page=10`

    Headers: `Authorization: Bearer {token}`

-   **Ringkasan Transaksi**

    Endpoint: `GET /api/user/transactions/summary`

    Headers: `Authorization: Bearer {token}`

-   **Ringkasan Semua Transaksi**

    Endpoint: `GET /api/user/transactions/all-summary`

    Headers: `Authorization: Bearer {token}`

### Pemrosesan Transaksi dengan Queue

Transaksi diproses di latar belakang menggunakan queue. Pastikan Anda menjalankan worker queue:

```bash
php artisan queue:work
```

### Caching

Caching menggunakan Redis untuk menyimpan data transaksi sementara.

### Throttle Limit

Throttle limit diterapkan untuk endpoint API untuk mencegah penyalahgunaan. Batasan default adalah 120 permintaan per menit.

## Pengujian

Jalankan pengujian unit dan feature:

```bash
php artisan test
```

### Pengujian Otentikasi

```php
// Tests\Feature\AuthTest.php
public function user_can_login()
{
    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->postJson(route('api.auth.login'), [
        'email' => 'admin@akil.co.id',
        'password' => 'Ve5JbvSCBXBkdni',
    ]);

    $response->assertOk();
}
```

### Pengujian Pemrosesan Transaksi

```php
// Tests\Feature\PaymentControllerTest.php
public function dispatches_a_job_to_process_transaction()
{
    Queue::fake();

    $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);
    $user = $this->getUser($this::EMAIL, $this::PASSWORD);

    $responseNewTransaction = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$token,
    ])->postJson('/api/transactions', [
        'amount' => 100.00,
    ]);

    $responseNewTransaction->assertStatus(201);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$token,
    ])->patchJson('/api/transactions/'.$responseNewTransaction['data']['id'], [
        'amount' => 100.00,
    ]);

    $response->assertStatus(200)
                ->assertJsonStructure([
                'status',
                'message',
                'description'
            ]);

    $transaction = Transaction::find($responseNewTransaction->json('data.id'));

    Queue::assertPushed(ProcessTransactionJob::class);
}
```

### Pengujian Caching dan Throttle Limit

```php
// Tests\Feature\TransactionSummaryTest.php
public function caches_all_transaction_summary()
{
    $valueTotalTransactions = 5;
    $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);

    $cacheKey = "all_transactions_page";

    Cache::shouldReceive('remember')
        ....
        ....
        ....

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$token,
    ])->getJson('/api/user/transactions/all-summary');

    $response->assertStatus(200)
                ....
                ....
                ....

    $this->assertEquals($response->json('total_transactions'), $valueTotalTransactions);
}

public function throttles_requests()
{
    $user = $this->getUser($this::EMAIL, $this::PASSWORD);
    $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);

    for ($i = 0; $i < 130; $i++) {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/user/transactions');

        if ($i < 119) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429); // Too Many Requests
        }
    }
}
```

## Penulis

-   ak4bento alias Muhammad Akil
-   Email: muhammad.@akil.co.id
-   GitHub: [ak4bento](https://github.com/ak4bento)

## Lisensi

Aplikasi ini di buat untuk testing keperluan personal.
