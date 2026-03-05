<x-app-layout>
<div class="container mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-4">
        📝 Registrar Informe — {{ $asignacion->plan->nombre_programa }}
    </h1>

    <form action="{{ route('evaluador.informes.store', $asignacion) }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block font-medium">Tipo de informe</label>
            <select name="tipo" class="border rounded w-full p-2" required>
                <option value="">-- Selecciona --</option>
                <option value="preliminar">Informe Preliminar</option>
                <option value="ejecutivo">Informe Ejecutivo</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Contenido</label>
            <textarea name="contenido" rows="10" class="w-full border rounded p-2" required></textarea>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">💾 Guardar Informe</button>
    </form>
</div>
</x-app-layout>
