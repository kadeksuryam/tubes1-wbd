const createHeader = (is_admin) => {
    const query_url = new URL(window.location.href).searchParams;

    return `
    <nav class="header">
        <div class="header__link__all">
            <a class="header__link" href="/dashboard">
                <span class="link__hover"> Home </span>
            </a>
            <a class="header__link" href="${!is_admin ? "/history" : "/dorayaki/create"}">
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

const main = (event) => {
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
main();