@extends('layouts.site')

@section('title', $pagina->titulo . ' - Matrix Provedor')

@section('content')
<!-- A classe hero-bg puxa exatamente a imagem e o tom azul do seu CSS original -->
<main class="hero-bg min-h-[80vh] flex-grow flex items-center justify-center pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    
    <div class="w-full flex justify-center z-10 relative">
        <!-- O código que você colar no editor vai renderizar perfeitamente aqui dentro -->
        {!! $pagina->conteudo !!}
    </div>

</main>
@endsection