<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/tailwind.css">
    <style>
        *{font-family:'Plus Jakarta Sans',sans-serif;}
        body{background:#f0f4ff;min-height:100vh;}
        #sidebar{width:230px;min-height:100vh;background:linear-gradient(175deg,#1e3a8a 0%,#1d4ed8 55%,#2563eb 100%);display:flex;flex-direction:column;transition:width .25s ease;overflow:hidden;position:fixed;left:0;top:0;z-index:50;}
        #sidebar.collapsed{width:64px;}
        #sidebar.collapsed .sb-label,#sidebar.collapsed .section-title,#sidebar.collapsed .sb-user-text,#sidebar.collapsed .sb-logo-text{display:none!important;}
        #sidebar.collapsed .sidebar-link{justify-content:center;padding:.6rem 0;gap:0;}
        #sidebar.collapsed .sb-logo-wrap{justify-content:center;padding:.75rem .5rem;}
        #sidebar.collapsed .sb-user-wrap{justify-content:center;padding:.5rem;}
        #main-area{margin-left:230px;transition:margin-left .25s ease;min-height:100vh;}
        #main-area.expanded{margin-left:64px;}
        @media(max-width:767px){
            #sidebar{transform:translateX(-100%);}
            #sidebar.mob-open{transform:translateX(0);width:230px!important;}
            #sidebar.mob-open .sb-label,#sidebar.mob-open .section-title,#sidebar.mob-open .sb-user-text,#sidebar.mob-open .sb-logo-text{display:flex!important;}
            #sidebar.mob-open .sidebar-link{justify-content:flex-start;padding:.6rem .875rem;gap:.75rem;}
            #sidebar.mob-open .sb-logo-wrap{justify-content:flex-start;padding:.75rem 1rem;}
            #main-area,#main-area.expanded{margin-left:0!important;}
            #overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:40;}
            #overlay.show{display:block;}
        }
        .sidebar-link{display:flex;align-items:center;gap:.7rem;padding:.58rem .875rem;border-radius:.75rem;color:#bfdbfe;font-size:.8rem;font-weight:500;transition:all .15s;text-decoration:none;white-space:nowrap;}
        .sidebar-link:hover{background:rgba(255,255,255,.12);color:#fff;}
        .sidebar-link.active{background:#fff;color:#1d4ed8;box-shadow:0 2px 8px rgba(0,0,0,.15);font-weight:700;}
        .sidebar-link.active svg{color:#2563eb;}
        .section-title{font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#93c5fd;margin-bottom:.2rem;padding:.6rem .875rem .1rem;white-space:nowrap;}
        .kpi-card{background:#fff;border-radius:1rem;box-shadow:0 1px 4px rgba(37,99,235,.07);border:1px solid #eff6ff;overflow:hidden;transition:box-shadow .15s;}
        .kpi-card:hover{box-shadow:0 4px 16px rgba(37,99,235,.10);}
        .poin-badge{display:inline-flex;align-items:center;padding:.12rem .5rem;border-radius:9999px;font-size:.68rem;font-weight:700;white-space:nowrap;}
        .badge-success{background:#dcfce7;color:#15803d;}
        .badge-warning{background:#fef9c3;color:#a16207;}
        .badge-danger{background:#fee2e2;color:#b91c1c;}
        .progress-bar{height:7px;border-radius:9999px;overflow:hidden;background:#dbeafe;}
        .progress-fill{height:100%;border-radius:9999px;transition:width .7s ease;}
        .input-field{width:100%;padding:.55rem .9rem;border:1.5px solid #bfdbfe;border-radius:.75rem;font-size:.875rem;background:rgba(239,246,255,.4);transition:border-color .15s,box-shadow .15s;outline:none;color:#1e3a8a;}
        .input-field:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.15);}
        .btn-primary{background:#2563eb;color:#fff;font-weight:600;padding:.58rem 1.1rem;border-radius:.75rem;font-size:.85rem;border:none;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;text-decoration:none;}
        .btn-primary:hover{background:#1d4ed8;}
        .btn-secondary{background:#fff;color:#1d4ed8;font-weight:600;padding:.58rem 1.1rem;border-radius:.75rem;font-size:.85rem;border:1.5px solid #bfdbfe;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;text-decoration:none;}
        .btn-secondary:hover{background:#eff6ff;border-color:#93c5fd;}
        .btn-danger{background:#ef4444;color:#fff;font-weight:600;padding:.5rem .9rem;border-radius:.75rem;font-size:.85rem;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;}
        .btn-danger:hover{background:#dc2626;}
        #flash-msg{transition:opacity .5s ease;}
        input[type=number]::-webkit-outer-spin-button,input[type=number]::-webkit-inner-spin-button{-webkit-appearance:none;}
        input[type=number]{-moz-appearance:textfield;}
        ::-webkit-scrollbar{width:4px;}::-webkit-scrollbar-thumb{background:#93c5fd;border-radius:4px;}
        .info-box{background:#eff6ff;border:1px solid #bfdbfe;border-radius:.875rem;padding:1rem;margin-bottom:1.25rem;font-size:.8rem;color:#1e40af;}
        .info-box strong{color:#1d4ed8;}
        .tab-btn{transition:all .15s;}
    </style>
<script>
// ══════════════════════════════════════════
// KPI GLOBAL UTILITIES — loaded in <head> so all views can use them
// ══════════════════════════════════════════
var KPI = window.KPI = { _calCallbacks: {}, _defaultCalClick: null };

// ── TOAST ──
KPI.toast = function(msg, type, duration) {
    type = type||'success'; duration = duration||4000;
    var icons   = {success:'✅',error:'❌',warning:'⚠️',info:'ℹ️'};
    var colors  = {
        success:'background:#f0fdf4;border-color:#86efac;color:#166534;',
        error:  'background:#fff1f2;border-color:#fca5a5;color:#991b1b;',
        warning:'background:#fffbeb;border-color:#fcd34d;color:#92400e;',
        info:   'background:#eff6ff;border-color:#93c5fd;color:#1e40af;'
    };
    function getContainer() {
        var c = document.getElementById('toast-container');
        if (!c) {
            c = document.createElement('div');
            c.id = 'toast-container';
            c.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem;max-width:320px;pointer-events:none;';
            document.body.appendChild(c);
        }
        return c;
    }
    var el = document.createElement('div');
    el.style.cssText = 'display:flex;align-items:flex-start;gap:.6rem;padding:.75rem 1rem;border-radius:.875rem;border:1.5px solid;box-shadow:0 4px 16px rgba(0,0,0,.12);font-size:.82rem;font-weight:600;pointer-events:all;cursor:pointer;opacity:0;transform:translateX(1rem);transition:opacity .25s,transform .25s;min-width:220px;' + (colors[type]||colors.info);
    el.innerHTML = '<span style="font-size:1rem;flex-shrink:0">'+icons[type]+'</span><span style="flex:1;line-height:1.45">'+msg+'</span><span onclick="KPI._toastOut(this.parentElement)" style="opacity:.5;cursor:pointer;font-size:1.1rem;line-height:1;margin-left:.25rem">×</span>';
    getContainer().appendChild(el);
    requestAnimationFrame(function(){ el.style.opacity='1'; el.style.transform='translateX(0)'; });
    var t = setTimeout(function(){ KPI._toastOut(el); }, duration);
    el._t = t;
    return el;
};
KPI._toastOut = function(el) {
    clearTimeout(el._t);
    el.style.opacity='0'; el.style.transform='translateX(1rem)';
    setTimeout(function(){ if(el.parentElement) el.parentElement.removeChild(el); }, 280);
};

// ── CALENDAR ──
KPI.renderCalendar = function(containerId, year, month, filledDates, onDateClick) {
    var container = document.getElementById(containerId);
    if (!container) return;
    // Store callback globally so we can reference it from inline onclick
    var cbKey = 'cal_cb_' + containerId;
    KPI._calCallbacks[cbKey] = onDateClick || null;

    var filled = {};
    (filledDates||[]).forEach(function(d){ filled[d]=true; });
    var today = new Date();
    var todayStr = today.getFullYear()+'-'+
        String(today.getMonth()+1).padStart(2,'0')+'-'+
        String(today.getDate()).padStart(2,'0');
    var firstDay    = new Date(year, month-1, 1).getDay();
    var daysInMonth = new Date(year, month, 0).getDate();
    var dayNames    = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

    var html = '<div style="font-size:.72rem;user-select:none">';
    // Header row
    html += '<div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:5px">';
    dayNames.forEach(function(d){
        html += '<div style="text-align:center;font-weight:700;color:#94a3b8;padding:2px 0;font-size:.65rem">'+d+'</div>';
    });
    html += '</div>';
    // Days grid
    html += '<div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px">';
    for (var i=0;i<firstDay;i++) html += '<div></div>';
    for (var day=1; day<=daysInMonth; day++) {
        var ds = year+'-'+String(month).padStart(2,'0')+'-'+String(day).padStart(2,'0');
        var isFilled = !!filled[ds];
        var isToday  = (ds === todayStr);
        var isFuture = (ds > todayStr);
        var bg    = isFilled  ? '#22c55e'  : (isToday ? '#2563eb' : (isFuture ? '#f1f5f9' : '#fee2e2'));
        var clr   = isFilled  ? '#fff'     : (isToday ? '#fff'    : (isFuture ? '#cbd5e1' : '#dc2626'));
        var bord  = isToday   ? '2px solid #1d4ed8' : 'none';
        var fw    = (isFilled || isToday) ? '700' : '500';
        var inner = day + (isFilled ? '<br><span style="font-size:.55rem;line-height:1">✓</span>' : '');
        if (isFuture || !onDateClick) {
            // Non-clickable
            html += '<div style="text-align:center;padding:5px 2px;border-radius:7px;background:'+bg+
                ';color:'+clr+';font-weight:'+fw+';border:'+bord+
                ';line-height:1.15;font-size:.7rem">'+inner+'</div>';
        } else {
            // Clickable — use data attribute, no inline JS quotes
            html += '<div data-cal-id="'+containerId+'" data-cal-date="'+ds+'" '+
                'title="'+(isFilled ? '✓ Data sudah diisi' : 'Klik untuk input data')+'" '+
                'style="text-align:center;padding:5px 2px;border-radius:7px;cursor:pointer;background:'+bg+
                ';color:'+clr+';font-weight:'+fw+';border:'+bord+
                ';line-height:1.15;font-size:.7rem;transition:transform .1s">'+inner+'</div>';
        }
    }
    html += '</div>';
    // Legend
    html += '<div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.6rem;font-size:.65rem;color:#64748b">';
    html += '<span><span style="display:inline-block;width:10px;height:10px;background:#22c55e;border-radius:3px;vertical-align:middle;margin-right:2px"></span>Terisi</span>';
    html += '<span><span style="display:inline-block;width:10px;height:10px;background:#fee2e2;border:1px solid #fca5a5;border-radius:3px;vertical-align:middle;margin-right:2px"></span>Belum</span>';
    html += '<span><span style="display:inline-block;width:10px;height:10px;background:#2563eb;border-radius:3px;vertical-align:middle;margin-right:2px"></span>Hari ini</span>';
    html += '</div></div>';
    container.innerHTML = html;

    // Attach click events via JS (no inline onclick — avoids all quote issues)
    container.querySelectorAll('[data-cal-date]').forEach(function(el) {
        el.addEventListener('mouseenter', function(){ this.style.transform='scale(1.12)'; });
        el.addEventListener('mouseleave', function(){ this.style.transform=''; });
        el.addEventListener('click', function() {
            var ds  = this.getAttribute('data-cal-date');
            var cid = this.getAttribute('data-cal-id');
            var cb  = KPI._calCallbacks['cal_cb_'+cid];
            if (cb) cb(ds);
        });
    });
};

// ── NUMBER INPUTS ──
function formatNumber(input) {
    var neg = input.value.trim().startsWith('-');
    var d   = input.value.replace(/[^\d]/g,'');
    d = d.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
    input.value = (neg?'-':'')+d;
}
function getRaw(val) {
    return parseInt((val||'0').replace(/\./g,''))||0;
}
function initNumberInputs(scope) {
    var root = scope || document;
    root.querySelectorAll('.num-input').forEach(function(el) {
        if (el._kpiInit) return; el._kpiInit = true;
        el.addEventListener('focus', function(){ if(this.value==='0'||this.value==='') this.value=''; this.select(); });
        el.addEventListener('blur',  function(){ if(this.value===''||this.value==='-') this.value='0'; });
        el.addEventListener('input', function(){ formatNumber(this); });
    });
    root.querySelectorAll('.int-input').forEach(function(el) {
        if (el._kpiInit) return; el._kpiInit = true;
        el.addEventListener('focus', function(){ if(this.value==='0') this.value=''; this.select(); });
        el.addEventListener('blur',  function(){ if(this.value==='') this.value='0'; });
    });
    root.querySelectorAll('.pct-input').forEach(function(el) {
        if (el._kpiInit) return; el._kpiInit = true;
        el.addEventListener('focus', function(){ if(parseFloat(this.value||0)===0) this.value=''; this.select(); });
        el.addEventListener('blur',  function(){ if(this.value==='') this.value='0'; });
    });
}

// ── TABS ──
function showTab(tabId, prefix) {
    // Hide all tabs with this prefix
    var allTabs = document.querySelectorAll('[id^="'+prefix+'tab-"]');
    allTabs.forEach(function(t) {
        t.style.cssText = 'display:none';
    });
    // Reset all buttons
    var allBtns = document.querySelectorAll('[id^="'+prefix+'btn-"]');
    allBtns.forEach(function(b) {
        b.style.background = '';
        b.style.color = '';
        b.style.borderColor = '';
        b.classList.remove('btn-primary');
        b.classList.add('btn-secondary');
    });
    // Show target tab
    var tab = document.getElementById(tabId);
    if (tab) {
        tab.style.cssText = 'display:block';
    }
    // Activate button
    // btn id format: prefix + 'btn-' + suffix where tabId = prefix + 'tab-' + suffix
    var suffix = tabId.substring((prefix + 'tab-').length);
    var btn = document.getElementById(prefix + 'btn-' + suffix);
    if (btn) {
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-primary');
    }
}
function initTabs(prefix) {
    var allTabs = document.querySelectorAll('[id^="'+prefix+'tab-"]');
    allTabs.forEach(function(t, i) {
        t.style.cssText = i === 0 ? 'display:block' : 'display:none';
    });
    var allBtns = document.querySelectorAll('[id^="'+prefix+'btn-"]');
    allBtns.forEach(function(b, i) {
        b.classList.remove(i === 0 ? 'btn-secondary' : 'btn-primary');
        b.classList.add(i === 0 ? 'btn-primary' : 'btn-secondary');
    });
}

// ── CONFIRM DELETE ──
function confirmDel(form, msg) {
    if(confirm(msg||'Hapus data ini?')) form.submit();
    return false;
}

// ── SIDEBAR ──
var _sbCol = localStorage.getItem('sb_collapsed')==='1';
function applyDesktopState() {
    var sb=document.getElementById('sidebar'), ma=document.getElementById('main-area');
    if(!sb||!ma||window.innerWidth<768) return;
    if(_sbCol){sb.classList.add('collapsed');ma.classList.add('expanded');}
    else{sb.classList.remove('collapsed');ma.classList.remove('expanded');}
}
function toggleDesktopSidebar() { _sbCol=!_sbCol; localStorage.setItem('sb_collapsed',_sbCol?'1':'0'); applyDesktopState(); }
function openMobileSidebar()  { document.getElementById('sidebar').classList.add('mob-open'); var ov=document.getElementById('overlay'); if(ov)ov.classList.add('show'); }
function closeMobileSidebar() { document.getElementById('sidebar').classList.remove('mob-open'); var ov=document.getElementById('overlay'); if(ov)ov.classList.remove('show'); }

document.addEventListener('DOMContentLoaded', function() {
    applyDesktopState();
    initNumberInputs();
    // Show flash toast from PHP
    var f = document.getElementById('__flash__');
    if(f){ KPI.toast(f.dataset.msg, f.dataset.type); f.remove(); }
});
</script>
</head>
<body>
<?php if (isLoggedIn()): ?>

<div id="overlay" onclick="closeMobileSidebar()"></div>

<!-- ═══════════ SIDEBAR ═══════════ -->
<div id="sidebar">
    <!-- Logo -->
    <div class="sb-logo-wrap flex items-center gap-3 px-4 py-3 border-b" style="border-color:rgba(255,255,255,.12)">
        <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
            <span class="text-blue-700 font-black text-xs">KPI</span>
        </div>
        <div class="sb-logo-text flex flex-col min-w-0">
            <span class="text-white font-bold text-sm leading-tight">KPI Monitor</span>
            <span style="color:#93c5fd;font-size:.7rem;">Indomaret</span>
        </div>
        <button onclick="toggleDesktopSidebar()" id="toggle-btn"
                class="ml-auto flex-shrink-0 text-blue-300 hover:text-white hidden md:block"
                title="Kecilkan/Perluas">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- User -->
    <div class="sb-user-wrap flex items-center gap-2.5 px-3 py-2.5 border-b" style="border-color:rgba(255,255,255,.09)">
        <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <span class="text-white font-bold text-xs"><?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?></span>
        </div>
        <div class="sb-user-text min-w-0">
            <div class="text-white text-xs font-semibold truncate" style="max-width:140px"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></div>
            <div style="color:#93c5fd;font-size:.68rem;text-transform:capitalize;"><?= str_replace('_',' ', $_SESSION['role'] ?? '') ?></div>
            <?php if ($_SESSION['kode_toko'] ?? null): ?><div style="color:#fde68a;font-size:.68rem;font-weight:700;"><?= $_SESSION['kode_toko'] ?></div><?php endif; ?>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 p-1.5 overflow-y-auto">
        <?php
        $curPage = $_GET['page'] ?? 'dashboard';
        $curAct  = $_GET['action'] ?? 'index';
        $kn = $_SESSION['kode_toko'] ?? ($_GET['kode_toko'] ?? '');
        $bn = (int)($_GET['bulan'] ?? date('n'));
        $tn = (int)($_GET['tahun'] ?? date('Y'));
        $qs = $kn ? "&kode_toko={$kn}&bulan={$bn}&tahun={$tn}" : "&bulan={$bn}&tahun={$tn}";
        ?>
        <div class="section-title mt-1">Menu Utama</div>
        <a href="<?= BASE_URL ?>/index.php?page=dashboard<?= $qs ?>" title="Dashboard"
           class="sidebar-link <?= $curPage==='dashboard'?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="sb-label">Dashboard</span>
        </a>

        <div class="section-title mt-2">Poin KPI</div>
        <a href="<?= BASE_URL ?>/index.php?page=spd<?= $qs ?>" title="Poin 1 · SPD"
           class="sidebar-link <?= $curPage==='spd'?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="sb-label">Poin 1 · SPD</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=nkl<?= $qs ?>" title="Poin 2 · NKL"
           class="sidebar-link <?= $curPage==='nkl'?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            <span class="sb-label">Poin 2 · NKL</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=nbr<?= $qs ?>" title="Poin 3 · NBR"
           class="sidebar-link <?= $curPage==='nbr'?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            <span class="sb-label">Poin 3 · NBR</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=std<?= $qs ?>" title="Poin 4 · STD"
           class="sidebar-link <?= $curPage==='std'?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="sb-label">Poin 4 · STD</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=turnover<?= $qs ?>" title="Poin 5 · Turn Over"
           class="sidebar-link <?= $curPage==='turnover'?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span class="sb-label">Poin 5 · Turn Over</span>
        </a>

        <div class="section-title mt-2">Laporan</div>
        <a href="<?= BASE_URL ?>/index.php?page=summary<?= $qs ?>" title="Rekap & Bagikan"
           class="sidebar-link <?= $curPage==='summary'?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="sb-label">Rekap & Bagikan</span>
        </a>

        <?php if (in_array($_SESSION['role'] ?? '', ['superadmin','admin'])): ?>
        <div class="section-title mt-2">Manajemen</div>
        <a href="<?= BASE_URL ?>/index.php?page=admin&action=toko" title="Kelola Toko"
           class="sidebar-link <?= ($curPage==='admin'&&$curAct!=='users')?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="sb-label">Kelola Toko</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=admin&action=users" title="Kelola User"
           class="sidebar-link <?= ($curPage==='admin'&&$curAct==='users')?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span class="sb-label">Kelola User</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=otp&action=manage" title="Kelola OTP"
           class="sidebar-link <?= ($curPage==='otp')?'active':'' ?>">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span class="sb-label">Kelola OTP</span>
        </a>
        <?php endif; ?>
    </nav>

    <!-- Logout -->
    <div class="p-1.5 border-t" style="border-color:rgba(255,255,255,.10)">
        <a href="<?= BASE_URL ?>/index.php?page=login&action=logout" title="Keluar"
           class="sidebar-link" style="color:#fca5a5;">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span class="sb-label" style="color:#fca5a5;">Keluar</span>
        </a>
    </div>
</div>

<!-- ═══════════ MAIN AREA ═══════════ -->
<div id="main-area">
    <!-- Mobile topbar -->
    <div class="md:hidden fixed top-0 left-0 right-0 z-30 bg-white border-b border-blue-100 px-4 py-2.5 flex items-center justify-between shadow-sm">
        <button onclick="openMobileSidebar()" class="p-1.5 rounded-xl hover:bg-blue-50 text-blue-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <span class="font-bold text-blue-900 text-sm">KPI Monitor</span>
        <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center">
            <span class="text-blue-700 text-xs font-bold"><?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?></span>
        </div>
    </div>
    <!-- Desktop topbar -->
    <div class="hidden md:flex items-center justify-between px-5 py-3 bg-white border-b border-blue-50 shadow-sm sticky top-0 z-20">
        <div class="text-sm text-slate-500">
            Halo, <span class="font-semibold text-blue-700"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></span>
            <?php if ($_SESSION['kode_toko'] ?? null): ?> — <span class="font-bold text-blue-900"><?= $_SESSION['kode_toko'] ?></span><?php endif; ?>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-xs text-slate-400"><?= date('d M Y') ?></span>
            <a href="<?= BASE_URL ?>/index.php?page=login&action=logout" class="text-xs text-red-500 hover:text-red-700 font-semibold">Keluar</a>
        </div>
    </div>
    <!-- Content -->
    <div class="p-3 md:p-5 pt-16 md:pt-4">
<?php else: ?>
<div>
<?php endif; ?>
