const createHeader = (is_admin) => {
    const query_url = new URL(window.location.href).searchParams;

    return `
    <nav class="header">
        <div class="header__link__all">
            <a class="header__link" href="/dashboard">
                <span class="link__hover"> Home </span>
            </a>
            <a class="header__link" href="${!is_admin ? `/riwayat/pembelian?user_id=${getCookie("user_id")}` : "/dorayaki/create"}">
                <span class="link__hover"> ${!is_admin ? "History" : "Add Dorayaki"} </span>
            </a>
        </div>

        <div class="header__search">
            <form action="/search" method="GET" class="header__form__search">
                <input class="header__search" type="text" placeholder="Find dorayaki" name="input_query" value="${
                    query_url.get("query_input") || ""
                }" />
            </form>
        </div>

        <div class="header__logout">
            <a class="header__link" href="/logout" onclick="logout">
                <span class="link__hover"> Logout </span>
            </a>
        </div>
    </nav>
    `;
}

const mainHeader = (event) => {
    let xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                let is_admin = false;
                let admin = ('; '+document.cookie).split(`; is_admin=`).pop().split(';')[0];
                if (admin == 1) {
                    is_admin = true;
                }

                document.getElementById("header-container").innerHTML = createHeader(is_admin);
            } else {

            }
        }
    }
    xhr.open("GET", "/api/dorayakis"); //blom
    xhr.send();
}

function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
}

mainHeader();