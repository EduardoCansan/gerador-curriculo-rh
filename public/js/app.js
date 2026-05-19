// ==========================================
//  RH PADRONIZADOR — JavaScript Principal
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    // ---- Toggle de entrada de currículo (texto/arquivo) ----
    const tipoTexto   = document.getElementById('tipoTexto');
    const tipoArquivo = document.getElementById('tipoArquivo');
    const secaoTexto  = document.getElementById('secaoTexto');
    const secaoArquivo= document.getElementById('secaoArquivo');

    function toggleEntrada() {
        if (!tipoTexto || !secaoTexto) return;
        const isArquivo = tipoArquivo.checked;
        secaoTexto.style.display  = isArquivo ? 'none' : 'block';
        secaoArquivo.style.display= isArquivo ? 'block' : 'none';
    }

    tipoTexto?.addEventListener('change', toggleEntrada);
    tipoArquivo?.addEventListener('change', toggleEntrada);

    // ---- File drop: mostrar nome do arquivo selecionado ----
    const fileInput   = document.getElementById('arquivo');
    const fileSelected= document.getElementById('fileSelected');

    fileInput?.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (file && fileSelected) {
            fileSelected.style.display = 'block';
            fileSelected.textContent   = `📎 ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            document.querySelector('.file-drop-content').style.display = 'none';
        }
    });

    // ---- Botão de processar: loading state ----
    const formProcessar = document.getElementById('formProcessar');
    const btnProcessar  = document.getElementById('btnProcessar');

    formProcessar?.addEventListener('submit', () => {
        if (btnProcessar) {
            btnProcessar.disabled   = true;
            btnProcessar.textContent= '⏳ Processando com IA... aguarde';
        }
    });

    // ---- Busca rápida na tabela de currículos ----
    const searchInput = document.getElementById('searchInput');
    const table       = document.getElementById('curriculosTable');

    searchInput?.addEventListener('input', () => {
        const term = searchInput.value.toLowerCase();
        table?.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    // ---- Auto-dismiss de alertas após 5 segundos ----
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
