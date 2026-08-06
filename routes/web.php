<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

// ==========================================
// IMPORTS DOS CONTROLADORES DO SITE PÚBLICO
// ==========================================
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\Api\CoberturaController;
use App\Http\Controllers\Api\SgpController;
use App\Http\Controllers\Admin\PlanoDetalheController;
use App\Http\Controllers\PreCadastroController;
use App\Http\Controllers\PaginaController;

// ==========================================
// IMPORTS DOS CONTROLADORES DO ADMIN
// ==========================================
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PoligonoController;
use App\Models\LeadCobertura;
use App\Models\Noticia;

// ==========================================
// ROTAS PÚBLICAS (O SITE)
// ==========================================

// A Home agora usa o novo padrão orquestrador
Route::get('/', [HomeController::class, 'index']);

Route::get('/planos-disponiveis', [HomeController::class, 'precadastro'])->name('precadastro');

Route::get('/sobre', function () {
    return view('sobre');
});
// Rota de Páginas Dinâmicas Institucionais
Route::get('/p/{slug}', [PaginaController::class, 'showPublic'])->name('pagina.show');
// Rota da Listagem de Notícias
Route::get('/noticias', function () {
    // Puxa as notícias ativas, da mais recente para a mais antiga, com paginação
    $noticias = Noticia::where('ativo', true)
            ->orderByRaw('COALESCE(publicado_em, created_at) DESC')
            ->paginate(9);
            
    return view('noticias', compact('noticias'));
});

// Rota de Leitura da Notícia Específica
Route::get('/noticia/{slug}', function ($slug) {
    $noticia = Noticia::where('slug', $slug)->where('ativo', true)->firstOrFail();
    return view('noticia-interna', compact('noticia'));
});

	



// ==========================================
// ROTAS DA API PÚBLICA (O MOTOR DO SITE)
// ==========================================
Route::post('/api/leads', [LeadController::class, 'store']); 
Route::post('/api/check-cobertura', [CoberturaController::class, 'check']);
Route::post('/api/enviar-precadastro', [PreCadastroController::class, 'store']);// Rota de Envio do Pré-cadastro para o SGP
Route::get('/api/poligonos', [PoligonoController::class, 'indexData']);


// ==========================================
// ROTAS DO PAINEL ADMIN (ÁREA RESTRITA)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. Dashboard (Lista de Leads)
    Route::get('/dashboard', function () {
        $leads = LeadCobertura::orderBy('created_at', 'desc')->get();
        return view('dashboard', compact('leads'));
    })->name('dashboard');

    // 2. Mapa de Cobertura (Geofencing)
    Route::get('/admin/mapa-cobertura', [PoligonoController::class, 'index'])->name('admin.mapa');
    Route::get('/admin/mapa-cobertura/data', [PoligonoController::class, 'indexData']); 
    Route::post('/admin/mapa-cobertura', [PoligonoController::class, 'store']);
    Route::put('/admin/mapa-cobertura/{id}', [PoligonoController::class, 'update']);
    Route::delete('/admin/mapa-cobertura/{id}', [PoligonoController::class, 'destroy']);

    // 3. Gerenciamento do Perfil do Administrador
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
	//4. Gerência de Personalização de Planos
    Route::get('/admin/planos', [PlanoDetalheController::class, 'index'])->name('admin.planos.index');
    Route::post('/admin/planos', [PlanoDetalheController::class, 'store'])->name('admin.planos.store');
	// 5. Configurações Globais
    Route::get('/admin/configuracoes', [\App\Http\Controllers\Admin\ConfiguracaoController::class, 'index'])->name('admin.configuracoes.index');
    Route::post('/admin/configuracoes', [\App\Http\Controllers\Admin\ConfiguracaoController::class, 'store'])->name('admin.configuracoes.store');
	// 6. Banners
    Route::get('/admin/banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('admin.banners.index');
    Route::post('/admin/banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('admin.banners.store');
    Route::delete('/admin/banners/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('admin.banners.destroy');
	// 7. Notícias
    Route::get('/admin/noticias', [\App\Http\Controllers\Admin\NoticiaController::class, 'index'])->name('admin.noticias.index');
    Route::post('/admin/noticias', [\App\Http\Controllers\Admin\NoticiaController::class, 'store'])->name('admin.noticias.store');
    Route::get('/admin/noticias/{id}/edit', [\App\Http\Controllers\Admin\NoticiaController::class, 'edit'])->name('admin.noticias.edit');
    Route::put('/admin/noticias/{id}', [\App\Http\Controllers\Admin\NoticiaController::class, 'update'])->name('admin.noticias.update');
    Route::delete('/admin/noticias/{id}', [\App\Http\Controllers\Admin\NoticiaController::class, 'destroy'])->name('admin.noticias.destroy');
    Route::post('/admin/noticias/upload-imagem', [App\Http\Controllers\Admin\NoticiaController::class, 'uploadImagem'])->name('admin.noticias.upload-imagem');
	// 8 . Banners
	// Rotas de Banners Avançados
    Route::get('/admin/banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('admin.banners.index');
    Route::post('/admin/banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('admin.banners.store');
    Route::put('/admin/banners/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('admin.banners.update');
    Route::patch('/admin/banners/{id}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('admin.banners.toggle');
    Route::post('/admin/banners/reordenar', [\App\Http\Controllers\Admin\BannerController::class, 'reordenar'])->name('admin.banners.reordenar');
    Route::delete('/admin/banners/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('admin.banners.destroy');
    // 9 . TAGs
    // Rotas de Gerenciamento de Tags Globais (Banners e Notícias)    
    Route::post('/admin/tags', [\App\Http\Controllers\Admin\TagController::class, 'store'])->name('admin.tags.store');
    Route::put('/admin/tags/{id}', [\App\Http\Controllers\Admin\TagController::class, 'update'])->name('admin.tags.update');
    Route::delete('/admin/tags/{id}', [\App\Http\Controllers\Admin\TagController::class, 'destroy'])->name('admin.tags.destroy');
    // 10. Páginas Institucionais Dinâmicas
    Route::resource('/admin/paginas', PaginaController::class)->names('admin.paginas');
});

// Rota de Bloqueio (Futuramente consultará o SGP pelo IP do cliente)
Route::get('/aviso/bloqueio', function (\Illuminate\Http\Request $request) {
    // Exemplo de como você vai capturar o IP real do cliente depois:
    // $ipCliente = $request->ip();
    
    // Por enquanto, envia uma variável estática para testar a View
    $nomeCliente = 'Cliente'; 
    
    return view('avisos.bloqueio', compact('nomeCliente'));
})->name('aviso.bloqueio');

// Rota de Manutenção
Route::get('/aviso/manutencao', function () {
    return view('avisos.manutencao');
})->name('aviso.manutencao');

require __DIR__.'/auth.php';