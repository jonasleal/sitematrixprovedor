<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="/assets/logo-matrix.png" alt="Matrix Provedor" class="block h-10 w-auto" />
                    </a>
                </div>

                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex">
                    
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @canany(['ver planos', 'ver cobertura'])
                    <div class="hidden sm:flex sm:items-center sm:ms-4">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-md text-gray-600 bg-white hover:text-green-600 focus:outline-none transition ease-in-out duration-150">
                                    <div>Comercial</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('ver planos')
                                    <x-dropdown-link :href="route('admin.planos.index')">{{ __('Planos de Internet') }}</x-dropdown-link>
                                @endcan
                                @can('ver cobertura')
                                    <x-dropdown-link :href="route('admin.mapa')">{{ __('Mapa de Cobertura') }}</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endcanany

                    @canany(['ver banners', 'ver noticias'])
                    <div class="hidden sm:flex sm:items-center sm:ms-4">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-md text-gray-600 bg-white hover:text-green-600 focus:outline-none transition ease-in-out duration-150">
                                    <div>Marketing</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('ver banners')
                                    <x-dropdown-link :href="route('admin.banners.index')">{{ __('Banners da Home') }}</x-dropdown-link>
                                @endcan
                                @can('ver noticias')
                                    <x-dropdown-link :href="route('admin.noticias.index')">{{ __('Notícias e Avisos') }}</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endcanany

                    @canany(['ver paginas', 'ver downloads', 'ver configuracoes', 'ver equipa'])
                    <div class="hidden sm:flex sm:items-center sm:ms-4">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-md text-gray-600 bg-white hover:text-green-600 focus:outline-none transition ease-in-out duration-150">
                                    <div>Sistema</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('ver paginas')
                                    <x-dropdown-link :href="route('admin.paginas.index')">{{ __('Páginas Institucionais') }}</x-dropdown-link>
                                @endcan
                                @can('ver downloads')
                                    <x-dropdown-link :href="route('admin.downloads.index')">{{ __('Central de Downloads') }}</x-dropdown-link>
                                @endcan
                                @can('ver configuracoes')
                                    <x-dropdown-link :href="route('admin.configuracoes.index')">{{ __('Configurações Globais') }}</x-dropdown-link>
                                @endcan
                                
                                @can('ver equipa')
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <x-dropdown-link :href="route('admin.equipa.index')" class="text-indigo-600 font-bold bg-indigo-50 hover:bg-indigo-100">
                                        {{ __('Gestão de Equipa') }}
                                    </x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endcanany
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-md text-gray-500 bg-gray-50 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Meu Perfil') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <span class="text-red-600 font-semibold">{{ __('Sair do Sistema') }}</span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @canany(['ver planos', 'ver cobertura'])
            <div class="px-4 py-2 mt-2 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">
                Comercial
            </div>
            @can('ver planos')
                <x-responsive-nav-link :href="route('admin.planos.index')" :active="request()->routeIs('admin.planos.*')">
                    {{ __('Planos de Internet') }}
                </x-responsive-nav-link>
            @endcan
            @can('ver cobertura')
                <x-responsive-nav-link :href="route('admin.mapa')" :active="request()->routeIs('admin.mapa')">
                    {{ __('Mapa de Cobertura') }}
                </x-responsive-nav-link>
            @endcan
            @endcanany

            @canany(['ver banners', 'ver noticias'])
            <div class="px-4 py-2 mt-2 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">
                Marketing
            </div>
            @can('ver banners')
                <x-responsive-nav-link :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')">
                    {{ __('Banners da Home') }}
                </x-responsive-nav-link>
            @endcan
            @can('ver noticias')
                <x-responsive-nav-link :href="route('admin.noticias.index')" :active="request()->routeIs('admin.noticias.*')">
                    {{ __('Notícias e Avisos') }}
                </x-responsive-nav-link>
            @endcan
            @endcanany

            @canany(['ver paginas', 'ver downloads', 'ver configuracoes', 'ver equipa'])
            <div class="px-4 py-2 mt-2 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">
                Sistema
            </div>
            @can('ver paginas')
                <x-responsive-nav-link :href="route('admin.paginas.index')" :active="request()->routeIs('admin.paginas.*')">
                    {{ __('Páginas Institucionais') }}
                </x-responsive-nav-link>
            @endcan
            @can('ver downloads')
                <x-responsive-nav-link :href="route('admin.downloads.index')" :active="request()->routeIs('admin.downloads.*')">
                    {{ __('Central de Downloads') }}
                </x-responsive-nav-link>
            @endcan
            @can('ver configuracoes')
                <x-responsive-nav-link :href="route('admin.configuracoes.index')" :active="request()->routeIs('admin.configuracoes.*')">
                    {{ __('Configurações Globais') }}
                </x-responsive-nav-link>
            @endcan
            @can('ver equipa')
                <x-responsive-nav-link :href="'#'" class="text-indigo-600 font-bold bg-indigo-50">
                    {{ __('Gestão de Equipa') }}
                </x-responsive-nav-link>
            @endcan
            @endcanany
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 bg-gray-50">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Meu Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <span class="text-red-600 font-semibold">{{ __('Sair do Sistema') }}</span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>