
{!! Form::model($maintenance, ['route' => ['admin.maintenance.update', $maintenance->id], 'files' => true , 'method' => 'PUT']) !!}
    @include('admin.maintenance.templantes.form')
    <div class="d-flex justify-content-end ">
        <button type="submit" class="btn btn-primary mr-2"> <i class="fas fa-cloud-upload-alt"></i> Actualizar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-ban"></i> Cancelar</button>
    </div>
{!! Form::close() !!}
