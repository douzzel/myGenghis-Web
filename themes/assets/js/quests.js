let questTool = null;

const startQuest = (quest, step = "1") => {
	if (questTool) {
		questTool.complete();
	}
	localStorage.setItem('questStep', step);
	localStorage.setItem('questCurrent', quest);

	switch (quest) {
		case 'quest_product':
			questProduct(step);
			break;
		case 'quest_family':
			questFamily(step);
			break;
		case 'quest_category':
			questCategory(step);
			break;
		case 'quest_devis':
			questDevis(step);
			break;
		case 'quest_facture_status':
			questFactureStatus(step);
			break;
		case 'quest_documents':
			questDocuments(step);
			break;
		case 'quest_contacts':
			questContacts(step);
			break;
		case 'quest_membres':
			questMembres(step);
			break;
		case 'quest_reservations':
			questReservations(step);
			break;
		case 'quest_click_and_collect':
			questClickAndCollect(step);
			break;
		case 'quest_article':
			questArticles(step);
			break;
		case 'quest_vente_settings':
			questVenteSettings(step);
			break;
		case 'quest_web':
			questWeb(step);
			break;
		default:
			break;
	}
	hideQuestMenu();
}

const hideQuestMenu = () => {
	$('.quest-panel').hide();
}
const showQuestMenu = () => {
	$('.quest-panel').show();
}

const endQuest = () => {
	localStorage.removeItem('questStep');
	localStorage.removeItem('questCurrent');
}

String.prototype.replaceAt = (index, char) => {
    var a = this.split("");
    a[index] = char;
    return a.join("");
}

const finishQuest = () => {
	questTool.complete();
	$.ajax({
		url: "/Administration",
		data: { quest_update: localStorage.getItem('questCurrent') },
		dataType: "json",
		method: "POST"
	});

	 /* Add experience and validate quest */
	const quest = localStorage.getItem('questCurrent');
	if ($('#'+quest+' .icon-status').text() == 'star_rate') {
		$('#'+quest).addClass('validated');
		$('#'+quest+' .icon-status').prop('title', 'Quête validée');
		$('#'+quest+' .icon-status').text('check');
		let experience = $('#quest-experience').data().value + (100 / 8);
		if (experience >= 100) {
			$('#quest-level').text(parseInt($('#quest-level').text()) + 1)
			experience = 0;
		}
		Metro.getPlugin('#quest-experience', "progress").val(experience);
		$('#quest-experience').data().value = experience;
		$('#quest-experience').prop('title', experience+'%');
		const questNumber = $('#'+quest).parent().find('.quest-count').text();
		$('#'+quest).parent().find('.quest-count').text(questNumber.replaceAt(0, parseInt(questNumber[0]) + 1));
	}
	localStorage.removeItem('questStep');
	localStorage.removeItem('questCurrent');
}

const nextQuest = () => {
	questTool.next();
}

const previousQuest = () => {
	questTool.back();
}

const startQuestTool = (steps, step, title) => {
	questTool = new Shepherd.Tour({
		defaultStepOptions: {
		  classes: 'drop-shadow bg-white',
		  scrollTo: true,
		  title: title,
		  buttons: [
			  { text: 'Suivant', action: nextQuest }
		  ],
		  cancelIcon: {enabled: true},
		  scrollTo: {behavior: 'smooth', block: 'center'}
		},
		steps,
		confirmCancel: true,
		confirmCancelMessage: 'Êtes vous sûr de vouloir arrêter la quête ?'
	  });
	  questTool.on('show', () => {
		  setTimeout(() => {
			if (questTool.currentStep) {
				questTool.steps.every((e, i) => {
					if (questTool.currentStep.id == e.id) {
						localStorage.setItem('questStep', i + 1);
						return false;
					}
					return true;
				});
			}
		}, 500);
	});
	questTool.on('cancel', () => endQuest());
	questTool.start();
	if (step) {
		questTool.show(step - 1);
	}

}

const setQuestStep = (step) => localStorage.setItem('questStep', step);

jQuery(() => {
	const currentQuest = localStorage.getItem('questCurrent');
	if (currentQuest) {
		const questStep = localStorage.getItem('questStep') ?? "1";
		localStorage.setItem('questStep', 3);
		startQuest(currentQuest, questStep);
	}
});

const questProduct = (step) => {
	const steps = [{
				text: 'Les produits permettent de réaliser des factures ou de vendre depuis votre e-commerce.'
			},{
				attachTo: {on: 'auto', element: "#graph_menu_vente"},
				text: 'Pour commencer, ouvrez le menu <b>vente</b> en passant la souris au dessus de <i class="material-icons vertical-align">store</i>',
				buttons: [],
				advanceOn: {selector: '#graph_menu_vente', event: 'mouseenter'}
			}, {
				attachTo: {on: 'auto', element: "#graph_menu_product"},
				text: 'Ensuite, affichez la liste des produits en cliquant sur <i class="material-icons vertical-align">shopping_cart</i>',
				buttons: []
			}, {
				attachTo: {on: 'auto', element: "#btn_add_product"},
				text: 'Sur cette page vous retrouvez l\'ensemble de vos produits. <br/> Cliquez sur le bouton <i class="material-icons vertical-align">add_circle_outline</i> pour en ajouter un nouveau.',
				buttons: []
			}, {
				text: 'Remplissez les informations de votre produit puis appuyer sur le bouton <i class="material-icons vertical-align">save</i> pour l\'enregister.',
				buttons: [
					{ text: "J'ai compris", action: finishQuest }
				]
			}];
	$("#graph_menu_product").on("click", () => setQuestStep(4));
	$("#btn_add_product").on("click", () => setQuestStep(5));
	startQuestTool(steps, step, '<i class="material-icons align-middle">shopping_cart</i> Produit');
}

const questFamily = (step) => {
	const steps = [{
			text: 'Vos produits sont organisés en <i class="material-icons vertical-align">layers</i> <b>catégories</b> puis en <i class="material-icons vertical-align">group_work</i> <b>familles</b>. <br/>\
					Par exemple, vous pouvez avoir une catégorie <b>Salle de bain</b> avec les familles <b>Robinets</b>, <b>Miroirs</b> et <b>Douches</b>. <br/>\
					Ou la catégorie <b>T-shirts</b> avec les familles <b>Manches courtes</b>, <b>Manches longues</b> et <b>Débardeur</b>. <br/>\
					Vous organiserez ensuite vos produits dans les différentes familles et catégories.'
		},{
			attachTo: {on: 'auto', element: "#graph_menu_vente"},
			text: 'Pour commencer, ouvrez le menu <b>vente</b> en passant la souris au dessus de <i class="material-icons vertical-align">store</i>',
			buttons: [],
			advanceOn: {selector: '#graph_menu_vente', event: 'mouseenter'}
		}, {
			attachTo: {on: 'auto', element: "#graph_menu_family"},
			text: 'Ensuite, affichez la liste des familles en cliquant sur <i class="material-icons vertical-align">group_work</i>',
			buttons: []
		}, {
			attachTo: {on: 'auto', element: "#btn_add_family"},
			text: 'Sur cette page vous retrouvez l\'ensemble de vos familles. <br/> Cliquez sur le bouton <i class="material-icons vertical-align">add_circle_outline</i> pour en ajouter une nouvelle.',
			buttons: [],
			advanceOn: {selector: '#btn_add_family', event: 'click'}
		}, {
			text: 'Remplissez les informations de votre famille puis appuyer sur le bouton <i class="material-icons vertical-align">save</i> pour l\'enregister.',
			buttons: [
				{ text: "J'ai compris", action: finishQuest }
			]
		}];
	$("#graph_menu_family").on("click", () => setQuestStep(4));
	startQuestTool(steps, step, '<i class="material-icons align-middle">group_work</i> Famille');
}


const questCategory = (step) => {
	const steps = [{
			text: 'Vos produits sont organisés en <i class="material-icons vertical-align">layers</i> <b>catégories</b> puis en <i class="material-icons vertical-align">group_work</i> <b>familles</b>. <br/>\
					Par exemple, vous pouvez avoir une catégorie <b>Salle de bain</b> avec les familles <b>Robinets</b>, <b>Miroirs</b> et <b>Douches</b>. <br/>\
					Ou la catégorie <b>T-shirts</b> avec les familles <b>Manches courtes</b>, <b>Manches longues</b> et <b>Débardeur</b>. <br/>\
					Vous organiserez ensuite vos <i class="material-icons vertical-align">shopping_cart</i> <b>produits</b> dans les différentes <i class="material-icons vertical-align">group_work</i> <b>familles</b> et <i class="material-icons vertical-align">layers</i> <b>catégories</b>.'
		},{
			attachTo: {on: 'auto', element: "#graph_menu_vente"},
			text: 'Pour commencer, ouvrez le menu <b>vente</b> en passant la souris au dessus de <i class="material-icons vertical-align">store</i>',
			buttons: [],
			advanceOn: {selector: '#graph_menu_vente', event: 'mouseenter'}
		}, {
			attachTo: {on: 'auto', element: "#graph_menu_category"},
			text: 'Ensuite, affichez la liste des catégories en cliquant sur <i class="material-icons vertical-align">layers</i>',
			buttons: []
		}, {
			attachTo: {on: 'auto', element: "#btn_add_category"},
			text: 'Sur cette page vous retrouvez l\'ensemble de vos catégories. <br/> Cliquez sur le bouton <i class="material-icons vertical-align">add_circle_outline</i> pour en ajouter une nouvelle.',
			buttons: [],
			advanceOn: {selector: '#btn_add_category', event: 'click'}
		}, {
			text: 'Remplissez les informations de votre catégorie puis appuyer sur le bouton <i class="material-icons vertical-align">save</i> pour l\'enregister.',
			buttons: [
				{ text: "J'ai compris", action: finishQuest }
			]
		}];
	$("#graph_menu_category").on("click", () => setQuestStep(4));
	startQuestTool(steps, step, '<i class="material-icons align-middle">layers</i> Catégorie');
}

const questDevis = (step) => {
	const steps = [{
		text: 'Vous pouvez depuis myGenghis créer des devis et des factures grâce à vos produits et votre liste de contacts.'
	},{
		attachTo: {on: 'auto', element: "#graph_menu_perf"},
		text: 'Pour commencer, ouvrez le menu <b>performance</b> en passant la souris au dessus de <i class="material-icons vertical-align">star</i>',
		buttons: [],
		advanceOn: {selector: '#graph_menu_perf', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_devis"},
		text: 'Ensuite, affichez la liste des devis en cliquant sur <i class="material-icons vertical-align">receipt</i>',
		buttons: []
	}, {
		attachTo: {on: 'auto', element: "#btn_add_devis"},
		text: 'Sur cette page vous retrouvez l\'ensemble de vos devis. <br/> Cliquez sur le bouton <i class="material-icons vertical-align">add_circle_outline</i> pour en créer un nouveau.',
		buttons: []
	}, {
		attachTo: {on: 'auto', element: "#container_select_part_pro"},
		text: 'Vous avez le choix entre un devis pour un <b>particulier</b> ou un <b>professionel</b>.'
	}, {
		attachTo: {on: 'auto', element: "#client"},
		text: 'Utilisez la barre de recherche pour retrouver un de vos <i class="material-icons vertical-align">perm_contact_calendar</i> <b>contacts</b> existant ou remplissez directement ses informations.<br/><br/>Si la <b>case « Nouveau contact » est coché</b>, les informations seront <b>enregistrés dans votre base de contacts</b>.'
	}, {
		attachTo: {on: 'auto', element: "#add-product"},
		text: 'Ajoutez les <i class="material-icons vertical-align">shopping_cart</i> <b>produits</b> de votre devis en cliquant ici. Vous pouvez organiser les produits sur votre devis grâce aux sections. <br/><br/>\
		 Commencez par ajouter un produit avec le bouton <b>Ajouter un produit</b><i class="ya ya-plus ml-2"></i>',
		 buttons: []
	}, {
		attachTo: {on: 'auto', element: "#container-search-product"},
		text: 'Utilisez la barre de recherche pour retrouver un de vos <i class="material-icons vertical-align">shopping_cart</i> <b>produits</b> existant.'
	}, {
		attachTo: {on: 'auto', element: "#modalAddProduct"},
		text: 'Vous pouvez aussi retrouver vos <i class="material-icons vertical-align">shopping_cart</i> <b>produits</b> organisés par <i class="material-icons vertical-align">layers</i> <b>catégories</b> et <i class="material-icons vertical-align">group_work</i> <b>familles</b> sur la barre de gauche.'
	}, {
		attachTo: {on: 'auto', element: 'a[data-target="#newProduct"]'},
		text: 'Il est possible de créer un nouveau <i class="material-icons vertical-align">shopping_cart</i> <b>produit</b> rapidement en cliquant sur ce bouton.'
	}, {
		attachTo: {on: 'auto', element: '#windowAddProduct'},
		text: 'Une fois votre produit sélectionné, vérifier les informations de votre produit, sélectionnez l\'endroit où l\'insérer puis cliquez sur le bouton <b>Ajouter</b>.',
		buttons: [],
		advanceOn: {selector: '#listProductAdd', event: 'click'}
	}, {
		attachTo: {on: 'auto', element: '#tbodyProduits'},
		text: 'Les produits ajoutés se retrouvent ici. Vous pouvez directement <b>modifier</b> le <b>prix</b>, la <b>quantité</b>, la <b>remise</b> et la <b>TVA</b> en cliquant sur les nombres de la ligne. <br/>\
			Il est possible de <b>réorganiser les produits</b> en restant appuyé sur le <b>numéro du produit</b>. <br/><br/>\
			En passant la souris sur la ligne du produit, vous disposé de 2 actions sur la droite du produit : <br/>\
			<ul class="pl-3"><li><i class="ya ya-plus"></i> permet d\'<b>insérer</b> un nouveau produit après celui-ci.</li>\
			<li><i class="material-icons vertical-align">delete</i> permet de le <b>supprimer</b> du devis.</li></ul>'
	}, {
		attachTo: {on: 'auto', element: 'a[data-target="#remise"]'},
		text: 'Avec ce bouton, vous pouvez ajouter une <b>remise en € ou en %</b> sur le montant HT de votre devis.'
	}, {
		attachTo: {on: 'auto', element: 'a[data-target="#acompte"]'},
		text: 'Ici vous pouvez rajouter un <b>acompte</b>. Un acompte est un paiement partiel qui survient lors de la conclusion du contrat de vente ou après sa conclusion.'
	}, {
		attachTo: {on: 'auto', element: '#devis-image'},
		text: 'Vous pouvez insérer <b>une image ou une vidéo</b> personnaliser sur votre devis. L\'image par défaut est modifiable depuis les paramètres de votre plateforme.<br/><br/>\
		Vous disposez aussi d\'un <b>champ texte en haut et en bas</b> de votre devis pour rajouter toutes informations utile à votre client.'
	}, {
		attachTo: {on: 'auto', element: '#devis-save-buttons'},
		text: 'Une fois votre devis complété, il est nécessaire de le <b>sauvegarder</b> grâce aux boutons : <br/>\
		<ul class="pl-3"><li><i class="material-icons text-info vertical-align mr-2">rule_folder</i>Enregistre le devis en <b>Brouillon</b>, il restera <b>modifiable</b>.</li>\
		<li><i class="material-icons text-success vertical-align mr-2">save</i>Enregistre le devis, il restera <b>modifiable</b> mais ne sera <b>plus supprimable</b>.</li></ul>',
		buttons: [
			{ text: "J'ai compris", action: finishQuest }
		]
	}];
	$("#graph_menu_devis").on("click", () => setQuestStep(4));
	$("#btn_add_devis").on("click", () => setQuestStep(5));
	$('#add-product a[data-target="#listProduct"]').on("click", () => setTimeout(() => questTool.next(), 500));

	if (step >= 8 && step <= 11) {
		$('#add-product a[data-target="#listProduct"]').click();
	}
	startQuestTool(steps, step, '<i class="material-icons align-middle">receipt</i> Devis');
}

const questFactureStatus = (step) => {
	const steps = [{
		text: 'Les différents statuts des devis :<br/>\
			<ul class="pl-3"><li><b class="text-info">Brouillon</b>, le devis est <b>modifiable et supprimable</b>. Il s\'agit d\'un devis pas encore envoyé au client.</li>\
			<li><b class="text-warning">En cours</b>, le devis à été ou va être <b>communiqué au client</b>. Il est <b>toujours modifiable</b> mais n\'est <b>plus supprimable</b>. Il peut-être transformé en devis <span class="text-success">validé</span> ou <span class="text-danger">perdu</span></li>\
			<li><b class="text-success">Validé</b>, le <b>client a accepté</b> le devis. Il peut-être transformé en facture.</li>\
			<li><b class="text-danger">Perdu</b>, le <b>client a refusé</b> le devis.</li>\
			</ul>'
	}, {
		title: '<i class="material-icons align-middle">request_quote</i> Facture',
		text: 'Les différents statuts des factures :<br/>\
			<ul class="pl-3"><li><b class="text-warning">En cours</b>, la facture est <b>en cours de traitement</b> avec le client.</li>\
			<li><b class="text-success">Terminée</b>, la facture est terminée et les <b>actions avec le client sont terminées</b>.</li>\
			<li><b>Livraison</b>, la facture est <b>en cours de livraison</b>.</li>\
			</ul>\
			Une facture peut aussi <b>être acquitée</b> ou non. Il est possible de modifier ce statut <b>directement sur la facture</b>.',
			buttons: [
				{ text: "Précédent", action: previousQuest },
				{ text: "J'ai compris", action: finishQuest }
			]
	}];
	startQuestTool(steps, step, '<i class="material-icons align-middle">receipt</i> Devis');
}

const questDocuments = (step) => {
	const steps = [{
		text: 'Votre plateforme permet de <b>stocker des documents</b> pour vous et ainsi que pour vos employés. Vous pouvez aussi créer des <b>liens de partage</b> afin de communiquer un document avec vos clients.'
	},{
		attachTo: {on: 'auto', element: "#graph_menu_gouv"},
		text: 'Pour commencer, ouvrez le menu <b>gouvernance</b> en passant la souris au dessus de <i class="material-icons vertical-align">business_center</i>',
		buttons: [],
		advanceOn: {selector: '#graph_menu_gouv', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_document"},
		text: 'Ensuite, ouvrez l\'espace de documents en cliquant sur <i class="material-icons vertical-align">description</i>',
		buttons: []
	}, {
		text: 'Les documents sont <b>organisés par 	<i class="material-icons vertical-align">bookmark</i> sections</b>. Les sections par défaut sont Communication, Performance, Gouvernance et Marketing.<br/>\
		Vous pouvez en ajouter de nouvelle grâce au menu en bas à droite. Vous pouvez les renommer en cliquant sur leur nom.'
	}, {
		text: 'Les <b>dossiers permettent d\'organiser vos différents liens et fichiers</b>. <br/><br/>Les documents sont uniquement des <b>documents texte</b>. Pour tout autre documents, il vous est possible d\'utiliser votre drive. <br/><br/> Les <b>liens</b> permettent d\'ajouter des <b>raccourcis</b> vers des sites internet.'
	}, {
		attachTo: {on: 'auto', element: '.breadcrumb'},
		text: 'Il est possible de <b>déplacer</b> les fichiers et les dossiers <b>en les glissant déposant</b> dans d\'autres sections ou dossier. Vous pouvez les déplacer dans le dossier précédant en les déposant dans le fil d\'Arianne.'
	}, {
		text: 'Les fichiers peuvent être <b>partagés à un utilisateur, un groupe ou un persona</b>. S\'ils sont partagés avec un groupe ou un persona, tous les utilisateurs liés à ses groupes ou personas auront accès au fichier.<br/><br/> Il est aussi possible de créer un <b>lien public</b> afin de pouvoir partager un lien avec des <b>personnes extérieures</b> à votre plateforme.',
		buttons: [
			{ text: "J'ai compris", action: finishQuest }
		]
	}];
	$("#graph_menu_document").on("click", () => setQuestStep(4));
	startQuestTool(steps, step, '<i class="material-icons align-middle">description</i> Documents');
}

const questContacts = (step) => {
	const steps = [{
		text: 'La base de contacts est une partie importante de votre plateforme. Vous allez ainsi pouvoir enregistrer tous vos <b>clients</b>, <b>prospects</b> et <b>fournisseurs</b> de votre entreprise.'
	},{
		attachTo: {on: 'auto', element: "#graph_menu_gouv"},
		text: 'Pour commencer, ouvrez le menu <b>gouvernance</b> en passant la souris au dessus de <i class="material-icons vertical-align">business_center</i>',
		buttons: [],
		advanceOn: {selector: '#graph_menu_gouv', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_contact"},
		text: 'Ensuite, ouvrez votre base de contacts en cliquant sur <i class="material-icons vertical-align">perm_contact_calendar</i>',
		buttons: []
	}, {
		text: 'Sur cette page, vous retrouvez l\'ensemble de vos contacts. <br/> Il est possible de les <b>trier par colonne</b> en <b>cliquant sur le titre</b> de la colonne souhaitée.'
	}, {
		attachTo: {on: 'auto', element: ".contact-search"},
		text: 'Vous pouvez <b>rechercher</b> un contact par <b>nom, adresse, email ou numéro de téléphone</b>.'
	}, {
		attachTo: {on: 'auto', element: "#filterForm"},
		text: 'Vous pouvez aussi <b>filtrer</b> vos contacts par <b>code postal, ville, type ou catégorie</b>.'
	}, {
		attachTo: {on: 'auto', element: "#btn_add_contact"},
		text: 'Cliquez sur le bouton <i class="material-icons vertical-align">add_circle_outline</i> pour en ajouter un nouveau.',
		buttons: [],
		advanceOn: {selector: '#btn_add_contact', event: 'click'}
	}, {
		text: 'Remplissez les informations de votre contacts puis appuyer sur le bouton <i class="material-icons vertical-align">save</i> pour l\'enregister.',
		buttons: [
			{ text: "J'ai compris", action: finishQuest }
		]
	}];
	$("#graph_menu_contact").on("click", () => setQuestStep(4));
	startQuestTool(steps, step, '<i class="material-icons align-middle">perm_contact_calendar</i> Contacts');
}

const questMembres = (step) => {
	const steps = [{
		text: 'Les membres correspondent aux <b>personnes pouvant se connecter à votre plateforme</b>. Les <b>clients</b> qui ont accès à votre réseau social d\'entreprise ou ceux ayant déjà passé commande ainsi que vos <b>employés</b> ou vos <b>partenaires de click & collect</b>.'
	},{
		attachTo: {on: 'auto', element: "#graph_menu_gouv"},
		text: 'Pour commencer, ouvrez le menu <b>gouvernance</b> en passant la souris au dessus de <i class="material-icons vertical-align">business_center</i>',
		buttons: [],
		advanceOn: {selector: '#graph_menu_gouv', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_membre"},
		text: 'Ensuite, ouvrez votre liste de membres en cliquant sur <i class="material-icons vertical-align">contacts</i>',
		buttons: []
	}, {
		text: 'Sur cette page, vous pouvez <b>modifier le groupe d\'un membre</b>. Les groupes permettent de <b>donner des droits et des permissions</b> à vos membres afin qu\'il ait accès à différentes parties de votre plateforme. Vous pouvez gérer les groupes ainsi que leurs droits depuis les paramètres de votre plateforme. <br/><br/>\
		Il est aussi possible de <b>relier un membre à un persona</b> ainsi que de définir un <b>membre comme point de click&collect</b>.'
	}, {
		text: 'Pour continuer, cliquez sur <i class="material-icons vertical-align">visibility</i> afin de consulter la <b>fiche d\'un membre</b>. Il s\'agit du même format de fiche que pour vos contacts.',
		buttons: []
	}, {
		attachTo: {on: 'auto', element: "#fiche-informations"},
		text: 'Vous pouvez retrouver toutes les <b>informations</b> de votre fiche ici. Pour les modifier, appuyer sur le bouton <i class="material-icons vertical-align">edit</i>.'
	}, {
		attachTo: {on: 'auto', element: ".menu-btn"},
		text: 'Depuis le menu en bas à droite, vous pouvez <b>ajouter des liens ou des notes</b> sur votre fiche.'
	}, {
		text: 'Vous pourrez aussi retrouver les <b>mails, devis et factures</b> de votre membre sur cette page.',
		buttons: [
			{ text: "J'ai compris", action: finishQuest }
		]
	}];
	$("#graph_menu_membre").on("click", () => setQuestStep(4));
	$(".btn-link-fiche").on("click", () => setQuestStep(6));
	startQuestTool(steps, step, '<i class="material-icons align-middle">contacts</i> Membres');
}

const questReservations = (step) => {
	const steps = [{
			text: 'Le module de réservation permet à vos clients de <b>réserver des créneaux</b>. <br/>Les créneaux peuvent être utilisés comme <b>horaire de livraison, horaire d\'intervention, réservation d\'une table…</b>'
		},{
			attachTo: {on: 'auto', element: "#graph_menu_perf"},
			text: 'Pour commencer, ouvrez le menu <b>performance</b> en passant la souris au dessus de <i class="material-icons vertical-align">star</i>',
			buttons: [],
			advanceOn: {selector: '#graph_menu_perf', event: 'mouseenter'}
		}, {
			attachTo: {on: 'auto', element: "#graph_menu_reservations"},
			text: 'Ensuite, ouvrez la liste des réservations en cliquant sur <i class="material-icons vertical-align">book_online</i>',
			buttons: []
		}, {
			attachTo: {on: 'auto', element: "#reservation-header"},
			text: 'Sur cette page vous retrouvez vos <b>lieu de réservations</b> indiquer sur une <b>carte</b> ainsi que leurs réservations.<br/><br/>Vous pouvez <b>ajouter de nouveau lieu</b> de réservation <b>depuis les paramètres</b> de la plateforme, catégorie Store.'
		}, {
			text: 'En dessous de la carte se trouve toutes vos réservations.<br/>Une réservation est toujours <b>reliée à une facture</b>, vous pouvez les <b>modifier</b> grâce au bouton <i class="material-icons vertical-align">edit</i>. Une fois terminée, vous pouvez les <b>supprimer</b> avec le bouton <i class="material-icons vertical-align">delete</i>.'
		}, {
			attachTo: {on: 'auto', element: "#btn-add-reservation"},
			text: 'Les clients peuvent <b>effectuer des réservations depuis votre store</b> ou vous pouvez <b>ajouter manuellement</b> des réservations avec le bouton <i class="material-icons vertical-align">add_circle_outline</i>',
			buttons: [],
			advanceOn: {selector: '#btn-add-reservation', event: 'click'}
		}, {
			text: 'Remplissez les informations de votre nouvelle réservation puis appuyer sur le bouton <i class="material-icons vertical-align">save</i> pour l\'enregister.',
			buttons: [
				{ text: "J'ai compris", action: finishQuest }
			]
		}];
	$("#graph_menu_reservations").on("click", () => setQuestStep(4));
	startQuestTool(steps, step, '<i class="material-icons align-middle">book_online</i> Réservations');
}

const questClickAndCollect = (step) => {
	const steps = [{
			text: 'Le module de Click&Collect permet à vos clients de <b>commander en ligne</b> un colis puis de le <b>récupérer en magasin</b> ou dans un <b>point de retrait</b>.'
		},{
			attachTo: {on: 'auto', element: "#graph_menu_perf"},
			text: 'Pour commencer, ouvrez le menu <b>performance</b> en passant la souris au dessus de <i class="material-icons vertical-align">star</i>',
			buttons: [],
			advanceOn: {selector: '#graph_menu_perf', event: 'mouseenter'}
		}, {
			attachTo: {on: 'auto', element: "#graph_menu_click_and_collect"},
			text: 'Ensuite, ouvrez la liste des commandes en Click&Collect en cliquant sur <i class="material-icons vertical-align">mouse</i>',
			buttons: []
		}, {
			attachTo: {on: 'auto', element: "#click-collect-header"},
			text: 'Sur cette page, vous retrouvez vos <b>point de retrait en Click&Collect</b> indiquer sur une <b>carte</b> ainsi que leurs colis.<br/><br/>Un point de retrait est lié à un compte membre. Pour en ajouter un nouveau, allez dans la liste des membres puis sélectionnez <b>Partenaire Click&Collect</b> à <b>oui</b>.<br/><br/>Les partenaires auront accès à une <b>page sur leur espace membre</b> permettant de <b>gérer leurs colis</b>.'
		}, {
			text: 'En dessous de la carte se trouve tout vos Click&Collect.<br/>Vous pouvez les <b>modifier</b> grâce au bouton <i class="material-icons vertical-align">edit</i> et <b>mettre a jour leur statut</b> en cliquant directement sur leur statut.'
		}, {
			attachTo: {on: 'auto', element: "#btn-add-click-collect"},
			text: 'Les clients peuvent <b>effectuer des commandes en Click&Collect depuis votre store</b> ou vous pouvez <b>en ajouter manuellement</b> grâce au bouton <i class="material-icons vertical-align">add_circle_outline</i>',
			buttons: [],
			advanceOn: {selector: '#btn-add-click-collect', event: 'click'}
		}, {
			text: 'Remplissez les informations de votre Click&Collect puis appuyer sur le bouton <i class="material-icons vertical-align">save</i> pour l\'enregister.',
			buttons: [
				{ text: "J'ai compris", action: finishQuest }
			]
		}];
	$("#graph_menu_click_and_collect").on("click", () => setQuestStep(4));
	startQuestTool(steps, step, '<i class="material-icons align-middle">mouse</i> Click&Collect');
}

const questArticles = (step) => {
	const steps = [{
		text: 'Votre plateforme vous permet de <b>partager en ligne des articles et vidéos</b>. Ils peuvent être <b>accessible depuis votre site internet ou votre espace membre</b>.'
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_communication"},
		text: 'Pour commencer, ouvrez le menu <b>communication</b> en passant la souris au dessus de <i class="material-icons vertical-align">important_devices</i>',
		buttons: [],
		advanceOn: {selector: '#graph_menu_communication', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_site"},
		text: 'Ensuite, ouvrez la page <b>Web, Articles et Vidéos</b> en cliquant sur <i class="material-icons vertical-align">web</i>',
		buttons: []
	}, {
		attachTo: {on: 'auto', element: '#listArticles'},
		text: 'Ici, vous pouvez retrouver vos articles et vidéos. Cliquez sur <i class="material-icons vertical-align">add_circle_outline</i> pour en ajouter un nouveau.',
		buttons: []
	}, {
		attachTo: {on: 'right', element: '#publish_in'},
		text: 'Vous pouvez choisir de publier votre article à différents endroits.<br/>\
		<ul><li>Publier dans le blog = Publier sur votre <b>site Internet</b></li>\
			<li>Publier pour les membres = Publier dans l\'<b>espace membre</b></li>\
			<li>Publier sur Facebook = Publier sur la <b>page facebook</b> relié à votre plateforme</li>\
			<li>Publier sur LinkedIn = Publier sur la <b>page LinkedIn</b> relié à votre plateforme</li></ul><br/>\
		Vous pouvez connecter les pages Facebook et Linkedin à votre plateforme depuis les paramètres.<br/><br/>'
	}, {
		attachTo: {on: 'right', element: '#title_summary'},
		text: 'Ensuite remplissez le <b>titre</b> de votre article ainsi qu\'un court <b>résumé</b> de quelques lignes.'
	}, {
		attachTo: {on: 'auto', element: '#image'},
		text: 'Un article doit <b>contenir une image</b>. Vous pouvez <b>ajouter une de vos photos</b> ou vous pouvez <b>trouver une photo d\'illustration</b> libre de droit sur internet avec par exemple le site <a href="https://www.pexels.com/popular-searches" target="_blank">pexels.com</a>'
	}, {
		attachTo: {on: 'auto', element: '#content_container'},
		text: '<b>Rédigez votre article avec notre éditeur de texte</b>.<br/> Une fois terminé, appuyez sur <i class="material-icons vertical-align">save</i> pour <b>sauvegarder et publier votre article</b>.<br/> Si aucun lieu de publication n\'a été choisi, votre article sera sauvegardé en tant que brouillon.',
		when: {
			hide: () => {setQuestStep(9); this.location = '/Administration/Video'}
		}
	}, {
		attachTo: {on: 'bottom', element: '#video_iframe'},
		text: 'Pour les vidéos, le <b>fonctionnement est indique</b>. Cependant vous devez <b>charger une vidéo</b> ou mettre le <b>texte d\'un iframe</b> à la place du contenu de l\'article.',
		buttons: [
			{ text: "J'ai compris", action: finishQuest }
		]
	}];
	$("#graph_menu_site").on("click", () => setQuestStep(4));
	$("#btn_add_articles").on("click", () => setQuestStep(5));
	startQuestTool(steps, step, '<i class="material-icons align-middle">web</i> Articles et Vidéos');
}

const questVenteSettings = (step) => {
	const steps = [{
		attachTo: {on: 'auto', element: "#graph_menu_vente"},
		text: 'Pour organiser plus efficacement vos produits, vous disposez de différents <b>paramètres avancés</b>. Ses paramètres se trouvent dans le <b>menu vente</b> <i class="material-icons vertical-align">store</i> et dans les <b>paramètres de votre plateforme</b>.',
		buttons: [],
		advanceOn: {selector: '#graph_menu_vente', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_conditionnement"},
		text: 'Le <b>conditionnement du produit</b> s\'affichera sur vos facture et sur votre store.',
		buttons: []
	}, {
		text: 'Vous pouvez en ajouter ou supprimer ceux existant depuis cette page.'
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_vente"},
		text: 'Rouvrez le menu vente <i class="material-icons vertical-align">store</i>',
		buttons: [],
		advanceOn: {selector: '#graph_menu_vente', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_scenarios"},
		text: 'Les <b>scénarios</b> permettent d\'<b>afficher du texte sur votre panier</b> selon certains critères.',
		buttons: []
	}, {
		text: 'Sur cette page vous pouvez en ajouter de nouveaux.<br/>\
		Le <b>déclencheur de quantité</b> définit le <b>nombre minimum de produit</b> dans le panier pour afficher le message.</br>\
		Le <b>déclencheur de kilomètre</b> permet de définir un <b>nombre de kilomètres minimum et maximum</b> pour afficher le message.<br/><br/>\
		Un scénario peut-être actif ou inactif.'
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_vente"},
		text: 'Rouvrez le menu vente <i class="material-icons vertical-align">store</i>',
		buttons: [],
		advanceOn: {selector: '#graph_menu_vente', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_tarif"},
		text: 'Le tarif de livraison permet de <b>calculer automatiquement les frais de livraisons</b> sur votre store.',
		buttons: []
	}, {
		attachTo: {on: 'auto', element: "#type"},
		text: 'Les tarifs sont séparés en <b>tarif forfait</b> et <b>tarif kilomètre</b>. Changez les tarifs visibles grâce à ce sélecteur.'
	}, {
		text: 'Les tarifs peuvent être <b>appliqués</b> à différents produits <b>suivant</b> les paramètres comme <b>le conditionnement et la catégorie du produit</b>.<br/>Ensuite les tarifs sont <b>appliqués par zones de kilomètres minimum et maximum</b>.'
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_vente"},
		text: 'Rouvrez le menu vente <i class="material-icons vertical-align">store</i>',
		buttons: [],
		advanceOn: {selector: '#graph_menu_vente', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_category_tva"},
		text: 'Les catégories de TVA permettent de régler les différents <b>taux de TVA applicables</b> à vos produits.',
		buttons: []
	}, {
		text: 'Vous pouvez en <b>ajouter de nouveaux</b> taux ou <b>modifier/supprimer ceux existant</b> depuis cette page.'
	}, {
		attachTo: {on: 'auto', element: '.btn-avatar'},
		text: 'Ensuite ouvrez le menu paramères en passant la souris sur votre nom.',
		buttons: [],
		advanceOn: {selector: '.btn-avatar', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: '#graph_menu_settings'},
		text: 'Ouvrez les paramètres <i class="material-icons vertical-align">admin_panel_settings </i>',
		buttons: []
	}, {
		text: 'Dans l\'onglet <b>store</b> <i class="material-icons vertical-align">store</i> vous retrouvez la possibilité d\'<b>ajouter des champs personnalisés</b> pour vos produits. Ainsi que <b>modifier</b> certains <b>affichages et textes</b> de votre plateforme.',
		buttons: [
			{ text: "J'ai compris", action: finishQuest }
		]
	}];
	$("#graph_menu_conditionnement").on("click", () => setQuestStep(3));
	$("#graph_menu_scenarios").on("click", () => setQuestStep(6));
	$("#graph_menu_tarif").on("click", () => setQuestStep(9));
	$("#graph_menu_category_tva").on("click", () => setQuestStep(13));
	$("#graph_menu_settings").on("click", () => setQuestStep(16));
	startQuestTool(steps, step, '<i class="material-icons align-middle">settings</i> Paramètres de vente');
}

const questWeb = (step) => {
	const steps = [{
		text: 'Avec votre plateforme, il est possible de <b>créer un site internet grâce à des modèles</b> de pages proposés.'
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_communication"},
		text: 'Pour commencer, ouvrez le menu <b>communication</b> en passant la souris au dessus de <i class="material-icons vertical-align">important_devices</i>',
		buttons: [],
		advanceOn: {selector: '#graph_menu_communication', event: 'mouseenter'}
	}, {
		attachTo: {on: 'auto', element: "#graph_menu_site"},
		text: 'Ensuite, ouvrez la page <b>Web, Articles et Vidéos</b> en cliquant sur <i class="material-icons vertical-align">web</i>',
		buttons: []
	}, {
		attachTo: {on: 'auto', element: '#listWebPages'},
		text: 'Ici, vous pouvez retrouver vos pages web. Cliquez sur <i class="material-icons vertical-align">add_circle_outline</i> pour en créer une nouvelle.',
		buttons: []
	}, {
		attachTo: {on: 'auto', element: '.card-body'},
		text: 'Pour créer votre page, vous avez besoin du <b>titre de votre page, de son adresse ainsi que du modèle</b>.<br/>Vous pouvez mettre une <b>page en brouillon</b> et sélectionnant <b>« Invisible sur le site »</b>.'
	}, {
		attachTo: {on: 'auto', element: '#btn_display_templates'},
		text: 'Vous pouvez sélectionner le template voulu depuis ici. Pour une présentation des modèles, cliquez sur le bouton <b>« Aperçu des modèles »</b>.'
	}, {
		text: 'Une fois votre page créé, vous pourrez y <b>modifier ses paramètres</b> ainsi que les différents <b>blocs de textes et d\'images</b> qui la composent.',
		buttons: [
			{ text: "J'ai compris", action: finishQuest }
		]
	}];
	$("#graph_menu_site").on("click", () => setQuestStep(4));
	$("#btn_add_web_page").on("click", () => setQuestStep(5));
	startQuestTool(steps, step, '<i class="material-icons align-middle">settings</i> Site internet');
}
