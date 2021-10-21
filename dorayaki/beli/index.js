function main() {
    let currPath = window.location.pathname;
    let parsePath = currPath.split("/");
    let dorayakiId = parsePath[3];
    if(isNaN(parseInt(dorayakiId))) window.location.href = "/notfound"

    xhrCookie = new XMLHttpRequest();
    xhrCookie.open("GET", "/api/auth/verify-cookie", true);
    xhrCookie.send(null);
    xhrCookie.onreadystatechange = function() {
        if(xhrCookie.readyState === 4) {
            if(xhrCookie.status !== 200) {
                window.location.replace("/login");
            }
        }
    }
    let isAdmin = getCookie("is_admin");

    let xhrDorayaki = new XMLHttpRequest();
    xhrDorayaki.open("GET", "/api/dorayakis/" + dorayakiId, true);
    xhrDorayaki.send(null);

    xhrDorayaki.onreadystatechange = function() {
        if(xhrDorayaki.status === 404) {
            window.location.href = "/notfound";
            return;
        }
        else if(xhrDorayaki.status === 200) {
            let dorayakiData = JSON.parse(xhrDorayaki.response);
            console.log(dorayakiData);
            initField(dorayakiData);
            let changeStokButton = document.getElementById("change-stok-button");
            changeStokButton.addEventListener("click", function() {
                let deltaStok = document.getElementById("delta-stok").value;
                let notifEl = document.getElementsByClassName("notification")[0];
                if(deltaStok == 0) {
                    notifEl.innerHTML = "<p style='color:red'>Error: transaksi gagal<br \/></p>";
                }
                else {
                    if(isAdmin == 0) deltaStok *= -1;
                    let stokVal = parseInt(dorayakiData.stok) + parseInt(deltaStok);

                    let xhrDorayaki = new XMLHttpRequest();
                    let params=`stok=${stokVal}`;
                    xhrDorayaki.open("POST", "/api/dorayakis/" + dorayakiId + "?type=update", true);
                    xhrDorayaki.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhrDorayaki.send(params);
            
                    xhrDorayaki.onload = function() {
                        if(xhrDorayaki.status === 200) {
                            let xhrDorayaki = new XMLHttpRequest();
                            xhrDorayaki.open("GET", "/api/dorayakis/" + dorayakiId, true);
                            xhrDorayaki.send(null);
            
                            xhrDorayaki.onload = function() {
                                if(xhrDorayaki.status === 200) {
                                    dorayakiData = JSON.parse(xhrDorayaki.response);
                                    document.getElementById("gambar").src = dorayakiData.gambar;
                                    document.getElementById("nama").innerText = dorayakiData.nama;
                                    document.getElementById("stok").innerText = `Stok sekararang: ${dorayakiData.stok}`;
                                    document.getElementById("delta-stok").value = 0;
                                }
                            }
                            if(isAdmin == 0) notifEl.innerHTML = "<p style='color:green'>Dorayaki berhasil dibeli</p>";
                            else notifEl.innerHTML = "<p style='color:green'>Dorayaki berhasil diubah</p>";
                        }
                        else {
                            notifEl.innerHTML = "<p style='color:red'>Error: transaksi gagal<br \/></p>";
                        }
                    }
                }
                setTimeout(function() {
                    notifEl.innerHTML = "";
                }, 2000);
            })
        }
    }
}

function initField(dorayakiData) {
    let isAdmin = getCookie("is_admin");
    let cardEl = document.getElementsByClassName("card")[0];
    if(isAdmin == 1) {
        document.getElementsByTagName("title")[0].innerText = "Pengubahan Dorayaki";
        document.getElementById("judul").innerText = "Pengubahan Dorayaki";
        cardEl.innerHTML = `
            <img id="gambar" src="${dorayakiData.gambar}" alt="dorayaki-gambar">
            <div class="card-container">
                <h4><b id="nama">${dorayakiData.nama}</b></h4>
                <p id="stok">Stok sekararang: ${dorayakiData.stok}</p>
                <label for="delta-stok">Jumlah Perubahan:</label>
                <input type="number" id="delta-stok" name="stok" value="0" placeholder="Jumlah pengubahan stok"><br/>
                <button id="change-stok-button">Ubah Stok</button>
            </div>
        `
    }
    else {
        document.getElementsByTagName("title")[0].innerText = "Pembelian Dorayaki";
        document.getElementById("judul").innerText = "Pembelian Dorayaki";
        cardEl.innerHTML = `
            <img id="gambar" src="${dorayakiData.gambar}" alt="dorayaki-gambar">
            <div class="card-container">
                <h4><b id="nama">${dorayakiData.nama}</b></h4>
                <p id="stok">Stok sekararang: ${dorayakiData.stok}</p>
                <label for="delta-stok">Jumlah Pembelian:</label>
                <input type="number" id="delta-stok" name="stok" min="1" value="1" placeholder="Jumlah pembelian"><br/>
                <button id="change-stok-button">Beli Dorayaki</button>
            </div>
        `
    }
}

main();
