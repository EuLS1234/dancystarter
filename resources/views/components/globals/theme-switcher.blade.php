<div x-data="{
    options: [
        { value: 'light', label: 'Light' },
        { value: 'dark', label: 'Dark' },
        { value: 'system', label: 'System' }
    ],
    isOpen: false,
    openedWithKeyboard: false,
    selectedOption: null,
    currentTheme: localStorage.theme || ((!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'system' : 'system'),
    
    init() {
        // Set initial theme based on localStorage or system preference
        this.updateTheme();
        
        // Watch for system preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.currentTheme === 'system') {
                this.updateTheme();
            }
        });

        // Set initial selected option
        this.selectedOption = this.options.find(option => option.value === this.currentTheme) || this.options[2];
    },
    
    setTheme(theme) {
        if (theme === 'system') {
            localStorage.removeItem('theme');
            this.currentTheme = 'system';
        } else {
            localStorage.theme = theme;
            this.currentTheme = theme;
        }
        
        this.updateTheme();
    },
    
    updateTheme() {
        // If theme is explicitly set to dark or system preference is dark and no override
        const isDark = localStorage.theme === 'dark' || 
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
        
        // Toggle dark class on the html element
        document.documentElement.classList.toggle('dark', isDark);
    },
    
    setSelectedOption(option) {
        this.selectedOption = option;
        this.isOpen = false;
        this.openedWithKeyboard = false;
        this.setTheme(option.value);
    },
    
    getIconColor(themeValue) {
        if (this.currentTheme === themeValue) {
            if (themeValue === 'light') return 'text-amber-500';
            if (themeValue === 'dark') return 'text-blue-500';
            if (themeValue === 'system') return 'text-green-500';
        }
        return 'text-gray-400';
    },
    
    highlightFirstMatchingOption(pressedKey) {
        const option = this.options.find(item =>
            item.label.toLowerCase().startsWith(pressedKey.toLowerCase())
        );
        if (option) {
            const index = this.options.indexOf(option);
            const allOptions = document.querySelectorAll('.combobox-option');
            if (allOptions[index]) {
                allOptions[index].focus();
            }
        }
    }
}" 
class="max-w-xs flex flex-col" 
x-on:keydown="highlightFirstMatchingOption($event.key)" 
x-on:keydown.esc.window="isOpen = false; openedWithKeyboard = false">
    <div class="relative">
        <!-- Trigger button - showing only the icon -->
        <button 
            type="button" 
            role="combobox" 
            class="inline-flex items-center justify-center p-2 bg-surface-alt text-on-surface transition hover:opacity-75 dark:bg-surface-dark-alt/50 dark:text-on-surface-dark rounded-radius cursor-pointer" 
            aria-haspopup="listbox" 
            aria-controls="themeList" 
            x-on:click="isOpen = !isOpen" 
            x-on:keydown.down.prevent="isOpen = true; openedWithKeyboard = true" 
            x-on:keydown.enter.prevent="isOpen = true; openedWithKeyboard = true" 
            x-on:keydown.space.prevent="isOpen = true; openedWithKeyboard = true" 
            x-bind:aria-label="selectedOption ? 'Current theme: ' + selectedOption.label : 'Select theme'" 
            x-bind:aria-expanded="isOpen || openedWithKeyboard"
        >
            <!-- Light icon -->
            <template x-if="selectedOption?.value === 'light'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </template>
            
            <!-- Dark icon -->
            <template x-if="selectedOption?.value === 'dark'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </template>
            
            <!-- System icon -->
            <template x-if="selectedOption?.value === 'system'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </template>
            
            <!-- Screen reader text -->
            <span class="sr-only" x-text="selectedOption ? selectedOption.label + ' theme' : 'Select theme'"></span>
        </button>

        <!-- Dropdown list -->
        <ul 
            x-cloak 
            x-show="isOpen || openedWithKeyboard" 
            id="themeList" 
            class="absolute z-10 left-0 top-11 flex max-h-44 w-48 flex-col overflow-hidden overflow-y-auto border-outline bg-surface-alt py-1.5 dark:border-outline-dark dark:bg-surface-dark-alt rounded-radius border" 
            role="listbox" 
            aria-label="Theme options" 
            x-on:click.outside="isOpen = false; openedWithKeyboard = false" 
            x-on:keydown.down.prevent="$focus.wrap().next()" 
            x-on:keydown.up.prevent="$focus.wrap().previous()" 
            x-transition 
            x-trap="openedWithKeyboard"
        >
            <template x-for="(item, index) in options" :key="item.value">   
                <li 
                    class="combobox-option inline-flex justify-between items-center gap-6 bg-surface-alt px-4 py-2 text-sm text-on-surface hover:bg-surface-dark-alt/5 hover:text-on-surface-strong focus-visible:bg-surface-dark-alt/5 focus-visible:text-on-surface-strong focus-visible:outline-hidden dark:bg-surface-dark-alt dark:text-on-surface-dark dark:hover:bg-surface-alt/5 dark:hover:text-on-surface-dark-strong dark:focus-visible:bg-surface-alt/10 dark:focus-visible:text-on-surface-dark-strong" 
                    role="option" 
                    x-on:click="setSelectedOption(item)" 
                    x-on:keydown.enter="setSelectedOption(item)" 
                    x-bind:id="'option-' + index" 
                    tabindex="0"
                >
                    <div 
                        :class="{ 
                            'text-amber-500': currentTheme === 'light' && item.value === 'light', 
                            'text-blue-500': currentTheme === 'dark' && item.value === 'dark',
                            'text-green-500': currentTheme === 'system' && item.value === 'system',
                            'text-gray-400': currentTheme !== item.value 
                        }"
                        class="flex items-center gap-2"
                    >
                        <template x-if="item.value === 'light'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </template>
                        <template x-if="item.value === 'dark'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </template>
                        <template x-if="item.value === 'system'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </template>
                        <!-- Label -->
                        <span x-bind:class="selectedOption?.value === item.value ? 'font-bold' : ''" x-text="item.label"></span>
                        <!-- Screen reader 'selected' indicator -->
                        <span class="sr-only" x-text="selectedOption?.value === item.value ? 'selected' : ''"></span>
                    </div>
                    <!-- Checkmark -->
                    <svg 
                        x-cloak 
                        x-show="selectedOption?.value === item.value" 
                        xmlns="http://www.w3.org/2000/svg" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor" 
                        fill="none" 
                        stroke-width="2" 
                        class="size-4" 
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                </li>
            </template>
        </ul>
    </div>
</div>