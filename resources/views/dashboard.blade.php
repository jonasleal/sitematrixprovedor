<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel de Controle Matrix') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-bold mb-6 text-green-600 border-b pb-2">Leads Capturados (Aguardando Expansão)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="py-3 px-4 text-left text-sm font-semibold">Data</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold">Cliente</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold">Endereço Pesquisado</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold">WhatsApp</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse($leads as $lead)
                                    @php
                                        // Limpa o número para o link do WhatsApp (deixa só os números)
                                        $numeroLimpo = preg_replace('/[^0-9]/', '', $lead->whatsapp);
                                    @endphp
                                    <tr class="hover:bg-gray-50 border-b border-gray-100 transition">
                                        <td class="py-3 px-4 text-sm">{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="py-3 px-4 text-sm font-medium">{{ $lead->pronome }} {{ $lead->nome }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-500">{{ $lead->endereco_pesquisado }}</td>
                                        <td class="py-3 px-4 text-sm">
                                            <a href="https://wa.me/55{{ $numeroLimpo }}" target="_blank" class="inline-flex items-center text-green-600 font-bold hover:text-green-800 bg-green-50 px-3 py-1 rounded-full">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.388 0 12.033c0 2.126.553 4.195 1.604 6.014L.272 24l6.09-1.6c1.76.953 3.743 1.455 5.769 1.455 6.646 0 12.032-5.388 12.032-12.033C24.163 5.388 18.777 0 12.031 0zm0 21.84c-1.802 0-3.563-.485-5.11-1.405l-.367-.217-3.8.998 1.018-3.7-.238-.38c-1.006-1.602-1.536-3.456-1.536-5.372 0-5.69 4.636-10.329 10.334-10.329s10.33 4.639 10.33 10.329c0 5.69-4.636 10.329-10.33 10.329zm5.666-7.75c-.31-.155-1.838-.908-2.122-1.012-.283-.103-.49-.155-.697.155-.207.31-.8 1.012-.98 1.218-.18.207-.36.233-.67.078-1.503-.746-2.585-1.39-3.64-3.19-.207-.352-.022-.542.133-.697.14-.14.31-.362.465-.543.155-.18.207-.31.31-.517.103-.207.052-.388-.026-.543-.078-.155-.697-1.68-.954-2.302-.25-.606-.505-.523-.697-.533-.18-.01-.388-.01-.595-.01-.207 0-.543.078-.827.414-.284.336-1.086 1.06-1.086 2.587 0 1.527 1.112 3.003 1.267 3.21.155.207 2.19 3.345 5.305 4.686.74.322 1.318.514 1.767.658.744.237 1.423.203 1.956.123.601-.09 1.838-.752 2.097-1.478.259-.726.259-1.348.181-1.478-.077-.13-.284-.207-.594-.362z"/></svg>
                                                {{ $lead->whatsapp }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                                {{ $lead->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-4 text-center text-gray-500">Ainda não temos leads capturados. Divulgue o site!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>