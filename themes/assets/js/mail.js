var cols = {},
  messageIsOpen = false,
  sendIsOpen = false,
  sidebarIsOpen = false;
cols.showOverlay = function () {
  $("body").addClass("show-main-overlay");
  sidebarIsOpen = true;
};
cols.hideOverlay = function () {
  $("body").removeClass("show-main-overlay");
  sidebarIsOpen = false;
};

cols.showMessage = function () {
  cols.hideSend();
  $("body").addClass("show-message");
  messageIsOpen = true;
};
cols.hideMessage = function () {
  $("body").removeClass("show-message");
  $("#main .message-list li").removeClass("active");
  messageIsOpen = false;
};

cols.showSend = function () {
  cols.hideMessage();
  $("body").addClass("show-send");
  sendIsOpen = true;
};
cols.hideSend = function () {
  $("body").removeClass("show-send");
  sendIsOpen = false;
};

cols.showSidebar = function () {
  $("body").addClass("show-sidebar");
};
cols.hideSidebar = function () {
  $("body").removeClass("show-sidebar");
};

jQuery(document).ready(function ($) {
  // * Show sidebar when trigger is clicked
  $(".trigger-toggle-sidebar").on("click", function () {
    if (!sidebarIsOpen) {
      cols.showSidebar();
      cols.showOverlay();
    } else {
      cols.hideSidebar();
      cols.hideOverlay();
    }
  });

  $(".trigger-message-close").on("click", function () {
    cols.hideMessage();
    cols.hideOverlay();
    cols.hideSend();
  });

  // * This will prevent click from triggering twice when clicking checkbox/label
  $("input[type=checkbox]").on("click", function (e) {
    e.stopImmediatePropagation();
  });

  // * When you click the overlay, close everything
  $("#main > .overlay").on("click", function () {
    cols.hideOverlay();
    cols.hideMessage();
    cols.hideSidebar();
    cols.hideSend();
  });

  // * Resize the Mail div
  const BORDER_SIZE = 10;
  const message = document.getElementById("message");

  let m_pos;

  function resizeMessage(e) {
    const dx = m_pos - e.x;
    m_pos = e.x;
    message.style.transitionDuration = "0s";
    message.style.width =
      parseInt(getComputedStyle(message, "").width) + dx + "px";
  }

  message.addEventListener(
    "mousedown",
    function (e) {
      if (e.offsetX < BORDER_SIZE) {
        m_pos = e.x;
        document.addEventListener("mousemove", resizeMessage, false);
      }
    },
    false
  );

  document.addEventListener(
    "mouseup",
    function () {
      message.style.transitionDuration = "";
      document.removeEventListener("mousemove", resizeMessage, false);
    },
    false
  );

  const send = document.getElementById("send");

  let m_pos_sent;

  function resizeSend(e) {
    const dx = m_pos_sent - e.x;
    m_pos_sent = e.x;
    send.style.transitionDuration = "0s";
    send.style.width = parseInt(getComputedStyle(send, "").width) + dx + "px";
  }

  send.addEventListener(
    "mousedown",
    function (e) {
      if (e.offsetX < BORDER_SIZE) {
        m_pos_sent = e.x;
        document.addEventListener("mousemove", resizeSend, false);
      }
    },
    false
  );

  document.addEventListener(
    "mouseup",
    function () {
      send.style.transitionDuration = "";
      document.removeEventListener("mousemove", resizeSend, false);
    },
    false
  );

  // * New Mail
  $(".compose-button").on("click", function () {
    cols.showSend();
    cols.showOverlay();
    $("#object")[0].value = "";
    $("#object_title").text("");
    $("#cc")[0].value = "";
    $("#bcc")[0].value = "";
    $("#id")[0].value = "";
    tinyMCE.activeEditor.setContent("");
  });

  // * On input object set title
  $("#object").on("input", function (e) {
    $("#object_title").text(e.target.value);
  });
});

// * Load More Mails
function loadMails(e) {
  $.get(
    "/Administration/Mails/" +
      $("#mail-filter")[0].value +
      "/" +
      e.currentTarget.dataset.offset,
    function (data) {
      if (data) {
        data = JSON.parse(data);
        if (data.state == true) {
          const mails = data.data;
          document.getElementById("message-list").innerHTML =
            document.getElementById("message-list").innerHTML + mails;
          document.getElementById("load-more-link").dataset.offset =
            data.offset;
          if (data.moreMails == 0) {
            document.getElementById("load-more-link").remove();
          }
        } else {
          document.getElementById("load-more-link").remove();
          return;
        }
      }
    }
  );
}

function reloadMails() {
  $.post(
    "/Administration/Mails/" + $("#mail-filter")[0].value,
    { reloadMails: true },
    () => window.location.reload()
  );
}

// * Recalc Iframe height from mail content
function calcIframeHeight() {
  if ($("#mail-content")[0].contentWindow.document.body) {
    $("#mail-content")[0].style.height =
      $("#mail-content")[0].contentWindow.document.body.scrollHeight +
      100 +
      "px";
  }
}

function requestOneMail(type, id) {
  $("#mail-title").html("");
  $("#mail-date").html("");
  $("#mail-dest").html("");
  $("#mail-from").html("");
  $("#mail-to").html("");
  $("#mail-tobcc").html("");
  $("#mail-ccLink").html("");
  $("#mail-fromEmail").val("");
  $("#mail-cc").val("");
  $("#mail-bcc").val("");
  $("#mail-id").val("");
  $('#mail-attachments').empty();
  $('#text-mail-attachments').hide()
  document.getElementById("id").value = "";
  document.getElementById("mail-content").srcdoc = "";
  $.get(
    "/Administration/Mails/" + (type == "sent" ? "Sent/" : "Received/") + id,
    function (data) {
      data = JSON.parse(data);
      if (data.state == true) {
        const mail = data.data;
        $("#mail-title").html(mail.subject);
        $("#mail-date").html(mail.date);
        $("#mail-to").html(mail.to);
        $("#mail-tobcc").html(mail.toBcc);
        $("#mail-ccLink").html(mail.ccLink);
        if (mail.cc) $("#mail-cc").val(mail.cc);
        if (mail.bcc) $("#mail-bcc").val(Object.values(mail.bcc).join(', '));
        $("#mail-id").val(mail.id);
        $("#mail-fromEmail").val(mail.fromEmail);
        $("#mail-from").html(mail.dest);
        document.getElementById("mail-content").srcdoc = mail.textHtml;
        document.getElementById("id").value = mail.id;

        if (mail.attachments && mail.attachments.length) {
          $('#text-mail-attachments').show()
          mail.attachments.forEach(e => {
            $('#mail-attachments').append('<li><a href="'+e.url+'" download="'+e.name+'" class="text-primary">'+e.name+'</a></li>')
          });
        }
        setTimeout(() => calcIframeHeight(), 300);
        setTimeout(calcIframeHeight(), 1000);
        setTimeout(() => calcIframeHeight(), 2000);
        if (window.location.pathname == '/Administration/Mails/Sent' || window.location.pathname == '/Administration/Mails/Draft') {
          window.history.pushState("", "", window.location.pathname+'?id='+mail.id+'#Sent');
        } else {
          window.history.pushState("", "", window.location.pathname+'?id='+mail.id+'#Mail');
        }
      } else {
        return;
      }
    }
  );
}

// * Upload attachments
function mailUploadFile(evt) {
  evt.preventDefault();
  $('#input_file_mail').click();
}

function mailFileList(e) {
  var files = $('#input_file_mail').prop("files");

  filelistall = $('#input_file_mail').prop("files");
  var fileBuffer = [];
  Array.prototype.push.apply( fileBuffer, filelistall );
  const dT = new ClipboardEvent('').clipboardData || new DataTransfer();
  for (let file of fileBuffer) { dT.items.add(file); }

  filelistall = $('#input_file_attachments').prop("files");
  fileBuffer = [];
  Array.prototype.push.apply( fileBuffer, filelistall );
  for (let file of fileBuffer) { dT.items.add(file); }
  filelistall = $('#input_file_attachments').prop("files", dT.files);

  files = $('#input_file_attachments').prop("files");
  $("#listfiles").html('');
  var totalBytesFiles = 0;
  for (var i = 0; i < files.length; i++) {
    var fileName = files[i].name;
    var totalBytes = files[i].size;
    totalBytesFiles += totalBytes;
    var size = totalBytes < 1000000 ? Math.floor(totalBytes/1000) + 'KB' : Math.floor(totalBytes/1000000) + 'MB';

    $("#listfiles").append(`<div id=preloadpic_${i} title='${fileName}'><p class="m-1">Fichier : <span class="text-primary">${fileName}</span> (${size}) <a onclick=mailDeleteFile(${i}) class="cursor-pointer"><i class="material-icons vertical-align">delete</i></a></p></div>`);
  }
  if (totalBytesFiles > 1000 * 1000 * 20) {
    $("#listfiles").append('<p class="color-danger font-weight-bold">La taille totale des pièces jointes est supérieur à 20 MB, le mail pourrait ne pas être correctement envoyé ou reçu.</p>');
  }
}

function mailDeleteFile(index)  {
  filelistall = $('#input_file_attachments').prop("files");
  var fileBuffer=[];
  Array.prototype.push.apply( fileBuffer, filelistall );
  fileBuffer.splice(index, 1);
  const dT = new ClipboardEvent('').clipboardData || new DataTransfer();
  console.log(fileBuffer);
  for (let file of fileBuffer) { dT.items.add(file); }
  filelistall = $('#input_file_attachments').prop("files",dT.files);
  $("#preloadpic_"+index).remove();
}

function deleteDraftAttachments() {
  $("#selectedFiles").html('');
  document.getElementById('includeDraftAttachments').value = 0;
}

// * When you click on a message, show it
function loadOneMail(e) {
  $("input[type=checkbox]").on("click", function (e) {
    e.stopImmediatePropagation();
  });
  if (
    e.target.textContent == "star" ||
    e.target.textContent == "star_outline"
  ) {
    e.stopImmediatePropagation();
    return;
  }
  var item = $(e.currentTarget),
    target = $(e.target);

  if (target.is("label")) {
    item.toggleClass("selected");
  } else {
    e.currentTarget.classList.remove("unread");
    if (messageIsOpen && item.is(".active")) {
      cols.hideMessage();
      cols.hideOverlay();
      window.history.pushState("", "", window.location.pathname);
    } else {
      if (messageIsOpen) {
        cols.hideMessage();
        item.addClass("active");
        setTimeout(function () {
          cols.showMessage();
        }, 300);
      } else {
        item.addClass("active");
        cols.showMessage();
      }
      cols.showOverlay();
      requestOneMail(e.currentTarget.dataset.type, e.currentTarget.dataset.id);
    }
  }
}

function transferMail() {
  cols.showSend();
  cols.showOverlay();
  $("#object")[0].value = "Fwd: " + $("#mail-title")[0].textContent;
  $("#object_title").text("Fwd: " + $("#mail-title")[0].textContent);
  $("#cc")[0].value = "";
  $("#bcc")[0].value = "";
  $("#id")[0].value = "";
  tinyMCE.activeEditor.setContent(
    '<div><br/><div><blockquote type="cite"><div>---------- Message d\'origine ----------</div>' +
      $("#mail-content")[0].contentWindow.document.body.innerHTML +
      "</blockquote>"
  );
}

function editMail() {
  cols.showSend();
  cols.showOverlay();
  $("#object")[0].value = $("#mail-title")[0].textContent;
  $("#object_title").text($("#mail-title")[0].textContent);
  $("#cc")[0].value = $("#mail-cc")[0].value;
  $("#bcc")[0].value = $("#mail-bcc")[0].value;
  $("#id")[0].value = $("#mail-id")[0].value;
  document.getElementById('includeDraftAttachments').value = 1;
  if ($('#mail-attachments').html()) {
    $("#selectedFiles").html($("#container-mail-attachments").html());
    $("#selectedFiles").append(`<a onclick="deleteDraftAttachments()" class="cursor-pointer" title="Supprimer les pièces jointes"><i class="material-icons">delete</i></a>`);
  }
  tinyMCE.activeEditor.setContent(
    $("#mail-content")[0].contentWindow.document.body.innerHTML
  );
}

function replyMail() {
  cols.showSend();
  cols.showOverlay();
  $("#object")[0].value = "Re: " + $("#mail-title")[0].textContent;
  $("#object_title").text("Re: " + $("#mail-title")[0].textContent);
  $("#cc")[0].value = $("#mail-fromEmail")[0].value;
  $("#bcc")[0].value = "";
  $("#id")[0].value = "";
  tinyMCE.activeEditor.setContent(
    '<div><br/><div><blockquote type="cite">' +
      $("#mail-content")[0].contentWindow.document.body.innerHTML +
      "</blockquote>"
  );
}

function deleteMail() {
  $.post(
    window.location.pathname,
    {
      id: document.getElementById("id").value,
      trash: true,
    },
    function () {
      createNotif("success", "Email supprimé");
      $("#main .message-list li.active").remove();
      cols.hideMessage();
      cols.hideOverlay();
      cols.hideSend();
    }
  );
}

function deleteMails() {
  ids = $("li.mail .checkbox-wrapper input:checkbox:checked");
  list = [];
  for (let i = 0; i < ids.length; i++) {
    const e = ids[i];
    list.push(e.id.substring(3));
  }
  $.post(
    window.location.pathname,
    {
      id: list,
      trash: true,
    },
    function () {
      createNotif("success", "Email supprimé");
      $("li.mail .checkbox-wrapper input:checkbox:checked")
        .parent()
        .parent()
        .parent()
        .remove();
      $(".message-list .selected").removeClass("selected");
      cols.hideMessage();
      cols.hideOverlay();
      cols.hideSend();
    }
  );
}

function importantMail(e, id) {
  $.post(
    window.location.pathname,
    {
      important: e.target.textContent == "star_outline" ? 1 : 0,
      id: id,
    },
    function (data) {
      data = JSON.parse(data);
      if (data.state == true) {
        e.target.textContent = data.important == 1 ? "star" : "star_outline";
      }
    }
  );
}

function importantMails() {
  ids = $("li.mail .checkbox-wrapper input:checkbox:checked");
  importantValue =
    $(ids[0]).parent().parent().children()[3].textContent == "star" ? 0 : 1;
  list = [];
  for (let i = 0; i < ids.length; i++) {
    const e = ids[i];
    important = $(e).parent().parent().children()[3];
    list.push(e.id.substring(3));
    important.textContent = importantValue == 1 ? "star" : "star_outline";
    $("li.mail .checkbox-wrapper input:checkbox:checked").prop(
      "checked",
      false
    );
  }
  $.post(
    window.location.pathname,
    {
      id: list,
      important: importantValue,
    },
    () => {
      $("li.mail .checkbox-wrapper input:checkbox:checked").prop(
        "checked",
        false
      );
      $(".message-list .selected").removeClass("selected");
    }
  );
}

function markReadMails() {
  ids = $("li.mail .checkbox-wrapper input:checkbox:checked");
  list = [];
  for (let i = 0; i < ids.length; i++) {
    const e = ids[i];
    list.push(e.id.substring(3));
    $("li.mail .checkbox-wrapper input:checkbox:checked").prop(
      "checked",
      false
    );
  }
  $.post(
    window.location.pathname,
    {
      id: list,
      read: true,
    },
    function () {
      $("li.mail .checkbox-wrapper input:checkbox:checked").prop(
        "checked",
        false
      );
      $(".message-list .selected").removeClass("selected");
    }
  );
}

function checkAll(event) {
  if (event.target.checked) {
    $("li.mail .checkbox-wrapper input:checkbox").prop("checked", true);
  } else {
    $("li.mail .checkbox-wrapper input:checkbox").prop("checked", false);
  }
}

function loadFolders() {
  $("#btn-load-folders").remove();
  $.post(
    "/Administration/Mails",
    {
      loadFolder: true,
    },
    (data) => {
      data = JSON.parse(data);
      if (data.state == true) {
        data.data.forEach((e) => {
          var html =
            "<li><a href='/Administration/Mails/F_" +
            encodeURIComponent(e) +
            "'>" +
            e +
            "</a></li>";
          $("#user-folders").append(html);
        });
      }
    }
  );
}

$(window).on("load", function () {
  if (window.location.hash) {
    if (window.location.hash == "#New") {
      cols.showSend();
      cols.showOverlay();
      $("#cc").val(window.location.search.split("=")[1]);
    } else if (window.location.hash == "#Mail") {
      requestOneMail("receive", window.location.search.split("=")[1]);
      cols.showMessage();
      cols.showOverlay();
    } else if (window.location.hash == "#Sent") {
      requestOneMail("sent", window.location.search.split("=")[1]);
      cols.showMessage();
      cols.showOverlay();
    }
  }
});
