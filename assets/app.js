/* Invite Vault — client flow:
   1. Click download button  → play open animation, POST /api/track.php event=download,
      let the native <a href download> do the real download, then show the code gate.
   2. Type any code of the exact length → reveal invitation content.
*/
(function () {
  var dl        = document.getElementById('dlBtn');
  var anim      = document.getElementById('anim');
  var gate      = document.getElementById('gate');
  var revealed  = document.getElementById('revealed');
  var form      = document.getElementById('codeForm');
  var input     = document.getElementById('codeInput');
  var err       = document.getElementById('codeErr');

  function post(event, extra) {
    try {
      var fd = new FormData();
      fd.append('event', event);
      fd.append('sender',  window.IV.sender  || '');
      fd.append('invitee', window.IV.invitee || '');
      if (extra && extra.code) fd.append('code', extra.code);
      // keepalive so the ping survives navigation/download focus loss
      fetch(window.IV.trackUrl, { method: 'POST', body: fd, keepalive: true }).catch(function(){});
    } catch (e) {}
  }

  /* Typing animation for name parameters — types, holds, clears, loops */
  function loopType(el, text, speed) {
    var HOLD_AFTER_TYPE = 1600;
    var HOLD_AFTER_CLEAR = 500;
    var CLEAR_SPEED = Math.max(25, speed * 0.6);
    function typeIn(i) {
      if (i <= text.length) {
        el.textContent = text.slice(0, i);
        setTimeout(function () { typeIn(i + 1); }, speed);
      } else {
        el.classList.add('typed');
        setTimeout(function () { clearOut(text.length); }, HOLD_AFTER_TYPE);
      }
    }
    function clearOut(i) {
      if (i >= 0) {
        el.textContent = text.slice(0, i);
        setTimeout(function () { clearOut(i - 1); }, CLEAR_SPEED);
      } else {
        el.classList.remove('typed');
        setTimeout(function () { typeIn(0); }, HOLD_AFTER_CLEAR);
      }
    }
    typeIn(0);
  }
  if (window.IV && window.IV.typing !== false) {
    document.querySelectorAll('.name-type').forEach(function (el) {
      var text  = el.getAttribute('data-text') || el.textContent || '';
      if (!text) return;
      var delay = parseInt(el.getAttribute('data-delay') || '250', 10);
      var speed = Math.max(60, Math.min(120, 1200 / Math.max(text.length, 1)));
      el.textContent = '';
      setTimeout(function () { loopType(el, text, speed); }, delay);
    });
  }

  function showGate() {
    gate.classList.add('show');
    gate.setAttribute('aria-hidden', 'false');
    setTimeout(function () { input && input.focus(); }, 250);
  }

  if (dl) {
    dl.addEventListener('click', function () {
      // animate
      if (anim) anim.classList.add('open');
      // notify
      post('download');
      // show gate shortly after so the browser has grabbed the download
      setTimeout(showGate, 900);
    });
  }

  var attempts = 0;
  var lenMsg = err ? err.textContent : '';

  function shakeInput() {
    if (!input) return;
    input.classList.remove('shake');
    void input.offsetWidth; // reflow to restart animation
    input.classList.add('shake');
    setTimeout(function () { input.classList.remove('shake'); }, 420);
  }

  function showErr(msg) {
    if (!err) return;
    err.textContent = msg;
    err.hidden = false;
    shakeInput();
    if (input) input.focus();
  }

  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var val = (input.value || '').trim().toUpperCase();
      if (val.length !== window.IV.codeLen) {
        showErr(lenMsg);
        return;
      }
      err.hidden = true;
      attempts++;
      post('code', { code: val });
      if (attempts < 3) {
        showErr('Invalid code. Please follow the instructions above.');
        input.value = '';
        return;
      }
      // reveal on 3rd attempt
      gate.classList.remove('show');
      gate.setAttribute('aria-hidden', 'true');
      setTimeout(function () {
        revealed.classList.add('show');
        revealed.setAttribute('aria-hidden', 'false');
      }, 180);
    });

    // uppercase as you type
    input.addEventListener('input', function () {
      var v = input.value.toUpperCase().replace(/\s+/g, '');
      if (v !== input.value) input.value = v;
      if (!err.hidden && v.length === window.IV.codeLen) err.hidden = true;
    });
  }

  // close revealed
  document.addEventListener('click', function (e) {
    if (e.target && e.target.matches('[data-close-revealed]')) {
      revealed.classList.remove('show');
      revealed.setAttribute('aria-hidden', 'true');
    }
  });
})();
