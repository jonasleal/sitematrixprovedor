@props(['tags', 'selecionada' => '', 'xModel' => null])

<div>
    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Tag do Balão</label>
    <div class="flex gap-2">
        <select name="tag_id" 
                @if($xModel) x-model="{{ $xModel }}" @endif
                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 bg-white text-sm shadow-sm focus:border-indigo-500">
            <option value="">Selecione uma Tag (Opcional)</option>
            @foreach($tags as $tag)
                <option value="{{ $tag->id }}" {{ $selecionada == $tag->id ? 'selected' : '' }}>
                    {{ mb_strtoupper($tag->nome) }}
                </option>
            @endforeach
        </select>
        
        <!-- Este botão aciona a variável modalTags do Alpine.js que estará na página -->
        <button type="button" @click="modalTags = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 rounded-lg text-xs font-bold shadow-sm transition">
            Tags
        </button>
    </div>
</div>