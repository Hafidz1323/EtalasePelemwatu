# Panduan Kontribusi — EtalasePelemwatu

Terima kasih atas minat Anda untuk berkontribusi pada pengembangan **EtalasePelemwatu**. Untuk menjaga kualitas dan konsistensi kode, mohon ikuti panduan berikut.

---

## 1. Alur Percabangan (Branching Workflow)

1. Branch `main` adalah branch produksi yang selalu stabil dan terverifikasi.
2. Setiap fitur, perbaikan bug, atau tugas baru dibuat di branch terpisah dengan format penamaan:
   - Fitur baru: `feature/<nama-fitur>`
   - Perbaikan bug: `fix/<nama-bug>`
   - Refactoring: `refactor/<topik>`
   - Dokumentasi: `docs/<topik>`
3. Jangan pernah melakukan commit langsung ke `main`. Selalu gunakan Pull Request (PR).

---

## 2. Standar Commit (Conventional Commits)

Format pesan commit mengikuti standar [Conventional Commits](https://www.conventionalcommits.org/):

```
<tipe>(<lingkup opsional>): <deskripsi singkat>
```

Contoh:
- `feat(auth): tambahkan rate limiter pada login warga`
- `fix(security): perbaiki sanitasi xss pada render galeri`
- `refactor(routes): bersihkan rute duplikat dan gunakan route view`
- `docs(readme): tambahkan panduan deploy dan konfigurasi nginx`
- `test(e2e): tambahkan DataSyncTest untuk sinkronisasi admin dan warga`

---

## 3. Checklist Sebelum Mengajukan Pull Request (PR)

Sebelum membuat Pull Request atau meminta review, pastikan:
- [ ] Dependensi PHP dan Node.js sinkron (`composer install`, `npm ci`).
- [ ] Asset Vite ter-build tanpa error (`npm run build`).
- [ ] Format kode bersih sesuai standar Laravel Pint (`vendor/bin/pint`).
- [ ] Seluruh automated test lulus 100% (`php artisan test`).
- [ ] Caching rute berhasil tanpa error (`php artisan route:cache && php artisan route:clear`).
- [ ] Tidak ada data sensitif (kunci rahasia, password asli, credential) yang ter-commit.
- [ ] Dokumentasi diperbarui jika terdapat endpoint API atau fitur baru.
