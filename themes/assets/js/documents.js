function saveSectionName(event) {
  var data = {
    editSectionName: event.target.textContent,
    editSectionId: event.target.dataset.id,
  };
  $.ajax({ url: location, type: "post", data });
}

function selectSection(id) {
  document.getElementById("link_section").value = id;
  document.getElementById("folder_section").value = id;
  document.getElementById("file_section").value = id;
  Metro.getPlugin("#link_section", "select").val(id);
  Metro.getPlugin("#folder_section", "select").val(id);
  Metro.getPlugin("#file_section", "select").val(id);
}

$("#folder-container").sortable({
  cursor: "move",
  items: "> .section",
  handle: ".handle",
  containment: $(".p-container"),
  sort: (event, ui) =>
    ui.helper.css({
      top: ui.position.top - 100 + "px",
      left:
        ui.position.left -
        document.getElementById("folder-container").getBoundingClientRect()
          .left +
        "px",
    }),
  stop: () => {
    var sectionsOrder = [];
    $("#folder-container")
      .children()
      .map((i, sec) => sectionsOrder.push(sec.dataset.id));
    var data = { sectionsOrder };
    $.ajax({ url: location, type: "post", data });
  },
});

$(".folder-block").draggable({
  revert: true,
  helper: "clone",
  cursor: "move",
  cursorAt: { top: 45, left: 25 },
  containment: $(".p-container"),
});

$(".folder-block[data-type='folder']").droppable({
  greedy: true,
  drop: function (event, ui) {
    if (this.dataset.type == "folder") {
      var elem = ui.draggable[0];

      if (elem.dataset.type == "folder") {
        var data = { moveFolder: elem.dataset.id, toFolder: this.dataset.id };
        $.ajax({ url: location, type: "post", data })
        .done(() => elem.parentElement.remove());
      } else if (elem.dataset.type == "document") {
        var data = { moveDocument: elem.dataset.id, toFolder: this.dataset.id };
        $.ajax({ url: location, type: "post", data })
        .done(() => elem.parentElement.remove());
      } else if (elem.dataset.type == "link") {
        var data = { moveLink: elem.dataset.id, toFolder: this.dataset.id };
        $.ajax({ url: location, type: "post", data })
         .done(() => elem.parentElement.remove());
      } else if (elem.dataset.type == "file") {
        var data = { moveFile: elem.dataset.id, toFolder: this.dataset.id };
        $.ajax({ url: location, type: "post", data })
         .done(() => elem.parentElement.remove());
      }
    }
  },
});

$(".section").droppable({
  drop: function (event, ui) {
    var elem = ui.draggable[0];

    if (elem.dataset.type == "folder") {
      var data = { moveFolder: elem.dataset.id, toSection: this.dataset.id };
      $.ajax({ url: location, type: "post", data })
      .done(() => $(elem).parent().detach().appendTo($(this).find(".folder-list")));
    } else if (elem.dataset.type == "document") {
      var data = { moveDocument: elem.dataset.id, toSection: this.dataset.id };
      $.ajax({ url: location, type: "post", data })
      .done(() => $(elem).parent().detach().appendTo($(this).find(".folder-list")));
    } else if (elem.dataset.type == "link") {
      var data = { moveLink: elem.dataset.id, toSection: this.dataset.id };
      $.ajax({ url: location, type: "post", data })
       .done(() => $(elem).parent().detach().appendTo($(this).find(".folder-list")));
    } else if (elem.dataset.type == "file") {
      var data = { moveFile: elem.dataset.id, toSection: this.dataset.id };
      $.ajax({ url: location, type: "post", data })
       .done(() => $(elem).parent().detach().appendTo($(this).find(".folder-list")));
    }
  },
});

function saveFolderName(event) {
  var data = {
    editFolderName: event.target.textContent,
    editFolderId: event.target.dataset.id,
  };
  $.ajax({
    url: location,
    type: "post",
    data,
  });
  $("nav .breadcrumb-item.active").text(event.target.textContent);
}

$(".folder-block").draggable({
  revert: true,
  helper: "clone",
  cursor: "move",
  cursorAt: {
    top: 45,
    left: 25,
  },
});

$(".breadcrumb-folder:not(.breadcrumb-home)").droppable({
  drop: function (event, ui) {
    if (this.dataset.type == "folder") {
      var elem = ui.draggable[0];

      if (elem.dataset.type == "folder") {
        var data = { moveFolder: elem.dataset.id, toFolder: this.dataset.id };
      } else if (elem.dataset.type == "document") {
        var data = { moveDocument: elem.dataset.id, toFolder: this.dataset.id };
      } else if (elem.dataset.type == "link") {
        var data = { moveLink: elem.dataset.id, toFolder: this.dataset.id };
      } else if (elem.dataset.type == "file") {
        var data = { moveLink: elem.dataset.id, toFolder: this.dataset.id };
      }
      if (data) {
        $.ajax({
          url: "/Administration/Documents",
          type: "post",
          data,
        })
        .done(() => elem.parentElement.remove());
      }
    } else if (this.dataset.type == "section") {
      var elem = ui.draggable[0];

      if (elem.dataset.type == "folder") {
        var data = { moveFolder: elem.dataset.id, toSection: this.dataset.id };
      } else if (elem.dataset.type == "document") {
        var data = {
          moveDocument: elem.dataset.id,
          toSection: this.dataset.id,
        };
      } else if (elem.dataset.type == "link") {
        var data = { moveLink: elem.dataset.id, toSection: this.dataset.id };
      } else if (elem.dataset.type == "file") {
        var data = { moveFile: elem.dataset.id, toSection: this.dataset.id };
      }
      if (data) {
        $.ajax({
          url: "/Administration/Documents",
          type: "post",
          data,
        })
        .done(() => elem.parentElement.remove());
      }
    }
  },
});

$(".folder-block[data-type='folder']").droppable({
  drop: function (event, ui) {
    if (this.dataset.type == "folder") {
      var elem = ui.draggable[0];

      if (elem.dataset.type == "folder") {
        var data = { moveFolder: elem.dataset.id, toFolder: this.dataset.id };
      } else if (elem.dataset.type == "document") {
        var data = { moveDocument: elem.dataset.id, toFolder: this.dataset.id };
      } else if (elem.dataset.type == "link") {
        var data = { moveLink: elem.dataset.id, toFolder: this.dataset.id };
      } else if (elem.dataset.type == "file") {
        var data = { moveFile: elem.dataset.id, toFolder: this.dataset.id };
      }
      if (data) {
        $.ajax({
          url: "/Administration/Documents",
          type: "post",
          data,
        })
        .done(() => elem.parentElement.remove());
      }
    }
  },
});

function loadFile(link, type, name) {
  if (type.search("video") != "-1") {
    document.getElementById("embedFile").style.display = "none";
    document.getElementById("embedVideo").style.display = "initial";
    document.getElementById("embedVideo").src = link;
    document.getElementById("embedVideo").play();
  } else {
    document.getElementById("embedFile").style.display = "initial";
    document.getElementById("embedVideo").style.display = "none";
    document.getElementById("embedFile").src = link;
    document.getElementById("embedFile").type = type;
  }
  if (type == "application/pdf") {
    document.getElementById("embedBody").style.minHeight = "80vh";
  } else {
    document.getElementById("embedBody").style.minHeight = "unset";
  }
  document.getElementById("embedFileDl").href = link;
  document.getElementById("embedFileName").textContent = name;
}

$("#user_id").on('change', (e) => {
  const userId = e.target.value;
  if (e.target.value) {
    const userName = $(e.target).find("option:selected").text();
    $("#shareTo").append(
      '<button value="' +
        userId +
        '" onclick="removeUser(event)" type="button" class="btn btn-link bg-light mb-2 mr-2">' +
        userName +
        "</button>"
    );
    Metro.getPlugin(e.target, "select").val("");
    let userShare = $("#user_share").val().split(",");
    userShare.push(userId);
    $("#user_share").val(userShare.join());
  }
});

$("#group_id").on('change', (e) => {
  const groupId = e.target.value;
  if (e.target.value) {
    const groupName = $(e.target).find("option:selected").text();
    $("#shareTo").append(
      '<button value="' +
        groupId +
        '" onclick="removeGroup(event)" type="button" class="btn btn-link bg-light mb-2 mr-2"><i class="material-icons vertical-align">group</i>' +
        groupName +
        "</button>"
    );
    Metro.getPlugin(e.target, "select").val("");
    let groupShare = $("#group_share").val().split(",");
    groupShare.push(groupId);
    $("#group_share").val(groupShare.join());
  }
});

$("#persona_id").on('change', (e) => {
  const personaId = e.target.value;
  if (e.target.value) {
    const personaName = $(e.target).find("option:selected").text();
    $("#shareTo").append(
      '<button value="' +
        personaId +
        '" onclick="removePersona(event)" type="button" class="btn btn-link bg-light mb-2 mr-2"><i class="material-icons vertical-align">face</i>' +
        personaName +
        "</button>"
    );
    Metro.getPlugin(e.target, "select").val("");
    let personaShare = $("#persona_share").val().split(",");
    personaShare.push(personaId);
    $("#persona_share").val(personaShare.join());
  }
});

function removeUser(event) {
  let userShare = $("#user_share").val().split(",");
  userShare = userShare.filter((i) => i != event.target.value);
  $("#user_share").val(userShare.join());
  $(event.target).remove();
}

function removeGroup(event) {
  let groupShare = $("#group_share").val().split(",");
  groupShare = groupShare.filter((i) => i != event.target.value);
  $("#group_share").val(groupShare.join());
  $(event.target).remove();
}

function removePersona(event) {
  let personaShare = $("#persona_share").val().split(",");
  personaShare = personaShare.filter((i) => i != event.target.value);
  $("#persona_share").val(personaShare.join());
  $(event.target).remove();
}
