let request = new XMLHttpRequest();
function processData(){
    "use strict";
    if(request.readyState === XMLHttpRequest.DONE && request.status === 200){
        if(request.responseText != null){
            const data = JSON.parse(request.responseText);
            process(data);
        }
        else {
            console.error('Dokument ist leer');
        }
    }
    else {
        console.error('Uebertragung fehlgeschlagen');
    }
}


function requestData(){
    "use strict";
    request.open("GET", "KundenStatus.php")
    request.onreadystatechange = processData;
    request.send(null);
}

function process(jsonData) {
    "use strict";
    let statusSection = document.getElementById('status_section');

    while (statusSection.firstChild) {
        statusSection.removeChild(statusSection.firstChild);
    }

    if (jsonData.length === 0) {
        const noPizzaMessage = document.createElement("p");
        noPizzaMessage.textContent = "No pizzas available.";
        statusSection.appendChild(noPizzaMessage);
        return;
    }

    //Make paragraph for order id
    const orderId = document.createElement("h2");
    orderId.textContent = "Bestellung: "+jsonData[0].ordering_id;
    statusSection.appendChild(orderId);

    jsonData.forEach(statusObj => {
        let article = document.createElement("article");
        const articleName = document.createElement("p");
        articleName.textContent = "Pizza: " + statusObj.name;
        article.appendChild(articleName);


        for (let i = 0; i < 5; i++) {
            const radio = document.createElement("input");
            radio.type = "radio";
            radio.name = "status_"+statusObj.ordered_article_id;
            radio.value = i;
            article.appendChild(radio);

            const label = document.createElement("label");
            if(i === 0) {
                label.textContent = "bestellt";
            } else if(i === 1) {
                label.textContent = "im Ofen";
            } else if(i === 2) {
                label.textContent = "fertig";
            } else if(i === 3) {
                label.textContent = "unterwegs";
            } else if(i === 4) {
                label.textContent = "geliefert";
            }

            article.appendChild(label)

            if (statusObj.status == i) {
                radio.checked = true;
            }
            radio.disabled = true;

        }
        statusSection.appendChild(article);
    });
}
window.onload = function() {
    requestData();
    setInterval(requestData, 2000);
};