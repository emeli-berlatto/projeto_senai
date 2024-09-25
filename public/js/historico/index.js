let cancelar = document.getElementById('cancelar')

cancelar.addEventListener('click', () => {
    window.location = '../index.shtml'
})

let params = new URLSearchParams(window.location.search);
let id = params.get('id');

if (id) {
    fetch('../../../src/conta.php?id='+id).then(function(resposta) {
        return resposta.json()
    }).then(function(data) {
        populate(data)
    })
}

function populate(data) {
    document.getElementById("id").value = data[0].id
    document.getElementById("descricao").value = data[0].descricao
    document.getElementById("banco").value = data[0].banco
    document.getElementById("saldo").value = data[0].saldo
}

let form = document.getElementById('form')

form.addEventListener('submit', e => {
    e.preventDefault();

    fetch(`../../../src/conta.php${id ? '?id=' + id : ''}`, {
        method: id ? 'PUT' : 'POST',
        body: JSON.stringify({
            descricao: document.getElementById("descricao").value,
            banco: document.getElementById("banco").value,
            saldo: document.getElementById("saldo").value
        }),
        headers: {
            'Content-Type': 'application/json'
        }    
    }).then(function(resposta) {
        return resposta.json()
    }).then(function(data) {
        window.alert(data.msg)
        window.location = 'index.shtml'
    })
})