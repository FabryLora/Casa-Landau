@php
    $location = request()->path();
    $isHome = $location === '/';
    $isPrivate = str_contains($location, 'privada');

    $defaultLinks = [
        ['title' => 'Nosotros', 'href' => '/nosotros'],
        ['title' => 'Productos', 'href' => '/productos'],
        ['title' => 'Novedades', 'href' => '/novedades'],

        ['title' => 'Contacto', 'href' => '/contacto'],
    ];
    $privateLinks = [
        ['title' => 'Productos', 'href' => '/privada/productos'],
        ['title' => 'Carrito', 'href' => '/privada/carrito'],
        ['title' => 'Mis pedidos', 'href' => '/privada/mispedidos'],
        ['title' => 'Lista de precios', 'href' => '/privada/listadeprecios'],
    ];
@endphp

<div x-data="{
        showModal: false,
        modalType: 'login',
        
        searchOpen: false,
        mobileMenuOpen: false,
        logoPrincipal: '{{ $logos->logo_principal ?? '' }}',
        logoSecundario: '{{ $logos->logo_secundario ?? '' }}',
        switchToLogin() {
            this.modalType = 'login';
        },
        switchToRegister() {
            this.modalType = 'register';
        },
        openModal(type = 'login') {
            this.modalType = type;
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        }
    }" class="z-50 sticky top-0 w-full transition-colors duration-300 h-[100px] max-sm:h-auto flex flex-col bg-white">




    <!-- Contenido principal navbar -->
    <div class="mx-auto flex h-full max-sm:h-[60px] w-[1200px] max-sm:w-full max-sm:px-4 items-center justify-between">
        <a href="/">
            <img :src="logoPrincipal" class="max-h-[83px] max-sm:h-8 transition-all duration-300" alt="Logo" />
        </a>

        <!-- Navegación desktop -->
        <div class="hidden md:flex gap-20 items-center">
            @foreach(($isPrivate ? $privateLinks : $defaultLinks) as $link)
                <a href="{{ $link['href'] }}"
                    class="text-sm hover:text-primary-orange transition-colors duration-300 text-black 
                                                                                                                                                                                                                        {{ Request::is(ltrim($link['href'], '/')) ? 'font-bold' : '' }}">
                    {{ $link['title'] }}
                </a>
            @endforeach
            <button @click="openModal('login')"
                class="text-sm max-sm:text-xs rounded-lg border border-primary-orange font-bold  px-4 py-2 text-primary-orange max-sm:px-2 hover:bg-primary-orange hover:text-white transition duration-300">
                <span class="max-sm:hidden">Zona Privada</span>
                <span class="hidden max-sm:inline">Privada</span>
            </button>
        </div>
    </div>

    <!-- Menú móvil -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="hidden max-sm:block max-sm:absolute max-sm:w-full max-sm:top-24 bg-white border-t border-gray-200 shadow-lg">
        <div class="py-2">
            @foreach(($isPrivate ? $privateLinks : $defaultLinks) as $link)
                <a href="{{ $link['href'] }}"
                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-orange transition-colors duration-300
                                                                                                                                                                                                                        {{ Request::is(ltrim($link['href'], '/')) ? 'font-bold bg-orange-50 text-primary-orange' : '' }}"
                    @click="mobileMenuOpen = false">
                    {{ $link['title'] }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Overlay del modal -->
    <div x-show="showModal" x-transition.opacity x-cloak class="fixed inset-0 bg-black/50 z-50" @click="closeModal()">
    </div>

    <!-- Modal de Login -->
    <div x-show="showModal && modalType === 'login'" x-transition.opacity x-cloak
        class="fixed inset-0 flex items-center justify-center z-50 max-sm:px-4">
        <form id="loginForm" method="POST" action="{{ route('login') }}" @click.away="closeModal()"
            class="relative bg-white rounded-lg shadow-lg w-[400px] max-sm:w-full max-w-[90vw] p-6 max-sm:p-4">

            <!-- Botón cerrar -->
            <button type="button" @click="closeModal()"
                class="absolute top-4 right-4 max-sm:top-3 max-sm:right-3 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6 max-sm:w-5 max-sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            @csrf
            <h2 class="text-2xl max-sm:text-xl font-semibold mb-6 max-sm:mb-4 text-center">Iniciar Sesión</h2>

            <div class="space-y-4 max-sm:space-y-3">
                <div>
                    <label for="login_name" class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Nombre de usuario o correo electrónico
                    </label>
                    <input name="usuario" type="text" id="login_name"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>

                <div>
                    <label for="login_password" class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Contraseña
                    </label>
                    <input name="password" type="password" id="login_password"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>

                <button form="loginForm" type="submit"
                    class="w-full bg-primary-orange text-white py-2 max-sm:py-1.5 px-4 rounded-md hover:bg-primary-orange/80 transition-colors text-sm max-sm:text-xs">
                    Iniciar Sesión
                </button>
            </div>

            <div class="mt-4 max-sm:mt-3 text-center">
                <p class="text-sm max-sm:text-xs text-gray-600">
                    ¿No tienes cuenta?
                    <button type="button" @click="switchToRegister()"
                        class="text-primary-orange hover:underline font-medium">
                        Regístrate aquí
                    </button>
                </p>
            </div>
        </form>
    </div>

    <!-- Modal de Registro -->
    <div x-show="showModal && modalType === 'register'" x-transition.opacity x-cloak
        class="fixed inset-0 flex items-center justify-center z-50 max-sm:px-4">
        <form id="registerForm" method="POST" action="{{ route('register') }}" @click.away="closeModal()"
            class="relative bg-white rounded-lg shadow-lg w-[500px] max-sm:w-full max-w-[90vw] p-6 max-sm:p-4 max-h-[90vh] max-sm:max-h-[95vh] overflow-y-auto">

            <!-- Botón cerrar -->
            <button type="button" @click="closeModal()"
                class="absolute top-4 right-4 max-sm:top-3 max-sm:right-3 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6 max-sm:w-5 max-sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            @csrf
            <h2 class="text-2xl max-sm:text-xl font-semibold mb-6 max-sm:mb-4 text-center">Crear Cuenta</h2>

            <div class="grid grid-cols-2 max-sm:grid-cols-1 gap-5 max-sm:gap-3">
                <div class="col-span-2 max-sm:col-span-1">
                    <label for="register_name" class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Nombre de usuario
                    </label>
                    <input name="name" type="text" id="register_name"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>

                <div>
                    <label for="password"
                        class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">Contraseña</label>
                    <input name="password" type="password" id="register_password"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>

                <div>
                    <label for="register_password_confirmation"
                        class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Confirmar contraseña
                    </label>
                    <input name="password_confirmation" type="password" id="register_password_confirmation"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>

                <div>
                    <label for="register_email" class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Correo electrónico
                    </label>
                    <input name="email" type="email" id="register_email"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>

                <div>
                    <label for="register_cuit" class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        CUIT
                    </label>
                    <input name="cuit" type="text" id="register_cuit"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>



                <div>
                    <label for="register_phone" class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Teléfono
                    </label>
                    <input name="telefono" type="text" id="register_phone"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>


                <div>
                    <label for="register_razon_social"
                        class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Razón Social
                    </label>
                    <input name="razon_social" type="text" id="register_razon_social"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>

                <div class="col-span-2">
                    <label for="register_address" class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Dirección
                    </label>
                    <input name="direccion" type="text" id="register_address"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                </div>

                <div>
                    <label for="register_provincia" class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Provincia
                    </label>
                    <select name="provincia" id="register_provincia"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                        <option value="">Seleccione una provincia</option>
                        @foreach($provincias as $provincia)
                            <option value="{{ $provincia->name }}">{{ $provincia->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="register_localidad" class="block text-sm max-sm:text-xs font-medium text-gray-700 mb-2">
                        Localidad
                    </label>
                    <select name="localidad" id="register_localidad"
                        class="w-full px-3 py-2 max-sm:px-2 max-sm:py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-orange text-sm max-sm:text-xs">
                        <option value="">Seleccione una localidad</option>
                        @foreach($provincias as $provincia)
                            @foreach($provincia->localidades as $localidad)
                                <option value="{{ $localidad->name }}">{{ $localidad->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <button form="registerForm" type="submit"
                    class="w-full bg-primary-orange text-white py-2 max-sm:py-1.5 px-4 rounded-md hover:bg-primary-orange/80 transition-colors col-span-2 max-sm:col-span-1 text-sm max-sm:text-xs">
                    Crear Cuenta
                </button>
            </div>

            <div class="mt-4 max-sm:mt-3 text-center">
                <p class="text-sm max-sm:text-xs text-gray-600">
                    ¿Ya tienes cuenta?
                    <button type="button" @click="switchToLogin()"
                        class="text-primary-orange hover:underline font-medium">
                        Inicia sesión aquí
                    </button>
                </p>
            </div>
        </form>
    </div>
</div>

{{--
<script>
    // Datos de ejemplo con la estructura de Laravel
    const provinciasData = [
        {
            id: 1,
            name: "Buenos Aires",
            localidades: [
                { id: 1, name: "La Plata" },
                { id: 2, name: "Mar del Plata" },
                { id: 3, name: "Bahía Blanca" },
                { id: 4, name: "Tandil" },
                { id: 5, name: "Olavarría" }
            ]
        },
        {
            id: 2,
            name: "Córdoba",
            localidades: [
                { id: 6, name: "Córdoba Capital" },
                { id: 7, name: "Villa Carlos Paz" },
                { id: 8, name: "Río Cuarto" },
                { id: 9, name: "Villa María" },
                { id: 10, name: "San Francisco" }
            ]
        },
        {
            id: 3,
            name: "Santa Fe",
            localidades: [
                { id: 11, name: "Santa Fe Capital" },
                { id: 12, name: "Rosario" },
                { id: 13, name: "Rafaela" },
                { id: 14, name: "Venado Tuerto" },
                { id: 15, name: "Reconquista" }
            ]
        },
        {
            id: 4,
            name: "Mendoza",
            localidades: [
                { id: 16, name: "Mendoza Capital" },
                { id: 17, name: "San Rafael" },
                { id: 18, name: "Godoy Cruz" },
                { id: 19, name: "Maipú" },
                { id: 20, name: "Luján de Cuyo" }
            ]
        }
    ];

    // Elementos del DOM
    const provinciaSelect = document.getElementById('register_provincia');
    const localidadSelect = document.getElementById('register_localidad');

    // Llenar el select de provincias
    function populateProvincias() {
        provinciasData.forEach(provincia => {
            const option = document.createElement('option');
            option.value = provincia.id;
            option.textContent = provincia.name;
            provinciaSelect.appendChild(option);
        });
    }

    // Filtrar localidades según provincia seleccionada
    function filterLocalidades(provinciaId) {
        // Limpiar localidades
        localidadSelect.innerHTML = '<option value="">Seleccione una localidad</option>';

        if (provinciaId === '' || provinciaId === null) {
            localidadSelect.disabled = true;
            return;
        }

        // Habilitar select de localidades
        localidadSelect.disabled = false;

        // Encontrar la provincia seleccionada
        const provinciaSeleccionada = provinciasData.find(p => p.id == provinciaId);

        if (provinciaSeleccionada && provinciaSeleccionada.localidades) {
            // Agregar localidades de la provincia seleccionada
            provinciaSeleccionada.localidades.forEach(localidad => {
                const option = document.createElement('option');
                option.value = localidad.id;
                option.textContent = localidad.name;
                localidadSelect.appendChild(option);
            });
        }
    }

    // Event listener para el cambio de provincia
    provinciaSelect.addEventListener('change', function () {
        const provinciaId = this.value;
        filterLocalidades(provinciaId);
    });

    // Inicializar
    populateProvincias();

    // Manejar envío del formulario (ejemplo)
    document.getElementById('registroForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const provinciaId = provinciaSelect.value;
        const localidadId = localidadSelect.value;

        if (provinciaId && localidadId) {
            const provincia = provinciasData.find(p => p.id == provinciaId);
            const localidad = provincia ? provincia.localidades.find(l => l.id == localidadId) : null;

            alert(`Provincia: ${provincia?.name}, Localidad: ${localidad?.name}`);
        } else {
            alert('Por favor seleccione provincia y localidad');
        }
    });
</script> --}}