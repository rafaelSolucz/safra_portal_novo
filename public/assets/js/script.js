// Lógica simples para alternar entre CPF e CNPJ no frontend
function toggleDocType(type) {
    const btnCpf = document.getElementById('btn-cpf');
    const btnCnpj = document.getElementById('btn-cnpj');
    const labelDoc = document.getElementById('label-doc');
    const inputDoc = document.getElementById('documento');

    if (type === 'cpf') {
        btnCpf.classList.add('active');
        btnCnpj.classList.remove('active');
        labelDoc.innerText = 'CPF';
        inputDoc.placeholder = 'Digite seu CPF';
    } else {
        btnCnpj.classList.add('active');
        btnCpf.classList.remove('active');
        labelDoc.innerText = 'CNPJ';
        inputDoc.placeholder = 'Digite seu CNPJ';
    }
}