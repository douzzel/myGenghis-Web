$(document).ready(function() {
	md.initDashboardPageCharts();

  });
const validateTask = (event) => {
  $(`.validateTask[data-id='${event.target.dataset.id}']`).prop('checked', event.target.checked);
	$.ajax({
		url: "/Administration/Dashboard",
		data: { validateTask: event.target.checked == true ? 1 : 0, taskId: event.target.dataset.id },
		dataType: "json",
		method: "POST"
	});
	countTasks();
};

const deleteTask = (event) => {
	$.ajax({
		url: "/Administration/Dashboard",
		data: { removeTask: event.currentTarget.dataset.id },
		dataType: "json",
		method: "POST"
	});
	$(`#task${event.currentTarget.dataset.id}`).remove();
	$(`#taskReminder${event.currentTarget.dataset.id}`).remove();
	countTasks();
};


const addTask = (event) => {
  const tableid = event.currentTarget.dataset.tableid;
  const id = new Date().getTime();
  let users = "";
  Object.values(employees).forEach(e => {
	users += `<input type="radio" id="newTask${id}Users${e.id}" value="${e.id}" onclick="taskSelectUser(event)">
			  <label for="newTask${id}Users${e.id}" title="${e.name}"><img src="${e.avatar}" class="img-avatar"></label>`;
  });
  $(event.currentTarget).parent().parent().html(`
	<td colspan="6" id="newTask${id}td">
	  <div class="row">
		<div class="col-12 my-4">
		  <label for="newTask${id}">Intitulé de la tâche</label>
		  <input name="newTask" type="text" id="newTask${id}" data-tableid="${tableid}" class="form-control">
		</div>

		<div class="col-6 mb-4">
		  <label for="newTask${id}Date">Date d'échéance</label>
		  <input type="text" id="newTask${id}Date" value="" class="form-control">
		</div>
		<div class="col-6 mb-4">
		  <label for="newTask${id}Deadline">Rappel avant échéance</label>
		  <select class="form-control" id="newTask${id}Deadline">
			<option value="60">Rappel 1h avant</option>
			<option value="360">Rappel 6h avant</option>
			<option value="1440">Rappel 24h avant</option>
			<option value="10080">Rappel 7j avant</option>
			<option value="20160">Rappel 14j avant</option>
			<option value="40320">Rappel 1 mois avant</option>
		  </select>
		</div>

		<div class="col-6 createTaskUsers">
		  <label for="newTask${id}Users" class="col-12">Utilisateurs</label>
		  ${users}
		</div>
		<div class="col-6 createTaskPriority">
		  <label for="newTask${id}Priority" class="col-12">Priorité</label>
		  <input type="radio" id="newTask${id}PriorityNone" name="newTask${id}Priority" value="">
		  <label for="newTask${id}PriorityNone" title="Aucune"><i class="material-icons">circle</i></label>
		  <input type="radio" id="newTask${id}PrioritySuccess" name="newTask${id}Priority" value="success">
		  <label for="newTask${id}PrioritySuccess" title="Basse"><i class="material-icons text-success">circle</i></label>
		  <input type="radio" id="newTask${id}PriorityWarning" name="newTask${id}Priority" value="warning">
		  <label for="newTask${id}PriorityWarning" title="Moyenne"><i class="material-icons text-warning">circle</i></label>
		  <input type="radio" id="newTask${id}PriorityDanger" name="newTask${id}Priority" value="danger">
		  <label for="newTask${id}PriorityDanger" title="Haute"><i class="material-icons text-danger">circle</i></label>
		</div>
		<div class="col-12 createTaskDescription">
		  <label for="newTask${id}Description">Description</label>
		  <textarea id="newTask${id}Description" name="newTask${id}Description" class="form-control"></textarea>
		</div>
	</td>
	<td class="td-actions text-right">
	  <button type="button" title="Sauvegarder la tâche" class="btn btn-success btn-link btn-sm" onclick="createTask(event, 'newTask${id}')">
		<i class="material-icons">check</i>
	  </button>
	  <button type="button" title="Annuler" class="btn btn-danger btn-link btn-sm mt-5" onclick="cancelCreateTask('newTask${id}', '${tableid}')">
		<i class="material-icons">cancel</i>
	  </button>
	</td>
	`);

	$(`#newTask${id}Date`).appendDtpicker({
	  "autodateOnStart": false,
	  "locale": "fr",
	  "dateFormat": "DD/MM/YY hh:mm",
	  "firstDayOfWeek": 1
	});
};


const taskEvents = () => {
  $('.validateTask').on('change', (event)  => validateTask(event));
  $('.deleteTask').on('click', (event)  => deleteTask(event));
  $('.addTask').on('click', (event) => addTask(event));
  countTasks();
};

const countTasks = () => {
  $('.editableTask .nav-link').each((key, val) => {
	  const nb_tasks = val.attributes.href ? $(`${val.attributes.href.value} .validateTask:not(:checked)`).length : 0;
	  $(val).find('.taskNumber').text(nb_tasks);
	  $(val).find('.taskNumber').css('display', nb_tasks > 0 ? 'block' : 'none');
  });
};

taskEvents();

const createTask = (event, id) => {
	if (!$(`#${id}`).val()) return;

	let priority = $(`input[name="${id}Priority"]:checked`).val();
	let description = $(`#${id}Description`).val().replace(/\n/g, '<br/>');
	const users = [];
	Object.values($(`#${id}td .createTaskUsers input:checked`)).forEach(e => e.value ? users.push(e.value) : '');
	const tableid = $(`#${id}`).data().tableid;
	const data = { taskTitle: $(`#${id}`).val(), tableId: tableid, priority, users: users.join(','), description};
	let deadline = null;
	if ($(`#${id}Date`).val()) {
		deadline = moment($(`#${id}Date`).val(), 'DD/MM/YYYY HH:mm');
		data['dateDeadline'] = deadline.format('YYYY-MM-DD HH:mm');
		if ($(`#${id}Deadline`).val()) {
		  data['dateReminder'] = moment(data['dateDeadline']).subtract($(`#${id}Deadline`).val(), 'minutes').format('YYYY-MM-DD HH:mm');
		}
	}
	$.ajax({
		url: "/Administration/Dashboard",
		data: data,
		dataType: "json",
		method: "POST"
	}).done((data) => {
		$(`#${id}td`).parent().attr('id', `task${data}`);
		$(`#task${data}`).parent().append(`
		  <tr><td colspan="6"></td>
			<td class="td-actions">
			  <button type="button" title="Ajouter une tâche" class="btn btn-success btn-link btn-sm addTask" data-tableid="${tableid}"> <i class="material-icons">add</i> </button>
			</td>
		  </tr>`);
		priority = priority ? `<i class="material-icons text-${priority}">circle</i>` : '';
		let usersHtml = "";
		users.forEach(e => {
		  usersHtml += `<a href="/Administration/Membres/${employees[e].id}" data-id="${employees[e].id}" title="${employees[e].name}" target="_parent"><img src="${employees[e].avatar}" class="img-avatar"></a>`;
		});
		$(`#task${data}`).html(`
		  <td>
			<div class="form-check">
			  <label class="form-check-label">
				<input class="form-check-input validateTask" type="checkbox" value="" {{task.validated ? 'checked' : ''}} data-id="${data}">
				<span class="form-check-sign">
				  <span class="check"></span>
				</span>
			  </label>
			</div>
		  </td>
		  <td class="taskTitle">${$(`#${id}`).val()}</td>
		  <td class="taskDate">${deadline ? deadline.format('DD/MM/YYYY [à] HH[h]mm') : ''}</td>
		  <td class="taskUsers"><div>${usersHtml}</div></td>
		  <td class="taskPriority">${priority}</td>
		  <td class="taskDescription">
			  <div class="bigDescription">
				${description}
			  </div>
		  </td>
		  <td class="td-actions text-right d-flex" style="width: 120px;">
			<button type="button" title="Description" class="btn btn-link btn-sm smallDescription" onclick="showDescription('${data}')">
			  <i class="material-icons">description</i>
			</button>
			<button type="button" title="Modifier" class="btn btn-link btn-sm" onclick="editTask('${data}')">
			  <i class="material-icons">edit</i>
			</button>
			<button type="button" title="Supprimer" class="btn btn-danger btn-link btn-sm deleteTask" data-id="${data}">
			  <i class="material-icons">close</i>
			</button>
		  </td>
		`);
		taskEvents();
	});
};

const editTask = (id) => {
  $(`#task${id}`).find('.td-actions').html(`<button type="button" title="Sauvegarder" class="btn btn-success btn-link btn-sm saveTask" onclick="saveEditTask('${id}')" data-id="${id}">
						  <i class="material-icons">check</i>
						</button>`);
  $(`#task${id}`).find('.taskTitle').prop('contenteditable', true);
  $(`#task${id}`).find('.taskTitle').css('text-decoration', 'underline');
  $(`#task${id}`).find('.taskDescription .bigDescription').prop('contenteditable', true);
  $(`#task${id}`).find('.taskDescription .bigDescription').css('text-decoration', 'underline');

  const selectedUsers = [];
  $(`#task${id} .taskUsers a`).each((e, v) => v.dataset.id ? selectedUsers.push(v.dataset.id) : '');
  let users = "";
  Object.values(employees).forEach(e => {
	const checked = selectedUsers.includes(e.id) ? 'checked' : '';
	users += `<input type="radio" id="newTask${id}Users${e.id}" value="${e.id}" onclick="taskSelectUser(event)" ${checked}>
			  <label for="newTask${id}Users${e.id}" title="${e.name}"><img src="${e.avatar}" class="img-avatar"></label>`;
  });
  $(`#task${id}`).find('.taskUsers').html(`<div>${users}</div>`);

  if ($(`#task${id}`).find('.taskPriority i').length == 0)
	var priority = false;
  else
	var priority = $(`#task${id}`).find('.taskPriority i')[0].classList[1];
  $(`#task${id}`).find('.taskPriority').html(`<div class="createTaskPriority row">
  <input type="radio" id="editTaskPriority${id}None" name="editTaskPriority${id}" value="" ${!priority ? 'checked' : ''}>
  <label for="editTaskPriority${id}None" title="Aucune"><i class="material-icons">circle</i></label>
  <input type="radio" id="editTaskPriority${id}Success" name="editTaskPriority${id}" value="success" ${priority == 'text-success' ? 'checked' : ''}>
  <label for="editTaskPriority${id}Success" title="Basse"><i class="material-icons text-success">circle</i></label>
  <input type="radio" id="editTaskPriority${id}Warning" name="editTaskPriority${id}" value="warning" ${priority == 'text-warning' ? 'checked' : ''}>
  <label for="editTaskPriority${id}Warning" title="Moyenne"><i class="material-icons text-warning">circle</i></label>
  <input type="radio" id="editTaskPriority${id}Danger" name="editTaskPriority${id}" value="danger" ${priority == 'text-danger' ? 'checked' : ''}>
  <label for="editTaskPriority${id}Danger" title="Haute"><i class="material-icons text-danger">circle</i></label> </div>`);
  var date = moment($(`#task${id}`).find('.taskDate').text(), 'DD/MM/YYYY [à] HH[h]mm');
  date = date.isValid() ? date.format('DD/MM/YY hh:mm') : '';
	$(`#task${id}`).find('.taskDate').html(`<input type="text" id="task${id}Date" value="${date}" class="form-control">`);
	$(`#task${id}Date`).appendDtpicker({
	  "autodateOnStart": false,
	  "locale": "fr",
	  "dateFormat": "DD/MM/YY hh:mm",
	  "firstDayOfWeek": 1
	});
};

const saveEditTask = (id) => {
  $(`#task${id}`).find('.td-actions').html(`

						<button type="button" title="Description" class="btn btn-link btn-sm smallDescription" onclick="showDescription('${id}')">
						  <i class="material-icons">description</i>
						</button>
						<button type="button" title="Modifier" class="btn btn-link btn-sm" onclick="editTask('${id}')">
						  <i class="material-icons">edit</i>
						</button>
						<button type="button" title="Supprimer" class="btn btn-danger btn-link btn-sm deleteTask" data-id="${id}">
						  <i class="material-icons">close</i>
						</button>`);
  $(`#task${id}`).find('.taskTitle').prop('contenteditable', false);
  $(`#task${id}`).find('.taskTitle').css('text-decoration', 'none');

  $(`#task${id}`).find('.taskDescription .bigDescription').prop('contenteditable', false);
  $(`#task${id}`).find('.taskDescription .bigDescription').css('text-decoration', 'none');

  const users = [];
  Object.values($(`#task${id} .taskUsers input:checked`)).forEach(e => e.value ? users.push(e.value) : '');
  let priority = $(`input[name="editTaskPriority${id}"]:checked`).val();
  $(`#task${id}`).find('.taskPriority').html(priority ? `<i class="material-icons text-${priority}">circle</i>` : '');
  let date = '';
  if ($(`#task${id}`).find('.taskDate input').val()) {
	date = moment($(`#task${id}`).find('.taskDate input').val(), 'DD/MM/YYYY HH:mm');
	$(`#task${id}`).find('.taskDate').html(date.format('DD/MM/YYYY [à] HH[h]mm'));
  } else {
	$(`#task${id}`).find('.taskDate').html('');
  }

  let usersHtml = "";
  users.forEach(e => {
	usersHtml += `<a href="/Administration/Membres/${employees[e].id}" data-id="${employees[e].id}" title="${employees[e].name}" target="_parent"><img src="${employees[e].avatar}" class="img-avatar"></a>`;
  });
  $(`#task${id}`).find('.taskUsers').html(`<div>${usersHtml}</div>`);
  $.ajax({
	  url: "/Administration/Dashboard",
	  data: { taskTitle: $(`#task${id}`).find('.taskTitle').text(), taskId: id, dateDeadline: date ? date.format('YYYY-MM-DD HH:mm') : '', priority, users: users.join(','), description: $(`#task${id}`).find('.taskDescription .bigDescription').html()},
	  dataType: "json",
	  method: "POST"
  });
};

const cancelCreateTask = (id, tableid) => {
  $(`#${id}`).parent().parent().parent().parent().html(`<td colspan="6"></td>
			<td class="td-actions">
			  <button type="button" title="Ajouter une tâche" class="btn btn-success btn-link btn-sm addTask" data-tableid="${tableid}"> <i class="material-icons">add</i> </button>
			</td>
		  `);
  $('.addTask').on('click', (event) => addTask(event));
};

const editTasksTable = () => {
  $('.tasksTablesTitle').attr('contenteditable','true');
  $('#buttonEditTasks').html(`<a class="nav-link editTasks" title="Ajouter un nouveau tableau" onclick="addTaskTable(event)"><i class="material-icons">add</i></a>
  <a class="nav-link editTasks" title="Sauvegarder les tableaux de tâches" onclick="saveTasksTable(event)"><i class="material-icons">check</i></a>`);
  $('.editableTask .material-icons').hide();
  $('.editableTask').append('<a class="nav-link deleteTaskTable" title="Supprimer le tableau"><i class="material-icons" onclick="deleteTaskTable(event)">delete</i></a>');
  $('.nav-tabs').addClass('editTask');
};

const saveTasksTable = (event) => {
  event.preventDefault();
  $('#buttonEditTasks').html('<a class="nav-link editTasks" title="Modifier les tableaux de tâches" onclick="editTasksTable(event)"><i class="material-icons">edit</i></a>');
  $('.editableTask .material-icons').show();
  $('.tasksTablesTitle').attr('contenteditable', 'false');
  $('.deleteTaskTable').remove();
  $('.nav-tabs').removeClass('editTask');
  const tasks = $('.editableTask');
  for (let i = 0; i < tasks.length; i++) {
	let title = $($('.editableTask')[i]).find('.tasksTablesTitle').text();
	title = title == "" ? "Nouveau" : title;
	$.ajax({
		url: "/Administration/Dashboard",
		data: { taskTableId: $('.editableTask')[i].dataset.id, taskTableTitle: title},
		dataType: "json",
		method: "POST"
	});
  }
};

const addTaskTable = (event) => {
  event.preventDefault();
  $.ajax({
		url: "/Administration/Dashboard",
		data: { createTaskTable: 'Nouveau'},
		dataType: "json",
		method: "POST"
	}).done((data) => {
	  $(`<li class="nav-item editableTask" data-id="${data}">
		  <a class="nav-link" href="#taskTable${data}" data-toggle="tab">
			<i class="material-icons" style="display: none;">view_agenda</i>
			<span class="tasksTablesTitle" contenteditable="true">Nouveau</span>
			<div class="taskNumber"></div>
		  </a>
		  <a class="nav-link deleteTaskTable" title="Supprimer le tableau"><i class="material-icons" onclick="deleteTaskTable(event)">delete</i></a>
		</li>`).insertBefore('#buttonEditTasks');
		$('#tasksTables').append(`<div class="tab-pane" id="taskTable${data}"> <table class="table"> <thead class="text-warning"> <tr> <th></th> <th>Titre</th> <th>Date</th> <th>Utilisateurs</th> <th>Priorité</th> <th></th> </tr> </thead> <tbody> <tr> <td colspan="6"></td> <td class="td-actions"> <button type="button" title="Ajouter une tâche" class="btn btn-success btn-link btn-sm addTask" data-tableid="${data}"> <i class="material-icons">add</i> </button> </td> </tr> </tbody> </table> </div>`);
		  taskEvents();
	});
};

const deleteTaskTable = (event) => {
  event.preventDefault();
  const id = $(event.target).parent().parent().data().id;
  $.ajax({
		url: "/Administration/Dashboard",
		data: { removeTaskTable: id},
		dataType: "json",
		method: "POST"
  });
  $(`.editableTask[data-id="${id}"]`).remove();
};

const expandTasksTable = () => {
  var taskCard = document.getElementById('taskCard').classList.toggle('col-xl-7');
  $('.expandTasks i').text($('.expandTasks i').text() == 'open_in_full' ? 'close_fullscreen' : 'open_in_full');
};

const taskSelectUser = () => {
  if (event.target.previous) {
	  event.target.checked = false;
  }
  event.target.previous = event.target.checked;
};

const showDescription = (id) => {
  $('#modalTaskDescription').show();
  $('#modalTaskDescription h4').text($(`#task${id} .taskTitle`).text());
  $('#modalTaskDescription p').html($(`#task${id} .bigDescription`).html());
  $('#modalTaskDescription').data('id', id);
};

const closeDescription = () => {
	$(`#modalTaskDescription`).find('.card-title').prop('contenteditable', false);
	$(`#modalTaskDescription`).find('p').prop('contenteditable', false);
	$(`#modalTaskDescription`).removeClass('editTaskDescription');
	$('#modalTaskDescription').hide();
	$('.saveDescription').hide();
	$('.editDescription').show();
};

const editDescription = () => {
  $(`#modalTaskDescription`).find('.card-title').prop('contenteditable', true);
  $(`#modalTaskDescription`).find('p').prop('contenteditable', true);
  $(`#modalTaskDescription`).addClass('editTaskDescription');
  $('.saveDescription').show();
  $('.editDescription').hide();
};

const saveDescription = () => {
  $(`#modalTaskDescription`).find('.card-title').prop('contenteditable', false);
  $(`#modalTaskDescription`).find('p').prop('contenteditable', false);
  $(`#modalTaskDescription`).removeClass('editTaskDescription');
  $('.saveDescription').hide();
  $('.editDescription').show();

  const title = $(`#modalTaskDescription`).find('.card-title').text();
  const description = $(`#modalTaskDescription`).find('p').html();
  const id = $('#modalTaskDescription').data('id');
  $.ajax({
	  url: "/Administration/Dashboard",
	  data: { taskId: id, taskTitle: title, description },
	  dataType: "json",
	  method: "POST"
  });
  $(`#task${id} .taskTitle`).text(title);
  $(`#task${id} .bigDescription`).html(description);
  closeDescription();
};

document.onkeydown = function(evt) {
	evt = evt || window.event;
	var isEscape = false;
	if ("key" in evt) {
		isEscape = (evt.key === "Escape" || evt.key === "Esc");
	} else {
		isEscape = (evt.keyCode === 27);
	}
	if (isEscape) {
		closeDescription();
	}
};
