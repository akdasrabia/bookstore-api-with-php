## Bookstore Api

### Postman API Dökümanı

API dökümanına [buradan ulaşabilirsiniz](https://documenter.getpostman.com/view/16036304/2sAXjM5Xg8).

### Kurulum

1. **Depoyu klonlayın**:
    ```bash
    git clone https://github.com/akdasrabia/bookstore-api-with-php.git
    cd bookstore-api-with-php
    ```

2. **Veritabanı yapılandırmasını yapın**:

    `config/database.php` dosyasını açın ve veritabanı bağlantı ayarlarını güncelleyin.

    ```php
    return [
        'host' => 'localhost',
        'dbname' => 'bookstore',
        'username' => 'root',
        'password' => '',
    ];
    ```

3. **Veritabanı tablosunu oluşturun**:

    Aşağıdaki SQL sorgusunu çalıştırarak `books` tablosunu oluşturun:

    ```sql
    CREATE TABLE books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        isbn VARCHAR(50) NOT NULL,
        price DECIMAL(10, 2) NOT NULL
    );
    ```


### Kullanım

#### GET /books

Tüm kitapları listeler.

- **Yanıt**: JSON formatında kitap listesi.

#### GET /books/{id}

Belirli bir kitabı getirir.

- **Parametreler**: `id` - Kitabın id si.
- **Yanıt**: JSON formatında kitap bilgisi veya `404 Not Found` hatası.

#### POST /books

Yeni bir kitap ekler.

- **Girdi**: JSON formatında kitap bilgileri (`title`, `author`, `isbn`, `price`).
- **Yanıt**: Başarı mesajı veya `400 Bad Request` hatası.

#### PUT /books/{id}

Belirli bir kitabı günceller.

- **Parametreler**: `id` - Güncellenecek kitabın unique id si.
- **Girdi**: JSON formatında güncellenmiş kitap bilgileri (`title`, `author`, `isbn`, `price`).
- **Yanıt**: Başarı mesajı veya `404 Not Found` hatası.

#### DELETE /books/{id}

Belirli bir kitabı siler.

- **Parametreler**: `id` - Silinecek kitabın unique id si.
- **Yanıt**: Başarı mesajı veya `404 Not Found` hatası.

### Güvenlik

Bu API, güvenliği sağlamak için bir API anahtarı kullanır. Her istek, `X-API-Key` başlığı altında geçerli bir anahtar içermelidir.

- **Geçerli API Anahtarı**: `bookstore12*`

Eğer geçerli bir anahtar sağlanmazsa, API `401 Unauthorized` hatası döner.

### Hata Yönetimi

Herhangi bir hata durumunda, API uygun bir HTTP durum kodu ile birlikte JSON formatında hata mesajı döner.

- **400 Bad Request**: Geçersiz veri veya veritabanı hatası.
- **401 Unauthorized**: Geçersiz API anahtarı.
- **404 Not Found**: Belirtilen kaynak bulunamadı.
- **405 Method Not Allowed**: Desteklenmeyen HTTP yöntemi.
