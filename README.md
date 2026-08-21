<div align="center">

#✈️

### AI Agent untuk Penanganan Krisis & Perubahan Jadwal Penerbangan (Post-Booking)

*Bukan tempat membeli tiket — melainkan agen cerdas yang menyelamatkan tiket yang sudah Anda miliki.*

[![Hackathon](https://img.shields.io/badge/Alibaba%20Cloud%20x%20Atlas-Agentic%20AI%20Hackathon-orange)]()
[![Platform](https://img.shields.io/badge/Dibangun%20dengan-Qoder-blue)]()
[![Cloud](https://img.shields.io/badge/Didukung%20oleh-Alibaba%20Cloud-FF6A00)]()
[![API](https://img.shields.io/badge/Data-Atlas%20Travel%20API-1f6feb)]()
[![License](https://img.shields.io/badge/Lisensi-MIT-green)]()

</div>

---

## 📑 Daftar Isi

- [Ringkasan](#-ringkasan)
- [Latar Belakang Masalah](#-latar-belakang-masalah)
- [Yang Membuat Rebound Berbeda](#-yang-membuat-rebound-berbeda)
- [Fitur Utama](#-fitur-utama)
- [Cara Kerja](#-cara-kerja)
- [Alur Pengguna](#-alur-pengguna)
- [Arsitektur](#-arsitektur)
- [Tools Agent](#-tools-agent)
- [Tech Stack](#-tech-stack)
- [Keselarasan dengan SDGs PBB](#-keselarasan-dengan-sdgs-pbb)
- [Pemetaan Kriteria Penilaian](#-pemetaan-kriteria-penilaian)
- [Roadmap](#-roadmap)
- [Tim](#-tim)
- [Lisensi](#-lisensi)

---

## 🧭 Ringkasan

**Rebound** adalah **aplikasi AI agentic** yang dirancang untuk menyelesaikan dua masalah paling menyakitkan — sekaligus paling terabaikan — dalam perjalanan udara: **gangguan penerbangan (delay/pembatalan)** dan **perubahan jadwal mandiri dengan validasi kebijakan**.

Berbeda dengan Online Travel Agent (OTA) seperti Traveloka atau tiket.com, Rebound **bukan** marketplace untuk membeli tiket baru. Rebound bekerja sepenuhnya di fase **pasca-pemesanan (post-booking)**: pengguna sudah memegang tiket, dan AI agent Rebound secara otonom menyelesaikan apa yang terjadi *selanjutnya* ketika rencana berubah.

Hasilnya adalah pengalaman yang **minimalis dan berbasis chat**, di mana AI-lah yang bekerja — membaca aturan tiket (fare rules), menghitung hak kompensasi, mencari alternatif, dan menerbitkan ulang tiket — sehingga pengguna tidak perlu repot.

---

## 🎯 Latar Belakang Masalah

Gangguan perjalanan pasca-pemesanan sangat kurang terlayani oleh platform yang ada saat ini:

- ⏳ Penumpang menunggu berjam-jam di telepon dengan call center saat terjadi delay.
- 📜 **Aturan tiket (fare rules)** maskapai rumit, tidak transparan, dan jarang dijelaskan dalam bahasa yang mudah dipahami.
- 😰 Saat krisis, penumpang dibiarkan mencari tahu sendiri soal kompensasi dan penjadwalan ulang.
- 🔁 Mengubah jadwal penerbangan sering kali memerlukan navigasi kebijakan yang membingungkan dan biaya tersembunyi.

OTA unggul dalam *menjual* tiket, tetapi menyediakan sedikit dukungan cerdas *setelah* pembelian. **Rebound mengisi celah ini.**

---

## 🆚 Yang Membuat Rebound Berbeda

Rebound sengaja menghilangkan segala hal yang tidak melayani dua proses bisnis utamanya:

| OTA Tradisional (Traveloka / tiket.com) | Rebound (secara desain) |
| :--- | :--- |
| Cari & beli tiket baru | ❌ — pengguna sudah punya tiket |
| Katalog hotel, kereta, atraksi | ❌ — fokus pada satu masalah |
| Banyak metode pembayaran & checkout | ❌ — satu tombol konfirmasi |
| Dashboard, tab, navigasi rumit | ❌ — satu layar percakapan |
| Pengguna mencari solusi sendiri | ✅ — **AI bertindak proaktif** |

> **Positioning:** *Agen Penanganan Krisis & Perubahan Pasca-Pemesanan — bukan sekadar aplikasi pemesanan tiket.*

---

## ✨ Fitur Utama

- 🤖 **Penanganan Krisis Proaktif** — agent bereaksi terhadap sinyal gangguan *sebelum* pengguna meminta bantuan.
- 📜 **Penalaran Berbasis Kebijakan (Policy-Aware Reasoning)** — agent **selalu membaca fare rules terlebih dahulu** sebelum menawarkan perubahan apa pun, lalu menjelaskannya dalam bahasa yang mudah dipahami.
- 💬 **Antarmuka Berbasis Chat** — satu percakapan, tanpa menu. Seluruh proses terjadi secara inline.
- 🎨 **Rendering UI Dinamis** — agent merender komponen langsung di dalam chat (kartu penerbangan, QR voucher, boarding pass), bukan sekadar teks.
- ⚡ **Interaksi Minimal** — penyelesaian krisis dalam ~2 ketukan; perubahan jadwal dalam ~3 ketukan.
- 🔍 **Transparan & Dapat Diaudit** — setiap keputusan AI didukung oleh kebijakan yang dikutip dan riwayat pemanggilan tool yang tercatat.

---

## ⚙️ Cara Kerja

Rebound mengikuti pola **agentic loop (ReAct)**: LLM bernalar, memilih tool, memanggil Atlas API, mengamati hasilnya, lalu bernalar lagi — berulang hingga masalah pengguna benar-benar terselesaikan.

```
Pengguna / Webhook → Agent (penalaran LLM) → pilih Tool → panggil Atlas API
                   → amati hasil → nalar lagi → render UI / tanya pengguna → ulangi
```

Perilaku khas agent ini adalah **Policy-Aware Guardrail**: agent tidak diizinkan menawarkan penerbangan alternatif apa pun sebelum memvalidasi aturan tiket. Inilah yang mengubah Rebound dari sekadar "bot pencari" menjadi **agen pengambil keputusan perjalanan** yang sesungguhnya.

---

## 🔄 Alur Pengguna

### Alur 1 — Penanganan Krisis (Proaktif)

AI bergerak lebih dulu, bahkan sebelum pengguna membuka aplikasi.

```
[Sinyal gangguan diterima]        ← disimulasikan lewat tombol "Trigger" saat demo
        │
        ▼
[Notifikasi push]  "Penerbangan Anda terdampak cuaca. Ketuk untuk melihat opsi."
        │
        ▼
[Buka aplikasi → langsung masuk chat]  Agent menyapa & menjelaskan situasi
        │
        ▼
[Agent mengevaluasi tingkat delay]
        │
   ┌────┴──────────────────────────┐
   ▼                               ▼
DELAY RINGAN (≤ 2 jam)         DELAY BERAT (> 4 jam)
"Anda berhak atas voucher"      "Kami sarankan pindah jadwal"
   │                               │
   ▼                               ▼
[Kartu voucher + tombol "Ambil"] [2–3 kartu penerbangan pengganti]
   │                             [Label: Gratis / Ditanggung Maskapai]
   ▼                               │
[Render QR Code]                   ▼
 (tukar di bandara)           [Pengguna memilih satu kartu]
   │                               │
   ▼                               ▼
 SELESAI                     [Agent menerbitkan ulang tiket]
                                   │
                                   ▼
                          [Render boarding pass baru]
                                 SELESAI
```

### Alur 2 — Perubahan Jadwal Mandiri (Policy-Aware)

AI membaca aturan agar pengguna tidak perlu repot.

```
[Buka aplikasi → langsung masuk chat]
        │
        ▼
[Pengguna mengetik]  "Ubah penerbangan saya dari Singapura menjadi besok pagi."
        │
        ▼
[Indikator loading transparan]
   "Membaca kebijakan tiket Anda..."
   "Mencari jadwal yang tersedia..."
        │
        ▼
[Agent membaca fare rules TERLEBIH DAHULU]   ← nilai jual utama
        │
        ▼
[Kartu penerbangan untuk besok pagi]
   + Kotak "Alasan Kebijakan":
     "Tiket kelas Y — perubahan diizinkan, biaya administrasi $50."
        │
        ▼
[Pengguna menekan "Lanjutkan"]
        │
        ▼
[Rincian selisih harga + biaya admin]
        │
        ▼
[Pengguna menekan "Konfirmasi"]   ← saldo sandbox berkurang. Tanpa pilih metode bayar.
        │
        ▼
[Render tiket baru di dalam chat]
     SELESAI
```

---

## 🏗 Arsitektur

Rebound tersusun dalam empat lapisan yang jelas, yang dipetakan langsung ke teknologi wajib hackathon.

```
┌─────────────────────────────────────────────────────────────┐
│  1. PRESENTATION LAYER (Frontend)                            │
│     Chat UI + Dynamic UI Renderer (kartu penerbangan, QR,    │
│     boarding pass) + Penerima Notifikasi Push                │
└───────────────────────────┬─────────────────────────────────┘
                            │  respons streaming + skema UI
┌───────────────────────────▼─────────────────────────────────┐
│  2. AGENT LAYER  ── dibangun di QODER ──                     │
│     • LLM: Qwen (Alibaba Cloud Model Studio)                 │
│     • Orchestrator (loop penalaran ReAct)                    │
│     • Tool Registry (lihat Tools Agent di bawah)             │
│     • Policy-Aware Guardrail (wajib validasi fare rules)     │
└───────────────────────────┬─────────────────────────────────┘
                            │  function calls
┌───────────────────────────▼─────────────────────────────────┐
│  3. INTEGRATION LAYER (Alibaba Cloud Function Compute)       │
│     Pembungkus tool → Atlas API  +  Webhook Listener         │
│     (menerima sinyal gangguan yang disimulasikan)            │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│  4. DATA & LAYANAN EKSTERNAL                                 │
│     • Atlas Travel API (penerbangan, fare rules, PNR,        │
│       re-issue)                                              │
│     • Database (PNR pengguna, log audit) — Alibaba RDS/Redis │
└─────────────────────────────────────────────────────────────┘
```

**Pemetaan teknologi:**

| Lapisan | Teknologi | Peran |
| :--- | :--- | :--- |
| Agent Layer | **Qoder** | Membangun & menjalankan inti penalaran agentic |
| LLM & Compute | **Alibaba Cloud** | Qwen (Model Studio) + Function Compute + Database |
| Aksi & Data | **Atlas Travel API** | Semua operasi perjalanan nyata |

> ℹ️ Untuk keperluan hackathon, **webhook gangguan disimulasikan** (satu tombol "Trigger Storm" saat demo). Seluruh logika di baliknya tetap berfungsi penuh dan merepresentasikan kondisi produksi.

---

## 🧰 Tools Agent

Kemampuan agent diekspos sebagai registry tool yang terdefinisi dengan baik:

| Tool | Deskripsi | Digunakan di |
| :--- | :--- | :--- |
| `get_flight_status(pnr)` | Mengambil status penerbangan & prediksi delay | Alur 1 |
| `read_fare_rules(ticket_code)` | Membaca aturan tiket → boleh diubah? berapa biaya? | Alur 1 & 2 |
| `search_alternatives(from, to, date, cabin_class)` | Mencari penerbangan pengganti | Alur 1 & 2 |
| `check_compensation(delay_minutes)` | Menghitung hak kompensasi (voucher / snack) | Alur 1 |
| `hold_seat(flight_id)` | Mengunci kursi sementara | Alur 1 & 2 |
| `reissue_ticket(pnr, new_flight)` | Menerbitkan ulang & memperbarui tiket | Alur 1 & 2 |

---

## 🧪 Tech Stack

| Kategori | Teknologi |
| :--- | :--- |
| **Platform Agent** | Qoder |
| **LLM** | Qwen (Alibaba Cloud Model Studio) |
| **Compute** | Alibaba Cloud Function Compute |
| **Database** | Alibaba Cloud RDS / Redis |
| **Travel API** | Atlas Travel API |
| **Frontend** | Antarmuka berbasis chat dengan rendering komponen dinamis |

---

## 🌍 Keselarasan dengan SDGs PBB

| SDG | Relevansi | Prioritas |
| :--- | :--- | :---: |
| **SDG 9 — Industri, Inovasi & Infrastruktur** | Membangun infrastruktur AI agentic modern yang menginovasi industri perjalanan. | ⭐ Utama |
| **SDG 12 — Konsumsi & Produksi yang Bertanggung Jawab** | Penjadwalan ulang yang efisien mengurangi kursi kosong dan penerbangan mubazir, sehingga menurunkan jejak karbon. | Pendukung |
| **SDG 8 — Pekerjaan Layak & Pertumbuhan Ekonomi** | Mengotomasi tugas dukungan berulang, membebaskan agen manusia untuk kasus kompleks dan meningkatkan efisiensi operasional. | Pendukung |

---

## ⚖️ Pemetaan Kriteria Penilaian

| Kriteria | Bobot | Cara Rebound Memenuhinya |
| :--- | :---: | :--- |
| **Innovation** | 30% | Policy-Aware Reasoning, AI proaktif, dan rendering UI dinamis di dalam chat |
| **Feasibility** | 30% | Scope pasca-pemesanan yang sempit, enam tool terdefinisi, arsitektur konkret, siap sandbox |
| **Use of Qoder** | 20% | Inti penalaran agent dibangun & dijalankan di Qoder |
| **Impact & Presentation** | 20% | Storytelling krisis dunia nyata + keselarasan SDG yang jelas |

---

## 🗺 Roadmap

- [ ] Mendefinisikan skema tool (JSON) dan mendaftarkannya di Qoder
- [ ] Mengimplementasikan Integration Layer (pembungkus Atlas API di Function Compute)
- [ ] Membangun frontend berbasis chat dengan rendering UI dinamis
- [ ] Menghubungkan webhook gangguan yang disimulasikan
- [ ] Demo end-to-end untuk kedua alur
- [ ] Pitch deck & presentasi




</div>
