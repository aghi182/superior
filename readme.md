# Superior Admin Dashboard (Flask + SQLite)

## Setup
1. Pastikan Python 3.9+
2. Install deps:
   pip install -r requirements.txt

3. Buat database dan default content:
   python init_db.py

4. Jalankan server:
   python app.py

5. Buka di browser:
   http://127.0.0.1:5000/

Uploaded images disimpan di folder `superior/pictures/`.
API endpoint utama: /api/get_all (mengembalikan JSON data)

Catatan:
- Ganti SECRET_KEY di app.py untuk production.
- Tambahkan autentikasi/role management pada admin area jika perlu.
