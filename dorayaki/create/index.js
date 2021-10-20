function main() {
    xhrCookie = new XMLHttpRequest();
    xhrCookie.open("GET", "/api/auth/verify-cookie", true);
    xhrCookie.send(null);
    xhrCookie.onreadystatechange = function() {
        if(xhrCookie.readyState === 4) {
            if(xhrCookie.status !== 200) {
                window.location.replace("/login");
            }
            else {
                if(getCookie("is_admin") != 1) {
                    window.location.replace("/forbidden");
                }
            }
        }
    }
    let form = document.forms.namedItem("create-dorayaki");
    form.addEventListener("submit", function(ev) {
        let oOutput = document.getElementById("notification"), oData = new FormData(form);

        let oReq = new XMLHttpRequest();
        oReq.open("POST", "/api/dorayakis", true);
        oReq.onload = function(oEvent) {
            if(oReq.status == 200) {
                form.reset();
                oOutput.innerHTML = "<p style='color:green'>Sucessfully created dorayaki</p>";
            } else {
                apiRes = JSON.parse(oReq.response);
                oOutput.innerHTML = "<p style='color:red'>Error: " + apiRes["message"] + "<br \/></p>";
            }
            setTimeout(function() {
                oOutput.innerHTML = "";
            }, 2000);
        }

        oReq.send(oData);
        ev.preventDefault();
    }, false)
}
main();