<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display System (KDS) - POSHUB ACCOUNTING</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
    <style>
        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background: #0b0f19;
            color: #f1f5f9;
            min-height: 100vh;
        }
        .kds-card {
            background: #1e293b;
            border: 2px solid #334155;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            transition: all 0.2s ease;
        }
        .kds-card.status-cooking {
            border-color: #3b82f6;
            background: #1e293b;
        }
        .kds-card.status-ready {
            border-color: #10b981;
            background: #064e3b;
        }
        .kds-card.urgent {
            border-color: #ef4444;
            animation: pulseRed 2s infinite;
        }
        @keyframes pulseRed {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .station-tab.active {
            background: #2563eb !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body class="p-3">
    <div class="container-fluid">
        <!-- KDS Top Navigation -->
        <header class="d-flex justify-content-between align-items-center mb-4 p-3 bg-dark rounded-4 border border-secondary">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger text-white p-2 rounded-3">
                    <i class="fa fa-fire fa-2x"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-white tracking-wide">KITCHEN DISPLAY SYSTEM</h3>
                    <span class="text-white-50 small">Layar Pesanan Dapur & Bar Real-Time</span>
                </div>
            </div>

            <!-- Station Switcher Tabs -->
            <div class="btn-group p-1 bg-black rounded-pill border border-secondary">
                <button class="btn btn-sm text-white-50 rounded-pill px-4 fw-bold station-tab active" onclick="switchStation(this, 'all')">Semua Station</button>
                <button class="btn btn-sm text-white-50 rounded-pill px-4 fw-bold station-tab" onclick="switchStation(this, 'kitchen')">Dapur (Kitchen)</button>
                <button class="btn btn-sm text-white-50 rounded-pill px-4 fw-bold station-tab" onclick="switchStation(this, 'bar')">Bar / Minuman</button>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span id="activeTicketCount" class="badge bg-primary fs-6 px-3 py-2 rounded-pill">0 Tiket Aktif</span>
                <span id="kdsClock" class="fw-bold text-white-50 fs-5">--:--:--</span>
            </div>
        </header>

        <!-- KDS Order Cards Grid -->
        <div id="ticketsGrid" class="row g-3">
            <div class="col-12 text-center py-5">
                <i class="fa fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                <h4 class="text-white-50">Memuat tiket pesanan dapur...</h4>
            </div>
        </div>
    </div>

    <script>
        let currentStation = 'all';

        function updateClock() {
            document.getElementById('kdsClock').textContent = new Date().toLocaleTimeString('id-ID');
        }
        setInterval(updateClock, 1000);
        updateClock();

        function switchStation(btn, station) {
            currentStation = station;
            document.querySelectorAll('.station-tab').forEach(el => el.classList.remove('active'));
            if (btn) btn.classList.add('active');
            fetchTickets();
        }

        async function fetchTickets() {
            try {
                const res = await fetch(`/api/kds/tickets/active?station=${currentStation}`);
                if (res.ok) {
                    const data = await res.json();
                    renderTickets(data.tickets || []);
                }
            } catch (e) {}
        }

        function renderTickets(tickets) {
            const grid = document.getElementById('ticketsGrid');
            document.getElementById('activeTicketCount').textContent = `${tickets.length} Tiket Aktif`;

            if (tickets.length === 0) {
                grid.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-check-circle fa-4x text-success opacity-50 mb-3"></i>
                        <h3 class="text-white fw-bold">Semua Pesanan Selesai!</h3>
                        <p class="text-white-50">Tidak ada tiket pesanan yang sedang menunggu di dapur.</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = tickets.map(t => {
                const isUrgent = t.is_urgent;
                const statusClass = `status-${t.status} ${isUrgent ? 'urgent' : ''}`;
                const stationBadge = t.station === 'bar' 
                    ? '<span class="badge bg-info text-dark fw-bold">BAR</span>' 
                    : '<span class="badge bg-warning text-dark fw-bold">KITCHEN</span>';

                let actionBtn = '';
                if (t.status === 'pending') {
                    actionBtn = `<button onclick="updateTicketStatus(${t.id}, 'cooking')" class="btn btn-primary w-100 fw-bold py-2"><i class="fa fa-utensils me-2"></i>Mulai Masak</button>`;
                } else if (t.status === 'cooking') {
                    actionBtn = `<button onclick="updateTicketStatus(${t.id}, 'ready')" class="btn btn-success w-100 fw-bold py-2"><i class="fa fa-check me-2"></i>Selesai (Ready)</button>`;
                } else if (t.status === 'ready') {
                    actionBtn = `<button onclick="updateTicketStatus(${t.id}, 'served')" class="btn btn-secondary w-100 fw-bold py-2"><i class="fa fa-concierge-bell me-2"></i>Sajikan (Served)</button>`;
                }

                const itemsHtml = (t.items || []).map(item => `
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom border-secondary">
                        <span class="fs-5 fw-bold text-white">${item.name || 'Menu'}</span>
                        <span class="badge bg-danger fs-6 px-3 py-1 rounded-pill">${item.qty || 1}x</span>
                    </li>
                `).join('');

                return `
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="kds-card ${statusClass} p-3 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
                                    <div>
                                        <h5 class="mb-0 fw-extrabold text-white">${t.ticket_number}</h5>
                                        <small class="text-white-50">${t.table_number} • ${t.customer_name}</small>
                                    </div>
                                    <div class="text-end">
                                        ${stationBadge}
                                        <div class="mt-1"><span class="badge ${isUrgent ? 'bg-danger' : 'bg-secondary'}">${t.elapsed_mins} mnt lalu</span></div>
                                    </div>
                                </div>

                                <ul class="list-unstyled mb-3">${itemsHtml}</ul>
                                ${t.notes ? `<div class="p-2 bg-black bg-opacity-50 rounded text-warning small mb-3"><i class="fa fa-sticky-note me-1"></i>${t.notes}</div>` : ''}
                            </div>

                            <div>${actionBtn}</div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        async function updateTicketStatus(id, newStatus) {
            try {
                const res = await fetch(`/api/kds/tickets/${id}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: newStatus })
                });
                if (res.ok) fetchTickets();
            } catch (e) {}
        }

        setInterval(fetchTickets, 3000);
        fetchTickets();
    </script>
</body>
</html>
