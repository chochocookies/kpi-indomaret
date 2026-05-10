    </div><!-- end content -->
</div><!-- end main-area -->

<script>
// ── Sidebar Desktop collapse ──
let sidebarCollapsed = localStorage.getItem('sb_collapsed') === '1';
function applyDesktopState() {
    const sb = document.getElementById('sidebar');
    const ma = document.getElementById('main-area');
    if (!sb || !ma || window.innerWidth < 768) return;
    if (sidebarCollapsed) { sb.classList.add('collapsed'); ma.classList.add('expanded'); }
    else { sb.classList.remove('collapsed'); ma.classList.remove('expanded'); }
}
function toggleDesktopSidebar() {
    sidebarCollapsed = !sidebarCollapsed;
    localStorage.setItem('sb_collapsed', sidebarCollapsed ? '1' : '0');
    applyDesktopState();
}
function openMobileSidebar() {
    document.getElementById('sidebar').classList.add('mob-open');
    var ov = document.getElementById('overlay');
    if (ov) ov.classList.add('show');
}
function closeMobileSidebar() {
    document.getElementById('sidebar').classList.remove('mob-open');
    var ov = document.getElementById('overlay');
    if (ov) ov.classList.remove('show');
}
document.addEventListener('DOMContentLoaded', function () {
    applyDesktopState();
    var flash = document.getElementById('flash-msg');
    if (flash) {
        setTimeout(function(){ flash.style.opacity='0'; }, 3000);
        setTimeout(function(){ flash.style.display='none'; }, 3600);
    }
});
// Format number input
function formatNumber(input) {
    var neg = input.value.trim().startsWith('-');
    var digits = input.value.replace(/[^\d]/g,'');
    digits = digits.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
    input.value = (neg ? '-' : '') + digits;
}
function getRaw(val) {
    return parseInt((val||'0').replace(/\./g,''))||0;
}
// Tab switcher
function showTab(id, prefix) {
    var tabs = document.querySelectorAll('[id^="'+prefix+'tab-"]');
    var btns = document.querySelectorAll('[id^="'+prefix+'btn-"]');
    tabs.forEach(function(t){ t.classList.add('hidden'); });
    btns.forEach(function(b){ b.className = b.className.replace('btn-primary','btn-secondary'); });
    var tab = document.getElementById(id);
    if (tab) tab.classList.remove('hidden');
    var btnId = id.replace('tab-','btn-');
    var btn = document.getElementById(btnId);
    if (btn) btn.className = btn.className.replace('btn-secondary','btn-primary');
}
</script>
</body>
</html>
