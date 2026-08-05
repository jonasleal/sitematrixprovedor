@props(['name', 'value' => '', 'id' => null])

@php
    $editorId = $id ?? $name;
@endphp

<div wire:ignore>
    <textarea name="{{ $name }}" id="{{ $editorId }}" class="hidden">{!! $value !!}</textarea>
</div>

<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        tinymce.init({
            selector: '#{{ $editorId }}',
            plugins: 'code lists link image table preview fullscreen',
            toolbar: 'undo redo | blocks | bold italic strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist | link image | code fullscreen',
            menubar: false,
            promotion: false, // Esconde as propagandas do TinyMCE
            branding: false,  // Esconde a marca d'água "Powered by TinyMCE"
            height: 500,
            skin: 'oxide',
            content_css: 'default',
            
            // HABILITA UPLOAD DE IMAGENS DIRETO NO EDITOR
            images_upload_url: '{{ route("admin.noticias.upload-imagem") }}',
            images_upload_credentials: true,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    var xhr, formData;
                    xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '{{ route("admin.noticias.upload-imagem") }}');
                    
                    // Adiciona o token CSRF do Laravel obrigatoriamente
                    var csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                    } else {
                        console.error('Meta tag CSRF-Token não encontrada no head do layout admin.');
                    }

                    xhr.upload.onprogress = function (e) {
                        progress(e.loaded / e.total * 100);
                    };

                    xhr.onload = function() {
                        if (xhr.status === 403) {
                            reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                            return;
                        }
                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('Erro no servidor HTTP: ' + xhr.status);
                            return;
                        }
                        var json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location != 'string') {
                            reject('Resposta inválida: ' + xhr.responseText);
                            return;
                        }
                        resolve(json.location); // Retorna a URL da imagem de volta pro Editor
                    };
                    xhr.onerror = function () {
                        reject('Falha de conexão durante o upload. Code: ' + xhr.status);
                    };
                    formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                });
            },
            // ---------------------------------------------
            
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save(); // Sincroniza o conteúdo visual com o <textarea> oculto
                });
            }
        });
    });
</script>