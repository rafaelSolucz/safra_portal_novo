// Variável global para rastrear o tipo de documento atual
let currentDocType = 'cpf';

// --- Lógica simples para alternar entre CPF e CNPJ no frontend ---
function toggleDocType(type) {
    currentDocType = type; // Atualiza o estado global
    
    const btnCpf = document.getElementById('btn-cpf');
    const btnCnpj = document.getElementById('btn-cnpj');
    const labelDoc = document.getElementById('label-doc');
    const inputDoc = document.getElementById('documento');
    const docError = document.getElementById('doc-error');

    // Limpa o input e as mensagens de erro ao trocar de aba
    inputDoc.value = '';
    if(docError) docError.textContent = '';

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

// --- Funções de Validação de Documento ---
function validaCPF(cpf) {
    if (typeof cpf !== 'string') return false;
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf.length !== 11 || !!cpf.match(/(\d)\1{10}/)) return false;
    cpf = cpf.split('').map(el => +el);
    const rest = (count) => (cpf.slice(0, count - 12).reduce((soma, el, index) => soma + el * (count - index), 0) * 10) % 11 % 10;
    return rest(10) === cpf[9] && rest(11) === cpf[10];
}

function validaCNPJ(cnpj) {
    if (typeof cnpj !== 'string') return false;
    cnpj = cnpj.replace(/[^\d]+/g, '');
    if (cnpj.length !== 14 || !!cnpj.match(/(\d)\1{13}/)) return false;
    
    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0)) return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }

    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    return resultado == digitos.charAt(1);
}

// --- Eventos de Máscara e Submissão do Formulário ---
document.addEventListener('DOMContentLoaded', () => {
    const inputDoc = document.getElementById('documento');
    const formLogin = document.querySelector('form[action="/safra_portal_novo/public/login"]');
    const docError = document.getElementById('doc-error');

    if (inputDoc) {
        // Aplica a máscara de CPF/CNPJ dinamicamente ao digitar
        inputDoc.addEventListener('input', (e) => {
            // Limpa a mensagem de erro assim que o usuário volta a digitar
            if (docError) docError.textContent = ''; 
            
            let value = e.target.value.replace(/\D/g, '');

            if (currentDocType === 'cpf') {
                value = value.substring(0, 11);
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                value = value.substring(0, 14);
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });
    }

    if (formLogin) {
        // Valida o documento no envio do formulário
        formLogin.addEventListener('submit', (e) => {
            const docValue = inputDoc.value.replace(/\D/g, '');
            let isValid = false;
            
            if (currentDocType === 'cpf') {
                if (docValue.length === 11) {
                    isValid = validaCPF(docValue);
                    if (!isValid) docError.textContent = 'O CPF informado é inválido.';
                } else {
                    docError.textContent = 'O CPF deve ter 11 dígitos.';
                }
            } else if (currentDocType === 'cnpj') {
                if (docValue.length === 14) {
                    isValid = validaCNPJ(docValue);
                    if (!isValid) docError.textContent = 'O CNPJ informado é inválido.';
                } else {
                    docError.textContent = 'O CNPJ deve ter 14 dígitos.';
                }
            }

            // Impede o envio se for inválido
            if (!isValid) {
                e.preventDefault();
            } else {
                // Efeito visual de loading no botão caso passe na validação
                const submitButton = formLogin.querySelector('.btn-submit');
                if(submitButton) {
                    submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Acessando...';
                    submitButton.disabled = true;
                }
            }
        });
    }
});

// --- Funções Restantes do Portal (Boleto) ---
async function gerarBoleto(contratoId) {
    const btn = document.getElementById('btn-gerar-boleto');
    const textoOriginal = btn.innerHTML;
    
    // Feedback visual de carregamento
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Gerando Boleto...';
    btn.disabled = true;

    try {
        const response = await fetch('/safra_portal_novo/public/gerar-boleto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ contrato: contratoId })
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('linha-digitavel').innerText = data.linha_digitavel;
            document.getElementById('boleto-action-container').style.display = 'none';
            document.getElementById('boleto-success-container').style.display = 'block';
        } else {
            alert('Erro ao gerar boleto. Tente novamente.');
            btn.innerHTML = textoOriginal;
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
        alert('Ocorreu um erro de conexão.');
        btn.innerHTML = textoOriginal;
        btn.disabled = false;
    }
}

function copiarCodigo() {
    const linhaDigitavel = document.getElementById('linha-digitavel').innerText;
    
    navigator.clipboard.writeText(linhaDigitavel).then(() => {
        const btnCopy = document.querySelector('.btn-copy');
        const originalHtml = btnCopy.innerHTML;
        
        btnCopy.innerHTML = '<i class="fa-solid fa-check"></i> Código Copiado!';
        btnCopy.style.backgroundColor = '#16a34a'; 
        
        setTimeout(() => {
            btnCopy.innerHTML = originalHtml;
            btnCopy.style.backgroundColor = 'var(--primary-color)';
        }, 2000);
    }).catch(err => {
        console.error('Erro ao copiar', err);
        alert('Erro ao copiar o código. Seu navegador pode não suportar esta função.');
    });
}