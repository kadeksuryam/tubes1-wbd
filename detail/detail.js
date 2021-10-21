const detail = dorayakiId => {

    let xhr = new XMLHttpRequest();
    xhr.open("GET", "/api/dorayakis/" + dorayakiId, false);
    xhr.send(null);
    if(xhr.status != 200) {
        window.location.href = "/notfound";
        return;
    }

    let dorayakiData = JSON.parse(xhr.response);
    document.getElementById("dorayaki-name").innerHTML = dorayakiData.nama;
    document.getElementById("dorayaki-img").src = dorayakiData.gambar;
    document.getElementById("dorayaki-price").innerHTML = `Price : ${dorayakiData.harga}`;
    document.getElementById("dorayaki-sold").innerHTML = `Sold : ${dorayakiData.terjual}`;
    document.getElementById("dorayaki-stock").innerHTML = `Stock : ${dorayakiData.stok}`;
    document.getElementById("dorayaki-description").innerHTML = dorayakiData.deskripsi;

    let admin = ('; '+document.cookie).split(`; is_admin=`).pop().split(';')[0];

    if (admin == 1) {
        document.getElementById("btn-buy").innerHTML = "Change Stock";
        document.getElementById("btn-buy").href = `/stock?id=${dorayakiId}`;
        document.getElementById("btn-edit").href = `/edit?id=${dorayakiId}`;
        document.getElementById("btn-delete").href = `/delete?id=${dorayakiId}`;

    } else {
        document.getElementById("btn-buy").innerHTML = "Buy Now";
        document.getElementById("btn-buy").href = `/buy?id=${dorayakiId}`;
        document.getElementById("btn-edit").style.visibility = 'hidden';
        document.getElementById("btn-delete").style.visibility = 'hidden';
    }
}

detail(new URLSearchParams(window.location.search).get("id"));