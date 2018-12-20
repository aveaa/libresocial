"use strict";
window.thisViewURL = window.location.href.replace(window.location.origin, "");
window._tmp = {};
const cropOpts = {
    aspectRatio: 1,
    crop(e) {
        $("#openvk-avedit > input[name=crop_x]").val(Math.round(e.detail.x));
        $("#openvk-avedit > input[name=crop_y]").val(Math.round(e.detail.y));
        $("#openvk-avedit > input[name=crop_w]").val(Math.round(e.detail.width));
        $("#openvk-avedit > input[name=crop_h]").val(Math.round(e.detail.height));
    }
};

window.resetSyncListeners = () => {
    $("#openvk-avedit").hide();
    let frAv = new FileReader();
    frAv.onload = () => {
        let image = $("#openvk-avatar-editScope");
        image.attr("src", frAv.result);
        if(typeof window._tmp.cropper !== "undefined") window._tmp.cropper.destroy();
        window._tmp.cropper = new Cropper(image.get(0), cropOpts);
        $("#openvk-avedit").show();
    }

    $("input[name=ava]").bind("change", e => {
        let input = e.target;
        let file  = input.files[0];
        if(typeof file == "undefined") return;
        frAv.readAsDataURL(file);
    });
};
(async function() {

let normalizeString = string => string.replace(/[ ]{2,}|\n/gm, "");

let abstractProxy = (action, type, id, body = null) => {
    return function() {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", `/${action}/${type}/${id}`, true);
        if(body != null) xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(body);
    }
};    

let likeProxy = lType => {
    return function() {
        let id = $(this).parent().data("i-pid");
        let likes = parseInt($("span", $(this)).text());
        if($(this).hasClass("liked")) {
            $(this).removeClass("liked");
            $("span", $(this)).text(likes-1);
            $("i", $(this)).removeClass("fas");
            $("i", $(this)).addClass("far");
        } else {
           $(this).addClass("liked");
            $("span", $(this)).text(likes+1);
            $("i", $(this)).addClass("fas");
            $("i", $(this)).removeClass("far"); 
        }
        (abstractProxy("like", lType, id))();
        return false;
    }
};

let editProxy = eType => {
    return function() {
        let id = $(this).parent().parent().data("i-pid");
        let contentNode = $(`.openvk-${eType == "p" ? "post" : "comment"}--content`, $(this).parent().parent().parent());
        let newContent  = prompt("Введите новый текст поста: ", normalizeString(contentNode.text()));
        if(newContent == null) return;
        (abstractProxy("ed", eType, id, `post=${encodeURI(newContent)}`))();
        contentNode.text(newContent);
        return false;
    }
};

let remProxy = eType => {
    return function() {
        if(!confirm("Вы действительно хотите удалить этот пост?")) return;
        (abstractProxy("rem", eType, $(this).parent().parent().data("i-pid")))();
        $(this).parent().parent().parent().remove();
        return false;
    }
};

let openCommentForm = e => $(".openvk-comment-form", $(e.target).parent().parent().parent()).toggleClass("hide");

function resetListeners() {
    $("a").click(loadView);
    $("form").submit(sendForm);
    
    $(".tab").hide();
    $(".tab").first().show();
    
    $(".tab-link").click(e => {
        let tab = $(e.target).attr("data-tab"); //lul
        $(".tab").hide();
        $(`.tab[data-name=${tab}]`).show();
        return false;
    });

    $(".openvk-post--likes").click(likeProxy("p"));
    $(".openvk-comment--likes").click(likeProxy("c"));
    
    $(".openvk-post--edit").click(editProxy("p"));
    $(".openvk-comment--edit").click(editProxy("c"));
    
    $(".openvk-post--rm").click(remProxy("p"));
    $(".openvk-comment--rm").click(remProxy("c"));

    $(".openvk-post--wcomm").click(e => !openCommentForm(e));
    $(".openvk-comment--reply").click(e => {
        let link   = $(".openvk-comment--info > h5 > a", $(e.target).parent().parent().parent());
        let name   = normalizeString(link.text()).split(" ")[0];
        let cForm  = $(".openvk-comment-form",  $(e.target).parent().parent().parent().parent().parent());
        let cInput = $(".openvk-post-field",  cForm);
        cForm.toggleClass("hide");
        cInput.val(`${link.attr("href").replace("/", "*")} (${name}), `);
        cInput.focus();
        return false;
    });
    
    window.resetSyncListeners();
}

async function updateView(url, post = false, forceReolad = false) {
    if(/(^([A-z]+):\/\/)|^#(.*)/.test(url)) return window.location.assign(url);
    let xhr = new XMLHttpRequest();
    xhr.open(post ? "POST" : "GET", url, true);
    xhr.setRequestHeader("X-Mxzr-Particle", "expect");
    xhr.onreadystatechange = () => {
        if(xhr.readyState === 4) {
            if(xhr.status >= 200 && xhr.status <= 204) {
                if(forceReolad) return reloadView(); //skip parsing, if result will be reset anyway.
                let doc = new DOMParser().parseFromString(xhr.responseText, "text/html");
                let title = doc.querySelector("title").innerText;
                doc.querySelector("title").remove();
                $("title").text(title);
                history.pushState([], title, url);
                $("main").html(doc.body.innerHTML);
                window.thisViewURL = url;
                resetListeners();
                window._tmp = {};
                return true;
            } else if(xhr.status >= 400) {
                console.error(`An error occured: ${xhr.responseText}`);
                document.body.innerHTML = xhr.responseText;
                return false;
            }
        }
    }
    xhr.send();
}

let reloadView = () => updateView(window.thisViewURL);

function loadView() {
    if($(this).hasClass("no-ajax") || /^\#/.test($(this).attr("href"))) return;
    let url = $(this).attr("href");
    updateView(url, $(this).hasClass("ajax-post"), $(this).hasClass("ajax-reload")).then(() => {
        if(!$(this).hasClass("no-autoscroll")) window.scrollTo({top:0,behavior:"smooth"});
    });
    return false;
}

window.onpopstate = () => void updateView(window.location.href.replace(window.location.origin, ""));

function sendForm(event) {
    if($(this).hasClass("noajaxy-form")) return;
    let data = new FormData($(this).get(0));
    let url  = typeof $(this).attr("action") === "undefined" ? window.location.href : $(this).attr("action");
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    xhr.onreadystatechange = () => {
        if(xhr.readyState === 4) {
            $(this).attr("action") === xhr.responseURL ? reloadView() : updateView(xhr.responseURL.replace(window.location.origin, ""));
        }
    };
    xhr.send(data);
    event.preventDefault();
}

resetListeners();

})();
