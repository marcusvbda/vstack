@extends("templates.default")
@section('body')
<div class="row d-flex flex-column align-items-center justify-content-center my-4 py-4">
	<div class="col-4">
		<div class="card">
			<div class="card-header">
				<h4><b>Relatório de {{$resource->label()}}</b></h4>
			</div>
			<div class="card-body">
				<div>Clique no botão abaixo para efetuar o download de seu relatório</div>
				<a class="mt-4 w-100 link-not-hovered" href="{{ $route }}">
					<button class="btn btn-primary btn-block py-3">Efetuar Download</button>
				</a>
			</div>
		</div>
	</div>
</div>
@endsection