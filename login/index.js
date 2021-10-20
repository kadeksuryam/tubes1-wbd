const main = event => {
    xhrCookie = new XMLHttpRequest();
    xhrCookie.open("GET", "/api/auth/verify-cookie", false);
    xhrCookie.send(null);
    xhrCookie.onreadystatechange = function() {
        if(xhrCookie.readyState === 4) {
            if(xhrCookie.status === 200) {
                window.location.replace("/dashboard");
            }
        }
    }

    let form = document.forms.namedItem("login-form");
    
    form.addEventListener("submit", function(event) {
        event.preventDefault();
        let loginPayload = new FormData(form);
        let xhrLogin = new XMLHttpRequest();
        
        xhrLogin.onreadystatechange = function () {
            if (xhrLogin.readyState === 4) {
                if (xhrLogin.status === 200) {
                    window.location.replace("/index.html");
                    console.log("HII")
                } else {
                    const result = JSON.parse(xhrLogin.responseText)
                    const errMessage = document.getElementById('error-login');
                    errMessage.innerHTML = result.message;
                    errMessage.className = 'notification error';
                }
            }
        };
        xhrLogin.open("POST", "/api/login", true);
    
        xhrLogin.send(loginPayload); 
    }, false);

}
main();
