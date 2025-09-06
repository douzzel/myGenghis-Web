
//* **************************GESTION PARTICULIER ET PRO************************************************* **
function modifyStyle(id, idOn, btnActive, btnDisabled, inputChoix, inputDisab ) {
	let blockOff = document.getElementById(id);
	let blockON = document.getElementById(idOn);
	let getbtnActive = document.getElementById(btnActive);
	let getbtnDisabled = document.getElementById(btnDisabled);
	if (document.getElementById(inputChoix))
		document.getElementById(inputChoix).value = true;
	if (document.getElementById(inputDisab))
		document.getElementById(inputDisab).value = false;

	var inputPartic = ["c_lname"];
	var inputProf = ["denomination"];
	if (inputChoix == 'choix_prof') {
	  inputPartic.map((e) => {
		  $("#"+e).removeAttr('required');
		  delete document.getElementById(e).dataset.validate;
	  });
	  inputProf.map((e) => {
		  $("#"+e).attr("required", "true");
		  document.getElementById(e).dataset.validate = "required";
	  });
	} else if (inputChoix == 'choix_partic') {
	  inputPartic.map((e) => {
		  $("#"+e).attr("required", "true");
		  document.getElementById(e).dataset.validate = "required";
		});
	  inputProf.map((e) => {
		  $("#"+e).removeAttr('required');
		  delete document.getElementById(e).dataset.validate;
		});
	}

	getbtnActive.setAttribute("class", "btn_action_choix active");
	getbtnDisabled.setAttribute("class", "btn_action_choix");
	blockOff.style.display = 'none';
	blockON.style.display = 'block';
  }

  function refreshList(elem) {
	var data = { refresh: elem };
	$.ajax({
	  url: location,
	  type: 'post',
	  data,
	  success: function (response) {
		  var id = '#'+elem;
		$(id).empty();
		$(id).append(response);
		Metro.getPlugin(id, 'select').data($(id).html());
	  }
	});

  }

  if (document.getElementById('particulier')) {
	document.getElementById('particulier').addEventListener("click", function(){
	  modifyStyle("detail_pro", "detail_particu", "particulier", "professionnel",  'choix_partic', 'choix_prof');
	}, false);
  }

  if (document.getElementById('professionnel')) {
	document.getElementById('professionnel').addEventListener("click", function(){
	  modifyStyle("detail_particu", "detail_pro", "professionnel", "particulier", 'choix_prof', 'choix_partic');
	  onclickPmPP( personne_physique, personne_moral);
	  managePP();
	}, false);
  }

  function managePP(){
	if(document.getElementById('personne_physique')){
	  let personne_physique = document.getElementById('personne_physique');
	  let personne_moral = document.getElementById('personne_moral');
	  personne_physique.addEventListener('click', function(){
		onclickPmPP( personne_physique, personne_moral);
	  }, false);
	  personne_moral.addEventListener('click', function(){
		onclickPmPP(personne_moral , personne_physique);
	  }, false);
	  if(personne_physique.checked == true){
		choice_personne('enseigne', '', 'forme_juridique');
	  }

	}
  }

  function onclickPmPP(on , off){
	on.checked = true;
	off.checked = false;
	if(personne_physique.checked == true){
	  choice_personne('enseigne', '', 'forme_juridique');
	  personne_moral.checked = false;
	}else{
	  personne_moral.checked = true;
	}
	if(personne_moral.checked == true ){
	  choice_personne('denomination', 'forme_juridique','');
	  personne_physique.checked = false;

	}else{
	  personne_physique.checked = true;
	}
  }

  function choice_personne(setOnInput, setSelectOn, setSelectOff){
	let label = document.getElementById('denomination-label');
	if (setOnInput == 'enseigne') {
	  label.textContent = '* Enseigne';
	} else {
	  label.textContent = decodeHTMLEntities('* Dénomination');
	}
	if (setSelectOn) {
		let selectOn = document.getElementById(setSelectOn);
		selectOn.setAttribute('class', 'd-block');
	}
	if (setSelectOff) {
		let selectOff = document.getElementById(setSelectOff);
		selectOff.setAttribute('class', 'd-none');
	}
  }

$( "#tbodyProduits" ).sortable({
	cursor: "move",
	items: "> .sortable",
	handle: ".handle",
	containment: document.getElementById('print'),
    sort: (event, ui) => ui.helper.css({
		'top' : ui.position.top - 180 + 'px',
		'left' : ui.position.left - document.getElementById('tableauProduits').getBoundingClientRect().left + 'px'}),
	start: (event) => event.target.classList.add('hide-add-product-bar'),
	stop: (event) => {
		event.target.classList.remove('hide-add-product-bar');
		recalcTotal();
		replaceProductsBar();
	}
});

function decodeHTMLEntities(str) {
	var element = document.createElement('div');
	if (str && typeof str === 'string') {
		str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
		str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
		element.innerHTML = str;
		str = element.textContent;
		element.textContent = '';
		return str;
	} else {
	  return '';
	}
  }
