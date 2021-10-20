function main() {
    document.cookie = "is_admin=1; Path=/; Expires=Thu, 21 Oct 2021 08:20:20 GMT;";
    document.cookie = "session_id=616fd144ae756; Path=/; Expires=Thu, 21 Oct 2021 08:20:20 GMT;";
    document.cookie = "user_id=1; Path=/; Expires=Thu, 21 Oct 2021 08:20:20 GMT;";

    let currPath = window.location.pathname;
    let parsePath = currPath.split("/");
    let dorayakiId = parsePath[3];
    if(isNaN(parseInt(dorayakiId))) window.location.href = "/notfound"

    let req = new XMLHttpRequest();
    req.open("GET", "/api/dorayakis/" + dorayakiId, false);
    req.send(null);
    if(req.status != 200) {
        window.location.href = "/notfound";
        return;
    }
    let dorayakiData = JSON.parse(req.response);

    let cardEl = document.getElementsByClassName("card")[0];
    let isAdmin = getCookie("isAdmin");

    if(isAdmin) {
        cardEl.innerHTML = `
            <img src="${dorayakiData.gambar}" alt="dorayaki-gambar">
            <div class="card-container">
                <h4><b>${dorayakiData.nama}</b></h4>
                <p>Stok sekararang: ${dorayakiData.stok}</p>
                <input type="number" name="stok" value="0" placeholder="Jumlah pengubahan stok"><br/>
                <button>Ubah Stok</button>
            </div>
        `
    }
    else {
        // let totalHarga = dorayakiData.harga*
        // cardEl.innerHTML = `
        //     <img src="${dorayakiData.gambar}" alt="dorayaki-gambar">
        //     <div class="card-container">
        //         <h4><b>${dorayakiData.nama}</b></h4>
        //         <p id="stok">Stok sekararang: ${dorayakiData.stok}</p>
        //         <p id="harga>Harga: ${dorayakiData.harga}</p>
        //         <p id="total-harga">Total harga: ${dorayaki}</p>
        //         <input type="number" name="stok" value="0" placeholder="Jumlah pengubahan stok"><br/>
        //         <button>Beli</button>
        //     </div>
        
    }

}

main();
