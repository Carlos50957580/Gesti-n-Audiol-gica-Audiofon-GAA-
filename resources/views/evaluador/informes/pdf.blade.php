<h1>Informe {{ ucfirst($informe->tipo) }}</h1>
<p><strong>Programa:</strong> {{ $informe->plan->nombre_programa }}</p>
<p><strong>Evaluador:</strong> {{ $informe->evaluador->name }}</p>
<hr>
<p>{!! nl2br(e($informe->contenido)) !!}</p>
