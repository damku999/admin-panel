{{--
    Universal Search Input Component
    
    Usage:
    <x-forms.search-field 
        id="searchInput"
        placeholder="Search customers..."
        :value="old('search')"
        onchange="filterData()"
        :with-button="true"
        button-text="Search"
        button-onclick="performSearch()">
    </x-forms.search-field>
    
    Note: Requires search-field.js to be included for clear button functionality
--}}

@props([
    'id' => 'search',
    'name' => 'search',
    'placeholder' => 'Search...',
    'value' => '',
    'onchange' => '',
    'oninput' => '',
    'onkeyup' => '',
    'withButton' => false,
    'buttonText' => 'Search',
    'buttonIcon' => 'fas fa-search',
    'buttonOnclick' => '',
    'buttonClass' => 'btn-primary',
    'clearButton' => true,
    'disabled' => false,
    'required' => false
])

<div class="search-field-container position-relative">
    <div class="input-group">
        <input type="text" 
               id="{{ $id }}" 
               name="{{ $name }}"
               class="form-control search-input"
               placeholder="{{ $placeholder }}"
               value="{{ $value }}"
               @if($onchange) onchange="{{ $onchange }}" @endif
               @if($oninput) oninput="{{ $oninput }}" @endif
               @if($onkeyup) onkeyup="{{ $onkeyup }}" @endif
               @if($disabled) disabled @endif
               @if($required) required @endif>
        
        @if($clearButton)
            <button type="button" 
                    class="btn btn-outline-secondary clear-search-btn" 
                    onclick="clearSearchField('{{ $id }}')"
                    title="Clear search"
                    style="display: none;">
                <i class="fas fa-times"></i>
            </button>
        @endif
        
        @if($withButton)
            <button type="button" 
                    class="btn {{ $buttonClass }}"
                    @if($buttonOnclick) onclick="{{ $buttonOnclick }}" @endif>
                <i class="{{ $buttonIcon }}"></i> {{ $buttonText }}
            </button>
        @endif
    </div>
</div>