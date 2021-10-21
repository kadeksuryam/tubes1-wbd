const displayDorayakiSearch = (arrayOfDorayaki, dorayakiPerPage) => {
    let cardDorayaki = "";
    for (let i = 0; i <= dorayakiPerPage; i++) {
        cardDorayaki += `
        <div class="card">
            <div class="card-img">
                <img src="${arrayOfDorayaki[i].gambar}"/>
            </div>
            <div class="card-body">
                <h5>${arrayOfDorayaki[i].nama}</h5>
            </div>
            <div class="card-btn">
                <a href="/detail/id=${chocolateArray[i].id}" class="btn btn-primary">Detail</a>
            </div>
        </div>
        `;
    }
    document.getElementsById("dorayaki_dashboard").innerHTML = cardDorayaki;
}

const navigation = (num_page, current_page) => {
    for (let i = 1; i <= num_page; i++) {
        if (i = current_page) {
            // print semua nomor halaman
        }
    }
}

const getUser = () => {
    const userId = ('; '+document.cookie).split(`; user_id=`).pop().split(';')[0];

    let xhr = new XMLHttpRequest();
    xhr.open("GET", "/api/auth/user/" + userId, false); // blom
    xhr.send(null);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const userData = JSON.parse(xhr.responseText);
                document.getElementById("username").innerHTML = userData.username;
            }
        }
    }
}

const getAllDorayaki = (size) => {
    let currPage = new URLSearchParams(window.location.search).get("page");
    if (currPage == null) {currPage = 1;}
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4) {
            if (xhr.status == 200) {
                const dataJson = JSON.parse(data);
                const arrayOfDorayaki = dataJson.payload;
                const arrayOfPage = dataJson.page;
                getUser();
                displayDorayakiDashboard(arrayOfDorayaki, arrayOfPage[2]);
                navigation(arrayOfPage[0], currPage);
            } else {
                console.log(xhr.responseText);
            }
        }
    }

    xhr.open("GET", `/api/dorayakis?page=${currPage}&size=${size}`);
    xhr.send();
}

const generateDorayaki = () => {
    xhr = new XMLHttpRequest();
    //xhr.send(null);
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4) {
            if(xhr.status === 200) {
                console.log(xhr.reponseText);
                window.location.replace("/dashboard");
                getAllDorayaki(9);
            } else{
                window.location.replace("/login");
            }
        }
    }
    xhr.open("GET", "/api/dorayakis");
    xhr.send();
}
//getAllDorayaki(9)