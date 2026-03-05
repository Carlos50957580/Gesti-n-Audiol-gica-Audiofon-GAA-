<h2>Reporte de Revisiones del Evaluador</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Código</th>
            <th>Programa</th>
            <th>Universidad</th>
            <th>Veredicto</th>
            <th>Fecha</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($asignaciones as $a)
        <tr>
            <td>{{ $a->plan->codigo_plan }}</td>
            <td>{{ $a->plan->nombre_programa }}</td>
            <td>{{ $a->plan->university->nombre }}</td>
            <td>{{ $a->veredicto }}</td>
            <td>{{ $a->updated_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
