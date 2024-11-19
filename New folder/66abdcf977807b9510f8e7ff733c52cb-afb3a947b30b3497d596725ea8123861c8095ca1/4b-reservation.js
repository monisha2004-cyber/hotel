var rsv = {
  // (A) HELPER - AJAX FETCH
  fetch : (data, after) => {
    // (A1) FORM DATA
    let form;
    if (data instanceof FormData) { form = data; }
    else {
      form = new FormData();
      for (let [k, v] of Object.entries(data)) { form.append(k, v); }
    }

    // (A2) FETCH
    fetch("3-ajax-hotel.php", { method : "post", body : form })
    .then(res => res.text())
    .then(txt => after(txt))
    .catch(err => console.error(err));
  },

  // (B) PROPERTIES
  hSec : null, // html sections
  date : null, // currently selected date
  room : null, // currently selected room

  // (C) INITIALIZE - GET HTML SECTIONS
  init : () => rsv.hSec = [
    document.getElementById("sDate"),
    document.getElementById("sRoom"),
    document.getElementById("sContact")
  ],

  // (D) SWITCH HTML SECTIONS
  switch : i => { for (let j in rsv.hSec) {
    if (i==j) { rsv.hSec[j].classList.remove("hide"); }
    else { rsv.hSec[j].classList.add("hide"); }
  }},

  // (E) GET ROOMS FOR SELECTED DATE PERIOD
  get : () => {
    // (E1) GET DATE
    rsv.date = {
      days : parseInt(document.getElementById("sDateTo").value),
      from : document.getElementById("sDateFrom").value
    }
    rsv.date.to = new Date(rsv.date.from);
    rsv.date.to = new Date(
      rsv.date.to.setDate(rsv.date.to.getDate() + rsv.date.days)
    ).toISOString().substring(0, 10);

    // (E2) FETCH ROOMS
    rsv.fetch({ req : "get", ...rsv.date }, res => {
      // (E2-1) DRAW SELECTED DATE
      rsv.hSec[1].innerHTML = "";
      let row = document.createElement("div");
      row.className = "rHead";
      row.innerHTML = `<div class="step">STEP 2 OF 3</div>
      <h1 class="head">SELECT A ROOM</h1>
      <div class="step">FROM ${rsv.date.from} TO ${rsv.date.to} (${rsv.date.days} DAYS)</div>`;
      rsv.hSec[1].appendChild(row);

      // (E2-2) DRAW ROOMS
      for (let [i,r] of Object.entries(JSON.parse(res))) {
        row = document.createElement("div");
        row.className = "rRow";
        row.innerHTML = `<div class="rType">${r.t}</div>`;
        if (r.b) {
          row.classList.add("rBooked");
          let s = '<div class="rStat">This room is booked.<ul>';
          for (let b of r.b) { s += `<li>${b.s} (PM) to ${b.e} (AM)</li>`; }
          s += '</ul></div>';
          row.innerHTML += s;
        } else {
          row.innerHTML += `<div class="rStat">
          Reserve this room for $${(rsv.date.days * r.p).toFixed(2)}.
          </div>`;
          row.onclick = () => rsv.set(i);
        }
        rsv.hSec[1].appendChild(row);
      }

      // (E2-3) BACK BUTTON
      row = document.createElement("input");
      row.type = "button";
      row.className = "button";
      row.value = "Back";
      row.onclick = () => rsv.switch(0);
      rsv.hSec[1].appendChild(row);

      // (E2-4) DONE - SHOW PAGE
      rsv.switch(1);
    });
    return false;
  },

  // (F) SELECT ROOM
  set : id => {
    rsv.room = id;
    rsv.switch(2);
  },

  // (G) SUBMIT RESERVATION
  reserve : () => {
    // (G1) FORM DATA
    let data = new FormData(rsv.hSec[2]);
    data.append("req", "reserve");
    data.append("id", rsv.room);
    data.append("start", rsv.date.from);
    data.append("end", rsv.date.to);

    // (G2) AJAX FETCH
    rsv.fetch(data, res => {
      if (res=="OK") { location.href = "5-thank-you.html"; }
      else { alert(res); }
    });
    return false;
  }
};
window.onload = rsv.init;