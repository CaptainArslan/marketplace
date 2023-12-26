self.addEventListener("install", (event) =>
  event.waitUntil(self.skipWaiting())
);

self.addEventListener("activate", (event) => {
  console.log("activated");
});

let oldcount = 0;
let url = "";
function getsound() {
  fetch(url)
    .then((t) => {
      return t.text();
    })
    .then((t) => {
        t = parseInt(t);
      if (t) {
        if (t > oldcount) {
          oldcount = t;
          postMessage(
            {
              key: "notification_count",
              result: t,
            }
           
          );
        }
      }
      setTimeout(getsound, 2000);
    })
    .catch((x) => {});
}

onmessage = function (e) {
  let data = e.data;
  if (typeof data == "object") {
    if (data.key == "init") {
      url = data.url;
      getsound();
    }
  }
};
