function addPizza(){
    "use strict";
    let articleId = this.getAttribute('data-article-id');
    let articleName = this.getAttribute('data-article-name');
    let articlePrice = parseFloat(this.getAttribute('data-article-price'));

    let option = document.createElement('option');
    option.value = articleId;
    option.text = articleName;
    option.setAttribute('data-article-price', articlePrice);
    option.selected = true;

    let warenkorb = document.getElementById('warenkorb');
    warenkorb.appendChild(option);

    updateTotalPrice();
}

function updateTotalPrice(){
    "use strict";
    let warenkorb = document.getElementById('warenkorb');
    let totalPrice = 0.0;

    for(let i = 0; i < warenkorb.options.length; i++){
        totalPrice += parseFloat(warenkorb.options[i].getAttribute('data-article-price'));
    }

    document.getElementById('totalPrice').textContent = 'Gesamtpreis: ' + totalPrice.toFixed(2) + '€';
}

function deleteAll(){
    "use strict";
    let warenkorb = document.getElementById('warenkorb');
    while(warenkorb.options.length > 0)
        warenkorb.removeChild(warenkorb.firstChild);

    updateTotalPrice();
}
function deleteSelection(){
    "use strict";
    let warenkorb = document.getElementById('warenkorb');
    for(let i = 0; i < warenkorb.options.length; i++){
        if(warenkorb.options[i].selected) {
            warenkorb.removeChild(warenkorb.options[i]);
            i--;
        }
    }

    updateTotalPrice();
}

function checkWarenkorbAndAddressField(){
    let warenkorb = document.getElementById('warenkorb');
    let addressInput = document.getElementById('addressInput');
    let bestellenButton = document.getElementById('bestellenButton');

    if(warenkorb.options.length === 0 || addressInput.value.trim() === ''){
        bestellenButton.disabled = true;
    } else {
        bestellenButton.disabled = false;
    }
}

document.getElementById('addressInput').addEventListener('input', checkWarenkorbAndAddressField);
document.getElementById('warenkorb').addEventListener('input', checkWarenkorbAndAddressField);
document.getElementById('warenkorb').addEventListener('change', checkWarenkorbAndAddressField);

checkWarenkorbAndAddressField();
