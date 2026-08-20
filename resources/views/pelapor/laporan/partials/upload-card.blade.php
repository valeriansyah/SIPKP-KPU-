<div class="border rounded-lg p-4 relative overflow-hidden group transition-all duration-200 {{ $errors->has('documents.'.$type->id) ? 'border-red-500 bg-red-50/10' : 'border-gray-200 hover:border-primary/50 hover:shadow-sm bg-white' }}" id="upload-card-{{ $type->id }}">
    
    <!-- Header: Name & Badge -->
    <div class="flex justify-between items-start mb-2">
        <label for="doc_{{ $type->id }}" class="block text-sm font-medium text-gray-900 cursor-pointer group-hover:text-primary transition-colors">
            {{ $type->name }}
        </label>
        @if($type->is_required)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                Wajib
            </span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                Opsional
            </span>
        @endif
    </div>
    
    <!-- Description -->
    <p class="text-xs text-muted mb-4">{{ $type->description }}</p>

    <!-- State: Not Uploaded (Default) -->
    <div id="state-empty-{{ $type->id }}" class="upload-state-empty border-2 border-dashed border-gray-200 rounded-md p-4 text-center cursor-pointer hover:bg-gray-50 transition-colors" onclick="document.getElementById('doc_{{ $type->id }}').click()">
        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
        </svg>
        <p class="text-xs text-gray-500 font-medium">Klik untuk memilih file</p>
        <p class="text-[10px] text-gray-400 mt-1">PDF, JPG, PNG (Maks. 5MB)</p>
    </div>

    <!-- State: File Selected -->
    <div id="state-filled-{{ $type->id }}" class="upload-state-filled hidden bg-gray-50 border border-gray-200 rounded-md p-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="flex-shrink-0 p-2 bg-white rounded border border-gray-200">
                    <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-900 truncate" id="filename-{{ $type->id }}">filename.pdf</p>
                    <p class="text-[10px] text-gray-500" id="filesize-{{ $type->id }}">0 KB</p>
                </div>
            </div>
            <div class="flex-shrink-0 ml-2">
                <button type="button" onclick="document.getElementById('doc_{{ $type->id }}').click()" class="text-xs text-primary hover:text-primary-dark font-medium mr-2">Ganti</button>
                <button type="button" onclick="clearFileInput({{ $type->id }})" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
            </div>
        </div>
    </div>

    <!-- State: Error JS -->
    <div id="state-error-{{ $type->id }}" class="upload-state-error hidden mt-2">
        <p class="text-xs text-red-600 flex items-start" id="error-msg-{{ $type->id }}">
            <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="error-text">File error</span>
        </p>
    </div>

    <!-- Backend Error Display -->
    @error('documents.'.$type->id)
        <p class="mt-2 text-xs text-red-600 flex items-start">
            <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ $message }}</span>
        </p>
    @enderror

    <!-- Actual Hidden File Input -->
    <input type="file" 
           name="documents[{{ $type->id }}]" 
           id="doc_{{ $type->id }}" 
           accept=".pdf,.jpg,.jpeg,.png" 
           class="sr-only document-input" 
           {{ $type->is_required ? 'required' : '' }} 
           aria-required="{{ $type->is_required ? 'true' : 'false' }}"
           onchange="handleFileSelect(this, {{ $type->id }})">
</div>
