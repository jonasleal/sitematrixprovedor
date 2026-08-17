<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        // 1. Busca todos os utilizadores (EXCETO o ID 1, que é o Super Admin Intocável)
        $users = User::with('permissions')->where('id', '!=', 1)->orderBy('name')->get();
        
        // 2. Busca os Cargos (Templates) e as suas permissões para o Alpine.js usar no Frontend
        $roles = Role::with('permissions')->get();

        // 3. Busca e agrupa as permissões para montar a grelha (Grid) visual
        $todasPermissoes = Permission::all();
        $permissoesAgrupadas = [];
        
        foreach ($todasPermissoes as $permissao) {
            $partes = explode(' ', $permissao->name); // Ex: "editar banners"
            $acao = $partes[0]; // "editar"
            $modulo = $partes[1]; // "banners"
            $permissoesAgrupadas[$modulo][$acao] = $permissao->name;
        }

        return view('admin.equipa.index', compact('users', 'roles', 'permissoesAgrupadas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
            'permissions' => ['nullable', 'array']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Sincroniza as permissões diretas no utilizador
        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.equipa.index')->with('success', 'Membro da equipa adicionado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        if ($id == 1) {
            return back()->with('error', 'O Super Administrador não pode ser alterado.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'password' => ['nullable', Rules\Password::defaults()], // Senha opcional na edição
            'permissions' => ['nullable', 'array']
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Só altera a password se ele digitou uma nova
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Atualiza a grelha de permissões
        $user->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.equipa.index')->with('success', 'Permissões atualizadas com sucesso!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Trava para ninguém conseguir apagar o dono do painel
        if ($user->id === 1 || $user->hasRole('super-admin')) {
            return redirect()->route('admin.equipa.index')->with('error', 'Acesso negado: Não é possível apagar a conta do Superadmin principal.');
        }

        // Previne que o administrador logado apague a si mesmo por aqui
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.equipa.index')->with('error', 'Você não pode apagar a sua própria conta por este menu.');
        }

        $user->delete();
        return redirect()->route('admin.equipa.index')->with('success', 'Utilizador removido com sucesso!');
    }
}