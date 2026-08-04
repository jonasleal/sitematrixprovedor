@props(['name', 'value' => '', 'id' => null])

@php
    // Se o ID não for informado, usa o mesmo valor do atributo 'name'
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
            skin: 'oxide-dark', // Tema escuro para combinar com o painel
            content_css: 'dark',
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save(); // Sincroniza o conteúdo visual com o <textarea> oculto para o Laravel salvar
                });
            }
        });
    });
</script>