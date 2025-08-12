@extends('layouts.app')

@section('title', __('app.translation_management'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-translate-2 me-2"></i>
                        {{ __('app.translation_management') }}
                    </h4>
                    <p class="card-subtitle text-muted">{{ __('app.edit_system_translations') }}</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Language Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="languageSelect" class="form-label">{{ __('app.select_language') }}</label>
                            <select class="form-select" id="languageSelect" onchange="loadTranslationFile()">
                                <option value="">{{ __('app.choose_language') }}</option>
                                @foreach($languages as $lang)
                                    <option value="{{ $lang }}">{{ strtoupper($lang) }} - {{ $lang === 'en' ? 'English' : 'Swahili' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fileSelect" class="form-label">{{ __('app.select_file') }}</label>
                            <select class="form-select" id="fileSelect" onchange="loadTranslationFile()">
                                <option value="">{{ __('app.choose_file') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Translation Editor -->
                    <div id="translationEditor" style="display: none;">
                        <form method="POST" action="{{ route('superadmin.translations.update') }}">
                            @csrf
                            <input type="hidden" name="language" id="selectedLanguage">
                            <input type="hidden" name="file" id="selectedFile">
                            
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('app.translation_editor') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div id="translationFields">
                                        <!-- Translation fields will be loaded here -->
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-base ri ri-save-line me-2"></i>
                                            {{ __('app.save_translations') }}
                                        </button>
                                        <button type="button" class="btn btn-secondary ms-2" onclick="resetForm()">
                                            <i class="icon-base ri ri-refresh-line me-2"></i>
                                            {{ __('app.reset') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Instructions -->
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h6><i class="icon-base ri ri-information-line me-2"></i>{{ __('app.translation_instructions') }}</h6>
                            <ul class="mb-0">
                                <li>{{ __('app.translation_instruction_1') }}</li>
                                <li>{{ __('app.translation_instruction_2') }}</li>
                                <li>{{ __('app.translation_instruction_3') }}</li>
                                <li>{{ __('app.translation_instruction_4') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const translationData = @json($translationFiles);

function loadTranslationFile() {
    const language = document.getElementById('languageSelect').value;
    const file = document.getElementById('fileSelect').value;
    
    if (!language || !file) {
        document.getElementById('translationEditor').style.display = 'none';
        return;
    }
    
    // Update hidden fields
    document.getElementById('selectedLanguage').value = language;
    document.getElementById('selectedFile').value = file;
    
    // Load file options for selected language
    if (language && !file) {
        const fileSelect = document.getElementById('fileSelect');
        fileSelect.innerHTML = '<option value="">{{ __('app.choose_file') }}</option>';
        
        if (translationData[language]) {
            Object.keys(translationData[language]).forEach(fileName => {
                const option = document.createElement('option');
                option.value = fileName;
                option.textContent = fileName + '.php';
                fileSelect.appendChild(option);
            });
        }
        return;
    }
    
    // Load translation fields
    if (translationData[language] && translationData[language][file]) {
        const fieldsContainer = document.getElementById('translationFields');
        fieldsContainer.innerHTML = '';
        
        const translations = translationData[language][file];
        Object.keys(translations).forEach(key => {
            const fieldDiv = document.createElement('div');
            fieldDiv.className = 'mb-3';
            
            const label = document.createElement('label');
            label.className = 'form-label';
            label.textContent = key;
            
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control';
            input.name = `translations[${key}]`;
            input.value = translations[key];
            
            fieldDiv.appendChild(label);
            fieldDiv.appendChild(input);
            fieldsContainer.appendChild(fieldDiv);
        });
        
        document.getElementById('translationEditor').style.display = 'block';
    }
}

function resetForm() {
    document.getElementById('languageSelect').value = '';
    document.getElementById('fileSelect').value = '';
    document.getElementById('translationEditor').style.display = 'none';
}
</script>
@endsection 