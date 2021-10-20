const checkInput = (fieldInput, err) => {
    const inputField = document.getElementById(fieldInput);
    inputField.className = 'input_field error';
    const errMessage = document.getElementById(err);
    errMessage.className = 'notification error';
}

const postSignupForm = event => {
    event.preventDefault();
    const password = document.getElementById("password");
    const confirm_password = document.getElementById("confirm_password");
    if (password.value === confirm_password.value) {
        const dataform = new FormData(document.querySelector('form'));
        let xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    window.location.replace("/index.html");
                } else {
                    const result = JSON.parse(xhr.responseText);
                    for (let i = 0; i<result.message.length; i++) {
                        if (result.message[i].hasOwnProperty("username")) {
                            document.getElementById('error-username').innerHTML = result.message[i].username;
                            checkInput('input_username','error-username');
                        } 

                        if (result.message[i].hasOwnProperty("password")) {
                            document.getElementById('error-password').innerHTML = result.message[i].password;
                            checkInput('input_password','error-password');
                        }

                        if (result.message[i].hasOwnProperty("email")) {
                            document.getElementById('error-email').innerHTML = result.message[i].email;
                            checkInput('input_email','error-email');
                        }
                    }
                }
            }
        };

        xhr.open("POST", "/api/register");

        xhr.send(dataform);
    } else {
        document.getElementById("error-match").innerHTML = "Please make sure your password match."
        const errConfirmPassword = document.getElementById('error-match');
        errConfirmPassword.className = 'notification error';
    }
    return;
}

function main() {
    xhrCookie = new XMLHttpRequest();
    xhrCookie.open("GET", "/api/auth/verify-cookie", true);
    xhrCookie.send(null);
    xhrCookie.onreadystatechange = function() {
        if(xhrCookie.readyState === 4) {
            if(xhrCookie.status === 200) {
                window.location.replace("/dashboard");
            }
        }
    }
}

main();