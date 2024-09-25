let cancelar = document.getElementById('cancelar')

cancelar.addEventListener('click', () => {
    window.location = '../index.shtml'
})

let params = new URLSearchParams(window.location.search);
let id = params.get('id');

if (id) {
    fetch('../../../src/despesa.php?id='+id).then(function(resposta) {
        return resposta.json()
    }).then(function(data) {
        populate(data)
    })
}

function populate(data) {
    document.getElementById("id").value = data[0].id
    document.getElementById("valor").value = data[0].valor
    document.getElementById("efetuado").value = data[0].efetuado
    document.getElementById("data").value = data[0].data
    document.getElementById("descricao").value = data[0].descricao
    document.getElementById("categoria").value = data[0].categoria
    document.getElementById("conta").value = data[0].conta
}

let form = document.getElementById('form')

form.addEventListener('submit', e => {
    e.preventDefault();
    
    fetch(`../../../src/despesa.php${id ? '?id=' + id : ''}`, {
        method: id ? 'PUT' : 'POST',
        body: JSON.stringify({
            valor: document.getElementById("valor").value,
            efetuado: document.getElementById("efetuado").value,
            data: document.getElementById("data").value,
            descricao: document.getElementById("descricao").value,
            categoria: document.getElementById("categoria").value,
            conta: document.getElementById("conta").value
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