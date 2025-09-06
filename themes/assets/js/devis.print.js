// Using es-2015 JS
Object.defineProperty(Array.prototype, "includes", {
  value: function (searchElement, fromIndex) {
    if (this == null) {
      throw new TypeError('"this" is null or not defined');
    }
    var o = Object(this);
    var len = o.length >>> 0;
    if (len === 0) {
      return false;
    }
    var n = fromIndex | 0;
    var k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);
    function sameValueZero(x, y) {
      return (
        x === y ||
        (typeof x === "number" && typeof y === "number" && isNaN(x) && isNaN(y))
      );
    }
    while (k < len) {
      if (sameValueZero(o[k], searchElement)) {
        return true;
      }
      k++;
    }
    return false;
  },
});

//* **************************RESUMETABLEAU************************************************* **
function drawDownTab() {
  var tableResume = document.getElementById("tableauResume");
  tab = tableResume.firstElementChild;
  while (tab.lastChild != tab.firstElementChild) {
    tab.removeChild(tab.lastChild);
  }

  var tableauProduits = document.getElementById("tableauProduits");
  var discount_pct = document.getElementById("discount_pct").value;
  var tauxArray = [];

  for (var i = 1; i < tableauProduits.rows.length; i++) {
    var xBBE = tableauProduits.rows[i].cells.length > 10 ? 2 : 0; // Add 2 columns if BBE

    if (tableauProduits.rows[i].cells[6 + xBBE]) {
      var tauxTax = parseFloat((tableauProduits.rows[i].cells[6 + xBBE].innerHTML.split('%'))[0]);
      if (!tauxTax) tauxTax = 0;

      if (Array.prototype.includes.call(tauxArray, tauxTax)) { // If taux exists
        var tvaLign = tableauProduits.rows[i].cells[7 + xBBE].innerHTML.split(" ")[0].replace(",", "");
        if (!tvaLign) tvaLign = 0;

        var lignHT = tableauProduits.rows[i].cells[8 + xBBE].innerHTML.split(" ")[0].replace(",", "");
        lignHT = lignHT - (lignHT * discount_pct / 100);
        tvaLign = tvaLign - (tvaLign * discount_pct / 100);
        changeTax(tauxTax, lignHT, tvaLign);

      } 
      else if (tableauProduits.rows[i].cells[8 + xBBE].innerHTML.split(" ")[0]) {
        tauxArray.push(tauxTax);

        var row = tableResume.insertRow(1);
        row.id = tauxTax;

        var tvaLign = tableauProduits.rows[i].cells[7 + xBBE].innerHTML.split(" ")[0].replace(",", "");
        if (!tvaLign) tvaLign = 0;
        tvaLign = tvaLign - (tvaLign * discount_pct / 100);

        var lignHT = tableauProduits.rows[i].cells[8 + xBBE].innerHTML.split(" ")[0].replace(",", "");
        lignHT = lignHT - (lignHT * discount_pct / 100);
        if (isNaN(lignHT) || lignHT == "") lignHT = 0;
        
        var Ttt = parseFloat(tvaLign) + parseFloat(lignHT);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        cell1.innerHTML = toPrice(lignHT);
        cell1.dataset.price = lignHT;
        cell2.innerHTML = tauxTax + " %";
        cell3.innerHTML = toPrice(tvaLign);
        cell4.innerHTML = toPrice(Ttt);
        cell3.dataset.price = tvaLign;
      }

      function roundToTwo(num) {
        return +(Math.round(num + "e+2") + "e-2");
      }

      function toPrice(price) {
        return roundToTwo(price).toFixed(2) + " €";
      }


      function changeTax(tauxTax, lignHT, tvaLign) {
        var row = document.getElementById(tauxTax);
        var oldHt = row.cells[0].dataset.price;
        if (isNaN(lignHT) || lignHT == '')
          lignHT = 0;

        var oldTva = row.cells[2].dataset.price;
        if (isNaN(oldTva) || oldTva == '')
          oldTva = 0;

        var newHt = parseFloat(lignHT) + parseFloat(oldHt);
        if (isNaN(newHt)) newHt = 0;
        
        var newTVA = parseFloat(tvaLign) + parseFloat(oldTva);
        if (isNaN(newTVA)) newTVA = 0;

        var newTtt = newTVA + newHt;
        row.cells[0].innerHTML = toPrice(newHt);
        row.cells[0].dataset.price = newHt;
        row.cells[1].innerHTML = tauxTax + ' %';
        row.cells[2].innerHTML = toPrice(newTVA);
        row.cells[2].dataset.price = newTVA;
        row.cells[3].innerHTML = toPrice(newTtt);
      }
    }
  }

    var tableFacture = document.getElementById("tbody_ant_fact");
    if (tableFacture) {
        for (var i = 0; i < tableFacture.rows.length; i++) {
          const e = tableFacture.rows[i];
          var tauxTax = parseFloat(e.cells[2].innerHTML.split('%')[0]);
          if (!tauxTax) {
            tauxTax = 0;
          }

          var lignHT = e.cells[1].innerHTML.split(' ')[0].replace(',', '');
          if (!(lignHT))
            lignHT = 0;
          var tvaLign = e.cells[3].innerHTML.split(' ')[0].replace(',', '') - lignHT;
          if (!(tvaLign))
            tvaLign = 0;
          else
            tvaLign = tvaLign.toFixed(2);

          if (tauxArray.length && Array.prototype.includes.call(tauxArray, tauxTax)) {
              changeTax(tauxTax, -lignHT, -tvaLign);
          } else {
              tauxArray.push(tauxTax);
              var row = tableResume.insertRow(1);
              row.id = tauxTax;
              var Ttt = parseFloat(tvaLign) + parseFloat(lignHT);
              var cell1 = row.insertCell(0);
              var cell2 = row.insertCell(1);
              var cell3 = row.insertCell(2);
              var cell4 = row.insertCell(3);
              cell1.innerHTML = toPrice(-lignHT);
              cell2.innerHTML = tauxTax + ' %';
              cell3.innerHTML = -tvaLign + " €";
              cell4.innerHTML = toPrice(-Ttt);
          }
        }
    }
}
drawDownTab();

var printContents = document.getElementById("print").innerHTML;
var originalContents = document.body.innerHTML;
document.body.innerHTML = printContents;
document.getElementsByTagName("body")[0].classList.add("printBody");

function getSelectedText(e) {
  if (e.selectedIndex == -1)
      return null;

  return e.options[e.selectedIndex].text;
}

var e = document.getElementsByTagName('select');
for (var i = 0; i < e.length; i++) {
  e[i].outerHTML = e[i].value ? getSelectedText(e[i]) : '';
  e[i].outerHTML = e[i].value ? getSelectedText(e[i]) : '';
}
