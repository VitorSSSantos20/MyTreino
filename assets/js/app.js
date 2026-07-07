document.addEventListener('DOMContentLoaded', function () {

    /* ---------- 1. Validação dos formulários ---------- */
    // Todo <form> com a classe .needs-validation só envia se for válido.
    var formularios = document.querySelectorAll('.needs-validation');

    formularios.forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            if (!form.checkValidity()) {
                evento.preventDefault();
                evento.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Validação extra: confirmação de senha no cadastro
    var senha = document.getElementById('senha');
    var confirmar = document.getElementById('confirmar_senha');

    if (senha && confirmar) {
        function validarSenhas() {
            if (confirmar.value !== senha.value) {
                confirmar.setCustomValidity('As senhas não são iguais.');
            } else {
                confirmar.setCustomValidity('');
            }
        }
        senha.addEventListener('input', validarSenhas);
        confirmar.addEventListener('input', validarSenhas);
    }

    /* ---------- 2. Confirmação antes de excluir ---------- */
    // Todo <form> com data-confirm="mensagem" pede confirmação antes de enviar.
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            var mensagem = form.getAttribute('data-confirm') || 'Tem certeza?';
            if (!window.confirm(mensagem)) {
                evento.preventDefault();
            }
        });
    });

    /* ---------- 3. Alertas que somem sozinhos ---------- */
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (alerta) {
        setTimeout(function () {
            // Usa o componente de alerta do Bootstrap para fechar com animação
            var instancia = bootstrap.Alert.getOrCreateInstance(alerta);
            instancia.close();
        }, 4000); // 4 segundos
    });

    /* ---------- 4. Campos do formulário de treino ---------- */
    // No formulário de treino existem dois rádios: "treino" e "descanso".
    // Quando "descanso" é escolhido, escondemos os campos do treino e
    // removemos o "required" deles (para o formulário poder ser enviado).
    var radiosTipo = document.querySelectorAll('input[name="tipo_dia"]');
    var camposTreino = document.getElementById('campos-treino');

    if (radiosTipo.length && camposTreino) {
        var obrigatorios = camposTreino.querySelectorAll('[data-obrigatorio]');

        function atualizarCampos() {
            var tipoEscolhido = document.querySelector('input[name="tipo_dia"]:checked').value;
            var ehTreino = (tipoEscolhido === 'treino');

            camposTreino.classList.toggle('d-none', !ehTreino);

            obrigatorios.forEach(function (campo) {
                campo.required = ehTreino;
            });
        }

        radiosTipo.forEach(function (radio) {
            radio.addEventListener('change', atualizarCampos);
        });

        atualizarCampos(); // aplica o estado inicial ao abrir a página
    }
});
