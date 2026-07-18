# 🌍 Global Supply Chain Risk Intelligence Platform

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange?style=flat&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple?style=flat&logo=bootstrap)
![Leaflet](https://img.shields.io/badge/Leaflet-1.9-green?style=flat&logo=leaflet)

Sistem monitoring risiko rantai pasok global berbasis multi-API dan analitik data. Memantau secara otomatis estimasi pengiriman barang, perubahan kurs, kondisi cuaca, dan risiko logistik di seluruh dunia.

---

## 📋 **Daftar Isi**

- [Fitur Utama](#-fitur-utama)
- [Teknologi](#-teknologi)
- [API yang Digunakan](#-api-yang-digunakan)
- [Instalasi](#-instalasi)
- [Struktur Database](#-struktur-database)
- [REST API Endpoints](#-rest-api-endpoints)
- [Kontributor](#-kontributor)

---

## 🚀 **Fitur Utama**

### 1. **Global Country Dashboard**
- Pilih negara dari 250+ negara di dunia
- Tampilkan GDP, inflasi, populasi, mata uang, dan cuaca saat ini
- Data ekonomi dari World Bank API
- Data cuaca real-time dari Open-Meteo API

### 2. **Risk Scoring Engine**
- Perhitungan risk score berbasis **Weighted Risk Model**
- Komponen risiko:
  - Weather Risk (30%)
  - Inflation Risk (20%)
  - Political News Risk (40%)
  - Currency Risk (10%)
- Output: Risk Score & Risk Level (Low/Medium/High/Critical)
- Risk Breakdown Chart dengan Chart.js

### 3. **Global Weather Monitoring**
- Peta dunia interaktif dengan Leaflet.js
- Menampilkan cuaca (suhu, angin, kondisi) per negara
- Marker cuaca dengan ikon dinamis (cerah, hujan, badai, dll)
- Tabel cuaca global dengan filter dan pencarian

### 4. **Currency Impact Dashboard**
- Nilai tukar real-time dari ExchangeRate API
- Grafik tren 30 hari dengan Chart.js
- Pilih mata uang berdasarkan negara
- Analisis dampak kurs terhadap supply chain

### 5. **News Intelligence**
- Berita terkini dari GNews API
- Filter berita berdasarkan negara dan kategori
- Tabel berita dengan pagination

### 6. **Sentiment Analysis (AI/Data Science)**
- Lexicon-based sentiment analysis dengan PHP
- Dictionary positif & negatif dari database
- Output: Positive/Neutral/Negative (persentase)
- Analisis otomatis untuk setiap berita

### 7. **Port Location Dashboard**
- 11.748+ pelabuhan dari UN/LOCODE dataset
- Peta interaktif dengan marker pelabuhan
- Fitur cari pelabuhan dan filter negara
- Informasi detail pelabuhan (nama, kota, tipe, ukuran)

### 8. **Route Simulation**
- Simulasikan rute antar negara atau pelabuhan
- Tampilan garis rute di peta
- Perkiraan jarak (km, miles, nautical miles)
- Perkiraan waktu tempuh (pesawat & kapal)
- Informasi pelabuhan terdekat

### 9. **Country Comparison Engine**
- Bandingkan 2 negara berdasarkan GDP, Inflasi, Risk, Mata Uang
- Visualisasi data dengan Chart.js
- Perbandingan chart yang informatif

### 10. **Favorite Monitoring List**
- User bisa menyimpan negara favorit
- Toggle add/remove dengan satu tombol
- Tampilan daftar favorit dengan informasi singkat

### 11. **Admin Dashboard**
- **Manage Users**: CRUD user, role management (admin/user)
- **Manage Ports**: CRUD pelabuhan
- **Manage Articles**: CRUD artikel dengan sentiment analysis otomatis
- Statistik dashboard (total users, ports, articles)

### 12. **Data Visualization**
- Grafik Risk Breakdown
- Currency Trend Chart (30 hari)
- GDP & Inflation Trend Chart
- Comparison Chart (perbandingan 2 negara)

---

## 🛠️ **Teknologi**

### **Backend**
- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL 5.7+
- **Authentication**: Manual (tanpa Breeze/Sanctum)

### **Frontend**
- **CSS Framework**: Bootstrap 5.3
- **JavaScript**: ES6 + jQuery 3.6
- **Mapping**: Leaflet.js 1.9
- **Charts**: Chart.js 4.4
- **Icons**: Font Awesome 6

### **API & Services**
- Open-Meteo API (cuaca)
- World Bank API (ekonomi)
- ExchangeRate API (kurs)
- GNews API (berita)
- Marine Traffic API (pelabuhan)
- OpenStreetMap (peta)

---

## 🔌 **API yang Digunakan**

| API | Fungsi | Status |
|-----|--------|--------|
| **Open-Meteo** | Cuaca real-time | ✅ Gratis, no API key |
| **World Bank** | GDP, inflasi, populasi | ✅ Gratis, no API key |
| **ExchangeRate** | Kurs mata uang | ⚠️ Free tier (100 req/hari) |
| **GNews** | Berita logistik & ekonomi | ⚠️ Free tier (100 req/hari) |
| **Marine Traffic** | Data pelabuhan | ⚠️ Free tier (terbatas) |
| **OpenStreetMap** | Peta dunia | ✅ Gratis, no API key |

---

## 📥 **Instalasi**

### **Prasyarat**
- PHP 8.2+
- Composer
- MySQL 5.7+
- XAMPP / Laragon / LAMP

### **Langkah Instalasi**

```bash
# 1. Clone repository
git clone https://github.com/username/supply-chain-monitoring.git
cd supply-chain-monitoring

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Setup database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supply_chain_db
DB_USERNAME=root
DB_PASSWORD=

# 6. Jalankan migration & seeder
php artisan migrate --seed

# 7. Import ports data (UN/LOCODE)
php artisan db:seed --class=UNLocodeSeeder

# 8. Sync news to articles
php artisan news:sync-to-articles

# 9. Jalankan server
php artisan serve
```

---

## 🗄️ **Struktur Database** 


| No | Tabel | Fungsi | Keterangan |
|----|-------|--------|------------|
| 1 | users | Data user sistem | Role: admin/user |
| 2 | countries | Data 250+ negara | GDP, inflasi, populasi, mata uang |
| 3 | ports | Data 11.748+ pelabuhan | Dari UN/LOCODE dataset |
| 4 | risk_scores |	Hasil perhitungan risk score | Weather, Inflation, Political, Currency, Logistics |
| 5	| risk_weights | Bobot indikator risiko | Weather 30%, Inflation 20%, Political 40%, Currency 10% |
| 6	| news_cache | Berita dari GNews API | Logistics, Trade, Shipping, Economy |
| 7	| articles | Artikel admin dengan sentiment | Positive/Negative/Neutral + score |
| 8	| watchlist | Daftar negara favorit user | User_id + Country_id |
| 9	| currencies | Data kurs mata uang | USD, EUR, IDR, dll |
| 10 | positive_words |	Kamus kata positif | growth, increase, profit, stable, improve |
| 11 | negative_words |	Kamus kata negatif | war, crisis, inflation, delay, disaster |
| 12 | weather_data | Cache data cuaca | Dari Open-Meteo API |
| 13 | historical_rates | Histori kurs 30 hari | Untuk currency trend chart |
| 14 | trade_data |	Data perdagangan | Ekspor, impor, trade balance |
| 15 | shipping_routes | Data rute pengiriman |	Origin-Destination port |
| 16 | country_comparisons | Cache perbandingan negara | GDP, Inflation, Risk, Weather, Currency |
| 17 | economic_indicators | Data indikator ekonomi | GDP, Inflation, Unemployment |
| 18 | user_preferences	| Preferensi user | Tema, layout, default currency |
| 19 | system_logs | Log sistem	| Debugging & monitoring |
| 20 | api_call_logs | Log pemanggilan API | Tracking API usage |
| 21 | notifications | Notifikasi user | Alert, warning, info |
| 22 | cache | Cache Laravel | Internal |
| 23 | cache_locks | Lock cache	| Internal |
| 24 | failed_jobs | Job gagal | Internal |
| 25 | jobs | Antrian job |	Internal |
| 26 | job_batches | Batch job | Internal |
| 27 | migrations |	Log migration | Internal |
| 28 | password_reset_tokens | Token reset password | Internal |
| 29 | sessions | Session user | Internal |
| 30 | negative_words |	(Backup) | - |

---

## 📡 **REST API Endpoints**
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | /api/countries | Get all countries |
| GET |	/api/countries/{code} |	Get country details |
| GET |	/api/countries/compare/{code1}/{code2} | Compare two countries |
| GET |	/api/risk |	Get all risk scores |
| GET |	/api/risk/{country} | Get risk score by country |
| GET |	/api/risk/calculate/{country} | Calculate risk score |
| POST | /api/risk/recalculate-all | Recalculate all risk scores |
| GET |	/api/ports | Get all ports |
| GET |	/api/ports/search |	Search ports |
| GET |	/api/ports/country/{code} |	Get ports by country |
| GET |	/api/news |	Get news |
| GET |	/api/news/category/{category} | Get news by category |
| GET |	/api/news/country/{country}	| Get news by country |
| GET |	/api/news/sentiment/{country} |	Get sentiment analysis |
| GET |	/api/currency |	Get currency rates |
| GET |	/api/currency/convert |	Convert currency |
| GET |	/api/currency/historical/{code}	| Get historical rates |

---

## 👨‍💻 **Kontributor**
Zaskia Azzura - github.com/zaskiaazzura
