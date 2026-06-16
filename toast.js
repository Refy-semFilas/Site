function showAlert(message, type) {
    var existing = document.querySelector('.alert-overlay');
    if (existing) existing.remove();

    var icons = {
        success: '<svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#46CF6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        error: '<svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        warning: '<svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#ffc107" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
    };

    var overlay = document.createElement('div');
    overlay.className = 'alert-overlay';

    var modal = document.createElement('div');
    modal.className = 'alert-modal';

    var iconDiv = document.createElement('div');
    iconDiv.className = 'alert-icon';
    iconDiv.innerHTML = icons[type] || icons.info;
    modal.appendChild(iconDiv);

    var msg = document.createElement('p');
    msg.className = 'alert-message';
    msg.textContent = message;
    modal.appendChild(msg);

    var btn = document.createElement('button');
    btn.className = 'alert-btn';
    btn.textContent = 'OK';
    btn.onclick = function() { overlay.remove(); };
    modal.appendChild(btn);

    overlay.appendChild(modal);
    document.body.appendChild(overlay);

    if (type === 'success') {
        setTimeout(function() { overlay.remove(); }, 3000);
    }
}

(function() {
    var style = document.createElement('style');
    style.textContent =
        '.alert-overlay{' +
            'position:fixed;top:0;left:0;width:100%;height:100%;' +
            'background:rgba(0,0,0,0.45);display:flex;justify-content:center;' +
            'align-items:center;z-index:99999;animation:alertFadeIn 0.2s ease;' +
        '}' +
        '.alert-modal{' +
            'background:#fff;border-radius:20px;padding:40px 32px 28px;' +
            'max-width:360px;width:90%;text-align:center;' +
            'box-shadow:0 20px 60px rgba(0,0,0,0.3);animation:alertScaleIn 0.25s ease;' +
        '}' +
        '.alert-icon{margin-bottom:16px;}' +
        '.alert-message{' +
            'font-size:16px;color:#2d2d2d;margin:0 0 24px;line-height:1.5;' +
            'font-family:system-ui,-apple-system,sans-serif;' +
        '}' +
        '.alert-btn{' +
            'display:inline-block;padding:12px 40px;border:none;border-radius:50px;' +
            'font-weight:700;font-size:15px;cursor:pointer;color:#fff;' +
            'background:linear-gradient(135deg,#ff6200,#ff8533);' +
            'box-shadow:0 4px 14px rgba(255,98,0,0.3);' +
        '}' +
        '.alert-btn:hover{' +
            'transform:translateY(-2px);box-shadow:0 6px 20px rgba(255,98,0,0.4);' +
        '}' +
        '@keyframes alertFadeIn{from{opacity:0}to{opacity:1}}' +
        '@keyframes alertScaleIn{from{transform:scale(0.85);opacity:0}to{transform:scale(1);opacity:1}}';
    document.head.appendChild(style);
})();
