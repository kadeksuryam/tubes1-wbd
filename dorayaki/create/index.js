function main() {
    document.cookie = "is_admin=1; Path=/; Expires=Wed, 20 Oct 2021 20:33:49 GMT;";
    document.cookie = "session_id=616f2bad3b446; Path=/; Expires=Wed, 20 Oct 2021 20:33:49 GMT;";
    document.cookie = "user_id=1; Path=/; Expires=Wed, 20 Oct 2021 20:33:49 GMT;";
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
                //console.log(oReq.response);
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