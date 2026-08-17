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
use App\Http\Controllers\DownloadController;

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
// Central de Downloads Pública
Route::get('/p/downloads', [DownloadController::class, 'showPublic'])->name('downloads.show');
// NOVO: Interceptor de Cliques (Contador de Hits)
Route::get('/go-download/{id}', [DownloadController::class, 'registrarClique'])->name('download.go');

Route::get('/sobre', function () {
    return view('sobre');
});
// Rota de Páginas Dinâmicas Institucionais
Route::get('/p/{slug}', [PaginaController::class, 'showPublic'])->name('pagina.show');
// Rota da Listagem de Notícias
// Rota da Página de Notícias com Filtro
Route::get('/noticias', [App\Http\Controllers\HomeController::class, 'noticias'])->name('noticias.index');

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
    
    // ==========================================
    // ROTAS BASE (Todos Logados Acessam)
    // ==========================================
    
    // O Dashboard será visível, mas os cards/listas lá dentro podem ser travados via @can no Blade
    Route::get('/dashboard', function () {
        $leads = LeadCobertura::orderBy('created_at', 'desc')->get();
        return view('dashboard', compact('leads'));
    })->name('dashboard');

    // Gerenciamento do próprio Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // ==========================================
    // GRUPOS PROTEGIDOS POR PERMISSÃO
    // ==========================================

    // COBERTURA (Leads/Mapa)
    Route::middleware('can:ver cobertura')->group(function () {
        Route::get('/admin/mapa-cobertura', [PoligonoController::class, 'index'])->name('admin.mapa');
        Route::get('/admin/mapa-cobertura/data', [PoligonoController::class, 'indexData']); 
        Route::get('/admin/mapa-cobertura/ctos', function (\App\Services\SgpService $sgp) {
            return response()->json($sgp->getDadosMapaNoc());
        });
        Route::post('/admin/mapa-cobertura', [PoligonoController::class, 'store'])->middleware('can:criar cobertura');
        Route::put('/admin/mapa-cobertura/{id}', [PoligonoController::class, 'update'])->middleware('can:editar cobertura');
        Route::delete('/admin/mapa-cobertura/{id}', [PoligonoController::class, 'destroy'])->middleware('can:excluir cobertura');
    });

    // PLANOS
    Route::middleware('can:ver planos')->group(function () {
        Route::get('/admin/planos', [PlanoDetalheController::class, 'index'])->name('admin.planos.index');
        Route::post('/admin/planos', [PlanoDetalheController::class, 'store'])->name('admin.planos.store')->middleware('can:criar planos');
    });

    // CONFIGURAÇÕES GLOBAIS
    Route::middleware('can:ver configuracoes')->group(function () {
        Route::get('/admin/configuracoes', [\App\Http\Controllers\Admin\ConfiguracaoController::class, 'index'])->name('admin.configuracoes.index');
        Route::post('/admin/configuracoes', [\App\Http\Controllers\Admin\ConfiguracaoController::class, 'store'])->name('admin.configuracoes.store')->middleware('can:editar configuracoes');
        Route::post('/admin/backup/run', [\App\Http\Controllers\Admin\ConfiguracaoController::class, 'runBackup'])->name('admin.backup.run')->middleware('can:editar configuracoes');
    });

    // BANNERS (Com as rotas avançadas)
    Route::middleware('can:ver banners')->group(function () {
        Route::get('/admin/banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('admin.banners.index');
        Route::post('/admin/banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('admin.banners.store')->middleware('can:criar banners');
        Route::put('/admin/banners/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('admin.banners.update')->middleware('can:editar banners');
        Route::patch('/admin/banners/{id}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('admin.banners.toggle')->middleware('can:editar banners');
        Route::post('/admin/banners/reordenar', [\App\Http\Controllers\Admin\BannerController::class, 'reordenar'])->name('admin.banners.reordenar')->middleware('can:editar banners');
        Route::delete('/admin/banners/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('admin.banners.destroy')->middleware('can:excluir banners');
    });

    // NOTÍCIAS
    Route::middleware('can:ver noticias')->group(function () {
        Route::get('/admin/noticias', [\App\Http\Controllers\Admin\NoticiaController::class, 'index'])->name('admin.noticias.index');
        Route::post('/admin/noticias', [\App\Http\Controllers\Admin\NoticiaController::class, 'store'])->name('admin.noticias.store')->middleware('can:criar noticias');
        Route::get('/admin/noticias/{id}/edit', [\App\Http\Controllers\Admin\NoticiaController::class, 'edit'])->name('admin.noticias.edit')->middleware('can:editar noticias');
        Route::put('/admin/noticias/{id}', [\App\Http\Controllers\Admin\NoticiaController::class, 'update'])->name('admin.noticias.update')->middleware('can:editar noticias');
        Route::post('/admin/noticias/upload-imagem', [\App\Http\Controllers\Admin\NoticiaController::class, 'uploadImagem'])->name('admin.noticias.upload-imagem')->middleware('can:criar noticias');
        Route::delete('/admin/noticias/{id}', [\App\Http\Controllers\Admin\NoticiaController::class, 'destroy'])->name('admin.noticias.destroy')->middleware('can:excluir noticias');
    });

    // TAGS (Usadas por Banners e Notícias)
    // Se o usuário pode ver banners ou notícias, ele pode ver as tags, mas para criar/editar ele precisa de permissão explícita
    Route::middleware('can:editar banners,editar noticias')->group(function () {
        Route::post('/admin/tags', [\App\Http\Controllers\Admin\TagController::class, 'store'])->name('admin.tags.store');
        Route::put('/admin/tags/{id}', [\App\Http\Controllers\Admin\TagController::class, 'update'])->name('admin.tags.update');
        Route::delete('/admin/tags/{id}', [\App\Http\Controllers\Admin\TagController::class, 'destroy'])->name('admin.tags.destroy');
    });

    // PÁGINAS INSTITUCIONAIS DINÂMICAS (Resource protegido no Controller ou via macro)
    Route::resource('/admin/paginas', PaginaController::class)->names('admin.paginas')->middleware('can:ver paginas');
    
    // CENTRAL DE DOWNLOADS (Resource protegido no Controller ou via macro)
    Route::resource('/admin/downloads', DownloadController::class)->names('admin.downloads')->middleware('can:ver downloads');

    // GESTÃO DE EQUIPA
    Route::middleware('can:ver equipa')->group(function () {
        Route::get('admin/equipa', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.equipa.index');
        Route::post('admin/equipa', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.equipa.store')->middleware('can:criar equipa');
        Route::put('admin/equipa/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.equipa.update')->middleware('can:editar equipa');
        Route::delete('admin/equipa/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.equipa.destroy')->middleware('can:excluir equipa');
    });

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