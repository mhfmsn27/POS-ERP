// POSHUB ACCOUNTING & ERP - User Documentation Navigation Schema v3.5 Enterprise
const NAV = [
  { section: "1. Memulai & Fondasi" },
  { label: "Pengantar & Arsitektur", icon: "fa-solid fa-house", href: "index.html", keywords: "laravel vue pwa hardware rsa quickstart install" },
  { label: "Gambaran Ekosistem v3.5", icon: "fa-solid fa-layer-group", href: "overview.html", keywords: "fitur modul arsitektur integrasi data flow" },
  { label: "Setup VPS Multi-Tenant", icon: "fa-solid fa-server", href: "setup-vps.html", keywords: "vps domainesia rumahweb nginx mysql ssl certbot isolasi" },
  { divider: true },

  { section: "2. Kasir POS & Smart Hardware" },
  { label: "Panel Kasir POS Modern", icon: "fa-solid fa-cash-register", href: "pos.html", keywords: "kasir shortcut keyboard barcode split bill payment f2 f4 f8" },
  { label: "Customer Facing Display", icon: "fa-solid fa-tv", href: "customer-display.html", keywords: "dual screen monitor kedua qris dinamis promo video cart" },
  { label: "Kitchen Display (KDS)", icon: "fa-solid fa-utensils", href: "kitchen-display.html", keywords: "dapur kds bar ticket pesanan routing waktu sla" },
  { label: "Hardware Thermal & Barcode", icon: "fa-solid fa-print", href: "hardware-drivers.html", keywords: "printer bluetooth esc pos 58mm 80mm cash drawer rj11 sunmi imin" },
  { label: "Shift Kasir & Z-Report WA", icon: "fa-solid fa-receipt", href: "shift-management.html", keywords: "shift register modal kas masuk keluar z report whatsapp selisih" },
  { divider: true },

  { section: "3. Penjualan, Stok & Rantai Pasok" },
  { label: "Produk, Varian & Multi-UOM", icon: "fa-solid fa-boxes-stacked", href: "products.html", keywords: "produk varian uom lusin karton pcs barcode code 128 sku" },
  { label: "Penawaran & Komisi Sales", icon: "fa-solid fa-file-signature", href: "quotations-sales.html", keywords: "quotation penawaran harga sales order invoice tempo komisi salesman target" },
  { label: "Pembelian & Multi-Gudang", icon: "fa-solid fa-truck-ramp-box", href: "purchases.html", keywords: "purchase order po supplier gudang transfer antar cabang grn" },
  { label: "Opname, Koreksi & Retur", icon: "fa-solid fa-clipboard-check", href: "stock-opname-returns.html", keywords: "stock opname fisik adjustment koreksi selisih retur jual beli supplier" },
  { label: "Peringatan Stok & Auto-PO", icon: "fa-solid fa-bell", href: "stock-alerts.html", keywords: "alert minimum reorder point rop auto po cron schedule" },
  { divider: true },

  { section: "4. Akuntansi & Keuangan Standar PSAK" },
  { label: "COA, Jurnal & Buku Besar", icon: "fa-solid fa-book-journal-whills", href: "general-ledger.html", keywords: "chart of accounts coa jurnal umum otomatis buku besar neraca saldo debit kredit" },
  { label: "Cost Center & P&L Proyek", icon: "fa-solid fa-diagram-project", href: "cost-centers.html", keywords: "cost center profit center laba rugi divisi cabang proyek p&l integrity seal" },
  { label: "Aset Tetap & Budgeting", icon: "fa-solid fa-vault", href: "fixed-assets-budgeting.html", keywords: "fixed asset aset tetap depresiasi penyusutan garis lurus anggaran budgeting multi currency kurs valuta" },
  { label: "Rekonsiliasi Bank & Kas Kecil", icon: "fa-solid fa-building-columns", href: "bank-reconciliation.html", keywords: "rekon bank matching engine rekening koran petty cash kas kecil imprest fluktuasi" },
  { label: "Laporan Keuangan & Forecast AI", icon: "fa-solid fa-chart-line", href: "financial-reports.html", keywords: "laporan neraca laba rugi arus kas altman z score health index forecast ai prediksi" },
  { label: "Kepatuhan Pajak & e-Faktur DJP", icon: "fa-solid fa-file-invoice-dollar", href: "tax-compliance.html", keywords: "pajak ppn 11 pph 21 23 e faktur djp xml export spt masa npwp" },
  { divider: true },

  { section: "5. Pelanggan, CRM & Portal B2B" },
  { label: "CRM, Loyalty Card & Poin", icon: "fa-solid fa-users", href: "customers.html", keywords: "pelanggan crm member silver gold platinum vip point reward vcard digital" },
  { label: "Portal B2B Grosir & Vendor", icon: "fa-solid fa-handshake", href: "b2b-portal.html", keywords: "b2b portal grosir reseller distributor plafon kredit top tempo invoice mandiri" },
  { label: "RMA & Servis Garansi", icon: "fa-solid fa-screwdriver-wrench", href: "rma-service.html", keywords: "rma servis perbaikan elektronik nomor seri spare part tracking garansi publik" },
  { label: "WhatsApp Gateway & Template", icon: "fa-brands fa-whatsapp", href: "whatsapp-automation.html", keywords: "whatsapp gateway multi device qr template pesan otomatis e receipt invoice pdf" },
  { divider: true },

  { section: "6. E-Commerce Omnichannel" },
  { label: "Toko Online B2C Terintegrasi", icon: "fa-solid fa-store", href: "ecommerce.html", keywords: "toko online website b2c flash sale banner promo keranjang satu halaman" },
  { label: "Midtrans QRIS & RajaOngkir", icon: "fa-solid fa-truck-fast", href: "payment-expedition.html", keywords: "midtrans qris virtual account bca mandiri rajaongkir jne j&t ongkir otomatis" },
  { divider: true },

  { section: "7. Mobile, Keamanan & Referensi" },
  { label: "PWA 2.0 & Native Mobile App", icon: "fa-solid fa-mobile-screen-button", href: "mobile-pwa.html", keywords: "pwa offline sync indexeddb background service worker capacitor android apk ios ipa" },
  { label: "Role, Hak Akses & Multi-Store", icon: "fa-solid fa-user-shield", href: "roles-permissions.html", keywords: "spatie role permission hak akses kasir admin manager akuntan audit trail" },
  { label: "Lisensi RSA-2048 & Anti-Bypass", icon: "fa-solid fa-shield-halved", href: "security-license.html", keywords: "lisensi 1 domain rsa 2048 signature asymmetric logic binding anti bypass keygen" },
  { label: "Import/Export Massal Excel", icon: "fa-solid fa-file-excel", href: "import-export.html", keywords: "import export excel csv master produk pelanggan saldo awal jurnal spt" },
  { label: "Auto Backup & Optimizer", icon: "fa-solid fa-database", href: "backup-maintenance.html", keywords: "backup otomatis database sql zip cron optimizer cache speed" },
  { label: "Developer & REST API", icon: "fa-solid fa-code", href: "api.html", keywords: "api endpoint bearer token sanctum post get json swagger webhook" },
  { label: "FAQ & Pemecahan Masalah", icon: "fa-solid fa-circle-question", href: "faq.html", keywords: "faq tanya jawab troubleshooting error solusi bluetooth printer lock screen" },
  { label: "Catatan Rilis (Changelog v3.5)", icon: "fa-solid fa-clock-rotate-left", href: "changelog.html", keywords: "changelog riwayat pembaruan versi 3.5 flagship 2026 update log" },
];

function renderNav(currentPage) {
  const sidebar = document.getElementById('sidebar');
  if (!sidebar) return;
  let html = '';
  NAV.forEach(item => {
    if (item.section) {
      html += `<div class="nav-section"><div class="nav-section-title">${item.section}</div>`;
    } else if (item.divider) {
      html += `</div><div class="nav-divider"></div>`;
    } else {
      const active = item.href === currentPage ? ' active' : '';
      html += `<a href="${item.href}" class="nav-link${active}"><i class="icon fa-fw ${item.icon}"></i><span>${item.label}</span></a>`;
    }
  });
  sidebar.innerHTML = html;
}

// Copy Code to Clipboard Helper
function copyCode(btn) {
  const pre = btn.closest('.code-container').querySelector('pre code');
  if (!pre) return;
  const text = pre.innerText;
  navigator.clipboard.writeText(text).then(() => {
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
    btn.style.background = '#059669';
    setTimeout(() => {
      btn.innerHTML = origHtml;
      btn.style.background = '';
    }, 2000);
  });
}

// FAQ Accordion Toggle
document.addEventListener('click', e => {
  const faqQ = e.target.closest('.faq-q');
  if (faqQ) {
    faqQ.closest('.faq-item').classList.toggle('open');
  }
});

// Scrollspy for Right TOC
window.addEventListener('DOMContentLoaded', () => {
  const sections = document.querySelectorAll('h2[id], h3[id]');
  const tocLinks = document.querySelectorAll('.toc-link');
  if (sections.length > 0 && tocLinks.length > 0) {
    window.addEventListener('scroll', () => {
      let current = '';
      sections.forEach(sec => {
        const top = sec.offsetTop - 120;
        if (window.scrollY >= top) {
          current = sec.getAttribute('id');
        }
      });
      tocLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) {
          link.classList.add('active');
        }
      });
    });
  }

  // Setup Global Search Engine Dialog
  setupSearchEngine();
});

function setupSearchEngine() {
  const searchTriggers = document.querySelectorAll('.header-search');
  
  // Inject Search Modal HTML if not exists
  if (!document.getElementById('poshubSearchModal')) {
    const modalHtml = `
      <div id="poshubSearchModal" class="search-modal-backdrop" style="display:none;">
        <div class="search-modal-box">
          <div class="search-modal-header">
            <i class="fa-solid fa-magnifying-glass text-muted"></i>
            <input type="text" id="poshubSearchInput" placeholder="Ketik kata kunci (misal: barcode, qris, coa, pwa, rsa, po)..." autocomplete="off" />
            <span class="search-close-btn" onclick="closeSearchModal()"><i class="fa-solid fa-xmark"></i></span>
          </div>
          <div class="search-modal-results" id="poshubSearchResults">
            <div class="search-hint">Mulai ketik untuk mencari topik dokumentasi...</div>
          </div>
          <div class="search-modal-footer">
            <span>Navigasi: Gunakan <strong>Enter</strong> untuk memilih &bull; <strong>Esc</strong> untuk menutup</span>
          </div>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
  }

  const modal = document.getElementById('poshubSearchModal');
  const input = document.getElementById('poshubSearchInput');
  const resultsContainer = document.getElementById('poshubSearchResults');

  function openSearchModal() {
    modal.style.display = 'flex';
    input.value = '';
    input.focus();
    renderSearchResults('');
  }

  window.closeSearchModal = function() {
    modal.style.display = 'none';
  };

  searchTriggers.forEach(el => {
    el.addEventListener('click', openSearchModal);
  });

  // Shortcut Ctrl + K or Cmd + K or '/'
  document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      openSearchModal();
    } else if (e.key === 'Escape' && modal.style.display === 'flex') {
      closeSearchModal();
    }
  });

  modal.addEventListener('click', e => {
    if (e.target === modal) closeSearchModal();
  });

  input.addEventListener('input', e => {
    renderSearchResults(e.target.value.trim().toLowerCase());
  });

  function renderSearchResults(query) {
    if (!query) {
      resultsContainer.innerHTML = `
        <div class="search-hint">
          <div style="font-weight:600; margin-bottom:6px; color:#1e293b;">Topik Populer:</div>
          <div style="display:flex; flex-wrap:wrap; gap:6px;">
            <span class="search-tag" onclick="fillSearch('pos')">Kasir POS</span>
            <span class="search-tag" onclick="fillSearch('qris')">QRIS Dinamis</span>
            <span class="search-tag" onclick="fillSearch('coa')">Bagan Akun (COA)</span>
            <span class="search-tag" onclick="fillSearch('pwa')">PWA Offline</span>
            <span class="search-tag" onclick="fillSearch('rsa')">Lisensi RSA-2048</span>
            <span class="search-tag" onclick="fillSearch('vps')">Setup VPS</span>
          </div>
        </div>
      `;
      return;
    }

    const matched = NAV.filter(item => {
      if (!item.href) return false;
      const label = item.label.toLowerCase();
      const kw = (item.keywords || '').toLowerCase();
      return label.includes(query) || kw.includes(query);
    });

    if (matched.length === 0) {
      resultsContainer.innerHTML = `<div class="search-hint text-danger"><i class="fa-solid fa-circle-exclamation"></i> Tidak ada topik yang cocok dengan "<strong>${query}</strong>". Coba kata kunci lain.</div>`;
      return;
    }

    let html = '';
    matched.forEach(item => {
      html += `
        <a href="${item.href}" class="search-result-item">
          <div class="search-result-icon"><i class="${item.icon}"></i></div>
          <div class="search-result-text">
            <div class="search-result-title">${item.label}</div>
            <div class="search-result-meta">${item.href} &bull; ${item.keywords || ''}</div>
          </div>
          <i class="fa-solid fa-arrow-right search-result-arrow"></i>
        </a>
      `;
    });
    resultsContainer.innerHTML = html;
  }

  window.fillSearch = function(val) {
    input.value = val;
    renderSearchResults(val);
  };
}
