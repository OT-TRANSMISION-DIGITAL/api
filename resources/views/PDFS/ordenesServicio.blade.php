@extends('layout')

@section('container')
    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <h1 class="text-center">Detalle de la order</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">descripcion</th>
                        <th scope="col">estatus</th>
                        <th scope="col">producto</th>
                        <th scope="col">orden</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr>
                                <td>{{$row['id']}}</td>
                                <td class="text-center">{{$row['cantidad']}}</td>
                                <td class="text-center">{{$row['descripcion']}}</td>
                                <td class="text-center">{{$row['estatus']}}</td>
                                <td class="text-center">{{$row['producto_id']}}</td>
                                <td class="text-center">{{$row['orden_id']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                  </table>
            </div>
        </div>
    </div>
@endsection
