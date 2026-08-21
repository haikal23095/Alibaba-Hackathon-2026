<div align="center">

# Rebound

### AI Agent untuk Penanganan Krisis & Perubahan Jadwal Penerbangan (Post-Booking)



[![Hackathon](https://img.shields.io/badge/Alibaba%20Cloud%20x%20Atlas-Agentic%20AI%20Hackathon-orange)]()
[![Platform](https://img.shields.io/badge/Dibangun%20dengan-Qoder-blue)]()
[![Cloud](https://img.shields.io/badge/Didukung%20oleh-Alibaba%20Cloud-FF6A00)]()
[![API](https://img.shields.io/badge/Data-Atlas%20Travel%20API-1f6feb)]()
[![License](https://img.shields.io/badge/Lisensi-MIT-green)]()

</div>

---

> 💡 **Cara membaca dokumen ini**
> Setiap bagian teknis diawali dengan penjelasan sederhana bertanda **🟢 Untuk Semua Orang**,
> lalu dilanjutkan detail bertanda **🔵 Untuk Pembaca Teknis**.
> Jadi dokumen ini nyaman dibaca baik oleh orang awam maupun developer.

---

## 📑 Daftar Isi

**Konsep**
- [Ringkasan](#-ringkasan)
- [Analogi Sederhana](#-analogi-sederhana)
- [Latar Belakang Masalah](#-latar-belakang-masalah)
- [Yang Membuat Rebound Berbeda](#-yang-membuat-rebound-berbeda)
- [Fitur Utama](#-fitur-utama)

**Cara Kerja**
- [Cara Kerja Agent](#-cara-kerja-agent)
- [Sumber Data & Peran PNR](#-sumber-data--peran-pnr)
- [Model Otorisasi](#-model-otorisasi)
- [Tools Agent](#-tools-agent)
- [Alur Pengguna](#-alur-pengguna)

**Teknis**
- [Arsitektur](#-arsitektur)
- [Tech Stack](#-tech-stack)
- [Lingkungan Sandbox](#-lingkungan-sandbox)

**Dampak & Penilaian**
- [Keselarasan dengan SDGs PBB](#-keselarasan-dengan-sdgs-pbb)
- [Pemetaan Kriteria Penilaian](#-pemetaan-kriteria-penilaian)
- [Roadmap](#-roadmap)
- [Tim](#-tim)
- [Lisensi](#-lisensi)

---

# 🧩 Konsep

## 🧭 Ringkasan

**Rebound** adalah **aplikasi AI agentic** yang menyelesaikan dua masalah paling krusial dalam perjalanan udara — yang selama ini paling terabaikan:

1. **Gangguan penerbangan** (delay / pembatalan)
2. **Perubahan jadwal mandiri** yang disertai validasi kebijakan tiket

Berbeda dengan Online Travel Agent (OTA) seperti Traveloka atau tiket.com, Rebound **bukan** tempat membeli tiket baru. Rebound bekerja di fase **pasca-pemesanan (post-booking)**: pengguna sudah memegang tiket, dan AI agent Rebound-lah yang mengurus apa yang terjadi *selanjutnya* ketika rencana berubah.

---

## 🍰 Analogi Sederhana

Bayangkan Anda sudah membeli tiket, lalu terjadi masalah (pesawat delay) atau Anda ingin mengubah jadwal.

- **Cara lama:** Anda harus menelepon call center, menunggu lama, membaca aturan tiket yang membingungkan, dan mencari solusi sendiri.
- **Dengan Rebound:** Anda cukup mengobrol dengan asisten AI — seperti chat biasa. AI-nya yang membaca aturan, menghitung biaya, mencari penerbangan pengganti, dan mengurus penggantian tiket. **Anda tinggal menyetujui.**

> Rebound seperti **asisten pribadi penerbangan** yang selalu siaga: ia bergerak lebih dulu saat ada masalah, dan menyelesaikan hal rumit agar Anda tidak perlu repot.

---

## 🎯 Latar Belakang Masalah

**🟢 Untuk Semua Orang:** Saat penerbangan bermasalah, penumpang sering bingung dan stres. Mereka harus mengurus semuanya sendiri, padahal aturannya rumit.

Masalah pasca-pemesanan yang belum terselesaikan dengan baik:

- ⏳ Menunggu berjam-jam menghubungi call center saat terjadi delay.
- 📜 **Aturan tiket (fare rules)** rumit, tidak transparan, dan jarang dijelaskan dengan bahasa yang mudah.
- 😰 Saat krisis, penumpang dibiarkan mencari solusi kompensasi & penjadwalan ulang sendiri.
- 🔁 Mengubah jadwal sering melibatkan kebijakan membingungkan dan biaya tersembunyi.

> OTA sangat baik dalam **menjual** tiket, tetapi memberi sedikit dukungan cerdas **setelah** pembelian. **Rebound mengisi celah ini.**

---

## 🆚 Yang Membuat Rebound Berbeda

Rebound sengaja menghilangkan semua hal yang tidak melayani dua proses bisnis utamanya:

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

- 🤖 **Penanganan Krisis Proaktif** — AI bereaksi terhadap masalah *sebelum* pengguna meminta bantuan.
- 📜 **Penalaran Berbasis Kebijakan (Policy-Aware Reasoning)** — AI **selalu membaca aturan tiket lebih dulu** sebelum menawarkan perubahan, lalu menjelaskannya dengan bahasa sederhana.
- 💬 **Antarmuka Berbasis Chat** — satu percakapan, tanpa menu yang rumit.
- 🎨 **Tampilan Dinamis di Dalam Chat** — AI menampilkan kartu penerbangan, voucher QR, dan boarding pass langsung di chat, bukan sekadar teks.
- ⚡ **Interaksi Minimal** — krisis selesai dalam ~2 ketukan; perubahan jadwal dalam ~3 ketukan.
- 🔍 **Transparan & Dapat Diaudit** — setiap keputusan AI disertai alasan kebijakan yang jelas.

---

# ⚙️ Cara Kerja

## ⚙️ Cara Kerja Agent

**🟢 Untuk Semua Orang:**
AI Rebound bekerja seperti asisten yang cekatan. Ia **berpikir → memilih tindakan → melakukannya → memeriksa hasil → berpikir lagi**, berulang sampai masalah Anda benar-benar selesai. Ia tidak asal menjawab; ia benar-benar mengecek data dan aturan sebelum bertindak.

**🔵 Untuk Pembaca Teknis:**
Rebound menggunakan pola **agentic loop (ReAct)**. LLM bernalar, memilih *tool*, memanggil Atlas API, mengamati hasilnya, lalu bernalar lagi hingga tujuan tercapai.

```
Pengguna / Sinyal  →  AI bernalar  →  pilih Tindakan (Tool)  →  panggil Atlas API
                   →  amati hasil  →  bernalar lagi  →  tampilkan hasil / bertanya  →  ulangi
```

**Aturan kunci — Policy-Aware Guardrail:**
AI **dilarang** menawarkan penerbangan pengganti sebelum memvalidasi aturan tiket. Inilah yang membuat Rebound bukan sekadar "bot pencari", melainkan **agen pengambil keputusan** yang benar-benar memahami kebijakan.

---

## 🔑 Sumber Data & Peran PNR

**🟢 Untuk Semua Orang:**
Pertanyaan penting: *Apakah Rebound terhubung ke tempat tiket yang dibeli ( ex: traveloka, trip.com, dll) ?* **Tidak.**

Data tiket yang sebenarnya tidak disimpan di Traveloka — Traveloka hanyalah tempat membeli (etalase). Data asli tersimpan di **sistem maskapai**. Yang menghubungkan Anda ke sana adalah **kode booking / PNR** (kode 6 karakter seperti `ABC123`) yang Anda terima setelah membeli tiket.

> **Analogi:** PNR itu seperti **nomor rekening bank**. Uang Anda tidak disimpan di "nomor"-nya, melainkan di **bank**-nya. Nomor rekening hanyalah *kunci* untuk mengaksesnya. Begitu pula PNR — ia kunci untuk membuka data tiket asli Anda.

**🔵 Untuk Pembaca Teknis:**
Rebound bersifat **OTA-agnostic**. Data pemesanan yang otoritatif berada di **sistem maskapai / GDS (Global Distribution System)** — seperti Amadeus, Sabre, atau Travelport. Rebound mengaksesnya melalui **Atlas Travel API**, menggunakan **PNR** sebagai kunci identifikasi.

```
Penumpang  →  Rebound (AI Agent)  →  Atlas Travel API  →  Sistem Maskapai / GDS
                                                          (sumber data otoritatif)
```

**Contoh nyata — Garuda 30 Nov ➜ ingin dimajukan ke 26 Nov:**

```
Langkah 1  Penumpang memberi PNR:  "Kode booking ABC123, tolong majukan ke 26 Nov"
              │
Langkah 2  get_flight_status("ABC123")
           → Ambil data asli: "Garuda GA-xxx, 30 Nov, kelas Y"
              │
Langkah 3  read_fare_rules("Y")        ← WAJIB lebih dulu (Policy-Aware)
           → "Tiket kelas Y boleh diubah, biaya admin $50"
              │
Langkah 4  search_alternatives("CGK", "SIN", "26 Nov", "Y")
           → Cari kursi tersedia pada 26 Nov
              │
Langkah 5  Tampilkan kartu penerbangan 26 Nov + selisih harga + biaya admin
              │
Langkah 6  Penumpang menekan "Konfirmasi" → reissue_ticket() → tiket 26 Nov terbit
```

> **Keunggulan:** karena hanya butuh PNR, Rebound melayani tiket dari **OTA mana pun** tanpa integrasi terpisah ke tiap platform.

---

## 🔐 Model Otorisasi

**🟢 Untuk Semua Orang:**
Rebound tidak bisa mengubah tiket seseorang tanpa izin. Karena itu Rebound bekerja atas dasar **persetujuan pengguna (consent)** — sama seperti aplikasi keuangan yang boleh mengakses rekening Anda hanya setelah Anda memberi izin. Pengguna memberi kuasa, lalu Rebound bertindak **atas nama pengguna**.

**🔵 Untuk Pembaca Teknis:**
Rebound menerapkan **Trust & Authorization Layer** yang bersifat *consent-first*:

- **Consent pengguna** → pengguna mengotorisasi Rebound untuk bertindak atas namanya.
- **Jalur resmi** → untuk tiket yang dikelola OTA, integrasi dilakukan melalui kemitraan/API resmi.
- **Audit trail** → setiap tindakan agent tercatat dan dapat ditelusuri.

> Selaras dengan salah satu track hackathon: **"trust and verification systems"**.

---

## 🧰 Tools Agent

**🟢 Untuk Semua Orang:** Ini adalah daftar "kemampuan" yang dimiliki AI — hal-hal konkret yang bisa ia lakukan.

**🔵 Untuk Pembaca Teknis:** Kemampuan agent diekspos sebagai *tool registry* yang terdefinisi jelas:

| Tool | Apa yang dilakukan | Dipakai di |
| :--- | :--- | :--- |
| `get_flight_status(pnr)` | Mengambil status penerbangan & prediksi delay | Alur 1 |
| `read_fare_rules(ticket_code)` | Membaca aturan tiket → boleh diubah? berapa biaya? | Alur 1 & 2 |
| `search_alternatives(from, to, date, cabin_class)` | Mencari penerbangan pengganti | Alur 1 & 2 |
| `check_compensation(delay_minutes)` | Menghitung hak kompensasi (voucher / snack) | Alur 1 |
| `hold_seat(flight_id)` | Mengunci kursi sementara | Alur 1 & 2 |
| `reissue_ticket(pnr, new_flight)` | Menerbitkan ulang & memperbarui tiket | Alur 1 & 2 |

---

## 🔄 Alur Pengguna

Rebound memiliki dua alur utama. Keduanya dibuat sesederhana mungkin.

### Alur 1 — Penanganan Krisis (AI Bergerak Lebih Dulu)

**🟢 Ringkasan:** Pesawat delay → AI langsung memberi tahu & menawarkan solusi (voucher atau pindah jadwal) → Anda tinggal memilih.

```
┌────────────────────────────────────────────────────────────────┐
│  1. Sistem mendeteksi penerbangan Anda delay                    │
│     (disimulasikan lewat tombol "Trigger" saat demo)            │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  2. Anda menerima notifikasi:                                   │
│     "Penerbangan Anda terdampak cuaca. Ketuk untuk lihat opsi." │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  3. Aplikasi terbuka → AI menyapa & menjelaskan situasi         │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
                 ┌──── AI menilai lama delay ────┐
                 ▼                               ▼
      ┌─────────────────────┐        ┌──────────────────────────┐
      │  DELAY RINGAN ≤ 2 jam│        │   DELAY BERAT > 4 jam     │
      │  → Berhak voucher    │        │   → Sebaiknya pindah jadwal│
      └──────────┬──────────┘        └─────────────┬────────────┘
                 ▼                                  ▼
      ┌─────────────────────┐        ┌──────────────────────────┐
      │  Tampil kartu voucher│        │  Tampil 2–3 penerbangan   │
      │  → tombol "Ambil"    │        │  pengganti (Ditanggung    │
      │                      │        │  Maskapai)                │
      └──────────┬──────────┘        └─────────────┬────────────┘
                 ▼                                  ▼
      ┌─────────────────────┐        ┌──────────────────────────┐
      │  Muncul QR Code      │        │  Anda pilih 1 → AI        │
      │  (tukar di bandara)  │        │  terbitkan tiket baru →   │
      │  → SELESAI ✅         │        │  boarding pass → SELESAI ✅│
      └─────────────────────┘        └──────────────────────────┘
```

### Alur 2 — Perubahan Jadwal Mandiri (AI Membaca Aturan untuk Anda)

**🟢 Ringkasan:** Anda ketik "ubah jadwal saya" → AI baca aturan tiket → tampilkan opsi + biaya secara transparan → Anda konfirmasi → tiket baru terbit.

```
┌────────────────────────────────────────────────────────────────┐
│  1. Anda buka aplikasi & ketik permintaan dengan bahasa biasa:  │
│     "Ubah penerbangan saya dari Singapura menjadi besok pagi."  │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  2. AI menampilkan proses secara transparan:                    │
│     "Membaca kebijakan tiket Anda..."                           │
│     "Mencari jadwal yang tersedia..."                           │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  3. AI membaca aturan tiket LEBIH DULU  ← nilai jual utama      │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  4. AI menampilkan kartu penerbangan + Alasan Kebijakan:        │
│     "Tiket kelas Y — perubahan diizinkan, biaya admin $50."     │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  5. Anda tekan "Lanjutkan" → AI tampilkan rincian selisih harga │
└───────────────────────────┬────────────────────────────────────┘
                            ▼
┌────────────────────────────────────────────────────────────────┐
│  6. Anda tekan "Konfirmasi" → tiket baru terbit di dalam chat ✅ │
│     (tanpa pilih metode bayar — cukup satu tombol)              │
└────────────────────────────────────────────────────────────────┘
```

---

# 🏗 Teknis

## 🏗 Arsitektur

**🟢 Untuk Semua Orang:** Sistem Rebound terbagi menjadi 4 "lapisan" yang bekerja sama — dari tampilan yang Anda lihat, otak AI-nya, jembatan ke data, hingga sumber data penerbangan.

**🔵 Untuk Pembaca Teknis:** Empat lapisan yang dipetakan langsung ke teknologi wajib hackathon:

```
┌─────────────────────────────────────────────────────────────┐
│  1. PRESENTATION LAYER (Tampilan)                            │
│     Chat UI + penampil komponen dinamis (kartu penerbangan,  │
│     QR, boarding pass) + penerima notifikasi                 │
└───────────────────────────┬─────────────────────────────────┘
                            │  respons + tampilan
┌───────────────────────────▼─────────────────────────────────┐
│  2. AGENT LAYER (Otak AI)   ── dibangun di QODER ──          │
│     • LLM: Qwen (Alibaba Cloud Model Studio)                 │
│     • Orchestrator (loop penalaran ReAct)                    │
│     • Tool Registry (daftar kemampuan agent)                 │
│     • Policy-Aware Guardrail (wajib validasi aturan tiket)   │
└───────────────────────────┬─────────────────────────────────┘
                            │  pemanggilan tool
┌───────────────────────────▼─────────────────────────────────┐
│  3. INTEGRATION LAYER (Jembatan)                             │
│     Alibaba Cloud Function Compute                           │
│     Pembungkus tool → Atlas API + penerima sinyal gangguan   │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│  4. DATA & LAYANAN EKSTERNAL (Sumber Data)                   │
│     • Atlas Travel API → jembatan ke Sistem Maskapai / GDS   │
│     • Database (PNR pengguna, log audit) — Alibaba RDS/Redis │
└─────────────────────────────────────────────────────────────┘
```

**Pemetaan teknologi:**

| Lapisan | Teknologi | Peran |
| :--- | :--- | :--- |
| Otak AI | **Qoder** | Membangun & menjalankan inti penalaran agentic |
| LLM & Compute | **Alibaba Cloud** | Qwen (Model Studio) + Function Compute + Database |
| Aksi & Data | **Atlas Travel API** | Jembatan ke sistem maskapai/GDS untuk semua operasi nyata |

---

## 🧪 Tech Stack

| Kategori | Teknologi |
| :--- | :--- |
| **Platform Agent** | Qoder |
| **LLM** | Qwen (Alibaba Cloud Model Studio) |
| **Compute** | Alibaba Cloud Function Compute |
| **Database** | Alibaba Cloud RDS / Redis |
| **Travel API** | Atlas Travel API |
| **Frontend** | Antarmuka berbasis chat dengan komponen dinamis |

---

## 🧭 Lingkungan Sandbox

**🟢 Untuk Semua Orang:**
Saat lomba, Rebound tidak menyentuh sistem penerbangan sungguhan. Sebagai gantinya, panitia menyediakan **lingkungan sandbox** — sebuah "ruang latihan" berisi **data simulasi** yang aman untuk diuji.

Di dalamnya sudah tersedia **rekaman pemesanan (PNR) yang disiapkan sebagai kondisi awal**. Ketika pengguna meminta perubahan jadwal, agent **tidak membuat pemesanan baru** — melainkan **memperbarui rekaman yang sudah ada**. Keberhasilan perubahan pada data ini menjadi **bukti** bahwa agent bekerja dengan benar.

**🔵 Untuk Pembaca Teknis:**
- Data uji berupa PNR di-*seed* sebagai kondisi awal di Atlas API sandbox.
- Operasi `reissue_ticket()` melakukan *update* terhadap rekaman yang ada, bukan *create* baru.
- Perbandingan state sebelum → sesudah (mis. 30 Nov → 26 Nov) menjadi validasi fungsional end-to-end.

> ℹ️ Sinyal gangguan (delay) juga **disimulasikan** melalui satu pemicu ("Trigger Storm") saat demo. Seluruh logika di baliknya tetap berfungsi penuh dan merepresentasikan kondisi produksi.

---

# 🌍 Dampak & Penilaian

## 🌍 Keselarasan dengan SDGs PBB

| SDG | Relevansi | Prioritas |
| :--- | :--- | :---: |
| **SDG 9 — Industri, Inovasi & Infrastruktur** | Membangun infrastruktur AI agentic modern yang menginovasi industri perjalanan. | ⭐ Utama |
| **SDG 12 — Konsumsi & Produksi yang Bertanggung Jawab** | Penjadwalan ulang efisien mengurangi kursi kosong & penerbangan mubazir, menurunkan jejak karbon. | Pendukung |
| **SDG 8 — Pekerjaan Layak & Pertumbuhan Ekonomi** | Mengotomasi tugas dukungan berulang, membebaskan agen manusia untuk kasus kompleks. | Pendukung |

---

## ⚖️ Pemetaan Kriteria Penilaian

| Kriteria | Bobot | Cara Rebound Memenuhinya |
| :--- | :---: | :--- |
| **Innovation** | 30% | Policy-Aware Reasoning, AI proaktif, dan tampilan dinamis di dalam chat |
| **Feasibility** | 30% | Scope pasca-pemesanan yang sempit, enam tool terdefinisi, arsitektur konkret, siap sandbox |
| **Use of Qoder** | 20% | Inti penalaran agent dibangun & dijalankan di Qoder |
| **Impact & Presentation** | 20% | Storytelling krisis dunia nyata + keselarasan SDG yang jelas |

---

## 🗺 Roadmap

- [ ] Mendefinisikan skema tool (JSON) dan mendaftarkannya di Qoder
- [ ] Mengimplementasikan Integration Layer (pembungkus Atlas API di Function Compute)
- [ ] Membangun frontend berbasis chat dengan komponen dinamis
- [ ] Menghubungkan pemicu gangguan (delay) yang disimulasikan
- [ ] Menyiapkan data awal (seed PNR) di sandbox
- [ ] Demo end-to-end untuk kedua alur
- [ ] Pitch deck & presentasi

---

## 👥 Tim

> _Tambahkan anggota tim Anda di sini._

| Nama | Peran |
| :--- | :--- |
| — | — |

---

## 📄 Lisensi

Proyek ini dirilis di bawah **Lisensi MIT**. Lihat [`LICENSE`](LICENSE) untuk detailnya.

---

<div align="center">

*Dibuat untuk Alibaba Cloud × Atlas Agentic AI Hackathon.*

**Rebound — saat rencana berubah, biar agent yang mengurus sisanya.**

</div>
