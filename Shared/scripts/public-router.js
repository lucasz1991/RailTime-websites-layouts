(function () {
  const body = document.body;
  const publicId = body && body.dataset ? body.dataset.publicLayout : "";
  const publicBase = body && body.dataset ? body.dataset.publicBase : "";
  if (!publicId || !publicBase) return;

  const routeMap = {
    "index.html": "",
    "index.php": "",
    "leistungen.html": "leistungen",
    "leistungen.php": "leistungen",
    "ueber-uns.html": "ueber-uns",
    "ueber-uns.php": "ueber-uns",
    "kontakt.html": "kontakt",
    "kontakt.php": "kontakt",
    "impressum.html": "impressum",
    "impressum.php": "impressum",
    "datenschutz.html": "datenschutz",
    "datenschutz.php": "datenschutz"
  };

  document.querySelectorAll("a[href]").forEach((link) => {
    const raw = link.getAttribute("href");
    if (!raw || raw.charAt(0) === "#" || /^(?:[a-z]+:|\/\/|\/)/i.test(raw)) return;

    const parts = raw.split("#");
    const normalized = parts[0].replace(/^\.\//, "");
    if (!(normalized in routeMap)) return;

    const target = routeMap[normalized];
    link.href = target ? publicBase + "/" + target : publicBase + "/";
    if (parts[1]) link.href += "#" + parts[1];
  });
})();

