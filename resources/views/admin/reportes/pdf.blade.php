<h2>Reporte de Planes</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <tr>
        <th>Código</th>
        <th>Programa</th>
        <th>Universidad</th>
        <th>Evaluador</th>
        <th>Veredicto</th>
        <th>Estado</th>
    </tr>

    @foreach ($planes as $p)
    <tr>
        <td>{{ $p->codigo_plan }}</td>
        <td>{{ $p->nombre_programa }}</td>
        <td>{{ $p->university->nombre }}</td>
        <td>{{ $p->asignacion->evaluador->name ?? '—' }}</td>
        <td>{{ $p->asignacion->veredicto ?? '—' }}</td>
        <td>{{ $p->estado }}</td>
    </tr>
    @endforeach
</table>
