// jQuery.noConflict();

$(".notif-close").click(function () {
  $(".notification").remove();
});

jQuery(document).ready(
  (function (global, factory) {
    typeof exports === "object" && typeof module !== "undefined"
      ? factory(exports, require("jquery"))
      : typeof define === "function" && define.amd
      ? define(["exports", "jquery"], factory)
      : factory((global.gameforest = {}), global.jQuery);
  })(this, function (exports, $) {
    "use strict";

    $(".notif-close").click(function () {
      $(".notification").remove();
    });

    $(".checkedAllStart").click(function () {
      if ($(this).is(":checked")) {
        $(".checkedAll").prop("checked", true);
        $(".checkedAllStart").prop("checked", true);
      } else {
        $(".checkedAll").prop("checked", false);
        $(".checkedAllStart").prop("checked", false);
      }
    });

    $("#table").on("click", ".clickable-row", function (event) {
      $(this).addClass("active").siblings().removeClass("active");
    });

    $(".checkedDroits").click(function () {
      $(".checkedDroits").prop("checked", false);
      $(this).prop("checked", true);
    });

    if ($("#gantt").length > 0) {
      $(".col-lg-3.order-2.order-lg-1").remove();
      $(".col-lg-9.order-1.order-lg-2.pl-lg-4")
        .removeClass("col-lg-9")
        .addClass("col-lg-12");
    }

    function setCookie(key, value, expiry) {
      var expires = new Date();
      expires.setTime(expires.getTime() + expiry * 24 * 60 * 60 * 1000);
      document.cookie = key + "=" + value + ";expires=" + expires.toUTCString();
    }

    function getCookie(key) {
      var keyValue = document.cookie.match("(^|;) ?" + key + "=([^;]*)(;|$)");
      return keyValue ? keyValue[2] : null;
    }

    function eraseCookie(key) {
      var keyValue = getCookie(key);
      setCookie(key, keyValue, "-1");
    }

    $('a[data-toggle="tab"]').click(function (e) {
      e.preventDefault();
      $(this).tab("show");
    });

    if (window.location.pathname !== '/Administration' && window.location.pathname !== '/Administration/') {
        $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
            var id = $(e.target).attr("href");
            localStorage.setItem("selectedTab", id);
        });

        var selectedTab = localStorage.getItem("selectedTab");
        if (selectedTab != null) {
            $('a[data-toggle="tab"][href="' + selectedTab + '"]').tab("show");
        }
      }

    if (!getCookie("dragDrag")) {
      $(".dragDrag").append("Activer le drag pour réorganiser les sections");
      $(".dragDrag").addClass("btn-primary");
      $(".dragDrag").click(function () {
        setCookie("dragDrag", "1", "1");
        window.location.reload();
      });
    } else {
      $(".dragDrag").append("Désactiver le drag");
      $(".dragDrag").addClass("btn-secondary");
      $(".dragDrag").click(function () {
        eraseCookie("dragDrag");
        window.location.reload();
      });
    }

    $(".editTab").click(function (e) {
      e.preventDefault();
      if (!getCookie("editTab")) {
        setCookie("editTab", "1", "1");
        window.location.reload();
      } else {
        eraseCookie("editTab");
        window.location.reload();
      }
    });

    //Inscription
    $(document).on("submit", ".RegisterAjax", function (e) {
      e.preventDefault();
      $(".loading-ajax").removeClass("d-none");
      $(".RegisterAjaxProcess").empty();

      $.ajax({
        url: "/Inscription",
        type: "POST",
        data: new FormData(this),
        dataType: "html",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
          data = JSON.parse(data);
          $(".loading-ajax").addClass("d-none");
          $(".RegisterAjaxProcess").html(data["html"]);
          $(".alert.d-flex.align-items-center.justify-content-between")
            .delay(2000)
            .slideUp(200, function () {
              $("div.notification").remove();
            });
          if (data["rd"]) {
            setTimeout(() => {
              location = location.origin + data["rd"];
            }, 2000);
          } else {
            // $('#image_de_verif')[0].src = 'auths.php?ord=' + (Math.random() * 500000000);
            // $("#captcha").val('');
          }
        },
      });
    });

    //Password recovery
    $(document).on("submit", ".PasswordAjax", function (e) {
      e.preventDefault();
      $(".loading-ajax").removeClass("d-none");
      $(".PasswordAjaxProcess").empty();

      $.ajax({
        url: $(this).attr("data-url"),
        type: "POST",
        data: new FormData(this),
        dataType: "html",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
          $(".loading-ajax").addClass("d-none");
          $(".PasswordAjaxProcess").html(JSON.parse(data));
          // $('#image_de_verif')[0].src = '/auths.php?ord=' + (Math.random() * 500000000);
          // $("#captcha").val('');
          $(".alert.d-flex.align-items-center.justify-content-between")
            .delay(2000)
            .slideUp(200, function () {
              $("div.notification").remove();
            });

          if ($("redir")) {
            window.setTimeout(function () {
              window.location.href = "/Connexion";
            }, 3000);
          }
        },
      });
    });

    $(document).on("submit", ".AddItemsAjax", function (e) {
      e.preventDefault();
      $(".addItemsProcess").empty();

      $.ajax({
        url: $(this).attr("data-url"),
        type: "POST",
        data: new FormData(this),
        dataType: "html",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
          $(".addItemsProcess").html(JSON.parse(data));
          $(".alert.d-flex.align-items-center.justify-content-between")
            .delay(2000)
            .slideUp(200, function () {
              $("div.notification").remove();
              location.reload();
            });
        },
      });
    });

    $(".copy .puce .ya").click(function () {
      $(this).parent().children($(".ya")).removeClass("text-primary");
      var encetre = $(this).closest(".copy").children();
      $(this)
        .closest(".copy")
        .children(".form-group")
        .children(".puceSelect")
        .val($(this).attr("data-original-title"));
      $(this).addClass("text-primary");
    });

    $(".multipleDiv").click(function () {
      $(".copy:first")
        .clone(true)
        .find("input:text")
        .val("")
        .end()
        .find(".removeIDADD")
        .val("")
        .end()
        .find(".ya")
        .removeClass("text-primary")
        .end()
        .find(".deleteDiv")
        .removeClass("d-none")
        .end()
        .appendTo(".paste");
    });

    $(".deleteDiv").click(function () {
      $(this).closest(".copy").remove();
    });

    $(".alert.d-flex.align-items-center.justify-content-between")
      .delay(4000)
      .slideUp(200, function () {
        $(".notification").remove();
        //$('div.notification').remove();
      });

    $(".auths").click(function (events) {
      event.preventDefault();
      // $('#image_de_verif')[0].src = 'auths.php?ord=' + (Math.random() * 500000000);
    });
  })
);

$("#reduire").click(function () {
  var x = document.getElementsByClassName("menu-item");
  var i;

  if (
    getComputedStyle(document.getElementById("title-menu")).display != "none"
  ) {
    $("#widget-menu").removeClass("col-lg-3");
    $("#widget-menu").addClass("col-lg-1");
    document.getElementById("reduire").innerHTML = "add";
    document.getElementById("title-menu").style.display = "none";
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    $("#body-nudge").removeClass("col-lg-9");
    $("#body-nudge").addClass("col-lg-11");
  } else {
    $("#widget-menu").addClass("col-lg-3");
    $("#widget-menu").removeClass("col-lg-1");
    document.getElementById("reduire").innerHTML = "remove";
    document.getElementById("title-menu").style.display = "block";
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "block";
    }
    $("#body-nudge").addClass("col-lg-9");
    $("#body-nudge").removeClass("col-lg-11");
  }
});

function printout(divName) {
  var printContents = document.getElementById(divName).innerHTML;
  var originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = originalContents;
  location.reload();
}

function previewImage(event, dest, destvideo = "") {
  var file = event.target.files[0];
  $("#" + dest).hide();
  if (destvideo) $("#" + destvideo).hide();
  if (file.type.search("image") == 0) {
    var reader = new FileReader();
    reader.onload = function () {
      var output = document.getElementById(dest);
      if (output) {
        output.src = reader.result;
      } else {
        console.error("Output '" + dest + "' not found.");
      }
    };
    reader.readAsDataURL(event.target.files[0]);
    $("#" + dest).show();
  } else if (file.type.search("video") == 0 && destvideo) {
    var fileUrl = window.URL.createObjectURL(event.target.files[0]);
    $("#" + destvideo).attr("src", fileUrl);
    $("#" + destvideo).show();
  }
}

var imageArray = [];

function removeImage(imageid) {
  imageArray.push(imageid);
  if (document.getElementById(imageid))
    document.getElementById(imageid).style.display = "none";
  if (document.getElementById("cancel" + imageid))
    document.getElementById("cancel" + imageid).style.display = null;
  if (document.getElementById("deleteImage"))
    document.getElementById("deleteImage").value = imageArray;
}

function cancelRemoveImage(imageid) {
  imageArray = imageArray.filter(item => (item != imageid));
  // imageArray.slice(imageArray.find(item => item != imageid), 1);
  if (document.getElementById(imageid))
    document.getElementById(imageid).style.display = null;
  if (document.getElementById("cancel" + imageid))
    document.getElementById("cancel" + imageid).style.display = "none";
  if (document.getElementById("deleteImage"))
    document.getElementById("deleteImage").value = imageArray;
}

function viewNotification() {
  var data = { viewNotification: "viewNotification" };
  $.ajax({
    url: location,
    type: "post",
    data,
    success: function (response) {
      if (document.getElementById("notificationNumber")) {
        document.getElementById("notificationNumber").style.display = "none";
      }
    }
  });
}

function deleteNotification(event, id) {
  event.stopPropagation();
  if (id == false) {
    var data = { deleteNotification: "deleteNotification"};
    if (document.getElementById("deleteNotification"))
      document.getElementById("deleteNotification").innerHTML = "<div class='notifEmpty'><i class='material-icons'>notifications</i><h6 style='color: black;'>Aucune notification</h6></div>";
    if (document.getElementsByClassName('notification-delete')[0])
      document.getElementsByClassName('notification-delete')[0].remove();
  } else {
    var data = { deleteNotification: "deleteNotification", id: id};
    if (document.getElementById("deleteNotification_" + id)) {
      document.getElementById("deleteNotification_" + id).remove();
    }
    if (document.getElementById('deleteNotification').childElementCount === 0) {
      if (document.getElementById("deleteNotification"))
        document.getElementById("deleteNotification").innerHTML = "<div class='notifEmpty'><i class='material-icons'>notifications</i><h6 style='color: black;'>Aucune notification</h6></div>";
      if (document.getElementsByClassName('notification-delete')[0])
        document.getElementsByClassName('notification-delete')[0].remove();
    }
  }
  $.ajax({
    url: location,
    type: "post",
    data
  });
}


function sortTable(columnName, sortTable, post = "sort") {
  if (!sortTable) var sortTable = "";
  $(".sorted_asc").removeClass("sorted_asc");
  $(".sorted_desc").removeClass("sorted_desc");
  var column = $("#column" + sortTable).val();
  var sort = column == columnName ? $("#sort" + sortTable).val() : "asc";
  event.target.className += " sorted_" + sort;
  var data = { columnName: columnName, sortTable: sortTable };
  data[post] = sort;
  $.ajax({
    url: location,
    type: "post",
    data,
    success: function (response) {
      $("#empTable" + sortTable).empty();
      $("#empTable" + sortTable).append(response);
      if (sort == "asc") {
        $("#sort" + sortTable).val("desc");
      } else {
        $("#sort" + sortTable).val("asc");
      }
      $("#column" + sortTable).val(columnName);
      reloadAjaxForm();
    },
  });
}

function getArticle(articleId, title) {
  $("#articleContent").empty();
  $("#articleTitle").text(title);
  var data = { getArticle: articleId };
  $.ajax({
    url: location,
    type: "post",
    data,
    success: function (response) {
      $("#articleContent").append(response);
    },
  });
}

function reduceBottomMenu() {
  $("#redGraphBottom").hide();
  $("#showGraphBottom").show();
  $("#menuGraphBottom").css("bottom", "-48px");
}

function showBottomMenu() {
  $("#showGraphBottom").hide();
  $("#redGraphBottom").show();
  $("#menuGraphBottom").css("bottom", "0px");
}

function resetNotificationNumber() {
  if (document.getElementById("notification"))
    document.getElementById("notification").value = true;
}

function disableForms() {
  $(":input").prop("disabled", true);
}

const fontsFormats = ["Arial=arial,helvetica,sans-serif", "Arial Black=arial black,avant garde", "Courier New=courier new,courier", "Georgia=georgia,palatino", "Helvetica=helvetica", "Lato=lato", "Palatino=palatino, book antiqua", "Roboto=roboto", "Symbol=symbol", "Tahoma=tahoma,arial,helvetica,sans-serif", "Times New Roman=times new roman,times", "Trebuchet MS=trebuchet ms,geneva", "Verdana=verdana,geneva", ...customFonts];

function loadTinyMce(selector, options = {}) {
  tinymce.init({
    selector: selector,
    plugins: [
      "advlist autolink link image lists charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking emoticons",
      "table paste help imagetools",
      "case codeeditor",
      "fontawesomepicker",
    ],
    toolbar:
      "undo redo | formatselect fontselect fontsizeselect | bold italic underline | forecolor backcolor | link image |" +
      "alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | case | searchreplace | emoticons fontawesomepicker | fullscreen | codeeditor code",
    toolbar_mode: "sliding",
    menubar: "file edit view insert format tools table help",
    menu: {
      file: { title: "File", items: "preview | print | fullscreen" },
    },
    font_formats: fontsFormats.sort().join(';'),
    contextmenu: false,
    height: 600,
    language: "fr_FR",
    image_title: true,
    automatic_uploads: true,
    image_caption: true,
    image_advtab: true,
    images_upload_url: '/Administration/Upload',
    image_list: '/Administration/List',
    relative_urls : false,
    valid_elements : '+*[*]',
    content_style: `body {color: #000;}
                    img { height: auto; max-width: 100% }
                    :root { ${defaultFonts} }
                    body { font-family: var(--font-text); }
                    h1, h2, h3, h4, h5, h6 { font-family: var(--font-title); }
                    ${customFontsCss}`,
    codeeditor_themes_pack: "monokai twilight merbivore dawn kuroir xcode",
    fontawesomeUrl: 'https://www.unpkg.com/@fortawesome/fontawesome-free@5.14.0/css/all.min.css',
    setup: (editor) => {
      editor.on('change', () => tinymce.triggerSave());
      editor.on('blur', () => tinymce.triggerSave());
    },
    ...options
  });
}

jQuery(() => {
  $('.tinymceClick').on('click', (e) => {
    $(e.target).hide();
    $('#spinner-'+e.target.dataset.id).css('display', 'inline-block');
      loadTinyMce('#'+e.target.dataset.id, { init_instance_callback: () => { $('#spinner-'+e.target.dataset.id).hide(); } });
  });

  $('.tinymceClickFullpage').on('click', (e) => {
    $(e.target).hide();
    $('#spinner-'+e.target.dataset.id).css('display', 'inline-block');
    loadTinyMce('#'+e.target.dataset.id, { init_instance_callback: () => { $('#spinner-'+e.target.dataset.id).hide(); }, height: 800, plugins: [
      "advlist autolink link image lists charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars codeeditor fullscreen media nonbreaking emoticons",
      "table paste help imagetools fullpage",
      "case",
      "fontawesomepicker",
    ]});
  });

  loadTinyMce('.tinymceFullpage', { height: 800, plugins: [
    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
    "searchreplace wordcount visualblocks visualchars codeeditor fullscreen media nonbreaking emoticons",
    "table paste help imagetools fullpage",
      "case",
      "fontawesomepicker",
  ]});

  loadTinyMce(".tinymce");
  loadTinyMce(".tinymceSimple", {height: 300, plugins: ["codeeditor link image lists advlist emoticons","case","fontawesomepicker",], menubar: false});
  loadTinyMce(".tinymceSimpleSmall", {height: 150, plugins: ["codeeditor link image lists advlist emoticons","case","fontawesomepicker",], menubar: false});
  loadTinyMce(".tinymceMail", { remove_script_host : false, convert_urls : true});
  loadTinyMce(".tinymceTemplateMail", { remove_script_host : false, convert_urls : true, plugins: [
    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
    "searchreplace wordcount visualblocks visualchars codeeditor fullscreen media nonbreaking emoticons",
    "table paste help imagetools fullpage",
    "case",
    "fontawesomepicker",]});

  $(document).on('focusin', function(e) {
    if ($(e.target).closest(".tox-dialog").length)
       e.stopImmediatePropagation();
  });
  $('.modal').on('shown.bs.modal', function() {
    $(document).off('focusin.modal');
  });

  $("[data-href]").click(function () {
    window.location = $(this).data("href");
    return false;
  });
});

document.addEventListener("keypress", function (e) {
  if (e.keyCode == 13 && e.target.type != "textarea") {
    e.preventDefault();
    e.target.blur();
  }
});

var fullscreen = false;
var elem = document.documentElement;

function openFullscreen() {
  if (fullscreen == false) {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) {
      /* Safari */
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
      /* IE11 */
      elem.msRequestFullscreen();
    }
    $("#fullscreenIcon")[0].textContent = "fullscreen_exit";
    $("#fullscreenIcon").parent().parent().addClass("current-menu-item");
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      /* Safari */
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
      /* IE11 */
      document.msExitFullscreen();
    }
    $("#fullscreenIcon")[0].textContent = "fullscreen";
    $("#fullscreenIcon").parent().parent().removeClass("current-menu-item");
  }
  fullscreen = !fullscreen;
}

function openMenu() {
  if (
    window.getComputedStyle(document.getElementById("graph_menu_ul"), null)
      .display == "none"
  ) {
    document.getElementById("graph_menu_ul").style.display = "block";
  } else {
    document.getElementById("graph_menu_ul").style.display = "none";
  }
}

function reloadAjaxForm() {
  $(".ajaxForm:not(:data(isAjaxForm))").submit(function (event) {
    event.preventDefault();
    var post_url = $(this).attr("action");
    var request_method = $(this).attr("method");
    var form_data = $(this).serialize();

    $.ajax({
      url: post_url,
      type: request_method,
      data: form_data,
    }).done(function (response) {
      const r = JSON.parse(response);
      const state = r.state === 0 ? "danger" : "success";
      $("body").append(
        '<div class="notification"> <div class="alert alert-' +
          state +
          ' w-50 d-flex align-items-center justify-content-between" role="alert">' +
          r.message +
          '<button type="button" class="close notif-close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button></div> </div>'
      );

      $(".notif-close").click(function () {
        $(".notification").remove();
      });

      if (r.redirect) {
        window.location.replace(r.redirect);
      }
      if (r.pushState) {
        window.history.pushState("", "", r.pushState);
      }
    });
  });
  $(".ajaxForm").data("isAjaxForm", "true");
}

$(document).ready(() => {
  reloadAjaxForm();
});

function createNotif(type, message) {
  $("body").append(
    '<div class="notification"> <div class="alert alert-' +
      type +
      ' w-50 d-flex align-items-center justify-content-between" role="alert">' +
      message +
      '<button type="button" class="close notif-close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button></div> </div>'
  );

  $(".notif-close").click(function () {
    $(".notification").remove();
  });
}

$(window).on("load", () => {
  $(".ajaxFormData").submit(function(event) {
    event.originalEvent.submitter.disabled = true;
    if (typeof tinymce !== 'undefined') {
      tinymce.triggerSave();
    }
    event.preventDefault();
    var post_url = $(this).attr("action");
    var form = this;
    var data = new FormData(form);
    $.ajax({
      url: post_url,
      type: "POST",
      enctype: "multipart/form-data",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      timeout: 800000,
    }).done(function (response) {
      event.originalEvent.submitter.disabled = false;
      const r = JSON.parse(response);
      const state = r.state === 0 ? "danger" : "success";
      createNotif(state, r.message);

      if (r.redirect) {
        window.location.replace(r.redirect);
      }
      if (r.pushState) {
        window.history.pushState("", "", r.pushState);
      }
    });
  });

});
if ($('.modal-dialog')) {
  $( ".modal-dialog" ).draggable({
    cursor: "move",
    handle: ".modal-header",
    });
}

function parseHash() {
  var hash = window.location.hash;
  if (hash.includes("nav")) {
    for (
      let i = 0;
      i < document.getElementsByClassName("tab-pane").length;
      i++
    ) {
      let e = document.getElementsByClassName("tab-pane")[i];
      e.classList.remove("show");
      e.classList.remove("active");
      if (document.getElementsByClassName("navTabList")[0]) {
        e = document.getElementsByClassName("navTabList")[0].children[i];
        if (e) {
          e.classList.remove("active");
          if (e.href && e.href.includes(hash)) {
            e.classList.add("active");
          } else if (e.attributes && e.attributes.href && e.attributes.href.value.includes(hash)) {
            e.classList.add("active");
          }
        }
      }
    }
    document.getElementById(hash.substring(1)).classList.add("show");
    document.getElementById(hash.substring(1)).classList.add("active");
  } else if (hash.includes("modal")) {
    var elem = document.getElementById(hash.substring(1));
    if (elem) {
      elem.click();
    }
  } else if (hash) {
    var elem = document.getElementById(hash.substring(1));
    if (elem) {
      elem.scrollIntoView({ behavior: 'smooth' });
      setTimeout(() => {
        window.scrollBy(0, -70);
      });
    }
  }
}

function findGetParam(parameterName) {
  var result = null,
      tmp = [];
  location.search
      .substr(1)
      .split("&")
      .forEach(function (item) {
        tmp = item.split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
      });
  return result;
}

$(window).on("hashchange", function () {
  parseHash();
});

document.addEventListener("DOMContentLoaded", () => {
  parseHash();

  $('.material-icons').attr('translate', 'no');
});

if ($('.iframe-fullscreen').length && location.pathname != '/Administration') {
  $('.quest-toggle-button').remove()
}

$(".nc-remove-header").on("load", () => removeHeader(".nc-remove-header"));
function removeHeader(id) {
  let head = $(id).contents().find("head");
  let css =
    `<style>#header {visibility: hidden !important;}
    #content, #content-vue {padding-top: 0 !important;}
    #app-navigation, #app-navigation-vue {top: 0 !important; height: 100vh !important;}
    #controls {top: 0 !important;}
    .overview-wrapper, .board-wrapper {max-height: 100vh !important;}
    .unified-search, .notifications {visibility: visible;}
    .unified-search svg, .notifications-button > img {filter: invert(1);}
    #app-content {padding-top: 50px;}
    #richdocumentsframe {height: 100vh !important}
    .top-bar .top-bar__button.primary {margin-right: 90px}
    #app-content-vue {padding-top: 50px; height: 100vh}
    #contactsmenu, #settings {display: none;}
    .modal-container {overflow-y: auto;}
    .main-view > .top-bar {top: 40px;}
    .header-right {padding-right: 30px;}
    .chatView {height: calc(100vh\ -\ 50px) !important;}</style>`;
  $(head).append(css);
  $(id).show();
}

function loadBlocNotes() {
  $("#iframeBlocNotes").attr("src", $("#iframeBlocNotes").attr("data-src"));
}

function loadCalculator() {
  $("#iframeCalculator").attr("src", $("#iframeCalculator").attr("data-src"));
}

function loadRPS() {
  $("#iframeRPS").attr("src", $("#iframeRPS").attr("data-src"));
}

function loadUrlShortener() {
  $("#iframeUrlShortener").attr("src", $("#iframeUrlShortener").attr("data-src"));
}

function divOpen(idOpen, idButton) {
  var btn = document.getElementById(idButton);
  if (btn.textContent == 'keyboard_arrow_down') {
    document.getElementById(idOpen).style.display = 'contents';
    btn.textContent = 'keyboard_arrow_up';
  } else {
    document.getElementById(idOpen).style.display = 'none';
    btn.textContent = 'keyboard_arrow_down';
  }
}

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

$(".cp-autocomplete").autocomplete({
    maxShowItems: 15,
    source: function (request, response) {
        $.ajax({
            url: "https://api-adresse.data.gouv.fr/search/?postcode="+$(".cp-autocomplete input").val()+"&limit=15",
            data: { q: request.term },
            dataType: "json",
            success: function (data) {
                var postcodes = [];
                response($.map(data.features, function (item) {
                    if ($.inArray(item.properties.postcode, postcodes) == -1) {
                        postcodes.push(item.properties.postcode);
                        return { label: item.properties.postcode + " - " + item.properties.city,
                                 city: item.properties.city,
                                 value: item.properties.postcode
                        };
                    }
                }));
            }
        });
    },
    select: function(event, ui) {
        $('.city-autocomplete input').val(ui.item.city);
        $('.city-autocomplete').val(ui.item.city);
    }
});
$(".city-autocomplete").autocomplete({
    maxShowItems: 15,
    source: function (request, response) {
        $.ajax({
            url: "https://api-adresse.data.gouv.fr/search/?city="+$(".city-autocomplete input").val()+"&limit=15",
            data: { q: request.term },
            dataType: "json",
            success: function (data) {
              var cities = [];
              response($.map(data.features, function (item) {
              if ($.inArray(item.properties.postcode, cities) == -1) {
                cities.push(item.properties.postcode);
                return {
                  label: item.properties.postcode + " – " + item.properties.city,
                  postcode: item.properties.postcode,
                  value: item.properties.city
                };
              }
            }));
          }
        });
    },
    select: function(event, ui) {
        $('.cp-autocomplete input').val(ui.item.postcode);
        $('.cp-autocomplete').val(ui.item.postcode);
    }
});
$(".address-autocomplete").autocomplete({
    maxShowItems: 15,
    source: function (request, response) {
        $.ajax({
            url: "https://api-adresse.data.gouv.fr/search/?postcode="+$(".cp-autocomplete input").val()+"&limit=15",
            data: { q: request.term },
            dataType: "json",
            success: function (data) {
                response($.map(data.features, function (item) {
                    return { label: item.properties.name + " - " + item.properties.postcode + " – " + item.properties.city, value: item.properties.name,
                    postcode: item.properties.postcode,
                    city: item.properties.city,};
                }));
            }
        });
    },
    select: function(event, ui) {
        $('.cp-autocomplete input').val(ui.item.postcode);
        $('.cp-autocomplete').val(ui.item.postcode);
        $('.city-autocomplete input').val(ui.item.city);
        $('.city-autocomplete').val(ui.item.city);
    }
});

$(".contact-search").autocomplete({
  maxShowItems: 30,
  source: function (request, response) {
    if (request.term.length > 1)
      $.ajax({
          url: "/Administration/Contacts",
          data: { search: request.term },
          dataType: "json",
          method: "POST",
          success: function (data) {
              response($.map(data, function (item) {
                  return {
                    label: item.name,
                    value: item.name,
                    id_contact: item.id_contact,
                    id_client: item.id_client,
                    id_tier: item.id_tier,
                  };
              }));
          }
      });
  },
  select: function(event, ui) {
    if (ui.item.id_tier)
      window.location.href = '/Administration/Contacts/TI-' + ui.item.id_tier;
    else if (ui.item.id_client)
      window.location.href = '/Administration/Membres/' + ui.item.id_client;
    else if (ui.item.id_contact)
      window.location.href = '/Administration/Contacts/' + ui.item.id_contact;
  }
});

$(".email-search").autocomplete({
  maxShowItems: 30,
  source: function (request, response) {
    if (request.term.length > 1)
      $.ajax({
          url: "/Administration/Contacts",
          data: { search: request.term },
          dataType: "json",
          method: "POST",
          success: function (data) {
              response($.map(data, function (item) {
                  if (item.email) {
                    return {
                      label: `[${item.name}] ${item.email}`,
                      value: item.email,
                    };
                  }
              }));
          }
      });
  },
  select: function(event, ui) {
    event.target.value = ui.item.email;
  }
});

$(".documents-search").autocomplete({
  maxShowItems: 30,
  source: function (request, response) {
    if (request.term.length > 1)
      $.ajax({
          url: "/Administration/Documents",
          data: { search: request.term },
          dataType: "json",
          method: "POST",
          success: function (data) {
              response($.map(data, function (item) {
                  return {
                    label: item.label,
                    value: item.label,
                    href: item.href
                  };
              }));
          }
      });
  },
  select: function(event, ui) {
    window.location.href = '/Administration/' + ui.item.href;
  }
});

$(".mail-search").autocomplete({
  maxShowItems: 20,
  position: { collision: 'fit' },
  source: function (request, response) {
    if (request.term.length > 1)
      $.ajax({
          url: "/Administration/Mails",
          data: { search: request.term },
          dataType: "json",
          method: "POST",
          success: function (data) {
              response($.map(data, function (item) {
                  return {
                    label: item.label,
                    value: item.value,
                  };
              }));
          }
      });
  },
  select: function(event, ui) {
      window.location.href = `/Administration/Mails?id=${ui.item.value}#Mail`;
  }
});


function getNatureEntreprise(nature_juridique_entreprise) {
  var nature = {
0000: "Organisme de placement collectif en valeurs mobilières sans personnalité morale",
1000: "Entrepreneur individuel",
2110: "Indivision entre personnes physiques",
2120: "Indivision avec personne morale",
2210: "Société créée de fait entre personnes physiques",
2220: "Société créée de fait avec personne morale",
2310: "Société en participation entre personnes physiques",
2320: "Société en participation avec personne morale",
2385: "Société en participation de professions libérales",
2400: "Fiducie",
2700: "Paroisse hors zone concordataire",
2900: "Autre groupement de droit privé non doté de la personnalité morale",
3110: "Représentation ou agence commerciale d'état ou organisme public étranger immatriculé au RCS",
3120: "Société commerciale étrangère immatriculée au RCS",
3205: "Organisation internationale",
3210: "État, collectivité ou établissement public étranger",
3220: "Société étrangère non immatriculée au RCS",
3290: "Autre personne morale de droit étranger",
4110: "Établissement public national à caractère industriel ou commercial doté d'un comptable public",
4120: "Établissement public national à caractère industriel ou commercial non doté d'un comptable public",
4130: "Exploitant public",
4140: "Établissement public local à caractère industriel ou commercial",
4150: "Régie d'une collectivité locale à caractère industriel ou commercial",
4160: "Institution Banque de France",
5191: "Société de caution mutuelle",
5192: "Société coopérative de banque populaire",
5193: "Caisse de crédit maritime mutuel",
5194: "Caisse (fédérale) de crédit mutuel",
5195: "Association coopérative inscrite (droit local Alsace Moselle)",
5196: "Caisse d'épargne et de prévoyance à forme coopérative",
5202: "Société en nom collectif",
5203: "Société en nom collectif coopérative",
5306: "Société en commandite simple",
5307: "Société en commandite simple coopérative",
5308: "Société en commandite par actions",
5309: "Société en commandite par actions coopérative",
5310: "Société en libre partenariat (SLP)",
5370: "Société de Participations Financières de Profession Libérale Société en commandite par actions (SPFPL SCA)",
5385: "Société d'exercice libéral en commandite par actions",
5410: "SARL nationale",
5415: "SARL d'économie mixte",
5422: "SARL immobilière pour le commerce et l'industrie (SICOMI)",
5426: "SARL immobilière de gestion",
5430: "SARL d'aménagement foncier et d'équipement rural (SAFER)",
5431: "SARL mixte d'intérêt agricole (SMIA)",
5432: "SARL d'intérêt collectif agricole (SICA)",
5442: "SARL d'attribution",
5443: "SARL coopérative de construction",
5451: "SARL coopérative de consommation",
5453: "SARL coopérative artisanale",
5454: "SARL coopérative d'intérêt maritime",
5455: "SARL coopérative de transport",
5458: "SARL coopérative ouvrière de production (SCOP)",
5459: "SARL union de sociétés coopératives",
5460: "Autre SARL coopérative",
5470: "Société de Participations Financières de Profession Libérale Société à responsabilité limitée (SPFPL SARL)",
5485: "Société d'exercice libéral à responsabilité limitée",
5499: "Société à responsabilité limitée",
5505: "SA à participation ouvrière à conseil d'administration",
5510: "SA nationale à conseil d'administration",
5515: "SA d'économie mixte à conseil d'administration",
5520: "Fonds à forme sociétale à conseil d'administration",
5522: "SA immobilière pour le commerce et l'industrie (SICOMI) à conseil d'administration",
5525: "SA immobilière d'investissement à conseil d'administration",
5530: "SA d'aménagement foncier et d'équipement rural (SAFER) à conseil d'administration",
5531: "Société anonyme mixte d'intérêt agricole (SMIA) à conseil d'administration",
5532: "SA d'intérêt collectif agricole (SICA) à conseil d'administration",
5542: "SA d'attribution à conseil d'administration",
5543: "SA coopérative de construction à conseil d'administration",
5546: "SA de HLM à conseil d'administration",
5547: "SA coopérative de production de HLM à conseil d'administration",
5548: "SA de crédit immobilier à conseil d'administration",
5551: "SA coopérative de consommation à conseil d'administration",
5552: "SA coopérative de commerçants-détaillants à conseil d'administration",
5553: "SA coopérative artisanale à conseil d'administration",
5554: "SA coopérative (d'intérêt) maritime à conseil d'administration",
5555: "SA coopérative de transport à conseil d'administration",
5558: "SA coopérative ouvrière de production (SCOP) à conseil d'administration",
5559: "SA union de sociétés coopératives à conseil d'administration",
5560: "Autre SA coopérative à conseil d'administration",
5570: "Société de Participations Financières de Profession Libérale Société anonyme à conseil d'administration (SPFPL SA à conseil d'administration)",
5585: "Société d'exercice libéral à forme anonyme à conseil d'administration",
5599: "SA à conseil d'administration (s.a.i.)",
5605: "SA à participation ouvrière à directoire",
5610: "SA nationale à directoire",
5615: "SA d'économie mixte à directoire",
5620: "Fonds à forme sociétale à directoire",
5622: "SA immobilière pour le commerce et l'industrie (SICOMI) à directoire",
5625: "SA immobilière d'investissement à directoire",
5630: "Safer anonyme à directoire",
5631: "SA mixte d'intérêt agricole (SMIA)",
5632: "SA d'intérêt collectif agricole (SICA)",
5642: "SA d'attribution à directoire",
5643: "SA coopérative de construction à directoire",
5646: "SA de HLM à directoire",
5647: "Société coopérative de production de HLM anonyme à directoire",
5648: "SA de crédit immobilier à directoire",
5651: "SA coopérative de consommation à directoire",
5652: "SA coopérative de commerçants-détaillants à directoire",
5653: "SA coopérative artisanale à directoire",
5654: "SA coopérative d'intérêt maritime à directoire",
5655: "SA coopérative de transport à directoire",
5658: "SA coopérative ouvrière de production (SCOP) à directoire",
5659: "SA union de sociétés coopératives à directoire",
5660: "Autre SA coopérative à directoire",
5670: "Société de Participations Financières de Profession Libérale Société anonyme à Directoire (SPFPL SA à directoire)",
5685: "Société d'exercice libéral à forme anonyme à directoire",
5699: "SA à directoire (s.a.i.)",
5710: "SAS, société par actions simplifiée",
5770: "Société de Participations Financières de Profession Libérale Société par actions simplifiée (SPFPL SAS)",
5785: "Société d'exercice libéral par action simplifiée",
5800: "Société européenne",
6100: "Caisse d'Épargne et de Prévoyance",
6210: "Groupement européen d'intérêt économique (GEIE)",
6220: "Groupement d'intérêt économique (GIE)",
6316: "Coopérative d'utilisation de matériel agricole en commun (CUMA)",
6317: "Société coopérative agricole",
6318: "Union de sociétés coopératives agricoles",
6411: "Société d'assurance à forme mutuelle",
6511: "Sociétés Interprofessionnelles de Soins Ambulatoires",
6521: "Société civile de placement collectif immobilier (SCPI)",
6532: "Société civile d'intérêt collectif agricole (SICA)",
6533: "Groupement agricole d'exploitation en commun (GAEC)",
6534: "Groupement foncier agricole",
6535: "Groupement agricole foncier",
6536: "Groupement forestier",
6537: "Groupement pastoral",
6538: "Groupement foncier et rural",
6539: "Société civile foncière",
6540: "Société civile immobilière",
6541: "Société civile immobilière de construction-vente",
6542: "Société civile d'attribution",
6543: "Société civile coopérative de construction",
6544: "Société civile immobilière d' accession progressive à la propriété",
6551: "Société civile coopérative de consommation",
6554: "Société civile coopérative d'intérêt maritime",
6558: "Société civile coopérative entre médecins",
6560: "Autre société civile coopérative",
6561: "SCP d'avocats",
6562: "SCP d'avocats aux conseils",
6563: "SCP d'avoués d'appel",
6564: "SCP d'huissiers",
6565: "SCP de notaires",
6566: "SCP de commissaires-priseurs",
6567: "SCP de greffiers de tribunal de commerce",
6568: "SCP de conseils juridiques",
6569: "SCP de commissaires aux comptes",
6571: "SCP de médecins",
6572: "SCP de dentistes",
6573: "SCP d'infirmiers",
6574: "SCP de masseurs-kinésithérapeutes",
6575: "SCP de directeurs de laboratoire d'analyse médicale",
6576: "SCP de vétérinaires",
6577: "SCP de géomètres experts",
6578: "SCP d'architectes",
6585: "Autre société civile professionnelle",
6589: "Société civile de moyens",
6595: "Caisse locale de crédit mutuel",
6596: "Caisse de crédit agricole mutuel",
6597: "Société civile d'exploitation agricole",
6598: "Exploitation agricole à responsabilité limitée",
6599: "Autre société civile",
6901: "Autre personne de droit privé inscrite au registre du commerce et des sociétés",
7111: "Autorité constitutionnelle",
7112: "Autorité administrative ou publique indépendante",
7113: "Ministère",
7120: "Service central d'un ministère",
7150: "Service du ministère de la Défense",
7160: "Service déconcentré à compétence nationale d'un ministère (hors Défense)",
7171: "Service déconcentré de l'État à compétence (inter) régionale",
7172: "Service déconcentré de l'État à compétence (inter) départementale",
7179: "(Autre) Service déconcentré de l'État à compétence territoriale",
7190: "Ecole nationale non dotée de la personnalité morale",
7210: "Commune et commune nouvelle",
7220: "Département",
7225: "Collectivité et territoire d'Outre Mer",
7229: "(Autre) Collectivité territoriale",
7230: "Région",
7312: "Commune associée et commune déléguée",
7313: "Section de commune",
7314: "Ensemble urbain",
7321: "Association syndicale autorisée",
7322: "Association foncière urbaine",
7323: "Association foncière de remembrement",
7331: "Établissement public local d'enseignement",
7340: "Pôle métropolitain",
7341: "Secteur de commune",
7342: "District urbain",
7343: "Communauté urbaine",
7344: "Métropole",
7345: "Syndicat intercommunal à vocation multiple (SIVOM)",
7346: "Communauté de communes",
7347: "Communauté de villes",
7348: "Communauté d'agglomération",
7349: "Autre établissement public local de coopération non spécialisé ou entente",
7351: "Institution interdépartementale ou entente",
7352: "Institution interrégionale ou entente",
7353: "Syndicat intercommunal à vocation unique (SIVU)",
7354: "Syndicat mixte fermé",
7355: "Syndicat mixte ouvert",
7356: "Commission syndicale pour la gestion des biens indivis des communes",
7357: "Pôle d'équilibre territorial et rural (PETR)",
7361: "Centre communal d'action sociale",
7362: "Caisse des écoles",
7363: "Caisse de crédit municipal",
7364: "Établissement d'hospitalisation",
7365: "Syndicat inter hospitalier",
7366: "Établissement public local social et médico-social",
7367: "Centre Intercommunal d'action sociale (CIAS)",
7371: "Office public d'habitation à loyer modéré (OPHLM)",
7372: "Service départemental d'incendie et de secours (SDIS)",
7373: "Établissement public local culturel",
7378: "Régie d'une collectivité locale à caractère administratif",
7379: "(Autre) Établissement public administratif local",
7381: "Organisme consulaire",
7382: "Établissement public national ayant fonction d'administration centrale",
7383: "Établissement public national à caractère scientifique culturel et professionnel",
7384: "Autre établissement public national d'enseignement",
7385: "Autre établissement public national administratif à compétence territoriale limitée",
7389: "Établissement public national à caractère administratif",
7410: "Groupement d'intérêt public (GIP)",
7430: "Établissement public des cultes d'Alsace-Lorraine",
7450: "Etablissement public administratif, cercle et foyer dans les armées",
7470: "Groupement de coopération sanitaire à gestion publique",
7490: "Autre personne morale de droit administratif",
8110: "Régime général de la Sécurité Sociale",
8120: "Régime spécial de Sécurité Sociale",
8130: "Institution de retraite complémentaire",
8140: "Mutualité sociale agricole",
8150: "Régime maladie des non-salariés non agricoles",
8160: "Régime vieillesse ne dépendant pas du régime général de la Sécurité Sociale",
8170: "Régime d'assurance chômage",
8190: "Autre régime de prévoyance sociale",
8210: "Mutuelle",
8250: "Assurance mutuelle agricole",
8290: "Autre organisme mutualiste",
8310: "Comité social économique d’entreprise",
8311: "Comité social économique d'établissement",
8410: "Syndicat de salariés",
8420: "Syndicat patronal",
8450: "Ordre professionnel ou assimilé",
8470: "Centre technique industriel ou comité professionnel du développement économique",
8490: "Autre organisme professionnel",
8510: "Institution de prévoyance",
8520: "Institution de retraite supplémentaire",
9110: "Syndicat de copropriété",
9150: "Association syndicale libre",
9210: "Association non déclarée",
9220: "Association déclarée",
9221: "Association déclarée d'insertion par l'économique",
9222: "Association intermédiaire",
9223: "Groupement d'employeurs",
9224: "Association d'avocats à responsabilité professionnelle individuelle",
9230: "Association déclarée, reconnue d'utilité publique",
9240: "Congrégation",
9260: "Association de droit local (Bas-Rhin, Haut-Rhin et Moselle)",
9300: "Fondation",
9900: "Autre personne morale de droit privé",
9970: "Groupement de coopération sanitaire à gestion privée "};
    return nature[nature_juridique_entreprise];
}

function completeCompany(c) {
  if (document.getElementById('ens')) {
    $('#ens input').val(c.denomination);
    $('#ens').val(c.denomination);
    $('#denom input').val(c.denomination);
    $('#denom').val(c.denomination);
  } else {
    $('#denomination input').val(c.denomination);
    $('#denomination').val(c.denomination);
    $('#n_siret').val(c.siret);
  }
    $('.address-autocomplete input').val(c.address);
    $('.address-autocomplete').val(c.address);
    $('.cp-autocomplete input').val(c.postcode);
    $('.cp-autocomplete').val(c.postcode);
    $('.city-autocomplete input').val(c.city);
    $('.city-autocomplete').val(c.city);
    $('#numero_tva input').val(c.numero_tva_intra);
    $('#numero_tva').val(c.numero_tva_intra);
    $('#forme_juridique_select input').val(c.nature_juridique_entreprise);
    $('#forme_juridique_select').val(c.nature_juridique_entreprise);
}

$(".siret-autocomplete").autocomplete({
    source: function (request, response) {
      if ($(".siret-autocomplete input").val().length === 14) {
        $.ajax({
          url: "https://entreprise.data.gouv.fr/api/sirene/v1/siret/"+$(".siret-autocomplete input").val(),
          dataType: "json",
          success: function (data) {
            response($.map([data], function (item) {
              return {
                label: `${item.etablissement.siret} - ${item.etablissement.l1_normalisee}`,
                value: item.etablissement.siret,
                denomination: item.etablissement.l1_normalisee,
                address: `${item.etablissement.numero_voie} ${item.etablissement.type_voie} ${item.etablissement.libelle_voie}`,
                postcode: item.etablissement.code_postal,
                city: item.etablissement.libelle_commune,
                nature_juridique_entreprise: getNatureEntreprise(item.etablissement.nature_juridique_entreprise)
              }
            }));
          }
        });
      } else if ($(".siret-autocomplete input").val().length >= 9) {
        var siren = $(".siret-autocomplete input").val().substring(0, 9);
        $.ajax({
          url: "https://entreprise.data.gouv.fr/api/sirene/v1/siren/"+siren,
          dataType: "json",
          success: function (data) {
            var etab = data.other_etablissements_sirets;
            etab.push(data.siege_social.siret);
            response($.map(etab, function (item) {
              return {
                label: `${item} - ${data.siege_social.l1_normalisee}`,
                value: item,
                siret: item
              }
            }));
          }
        });
      }
    },
    select: function(event, ui) {
      if (ui.item.siret) {
        $.ajax({
          url: "https://entreprise.data.gouv.fr/api/sirene/v1/siret/"+ui.item.siret,
          dataType: "json",
          success: function (item) {
            var etab = {
              denomination: item.etablissement.l1_normalisee,
              address: `${item.etablissement.numero_voie} ${item.etablissement.type_voie} ${item.etablissement.libelle_voie}`,
              postcode: item.etablissement.code_postal,
              city: item.etablissement.libelle_commune,
              nature_juridique_entreprise: getNatureEntreprise(item.etablissement.nature_juridique_entreprise)};
            completeCompany(etab);
          }
        });
      } else {
        completeCompany(ui.item);
      }
    }
});

$(".company-autocomplete").autocomplete({
  maxShowItems: 30,
  source: function (request, response) {
    $.ajax({
      url: "https://entreprise.data.gouv.fr/api/sirene/v1/full_text/"+encodeURIComponent($(".company-autocomplete input").val())+"?per_page=30",
      dataType: "json",
      success: function (data) {
        const etablissement = data.etablissement;
        response($.map(etablissement, function (item) {
          return {
            label: `${item.siret} - ${item.l1_normalisee}`,
            value: item.l1_normalisee,
            denomination: item.l1_normalisee,
            address: `${item.numero_voie} ${item.type_voie} ${item.libelle_voie}`,
            postcode: item.code_postal,
            city: item.libelle_commune,
            nature_juridique_entreprise: getNatureEntreprise(item.nature_juridique_entreprise),
            siret: item.siret
          }
        }));
      }
    });
  },
  select: function(event, ui) {
    completeCompany(ui.item);
  }
});

$(".rl-search").autocomplete({
  maxShowItems: 30,
  source: function (request, response) {
    if (request.term.length > 1)
      $.ajax({
          url: "/Administration/Contacts",
          data: { search: request.term, filter: 'contacts_accounts' },
          dataType: "json",
          method: "POST",
          success: function (data) {
              response($.map(data, function (item) {
                  return {
                    label: item.name ? item.name : item.email,
                    value: item.fullname,
                    id_contact: item.id_contact,
                    id_client: item.id_client
                  };
              }));
          }
      });
  },
  select: function(e, ui) {
      if (ui.item.id_client) {
        document.getElementById('representant_legal_id').value = '';
        document.getElementById('representant_legal_id_client').value = ui.item.id_client;
      } else if (ui.item.id_contact) {
        document.getElementById('representant_legal_id').value = ui.item.id_contact;
        document.getElementById('representant_legal_id_client').value = '';
      }
      setTimeout(() => document.getElementById('representant_legal_search').value = ui.item.value);
  }
});

function navigate(address) {
  // If it's an iPhone..
  if ((navigator.platform.indexOf("iPhone") !== -1) || (navigator.platform.indexOf("iPod") !== -1)) {
  function iOSversion() {
    if (/iP(hone|od|ad)/.test(navigator.platform)) {
    // supports iOS 2.0 and later
    var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
    return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
    }
  }
  var ver = iOSversion() || [0];

  var protocol = 'http://';
  if (ver[0] >= 6) {
    protocol = 'maps://';
  }
  window.location = protocol + 'maps.apple.com/maps?q=' + address + '&amp;ll=';
  }
  else {
  window.open('http://maps.google.com?q=' + address + '&amp;ll=');
  }
}

// * Center table on small screens
window.onresize = () => {
  if ($('#print')[0] && $('#print > table')[0] && $('#print > table')[0].offsetWidth > $('#print')[0].offsetWidth) {
    if ($(window).width() > $('#print > table')[0].offsetWidth) {
      $('#print > table')[0].style.marginLeft = `-${(($('#print > table')[0].offsetWidth - $('#print')[0].offsetWidth) / 2)}px`;
    } else {
      $('#print > table')[0].style.marginLeft = `-${($(window).width() - $('.p-container')[0].offsetWidth) / 2}px`;
    }
  }
};

window.onload = () => {
  if ($('#print')[0] && $('#print > table')[0] && $('#print > table')[0].offsetWidth > $('#print')[0].offsetWidth) {
    if ($(window).width() > $('#print > table')[0].offsetWidth) {
      $('#print > table')[0].style.marginLeft = `-${(($('#print > table')[0].offsetWidth - $('#print')[0].offsetWidth) / 2)}px`;
    } else {
      $('#print > table')[0].style.marginLeft = `-${($(window).width() - $('.p-container')[0].offsetWidth) / 2}px`;
    }
  }
};
