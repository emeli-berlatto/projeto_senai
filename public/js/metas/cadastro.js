let cancelar = document.getElementById('cancelar')

cancelar.addEventListener('click', () => {
    window.location = '../index.shtml'
})

let params = new URLSearchParams(window.location.search);
let id = params.get('id');

if (id) {
    fetch('../../../src/metas.php?id='+id).then(function(resposta) {
        return resposta.json()
    }).then(function(data) {
        populate(data)
    })
}

function populate(data) {
    document.getElementById("id").value = data[0].id
    document.getElementById("data").value = data[0].data
    document.getElementById("descricao").value = data[0].descricao
    document.getElementById("valor").value = data[0].valor
}

let form = document.getElementById('form')

form.addEventListener('submit', e => {
    e.preventDefault();

    fetch(`../../../src/metas.php${id ? '?id=' + id : ''}`, {
        method: id ? 'PUT' : 'POST',
        body: JSON.stringify({
            data: document.getElementById("data").value,
            descricao: document.getElementById("descricao").value,
            valor: document.getElementById("valor").value
        }),
        headers: {
            'Content-Type': 'application/json'
        }    
    }).then(function(resposta) {
        return resposta.json()
    }).then(function(data) {
        window.alert(data.msg)
        if(data.status == 'ok'){
            window.location = '../index.shtml'
        }
    })
})