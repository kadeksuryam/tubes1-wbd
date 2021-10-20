const main = event => {
    xhr = new XMLHttpRequest();
    xhr.open("GET", "/api/dorayakis");
    xhr.send(null);
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4) {
            if(xhr.status === 401) {
                console.log("OKE");
                window.location.replace("/login");
            }
        } else {
            console.log("SUKSES");
        }
    }
}

const displayDorayakiDashboard = (arrayOfDorayaki, pageLimit, currentPage) => {
    let startIdx = pageLimit * (currentPage - 1);
	let endIdx = startIdx + pageLimit - 1;
    if (endIdx > (arrayOfDorayaki.length - 1)) {
        endIdx = arrayOfDorayaki.length - 1
    }

    let cardDorayaki = "";
    for (let i = startIdx; i <= endIdx; i++) {
        cardDorayaki += `
        <div class="card">
            <div class="card-img">
                <img src="$arrayOfDorayaki[i].imagePath"/>
            </div>
            <div class="card-body">
                <h5>$arrayOfDorayaki[i].name</h5>
                <h6>$arrayOfDorayaki[i].varian</h6>
            </div>
            <div class="card-btn">
                <a href="" class="btn btn-primary">Detail</a>
            </div>
        </div>
        `;
    }
    document.getElementsById("dorayaki_dashboard").innerHTML = cardDorayaki;
}
main();