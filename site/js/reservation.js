var datePicker;
window.addEventListener("load", function (event) {
	if (document.getElementById('res-loc')) {
	  window.addEventListener('hide.daterangepicker', (ev) => {
		$.ajax({
		  url: "/site/index.php",
		  method: "POST",
		  data: {reservationDate: ev.detail.startDate.format('YYYY-MM-DD'), reservationLocation: document.getElementById('res-loc').value},
		  dataType: "json",
		  success: (data) => {
			$('#res-time').empty();

			if (data.slot.length == 0) {
			  $('#res-time').html("<span class='text-warning res-option-text'>Aucun créneau disponible à cette date</span>");
			} else {
			  var html = "";
			  var old_time = 0;
			  var column_size = 60/data.duration <= 6 && 60/data.duration >= 1 ? Math.round(60/data.duration) : 4;
			  data.slot.forEach((e) => {
				if (data.duration <= 30 && old_time != e.split(':')[0]) {
				  if (old_time != 0)
					html += "</div>";
				  html += `<div class='res-option-hour' style='grid-template-columns: repeat(${column_size}, 1fr);'>`;
				}
				old_time = e.split(':')[0];
				html += `<div class='res-option'>${e}</div>`;
			  });
			  if (data.duration <= 30) {
				html += '</div>';
			  }
			  $('#res-time').html(html);

				if ($('.modal')) {
					$(".modal").animate({ scrollTop: $(document).height() }, 1000);
				}

			  $('.res-option').on('click', (e) => {
				$('.res-option').removeClass('selected');
				$(e.target).addClass('selected');
				$('#res-time-value').val(e.target.textContent);
				if ($('#btnResaDemo').length) {
					$('#btnResaDemo').show();
					if ($('.modal')) {
						$(".modal").animate({ scrollTop: $(document).height() }, 1000);
					}
				}
			  });
			}
		  },
		});
		return true;
	  });
	  selectLocation();
	}
});

window.addEventListener('contact-form', (ev) => {
	$('#container-contact-form').html(`
	<center><h3>Votre réservation est bien enregistrée.<br/></h3>
	<h4>${ev.detail.data.loc_name}<br/><a href="https://www.openstreetmap.org/search?query=${ev.detail.data.loc_address}" target="_blank"><u>${ev.detail.data.loc_address}</u></a></h4>
	<p>Le <b>${ev.detail.data.res_date}</b> à <b>${ev.detail.data.res_time}</b><br/> Vous recevrez prochainement un mail de confirmation.</p></center>`);
});

function selectLocation() {
  $.ajax({
	url: "/site/index.php",
	method: "POST",
	data: {reservationLocationSelect: document.getElementById('res-loc').value},
	dataType: "json",
	success: (data) => {
	  openDays = data.days.split(',');
	  if (!datePicker) {
		datePicker = new DateRangePicker('res-date', {
		  minDate: new Date(),
		  singleDatePicker: true,
		  isInvalidDate: (d) => {const date = moment(d); return !openDays.includes(date.day().toString())},
		  autoApply: true,
		  locale: {
			applyLabel: 'Valider',
			cancelLabel: 'Annuler',
			daysOfWeek: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
			monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			firstDay: 1,
			format: "YYYY-MM-DD",
			},
			clickApply: () => {;}
		});
	  } else {
		$('#res-time').html("<span class='text-warning res-option-text'>Sélectionnez une date</span>");
		datePicker.isInvalidDate = (d) => {const date = moment(d); return !openDays.includes(date.day().toString())};
	  }
	},
  });
}
