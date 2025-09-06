// * Add a discount
function displayRemise() {
  $('.d-remise').show();
  $('.btn-remise').hide();
}

// * Add an acompte
function displayAcompte() {
  $('.d-acompte').show();
  $('.btn-acompte').hide();
}

// * Round price and add euro symbol
function toPrice(price) {
  return roundToTwo(price).toFixed(2) + " €";
}

var TTF,
    real_TTF,
    TIT,
    TVA,
    p_family = [],
    familyInFacture,
    descriptionInFacture,
    imageInFacture,
    remise_val,
    vol_st = 0.00,
    vol_m3 = 0.00;
if (document.getElementById("choix_partic")) {
  document.getElementById("choix_partic").value = true;
}

if (document.getElementById("choix_prof")) {
  document.getElementById("choix_prof").value = false;
}

if (document.getElementById("client")) {
  document.getElementById("client").selectedIndex = -1;
}

// * Search a contact or a member
$(".part-search").autocomplete({
  maxShowItems: 30,
  source: function (request, response) {
    if (request.term.length > 1)
      $.ajax({
          url: "/Administration/Contacts",
          data: { search: request.term, filter: 'contacts_accounts', type: 'devis' },
          dataType: "json",
          method: "POST",
          success: function (data) {
              response($.map(data, function (item) {
                  return {
                    label: item.name,
                    value: item.name,
                    ...item
                  };
              }));
          }
      });
  },
  select: function(event, ui) {
    document.getElementById("c_lname").value = ui.item.nom;
    document.getElementById("c_fname").value = ui.item.prenom;
    document.getElementById("c_address").value = ui.item.address;
    document.getElementById("c_address_complement").value = ui.item.address_complement;
    document.getElementById("c_town").value = ui.item.ville;
    document.getElementById("c_pcode").value = ui.item.cp;
    document.getElementById("c_country").value = ui.item.pays;
    document.getElementById("c_email").value = ui.item.email;
    document.getElementById("c_civilite").value = ui.item.civilite;
    document.getElementById("id_contact").value = ui.item.id_contact;
    document.getElementById("c_address_complement").value = ui.item.address_complement;
    document.getElementById("new_client").checked = false;
    setTimeout(() => {document.getElementById("client").value = "";}, 1000);
  }
});

// * Search a Tier
$(".pro-search").autocomplete({
  maxShowItems: 30,
  source: function (request, response) {
    if (request.term.length > 1)
      $.ajax({
          url: "/Administration/Contacts",
          data: { search: request.term, filter: 'tiers', type: 'devis' },
          dataType: "json",
          method: "POST",
          success: function (data) {
              response($.map(data, function (item) {
                  return {
                    label: item.name,
                    value: item.name,
                    ...item
                  };
              }));
          }
      });
  },
  select: function(event, ui) {
    if (ui.item.type == 0) {
      document.getElementById('personne_moral').checked = true;
      onclickPmPP(personne_moral , personne_physique);
    } else {
      onclickPmPP( personne_physique, personne_moral);
    }

    document.getElementById("id_tier").value = ui.item.id_tier;
    document.getElementById("denomination").value = ui.item.denomination;
    document.getElementById("n_siret").value = ui.item.siret;
    document.getElementById("t_address").value = ui.item.address;
    document.getElementById("t_address_complement").value = ui.item.address_complement;
    document.getElementById("t_pcode").value = ui.item.cp;
    document.getElementById("t_boite_postale").value = ui.item.boite_postale;
    document.getElementById("t_town").value = ui.item.ville;
    document.getElementById("t_country").value = ui.item.pays;
    document.getElementById("numero_tva").value = ui.item.numero_tva;
    document.getElementById('representant_legal_id').value = ui.item.representant_legal_id;
    document.getElementById('representant_legal_id_client').value = ui.item.representant_legal_id_client;
    document.getElementById('representant_legal_search').value = ui.item.representant_legal_name;
    document.getElementById("forme_juridique_select").value = ui.item.forme_name;
    document.getElementById("t_email").value = ui.item.email;
    setTimeout(() => {document.getElementById("tier").value = "";}, 1000);
  }
});

//* **************************REMISE************************************************* **
function remise() {
    if (document.getElementById("remise_val"))
        old_remise_val = document.getElementById("remise_val").value;

    if (document.getElementById("remise_pct"))
        old_remise_pct = document.getElementById("remise_pct").value;

    remise_val = parseFloat(old_remise_val);
    var remise_pct = parseFloat(old_remise_pct);
    if (!isNaN(remise_pct) && remise_pct != 0) {
        remise_val = real_TTF / 100 * remise_pct;
        document.getElementById("disp_rem").innerHTML = remise_pct + "% (soit " + roundToTwo(remise_val).toFixed(2) + " €)";
        document.getElementById("db_save_estim_dip").value = remise_pct;
        document.getElementById("db_save_estim_dvl").value = remise_val;
    } else if (remise_val) {
        remise_pct = remise_val * 100 / real_TTF;
        if (isNaN(remise_val))
            remise_val = 0.00;
        document.getElementById("disp_rem").innerHTML = roundToTwo(remise_val).toFixed(2) + " €";
        document.getElementById("db_save_estim_dip").value = 0.00;
        document.getElementById("db_save_estim_dvl").value = remise_val;
    } else {
        remise_val = 0;
        remise_pct = 0;
        document.getElementById("disp_rem").innerHTML = "-";
        document.getElementById("db_save_estim_dip").value = 0.00;
        document.getElementById("db_save_estim_dvl").value = 0.00;
    }
    document.getElementById("discount_pct").value = remise_pct;
    document.getElementById("discount_val").value = remise_val;

    var d_TTF = TTF - remise_val;
    if (isNaN(d_TTF)) {
      d_TTF = 0.00;
    }
    var TIT = d_TTF + TVA;
    if (isNaN(TIT)) {
      TIT = 0.00;
    }
    document.getElementById("sousTTF").innerHTML = roundToTwo(d_TTF).toFixed(2) + " €";
    document.getElementById("disp_livr_ttc").innerHTML = roundToTwo(TIT).toFixed(2) + " €";

    acompte();
    drawDownTab();
}

//* **************************ACOMPTE************************************************* **
function acompte(event = false) {
    var acompte = parseFloat(document.getElementById("acompte_val").value) ?? 0.00;
    if (event) {
      acompte = parseFloat(event.srcElement.textContent) ?? 0.00;
      document.getElementById("acompte_val").value = acompte;
    }
    if (isNaN(remise_val)) {
      remise_val = 0;
    }
    if (isNaN(acompte)) {
      acompte = 0;
    }
    var total_acompte = TTF + TVA - acompte - remise_val;
    if (isNaN(total_acompte)) {
      total_acompte = 0;
    }

    if (acompte > 0) {
      displayAcompte();
    }

    document.getElementById("db_save_estim_acompte_val").value = acompte;
    document.getElementById("disp_acompte").innerHTML = roundToTwo(acompte).toFixed(2);
    document.getElementById("disp_acompte_total").innerHTML = roundToTwo(total_acompte).toFixed(2) + " €";
    if (document.getElementById("billable"))
        document.getElementById("billable").innerHTML = roundToTwo(total_acompte).toFixed(2) + " €";
}

// * Calculate product row
function recalcRow(event) {
  var cell = event.target
  var row = cell.parentNode.children;

  var xBBE = row.length > 10 ? 2 : 0; // Add 2 columns if BBE

  var qt = row[4].textContent;
  if (!qt || isNaN(qt)) {
    qt = 0;
    row[4].textContent = 0;
  }

  if (row[5] == cell) { // If discount Product column, parse percent symbol
    if (cell.textContent) {
      if (cell.textContent.search('%') > 0) {
        cell.classList.remove('euro');
        cell.classList.add('percent');
        cell.textContent = roundToTwo(parseFloat(cell.textContent)).toFixed(2);
      } else if (!cell.classList.contains('euro') && !cell.classList.contains('percent')) {
        cell.classList.add('euro');
      }
    } else {
      cell.textContent = '';
      cell.classList.remove('euro');
      cell.classList.remove('percent');
    }
  }

  if (xBBE > 0) { // If BBE, recalc Steres and m³
    row[6].textContent = roundToTwo(row[6].dataset.value * qt).toFixed(2);
    row[7].textContent = roundToTwo(row[7].dataset.value * qt).toFixed(2);
  }

  if (isNaN(cell.textContent)) {
     cell.textContent = parseFloat(cell.textContent) ?? 0;
      if (isNaN(cell.textContent)) {
        cell.textContent = 0;
      }
  }

  var productPrice = parseFloat(row[2].textContent);
  if (!productPrice || isNaN(productPrice)) {
    productPrice = 0;
    row[2].textContent = 0;
  }

  var discount_product = row[5].textContent;

  var tva = parseFloat(row[6 + xBBE].textContent);
  if (tva < 0 || isNaN(tva)) {
    tva = 0;
    row[6 + xBBE].textContent = 0;
  }
  var newPrice = row[5].classList.contains('percent') ? qt * productPrice - (qt * productPrice * discount_product / 100) : qt * (productPrice - discount_product);
  var newTva = (newPrice * tva / 100);
  row[7 + xBBE].textContent = toPrice(newTva);
  row[8 + xBBE].innerHTML = toPrice(newPrice);
  row[8 + xBBE].dataset.value = toPrice(newPrice);

  remise();
  acompte();
}

// * Calculate products lines and tva lines
function recalcTotal() {
  var table = document.getElementById("tableauProduits");
  var insertPlaceHtml = '';
  TTF = 0;
  real_TTF = 0;
  TVA = 0;

  for (let i = 1; i < table.rows.length - 1; i++) {
    var cols = table.rows[i].children;

    if (cols.length > 8) {
      var xBBE = cols.length > 10 ? 2 : 0; // Add 2 columns if BBE

      var calcPrice = roundToTwo(cols[2].textContent).toFixed(2);
      cols[2].textContent = isNaN(calcPrice) ? "0.00" : calcPrice;
      var discount_pct = document.getElementById("discount_pct").value;
      if (!isFinite(discount_pct)) discount_pct = 0;
      var tvaLign = parseFloat(cols[7 + xBBE].textContent);
      if (!isNaN(tvaLign) && tvaLign != 0)
        TVA += tvaLign - (tvaLign * discount_pct / 100);
      var ttLign = parseFloat(cols[8 + xBBE].dataset.value);
      if (!isNaN(tvaLign) && ttLign != 0) {
        TTF += ttLign;
      }

      insertPlaceHtml += `<option value="${i + 1}">${cols[1].innerText}</option>`;
    } else if (cols[1]) {
      if (insertPlaceHtml) {
        insertPlaceHtml += "</optgroup>";
      }
      insertPlaceHtml += `<optgroup label="${cols[1].innerText}">"<option value="${i + 1}" data-template="<b>— $1 —</b>">${cols[1].innerText}</option>`;
    }
    insertPlaceHtml += "</optgroup>";
  }

  $('#insertPlace')[0].innerHTML = insertPlaceHtml;
  Metro.getPlugin("#insertPlace", 'select').data($('#insertPlace').html());

  real_TTF = TTF;
  var tableFacture = document.getElementById("tbody_ant_fact");
  if (tableFacture) {

    for (let i = 0; i < tableFacture.rows.length; i++) {
      const e = tableFacture.rows[i];
      const TTC = parseFloat(e.children[3].textContent);
      const HT = parseFloat(e.children[1].textContent);
      TVA -= (TTC - HT).toFixed(2);
      TTF -= HT;
    }
  }
  TIT = TTF + TVA;
  document.getElementById("sousTHT").innerHTML = toPrice(TTF);
  document.getElementById("sousTTF").innerHTML = toPrice(TTF);
  document.getElementById("disp_livr_ttc").innerHTML = toPrice(TIT);
  document.getElementById("tva").innerHTML = toPrice(TVA);
  remise();
  acompte();
}



//* **************************FAMILY************************************************* **
function newFamily(id, title, xBBE, pos = null) {
  var table = document.getElementById("tableauProduits");
  if (!pos) pos = table.rows.length - 1;
  var rowFamily = table.insertRow(pos);
  rowFamily.classList.add("bg-dark");
  rowFamily.classList.add("sortable");
  rowFamily.id = id;
  var cellFamily0 = rowFamily.insertCell(0);
  cellFamily0.classList.add("handle");
  var cellFamily1 = rowFamily.insertCell(1);
  cellFamily1.classList.add("color-white");
  cellFamily1.innerHTML = title;
  cellFamily1.colSpan = 8 + xBBE;
  cellFamily1.contentEditable = true;
  var cellFamily2 = rowFamily.insertCell(2);
  cellFamily2.classList.add("actions-fam");
  cellFamily2.innerHTML = "<a class='remove remove-fam' onclick=\"removeFamily('"+id+"')\" title='Supprime le séparateur'><i class='material-icons'>delete</i></a> \
                <a class='insert-product' title='Insérer une nouvelle section' onclick='insertFamily(event)'><i class='ml-2 material-icons'>library_add</i></a></td>";
  var rowAddProduct = table.insertRow(pos + 1);
  rowAddProduct.classList.add('add-product-bar');
  $(rowAddProduct).append("<td colspan='20' id='add-product' class='p-1'> \
      <a class='btn-lg text-hover-theme cursor-pointer' data-toggle='modal' data-target='#listProduct' onclick='selectInsertPlace(event, \"true\")'> <b>Ajouter un produit</b><i class='ya ya-plus'></i> </a> \
      <a class='btn-lg text-hover-theme cursor-pointer' onclick='insertFamily(event)'> <b>Ajouter une section</b><i class='ml-2 material-icons vertical-align'>library_add</i> </a> \
    </td>");
  p_family.push(id);
}

var newFamilyNumber = -1;
function addFamily() {
  if (familyInFacture) {
    var name = document.getElementById("new_family_name").value;
    if (document.getElementById("create_family").checked) {
        document.getElementById("db_save_estim_create_family").value += name + "|";
    }
    document.getElementById("new_family_name").value = "";
    document.getElementById("create_family").checked = false;
    $('#familyedit').append(`<option value="${newFamilyNumber}">${name}</option>`);
    Metro.getPlugin("#familyedit", 'select').data($('#familyedit').html());
    Metro.getPlugin("#familyedit", 'select').val(newFamilyNumber);
  }
  newFamilyNumber--;
}

function removeFamily(id) {
    if (document.getElementById(id).previousSibling.previousSibling && document.getElementById(id).previousSibling.previousSibling.classList && document.getElementById(id).previousSibling.previousSibling.classList.contains('add-product-bar'))
        document.getElementById(id).previousSibling.previousSibling.remove();
    document.getElementById(id).remove();
    p_family.splice(p_family.indexOf(id));
    recalcTotal();
    replaceProductsBar();
}

// * Add a product from modal to table
function addRow() {
    var pid = document.getElementById("select-product").value;
    if (!pid)
      return;

    var pdets = pid.split("|");
    bbe = (pdets.length == 12 || pdets.length == 13);
    xBBE = bbe ? 2 : 0;

    if (pdets.length != 10 && pdets.length != 11 && !bbe)
      return;
    var table = document.getElementById("tableauProduits");
    insertPlace = [table.rows.length - 1];

    // * Product Family
    if (familyInFacture && !document.getElementById('insertPlace').value) {
      var family = $('#familyedit').val();
      var title = !family ? 'Autres' : $('#familyedit option:selected').text();
      if (p_family.indexOf("family_" + title) == -1) {
        newFamily("family_" + title, title, xBBE);
        insertPlace = [table.rows.length - 1];
      } else {
        var f = document.getElementById("family_" + title);
        if (f) {
          insertPlace = [$(f).parent().children().index(f) + 2];
        }
      }
    } else if (document.getElementById('insertPlace').value) {
      insertPlace = Metro.getPlugin("#insertPlace", 'select').val();
    }

    insertPlace = insertPlace.map(e => parseFloat(e));
    insertPlace.sort((a, b) => a > b);
    for (var i = 0; i < insertPlace.length; i++) {
      var row = table.insertRow(parseFloat(insertPlace[i]) + i);
      row.classList.add("sortable");

      var cellId = row.insertCell(0);
      cellId.classList.add("handle");
      cellId.innerHTML = pdets[0];

      var cellDesignation = row.insertCell(1);
      var sub_interval = Metro.getPlugin("#subscription_interval", 'select').val();
      var sub_interval_count = document.getElementById("subscription_interval_count").value;
      sub_interval_count = sub_interval_count == '' ? 1 : sub_interval_count;
      if (sub_interval && sub_interval_count) { // Subscription
        if (sub_interval_count > 1) {
            interval = {day: 'jours', week: 'semaines', month: 'mois', year: 'ans'};
            subscription = `Abonnement tous les ${sub_interval_count} ${interval[sub_interval]}`;
        } else {
            interval = {day: 'quotidien', week: 'hebdomadaire', month: 'mensuel', year: 'annuel'};
            subscription = "Abonnement "+interval[sub_interval];
        }
        cellDesignation.innerHTML = "<p class='subscription-"+sub_interval+"' data-sub-interval='"+sub_interval+"' data-sub-interval-count='"+sub_interval_count+"' title='"+subscription+"'>"+pdets[1]+"</p>";
      } else {
        cellDesignation.innerHTML = "<p>"+pdets[1]+"</p>";
      }

      cellDesignation.classList.add("text-left");
      if (imageInFacture && pdets[8] && pdets[8] != "") {
        cellDesignation.innerHTML += "<div class='imageResize'><img src='"+pdets[8]+"' class='mb-2'></div>";
      }
      if (pdets.length == 11) {
        var description = pdets[10].replace(/(<br ?\/?>)/g, "\n");
        cellDesignation.innerHTML += `<br/><textarea class='form-control' style='height: 32px'>${description}</textarea>`;
      } else if (pdets.length == 13) { // IF_BBE
        var description = pdets[12].replace(/(<br ?\/?>)/g, "\n");
        cellDesignation.innerHTML += `<br/><textarea class='form-control' style='height: 32px'>${description}</textarea>`;
      }

      var manPrice = Number(parseFloat(document.getElementById("prx").value).toFixed(2));
      var cellPrice = row.insertCell(2);
      cellPrice.contentEditable = true;
      cellPrice.classList.add("euro");
      cellPrice.addEventListener('input', (e) => recalcRow(e));
      cellPrice.addEventListener('blur', () => recalcTotal());
      cellPrice.innerHTML = roundToTwo(isNaN(manPrice) ? parseFloat(pdets[2]).toFixed(2) : manPrice).toFixed(2);

      var cellUnite = row.insertCell(3);
      cellUnite.innerHTML = pdets[3];

      var cellQte = row.insertCell(4);
      cellQte.contentEditable = true;
      cellQte.addEventListener('input', (e) => recalcRow(e));
      cellQte.addEventListener('blur', () => recalcTotal());
      cellQte.innerHTML = document.getElementById("qte").value;

      var cellDiscount = row.insertCell(5);
      cellDiscount.contentEditable = true;

      var productDiscount = parseFloat(document.getElementById("product_discount").value).toFixed(2);
      if (isNaN(productDiscount)) productDiscount = '';
      if (productDiscount && document.getElementById("product_discount").value.search('%') > 0)
        cellDiscount.classList.add('percent');
      else if (productDiscount)
        cellDiscount.classList.add('euro');
      cellDiscount.innerHTML = productDiscount;

      cellDiscount.addEventListener('input', (e) => recalcRow(e));
      cellDiscount.addEventListener('blur', () => recalcTotal());

      var manTva = Number(parseFloat(document.getElementById("tvaedit").value).toFixed(2));
      var pQuantity = parseFloat(document.getElementById("qte").value).toFixed(2);
      var prdtPrice = (isNaN(manPrice) ? parseFloat(pdets[2]).toFixed(2) : manPrice);
      var linePrice = cellDiscount.classList.contains('percent') ? pQuantity * (prdtPrice - (prdtPrice * productDiscount / 100)) : pQuantity * (prdtPrice - productDiscount);
      var dispLinePrice = roundToTwo(linePrice.toFixed(2)).toFixed(2);
      let tvaLign = (linePrice * (isNaN(manTva) ? pdets[4] : manTva)) / 100;

      if (bbe) { // If BBE, add steres and square meters
        var cellSteres = row.insertCell(6);
        cellSteres.contentEditable = true;
        cellSteres.innerHTML = pdets[10] * pQuantity;
        cellSteres.dataset.value = pdets[10];

        var cellSquaresMeters = row.insertCell(7);
        cellSquaresMeters.contentEditable = true;
        cellSquaresMeters.innerHTML = pdets[11] * pQuantity;
        cellSquaresMeters.dataset.value = pdets[11];

        var cellTva = row.insertCell(8);
        var cellTotalTva = row.insertCell(9);
        var cellTotal = row.insertCell(10);
        var cellDelete = row.insertCell(11);
      } else {
        var cellTva = row.insertCell(6);
        var cellTotalTva = row.insertCell(7);
        var cellTotal = row.insertCell(8);
        var cellDelete = row.insertCell(9);
      }

      cellTva.contentEditable = true;
      cellTva.classList.add("percent");
      cellTva.addEventListener('input', (e) => recalcRow(e));
      cellTva.addEventListener('blur', () => recalcTotal());
      cellTva.innerHTML = (isNaN(manTva) ? pdets[4] : manTva);

      cellTotalTva.innerHTML = (tvaLign).toFixed(2) + " €";
      cellTotal.innerHTML = dispLinePrice + ' €';
      cellTotal.dataset.value = dispLinePrice;

      cellDelete.classList.add('d-flex');
      cellDelete.innerHTML = "<a class='remove cursor-pointer text-hover-theme remove-product ' onclick='deleteData(this)' title='Supprimer ce produit'><i class='material-icons'>delete</i></a> \
      <a class='insert-product'  data-toggle='modal' data-target='#listProduct' title='Insérer un produit après ce produit' onclick='selectInsertPlace(event)'><i class='ya ya-plus'></i></a>";

      TTF += parseFloat(dispLinePrice);
      TVA += tvaLign;
      TIT = TTF + TVA;
    }
    document.getElementById("sousTHT").innerHTML = toPrice(TTF);
    document.getElementById("sousTTF").innerHTML = toPrice(TTF);
    document.getElementById("disp_livr_ttc").innerHTML = toPrice(TIT);
    document.getElementById("tva").innerHTML = toPrice(TVA);
    document.getElementById("add-product").style.color = 'black';

    document.getElementById("prx").value = "";
    document.getElementById("qte").value = "1";
    document.getElementById("product_discount").value = "";
    Metro.getPlugin("#tvaedit", 'select').val('');
    Metro.getPlugin("#select-product", 'select').val('');
    Metro.getPlugin("#subscription_interval", 'select').val('');
    document.getElementById("subscription_interval_count").value = '';
    if (familyInFacture) {
      replaceProductsBar();
      Metro.getPlugin("#familyedit", 'select').val('');
    }

    $("#listProductAdd").addClass('disabled');

    if (document.getElementById('tbodyProduits').rows.length < 5) {
      replaceProductsBar();
    }
    recalcTotal();
}


function roundToTwo(num) {
    return +(Math.round(num + "e+2") + "e-2");
}

// * Delete a product line
function deleteData(element) {
    let suppressEle = element.parentNode;
    suppressEle.parentNode.remove();
    recalcTotal();
    replaceProductsBar();
}

// * Save facture in DB
function saveDataPost(event, draft = false) {

    recalcTotal();

    let choix_partic = document.getElementById("choix_partic").value;
    var save_FID = document.getElementById("bill_ID").innerHTML;
    var tva_total = parseFloat(document.getElementById("tva").innerHTML.replace(" €", "")).toFixed(2);

    if (draft == true) {
      document.getElementById("db_save_estim_draft").value = draft;
    }

    /* Reset default values */
    document.getElementById("db_save_estim_pnm").value = "";
    document.getElementById("db_save_estim_des").value = "";
    document.getElementById("db_save_estim_pup").value = "";
    document.getElementById("db_save_estim_uni").value = "";
    document.getElementById("db_save_estim_pqt").value = "";
    document.getElementById("db_save_estim_tva_pct").value = "";
    document.getElementById("db_save_estim_pvs").value = "";
    document.getElementById("db_save_estim_pvm").value = "";
    document.getElementById("db_save_estim_sub_interval").value = "";
    document.getElementById("db_save_estim_sub_interval_count").value = "";

    var save_billTTF = parseFloat(document.getElementById("sousTTF").innerHTML.replace(" €", "")).toFixed(2);
    var save_billTIT = parseFloat(document.getElementById("disp_livr_ttc").innerHTML.replace(" €", "")).toFixed(2);
    var delivery_value = 0.0;
    var save_delivery = (isNaN(delivery_value) ? 0.00 : delivery_value);
    var i = 0;
    var x = document.getElementById("tableauProduits").rows.length;
    if (x == 2) {
        document.getElementById("add-product").style.color = 'red';
        alert('Aucun produit ajouté');
        return event.preventDefault();
    }
    if (document.getElementById('db_save_estim_ref_FID').value && parseFloat(document.getElementById('disp_acompte_total').textContent) < 0) {
        alert('Le total d\'une facture partielle ne peut-être négatif');
        return event.preventDefault();
    }

      document.getElementById("db_save_estim_fid").value = save_FID;
      document.getElementById("db_save_estim_txb").value = document.getElementById("text_bottom").value;
      document.getElementById("db_save_estim_txt").value = document.getElementById("text_top").value;
      document.getElementById("db_save_estim_ttf").value = save_billTTF;
      document.getElementById("db_save_estim_tit").value = save_billTIT;
      document.getElementById("db_save_estim_tva").value = tva_total;
      document.getElementById("db_save_estim_dvy").value = save_delivery;

      // if(document.getElementById('numero_facture')){

      //   var numero_fac = document.getElementById('numero_facture').innerHTML;
      //   document.getElementById("db_save_estim_fac").value = numero_fac;
      // }

    var familyName = '';
    var emptyFamily = false;
    var ifBBe = false;
    for (i = 1; i < (x - 1); i++) {
        var imageProduct = '';
        var imageWidth = '';
        var r = document.getElementById("tableauProduits").rows[i];

        if (r.cells.length < 7) { // If not a product
          if (r.cells[1]) { // If family Name

              if (emptyFamily == true) { // If multiple empty families
                document.getElementById("db_save_estim_pnm").value += "--emptyLine--|";
                document.getElementById("db_save_estim_sub_interval").value += "|";
                document.getElementById("db_save_estim_sub_interval_count").value += "|";
                document.getElementById("db_save_estim_pup").value += "|";
                document.getElementById("db_save_estim_uni").value += "|";
                document.getElementById("db_save_estim_pqt").value += "|";
                document.getElementById("db_save_estim_discount_product").value += "|";
                if (ifBBe) {
                  document.getElementById("db_save_estim_pvs").value += "|";
                  document.getElementById("db_save_estim_pvm").value += "|";
                  document.getElementById("db_save_estim_tva_pct").value += "|";
                } else {
                  document.getElementById("db_save_estim_tva_pct").value += "|";
                }
                document.getElementById("db_save_estim_family").value += familyName + "|";
                document.getElementById("db_save_estim_des").value += "|";
                document.getElementById("db_save_estim_image_product").value += '|';
                document.getElementById("db_save_estim_image_width").value += '|';
              }
              familyName = r.cells[1].textContent;
              emptyFamily = true;
            }
        } else { // If Product
          document.getElementById("db_save_estim_pnm").value += (r.cells[1].firstChild.textContent).toString() + "|";
          document.getElementById("db_save_estim_sub_interval").value += (r.cells[1].firstChild.dataset.subInterval ?? '').toString() + "|";
          document.getElementById("db_save_estim_sub_interval_count").value += (r.cells[1].firstChild.dataset.subIntervalCount ?? '').toString() + "|";
          document.getElementById("db_save_estim_pup").value += (parseFloat(r.cells[2].innerHTML).toFixed(2)).toString() + "|";
          document.getElementById("db_save_estim_uni").value += (r.cells[3].innerHTML).toString() + "|";
          document.getElementById("db_save_estim_pqt").value += (parseFloat(r.cells[4].innerHTML).toFixed(2)).toString() + "|";
          document.getElementById("db_save_estim_discount_product").value += (r.cells[5].classList.contains('percent') ? (parseFloat(r.cells[5].innerHTML).toFixed(2)).toString()+'%' : (parseFloat(r.cells[5].innerHTML).toFixed(2)).toString()) + "|";
          if (r.cells.length > 10) { // IF_BBE
            ifBBe = true;
            document.getElementById("db_save_estim_pvs").value += (parseFloat(r.cells[6].innerHTML).toFixed(2)).toString() + "|";
            document.getElementById("db_save_estim_pvm").value += (parseFloat(r.cells[7].innerHTML).toFixed(2)).toString() + "|";
            document.getElementById("db_save_estim_tva_pct").value += (parseFloat(r.cells[8].innerHTML).toFixed(2)).toString() + "|";
          } else {
            document.getElementById("db_save_estim_tva_pct").value += (parseFloat(r.cells[6].innerHTML).toFixed(2)).toString() + "|";
          }
          document.getElementById("db_save_estim_family").value += familyName + "|";
          emptyFamily = false;

          for (let i = 0; i < r.cells[1].children.length; i++) {
            const e = r.cells[1].children[i];
            if (e) {
              if (e.tagName == "DIV" && e.firstChild && e.firstChild.tagName == "IMG") {
                imageProduct = e.firstChild.attributes.src.value + "|";
                imageWidth = e.style.width + "|";
              } else if (e.tagName == "TEXTAREA") {
                if ((e.value || e.textContent)) {
                  var des = e.value ?? e.textContent;
                  document.getElementById("db_save_estim_des").value += (des.trim().replace(/\\n/g, "<br />")) + "|";
                } else {
                  document.getElementById("db_save_estim_des").value += "|";
                }
              }
            }
          }
          document.getElementById("db_save_estim_image_product").value += (imageProduct ? imageProduct : '|');
          document.getElementById("db_save_estim_image_width").value += (imageWidth ? imageWidth : '|');
        }
    }

}

// * Calculate TVA line
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

// * Calculate table resume and TVA table
function drawDownTab() {
    var tableResume = document.getElementById("tableauResume");
    tab = tableResume.firstElementChild;
    while (tab.lastChild != tab.firstElementChild) {
      tab.removeChild(tab.lastChild);
    }

    var tableauProduits = document.getElementById("tableauProduits");
    var discount_pct = document.getElementById("discount_pct").value;
    var tauxArray = [];

    for (var i = 1; i < tableauProduits.rows.length; i++) { // For all products
        var xBBE = tableauProduits.rows[i].cells.length > 10 ? 2 : 0; // Add 2 columns if BBE

        if (tableauProduits.rows[i].cells[6 + xBBE]) {
            var tauxTax = parseFloat((tableauProduits.rows[i].cells[6 + xBBE].innerHTML.split('%'))[0]);
            if (!tauxTax) tauxTax = 0;

            if (tauxArray.includes(tauxTax)) { // If taux exists
                var tvaLign = tableauProduits.rows[i].cells[7 + xBBE].innerHTML.split(' ')[0].replace(',', '');
                if (!(tvaLign)) tvaLign = 0;

                var lignHT =  tableauProduits.rows[i].cells[8 + xBBE].dataset.value.split(' ')[0].replace(',', '');
                lignHT = lignHT - (lignHT * discount_pct / 100);
                tvaLign = tvaLign - (tvaLign * discount_pct / 100);
                changeTax(tauxTax, lignHT, tvaLign);

            }
            else if (tableauProduits.rows[i].cells[8 + xBBE].innerHTML.split(' ')[0]) {
                tauxArray.push(tauxTax);

                var row = tableResume.insertRow(1);
                row.id = tauxTax;

                var tvaLign = parseFloat(tableauProduits.rows[i].cells[7 + xBBE].innerHTML.split(' ')[0].replace(',', ''));
                if (!tvaLign) tvaLign = 0;
                tvaLign = tvaLign - (tvaLign * discount_pct / 100);

                var lignHT = parseFloat(tableauProduits.rows[i].cells[8 + xBBE].dataset.value.split(' ')[0].replace(',', ''));
                lignHT = lignHT - (lignHT * discount_pct / 100);
                if (isNaN(lignHT) || lignHT == '') lignHT = 0;

                var Ttt = parseFloat(tvaLign) + parseFloat(lignHT);
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                cell1.innerHTML = toPrice(lignHT);
                cell1.dataset.price = lignHT;
                cell2.innerHTML = tauxTax + ' %';
                cell3.innerHTML = toPrice(tvaLign);
                cell4.innerHTML = toPrice(Ttt);
                cell3.dataset.price = tvaLign;
            }
          }
      }

    var tableFacture = document.getElementById("tbody_ant_fact");
    if (tableFacture) {
        for (let i = 0; i < tableFacture.rows.length; i++) {
          const e = tableFacture.rows[i];
          let tauxTax = parseFloat(e.cells[2].innerHTML.split('%')[0]);
          if (!tauxTax) {
            tauxTax = 0;
          }

          let lignHT = e.cells[1].innerHTML.split(' ')[0].replace(',', '');
          if (!(lignHT))
            lignHT = 0;
          let tvaLign = e.cells[3].innerHTML.split(' ')[0].replace(',', '') - lignHT;
          if (!(tvaLign))
            tvaLign = 0;
          else
            tvaLign = tvaLign.toFixed(2);

          if (tauxArray.length && tauxArray.includes(tauxTax)) {
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
              cell3.innerHTML = -tvaLign + " €";
              cell4.innerHTML = toPrice(-Ttt);
          }
        }
    }
}

// * Create a new product
function newProduct() {
  var new_name = document.getElementById('new_name');
  var new_uni = document.getElementById('new_uni');
  var desc = descriptionInFacture ? '|' : '';
  if (location.host == "bois-buche-energie.com") {
    var value = `|${new_name.value}|0.0|${new_uni.value}|0||0|0||0|0|0${desc}`;
  } else {
    var value = `|${new_name.value}|0.0|${new_uni.value}|0||0|0||0${desc}`;
  }
  $('#select-product').append(`<option value="${value}">${new_name.value}</option>`);
  Metro.getPlugin("#select-product", 'select').data($('#select-product').html());
  addProductList(value);
  new_name.value = "";
  Metro.getPlugin("#new_uni", 'select').val('');
  setTimeout(() => document.getElementById('add_new_product').disabled = true, 1000);
}

// * Select product in add product modal
function fnAddProduct(pid) {
  var pdets = pid.split("|");
  if (pdets.length > 4) {
    document.getElementById("prx").value = pdets[2];
    Metro.getPlugin("#tvaedit", 'select').val(pdets[4]);
    if (familyInFacture)
      Metro.getPlugin("#familyedit", 'select').val('');
    Metro.getPlugin("#subscription_interval", 'select').val(pdets[6]);
    document.getElementById("subscription_interval_count").value = pdets[7];
    document.getElementById("product_discount").value = (pdets[9] != 'NULL') ? pdets[9] : '';
    $("#listProductAdd").removeClass('disabled');
  }
}

function selectProduct(event) {
  fnAddProduct(event.target.value);
}

var TTF, TIT, TVA, vol_st, vol_m3;

function loadFamily(id, child, parent = "") {
  var elem = '#family-' + id + (parent ? '-' + parent.replace(/[|]/g, '-') : '');
  if ($(elem)[0].childElementCount == 0 && $(elem+'-status')[0].textContent == "add") {
      var data = { familyProducts: id, child: (child + 1), parentList: parent };
      $.ajax({
        url: location,
      type: 'post',
      data,
      success: function (response) {
        $(elem).append(response);
        if ($(elem+'-status')[0]) {
          $(elem+'-status')[0].textContent = "remove";
        }
      }
    });
  } else {
    $(elem).empty();
    $(elem+'-status')[0].textContent = "add";
  }
}

function addProductList(value, family) {
  Metro.getPlugin("#select-product", 'select').val(value);
  fnAddProduct(value);
  if (familyInFacture) {
    console.log(family);
    if (family) {
      Metro.getPlugin("#familyedit", 'select').val(family);
    } else {
      Metro.getPlugin("#familyedit", 'select').val('');
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  TTF = parseFloat(document.getElementById("sousTHT").innerHTML.replace(',', ''));
  TIT = parseFloat(document.getElementById("disp_livr_ttc").innerHTML.replace(',', ''));
  TVA = parseFloat(document.getElementById("tva").innerHTML.replace(',', ''));
  vol_st = 0.00;
  vol_m3 = 0.00;

  if (document.getElementById("client"))
    document.getElementById("client").selectedIndex = -1;
  if (document.getElementById("c_lname") && document.getElementById("new_client") && document.getElementById("c_lname").value) {
    document.getElementById("new_client").checked = false;
  } else if (document.getElementById("new_client")) {
    document.getElementById("new_client").checked = true;
  }

  // Delete img with error
  document.querySelectorAll('img').forEach(function(img){
    if (img.id != 'devis-image') {
      img.onerror = function(){this.remove();};
    }
  })
  drawDownTab();

  if (document.getElementById("remise_pct")) {
    if (document.getElementById("remise_val").value != 0 || document.getElementById("remise_pct").value != 0) {
      displayRemise();
    }
    if (document.getElementById("acompte_val").value != 0) {
      displayAcompte();
    }
    recalcTotal();
    setTimeout(() => {recalcTotal()}, 1000);

    var families = document.querySelectorAll('*[id^="family_"]');
    families.forEach((f) => p_family.push(f.id));
  } else {
    v_remise = document.getElementById("discount_val") ? parseFloat(document.getElementById("discount_val").value) : 0;
    v_remise = isNaN(v_remise) ? 0 : v_remise;
    v_acompte = document.getElementById("acompte_val") ? parseFloat(document.getElementById("acompte_val").textContent) : 0;
    v_acompte = isNaN(v_acompte) ? 0 : v_acompte;
    document.getElementById("sousTTF").innerHTML = toPrice(TTF - v_remise - v_acompte);
  }

}, false);

function removeAntFact() {
    document.getElementById("table_ant_fact").remove();
    recalcTotal();
    document.getElementById("db_save_estim_no_fact_ant").value = true;
}

function emptyProducts() {
    var products = document.getElementById('tbodyProduits').rows;
    if (products.length > 1) {
      for (let i = 0; i < products.length; i++) {
        products[0].remove();
      }
    }
}

function selectInsertPlace(event, addButton = false) {
  var tr = event.target.parentElement.parentElement;
  if (tr.nodeName == 'TR') {
    var tbodyProducts = event.target.parentElement.parentElement.parentElement.children;
  } else {
    tr = tr.parentElement;
    var tbodyProducts = event.target.parentElement.parentElement.parentElement.parentElement.children;
  }
  var pos = Array.prototype.indexOf.call(tbodyProducts, tr);
  if (addButton) {
    Metro.getPlugin("#insertPlace", 'select').val(pos + 1);
  } else {
    Metro.getPlugin("#insertPlace", 'select').val(pos + 2);
  }
}

function insertFamily(event) {
  var tr = event.target.parentElement.parentElement;
  if (tr.nodeName == 'TR') {
    var tbodyProducts = event.target.parentElement.parentElement.parentElement.children;
  } else {
    tr = tr.parentElement;
    var tbodyProducts = event.target.parentElement.parentElement.parentElement.parentElement.children;
  }
  var pos =  Array.prototype.indexOf.call(tbodyProducts, tr);
  for (var i = pos; i < tbodyProducts.length; i++) {
    const e = tbodyProducts[i];
    if (e.classList.contains('add-product-bar')) {
      pos = i;
      break;
    }
  }
  var timestamp = new Date().getTime();
  newFamily("family_"+timestamp, 'Nouvelle section', false, pos + 2);
  if (document.getElementById('tbodyProduits').rows.length < 5) {
    setTimeout(() => replaceProductsBar(), 200)
  }
  recalcTotal();
}

// * Redraw add product bars
function replaceProductsBar() {
  $('.add-product-bar').remove();
  var table = document.getElementById('tbodyProduits');
  var products = table.rows;
  let i;

  for (i = 0; i < products.length; i++) {
    const e = products[i];
    if (i > 0 && e.classList.contains('bg-dark')) {
      var row = table.insertRow(i);
      i += 1;
      row.classList.add('add-product-bar');
      var html ="<td colspan='20' id='add-product' class='p-1'>\
      <a class='btn-lg text-hover-theme cursor-pointer' data-toggle='modal' data-target='#listProduct' onclick='selectInsertPlace(event, \"true\")'>\
          <b>Ajouter un produit <i class='ml-2 ya ya-plus'></i></b>\
      </a>";
      if (familyInFacture) {
        html += "<a class='btn-lg text-hover-theme cursor-pointer' onclick='insertFamily(event)'> <b>Ajouter une section <i class='ml-2 material-icons vertical-align'>library_add</i></b> </a>"
      }
      html += "</td>";
      $(row).html(html);
    }
  }
  var row = table.insertRow(i);
  i += 2;
  row.classList.add('add-product-bar');
  html ="<td colspan='20' id='add-product' class='p-1'>\
  <a class='btn-lg text-hover-theme cursor-pointer' data-toggle='modal' data-target='#listProduct' onclick='selectInsertPlace(event, \"true\")'>\
      <b>Ajouter un produit <i class='ml-2 ya ya-plus'></i></b>\
  </a>";
  if (familyInFacture) {
    html += "<a class='btn-lg text-hover-theme cursor-pointer' onclick='insertFamily(event)'> <b>Ajouter une section <i class='ml-2 material-icons vertical-align'>library_add</i></b> </a>"
  }
  html += "</td>";
  $(row).html(html);
}

// * Shortcut to create a facture with a selected contact or tier
$(window).on("load", function () {
  if (window.location.search) {
    var search = window.location.search.split("=");
    if (search[0] == "?contact") {
        $.ajax({
          url: "/Administration/Contacts",
          data: { search: search[1], filter: 'contacts_accounts', type: 'id_contact' },
          dataType: "json",
          method: "POST",
          success: function (data) {
            document.getElementById("c_lname").value = data[0].nom;
            document.getElementById("c_fname").value = data[0].prenom;
            document.getElementById("c_address").value = data[0].address;
            document.getElementById("c_address_complement").value = data[0].address_complement;
            document.getElementById("c_town").value = data[0].ville;
            document.getElementById("c_pcode").value = data[0].cp;
            document.getElementById("c_country").value = data[0].pays;
            document.getElementById("c_email").value = data[0].email;
            document.getElementById("c_civilite").value = data[0].civilite;
            document.getElementById("id_contact").value = data[0].id_contact;
            document.getElementById("c_address_complement").value = data[0].address_complement;
            document.getElementById("new_client").checked = false;
          }
        });
    }
    if (search[0] == "?TI") {
      $('#professionnel').click();
      $.ajax({
        url: "/Administration/Contacts",
        data: { search: search[1], filter: 'tiers', type: 'id_tier' },
        dataType: "json",
        method: "POST",
        success: function (data) {
          document.getElementById("id_tier").value = data[0].id_tier;
          document.getElementById("denomination").value = data[0].denomination;
          document.getElementById("n_siret").value = data[0].siret;
          document.getElementById("t_address").value = data[0].address;
          document.getElementById("t_address_complement").value = data[0].address_complement;
          document.getElementById("t_pcode").value = data[0].cp;
          document.getElementById("t_boite_postale").value = data[0].boite_postale;
          document.getElementById("t_town").value = data[0].ville;
          document.getElementById("t_country").value = data[0].pays;
          document.getElementById("numero_tva").value = data[0].numero_tva;
          document.getElementById('representant_legal_id').value = data[0].representant_legal_id;
          document.getElementById('representant_legal_search').value = data[0].representant_legal_name;
          document.getElementById("forme_juridique_select").value = data[0].forme_name;
          document.getElementById("t_email").value = data[0].email;
        }
      });
    }
  }
});

// * Keyboard shortcuts
document.addEventListener('keydown', (event) => {
  if (event.key === "Escape") {
      $('#addProductClose').click();
  } else if (event.key === "Enter") {
      if ($('#listProduct')[0].className.contains('show') !== false) {
        $('#listProductAdd').click();
      }
  } else if (event.key === "+") {
    if (event.target.tagName !== "INPUT")
    $('#listProduct').modal('show');
  }
});

// * On edit text, recalc family
$('body').on('blur', '[contenteditable]', () => recalcTotal());
