"use strict";

/* =========================================================
   UAS PRAKTIKUM — DATA MODEL & LOGIC
   Storage: IndexedDB + localStorage fallback
   ========================================================= */
const STORE_KEY = "rndSQL2_uas_v5";
const IDB_NAME  = "rndSQL2DB";
const IDB_STORE = "doc";
const TIME_LIMIT = 90 * 60;
const TIMER_STORE = "rndSQL2_uas_timer";

let lockedNim = null;
let timerRemaining = TIME_LIMIT;
let timerInterval = null;

const $ = id => document.getElementById(id);

/* ========== NIM SEED ========== */
function nimSeed(nim) {
  if (!nim || !nim.match(/^\d+$/)) return null;
  let s = 0;
  for (let i = 0; i < nim.length; i++) s = (s * 31 + parseInt(nim[i])) >>> 0;
  return { seed: s, nim: nim };
}
function pickIdx(seed, count, salt) {
  if (!seed) return 0;
  // ponytail: per-pool hash so each pool picks an INDEPENDENT variant.
  // Without the salt every pool got the same index → only `count` distinct papers.
  let h = (seed.seed ^ Math.imul((salt | 0) + 1, 0x9E3779B1)) >>> 0;
  h = Math.imul(h ^ (h >>> 13), 0x5bd1e995) >>> 0;
  h = (h ^ (h >>> 15)) >>> 0;
  return h % count;
}

/* ========== IndexedDB ========== */
function idbOpen() {
  return new Promise((res, rej) => {
    if (!window.indexedDB) { rej(new Error("IndexedDB tidak tersedia")); return; }
    const req = indexedDB.open(IDB_NAME, 1);
    req.onupgradeneeded = () => { req.result.createObjectStore(IDB_STORE); };
    req.onsuccess = () => res(req.result);
    req.onerror = () => rej(req.error);
  });
}
function idbSet(key, val) {
  return idbOpen().then(db => new Promise((res, rej) => {
    const tx = db.transaction(IDB_STORE, "readwrite");
    tx.objectStore(IDB_STORE).put(val, key);
    tx.oncomplete = () => res(true);
    tx.onerror = () => rej(tx.error);
  }));
}
function idbGet(key) {
  return idbOpen().then(db => new Promise((res, rej) => {
    const tx = db.transaction(IDB_STORE, "readonly");
    const r = tx.objectStore(IDB_STORE).get(key);
    r.onsuccess = () => res(r.result);
    r.onerror = () => rej(r.error);
  }));
}
function updateStorageMeter() {
  const el = $("storageMeter"); if (!el) return;
  if (navigator.storage && navigator.storage.estimate) {
    navigator.storage.estimate().then(est => {
      const usedMB = (est.usage || 0) / 1048576;
      el.textContent = "DB: " + usedMB.toFixed(1) + " MB";
    }).catch(() => { el.textContent = "DB: ok"; });
  } else { el.textContent = "DB: ok"; }
}

/* ========== COLLECT / APPLY ========== */
function collectData() {
  const data = {};
  document.querySelectorAll("#uasDocument input, #uasDocument textarea").forEach(el => {
    if (el.id) data[el.id] = el.value;
  });
  data.__lockedNim = lockedNim || "";
  return data;
}
function applyData(d) {
  for (const k in d) {
    if (k === "__lockedNim") continue;
    const el = $(k); if (el) el.value = d[k];
  }
}

/* ========== TIMER ========== */
function formatTime(s) {
  const m = Math.floor(s / 60);
  const sec = s % 60;
  return String(m).padStart(2, "0") + ":" + String(sec).padStart(2, "0");
}
function updateTimerDisplay() {
  const el = $("timerDisplay"); if (!el) return;
  el.textContent = formatTime(timerRemaining);
  el.classList.remove("warn", "danger");
  if (timerRemaining <= 60) el.classList.add("danger");
  else if (timerRemaining <= 300) el.classList.add("warn");
}
function startTimer(remaining) {
  timerRemaining = remaining || TIME_LIMIT;
  $("timerBar").style.display = "flex";
  updateTimerDisplay();
  if (timerInterval) clearInterval(timerInterval);
  timerInterval = setInterval(() => {
    timerRemaining--;
    updateTimerDisplay();
    if (timerRemaining <= 0) {
      clearInterval(timerInterval);
      timerInterval = null;
      alert("Waktu habis! Simpan dan cetak jawaban Anda.");
    }
    saveData(true);
  }, 1000);
}
function startTimerIfNeeded() {
  if (timerInterval) return;
  let remaining = TIME_LIMIT;
  const saved = localStorage.getItem(TIMER_STORE);
  if (saved) {
    try {
      const t = JSON.parse(saved);
      if (t.nim === lockedNim) {
        const elapsed = Math.floor((Date.now() - t.startedAt) / 1000);
        remaining = Math.max(0, TIME_LIMIT - elapsed);
      }
    } catch (e) { /* ignore */ }
  } else {
    localStorage.setItem(TIMER_STORE, JSON.stringify({ nim: lockedNim, startedAt: Date.now() }));
  }
  startTimer(remaining);
}
function stopTimer() {
  if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
  localStorage.removeItem(TIMER_STORE);
  $("timerBar").style.display = "none";
}

/* ========== PROGRESS ========== */
function updateProgress() {
  const req = document.querySelectorAll("[data-required]");
  let filled = 0;
  req.forEach(el => { if (el.value && el.value.trim() !== "") filled++; });
  const total = req.length || 1;
  const pct = Math.round(filled / total * 100);
  $("progressFill").style.width = pct + "%";
  $("progressPct").textContent = pct + "%";
}

/* ========== PRINT MIRROR ========== */
function syncPrintMirrors() {
  document.querySelectorAll("#uasDocument input[id], #uasDocument textarea[id]").forEach(el => {
    let m = el.nextElementSibling;
    if (!m || !m.classList || !m.classList.contains("print-mirror")) {
      m = document.createElement("div");
      m.className = "print-mirror";
      el.parentNode.insertBefore(m, el.nextSibling);
    }
    m.textContent = (el.value || "").trim() || "—";
  });
}

/* ========== KUNCI NIM ========== */
function kunciNIM() {
  const inp = $("nim"); if (!inp) return;
  const nimVal = inp.value.trim();
  const seed = nimSeed(nimVal);
  if (!seed || nimVal.length < 5) {
    $("nimInfoText").innerHTML = "NIM tidak valid (min. 5 digit angka)";
    $("nimInfoBar").style.background = "var(--warn)";
    return;
  }
  lockedNim = nimVal;
  inp.disabled = true;
  inp.style.background = "var(--rule-soft)";
  inp.style.opacity = "0.6";
  $("lockNimBtn").textContent = "Terkunci";
  $("lockNimBtn").disabled = true;

  const variantId = "V" + ((seed.seed % 1000) + 1000);
  $("nimVariantLabel").textContent = "#" + variantId + " | NIM: " + lockedNim;
  $("nimInfoText").innerHTML = "Soal digenerate berdasarkan NIM <strong>" + lockedNim + "</strong>";

  generateSoal(seed);
  saveData(true);
}
function unlockNim() {
  lockedNim = null;
  const inp = $("nim");
  if (inp) { inp.disabled = false; inp.style.background = "transparent"; inp.style.opacity = "1"; }
  $("lockNimBtn").textContent = "Kunci NIM";
  $("lockNimBtn").disabled = false;
  $("soalContainer").innerHTML = "";
  $("bagianPernyataan").style.display = "none";
  $("timerBar").style.display = "none";
  $("nimInfoText").innerHTML = "Masukkan NIM valid lalu klik <strong>Kunci NIM</strong> untuk generate soal";
  $("nimVariantLabel").textContent = "—";
  stopTimer();
}
window.kunciNIM = kunciNIM;

/* ========== QUESTION POOLS — 14 pool x 3-5 varian ========== */
const POOLS = [
  // ===== 1-2: DASAR (M2, M5) — 10% =====
  { id:"a",label:"DDL — CREATE TABLE",bobot:"5%",soal:[
    {text:'Buat <strong>CREATE TABLE</strong> <code>kategori</code>: <code>id_kategori INT PK AUTO_INCREMENT</code>, <code>nm_kategori VARCHAR(100) NOT NULL</code>.',hint:'CREATE TABLE kategori (id_kategori INTEGER PRIMARY KEY AUTOINCREMENT, nm_kategori TEXT NOT NULL);'},
    {text:'Buat <strong>CREATE TABLE</strong> <code>pemasok</code>: <code>id_pemasok INT PK</code>, <code>nm_pemasok VARCHAR(150) NOT NULL</code>, <code>kota VARCHAR(50)</code>.',hint:'CREATE TABLE pemasok (id_pemasok INTEGER PRIMARY KEY AUTOINCREMENT, nm_pemasok TEXT NOT NULL, kota TEXT);'},
    {text:'Buat <strong>CREATE TABLE</strong> <code>pelanggan</code>: <code>id_pelanggan INT PK</code>, <code>nm_pelanggan VARCHAR(150) NOT NULL</code>, <code>email VARCHAR(100) NOT NULL</code>, <code>no_telp VARCHAR(20)</code>.',hint:'CREATE TABLE pelanggan (id_pelanggan INTEGER PRIMARY KEY AUTOINCREMENT, nm_pelanggan TEXT NOT NULL, email TEXT NOT NULL, no_telp TEXT);'}
  ]},
  { id:"b",label:"DML — INSERT &amp; SELECT",bobot:"5%",soal:[
    {text:'(a) INSERT 3 record ke <code>kategori</code>: "Elektronik", "Furniture", "ATK". (b) SELECT semua data dari kategori.',hint:"INSERT INTO kategori(nm_kategori) VALUES ('Elektronik'),('Furniture'),('ATK');\nSELECT * FROM kategori;"},
    {text:'(a) INSERT 3 record ke <code>pemasok</code>. (b) SELECT dengan WHERE: hanya pemasok dari "Bandar Lampung".',hint:"INSERT INTO pemasok(nm_pemasok,kota) VALUES ('PT Maju Jaya','Bandar Lampung'),('CV Sukses','Jakarta'),('UD Barokah','Metro');\nSELECT * FROM pemasok WHERE kota='Bandar Lampung';"},
    {text:'(a) INSERT 3 record ke <code>pelanggan</code>. (b) SELECT pelanggan yang email mengandung "mail.com".',hint:"INSERT INTO pelanggan(nm_pelanggan,email) VALUES ('Andi','andi@mail.com'),('Budi','budi@mail.com'),('Citra','citra@mail.com');\nSELECT * FROM pelanggan WHERE email LIKE '%mail.com%';"}
  ]},

  // ===== 3-4: JOIN & AGGREGATE (M6-M7) — 10% =====
  { id:"c",label:"SELECT dengan JOIN",bobot:"5%",soal:[
    {text:'INNER JOIN: tampilkan <code>npm, nama, nmprodi</code> dari mahasiswa JOIN prodi, urut A-Z.',hint:"SELECT m.npm,m.nama,p.nmprodi FROM mahasiswa m INNER JOIN prodi p ON m.idprodi=p.idprodi ORDER BY m.nama ASC;"},
    {text:'LEFT JOIN: tampilkan <code>nmprodi, COUNT(npm)</code> semua prodi (termasuk yg 0 mahasiswa).',hint:"SELECT p.nmprodi,COUNT(m.npm) FROM prodi p LEFT JOIN mahasiswa m ON p.idprodi=m.idprodi GROUP BY p.idprodi,p.nmprodi;"},
    {text:'Multi-JOIN: <code>npm,nama,nmprodi,nmjenjang</code> — mahasiswa JOIN prodi JOIN jenjang.',hint:"SELECT m.npm,m.nama,p.nmprodi,j.nmjenjang FROM mahasiswa m INNER JOIN prodi p ON m.idprodi=p.idprodi INNER JOIN jenjang j ON p.idjenjang=j.idjenjang;"}
  ]},
  { id:"d",label:"Aggregate &amp; GROUP BY",bobot:"5%",soal:[
    {text:'<code>idprodi, COUNT(*)</code> per prodi, hanya >5 mahasiswa (HAVING).',hint:"SELECT idprodi,COUNT(*) FROM mahasiswa GROUP BY idprodi HAVING COUNT(*)>5;"},
    {text:'<code>idprodi, AVG(ipk)</code> rata-rata IPK per prodi, hanya >3.0.',hint:"SELECT idprodi,AVG(ipk) FROM mahasiswa GROUP BY idprodi HAVING AVG(ipk)>3.0;"},
    {text:'<code>thn_masuk, COUNT(*), AVG(ipk)</code> per tahun, urut tahun terbaru.',hint:"SELECT thn_masuk,COUNT(*),AVG(ipk) FROM mahasiswa GROUP BY thn_masuk ORDER BY thn_masuk DESC;"}
  ]},

  // ===== 5-6: CASE & SUBQUERY (M3, M5-M7) — 10% =====
  { id:"e",label:"T-SQL — CASE",bobot:"5%",soal:[
    {text:'CASE IPK: >=3.5 "Memuaskan", >=3.0 "Baik", >=2.5 "Cukup", else "Kurang". Tampil <code>npm,nama,ipk,kategori</code>.',hint:"SELECT npm,nama,ipk,CASE WHEN ipk>=3.5 THEN 'Memuaskan' WHEN ipk>=3.0 THEN 'Baik' WHEN ipk>=2.5 THEN 'Cukup' ELSE 'Kurang' END AS kategori FROM mahasiswa;"},
    {text:'CASE tahun: 2020 "Lama", 2021 "Menengah", 2022-2023 "Baru", else "Lainnya".',hint:"SELECT npm,nama,thn_masuk,CASE WHEN thn_masuk=2020 THEN 'Lama' WHEN thn_masuk=2021 THEN 'Menengah' WHEN thn_masuk IN(2022,2023) THEN 'Baru' ELSE 'Lainnya' END AS angkatan FROM mahasiswa;"},
    {text:'CASE pendidikan dosen: S3 "Doktor", S2 "Magister", else "Lainnya". Tampil <code>nmdosen,gelar,golongan</code>.',hint:"SELECT d.nmdosen,d.gelar,CASE WHEN p.nmpendidikan='S3' THEN 'Doktor' WHEN p.nmpendidikan='S2' THEN 'Magister' ELSE 'Lainnya' END AS golongan FROM dosen d INNER JOIN pendidikan p ON d.idpendidikan=p.idpendidikan;"}
  ]},
  { id:"f",label:"Subquery",bobot:"5%",soal:[
    {text:'Subquery WHERE: mahasiswa dengan <code>ipk > rata-rata ipk</code> seluruh mahasiswa.',hint:"SELECT npm,nama,ipk FROM mahasiswa WHERE ipk>(SELECT AVG(ipk) FROM mahasiswa);"},
    {text:'EXISTS: tampilkan prodi yang memiliki mahasiswa.',hint:"SELECT nmprodi FROM prodi WHERE EXISTS(SELECT 1 FROM mahasiswa WHERE idprodi=prodi.idprodi);"},
    {text:'Subquery di FROM: jumlah mahasiswa per prodi, tampilkan >3.',hint:"SELECT * FROM (SELECT idprodi,COUNT(*) jml FROM mahasiswa GROUP BY idprodi) sub WHERE jml>3;"}
  ]},

  // ===== 7-9: ADVANCED (M9-M13) — 20% =====
  { id:"g",label:"Stored Procedure",bobot:"7%",soal:[
    {text:'SP <code>sp_jml_mahasiswa(IN id_prodi INT, OUT jml INT)</code>: hitung mahasiswa per prodi.',hint:"CREATE PROCEDURE sp_jml_mahasiswa(IN id_prodi INT, OUT jml INT)\nBEGIN\n  SELECT COUNT(*) INTO jml FROM mahasiswa WHERE idprodi=id_prodi;\nEND;"},
    {text:'SP <code>sp_tambah_mahasiswa(IN p_npm VARCHAR, IN p_nama VARCHAR, IN p_idprodi INT)</code>: INSERT.',hint:"CREATE PROCEDURE sp_tambah_mahasiswa(IN p_npm VARCHAR(20), IN p_nama VARCHAR(100), IN p_idprodi INT)\nBEGIN\n  INSERT INTO mahasiswa(npm,nama,idprodi) VALUES(p_npm,p_nama,p_idprodi);\nEND;"},
    {text:'SP <code>sp_update_ipk(IN p_npm VARCHAR, IN p_ipk REAL)</code>: UPDATE jika ipk valid 0-4.',hint:"CREATE PROCEDURE sp_update_ipk(IN p_npm VARCHAR(20), IN p_ipk REAL)\nBEGIN\n  IF p_ipk>=0 AND p_ipk<=4 THEN\n    UPDATE mahasiswa SET ipk=p_ipk WHERE npm=p_npm;\n  END IF;\nEND;"}
  ]},
  { id:"h",label:"Trigger",bobot:"6%",soal:[
    {text:'AFTER INSERT: catat ke <code>audit_log</code> (aksi,tabel_target,ref_key=NEW.npm).',hint:"CREATE TRIGGER trg_audit_mhs AFTER INSERT ON mahasiswa FOR EACH ROW BEGIN INSERT INTO audit_log(aksi,tabel_target,ref_key) VALUES('INSERT','mahasiswa',NEW.npm); END;"},
    {text:'AFTER UPDATE: catat perubahan <code>ipk</code> (OLD.ipk, NEW.ipk) ke audit_log.',hint:"CREATE TRIGGER trg_audit_ipk AFTER UPDATE ON mahasiswa FOR EACH ROW WHEN NEW.ipk!=OLD.ipk BEGIN INSERT INTO audit_log(aksi,tabel_target,ref_key,kolom,nilai_lama,nilai_baru) VALUES('UPDATE','mahasiswa',NEW.npm,'ipk',OLD.ipk,NEW.ipk); END;"},
    {text:'BEFORE INSERT: RAISE ABORT jika ipk di luar 0.0–4.0.',hint:"CREATE TRIGGER trg_validasi_ipk BEFORE INSERT ON mahasiswa FOR EACH ROW BEGIN IF NEW.ipk<0.0 OR NEW.ipk>4.0 THEN SELECT RAISE(ABORT,'IPK tidak valid'); END IF; END;"}
  ]},
  { id:"i",label:"Transaction &amp; User Management",bobot:"7%",soal:[
    {text:'(a) BEGIN, INSERT mahasiswa, COMMIT. (b) CREATE USER <code>user1</code>, GRANT SELECT ON mahasiswa.',hint:'BEGIN; INSERT INTO mahasiswa(npm,nama,idprodi) VALUES("999","Test",1); COMMIT;\nCREATE USER "user1"@"localhost" IDENTIFIED BY "pass123";\nGRANT SELECT ON mahasiswa TO "user1"@"localhost";'},
    {text:'(a) BEGIN, INSERT, ROLLBACK. (b) CREATE USER <code>admin</code>, GRANT ALL ON *.*.',hint:'BEGIN; INSERT INTO mahasiswa(npm,nama,idprodi) VALUES("111","Ali",2); ROLLBACK;\nCREATE USER "admin"@"localhost" IDENTIFIED BY "admin123";\nGRANT ALL PRIVILEGES ON *.* TO "admin"@"localhost";'},
    {text:'(a) BEGIN, INSERT, SAVEPOINT sp1, UPDATE, ROLLBACK TO sp1, COMMIT. (b) CREATE USER <code>operator</code>, GRANT SELECT,INSERT,UPDATE ON mahasiswa.',hint:'BEGIN; INSERT INTO mahasiswa(npm,nama) VALUES("555","Budi");\nSAVEPOINT sp1;\nUPDATE mahasiswa SET nama="Budi REVISI" WHERE npm="555";\nROLLBACK TO sp1;\nCOMMIT;\nCREATE USER "operator"@"localhost" IDENTIFIED BY "op123";\nGRANT SELECT,INSERT,UPDATE ON mahasiswa TO "operator"@"localhost";'}
  ]},

  // ===== 10-14: PROJECT RND — CRUD (M14-M15) — 50% =====
  { id:"j",label:"Project — Skema Database",bobot:"10%",soal:[
    {text:'<strong>Berdasarkan project RND Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Sebutkan <strong>semua tabel</strong> di database aplikasi Anda.</li><li>Tuliskan <strong>CREATE TABLE</strong> untuk <u>satu tabel master</u> terpenting (lengkap dengan PK, tipe data, dan constraint).</li><li>Tuliskan <strong>CREATE TABLE</strong> untuk <u>satu tabel transaksi/junction</u> yang memiliki <strong>foreign key</strong> ke tabel master.</li></ol>',hint:"Tabel master: CREATE TABLE ...\nTabel transaksi: CREATE TABLE ... FOREIGN KEY ... REFERENCES ..."},
    {text:'<strong>Berdasarkan project RND Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Gambarkan relasi antar tabel (FK).</li><li>Jelaskan jenis relasi: one-to-one, one-to-many, many-to-many.</li><li>Tuliskan <strong>DDL lengkap</strong> (CREATE TABLE) untuk 3 tabel utama.</li></ol>',hint:"Relasi: tabel1.id_x → tabel2.id_x (one-to-many)\nCREATE TABLE ..."},
    {text:'<strong>Berdasarkan project RND Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Sebutkan <strong>semua tabel</strong> dan jelaskan fungsinya.</li><li>Tuliskan <strong>kamus data</strong> untuk salah satu tabel (nama kolom, tipe, constraint, keterangan).</li><li>Gambarkan <strong>diagram relasi</strong> antar tabel.</li></ol>',hint:"Tabel 1: ... (fungsi)\nKamus data: kolom | tipe | PK/FK | NOT NULL | keterangan\nRelasi: ..."}
  ]},
  { id:"k",label:"Project — CRUD: INSERT",bobot:"10%",soal:[
    {text:'<strong>INSERT data ke project Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Tuliskan <strong>3 query INSERT</strong> untuk menambahkan data contoh ke tabel master di project Anda.</li><li>Jelaskan bagaimana aplikasi Anda menangani <strong>validasi input</strong> sebelum INSERT (contoh: cek duplikat, format data).</li><li>Tuliskan <strong>SELECT</strong> untuk memverifikasi data yang baru di-INSERT.</li></ol>',hint:"INSERT 1: ...\nINSERT 2: ...\nINSERT 3: ...\nValidasi: ...\nSELECT verifikasi: ..."},
    {text:'<strong>INSERT data ke project Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Tuliskan <strong>INSERT</strong> ke <u>tabel transaksi</u> yang melibatkan minimal 2 foreign key.</li><li>Jelaskan bagaimana aplikasi Anda memastikan <strong>referential integrity</strong> saat INSERT.</li><li>Tuliskan <strong>SELECT dengan JOIN</strong> untuk melihat data transaksi lengkap.</li></ol>',hint:"INSERT transaksi: ...\nReferential integrity: ...\nSELECT JOIN: ..."},
    {text:'<strong>INSERT data ke project Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Tuliskan <strong>3 INSERT</strong> ke 3 tabel berbeda di project Anda.</li><li>Jelaskan <strong>alur kode</strong> dari form input → validasi → query INSERT di aplikasi Anda.</li><li>Bagaimana aplikasi menangani jika INSERT <strong>gagal</strong> (error handling)?</li></ol>',hint:"INSERT 1: ... INSERT 2: ... INSERT 3: ...\nAlur: form → validasi → prepared statement → execute → feedback\nError handling: try-catch / error message"}
  ]},
  { id:"l",label:"Project — CRUD: SELECT &amp; JOIN",bobot:"10%",soal:[
    {text:'<strong>SELECT dari project Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Tuliskan <strong>SELECT dengan INNER JOIN</strong> minimal 2 tabel dari project Anda.</li><li>Tambahkan <strong>WHERE</strong> untuk filter spesifik.</li><li>Tambahkan <strong>ORDER BY</strong> untuk mengurutkan hasil.</li><li>Jelaskan <strong>apa yang ditampilkan</strong> query ini di aplikasi Anda.</li></ol>',hint:"SELECT ... FROM t1 INNER JOIN t2 ON ... WHERE ... ORDER BY ...\nMenampilkan: ..."},
    {text:'<strong>SELECT dari project Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Tuliskan <strong>SELECT dengan agregasi</strong> (COUNT/SUM/AVG) + GROUP BY dari project Anda.</li><li>Tambahkan <strong>HAVING</strong> untuk filter hasil agregasi.</li><li>Jelaskan <strong>kegunaan</strong> query ini di aplikasi Anda (misal: laporan, dashboard).</li></ol>',hint:"SELECT ... COUNT(...) FROM ... GROUP BY ... HAVING ...\nKegunaan: laporan ..."},
    {text:'<strong>SELECT dari project Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Tuliskan <strong>subquery</strong> yang relevan dengan project Anda.</li><li>Tuliskan <strong>SELECT dengan LIKE</strong> untuk fitur pencarian.</li><li>Bagaimana aplikasi Anda menampilkan hasil query ke pengguna?</li></ol>',hint:"Subquery: SELECT ... WHERE ... (SELECT ...)\nLIKE: SELECT ... WHERE kolom LIKE '%keyword%'\nTampilan: tabel HTML / grid / list"}
  ]},
  { id:"m",label:"Project — CRUD: UPDATE &amp; DELETE",bobot:"10%",soal:[
    {text:'<strong>UPDATE &amp; DELETE di project Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Tuliskan <strong>UPDATE</strong> yang mengubah data di salah satu tabel project Anda (dengan WHERE spesifik).</li><li>Tuliskan <strong>DELETE</strong> dari tabel transaksi (dengan WHERE spesifik).</li><li>Jelaskan <strong>dampak DELETE</strong> terhadap data di tabel lain (FK cascade / restrict).</li></ol>',hint:"UPDATE ... SET ... WHERE ...\nDELETE FROM ... WHERE ...\nDampak: ... (ON DELETE CASCADE / RESTRICT)"},
    {text:'<strong>UPDATE &amp; DELETE di project Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Tuliskan <strong>UPDATE multi-kolom</strong> dengan kondisi WHERE yang melibatkan subquery.</li><li>Tuliskan <strong>DELETE</strong> dan jelaskan bagaimana aplikasi Anda memberi <strong>konfirmasi</strong> ke pengguna sebelum hapus.</li><li>Bagaimana aplikasi menangani <strong>soft delete</strong> vs hard delete?</li></ol>',hint:"UPDATE ... SET ... WHERE ... IN (SELECT ...)\nDELETE FROM ... WHERE ...\nKonfirmasi: dialog / modal\nSoft delete: UPDATE status = 'dihapus'"},
    {text:'<strong>UPDATE &amp; DELETE di project Anda:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Tuliskan <strong>transaksi UPDATE</strong> yang melibatkan 2 tabel sekaligus (dengan BEGIN/COMMIT).</li><li>Tuliskan <strong>DELETE dengan JOIN</strong> untuk menghapus data terkait.</li><li>Jelaskan bagaimana aplikasi Anda menangani <strong>kesalahan</strong> saat UPDATE/DELETE.</li></ol>',hint:"BEGIN; UPDATE t1 ...; UPDATE t2 ...; COMMIT;\nDELETE t1 FROM t1 JOIN t2 ON ... WHERE ...\nError handling: ..."}
  ]},
  { id:"n",label:"Project — Arsitektur &amp; Deployment",bobot:"10%",soal:[
    {text:'<strong>Arsitektur &amp; deployment:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Sebutkan <strong>tech stack</strong> lengkap (bahasa pemrograman, framework, database, tools).</li><li>Jelaskan <strong>struktur folder</strong> project Anda.</li><li>Jelaskan <strong>alur data</strong> dari browser pengguna → backend → database → kembali ke pengguna.</li></ol>',hint:"Stack: Backend=..., Database=..., Frontend=...\nFolder: project/\n  ├── config/\n  ├── ...\nAlur: request → routing → controller → model → query → DB → response"},
    {text:'<strong>Deployment:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Jelaskan <strong>langkah deployment</strong> project Anda (dari lokal → hosting).</li><li>Sebutkan <strong>URL deployment</strong> jika sudah online.</li><li>Sebutkan <strong>perbedaan konfigurasi</strong> antara lokal dan hosting.</li></ol>',hint:"Langkah: 1. Export DB 2. Upload file 3. Import SQL 4. Konfigurasi koneksi 5. Testing\nURL: https://...\nKonfigurasi: localhost → server/database production"},
    {text:'<strong>Evaluasi project:</strong></p><ol style="line-height:1.8;padding-left:20px;"><li>Sebutkan <strong>fitur paling kompleks</strong> di project Anda dan mengapa.</li><li>Sebutkan <strong>3 kendala</strong> yang dihadapi dan solusinya.</li><li>Jika dikerjakan ulang, apa yang akan Anda <strong>perbaiki/tambahkan</strong>?</li></ol>',hint:"Fitur kompleks: ... karena ...\nKendala 1: ... Solusi: ...\nKendala 2: ... Solusi: ...\nKendala 3: ... Solusi: ...\nPerbaikan: ..."}
  ]}
// --- BEGIN AUTO ANSWERS INJECTION ---
POOLS[9].soal[0].answer = "1. Tabel: users, categories, suppliers, products, stock, purchase_orders, purchase_order_items, receiving, receiving_items, sales_orders, sales_order_items, stock_movements, audit_logs.\n2. CREATE TABLE products (\n    product_id INT PRIMARY KEY AUTO_INCREMENT,\n    kode_produk VARCHAR(50) UNIQUE NOT NULL,\n    nama_produk VARCHAR(100) NOT NULL,\n    category_id INT NOT NULL,\n    supplier_id INT NOT NULL,\n    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT,\n    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE RESTRICT\n);\n3. CREATE TABLE receiving_items (\n    ri_id INT PRIMARY KEY AUTO_INCREMENT,\n    receiving_id INT NOT NULL,\n    product_id INT NOT NULL,\n    quantity_received INT NOT NULL,\n    FOREIGN KEY (receiving_id) REFERENCES receiving(receiving_id) ON DELETE CASCADE,\n    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT\n);";
POOLS[9].soal[1].answer = "1. products FK ke categories & suppliers. receiving FK ke suppliers & users. receiving_items FK ke receiving & products.\n2. Relasi: One-to-Many (1 kategori memiliki banyak produk, 1 penerimaan memiliki banyak item).\n3. \nCREATE TABLE categories (category_id INT PRIMARY KEY, nama_kategori VARCHAR(100));\nCREATE TABLE products (product_id INT PRIMARY KEY, category_id INT, FOREIGN KEY (category_id) REFERENCES categories(category_id));\nCREATE TABLE receiving_items (ri_id INT PRIMARY KEY, receiving_id INT, product_id INT, FOREIGN KEY (receiving_id) REFERENCES receiving(receiving_id), FOREIGN KEY (product_id) REFERENCES products(product_id));";
POOLS[9].soal[2].answer = "1. products (data barang), categories (kategori barang), receiving (header penerimaan), receiving_items (detail item diterima), stock (stok saat ini).\n2. Kamus data products: product_id (INT, PK), kode_produk (VARCHAR(50), UNIQUE), nama_produk (VARCHAR(100)), category_id (INT, FK).\n3. Diagram Relasi: categories(1) -> (M)products(1) -> (M)receiving_items(M) <- (1)receiving";
POOLS[10].soal[0].answer = "1. INSERT INTO categories (nama_kategori) VALUES ('Elektronik');\nINSERT INTO suppliers (nama_supplier, alamat) VALUES ('PT Maju', 'Jakarta');\nINSERT INTO products (kode_produk, nama_produk, category_id, supplier_id) VALUES ('E-01', 'TV', 1, 1);\n2. Validasi input: Laravel request validation (required, exists:table,id, numeric, dll) sebelum eksekusi Model::create().\n3. SELECT * FROM products WHERE kode_produk = 'E-01';";
POOLS[10].soal[1].answer = "1. INSERT INTO receiving (nomor_terima, supplier_id, user_id, tanggal_terima, status) VALUES ('TRM-001', 1, 1, NOW(), 'proses');\n2. Referential integrity: DB di-set ON DELETE RESTRICT pada FK supplier_id dan Laravel memvalidasi 'exists:suppliers,supplier_id' sebelum query INSERT.\n3. SELECT r.nomor_terima, s.nama_supplier FROM receiving r JOIN suppliers s ON r.supplier_id = s.supplier_id;";
POOLS[10].soal[2].answer = "1. INSERT INTO categories (nama_kategori) VALUES ('ATK');\nINSERT INTO suppliers (nama_supplier, alamat) VALUES ('CV Maju', 'Metro');\nINSERT INTO products (kode_produk, nama_produk, category_id, supplier_id) VALUES ('A-01', 'Buku', 1, 1);\n2. Alur: User submit form -> Route -> Controller -> $request->validate() -> Eloquent Model::create() -> Session Flash Message -> Redirect View.\n3. Error handling: Menggunakan try-catch block di controller, jika exception maka return back()->with('error', $e->getMessage()).";
POOLS[11].soal[0].answer = "1. SELECT p.kode_produk, p.nama_produk, c.nama_kategori FROM products p INNER JOIN categories c ON p.category_id = c.category_id\n2. WHERE p.status = 'aktif'\n3. ORDER BY p.nama_produk ASC;\n4. Query ini menampilkan daftar produk yang statusnya aktif beserta nama kategorinya (bukan cuma ID) dan diurutkan sesuai abjad.";
POOLS[11].soal[1].answer = "1. SELECT c.nama_kategori, COUNT(p.product_id) as jumlah_produk FROM categories c LEFT JOIN products p ON c.category_id = p.category_id GROUP BY c.category_id, c.nama_kategori\n2. HAVING jumlah_produk > 0;\n3. Kegunaan: Menampilkan kategori apa saja yang sudah memiliki barang/produk terdaftar di sistem, cocok untuk laporan inventory.";
POOLS[11].soal[2].answer = "1. Subquery: SELECT * FROM products WHERE product_id IN (SELECT product_id FROM stock WHERE quantity_on_hand < reorder_level);\n2. SELECT * FROM products WHERE nama_produk LIKE '%Minyak%';\n3. Hasil query ditampilkan dalam bentuk Tabel HTML di halaman web Blade dengan fitur pagination dari Laravel.";
POOLS[12].soal[0].answer = "1. UPDATE products SET harga_jual = 60000, updated_at = NOW() WHERE kode_produk = 'MAKANAN-001';\n2. DELETE FROM receiving WHERE receiving_id = 1 AND status = 'proses';\n3. Dampak: Karena tabel receiving_items di-setting ON DELETE CASCADE, maka semua detail item untuk penerimaan tersebut akan ikut terhapus otomatis dari database.";
POOLS[12].soal[1].answer = "1. UPDATE products SET status = 'nonaktif', updated_at = NOW() WHERE product_id IN (SELECT product_id FROM stock WHERE quantity_on_hand = 0);\n2. DELETE: DELETE FROM suppliers WHERE supplier_id = 2. Konfirmasi: Aplikasi memunculkan JS pop-up confirm('Yakin hapus data?') sebelum request DELETE dijalankan.\n3. Aplikasi menggunakan Hard Delete (hapus permanen dari DB), namun dilindungi validasi bisnis: data transaksi 'selesai' tidak bisa dihapus.";
POOLS[12].soal[2].answer = "1. BEGIN;\nUPDATE receiving SET status = 'dibatalkan' WHERE receiving_id = 1;\nUPDATE stock SET quantity_on_hand = quantity_on_hand - 5 WHERE product_id = 1;\nCOMMIT;\n2. DELETE ri FROM receiving_items ri JOIN receiving r ON ri.receiving_id = r.receiving_id WHERE r.status = 'dibatalkan';\n3. Kesalahan diatasi dengan DB::beginTransaction() & DB::rollBack() di Laravel jika terjadi exception saat update/delete.";
POOLS[13].soal[0].answer = "1. Tech Stack: Backend = Laravel 10 (PHP), Database = MySQL, Frontend = Blade Template + Vanilla CSS, Versioning = Git.\n2. Struktur: app/ (Logic Model & Controller), database/ (Migrations schema), resources/views/ (Tampilan web HTML/Blade), routes/ (Routing url).\n3. Alur data: Request dari browser -> Route web.php -> Controller -> Model akses MySQL -> Controller dapat data -> Di-pass ke View Blade -> HTML dikirim ke browser.";
POOLS[13].soal[1].answer = "1. Langkah Deployment: 1) Export database SQL dari localhost. 2) Upload source code via FTP/cPanel ke Hosting. 3) Import SQL ke PhpMyAdmin Hosting. 4) Edit file .env sesuaikan DB_HOST, DB_USER, DB_PASSWORD. 5) Testing aplikasi.\n2. URL deployment: (Diuji di localhost menggunakan php artisan serve / xampp).\n3. Perbedaan Konfigurasi: Di lokal APP_ENV=local, DB_HOST=localhost. Di hosting APP_ENV=production, DB_HOST=(ip_server/localhost hosting).";
POOLS[13].soal[2].answer = "1. Fitur paling kompleks: Modul Penerimaan (Receiving) karena melibatkan insert ke multi-tabel (header & detail), validasi status, dan memicu trigger DB / logika controller untuk update tabel Stok.\n2. Kendala: a) Sinkronisasi stok (Solusi: pakai DB Trigger dan validasi status transaksi). b) Query Join panjang (Solusi: manfaatkan Eager Loading / VIEW MySQL). c) Tampilan cetak berantakan (Solusi: tambah class @media print CSS).\n3. Perbaikan: Akan menambahkan fitur cetak PDF otomatis dan grafik statistik dashboard.";
// --- END AUTO ANSWERS INJECTION ---
];

/* ========== JADWAL DEMO ZOOM (global, anti-AI: demo live) ========== */
// Jadwal global untuk semua mahasiswa (tidak ada data roster per-NIM).
// Demo live di Zoom = bukti yang AI tak bisa kalahkan.
function zoomHtml() {
  return '<div class="section"><div class="section-head"><span class="num">DEMO</span><h2>Jadwal Demo Zoom</h2></div>' +
    '<div class="section-body">' +
      '<div class="pernyataan-box" style="border-left-color:var(--accent-2);">' +
        '<p style="margin:0 0 6px;">Setelah selesai mengerjakan, lakukan <strong>demo aplikasi secara live via Zoom</strong> (&plusmn;10 menit/mahasiswa).</p>' +
        '<p style="font-size:20px;font-weight:800;margin:8px 0;">Jadwal: <span style="font-family:var(--mono);">hari ujian, pukul 18:00 &ndash; 21:00 WIB</span></p>' +
        '<p style="margin:0;">Link Zoom diumumkan dosen. Masuk waiting room dan tunggu dipanggil. Saat demo: perkenalkan diri (nama &amp; NIM), lalu jalankan CRUD langsung di database project Anda.</p>' +
      '</div>' +
      '<div class="soal-item" style="border-left-color:var(--warn);">' +
        '<div class="soal-nomor">Jika berhalangan hadir di Zoom</div>' +
        '<p style="margin:8px 0;">WAJIB membuat video presentasi (demo CRUD + perkenalan nama &amp; NIM), unggah ke YouTube (status <em>Unlisted</em>), lalu <strong>laporkan tautannya langsung ke dosen</strong> setelah video dibuat. <u>Tidak perlu</u> menulis link di file ini. Tanpa demo live maupun video = nilai demo 0.</p>' +
      '</div>' +
    '</div></div>';
}

/* ========== GENERATE SOAL ========== */
function generateSoal(seed) {
  if (!seed) { seed = nimSeed(lockedNim); if (!seed) return; }
  const container = $("soalContainer"); if (!container) return;

  if (container.hasAttribute("data-generated") && container.getAttribute("data-nim") === lockedNim) {
    $("bagianPernyataan").style.display = "block";
    startTimerIfNeeded();
    return;
  }

  let html = zoomHtml();
  POOLS.forEach((pool, pi) => {
    const idx = pickIdx(seed, pool.soal.length, pi);
    const q = pool.soal[idx];
    html += '<div class="section">' +
      '<div class="section-head"><span class="num">' + (pi + 1) + '</span><h2>' + pool.label +
      ' <span style="font-size:13px;font-weight:400;text-transform:none;letter-spacing:0;">(Bobot: ' + pool.bobot + ')</span></h2></div>' +
      '<div class="section-body">' +
        '<div class="soal-item">' +
          '<div class="soal-nomor">Soal ' + (pi + 1) +
            '<span class="soal-variant">Varian ' + (idx + 1) + '/' + pool.soal.length + '</span></div>' +
          '<div class="soal-teks">' + q.text + '</div>' +
          (q.hint ? '<div class="soal-petunjuk">Petunjuk: ' + q.hint + '</div>' : '') +
          '<textarea class="jawaban code" id="jawab_' + (pi + 1) + '" rows="5" placeholder="Tulis jawaban...">' + (q.answer || q.hint || "").replace(/<br>/g, "\\n") + '</textarea>' +
        '</div>' +
      '</div>' +
    '</div>';
  });

  container.innerHTML = html;
  container.setAttribute("data-generated", "1");
  container.setAttribute("data-nim", lockedNim);
  $("bagianPernyataan").style.display = "block";
  startTimerIfNeeded();
  updateProgress();
  window.scrollTo({ top: container.offsetTop - 100, behavior: "smooth" });
}

/* ========== SAVE / LOAD ========== */
function saveData(silent) {
  const data = collectData();
  try { localStorage.setItem(STORE_KEY, JSON.stringify(data)); } catch (e) {}
  idbSet(STORE_KEY, data).then(() => updateStorageMeter()).catch(() => {});
}
function loadData() {
  idbGet(STORE_KEY).then(d => {
    if (d) { restoreState(d); return; }
    const raw = localStorage.getItem(STORE_KEY);
    if (raw) { try { restoreState(JSON.parse(raw)); } catch (e) {} }
    updateStorageMeter();
  }).catch(() => {
    const raw = localStorage.getItem(STORE_KEY);
    if (raw) { try { restoreState(JSON.parse(raw)); } catch (e) {} }
    updateStorageMeter();
  });
}
function restoreState(d) {
  applyData(d);
  if (d.__lockedNim) {
    lockedNim = d.__lockedNim;
    const inp = $("nim"); if (inp) {
      inp.value = d.__lockedNim;
      inp.disabled = true; inp.style.background = "var(--rule-soft)"; inp.style.opacity = "0.6";
      $("lockNimBtn").textContent = "Terkunci"; $("lockNimBtn").disabled = true;
    }
    generateSoal();
    applyData(d); // re-apply: jawab_N only exist after generateSoal
    updateProgress();
  }
}

/* ========== EXPORT / IMPORT ========== */
function exportJSON() {
  const data = collectData();
  const nimId = (lockedNim || data.nim || "uas").replace(/[^\w.-]/g, "_");
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: "application/json" });
  const a = document.createElement("a");
  a.href = URL.createObjectURL(blob);
  a.download = "UAS_SQL2_" + nimId + ".json";
  a.click();
  setTimeout(() => URL.revokeObjectURL(a.href), 2000);
}
function importJSON(file) {
  const r = new FileReader();
  r.onload = ev => {
    try {
      const d = JSON.parse(ev.target.result);
      if (!confirm("Muat data dari file? Data saat ini akan tertimpa.")) return;
      unlockNim(); stopTimer();
      applyData(d);
      if (d.__lockedNim) { lockedNim = d.__lockedNim; const inp = $("nim"); if (inp) inp.value = d.__lockedNim; kunciNIM(); }
      updateProgress(); saveData(true);
      alert("Jawaban berhasil dimuat.");
    } catch (e) { alert("File tidak valid."); }
  };
  r.readAsText(file);
}
function resetData() {
  if (!confirm("Yakin reset SEMUA jawaban? Data akan hilang permanen.")) return;
  stopTimer();
  localStorage.removeItem(STORE_KEY); localStorage.removeItem(TIMER_STORE);
  idbSet(STORE_KEY, null).catch(() => {});
  unlockNim();
  document.querySelectorAll("#uasDocument input, #uasDocument textarea").forEach(el => { el.value = ""; });
  $("soalContainer").innerHTML = "";
  updateProgress(); updateStorageMeter();
  alert("Semua jawaban telah direset.");
}

/* ========== VALIDATE & PRINT ========== */
function validateAndPrint() {
  if (!lockedNim) { alert("Kunci NIM terlebih dahulu."); return; }
  const missing = [];
  document.querySelectorAll("[data-required]").forEach(el => {
    if (!el.value || el.value.trim() === "") missing.push(el.getAttribute("data-label") || el.id);
  });
  if (missing.length) {
    if (!confirm(missing.length + " wajib belum diisi:\n- " + missing.slice(0,8).join("\n- ") + (missing.length>8?"\n- ...":"") + "\n\nTetap cetak?")) return;
  }
  syncPrintMirrors(); saveData(true); window.print();
}

/* ========== ANTI COPY-PASTE + SEB DETECTION ========== */
function detectSEB() {
  // Safe Exam Browser mendeteksi dari beberapa indikator:
  // 1. User Agent SEB
  if (navigator.userAgent.indexOf("SEB") !== -1) return true;
  // 2. window.safeexambrowser API (SEB 3.x)
  if (typeof window.safeexambrowser !== "undefined") return true;
  // 3. Non-standard SEB property
  if (typeof window.Seb !== "undefined") return true;
  return false;
}

function showSEBStatus() {
  // Deteksi SEB hanya untuk informasi — TIDAK memblokir akses
  const isSEB = detectSEB();
  const bar = $("nimInfoBar");
  if (!bar) return;
  if (isSEB) {
    bar.style.background = "var(--ok)";
    $("nimInfoText").innerHTML = "Safe Exam Browser terdeteksi — mode ujian aman";
  }
  // Tidak di SEB = tetap bisa ujian (tidak diblokir)
}

function antiCheat() {
  // Tampilkan status SEB
  showSEBStatus();

  // Blokir context menu
  document.addEventListener("contextmenu", e => {
    const tag = (e.target.tagName || "").toLowerCase();
    if (tag === "input" || tag === "textarea") return;
    e.preventDefault();
    return false;
  });

  // Blokir copy/cut/paste di luar field input
  document.addEventListener("copy", e => {
    const tag = (e.target.tagName || "").toLowerCase();
    if (tag === "input" || tag === "textarea") return;
    e.preventDefault();
    return false;
  });
  document.addEventListener("cut", e => {
    const tag = (e.target.tagName || "").toLowerCase();
    if (tag === "input" || tag === "textarea") return;
    e.preventDefault();
    return false;
  });
  document.addEventListener("paste", e => {
    const tag = (e.target.tagName || "").toLowerCase();
    if (tag === "input" || tag === "textarea") return;
    e.preventDefault();
    return false;
  });

  // Blokir keyboard shortcuts
  document.addEventListener("keydown", e => {
    const tag = (e.target.tagName || "").toLowerCase();
    const inField = (tag === "input" || tag === "textarea");
    if ((e.ctrlKey || e.metaKey) && !inField) {
      const k = e.key.toLowerCase();
      if (k === "c" || k === "x" || k === "v" || k === "a" || k === "u" || k === "s" || k === "p") {
        e.preventDefault();
        return false;
      }
    }
    // F12, Ctrl+Shift+I/J, Ctrl+U
    if (e.key === "F12" || (e.ctrlKey && e.shiftKey && (e.key.toLowerCase() === "i" || e.key.toLowerCase() === "j")) || (e.ctrlKey && e.key.toLowerCase() === "u")) {
      e.preventDefault();
      return false;
    }
  });

  // Blokir drag
  document.addEventListener("dragstart", e => { e.preventDefault(); return false; });

  // Deteksi DevTools terbuka (heuristic)
  let devtoolsOpen = false;
  const threshold = 160;
  setInterval(() => {
    const widthThreshold = window.outerWidth - window.innerWidth > threshold;
    const heightThreshold = window.outerHeight - window.innerHeight > threshold;
    if (widthThreshold || heightThreshold) {
      if (!devtoolsOpen) {
        devtoolsOpen = true;
        // Blur jawaban jika devtools terdeteksi
        document.querySelectorAll(".jawaban").forEach(el => { el.style.filter = "blur(8px)"; });
        document.querySelectorAll(".soal-teks").forEach(el => { el.style.filter = "blur(8px)"; });
      }
    } else {
      if (devtoolsOpen) {
        devtoolsOpen = false;
        document.querySelectorAll(".jawaban").forEach(el => { el.style.filter = ""; });
        document.querySelectorAll(".soal-teks").forEach(el => { el.style.filter = ""; });
      }
    }
  }, 1000);
}

/* ========== INIT ========== */
window.addEventListener("DOMContentLoaded", () => {
  antiCheat();
  const yr = $("copyYear"); if (yr) yr.textContent = new Date().getFullYear();
  loadData(); updateProgress();

  $("simpanBtn").addEventListener("click", () => { saveData(false); alert("Tersimpan."); });
  $("resetBtn").addEventListener("click", resetData);
  $("cetakBtn").addEventListener("click", validateAndPrint);
  $("exportBtn").addEventListener("click", exportJSON);
  $("importBtn").addEventListener("click", () => $("importFile").click());
  $("importFile").addEventListener("change", e => {
    if (e.target.files[0]) importJSON(e.target.files[0]); e.target.value = "";
  });
  window.addEventListener("beforeprint", syncPrintMirrors);

  let t;
  document.addEventListener("input", () => {
    updateProgress();
    clearTimeout(t);
    t = setTimeout(() => saveData(true), 1500);
  });
});
