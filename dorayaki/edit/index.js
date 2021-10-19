function main() {
    document.cookie = "is_admin=1; Path=/; Expires=Wed, 20 Oct 2021 21:36:25 GMT;";
    document.cookie = "session_id=616f3a5967738; Path=/; Expires=Wed, 20 Oct 2021 21:36:25 GMT;";
    document.cookie = "user_id=1; Path=/; Expires=Wed, 20 Oct 2021 21:36:25 GMT;";

    let currPath = window.location.pathname;
    let parsePath = currPath.split("/");
    let dorayakiId = parsePath[3];
    if(isNaN(parseInt(dorayakiId))) window.location.href = "/notfound"

    let req = new XMLHttpRequest();
    req.open("GET", "/api/dorayakis/" + dorayakiId, false);
    req.send(null);
    if(req.status != 200) {
        window.location.href = "/notfound";
        return;
    }
    let dorayakiData = JSON.parse(req.response);


    let form = document.forms.namedItem("edit-dorayaki");
    
    form.elements["nama"].value = dorayakiData.nama;
    form.elements["deskripsi"].value = dorayakiData.deskripsi;
    form.elements["harga"].value = dorayakiData.harga;
    form.elements["stok"].value = dorayakiData.stok;

    form.addEventListener("submit", function(ev) {
        let oOutput = document.getElementById("notification"), oData = new FormData(form);

        let oReq = new XMLHttpRequest();
        oReq.open("POST", `/api/dorayakis/${dorayakiId}?type=update`, true);
        oReq.onload = function(oEvent) {
            if(oReq.status == 200) {
                let req = new XMLHttpRequest();
                req.open("GET", "/api/dorayakis/" + dorayakiId, false);
                req.send(null);
                let dorayakiData = JSON.parse(req.response);
            
                let form = document.forms.namedItem("edit-dorayaki");
                
                form.elements["nama"].value = dorayakiData.nama;
                form.elements["deskripsi"].value = dorayakiData.deskripsi;
                form.elements["harga"].value = dorayakiData.harga;
                form.elements["stok"].value = dorayakiData.stok;        
                oOutput.innerHTML = "<p style='color:green'>Sucessfully updated dorayaki</p>";
            } else {
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