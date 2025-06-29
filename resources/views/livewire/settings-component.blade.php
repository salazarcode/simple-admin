<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Configuración del Sistema') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Email Settings -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Configuración del Servidor de Email</h3>
                            <p class="text-sm text-gray-600">Configure las credenciales SMTP para el envío de emails desde la aplicación.</p>
                        </div>

                        <form wire:submit="saveEmailSettings">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Configuración rápida</label>
                                        <div class="flex space-x-2">
                                            <button 
                                                type="button" 
                                                wire:click="useGmailPresets" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors duration-150"
                                            >
                                                Usar Gmail
                                            </button>
                                            <span class="text-xs text-gray-500 flex items-center">Configure automáticamente para Gmail</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="mail_host" class="block text-sm font-medium text-gray-700">Servidor SMTP</label>
                                        <input 
                                            type="text" 
                                            id="mail_host" 
                                            wire:model="mail_host" 
                                            placeholder="smtp.gmail.com"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        @error('mail_host') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="mail_port" class="block text-sm font-medium text-gray-700">Puerto</label>
                                            <input 
                                                type="number" 
                                                id="mail_port" 
                                                wire:model="mail_port" 
                                                placeholder="587"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            >
                                            @error('mail_port') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="mail_encryption" class="block text-sm font-medium text-gray-700">Encriptación</label>
                                            <select 
                                                id="mail_encryption" 
                                                wire:model="mail_encryption" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            >
                                                <option value="tls">TLS</option>
                                                <option value="ssl">SSL</option>
                                                <option value="">Sin encriptación</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="mail_username" class="block text-sm font-medium text-gray-700">Usuario SMTP</label>
                                        <input 
                                            type="email" 
                                            id="mail_username" 
                                            wire:model="mail_username" 
                                            placeholder="tu-email@gmail.com"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        @error('mail_username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="mail_password" class="block text-sm font-medium text-gray-700">Contraseña SMTP</label>
                                        <input 
                                            type="password" 
                                            id="mail_password" 
                                            wire:model="mail_password" 
                                            placeholder="Contraseña o App Password"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        @error('mail_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        <p class="text-xs text-gray-500 mt-1">Para Gmail, use una contraseña de aplicación</p>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-4">
                                    <div>
                                        <label for="mail_from_address" class="block text-sm font-medium text-gray-700">Email de origen</label>
                                        <input 
                                            type="email" 
                                            id="mail_from_address" 
                                            wire:model="mail_from_address" 
                                            placeholder="noreply@tudominio.com"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        @error('mail_from_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        <p class="text-xs text-gray-500 mt-1">Email que aparecerá como remitente</p>
                                    </div>

                                    <div>
                                        <label for="mail_from_name" class="block text-sm font-medium text-gray-700">Nombre de origen</label>
                                        <input 
                                            type="text" 
                                            id="mail_from_name" 
                                            wire:model="mail_from_name" 
                                            placeholder="Mi Aplicación"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        @error('mail_from_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        <p class="text-xs text-gray-500 mt-1">Nombre que aparecerá como remitente</p>
                                    </div>

                                    <!-- Configuration tips -->
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                        <h4 class="text-sm font-medium text-blue-900 mb-2">Configuración para Gmail:</h4>
                                        <ul class="text-xs text-blue-800 space-y-1">
                                            <li>• Activar autenticación de 2 factores</li>
                                            <li>• Generar una contraseña de aplicación</li>
                                            <li>• Usar la contraseña de aplicación aquí</li>
                                        </ul>
                                    </div>

                                    <!-- Configuration Source Status -->
                                    @php
                                        $settings = \App\Models\EmailSetting::getActiveSettings();
                                        $usingDatabase = $settings !== null;
                                        $currentMailer = config('mail.default');
                                        $currentHost = config('mail.mailers.smtp.host');
                                    @endphp
                                    
                                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Configuración activa:</h4>
                                        <div class="text-xs text-gray-600 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span>Fuente:</span>
                                                @if($usingDatabase)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        📱 Base de Datos
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        📄 Archivo .env
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <span>Mailer actual:</span>
                                                <span class="font-mono text-xs">{{ $currentMailer }}</span>
                                            </div>
                                            
                                            @if($currentHost)
                                                <div class="flex items-center justify-between">
                                                    <span>Host SMTP:</span>
                                                    <span class="font-mono text-xs">{{ $currentHost }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if(!$usingDatabase)
                                            <div class="mt-3 pt-2 border-t border-gray-300">
                                                <p class="text-xs text-orange-600 mb-2">Usando configuración del archivo .env</p>
                                                <p class="text-xs text-gray-500">Guarde una configuración aquí para usar la base de datos</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Test Results -->
                                    @if($settings && $settings->tested_at)
                                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                                            <h4 class="text-sm font-medium text-gray-900 mb-2">Último test de conexión:</h4>
                                            <div class="text-xs text-gray-600 space-y-1">
                                                <p>Fecha: {{ $settings->tested_at->format('d/m/Y H:i') }}</p>
                                                <p class="flex items-center">
                                                    Resultado: 
                                                    @if(str_contains($settings->test_result, 'success'))
                                                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            ✓ Funcionando
                                                        </span>
                                                    @else
                                                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                            ✗ Error
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                                <div class="flex flex-wrap gap-3">
                                    <button 
                                        type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition-colors duration-150"
                                    >
                                        Guardar Configuración
                                    </button>
                                    
                                    <button 
                                        type="button" 
                                        wire:click="openTestModal" 
                                        @if(!$this->canTest) disabled @endif
                                        class="bg-green-500 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-2 px-6 rounded transition-colors duration-150"
                                    >
                                        Probar Conexión
                                    </button>
                                    
                                    @php
                                        $hasDbSettings = \App\Models\EmailSetting::getActiveSettings() !== null;
                                    @endphp
                                    
                                    @if($hasDbSettings)
                                        <button 
                                            type="button" 
                                            wire:click="useEnvConfiguration" 
                                            onclick="return confirm('¿Está seguro? Esto desactivará la configuración de la base de datos y usará el archivo .env')"
                                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition-colors duration-150"
                                        >
                                            Usar .env
                                        </button>
                                    @endif
                                </div>
                                
                                <div class="text-right">
                                    @if(!$this->canTest)
                                        <p class="text-sm text-gray-500">Complete todos los campos para habilitar la prueba</p>
                                    @endif
                                    
                                    @if($hasDbSettings)
                                        <p class="text-xs text-blue-600 mt-1">✓ Usando configuración de base de datos</p>
                                    @else
                                        <p class="text-xs text-gray-500 mt-1">Usando configuración del archivo .env</p>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    @if($showTestModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Probar Configuración de Email</h3>
                    
                    <div class="mb-4">
                        <label for="test_email" class="block text-sm font-medium text-gray-700">Email de destino</label>
                        <input 
                            type="email" 
                            id="test_email" 
                            wire:model="test_email" 
                            placeholder="test@ejemplo.com"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                        @error('test_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Se enviará un email de prueba a esta dirección</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button" 
                            wire:click="closeTestModal" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-150"
                        >
                            Cancelar
                        </button>
                        <button 
                            wire:click="testEmailConnection" 
                            @if($testing) disabled @endif
                            class="bg-green-500 hover:bg-green-700 disabled:bg-green-300 text-white font-bold py-2 px-4 rounded transition-colors duration-150 flex items-center"
                        >
                            @if($testing)
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enviando...
                            @else
                                Enviar Prueba
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('refresh-page', () => {
        setTimeout(() => {
            window.location.reload();
        }, 500);
    });
});
</script>