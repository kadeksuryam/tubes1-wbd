function main() {
    const xhrCookie = new XMLHttpRequest();
    xhrCookie.open("GET", "/api/auth/verify-cookie", true);
    xhrCookie.send(null);
    xhrCookie.onreadystatechange = function() {
        if(xhrCookie.readyState === 4) {
            if(xhrCookie.status !== 200) {
                window.location.replace("/login");
            }
        }
    }
    const urlSearchParams = new URLSearchParams(window.location.search);
    const params = Object.fromEntries(urlSearchParams.entries());
    const userId = params["user_id"];
    const isAdmin = (getCookie("is_admin") == 1);
    
    const titleEl = document.getElementById("riwayat-title");
    const headerEl = document.getElementById("riwayat-header");
    
    if(isAdmin) {
        titleEl.innerText = "Riwayat Pengubahan Dorayaki";
        headerEl.innerText = "Riwayat Pengubahan Dorayaki";
    }
    else {
        titleEl.innerText = "Riwayat Pembelian Dorayaki";
        headerEl.innerText = "Riwayat Pembelian Dorayaki";
    }

    if(isNaN(parseInt(userId))) {
        if(!isAdmin) window.location.replace("/forbidden");
        else {
            const xhrAllRiwayat = new XMLHttpRequest();
            xhrAllRiwayat.open("GET", "/api/riwayat/dorayaki");
            xhrAllRiwayat.send(null);
            xhrAllRiwayat.onreadystatechange = function() {
                if(xhrAllRiwayat.readyState === 4) {
                    const resRiwayat = JSON.parse(xhrAllRiwayat.response);
                    setHTMLField(resRiwayat, isAdmin);
                }
            }
        }
    }
    else {
        const xhrRiwayat = new XMLHttpRequest();
        xhrRiwayat.open("GET", `/api/riwayat/dorayaki?user_id=${parseInt(userId)}`);
        xhrRiwayat.send(null);
        xhrRiwayat.onreadystatechange = function() {
            if(xhrRiwayat.readyState === 4) {
                if(xhrRiwayat.status === 200) {
                    const resRiwayat = JSON.parse(xhrRiwayat.response);
                    setHTMLField(resRiwayat, isAdmin);
                }
                else if(xhrRiwayat.status === 403) {
                    window.location.replace("/forbidden");
                }
            }
        }
    }

}

function setHTMLField(riwayats, isAdmin) {
    const tableEl = document.getElementById("riwayat-table");
    if(isAdmin) {
        tableEl.innerHTML += `
            <tr>
                <th>#</th>
                <th>Nama Dorayaki</th>
                <th>Jumlah Pengubahan</th>
                <th>Username Pengubah</th>
                <th>Waktu Pengubahan</th>
            </tr>
        `;
        let idx = 0;
        for(let i=0;i<riwayats.length;i++) {
            tableEl.innerHTML += `
                <tr>
                    <td>${i+1}</td>
                    <td><a href="/detail?id=${riwayats[i].dorayaki_id}">${riwayats[i].dorayaki_nama}</a></td>
                    <td>${riwayats[i].jumlah}</td>
                    <td>${riwayats[i].username}</td>
                    <td>${riwayats[i].updated_at}</td>
                </tr>
            `
            idx += 1;
        }
    } else {
        tableEl.innerHTML += `
            <tr>
                <th>#</th>
                <th>Nama Dorayaki</th>
                <th>Jumlah Pembelian</th>
                <th>Total Harga</th>
                <th>Waktu Pembelian</th>
            </tr>
        `;
        let idx = 0;
        for(let i=0;i<riwayats.length;i++) {
            const totalHarga = parseInt(riwayats[i].dorayaki_harga)*Math.abs(parseInt(riwayats[i].jumlah));

            tableEl.innerHTML += `
                <tr>
                    <td>${i+1}</td>
                    <td><a href="/detail?id=${riwayats[i].dorayaki_id}">${riwayats[i].dorayaki_nama}</a></td>
                    <td>${Math.abs(riwayats[i].jumlah)}</td>
                    <td>${totalHarga}</td>
                    <td>${riwayats[i].updated_at}</td>
                </tr>
            `
            idx += 1;
        }
    }
}

main();