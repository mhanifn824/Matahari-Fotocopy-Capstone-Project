<!-- Topbar Right Component: Live Clock, Global Real-Time Notifications, Profile Avatar -->
<div class="flex items-center gap-3 sm:gap-4 shrink-0">

    <!-- 1. Live Clock & Date -->
    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 bg-slate-50/90 border border-slate-200/80 px-3 py-1.5 rounded-full font-mono shadow-2xs">
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span id="liveClockDisplay" class="admin-live-clock-target">{{ date('H:i') }} WIB • {{ date('d M') }}</span>
    </div>

    <!-- 2. Global Centralized Notification Bell with Dropdown -->
    <div class="relative" id="global-admin-notif-container">
        <button 
            type="button" 
            id="global-notif-btn"
            onclick="toggleGlobalNotificationDropdown()" 
            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition relative active:scale-95"
            title="Notifikasi Global Admin"
            aria-label="Notifikasi Admin"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span id="global-notif-badge" class="absolute top-0 right-0 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white hidden"></span>
        </button>

        <!-- Dropdown Box -->
        <div 
            id="global-notif-dropdown" 
            class="hidden absolute right-0 mt-2.5 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-200 p-3.5 z-50 text-xs space-y-2.5 animate-in fade-in zoom-in-95 duration-100"
        >
            <!-- Dropdown Header -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="font-extrabold text-slate-900 text-xs sm:text-sm">Notifikasi Terpusat</span>
                    <span id="global-notif-count-pill" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 font-mono hidden">0 Baru</span>
                </div>
                <button 
                    type="button" 
                    onclick="markAllGlobalNotificationsRead()" 
                    class="text-[11px] font-bold text-amber-600 hover:text-amber-700 hover:underline transition"
                >
                    Tandai Semua Dibaca
                </button>
            </div>

            <!-- Notification Items List -->
            <div id="global-notif-list" class="max-h-80 overflow-y-auto space-y-1.5 pr-1 divide-y divide-slate-100">
                <div class="py-6 text-center text-slate-400 font-medium">
                    Memuat notifikasi...
                </div>
            </div>

            <!-- Dropdown Footer -->
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                <span>Sinkron Seluruh Menu</span>
                <span class="flex items-center gap-1 text-emerald-600 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Aktif (5s Polling)
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Profile Avatar Silhouette (Consistent Amber Circle with FA) -->
    <div 
        class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-xs ring-2 ring-amber-200/80 shadow-xs select-none cursor-default shrink-0" 
        title="{{ Auth::user()->nama ?? 'Feri Anto (Admin)' }}"
    >
        FA
    </div>

</div>

<!-- Real-time Unified Clock & Notification Script -->
<script>
(function() {
    // 1. Unified Real-time Clock Mechanism
    function updateAdminLiveClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const day = now.getDate();
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const formatted = `${hours}:${minutes} WIB • ${day} ${months[now.getMonth()]}`;

        const targets = document.querySelectorAll('.admin-live-clock-target, #liveClockDisplay, #live-clock');
        targets.forEach(el => {
            el.textContent = formatted;
        });
    }

    setInterval(updateAdminLiveClock, 1000);
    updateAdminLiveClock();

    // 2. Global Dropdown Toggle
    window.toggleGlobalNotificationDropdown = function() {
        const dropdown = document.getElementById('global-notif-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    };

    // Close on click outside
    document.addEventListener('click', function(e) {
        const container = document.getElementById('global-admin-notif-container');
        const dropdown = document.getElementById('global-notif-dropdown');
        if (container && dropdown && !container.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // 3. Centralized Notifications Logic
    let currentNotificationList = [];

    function getLocalReadIds() {
        try {
            return JSON.parse(localStorage.getItem('admin_read_notification_ids') || '[]');
        } catch (e) {
            return [];
        }
    }

    function saveLocalReadIds(ids) {
        try {
            localStorage.setItem('admin_read_notification_ids', JSON.stringify(ids));
        } catch (e) {}
    }

    window.handleNotificationClick = async function(id, targetUrl) {
        // Mark as read locally immediately
        const localReads = getLocalReadIds();
        if (!localReads.includes(id)) {
            localReads.push(id);
            saveLocalReadIds(localReads);
        }

        // Sync with backend session
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            await fetch("{{ route('admin.notifications.mark-read') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf || ''
                },
                body: JSON.stringify({ id: id })
            });
        } catch (err) {
            console.error('Error marking notification as read:', err);
        }

        // Navigate to the target page
        window.location.href = targetUrl;
    };

    window.markAllGlobalNotificationsRead = async function() {
        const ids = currentNotificationList.map(n => n.id);
        const localReads = Array.from(new Set([...getLocalReadIds(), ...ids]));
        saveLocalReadIds(localReads);

        // Update UI immediately
        const badge = document.getElementById('global-notif-badge');
        const countPill = document.getElementById('global-notif-count-pill');
        if (badge) badge.classList.add('hidden');
        if (countPill) countPill.classList.add('hidden');

        renderNotifications(currentNotificationList.map(n => ({ ...n, is_read: true })));

        // Sync with backend
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            await fetch("{{ route('admin.notifications.mark-all-read') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf || ''
                },
                body: JSON.stringify({ ids: ids })
            });
        } catch (err) {
            console.error('Error marking all notifications read:', err);
        }
    };

    function renderNotifications(items) {
        const listContainer = document.getElementById('global-notif-list');
        const badge = document.getElementById('global-notif-badge');
        const countPill = document.getElementById('global-notif-count-pill');
        if (!listContainer) return;

        const localReads = getLocalReadIds();

        // Calculate unread
        let unread = 0;
        items.forEach(item => {
            if (!item.is_read && !localReads.includes(item.id)) {
                unread++;
            }
        });

        if (unread > 0) {
            if (badge) badge.classList.remove('hidden');
            if (countPill) {
                countPill.textContent = `${unread} Baru`;
                countPill.classList.remove('hidden');
            }
        } else {
            if (badge) badge.classList.add('hidden');
            if (countPill) countPill.classList.add('hidden');
        }

        if (!items || items.length === 0) {
            listContainer.innerHTML = `
                <div class="py-6 text-center text-slate-400 font-medium">
                    <p class="text-xs">Belum ada aktivitas baru</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Notifikasi pesanan, stok, dan member akan muncul di sini.</p>
                </div>
            `;
            return;
        }

        let html = '';
        items.forEach(item => {
            const isRead = item.is_read || localReads.includes(item.id);
            const readClass = isRead 
                ? 'opacity-70 hover:opacity-100 bg-white hover:bg-slate-50' 
                : 'bg-amber-50/50 hover:bg-amber-50/80 border border-amber-200/60 font-semibold';
            
            // Icon per type
            let iconSvg = '';
            if (item.type === 'pesanan') {
                iconSvg = `<span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 font-bold text-xs">🛍️</span>`;
            } else if (item.type === 'stok') {
                iconSvg = `<span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 font-bold text-xs">⚠️</span>`;
            } else {
                iconSvg = `<span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-bold text-xs">👤</span>`;
            }

            html += `
                <div 
                    onclick="handleNotificationClick('${item.id}', '${item.url}')" 
                    class="p-2.5 rounded-xl cursor-pointer transition flex items-start gap-2.5 ${readClass}"
                >
                    ${iconSvg}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase tracking-wider ${item.badge_color}">
                                ${item.badge}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">${item.time}</span>
                        </div>
                        <h4 class="text-xs text-slate-900 font-bold mt-1 leading-snug">${item.title}</h4>
                        <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-2 leading-relaxed">${item.message}</p>
                    </div>
                    ${!isRead ? '<span class="w-2 h-2 rounded-full bg-amber-500 shrink-0 mt-1"></span>' : ''}
                </div>
            `;
        });

        listContainer.innerHTML = html;
    }

    async function fetchGlobalNotifications() {
        try {
            const res = await fetch("{{ route('admin.notifications') }}", {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) return;

            const data = await res.json();
            if (data.status === 'success' && Array.isArray(data.notifications)) {
                currentNotificationList = data.notifications;
                
                // Merge backend read_ids with local
                if (Array.isArray(data.read_ids)) {
                    const merged = Array.from(new Set([...getLocalReadIds(), ...data.read_ids]));
                    saveLocalReadIds(merged);
                }

                renderNotifications(data.notifications);
            }
        } catch (err) {
            // Silently handle offline/network jitter
        }
    }

    // Initial fetch
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fetchGlobalNotifications);
    } else {
        fetchGlobalNotifications();
    }

    // Polling every 5 seconds
    setInterval(fetchGlobalNotifications, 5000);
})();
</script>

