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
        const deleteButton =  document.getElementById("btn-delete");
        document.getElementById("btn-buy").innerHTML = "Change Stock";
        document.getElementById("btn-buy").href = `/dorayaki/beli?id=${dorayakiId}`;
        document.getElementById("btn-edit").href = ``;
        //deleteButton.href = `/dorayaki/delete?id=${dorayakiId}`;

        deleteButton.addEventListener("click", function() {
            if (confirm('Are you sure you want to delete this dorayaki?')) {
                let xhr = new XMLHttpRequest();
                xhr.open("DELETE", `/api/dorayakis/` + dorayakiId, true);
                xhr.send(null);
                xhr.onreadystatechange = function() {
                    if(xhr.readyState === 4) {
                        if(xhr.status === 200) {
                            window.location.href = "/dashboard";
                            return;
                        }
                        else alert(xhr.response.message);
                    }
                }
            }
        })

    } else {
        document.getElementById("btn-buy").innerHTML = "Buy Now";
        document.getElementById("btn-buy").href = `/dorayaki/beli?id=${dorayakiId}`;
        document.getElementById("btn-edit").style.visibility = 'hidden';
        document.getElementById("btn-delete").style.visibility = 'hidden';
    }
}

detail(new URLSearchParams(window.location.search).get("id"));