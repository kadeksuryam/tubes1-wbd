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
    let form = document.forms.namedItem("edit-dorayaki");
    let currPath = window.location.pathname;
    let parsePath = currPath.split("/");
    let dorayakiId = parsePath[3];
    if(isNaN(parseInt(dorayakiId))) window.location.href = "/notfound"

    let reqDorayaki = new XMLHttpRequest();
    reqDorayaki.open("GET", "/api/dorayakis/" + dorayakiId, true);
    reqDorayaki.send(null);
    reqDorayaki.onload = function() {
        if(reqDorayaki.status != 200) {
            window.location.href = "/notfound";
            return;
        }
        else {
            let dorayakiData = JSON.parse(reqDorayaki.response);
            form.elements["nama"].value = dorayakiData.nama;
            form.elements["deskripsi"].value = dorayakiData.deskripsi;
            form.elements["harga"].value = dorayakiData.harga;
            form.elements["stok"].value = dorayakiData.stok;        
        }
    }

    form.addEventListener("submit", function(ev) {
        let oOutput = document.getElementById("notification"), oData = new FormData(form);

        let oReq = new XMLHttpRequest();
        oReq.open("POST", `/api/dorayakis/${dorayakiId}?type=update`, true);
        oReq.onload = function(oEvent) {
            if(oReq.status == 200) {
                let req = new XMLHttpRequest();
                req.open("GET", "/api/dorayakis/" + dorayakiId, true);
                req.send(null);
                req.onload = function() {
                    if(req.status == 200) {
                        let dorayakiData = JSON.parse(req.response);
                
                        let form = document.forms.namedItem("edit-dorayaki");
                        
                        form.elements["nama"].value = dorayakiData.nama;
                        form.elements["deskripsi"].value = dorayakiData.deskripsi;
                        form.elements["harga"].value = dorayakiData.harga;
                        form.elements["stok"].value = dorayakiData.stok;        
                        oOutput.innerHTML = "<p style='color:green'>Sucessfully updated dorayaki</p>";
                    }
                }
            } else {
                let apiRes = JSON.parse(oReq.response);
                let errMsg = "";  
                if(Array.isArray(apiRes.message)) {
                    for(let i in apiRes.message) {
                        for(let key in apiRes.message[i]) {
                            errMsg += `<br/>${key}: ${apiRes.message[i][key]}`
                        }
                    }
                }
                else errMsg = apiRes.message;
                console.log(errMsg);
                oOutput.innerHTML = "<p style='color:red'>Error: " + errMsg + "<br \/></p>";
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