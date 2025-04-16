<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    sedes: Array,
    cargos: Array
});

const loading = ref(false);
const error = ref('');
const success = ref(false);
const diasReposicion = ref([
    { fecha: '', horaInicio: '', horaFin: '' }
]);

const form = useForm({
    dui: '',
    nombre: '',
    fechaPermiso: '',
    horaInicio: '',
    horaFin: '',
    motivo: '',
    conDescuento: false,
    sede: '',
    cargo: '',
    diasReposicion: diasReposicion.value
});

// Formatear automáticamente el DUI mientras el usuario lo escribe
watch(() => form.dui, (newValue) => {
    // Eliminar caracteres no numéricos
    let cleaned = newValue.replace(/[^0-9]/g, '');
    
    // Aplicar el formato 00000000-0
    if (cleaned.length <= 8) {
        form.dui = cleaned;
    } else {
        form.dui = cleaned.substring(0, 8) + '-' + cleaned.substring(8, 9);
    }
});

const tiempoSolicitado = computed(() => {
    if (!form.horaInicio || !form.horaFin) return '';
    
    const inicio = new Date(`2000-01-01T${form.horaInicio}`);
    const fin = new Date(`2000-01-01T${form.horaFin}`);
    
    if (isNaN(inicio) || isNaN(fin)) return '';
    
    let diff = (fin - inicio) / 1000 / 60 / 60;
    if (diff < 0) diff += 24; // si cruza medianoche
    if (diff > 4) {
        error.value = 'El tiempo máximo permitido es de 4 horas';
        setTimeout(() => { error.value = ''; }, 3000);
        form.horaFin = '';
        return '';
    }
    
    return diff.toFixed(1) + ' horas';
});

const tiempoSolicitadoEnHoras = computed(() => {
    if (!form.horaInicio || !form.horaFin) return 0;
    
    const inicio = new Date(`2000-01-01T${form.horaInicio}`);
    const fin = new Date(`2000-01-01T${form.horaFin}`);
    
    if (isNaN(inicio) || isNaN(fin)) return 0;
    
    let diff = (fin - inicio) / 1000 / 60 / 60;
    if (diff < 0) diff += 24; // si cruza medianoche
    
    return diff;
});

const tiempoTotalReposicion = computed(() => {
    if (!diasReposicion.value.length) return 0;
    
    let totalMinutos = 0;
    diasReposicion.value.forEach(dia => {
        if (dia.horaInicio && dia.horaFin) {
            const inicio = new Date(`2000-01-01T${dia.horaInicio}`);
            const fin = new Date(`2000-01-01T${dia.horaFin}`);
            
            if (!isNaN(inicio) && !isNaN(fin)) {
                let diff = (fin - inicio) / 1000 / 60; // En minutos
                if (diff < 0) diff += 24 * 60; // Si cruza medianoche
                totalMinutos += diff;
            }
        }
    });
    
    return totalMinutos / 60; // Convertir a horas
});

const agregarDiaReposicion = () => {
    diasReposicion.value.push({ fecha: '', horaInicio: '', horaFin: '' });
    form.diasReposicion = diasReposicion.value;
};

const eliminarDiaReposicion = (index) => {
    if (diasReposicion.value.length > 1) {
        diasReposicion.value.splice(index, 1);
        form.diasReposicion = diasReposicion.value;
    }
};

const validarDui = (dui) => {
    const regex = /^\d{8}-\d{1}$/;
    return regex.test(dui);
};

// Función para validar el formulario - reutilizable para previsualizar y descargar
const validarFormulario = () => {
    // Validaciones básicas
    if (!validarDui(form.dui)) {
        error.value = 'El DUI debe tener el formato 00000000-0';
        setTimeout(() => { error.value = ''; }, 3000);
        return false;
    }
    
    if (!form.nombre) {
        error.value = 'Por favor ingrese su nombre';
        setTimeout(() => { error.value = ''; }, 3000);
        return false;
    }
    
    if (!form.fechaPermiso) {
        error.value = 'Por favor seleccione la fecha del permiso';
        setTimeout(() => { error.value = ''; }, 3000);
        return false;
    }
    
    if (!form.horaInicio || !form.horaFin) {
        error.value = 'Por favor seleccione la hora de inicio y fin';
        setTimeout(() => { error.value = ''; }, 3000);
        return false;
    }
    
    if (!form.motivo) {
        error.value = 'Por favor ingrese el motivo de la solicitud';
        setTimeout(() => { error.value = ''; }, 3000);
        return false;
    }
    
    if (!form.sede) {
        error.value = 'Por favor seleccione una sede';
        setTimeout(() => { error.value = ''; }, 3000);
        return false;
    }
    
    if (!form.cargo) {
        error.value = 'Por favor seleccione un cargo';
        setTimeout(() => { error.value = ''; }, 3000);
        return false;
    }
    
    let diasValidos = true;
    diasReposicion.value.forEach((dia) => {
        if (!dia.fecha || !dia.horaInicio || !dia.horaFin) {
            diasValidos = false;
        }
    });
    
    if (!diasValidos) {
        error.value = 'Por favor complete todos los campos de días de reposición';
        setTimeout(() => { error.value = ''; }, 3000);
        return false;
    }
    
    // Validar que el tiempo de reposición sea igual o mayor al tiempo solicitado
    if (tiempoTotalReposicion.value < tiempoSolicitadoEnHoras.value) {
        error.value = `El tiempo de reposición (${tiempoTotalReposicion.value.toFixed(1)} horas) debe ser igual o mayor al tiempo solicitado (${tiempoSolicitadoEnHoras.value.toFixed(1)} horas)`;
        setTimeout(() => { error.value = ''; }, 5000);
        return false;
    }
    
    return true;
};

// Función para previsualizar el PDF
const previsualizar = () => {
    if (!validarFormulario()) return;
    
    loading.value = true;
    error.value = '';
    
    // Preparar los datos
    const formData = new FormData();
    for (const key in form) {
        if (key === 'diasReposicion') {
            formData.append(key, JSON.stringify(diasReposicion.value));
        } else {
            formData.append(key, form[key]);
        }
    }
    
    // Añadir la bandera de previsualización
    formData.append('previsualizar', 'true');
    
    // Usar axios para abrir PDF en nueva ventana
    axios.post(route('permiso.pdf'), formData, {
        responseType: 'blob'
    })
    .then(response => {
        loading.value = false;
        
        // Crear un objeto URL para el blob
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        
        // Abrir el PDF en una nueva ventana/pestaña
        window.open(url, '_blank');
    })
    .catch(error => {
        loading.value = false;
        console.error(error);
        error.value = 'Ocurrió un error al generar el PDF. Por favor revise los datos ingresados.';
        setTimeout(() => { error.value = ''; }, 5000);
    });
};

// Función para descargar el PDF
const descargar = () => {
    if (!validarFormulario()) return;
    
    loading.value = true;
    error.value = '';
    
    // Preparar los datos
    const formData = new FormData();
    for (const key in form) {
        if (key === 'diasReposicion') {
            formData.append(key, JSON.stringify(diasReposicion.value));
        } else {
            formData.append(key, form[key]);
        }
    }
    
    // Añadir la bandera de previsualización (false para descarga)
    formData.append('previsualizar', 'false');
    
    // Usar axios directamente para descargar el PDF
    axios.post(route('permiso.pdf'), formData, {
        responseType: 'blob'
    })
    .then(response => {
        loading.value = false;
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'solicitud_permiso.pdf');
        document.body.appendChild(link);
        link.click();
        link.remove();
        success.value = true;
        setTimeout(() => { success.value = false; }, 3000);
    })
    .catch(error => {
        loading.value = false;
        console.error(error);
        error.value = 'Ocurrió un error al generar el PDF. Por favor revise los datos ingresados.';
        setTimeout(() => { error.value = ''; }, 5000);
    });
};

// Función original submit (ahora llama a descargar)
const submit = (e) => {
    e?.preventDefault();
    descargar();
};
</script>

<template>
    <GuestLayout>
        <Head title="Solicitud de Permiso y Control de Reposición" />
        
        <div class="bg-gradient-to-b from-[#363d4d] to-[#2c3340] py-8">
            <!-- Header Section -->
            <div class="container mx-auto px-4 mb-6">
                <div class="text-center text-white space-y-2">
                    <h1 class="text-3xl md:text-4xl font-bold tracking-wide">Solicitud de Permiso</h1>
                    <p class="text-xl text-gray-300">Censo Agropecuario y Pesca 2025</p>
                </div>
            </div>
            
            <!-- Notificaciones -->
            <div class="container mx-auto px-4 mb-4">
                <div v-if="error" class="max-w-4xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate__animated animate__fadeIn">
                    <strong class="font-bold">Error: </strong>
                    <span class="block sm:inline">{{ error }}</span>
                </div>
                
                <div v-if="success" class="max-w-4xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 animate__animated animate__fadeIn">
                    <strong class="font-bold">Éxito: </strong>
                    <span class="block sm:inline">La solicitud de permiso se ha generado correctamente.</span>
                </div>
            </div>
            
            <!-- Formulario de Permiso -->
            <div class="container mx-auto px-4 mb-8">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white dark:bg-[#303844] rounded-xl shadow-2xl overflow-hidden">
                        <div class="p-1 bg-gradient-to-r from-blue-500 via-blue-400 to-blue-600"></div>
                        <div class="px-6 py-8 md:px-10">
                            <!-- Instrucciones -->
                            <div class="mb-8 p-4 bg-blue-50 dark:bg-gray-700 rounded-lg border-l-4 border-blue-500">
                                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-2">Instrucciones</h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Complete todos los campos del formulario para generar su solicitud de permiso. Recuerde que los permisos se deben solicitar con 3 días de anticipación y debe presentar una constancia.
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">
                                    <strong>Importante:</strong> Antes de llenar este formulario, debe haber consultado con su administrador de contrato sobre el permiso y la forma de reposición.
                                </p>
                            </div>
                        
                            <form @submit.prevent="submit" class="space-y-8">
                                <!-- Información Personal -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                                        Información Personal
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- DUI y Nombre -->
                                        <div class="space-y-2">
                                            <label for="dui" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                DUI <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <input
                                                    id="dui"
                                                    type="text"
                                                    v-model="form.dui"
                                                    placeholder="00000000-0"
                                                    maxlength="10"
                                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                    required
                                                />
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Formato: 00000000-0</p>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Nombre completo <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <input
                                                    id="nombre"
                                                    type="text"
                                                    v-model="form.nombre"
                                                    placeholder="Ingrese su nombre completo"
                                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                    required
                                                />
                                            </div>
                                        </div>
                                        
                                        <!-- Sede y Cargo -->
                                        <div class="space-y-2">
                                            <label for="sede" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Sede <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v4H7V5zm8 8v-4H5v4h10z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <select
                                                    id="sede"
                                                    v-model="form.sede"
                                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                    required
                                                >
                                                    <option value="" disabled selected>Seleccione una sede</option>
                                                    <option v-for="sede in sedes" :key="sede" :value="sede">{{ sede }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <label for="cargo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Cargo <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                                                    </svg>
                                                </div>
                                                <select
                                                    id="cargo"
                                                    v-model="form.cargo"
                                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                    required
                                                >
                                                    <option value="" disabled selected>Seleccione un cargo</option>
                                                    <option v-for="cargo in cargos" :key="cargo" :value="cargo">{{ cargo }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Detalles del Permiso -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                                        Detalles del Permiso
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Fecha Permiso -->
                                        <div class="space-y-2">
                                            <label for="fechaPermiso" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Fecha del permiso <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <input
                                                    id="fechaPermiso"
                                                    type="date"
                                                    v-model="form.fechaPermiso"
                                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                    required
                                                />
                                            </div>
                                        </div>
                                        
                                        <!-- Con/Sin Descuento -->
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Tipo de permiso <span class="text-red-500">*</span>
                                            </label>
                                            <div class="flex space-x-6 py-2">
                                                <label class="inline-flex items-center">
                                                    <input 
                                                        type="radio" 
                                                        v-model="form.conDescuento" 
                                                        :value="true"
                                                        class="form-radio h-5 w-5 text-blue-600 dark:text-blue-400"
                                                    >
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Con descuento</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input 
                                                        type="radio" 
                                                        v-model="form.conDescuento" 
                                                        :value="false"
                                                        class="form-radio h-5 w-5 text-blue-600 dark:text-blue-400"
                                                    >
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Sin descuento</span>
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Seleccione según lo indicado por su administrador de contrato
                                            </p>
                                        </div>
                                        
                                        <!-- Tiempo solicitado (Desde - Hasta) -->
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Horario (Desde - Hasta) <span class="text-red-500">*</span>
                                            </label>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <input
                                                        type="time"
                                                        v-model="form.horaInicio"
                                                        class="w-full pl-10 pr-3 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                        required
                                                    />
                                                </div>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <input
                                                        type="time"
                                                        v-model="form.horaFin"
                                                        class="w-full pl-10 pr-3 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Máximo 4 horas permitidas
                                            </p>
                                        </div>
                                        
                                        <!-- Tiempo Solicitado (Calculado) -->
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Tiempo solicitado
                                            </label>
                                            <div class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-800 dark:text-white">
                                                {{ tiempoSolicitado || 'Pendiente de cálculo' }}
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Calculado automáticamente en base al horario
                                            </p>
                                        </div>
                                        
                                        <!-- Motivo -->
                                        <div class="space-y-2 md:col-span-2">
                                            <label for="motivo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Motivo de la solicitud <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 pt-3 pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <textarea
                                                    id="motivo"
                                                    v-model="form.motivo"
                                                    rows="3"
                                                    placeholder="Detalle el motivo de su solicitud de permiso"
                                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                    required
                                                ></textarea>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Explique de manera clara y concisa el motivo de su solicitud de permiso
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Reposición -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                                        Reposición del Tiempo
                                    </h3>
                                    
                                    <div class="space-y-6">
                                        <!-- Información de reposición -->
                                        <div class="bg-yellow-50 dark:bg-gray-700 border-l-4 border-yellow-400 p-4 rounded">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Información importante</h3>
                                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-200">
                                                        <p>Debe ingresar el detalle de cómo repondrá el tiempo solicitado. El tiempo total de reposición debe ser igual o mayor al tiempo solicitado.</p>
                                                        <p class="mt-2">
                                                            <span class="font-medium">Tiempo solicitado:</span> {{ tiempoSolicitado || '0 horas' }}
                                                        </p>
                                                        <p>
                                                            <span class="font-medium">Tiempo de reposición:</span> {{ tiempoTotalReposicion.toFixed(1) }} horas
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Días de Reposición -->
                                        <div class="space-y-4">
                                            <div class="flex justify-between items-center">
                                                <h4 class="text-md font-medium text-gray-800 dark:text-white">Calendario de reposición <span class="text-red-500">*</span></h4>
                                                <button 
                                                    type="button" 
                                                    @click="agregarDiaReposicion"
                                                    class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 transition"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    Agregar día
                                                </button>
                                            </div>
                                            
                                            <div class="bg-blue-50 dark:bg-gray-700 border-l-4 border-blue-400 p-3 mb-3 rounded">
                                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                                    Detalle los días y horas en que repondrá el tiempo solicitado. Debe cubrir al menos 
                                                    el tiempo total que estará ausente durante su permiso.
                                                </p>
                                            </div>
                                            
                                            <div v-for="(dia, index) in diasReposicion" :key="index" class="flex flex-wrap md:flex-nowrap gap-3 items-center py-4 px-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                                                <div class="w-full md:w-1/3">
                                                    <label :for="`diaReposicion-${index}`" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                        Fecha <span class="text-red-500">*</span>
                                                    </label>
                                                    <input
                                                        :id="`diaReposicion-${index}`"
                                                        type="date"
                                                        v-model="dia.fecha"
                                                        class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                        required
                                                    />
                                                </div>
                                                <div class="w-full md:w-1/4">
                                                    <label :for="`horaInicioReposicion-${index}`" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                        Hora inicio <span class="text-red-500">*</span>
                                                    </label>
                                                    <input
                                                        :id="`horaInicioReposicion-${index}`"
                                                        type="time"
                                                        v-model="dia.horaInicio"
                                                        class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                        required
                                                    />
                                                </div>
                                                <div class="w-full md:w-1/4">
                                                    <label :for="`horaFinReposicion-${index}`" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                        Hora fin <span class="text-red-500">*</span>
                                                    </label>
                                                    <input
                                                        :id="`horaFinReposicion-${index}`"
                                                        type="time"
                                                        v-model="dia.horaFin"
                                                        class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:text-white transition"
                                                        required
                                                    />
                                                </div>
                                                <div class="flex items-end w-10 ml-auto md:ml-0 mb-1 md:mb-0 h-full md:self-end">
                                                    <button 
                                                        v-if="diasReposicion.length > 1"
                                                        type="button" 
                                                        @click="eliminarDiaReposicion(index)"
                                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 focus:outline-none"
                                                        title="Eliminar día"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Términos y condiciones -->
                                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        <strong>Importante:</strong> Al enviar este formulario, reconozco que he consultado con mi administrador 
                                        de contrato y con el delegado BCR, y que deberé imprimir y firmar físicamente este 
                                        documento para completar el proceso.
                                    </p>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="flex justify-center gap-4 pt-4">
                                    <button 
                                        type="button"
                                        @click="previsualizar"
                                        class="px-8 py-3 text-base font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 dark:focus:ring-offset-gray-800 shadow-lg flex items-center"
                                        :disabled="loading"
                                    >
                                        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Vista previa
                                    </button>
                                    
                                    <button 
                                        type="submit" 
                                        class="px-8 py-3 text-base font-medium text-white bg-[#363d4d] hover:bg-[#2c3340] rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#363d4d] disabled:opacity-50 dark:focus:ring-offset-gray-800 shadow-lg flex items-center"
                                        :disabled="loading"
                                    >
                                        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Descargar Solicitud
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>