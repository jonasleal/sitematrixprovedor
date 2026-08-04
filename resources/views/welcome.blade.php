@extends('layouts.site')

@section('content')

    @include('partials.hero')
	
	@include('partials.banner')
	
	@include('partials.campaigns')

    @include('partials.plans')
	
	@include('partials.features')

	@include('partials.coverage_news')
	@if(isset($erroDebug) && $erroDebug)
    <script>
        console.group('%c⚠️ Atenção Desenvolvedor (SGP Sync)', 'color: #f59e0b; font-weight: bold; font-size: 14px;');
        console.error("A sincronização em background com o SGP falhou. O site continuou rodando com Fallback.");
        console.info("Detalhe Técnico: {{ $erroDebug }}");
        console.groupEnd();
    </script>
	<!-- ChatConversaAI Widget - Floating Button -->
<script src="https://sistema.chatconversaai.com.br/widget.js" data-slug="alves-leal-ltda-site" data-mode="floating" data-target=""></script>
    @endif
    @endsection