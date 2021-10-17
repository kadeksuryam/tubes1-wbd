
function main() {
    currPath = window.location.pathname;
    parsePath = currPath.split("/");
    let req = new XMLHttpRequest();
    req.open("GET", "/api/dorayakis/" + parsePath[3], false);
    req.send(null);
    if(req.status != 200) {
        window.location.href = "/notfound";
        return;
    }
    let dorayakiData = JSON.parse(req.response);

    document.cookie = "is_admin=1; Path=/; Expires=Mon, 18 Oct 2021 19:12:06 GMT;";
    document.cookie = "session_id=616c75862c20a; Path=/; Expires=Mon, 18 Oct 2021 19:12:06 GMT;";
    document.cookie = "user_id=1; Path=/; Expires=Mon, 18 Oct 2021 19:12:06 GMT;";
    let form = document.forms.namedItem("edit-dorayaki");
    
    form.elements["nama"].value = dorayakiData.nama;
    form.elements["deskripsi"].value = dorayakiData.deskripsi;
    form.elements["harga"].value = dorayakiData.harga;
    form.elements["stok"].value = dorayakiData.stok;

    form.addEventListener("submit", function(ev) {
        let oOutput = document.getElementById("notification"), oData = new FormData(form);

        let oReq = new XMLHttpRequest();
        oReq.open("POST", "/api/dorayakis", true);
        oReq.onload = function(oEvent) {
            if(oReq.status == 200) {
                form.reset();
                oOutput.innerHTML = "Sucessfully created dorayaki";
            } else {
                oOutput.innerHTML = "Error " + oReq.status + " occurred when trying to upload your file.<br \/>";
            }
        }

        oReq.send(oData);
        ev.preventDefault();
    }, false)
}
main();