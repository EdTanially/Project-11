@echo off
setlocal
cd /d D:\HIS\mysql\bin
if not exist "D:\HIS\mysql\bin\my_cpoe.ini" (
  echo ERROR: D:\HIS\mysql\bin\my_cpoe.ini tidak ditemukan.
  echo Pastikan konfigurasi MariaDB CPOE kamu sudah dibuat.
  pause
  exit /b 1
)

echo Starting MariaDB for CareFlow on port 3307...
echo Jangan tutup window ini selama aplikasi digunakan.
mysqld.exe --defaults-file="D:\HIS\mysql\bin\my_cpoe.ini" --console
pause
