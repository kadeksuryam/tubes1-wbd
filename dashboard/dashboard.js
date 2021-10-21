const displayDorayakiDashboard = (arrayOfDorayaki, dorayakiPerPage) => {
    let cardDorayaki = "";

    for (let i = 0; i < dorayakiPerPage; i++) {
        cardDorayaki += `
        <div class="card">
            <div class="card-img">
                <img src="${arrayOfDorayaki[i].gambar}"/>
            </div>
            <div class="card-body">
                <h5>${arrayOfDorayaki[i].nama}</h5>
            </div>
            <div class="card-btn">
                <a href="/detail?id=${arrayOfDorayaki[i].id}" class="btn btn-primary">Detail</a>
            </div>
        </div>
        `;
    }
    document.getElementById('dorayaki_dashboard').innerHTML = cardDorayaki;
}

const navigation = (num_page, current_page, size) => {
    const pagination_element = document.getElementById('pagination');

    for (let i = 1; i <= num_page; i++) {
        let btn = paginationButton(i, current_page,size);
        pagination_element.appendChild(btn);
    }
}

const paginationButton = (page, current_page, size) => {
    let btn = document.createElement('button');
    btn.innerText = page;

    if (current_page == page) btn.classList.add('active');
    btn.addEventListener('click', function() {
        current_page = page;
        window.location.replace(`/dashboard?page=${current_page}&size=${size}`);
    });
    return btn;
}

const getUser = () => {
    const userId = ('; '+document.cookie).split(`; user_id=`).pop().split(';')[0];

    let xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const userData = JSON.parse(xhr.responseText);
                document.getElementById("username").innerHTML = userData.username;
            }
        }
    }
    xhr.open("GET", "/api/users/" + userId);
    xhr.send();
}

const getAllDorayaki = (size) => {
    let currPage = new URLSearchParams(window.location.search).get("page");
    if (currPage === null) {currPage = 1;}
    let xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4) {
            if (xhr.status == 200) {
                const dataJson = JSON.parse(xhr.responseText);
                const arrayOfDorayaki = dataJson.payload;
                const arrayOfPage = dataJson.page;
                getUser();
                displayDorayakiDashboard(arrayOfDorayaki, arrayOfPage[1]);
                navigation(arrayOfPage[0], currPage, size);
            } else {
                window.location.replace("/login");
            }
        }
    }

    xhr.open("GET", `/api/dorayakis?page=${currPage}&size=${size}`);
    xhr.send();
}

// const generateDorayaki = () => {
//     xhr = new XMLHttpRequest();
//     xhr.onreadystatechange = function() {
//         if(xhr.readyState === 4) {
//             if(xhr.status === 200) {
//                 //console.log(xhr.reponseText);
//                 window.location.replace("/dashboard");
//                 getAllDorayaki(9);
//             } else{
//                 window.location.replace("/login");
//             }
//         }
//     }
//     xhr.open("GET", "/api/dorayakis");
//     xhr.send();
// }

getAllDorayaki(8)