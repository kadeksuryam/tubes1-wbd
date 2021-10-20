function main() {
    let xhrLogout = new XMLHttpRequest();
    xhrLogout.open("GET", "/api/logout", true);
    xhrLogout.send(null);   
    xhrLogout.onreadystatechange = function() {
        if(xhrLogout.readyState === 4) {
            console.log("tes");
            if(xhrLogout.status === 200) {
                window.location.replace("/login");
            }
        }
    }   
}
main();